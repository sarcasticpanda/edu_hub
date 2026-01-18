<?php
// Student Logout Handler
session_start();

// Get the referrer URL before destroying session
$return_url = $_SERVER['HTTP_REFERER'] ?? '';

// Destroy all session data
$_SESSION = array();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the page they were on, or homepage
if (!empty($return_url) && strpos($return_url, 'localhost') !== false && strpos($return_url, 'student_dashboard') === false) {
    header('Location: ' . $return_url);
} else {
    header('Location: index.php');
}
exit;
