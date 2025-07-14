<?php
require_once '../admin/includes/db.php';

try {
    $count = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO about_content (section, title, content) VALUES (?, ?, ?)");
        $stmt->execute([
            'main',
            'About Our School',
            'Welcome to our prestigious educational institution. We are committed to providing excellence in education and fostering the growth of our students in a supportive and innovative environment.'
        ]);
        echo "Inserted default about content.\n";
    } else {
        echo "about_content table already has data.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 