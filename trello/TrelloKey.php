<?php

class TrelloApiKeyManager {
    private $apiKeyPath = 'dataConfig/trelloKey.json'; // Ruta del archivo JSON donde se almacenará la API Key.

    /**
     * Obtiene la API Key de Trello.
     * 
     * @return string La API Key existente o recién generada.
     */
    public function getApiKey() {
        // Verifica si existe el archivo con la API Key.
        if (file_exists($this->apiKeyPath)) {
            // Leer y decodificar el archivo JSON.
            $jsonData = json_decode(file_get_contents($this->apiKeyPath), true);

            // Verifica que el archivo contenga una API Key válida.
            if (isset($jsonData['apiKey']) && !empty($jsonData['apiKey'])) {
                return $jsonData['apiKey']; // Devuelve la API Key almacenada.
            }
        }

        // Si no existe el archivo, genera uno nuevo.
        return $this->requestNewApiKey();
    }

    /**
     * Solicita una nueva API Key al usuario y la guarda en un archivo JSON.
     * 
     * @return string La nueva API Key proporcionada por el usuario.
     */
    private function requestNewApiKey() {
        echo "🔹 No se encontró un archivo de API Key existente.\n";
        echo "🔹 Por favor, abre el siguiente enlace en tu navegador para generar tu API Key de Trello:\n";
        echo "   👉 https://trello.com/power-ups/admin/new\n\n";

        echo "🔹 Ingresa tu nueva API Key aquí: ";
        $apiKey = trim(fgets(STDIN)); // Obtiene la API Key ingresada por el usuario desde la terminal.

        // Asegura que el directorio exista antes de guardar el archivo
        $dir = dirname($this->apiKeyPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Guardar la API Key en un archivo JSON.
        $jsonData = ['apiKey' => $apiKey];
        file_put_contents($this->apiKeyPath, json_encode($jsonData, JSON_PRETTY_PRINT));

        echo "✅ API Key guardada exitosamente en: {$this->apiKeyPath}\n";

        return $apiKey; // Devuelve la nueva API Key.
    }
}
