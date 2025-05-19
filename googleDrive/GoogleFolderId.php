<?php

/**
 * Obtiene la ID de la carpeta de Google Drive.
 *
 * Si la ID ya está guardada en un archivo JSON dentro de la carpeta `dataConfig`, se lee desde allí.
 * Si no, solicita la ID al usuario, la guarda en un archivo JSON y la devuelve.
 *
 * @return string $parentFolderId La ID de la carpeta.
 */
function getGoogleFolderId() {
    $configDir = __DIR__ . '/../dataConfig'; // Directorio donde se almacenará el archivo JSON
    $configFile = $configDir . '/googleId.json'; // Ruta del archivo JSON

    // Crear la carpeta `dataConfig` si no existe
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
    }

    // Verificar si el archivo JSON existe
    if (file_exists($configFile)) {
        // Leer la ID desde el archivo JSON
        $configData = json_decode(file_get_contents($configFile), true);
        if (isset($configData['folderId']) && !empty($configData['folderId'])) {
            echo "✅ ID de carpeta cargada desde el archivo JSON: {$configData['folderId']}\n";
            return $configData['folderId'];
        }
    }

    // Solicitar la ID al usuario si no está guardada
    echo "🔗 Abre el siguiente enlace para acceder a Google Drive y copia la ID de la carpeta deseada:\n";
    echo "https://drive.google.com/drive/u/0/my-drive\n\n";

    // Solicitar la ID de la carpeta
    echo "Ingresa la ID de la carpeta: ";
    $parentFolderId = trim(fgets(STDIN)); // Leer entrada del usuario

    // Validar que la ID no esté vacía
    if (empty($parentFolderId)) {
        echo "⚠️ No se ingresó ninguna ID de carpeta. Finalizando el proceso.\n";
        exit;
    }

    // Guardar la ID en el archivo JSON
    $configData = ['folderId' => $parentFolderId];
    file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT));
    echo "✅ ID de carpeta guardada en el archivo JSON: $parentFolderId\n";

    return $parentFolderId;
}