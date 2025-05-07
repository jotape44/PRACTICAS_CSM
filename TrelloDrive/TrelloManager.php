<?php

// MANEJO DE TARJETAS, LISTAS Y CHECKLISTS EN TRELLO
class TrelloManager {
    private $client;
    private $authParams;

    public function __construct(TrelloClient $trelloClient) {
        $this->client = $trelloClient->getClient();
        $this->authParams = $trelloClient->getAuthParams();
    }

    public function createCard($listId, $cardName, $cardDesc) {
        $trelloApiUrl = "https://api.trello.com/1/cards";

        $response = $this->client->post($trelloApiUrl, [
            'query' => array_merge($this->authParams, [
                'idList' => $listId,
                'name' => $cardName,
                'desc' => $cardDesc
            ])
        ]);

        return json_decode($response->getBody(), true);
    }

    public function attachFolderToCard($folderUrl, $cardId) {
        $trelloApiUrl = "https://api.trello.com/1/cards/$cardId/attachments";

        $response = $this->client->post($trelloApiUrl, [
            'query' => array_merge($this->authParams, ['url' => $folderUrl])
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getLists($boardId) {
        $trelloApiUrl = "https://api.trello.com/1/boards/$boardId/lists";

        $response = $this->client->get($trelloApiUrl, [
            'query' => $this->authParams
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getListByName($boardId, $listName) {
        $lists = $this->getLists($boardId);

        foreach ($lists as $list) {
            if (strcasecmp($list['name'], $listName) == 0) {
                return $list;
            }
        }

        return null;
    }

    public function getCardIdByName($boardId, $cardName) {
        $trelloApiUrl = "https://api.trello.com/1/boards/$boardId/cards";

        $response = $this->client->get($trelloApiUrl, [
            'query' => $this->authParams
        ]);

        $cards = json_decode($response->getBody(), true);

        foreach ($cards as $card) {
            if (strcasecmp($card['name'], $cardName) == 0) {
                return $card['id'];
            }
        }

        return null;
    }

    public function createChecklistInCard($cardId, $checklistName, $items) {
        $createChecklistUrl = "https://api.trello.com/1/checklists";
        $response = $this->client->post($createChecklistUrl, [
            'query' => array_merge($this->authParams, ['idCard' => $cardId])
        ]);

        $checklist = json_decode($response->getBody(), true);
        if (!isset($checklist['id'])) {
            return "⚠️ Error al crear el checklist.";
        }

        $checklistId = $checklist['id'];

        foreach ($items as $item) {
            $addItemUrl = "https://api.trello.com/1/checklists/$checklistId/checkItems";
            $this->client->post($addItemUrl, [
                'query' => array_merge($this->authParams, ['name' => $item])
            ]);
        }

        return "✅ Checklist '$checklistName' creado con éxito.";
    }
}