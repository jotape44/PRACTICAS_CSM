<?php
require 'googleDrive/GoogleDriveClient.php';
require 'googleDrive/GoogleDriveManager.php';
require 'googleDrive/GoogleFolderId.php';
require 'trello/TrelloClient.php';
require 'trello/TrelloManager.php';
require 'trello/TrelloAuthorize.php';
require 'trello/TrelloKey.php';

// CONFIGURA TUS VARIABLES
$cardDesc = "Descripción de la nueva tarea en Trello";
$boardDesc = "Un tablero creado automáticamente si no existe";

// Inicializa el gestor de clave de API Trello
$apiKeyManager = new TrelloApiKeyManager();
$trelloKey = $apiKeyManager->getApiKey();

// Crea una instancia del gestor de autorización de Trello, responsable de manejar la autenticación y obtención del token de acceso.
$trello = new TrelloAuthorize();

// Usa el objeto de autorización junto con la API Key para obtener el token de acceso necesario para interactuar con la API de Trello.
$trelloToken = $trello->KeyToken($trelloKey);

// Crea el cliente de Trello, configurado con la API Key y el token de acceso, para realizar peticiones autenticadas a la API.
$trelloClient = new TrelloClient($trelloKey, $trelloToken);

// Instancia el gestor de Trello para crear tableros, listas y tarjetas utilizando el cliente autenticado.
$trelloManager = new TrelloManager($trelloClient);

// Instanciar clientes y gestores
$googleDriveClient = new GoogleDriveClient();
$driveManager = new GoogleDriveManager($googleDriveClient);

// CONFIGURACIÓN CHECKLIST
$checklistNameNuevo = "TAREA COLEGIO NUEVO";  // Nombre del checklist
$checklistItemsNuevo = [
    "Crear base de datos",
    "Crear los datos de la institución por medio del servicio institucion/guardar",
    "Cargar estructura de la base de datos",
    "Crear datos iniciales en la base de datos",
    "Cargar sedes",
    "Cargar daos de vigencia y datos de valoraciones (escalas comportamieno, parámetros, etc)",
    "Cargar escudo",
    "Cargar estudiantes",
    "Cargar areas asignaturas",
    "Cargar asignaturas a los grados",
    "Cargar docentes",
    "Crear plan de estudios",
    "Modigicar directores de grupo",
    "Cargar Desempeños"
];  

$checklistNameAntiguo = "TAREA COLEGIO ANTIGUO";  // Nombre del checklist
$checklistItemsAntiguo = [
    "Configurar cierre vigencia 2024",
    "Cargar estudiantes",
    "Cargar/Revisar plan de estudio",
    "Modificar directores de grupo",
    "Revisar y/o cargar desempeños",
    "Confirmar datos de vigencia y datos de valoraciones (escalas comportamiento, parámetros, etc)",
    "Confirmar cantidad de periodos y fechas"
];

$arrayListas = [
    "Terminado",
    "Cargue de desempeños",
    "Alistamiento",
    "Cargue inicial",
    "Pendiente"
];

// Para ingresar por consola el nombre del tablero
echo "Ingrese el nombre del tablero: ";
$boardName = trim(readline());

// Obtén o crea el tablero y consigue su ID
$board = $trelloManager->CreateBoard($boardName, $boardDesc);
$boardId = $board['id'];
echo "Tablero Creado: $boardId\n";  
$pendienteListId = null;

foreach ($arrayListas as $itemLista) {
    $listName = $itemLista;
    $list = $trelloManager->CreateList($boardId, $listName);
    $listId = $list['id'];

    
   if ($itemLista === "Pendiente") {
        $pendienteListId = $listId;
    }
}

// Mostrar solo el ID de la lista "Pendiente"
if ($pendienteListId !== null) {
    echo "ID de la lista 'Pendiente': $pendienteListId\n";
} else {
    echo "La lista 'Pendiente' no fue encontrada.\n";
}

// echo "Lista Creado: $listId\n";

$json = '[{"nombre":"Huila","ciudad":[{"nombre":"Campoalegre","instituciones":[{"nombre":"Eugenio","nuevo":false},{"nombre":"Misael","nuevo":false},{"nombre":"Colegio Nuevo","nuevo":true}]},{"nombre":"La Plata","instituciones":[{"nombre":"San Sebastian","nuevo":false},{"nombre":"Colegio Nuevo","nuevo":true}]}]},{"nombre":"Tolima","ciudad":[{"nombre":"Cajamarca","instituciones":[{"nombre":"Tecnica","nuevo":false},{"nombre":"Colegio Nuevo","nuevo":true}]}]}]';

$institucionesjson = json_decode($json);

$rootFolderId = getGoogleFolderId(); // Carpeta raíz

foreach ($institucionesjson as $departamento) {
    // Carpeta del departamento
    $carpetaDepartamento = $driveManager->createSubfolder($rootFolderId, $departamento->nombre);
    $departamentoId = $carpetaDepartamento->id;

    foreach ($departamento->ciudad as $ciudad) {
        // Carpeta del municipio dentro del departamento
        $carpetaCiudad = $driveManager->createSubfolder($departamentoId, $ciudad->nombre);
        $ciudadId = $carpetaCiudad->id;

        foreach ($ciudad->instituciones as $institucion) {
            $nombreColegio = $institucion->nombre. ' - ' . $ciudad->nombre;
            $esNuevo = $institucion->nuevo;
            // Carpeta de la institución dentro del municipio
            $carpetaInstitucion = $driveManager->createSubfolder($ciudadId, $institucion->nombre);
            $subfolderUrl = $carpetaInstitucion->webViewLink;
            // Elegir nombre de checklist e items según si es nuevo o antiguo
            if ($esNuevo) {
                $checklistName = $checklistNameNuevo;
                $checklistItems = $checklistItemsNuevo;
            } else {
                $checklistName = $checklistNameAntiguo;
                $checklistItems = $checklistItemsAntiguo;
            }

            // Crear la tarjeta para este colegio
            $cardDesc = "Tareas para el colegio: " . $nombreColegio;
            $trelloCard = $trelloManager->createCard($pendienteListId, $nombreColegio, $cardDesc);
            $trelloCardId = $trelloCard['id'];

            echo "ID de la nueva tarjeta creada para $nombreColegio: " . $trelloCardId . "<br>";

            // Crear el checklist en la tarjeta
            $checklist = $trelloManager->createChecklistInCard($trelloCardId, $checklistName);
            if (!isset($checklist['id'])) {
                echo "Error al crear el checklist para $nombreColegio.<br>";
                continue;
            }
            $checklistId = $checklist['id'];
            $trelloManager->addItemsToChecklist($checklistId, $checklistItems);
            echo "Checklist creado para $nombreColegio: " . $checklistId . "<br>";



            // Adjuntar el enlace en la tarjeta de Trello
            $trelloResponse = $trelloManager->attachFolderToCard($subfolderUrl, $trelloCardId);
            echo "Adjunto en Trello para $nombreColegio: " . $trelloResponse['id'] . "<br>";
        }
    }
}

?>
