<?php
// Google Login Button Page
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$clientID = GOOGLE_CLIENT_ID;
$redirectUri = GOOGLE_REDIRECT_URI;

$client = new Google_Client();
$client->setClientId($clientID);
$client->setRedirectUri($redirectUri);
$client->addScope('email');
$client->addScope('profile');
$client->setAccessType('offline');
$client->setPrompt('select_account');

$authUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Google Login Test</title>
</head>
<body>
    <h2>Login as Student</h2>
    <a href="<?= htmlspecialchars($authUrl) ?>">
        <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" />
    </a>
</body>
</html>
