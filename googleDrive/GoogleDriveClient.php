<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// AUTENTICACIÓN DE GOOGLE DRIVE
class GoogleDriveClient {
    private $client;
    private $tokenPath = 'dataConfig/googleToken.json';

    public function __construct() {
        $this->client = new Client();
        $this->client->setAuthConfig('dataConfig/credentials.json');
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->setAccessType('offline');
        $this->client->setRedirectUri('https://portal.csmeducativo.com/');
        $this->client->setPrompt('select_account consent');

        $this->loadToken();
    }

    private function loadToken() {
        if (file_exists($this->tokenPath)) {
            $accessToken = json_decode(file_get_contents($this->tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                echo "🔹 Abre el siguiente enlace y autoriza el acceso:\n";
                echo $this->client->createAuthUrl() . "\n";

                echo "🔹 Ingresa el código de verificación: ";
                $authCode = trim(fgets(STDIN));

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                file_put_contents($this->tokenPath, json_encode($accessToken));
            }
        }
    }

    public function getClient() {
        return $this->client;
    }
}