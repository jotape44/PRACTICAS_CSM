<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;
use GuzzleHttp\Client as GuzzleClient;

// 🔹 CONFIGURAR CLIENTE GOOGLE DRIVE
function getClient() {
    $client = new Client();
    $client->setAuthConfig('credentials.json');
    $client->addScope(Drive::DRIVE_FILE);
    $client->setAccessType('offline');
    $client->setRedirectUri('https://portal.csmeducativo.com/');
    
    // 🔹 FORZAR ELECCIÓN DE CUENTA
    $client->setPrompt('select_account consent');


    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            echo "🔹 Abre el siguiente enlace en tu navegador y autoriza el acceso:\n";
            echo $client->createAuthUrl() . "\n";

            echo "🔹 Ingresa el código de verificación: ";
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            file_put_contents($tokenPath, json_encode($accessToken));
        }
    }

    return $client;
}

// 🔹 CREAR SUBCARPETA EN GOOGLE DRIVE
function createSubfolderInDrive($parentFolderId, $subfolderName) {
    $client = getClient();
    $service = new Drive($client);

    $fileMetadata = new Drive\DriveFile([
        'name' => $subfolderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentFolderId] // Ubicar en la carpeta existente
    ]);

    $folder = $service->files->create($fileMetadata, ['fields' => 'id, webViewLink']);
    return $folder;
}

// 🔹 ADJUNTAR CARPETA EN TRELLO
function attachFolderToTrelloCard($folderUrl, $cardId, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();
    $trelloApiUrl = "https://api.trello.com/1/cards/$cardId/attachments";

    $response = $client->post($trelloApiUrl, [
        'query' => [
            'url' => $folderUrl,
            'key' => $trelloKey,
            'token' => $trelloToken
        ]
    ]);

    return json_decode($response->getBody(), true);
}

function createTrelloCard($boardId, $listId, $cardName, $cardDesc, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();
    $trelloApiUrl = "https://api.trello.com/1/cards";

    $response = $client->post($trelloApiUrl, [
        'query' => [
            'idList' => $listId,  // 📌 ID de la lista dentro del tablero
            'name' => $cardName,  // 📌 Nombre del Card
            'desc' => $cardDesc,  // 📌 Descripción del Card
            'key' => $trelloKey,  
            'token' => $trelloToken
        ]
    ]);

    return json_decode($response->getBody(), true);
}

function getTrelloLists($boardId, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();
    $trelloApiUrl = "https://api.trello.com/1/boards/$boardId/lists";

    $response = $client->get($trelloApiUrl, [
        'query' => [
            'key' => $trelloKey,
            'token' => $trelloToken
        ]
    ]);

    return json_decode($response->getBody(), true);
}

function getListByName($boardId, $listName, $trelloKey, $trelloToken) {
    $lists = getTrelloLists($boardId, $trelloKey, $trelloToken); // Llamar a la función que obtiene todas las listas

    // Filtrar la lista que coincide con el nombre buscado
    $filteredLists = array_filter($lists, function($list) use ($listName) {
        return strtolower($list['name']) === strtolower($listName);
    });

    // Si encontró la lista, devolver el primer resultado
    return count($filteredLists) > 0 ? array_values($filteredLists)[0] : null;
}

function getTrelloCardName($cardId, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();
    $trelloApiUrl = "https://api.trello.com/1/cards/$cardId";

    $response = $client->get($trelloApiUrl, [
        'query' => [
            'key' => $trelloKey,
            'token' => $trelloToken
        ]
    ]);

    $cardData = json_decode($response->getBody(), true);

    return isset($cardData['name']) ? $cardData['name'] : null;
}

function getCardIdByName($boardId, $cardName, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();
    
    // 🔹 1. Obtener todas las tarjetas del tablero
    $url = "https://api.trello.com/1/boards/$boardId/cards?key=$trelloKey&token=$trelloToken";
    $response = $client->get($url);
    $cards = json_decode($response->getBody(), true);

    // 🔹 2. Buscar la tarjeta por su nombre
    foreach ($cards as $card) {
        if (strcasecmp($card['name'], $cardName) == 0) { // Comparación sin distinguir mayúsculas/minúsculas
            return $card['id']; // ✅ Retorna el ID correcto
        }
    }

    return "⚠️ No se encontró una tarjeta con el nombre '$cardName'.";
}

function createChecklistInCard($cardId, $checklistName, $items, $trelloKey, $trelloToken) {
    $client = new GuzzleClient();

    // 🔹 1. Crear el checklist en la tarjeta
    $createChecklistUrl = "https://api.trello.com/1/checklists";
    $response = $client->post($createChecklistUrl, [
        'query' => [
            // 'name' => $checklistName,
            'idCard' => $cardId,
            'key' => $trelloKey,
            'token' => $trelloToken
        ]
    ]);

    $checklist = json_decode($response->getBody(), true);
    
    if (!isset($checklist['id'])) {
        return "⚠️ Error al crear el checklist.";
    }

    $checklistId = $checklist['id'];

    // 🔹 2. Agregar los elementos al checklist
    foreach ($items as $item) {
        $addItemUrl = "https://api.trello.com/1/checklists/$checklistId/checkItems";
        $client->post($addItemUrl, [
            'query' => [
                'name' => $item,
                'key' => $trelloKey,
                'token' => $trelloToken
            ]
        ]);
    }

    return "✅ Checklist '$checklistName' creado con éxito en la tarjeta.\n";
}

// 📌 CONFIGURA TUS VARIABLES
$parentFolderId = "1UDlVUi379hNN-nd0AoQK2WL4kiHdntm_"; // ID de la carpeta en Drive
$subfolderName = "";
$trelloKey = "c190c5466ead1789eba0d427945753db";
$trelloToken = "ATTA0581d2123646ad0f5d29652d7cd519c601764c4f4db8221c2ab5c3febc3382da3B6D84A5";

// 📌 CONFIGURACIÓN CHECKLIST
$checklistName = "Tareas Pendientes";  // Nombre del checklist
$items = ["Revisar documentos", "Enviar informe", "Actualizar reporte"];  // Elementos del checklist

// 📌 Crear card en Trello
$boardId = "sVx3cZhN";  // 🔹 Reemplaza con el ID de tu tablero
$trelloCardId = "";
$trelloCardName = "asdad";
$listName = "Lista de tareas";   // Nombre de la lista que quieres obtener
$listId = "";    // 🔹 Reemplaza con el ID de la lista donde agregarás el Card
$cardDesc = "Descripción de la nueva tarea en Trello";

// // ✅ Obtener la lista por nombre
$list = getListByName($boardId, $listName, $trelloKey, $trelloToken);
if ($list) {
    $listId = $list['id'];
} else {
    echo "⚠️ No se encontró la lista '$listName' en el tablero.\n";
    return;
}
// // ✅ Crear la tarjeta en Trello
$trelloCard = createTrelloCard($boardId, $listId, $trelloCardName, $cardDesc, $trelloKey, $trelloToken);
$trelloCardId = $trelloCard['id'];
$trelloCardName = $trelloCard['name'];

// // ✅ Obtiene una tarjeta en Trello
$cardId = getCardIdByName($boardId, $trelloCardName, $trelloKey, $trelloToken);
echo "ID de la tarjeta: " . $cardId."\n";
$trelloCardId = $cardId;

// ✅ Crear el checklist en la tarjeta
$result = createChecklistInCard($trelloCardId, $checklistName, $items, $trelloKey, $trelloToken);
echo $result;

$subfolderName = getTrelloCardName($trelloCardId, $trelloKey, $trelloToken);
if ($subfolderName == null) {
    echo "⚠️ No se encontró el card '$trelloCardId' en el tablero.\n";
    return;
}

// ✅ Crear subcarpeta en Google Drive
$subfolder = createSubfolderInDrive($parentFolderId, $subfolderName);
$parentFolderId = $subfolder->id;
// ✅ Crear sub-subcarpeta en Google Drive
$subfolderName = "Prueba Subfolder";
$subfolder = createSubfolderInDrive($parentFolderId, $subfolderName);
$subfolderUrl = $subfolder->webViewLink;
echo "✅ Subcarpeta creada: " . $subfolderUrl . "\n";

// ✅ Adjuntar el enlace en la tarjeta de Trello
$trelloResponse = attachFolderToTrelloCard($subfolderUrl, $trelloCardId, $trelloKey, $trelloToken);
echo "✅ Adjunto en Trello: " . $trelloResponse['id'];
?>
