<?php
// Fix database schema - Add missing columns
$host = 'localhost';
$db   = 'school_management_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "<h2>Fixing Database Schema...</h2>";
    
    // Check and add reviewed_by column
    try {
        $pdo->exec("ALTER TABLE student_applications ADD COLUMN reviewed_by VARCHAR(100)");
        echo "Added 'reviewed_by' column<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "'reviewed_by' column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // Check and add submission_count column
    try {
        $pdo->exec("ALTER TABLE student_applications ADD COLUMN submission_count INT DEFAULT 1");
        echo "Added 'submission_count' column<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "'submission_count' column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // Check and add status_updated_at column
    try {
        $pdo->exec("ALTER TABLE student_applications ADD COLUMN status_updated_at TIMESTAMP NULL");
        echo "Added 'status_updated_at' column<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "'status_updated_at' column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // Create student_to_admin_messages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS student_to_admin_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_email VARCHAR(255) NOT NULL,
        application_id INT,
        subject VARCHAR(255),
        message TEXT,
        attachment VARCHAR(255),
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read BOOLEAN DEFAULT 0,
        admin_reply TEXT,
        replied_at TIMESTAMP NULL
    )");
    echo "Created/verified 'student_to_admin_messages' table<br>";
    
    // Create application_additional_documents table
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_additional_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_email VARCHAR(255) NOT NULL,
        application_id INT,
        document_name VARCHAR(255),
        document_path VARCHAR(255),
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created/verified 'application_additional_documents' table<br>";
    
    // Create application_form_fields table
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_form_fields (
        id INT AUTO_INCREMENT PRIMARY KEY,
        field_name VARCHAR(255) NOT NULL,
        field_label VARCHAR(255) NOT NULL,
        field_type ENUM('text', 'email', 'tel', 'date', 'textarea', 'file', 'select') DEFAULT 'text',
        is_required BOOLEAN DEFAULT 1,
        field_options TEXT,
        display_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created/verified 'application_form_fields' table<br>";
    
    // Create application_custom_data table
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_custom_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_email VARCHAR(255) NOT NULL,
        field_name VARCHAR(255) NOT NULL,
        field_value TEXT,
        file_path VARCHAR(255),
        UNIQUE KEY unique_student_field (student_email, field_name)
    )");
    echo "Created/verified 'application_custom_data' table<br>";
    
    echo "<br><strong style='color:green;'>Database schema fixed successfully!</strong>";
    echo "<br><br><a href='student_applications.php'>Go to Student Applications</a>";
    
} catch (PDOException $e) {
    echo "<strong style='color:red;'>Error: " . $e->getMessage() . "</strong>";
}
?>
