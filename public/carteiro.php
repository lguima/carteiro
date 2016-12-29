<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Location: ' . filter_var('http://'. $_SERVER['HTTP_HOST'] .'/callback.php', FILTER_SANITIZE_URL));
    exit;
}

date_default_timezone_set('Etc/UTC');

$content = file_get_contents('templates/'. MAIL_TEMPLATE .'/index.html');
$content = str_replace('{URL_TEMPLATE}', 'http://'. $_SERVER['HTTP_HOST'] .'/templates/'. MAIL_TEMPLATE .'/', $content);

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->Subject = MAIL_SUBJECT;
$mail->msgHTML($content);
$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
$mail->addAddress(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
$mail->addReplyTo(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

foreach (MAIL_RECIPIENTS as $recipient) {
    $mail->addBCC($recipient['address'], $recipient['name']);
}

$mail->preSend();
$mime = $mail->getSentMIMEMessage();

// web-safe base64
$raw = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');

$message = new Google_Service_Gmail_Message();
$message->setRaw($raw);

$client = new Google_Client();
$client->setAuthConfigFile(__DIR__.'/../client_secret_'. GOOGLE_CLIENT_ID .'.json');
$client->setScopes([Google_Service_Gmail::GMAIL_SEND]);

$client->setAccessToken($_SESSION['access_token']);
$service = new Google_Service_Gmail($client);

try {
    $message = $service->users_messages->send('me', $message);
    echo "Mensagem #{$message->getId()} entregue! :)";
} catch (Exception $e) {
    echo $e->getMessage();
}
