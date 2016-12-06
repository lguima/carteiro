<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.0 404 Not Found');
    exit;
}

if (!isset($_POST['id_token']) || empty(trim($_POST['id_token'])) || !isset($_POST['revoke_token'])) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

session_start();

$id_token = $_POST['id_token'];
$revoke_token = filter_var($_POST['revoke_token'], FILTER_VALIDATE_BOOLEAN);

$client = new Google_Client();

try {
    $payload = $client->verifyIdToken($id_token);
} catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

if (strpos($payload['aud'], OAUTH_CLIENT_ID) === false) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

if ($revoke_token)
    $client->revokeToken($_SESSION['access_token']);

unset($_SESSION['access_token']);

header('Content-Type: application/json');
echo json_encode(['id_token' => $id_token]);
