<?php

// MANEJO DE TARJETAS, LISTAS Y CHECKLISTS EN TRELLO
class TrelloManager {
    private $client;
    private $authParams;

    public function __construct(TrelloClient $trelloClient) {
        $this->client = $trelloClient->getClient();
        $this->authParams = $trelloClient->getAuthParams();
    }

    public function CreateBoard($boardName, $boardDescription) {
        $trelloApiUrl = "https://api.trello.com/1/boards/";

        $response = $this->client->post($trelloApiUrl, [
            'query' => array_merge($this->authParams, [
                'name' => $boardName,
                'desc' => $boardDescription,
                'defaultLists' => false
            ])
        ]);

        return json_decode($response->getBody(), true);
    }

    public function CreateList($boardId, $listName) {
        $trelloApiUrl = "https://api.trello.com/1/lists";

        $response = $this->client->post($trelloApiUrl, [
            'query' => array_merge($this->authParams, [
                'name' => $listName,
                'idBoard' => $boardId
            ])
        ]);

        return json_decode($response->getBody(), true);
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

    public function createChecklistInCard($cardId, $checklistName) {
        $createChecklistUrl = "https://api.trello.com/1/checklists";
        $response = $this->client->post($createChecklistUrl, [
            'query' => array_merge($this->authParams, [
                'idCard' => $cardId,
                'name' => $checklistName
            ])
        ]);

        return json_decode($response->getBody(), true);
    }

    public function addItemsToChecklist($checklistId, $items) {
        $addItemUrl = "https://api.trello.com/1/checklists/$checklistId/checkItems";
        foreach ($items as $item) {
            $this->client->post($addItemUrl, [
                'query' => array_merge($this->authParams, [
                    'name' => $item,
                    'checked' => false
                ])
            ]);
        }
        return "✅ Checklist creado con éxito.";
    }
}