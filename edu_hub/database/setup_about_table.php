<?php
// Script to create about_content table
require_once '../admin/includes/db.php';

try {
    // Create about_content table
    $sql = "CREATE TABLE IF NOT EXISTS about_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        image_path VARCHAR(500),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_section_title (section, title)
    )";
    
    $pdo->exec($sql);
    echo "about_content table created successfully!\n";
    
    // Insert default content if table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
    
    if ($count == 0) {
        // Insert default about content
        $stmt = $pdo->prepare("INSERT INTO about_content (section, title, content) VALUES (?, ?, ?)");
        $stmt->execute(['main', 'About Our School', 'Welcome to our prestigious educational institution. We are committed to providing excellence in education and fostering the growth of our students in a supportive and innovative environment.']);
        
        echo "Default about content inserted successfully!\n";
    }
    
    echo "About page setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error setting up about_content table: " . $e->getMessage() . "\n";
}
?> 