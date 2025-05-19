<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;

// CONEXIÓN CON TRELLO
class TrelloClient {
    private $client;
    private $trelloKey;
    private $trelloToken;

    public function __construct($trelloKey, $trelloToken) {
        $this->client = new GuzzleClient();
        $this->trelloKey = $trelloKey;
        $this->trelloToken = $trelloToken;
    }

    public function getClient() {
        return $this->client;
    }

    public function getAuthParams() {
        return [
            'key' => $this->trelloKey,
            'token' => $this->trelloToken
        ];
    }
}