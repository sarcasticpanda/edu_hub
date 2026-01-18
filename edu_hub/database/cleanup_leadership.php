<?php
require_once __DIR__ . '/../admin/includes/db.php';

echo "=== Cleaning and Updating Leadership Table ===\n\n";

try {
    // 1. Change section from ENUM to VARCHAR to allow custom sections
    echo "1. Changing section column to VARCHAR...\n";
    $pdo->exec("ALTER TABLE leadership MODIFY COLUMN section VARCHAR(100) DEFAULT 'Primary'");
    echo "   ✓ Done\n";
    
    // 2. Add missing columns if needed
    $columns = $pdo->query("DESCRIBE leadership")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('years_worked', $columns)) {
        echo "2. Adding years_worked column...\n";
        $pdo->exec("ALTER TABLE leadership ADD COLUMN years_worked VARCHAR(50)");
        echo "   ✓ Done\n";
    }
    
    if (!in_array('contact_email', $columns)) {
        echo "3. Adding contact_email column...\n";
        $pdo->exec("ALTER TABLE leadership ADD COLUMN contact_email VARCHAR(255)");
        echo "   ✓ Done\n";
    }
    
    if (!in_array('qualification', $columns)) {
        echo "4. Adding qualification column...\n";
        $pdo->exec("ALTER TABLE leadership ADD COLUMN qualification VARCHAR(255)");
        echo "   ✓ Done\n";
    }
    
    // 3. Delete duplicate/test entries - keep only entries with proper data
    echo "5. Cleaning up test/duplicate entries...\n";
    $pdo->exec("DELETE FROM leadership WHERE name IN ('s', 'sa', 'D', 'CS') OR name LIKE '%test%'");
    echo "   ✓ Done\n";
    
    // 4. Fix empty sections
    echo "6. Fixing empty sections...\n";
    $pdo->exec("UPDATE leadership SET section = 'Primary' WHERE section = '' OR section IS NULL");
    echo "   ✓ Done\n";
    
    // 5. Create leadership_sections table for custom sections
    echo "7. Creating/updating leadership_sections table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS leadership_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add default sections
    $defaultSections = ['Individual', 'Primary', 'Junior', 'Senior', 'Non-Teaching'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO leadership_sections (name) VALUES (?)");
    foreach ($defaultSections as $sec) {
        $stmt->execute([$sec]);
    }
    echo "   ✓ Done\n";
    
    // Show remaining entries
    echo "\n=== Remaining Leadership Entries ===\n";
    $entries = $pdo->query("SELECT * FROM leadership ORDER BY section, display_order")->fetchAll(PDO::FETCH_ASSOC);
    echo "Total: " . count($entries) . "\n";
    foreach ($entries as $e) {
        echo "  ID {$e['id']}: {$e['name']} | {$e['role']} | {$e['section']}\n";
    }
    
    echo "\n=== Done ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
