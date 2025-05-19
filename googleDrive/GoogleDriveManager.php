<?php
require 'vendor/autoload.php';

use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

// MANEJO DE CARPETAS EN DRIVE
class GoogleDriveManager {
    private $service;

    public function __construct(GoogleDriveClient $googleDriveClient) {
        $this->service = new Drive($googleDriveClient->getClient());
    }

    public function createSubfolder($parentFolderId, $subfolderName) {
        $fileMetadata = new DriveFile([
            'name' => $subfolderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentFolderId]
        ]);

        return $this->service->files->create($fileMetadata, ['fields' => 'id, webViewLink']);
    }
}