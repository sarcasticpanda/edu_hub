<?php
$pdo = new PDO('mysql:host=localhost;dbname=school_management_system', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Clean up test sections
$testSections = ['adds', 'manager'];
$stmt = $pdo->prepare('DELETE FROM leadership_sections WHERE name = ?');
foreach ($testSections as $sec) {
    $stmt->execute([$sec]);
    echo "Deleted section: $sec\n";
}

echo "\nRemaining sections:\n";
$sections = $pdo->query('SELECT name FROM leadership_sections ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $sections) . "\n";

echo "\n✓ Cleanup complete!\n";
?>
