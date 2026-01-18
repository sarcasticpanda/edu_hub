<?php
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$type = $_POST['type'] ?? 'events'; // events, officials, students, faculty, infrastructure
$allowed_types = ['events', 'officials', 'students', 'faculty', 'infrastructure'];

if (!in_array($type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload type']);
    exit;
}

$file = $_FILES['image'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file size
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
    exit;
}

// Validate file extension
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP']);
    exit;
}

// Validate image
$image_info = getimagesize($file['tmp_name']);
if ($image_info === false) {
    echo json_encode(['success' => false, 'message' => 'File is not a valid image']);
    exit;
}

// Generate unique filename
$new_filename = uniqid() . '_' . time() . '.' . $file_extension;
$upload_dir = __DIR__ . '/uploads/' . $type . '/';
$upload_path = $upload_dir . $new_filename;

// Ensure directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Return relative path from webroot
    $web_path = '/2026/edu_hub/edu_hub/admin/uploads/' . $type . '/' . $new_filename;
    echo json_encode([
        'success' => true, 
        'message' => 'Image uploaded successfully!',
        'path' => $web_path
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
}
?>
