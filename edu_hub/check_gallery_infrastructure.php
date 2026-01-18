<?php
require_once 'admin/includes/db.php';

// Handle delete request
if (isset($_GET['delete_gallery']) && is_numeric($_GET['delete_gallery'])) {
    $id = (int)$_GET['delete_gallery'];
    $pdo->prepare("DELETE FROM gallery_images WHERE id = ?")->execute([$id]);
    echo "<p style='color:green;font-size:20px;'>✅ Deleted gallery_images entry with ID: $id</p>";
}

if (isset($_GET['delete_infrastructure']) && is_numeric($_GET['delete_infrastructure'])) {
    $id = (int)$_GET['delete_infrastructure'];
    $pdo->prepare("DELETE FROM infrastructure WHERE id = ?")->execute([$id]);
    echo "<p style='color:green;font-size:20px;'>✅ Deleted infrastructure entry with ID: $id</p>";
}

try {
    // 1. GALLERY_IMAGES table - Homepage display
    echo "<h2 style='background:#007bff;color:white;padding:10px;'>1. GALLERY_IMAGES Table (Homepage/Both)</h2>";
    $stmt = $pdo->query("SELECT * FROM gallery_images WHERE LOWER(display_location) IN ('homepage', 'both') ORDER BY id ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Title</th><th>Category</th><th>Display Location</th><th>Action</th></tr>";
    
    foreach($data as $row) {
        $bg = (strlen($row['title']) <= 2) ? "background:#ffcccc;" : "";
        echo "<tr style='$bg'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['category'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['display_location'] ?? '') . "</td>";
        echo "<td><a href='?delete_gallery=" . $row['id'] . "' onclick='return confirm(\"Delete this entry?\")' style='color:red;'>🗑 DELETE</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Count: " . count($data) . " entries</p>";
    
    // 2. INFRASTRUCTURE table
    echo "<h2 style='background:#28a745;color:white;padding:10px;'>2. INFRASTRUCTURE Table</h2>";
    $stmt2 = $pdo->query("SELECT * FROM infrastructure ORDER BY id ASC");
    $data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Title</th><th>Description</th><th>Is Active</th><th>Action</th></tr>";
    
    foreach($data2 as $row) {
        $bg = (strlen($row['title']) <= 2) ? "background:#ffcccc;" : "";
        echo "<tr style='$bg'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
        echo "<td>" . htmlspecialchars(substr($row['description'] ?? '', 0, 30)) . "...</td>";
        echo "<td>" . (isset($row['is_active']) ? $row['is_active'] : 'N/A') . "</td>";
        echo "<td><a href='?delete_infrastructure=" . $row['id'] . "' onclick='return confirm(\"Delete this entry?\")' style='color:red;'>🗑 DELETE</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Count: " . count($data2) . " entries</p>";
    
    // 3. Show entries with short titles (likely the 's' entry)
    echo "<h2 style='background:#dc3545;color:white;padding:10px;'>⚠️ SUSPICIOUS ENTRIES (Short Titles)</h2>";
    $stmt3 = $pdo->query("SELECT 'gallery_images' as tbl, id, title FROM gallery_images WHERE LENGTH(title) <= 3
                          UNION ALL
                          SELECT 'infrastructure' as tbl, id, title FROM infrastructure WHERE LENGTH(title) <= 3");
    $data3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($data3)) {
        echo "<p style='color:green;'>✅ No suspicious short-titled entries found!</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;background:#fff3cd;'>";
        echo "<tr><th>Table</th><th>ID</th><th>Title</th><th>Action</th></tr>";
        foreach($data3 as $row) {
            echo "<tr>";
            echo "<td>" . $row['tbl'] . "</td>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
            $deleteParam = ($row['tbl'] == 'gallery_images') ? 'delete_gallery' : 'delete_infrastructure';
            echo "<td><a href='?$deleteParam=" . $row['id'] . "' onclick='return confirm(\"Delete this entry?\")' style='color:red;font-weight:bold;'>🗑 DELETE THIS</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
