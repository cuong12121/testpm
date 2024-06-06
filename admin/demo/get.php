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

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            
            
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

// Get the API client and construct the service object.
$client = getClient();

$service = new Google_Service_Drive($client);

$folderId = '1472lxXiK2ScO3WbYKv68bBgoeEVofpF1';

// Truy vấn danh sách file
$optParams = array(
     'q' => "'$folderId' in parents",
    'spaces' => 'drive',
    'fields' => 'nextPageToken, files(id, name, mimeType)',
   
);
$files = $service->files->listFiles($optParams);

// In thông tin file
foreach ($files['files'] as $file) {
    echo 'ID: ' . $file['id'] . '<br>';
    // echo 'Tên: ' . $file['name'] . PHP_EOL;
    // echo 'Loại MIME: ' . $file['mimeType'] . PHP_EOL;
    // echo 'Kích thước: ' . $file['size'] . ' bytes' . PHP_EOL;
    // echo 'Thời gian tạo: ' . $file['createdTime'] . PHP_EOL;
    
}

die();

// Specify the file ID
$fileId = '1jbrHN6Z9hcOacMSrjPy2wARDdLcbJ3dy';

try {
  // Download the file content (assuming it's a text file)
  $file = $service->files->get($fileId, ['alt' => 'media']);
  $content = $file->getBody()->getContents();
  
 header("Content-type: image/jpeg");
 header('Content-Type: application/pdf');

  // Display the file content
 
  echo $content; // Add line breaks for better readability

} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}

die;

// Lấy ID thư mục
// $folderId = '1Z-eSwwRTBdk3AoXP1NWZsqHtP0WY_x11';

// // Truy vấn file trong thư mục
// $files = $service->files->listFiles(array(
//     'q' => "'$folderId' in parents",
//     'fields' => 'nextPageToken, files(id, name, mimeType, size, createdTime)'
// ));

// // In thông tin file
// foreach ($files['files'] as $file) {
//     echo 'ID: ' . $file['id'] . '<br>';
//     // echo 'Tên: ' . $file['name'] . PHP_EOL;
//     // echo 'Loại MIME: ' . $file['mimeType'] . PHP_EOL;
//     // echo 'Kích thước: ' . $file['size'] . ' bytes' . PHP_EOL;
//     // echo 'Thời gian tạo: ' . $file['createdTime'] . PHP_EOL;
    
// }


die();

// Specify the file ID
$fileId = '1gyO24YthK6NJjXBGwGIN3sUGi9gjo7fa';

try {
  // Download the file content (assuming it's a text file)
  $file = $service->files->get($fileId, ['alt' => 'media']);
  $content = $file->getBody()->getContents();
  
 header("Content-type: image/jpeg");
 header('Content-Type: application/pdf');



  // Display the file content
 
  echo $content; // Add line breaks for better readability

} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}



// try {
//   // Download the file content
//   $file = $service->files->get($fileId, ['alt' => 'media']);
//   $content = $file->getBody()->getContents();

//   // Process the downloaded content as needed (e.g., display, save)
//   echo "File content retrieved successfully.";

// } catch (Exception $e) {
//   echo "Error: " . $e->getMessage();
// }
// $response = $service->files->get($fileId, array(
//     //'pageSize' => 10,
//     'fields' => 'id, description,webViewLink,webContentLink,properties',        
//     //'alt' => 'media'
// ));
// header('Content-Type: application/json'); //to beautify view in browser
// echo json_encode($response);

die();



// Duyệt qua danh sách file
foreach ($files->getFiles() as $file) {
  echo $file->getId() . ' - ' . $file->getName() . PHP_EOL;
}

die();

// Lấy danh sách file trong thư mục cha
$files = $service->files->listFiles([
  'q' => "'$parentId' in parents",
  'fields' => 'files(id, name)'
]);

// Lấy thông tin file
$file = $files->getFiles()[0];

// Mở file
$url = $service->files->getDownloadUrl($file->getId());
header('Location: ' . $url);