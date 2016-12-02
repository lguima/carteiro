<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile(__DIR__.'/../client_secret_'. OAUTH_CLIENT_ID .'.json');
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
$client->setRedirectUri('http://'. $_SERVER['HTTP_HOST'] .'/callback.php');

if (!isset($_GET['code']))
    header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));

$client->authenticate($_GET['code']);

$_SESSION['access_token'] = $client->getAccessToken();

header('Location: ' . filter_var('http://'. $_SERVER['HTTP_HOST'] .'/carteiro.php', FILTER_SANITIZE_URL));
