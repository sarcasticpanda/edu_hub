<?php
require_once 'admin/includes/db.php';

try {
    echo "<h3>Infrastructure Table Data:</h3>";
    $stmt = $pdo->query('SELECT * FROM infrastructure ORDER BY id ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Description</th><th>Image Path</th><th>Display Order</th><th>Is Active</th></tr>";
    
    foreach($data as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['description'] ?? '', 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($row['image_path'] ?? '') . "</td>";
        echo "<td>" . $row['display_order'] . "</td>";
        echo "<td>" . (isset($row['is_active']) ? $row['is_active'] : 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>Count: " . count($data) . " entries</h3>";
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
