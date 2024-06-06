<?php
require __DIR__ . '/vendor/autoload.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setScopes(Google_Service_Drive::DRIVE_FILE);
    $client->setAuthConfig(__DIR__ . '/client_secret.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = __DIR__ . '/token_id.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    
    return $client;
}

// Get the API client and construct the service object.
$client = getClient();

$service = new Google_Service_Drive($client);

$folder_id_parent = '1L89wtmsZ1GFQc6sJTkbOe530qbsOVbow';

// Tạo folder mới
$folder = new Google_Service_Drive_DriveFile();
$folder->setName('02');
$folder->setMimeType('application/vnd.google-apps.folder');
$folder->setParents([$folder_id_parent]);

// Thực thi tạo folder
$createdFolder = $service->files->create($folder);

// $folder_id_parent = $createdFolder->getId();


// // Tạo folder con mới
// $folder = new Google_Service_Drive_DriveFile();
// $folder->setName('orders');
// $folder->setMimeType('application/vnd.google-apps.folder');
// $folder->setParents([$folder_id_parent]);

// // Thực thi tạo folder
// $createdFolder = $service->files->create($folder);

echo "thành công!";






