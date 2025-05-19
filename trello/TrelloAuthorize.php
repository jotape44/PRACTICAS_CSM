<?php

class TrelloAuthorize{

    private $tokenPath = 'dataConfig/trelloToken.json';

    public function KeyToken($apiKey){
        if (file_exists($this->tokenPath)) {
            $jsonData = json_decode(file_get_contents($this->tokenPath), true);
            return $jsonData['trelloToken'];
        }
            
        $scope = 'read,write';
        $name = 'MiApp';
        $expiration = 'never';
        $responseType = 'token';

        // Construir la URL de la solicitud
        $url = "https://trello.com/1/authorize?key=$apiKey&scope=$scope&name=$name&expiration=$expiration&response_type=$responseType";

        echo "🔹 Abre el siguiente enlace y autoriza el acceso:\n";
        echo $url. "\n";

        echo "🔹 Ingresa el código de verificación: ";
        $tokenCode = trim(fgets(STDIN));

        $jsonData = [
            'trelloToken' => $tokenCode,
        ];

        //Se crea el archivo y se guarda el token
        file_put_contents($this->tokenPath, json_encode($jsonData));
        
        return $tokenCode;
    }
}