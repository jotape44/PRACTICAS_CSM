<?php
require 'googleDrive/GoogleDriveClient.php';
require 'googleDrive/GoogleDriveManager.php';
require 'googleDrive/GoogleFolderId.php';
require 'trello/TrelloClient.php';
require 'trello/TrelloManager.php';
require 'trello/TrelloAuthorize.php';
require 'trello/TrelloKey.php';

// 📌 CONFIGURA TUS VARIABLES
$trelloCardName = "tarjeta token 3";
$cardDesc = "Descripción de la nueva tarea en Trello";
$boardName = "Tablero Automatizado 4";
$boardDesc = "Un tablero creado automáticamente si no existe";
$subfolderName = "Prueba Subfolder";

// ✅ Inicializa el gestor de clave de API Trello
$apiKeyManager = new TrelloApiKeyManager();
$trelloKey = $apiKeyManager->getApiKey();

// Elementos del checklist
$trello = new TrelloAuthorize();
$trelloToken = $trello->KeyToken($trelloKey);
$trelloClient = new TrelloClient($trelloKey, $trelloToken);
$trelloManager = new TrelloManager($trelloClient);

// 📌 CONFIGURACIÓN CHECKLIST
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
    "Pendiente", 
    "Cargue inicial", 
    "Alistamiento",
    "Cargue de desempeños",
    "Terminado"
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
    if ($itemInstitucion->nuevo== true) {
        echo $itemInstitucion->nombre . " es nuevo.<br>";
    } else {
        echo $itemInstitucion->nombre . " no es nuevo.<br>";
    }
}


 // ✅ Crear la tarjeta en Trello
 $trelloCard = $trelloManager->createCard($listId, $trelloCardName, $cardDesc);
 $trelloCardId = $trelloCard['id'];
 $trelloCardName = $trelloCard['name'];

 echo "ID de la nueva tarjeta creada: " . $trelloCardId . "\n";

 // ✅ Crear el checklist en la nueva tarjeta
 $checklist = $trelloManager->createChecklistInCard($trelloCardId, $checklistName);

if (!isset($checklist['id'])) {
    return "⚠️ Error al crear el checklist.";
}
$checklistId = $checklist['id'];
$trelloManager->addItemsToChecklist($checklistId, $checklistItems);
echo "✅ Checklist creado: " . $checklistId . "\n";

 // ✅ Obtener nombre de la nueva tarjeta como nombre de la subcarpeta
 $subfolderName = $trelloCardName;
 if (!$subfolderName) {
     echo "⚠️ No se encontró el card '$trelloCardId' en el tablero.\n";
     return;
 }

// ✅ OBTENER ID DE CARPETA EN GOOGLE DRIVE

$parentFolderId = getGoogleFolderId(); // Llama a la función para obtener la ID de la carpeta

//-------------Crear carpeta en Google Driver

// ✅ Instanciar clientes y gestores
$googleDriveClient = new GoogleDriveClient();
$driveManager = new GoogleDriveManager($googleDriveClient);
// ✅ Crear subcarpeta en Google Drive
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$parentFolderId = $subfolder->id;

// ✅ Crear sub-subcarpeta en Google Drive
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$subfolderUrl = $subfolder->webViewLink;
echo "✅ Subcarpeta creada: " . $subfolderUrl . "\n";

// ✅ Adjuntar el enlace en la nueva tarjeta de Trello
$trelloResponse = $trelloManager->attachFolderToCard($subfolderUrl, $trelloCardId);
echo "✅ Adjunto en Trello: " . $trelloResponse['id'];

?>
