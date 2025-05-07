<?php
require 'GoogleDriveClient.php';
require 'GoogleDriveManager.php';
require 'TrelloClient.php';
require 'TrelloManager.php';
require 'TrelloAuthorize.php';

// 📌 CONFIGURA TUS VARIABLES
$parentFolderId = "1UDlVUi379hNN-nd0AoQK2WL4kiHdntm_"; // ID de la carpeta en Drive
$subfolderName = "";
$trelloKey = "c190c5466ead1789eba0d427945753db";
$boardId = "sVx3cZhN";  // 🔹 Reemplaza con el ID de tu tablero
$trelloCardId = "";
$trelloCardName = "tarjeta token 3";
$listName = "Lista de tareas";   // Nombre de la lista que quieres obtener
$listId = "";    // 🔹 Reemplaza con el ID de la lista donde agregarás el Card
$cardDesc = "Descripción de la nueva tarea en Trello";

// 📌 CONFIGURACIÓN CHECKLIST
$checklistName = "Tareas Pendientes";  // Nombre del checklist
$items = [
    "Revisar documentos", 
    "Enviar informe", 
    "Actualizar reporte"
];  // Elementos del checklist

$trello = new TrelloAuthorize();
$trelloToken = $trello->KeyToken($trelloKey);
$trelloClient = new TrelloClient($trelloKey, $trelloToken);
$trelloManager = new TrelloManager($trelloClient);

// ✅ Obtener la lista del tablero de trello por nombre
$list = $trelloManager->getListByName($boardId, $listName);
if ($list) {
    $listId = $list['id'];
} else {
    echo "⚠️ No se encontró la lista '$listName' en el tablero.\n";
    return;
}

// // ✅ Crear la tarjeta en Trello
// $trelloCard = $trelloManager->createCard($listId, $trelloCardName, $cardDesc);
// $trelloCardId = $trelloCard['id'];
// $trelloCardName = $trelloCard['name'];

// echo "ID de la nueva tarjeta creada: " . $trelloCardId . "\n";

// // ✅ Crear el checklist en la nueva tarjeta
// $result = $trelloManager->createChecklistInCard($trelloCardId, $checklistName, $items);
// echo $result . "\n";

// // ✅ Obtener nombre de la nueva tarjeta como nombre de la subcarpeta
// $subfolderName = $trelloCardName;
// if (!$subfolderName) {
//     echo "⚠️ No se encontró el card '$trelloCardId' en el tablero.\n";
//     return;
// }

//-------------Crear carpeta en Google Driver

// ✅ Instanciar clientes y gestores
$googleDriveClient = new GoogleDriveClient();
$driveManager = new GoogleDriveManager($googleDriveClient);
// ✅ Crear subcarpeta en Google Drive
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$parentFolderId = $subfolder->id;

// ✅ Crear sub-subcarpeta en Google Drive
$subfolderName = "Prueba Subfolder";
$subfolder = $driveManager->createSubfolder($parentFolderId, $subfolderName);
$subfolderUrl = $subfolder->webViewLink;
echo "✅ Subcarpeta creada: " . $subfolderUrl . "\n";

// ✅ Adjuntar el enlace en la nueva tarjeta de Trello
$trelloResponse = $trelloManager->attachFolderToCard($subfolderUrl, $trelloCardId);
echo "✅ Adjunto en Trello: " . $trelloResponse['id'];

?>
