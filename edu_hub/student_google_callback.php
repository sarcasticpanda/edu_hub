<?php
// Google OAuth Callback Handler
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

$clientID = GOOGLE_CLIENT_ID;
$clientSecret = defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '';
$redirectUri = GOOGLE_REDIRECT_URI;

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();
        $email = $userInfo->email;
        $name = $userInfo->name;
        $googleId = $userInfo->id;
        // Store in DB
        $host = 'localhost';
        $db   = 'school_management_system';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            // Create students table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS students (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                name VARCHAR(255),
                google_id VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            // Insert or update student
            $stmt = $pdo->prepare("INSERT INTO students (email, name, google_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), google_id = VALUES(google_id)");
            $stmt->execute([$email, $name, $googleId]);
            
            // Set session and redirect to dashboard
            $_SESSION['student_email'] = $email;
            $_SESSION['student_name'] = $name;
            $_SESSION['login_message'] = 'Login successful! Welcome, ' . $name;
            header('Location: student_dashboard.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
            header('Location: student_login_signup.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Google Auth Error: ' . htmlspecialchars($token['error']);
        header('Location: student_login_signup.php');
        exit;
    }
} else {
    $_SESSION['error_message'] = 'No code received from Google.';
    header('Location: student_login_signup.php');
    exit;
}
