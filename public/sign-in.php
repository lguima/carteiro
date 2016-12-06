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

if (strpos($payload['aud'], OAUTH_CLIENT_ID) === false) {
    header('HTTP/1.0 404 Not Found');
}

$_SESSION['access_token'] = $auth_response;

header('Content-Type: application/json');
echo json_encode(['id_token' => $auth_response['id_token']]);
