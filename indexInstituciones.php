<?php
require 'googleDrive/GoogleDriveClient.php';
require 'googleDrive/GoogleDriveManager.php';
require 'googleDrive/GoogleFolderId.php';
require 'trello/TrelloClient.php';
require 'trello/TrelloManager.php';
require 'trello/TrelloAuthorize.php';
require 'trello/TrelloKey.php';

// 📌 CONFIGURA TUS VARIABLES
$cardDesc = "Descripción de la nueva tarea en Trello";
$boardName = "Tablero Automatizado 4";
$boardDesc = "Un tablero creado automáticamente si no existe";

// ✅ Inicializa el gestor de clave de API Trello
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

// ✅ Instanciar clientes y gestores
$googleDriveClient = new GoogleDriveClient();
$driveManager = new GoogleDriveManager($googleDriveClient);

// 📌 CONFIGURACIÓN CHECKLIST
$checklistNameNuevo = "TAREA COLEGIO NUEVO";  // Nombre del checklist
$checklistItemsNuevo = [
    "Cargar Desempeños",
    "Modigicar directores de grupo",
    "Crear plan de estudios",
    "Cargar docentes",
    "Cargar asignaturas a los grados",
    "Cargar areas asignaturas",
    "Cargar estudiantes",
    "Cargar escudo",
    "Cargar daos de vigencia y datos de valoraciones (escalas comportamieno, parámetros, etc)",
    "Cargar sedes",
    "Crear datos iniciales en la base de datos",
    "Cargar estructura de la base de datos",
    "Crear los datos de la institución por medio del servicio institucion/guardar", 
    "Crear base de datos"
];  

$checklistNameAntiguo = "TAREA COLEGIO ANTIGUO";  // Nombre del checklist
$checklistItemsAntiguo = [
    "Confirmar cantidad de periodos y fechas",
    "Confirmar datos de vigencia y datos de valoraciones (escalas comportamiento, parámetros, etc)",
    "Revisar y/o cargar desempeños",
    "Modificar directores de grupo",
    "Cargar/Revisar plan de estudio",
    "Cargar estudiantes",
    "Configurar cierre vigencia 2024"
];

$arrayListas = [
    "Terminado",
    "Cargue de desempeños",
    "Alistamiento",
    "Cargue inicial",
    "Pendiente"
];

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

$json = '[{"nombre":"Eugenio Ferro","nuevo":false},{"nombre":"Maria Auxiliadora","nuevo":false},{"nombre":"Colegio Nuevo","nuevo":true}]';

// Convertir JSON a array de objetos stdClass
$instituciones = json_decode($json);
foreach ($instituciones as $itemInstitucion) {
    $nombreColegio = $itemInstitucion->nombre;
    $esNuevo = $itemInstitucion->nuevo;

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
        echo "⚠️ Error al crear el checklist para $nombreColegio.<br>";
        continue;
    }
    $checklistId = $checklist['id'];
    $trelloManager->addItemsToChecklist($checklistId, $checklistItems);
    echo "✅ Checklist creado para $nombreColegio: " . $checklistId . "<br>";

 // Crear subcarpeta en Google Drive con el nombre del colegio
    $subfolderName = $nombreColegio;
// ✅ OBTENER ID DE CARPETA EN GOOGLE DRIVE
    $parentFolderId = getGoogleFolderId(); // Llama a la función para obtener la ID de la carpeta
    $subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
    $subfolderUrl = $subfolder->webViewLink;
    echo "✅ Subcarpeta creada para $nombreColegio: " . $subfolderUrl . "<br>";

    // Adjuntar el enlace en la tarjeta de Trello
    $trelloResponse = $trelloManager->attachFolderToCard($subfolderUrl, $trelloCardId);
    echo "✅ Adjunto en Trello para $nombreColegio: " . $trelloResponse['id'] . "<br>";
}

/* 
//-------------Crear carpeta en Google Driver

// ✅ Crear subcarpeta en Google Drive
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$parentFolderId = $subfolder->id;

// ✅ Crear sub-subcarpeta en Google Drive
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$subfolderUrl = $subfolder->webViewLink;
echo "✅ Subcarpeta creada: " . $subfolderUrl . "\n";

// ✅ Adjuntar el enlace en la nueva tarjeta de Trello
$trelloResponse = $trelloManager->attachFolderToCard($subfolderUrl, $trelloCardId);
echo "✅ Adjunto en Trello: " . $trelloResponse['id']; */

?>
