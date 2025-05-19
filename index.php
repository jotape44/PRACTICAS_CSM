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
$listName = "Lista de tareas";   // Nombre de la lista que quieres obtener
$cardDesc = "Descripción de la nueva tarea en Trello";
$boardName = "Tablero Automatizado 4";
$boardDesc = "Un tablero creado automáticamente si no existe";
$subfolderName = "Prueba Subfolder";

// 📌 CONFIGURACIÓN CHECKLIST
$checklistName = "Tareas Pendientes";  // Nombre del checklist
$checklistItems = [
    "Revisar documentos", 
    "Enviar informe", 
    "Actualizar reporte"
];  

// ✅ Inicializa el gestor de clave de API Trello
$apiKeyManager = new TrelloApiKeyManager();
$trelloKey = $apiKeyManager->getApiKey();

// Elementos del checklist
$trello = new TrelloAuthorize();
$trelloToken = $trello->KeyToken($trelloKey);
$trelloClient = new TrelloClient($trelloKey, $trelloToken);
$trelloManager = new TrelloManager($trelloClient);

// Obtén o crea el tablero y consigue su ID
$board = $trelloManager->CreateBoard($boardName, $boardDesc);
$boardId = $board['id'];
echo "Tablero Creado: $boardId\n";

$list = $trelloManager->CreateList($boardId, $listName);

$listId = $list['id'];
echo "Lista Creado: $listId\n";

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
