<?php
// Test file to check upload functionality and permissions
require_once 'includes/db.php';

echo "<h2>Notice Board Upload Test</h2>";

// Check if notice_attachments directory exists and is writable
$attachments_dir = '../check/notice_attachments/';
echo "<h3>Directory Check:</h3>";
echo "Directory path: " . realpath($attachments_dir) . "<br>";
echo "Directory exists: " . (is_dir($attachments_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($attachments_dir) ? 'YES' : 'NO') . "<br>";

// Create directory if it doesn't exist
if (!is_dir($attachments_dir)) {
    if (mkdir($attachments_dir, 0755, true)) {
        echo "Directory created successfully!<br>";
    } else {
        echo "Failed to create directory!<br>";
    }
}

// Check database table structure
echo "<h3>Database Table Structure:</h3>";
try {
    $result = $pdo->query("DESCRIBE notices");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}

// Test file upload form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Test Result:</h3>";
    $file = $_FILES['test_file'];
    
    echo "File name: " . htmlspecialchars($file['name']) . "<br>";
    echo "File size: " . $file['size'] . " bytes<br>";
    echo "File type: " . htmlspecialchars($file['type']) . "<br>";
    echo "Upload error: " . $file['error'] . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'test_' . time() . '.' . $ext;
        $target = $attachments_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            echo "<strong style='color: green;'>File uploaded successfully to: " . $target . "</strong><br>";
            echo "File exists: " . (file_exists($target) ? 'YES' : 'NO') . "<br>";
            echo "File size on disk: " . filesize($target) . " bytes<br>";
        } else {
            echo "<strong style='color: red;'>Failed to move uploaded file!</strong><br>";
        }
    } else {
        echo "<strong style='color: red;'>Upload error occurred!</strong><br>";
    }
}
?>

<h3>Test File Upload:</h3>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_file" accept=".pdf,.jpg,.png,.gif,.doc,.docx" required>
    <button type="submit">Test Upload</button>
</form>

<h3>Current Notices:</h3>
<?php
try {
    $notices = $pdo->query("SELECT id, title, attachment_path, attachment_type, created_at FROM notices ORDER BY created_at DESC LIMIT 5")->fetchAll();
    if (empty($notices)) {
        echo "No notices found.";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Attachment Path</th><th>Attachment Type</th><th>Created</th></tr>";
        foreach ($notices as $notice) {
            echo "<tr>";
            echo "<td>" . $notice['id'] . "</td>";
            echo "<td>" . htmlspecialchars($notice['title']) . "</td>";
            echo "<td>" . htmlspecialchars($notice['attachment_path'] ?? 'None') . "</td>";
            echo "<td>" . htmlspecialchars($notice['attachment_type'] ?? 'None') . "</td>";
            echo "<td>" . $notice['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "Error fetching notices: " . $e->getMessage();
}
?>

<p><a href="notices.php">Back to Notice Management</a></p>