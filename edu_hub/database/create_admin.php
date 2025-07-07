<?php
// Script to create admin user
require_once '../admin/includes/db.php';

$email = 'admin@school.edu';
$password = 'admin123';
$name = 'Administrator';

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO admins (email, password_hash, name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), name = VALUES(name)");
    $stmt->execute([$email, $password_hash, $name]);
    
    echo "Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Please change the password after first login.\n";
} catch (Exception $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
?>