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
$client = getClient();


$service = new Google_Service_Drive($client);



// Lấy id file
$fileID = '1m7HZ9jXLd0xaoj2ENj5C6Mj8byOSFIc9';

$file = $service->files->get($fileID);

$permissions = $file->getPermissions();

echo "<pre>";
    print_r($file); // or var_dump($data);
echo "</pre>";


die();
foreach ($permissions as $permission) {
  echo "Email: " . $permission->getEmailAddress() . ", Role: " . $permission->getRole() . "<br>";
}
