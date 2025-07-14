<?php
die('start');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../admin/includes/db.php';

echo "Script started\n";
@ob_flush(); flush();

try {
    $dbname = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Connected to database: $dbname\n";
    @ob_flush(); flush();
    $count = $pdo->query('SELECT COUNT(*) FROM about_content')->fetchColumn();
    echo "Row count: $count\n";
    @ob_flush(); flush();
    $rows = $pdo->query('SELECT * FROM about_content')->fetchAll();
    echo "\n--- about_content table ---\n";
    foreach ($rows as $row) {
        print_r($row);
        echo "\n-------------------------\n";
        @ob_flush(); flush();
    }
    if (empty($rows)) {
        echo "No rows found in about_content table.\n";
        @ob_flush(); flush();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    @ob_flush(); flush();
} 