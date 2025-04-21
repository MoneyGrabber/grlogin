<?php
require 'vendor/autoload.php'; // Must include Google Client libraries via Composer

$client = new Google_Client();
$client->setClientId('87338492874-ktl9s2871de6adkhvdk0tv8knifi1jv3.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost:8000/authentification.php'); // Your redirect URI

$client->setAccessType('offline'); // this is important to get a refresh token
$client->setPrompt('consent');
$client->addScope('https://www.googleapis.com/auth/gmail.send');

// Step 1: Open this URL in your browser
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "Open the following URL in your browser:<br><a href='$authUrl'>$authUrl</a>";
} else {
    // Step 2: Exchange authorization code for access & refresh token
    $client->authenticate($_GET['code']);
    $tokens = $client->getAccessToken();
    echo "<pre>";
    print_r($tokens); // This includes the refresh token!
    echo "</pre>";
}