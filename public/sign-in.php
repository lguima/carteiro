<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.0 404 Not Found');
    exit;
}

if (!isset($_POST['auth_response']) || empty($_POST['auth_response'])) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

session_start();

$auth_response = $_POST['auth_response'];

$client = new Google_Client();

try {
    $payload = $client->verifyIdToken($auth_response['id_token']);
} catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

if (strpos($payload['aud'], GOOGLE_CLIENT_ID) === false) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$_SESSION['access_token'] = $auth_response;

$client->setAuthConfigFile(__DIR__.'/../client_secret_'. GOOGLE_CLIENT_ID .'.json');
$client->setDeveloperKey(GOOGLE_API_KEY);
$client->setAccessToken($_SESSION['access_token']);

// Drive
$client->setScopes(Google_Service_Drive::DRIVE);
$drive = new Google_Service_Drive($client);

try {
    // Carteiro
    $folder_carteiro = $drive->files->listFiles(['q' => "'root' in parents and mimeType='application/vnd.google-apps.folder' and name='Carteiro' and trashed=false"]);

    if (empty($folder_carteiro->files)) {
        $folder_carteiro_metadata = new Google_Service_Drive_DriveFile(array(
            'name' => 'Carteiro',
            'mimeType' => 'application/vnd.google-apps.folder',
            'description' => 'Pasta criada pelo aplicativo "Carteiro" / Mantenha-a sem alterações')
        );

        $folder_carteiro = $drive->files->create($folder_carteiro_metadata, array(
            'fields' => 'id')
        );
    } else {
        $folder_carteiro = $folder_carteiro->files[0];
    }

    $_SESSION['carteiro_folder'] = $folder_carteiro->id;

    // Templates
    $folder_templates = $drive->files->listFiles(['q' => "'{$folder_carteiro->id}' in parents and mimeType='application/vnd.google-apps.folder' and name='Templates' and trashed=false"]);

    if (empty($folder_templates->files)) {
        $folder_templates_metadata = new Google_Service_Drive_DriveFile(array(
            'name' => 'Templates',
            'mimeType' => 'application/vnd.google-apps.folder',
            'description' => 'Pasta criada pelo aplicativo "Carteiro" / Mantenha-a sem alterações',
            'parents' => [$folder_carteiro->id])
        );

        $folder_templates = $drive->files->create($folder_templates_metadata, array(
            'fields' => 'id')
        );
    } else {
        $folder_templates = $folder_templates->files[0];
    }

    $_SESSION['templates_folder'] = $folder_templates->id;

    // Images
    $folder_images = $drive->files->listFiles(['q' => "'{$folder_carteiro->id}' in parents and mimeType='application/vnd.google-apps.folder' and name='Imagens' and trashed=false"]);

    if (empty($folder_images->files)) {
        $folder_images_metadata = new Google_Service_Drive_DriveFile(array(
            'name' => 'Imagens',
            'mimeType' => 'application/vnd.google-apps.folder',
            'description' => 'Pasta criada pelo aplicativo "Carteiro" / Mantenha-a sem alterações',
            'parents' => [$folder_carteiro->id])
        );

        $folder_images = $drive->files->create($folder_images_metadata, array(
            'fields' => 'id')
        );
    } else {
        $folder_images = $folder_images->files[0];
    }

    $_SESSION['images_folder'] = $folder_images->id;
} catch (Google_Service_Exception $e) {
    // Google_Service_Exception
}

header('Content-Type: application/json');
echo json_encode(['id_token' => $auth_response['id_token']]);
