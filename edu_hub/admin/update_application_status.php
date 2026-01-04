<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$app_id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';
$quick = $_GET['quick'] ?? 0;

if ($app_id && $status) {
    $reviewed_by = $_SESSION['admin_name'] ?? 'Admin';
    
    // Check which columns exist
    $has_reviewed_by = !empty($pdo->query("SHOW COLUMNS FROM student_applications LIKE 'reviewed_by'")->fetchAll());
    $has_status_updated = !empty($pdo->query("SHOW COLUMNS FROM student_applications LIKE 'status_updated_at'")->fetchAll());
    
    // Build update query based on available columns
    if ($has_reviewed_by && $has_status_updated) {
        $stmt = $pdo->prepare("UPDATE student_applications 
                              SET status = ?, reviewed_by = ?, status_updated_at = NOW() 
                              WHERE id = ?");
        $stmt->execute([$status, $reviewed_by, $app_id]);
    } elseif ($has_reviewed_by) {
        $stmt = $pdo->prepare("UPDATE student_applications 
                              SET status = ?, reviewed_by = ? 
                              WHERE id = ?");
        $stmt->execute([$status, $reviewed_by, $app_id]);
    } elseif ($has_status_updated) {
        $stmt = $pdo->prepare("UPDATE student_applications 
                              SET status = ?, status_updated_at = NOW() 
                              WHERE id = ?");
        $stmt->execute([$status, $app_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE student_applications 
                              SET status = ? 
                              WHERE id = ?");
        $stmt->execute([$status, $app_id]);
    }
    
    $_SESSION['success_message'] = 'Application status updated to ' . ucwords(str_replace('_', ' ', $status)) . '!';
}

if ($quick) {
    header('Location: student_applications.php');
} else {
    header('Location: view_application.php?id=' . $app_id);
}
exit;
