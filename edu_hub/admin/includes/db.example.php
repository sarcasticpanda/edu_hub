<?php
/**
 * Database Configuration Template
 * 
 * Instructions:
 * 1. Copy this file and rename it to 'db.php'
 * 2. Fill in your actual database credentials
 * 3. NEVER commit db.php to GitHub
 */

$host = 'localhost';
$db   = 'school_management_system';
$user = 'root';  // Change for production
$pass = '';      // Set password for production
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Helper function to get school configuration
function getSchoolConfig($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT config_value FROM school_config WHERE config_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Helper function to set school configuration
function setSchoolConfig($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO school_config (config_key, config_value) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE config_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
