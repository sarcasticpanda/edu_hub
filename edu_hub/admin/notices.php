<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

$upload_dir_relative = '../check/notice_attachments/';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_notice'])) {
            $attachment_path = null;
            $attachment_type = null;
            
            // File upload logic
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['attachment'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'];
                
                if (in_array($ext, $allowed)) {
                    $filename = 'notice_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/notice_attachments/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($upload_dir_absolute)) {
                        if (!mkdir($upload_dir_absolute, 0777, true)) {
                            $error = 'Failed to create upload directory. Please check folder permissions.';
                        }
                    }
                    
                    $target_file_absolute = $upload_dir_absolute . $filename;

                    echo "Attempting to move file from {$file['tmp_name']} to {$target_file_absolute}<br>";
                    if (move_uploaded_file($file['tmp_name'], $target_file_absolute)) {
                        echo "File moved successfully!<br>";
                        $attachment_path = $filename; // Store only the filename in DB
                        $attachment_type = ($ext === 'pdf') ? 'pdf' : (in_array($ext, ['doc', 'docx']) ? 'document' : 'image');
                        $message = 'Notice added successfully with attachment!';
                    } else {
                        echo "Failed to move file!<br>";
                        // Add more specific error detail if move_uploaded_file fails
                        $last_error = error_get_last();
                        echo "PHP Error: " . ($last_error['message'] ?? 'No specific error message') . "<br>";
                        $error = 'Failed to upload attachment.';
                    }
                } else {
                    $error = 'Invalid file type. Only PDF, images, and documents are allowed.';
                }
            } else if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle specific upload errors (if file was attempted but failed)
                switch ($_FILES['attachment']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error = 'The uploaded file was only partially uploaded.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error = 'Missing a temporary folder for uploads.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error = 'Failed to write file to disk.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error = 'A PHP extension stopped the file upload.';
                        break;
                    default:
                        $error = 'An unknown upload error occurred.';
                        break;
                }
            }
            
            if (empty($error)) {
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                echo "Attempting to insert into database...<br>";
                $stmt = $pdo->prepare("INSERT INTO notices_new (title, subheading, content, posted_by, created_at, attachment_path, attachment_type, is_active) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)");
                if ($stmt->execute([
                    $_POST['title'],
                    $_POST['subheading'],
                    $_POST['content'],
                    $_POST['posted_by'],
                    $attachment_path,
                    $attachment_type,
                    $is_active
                ])) {
                    echo "Database insert successful!<br>";
                    if (empty($message)) {
                        $message = 'Notice added successfully!';
                    }
                } else {
                    $error_info = $stmt->errorInfo();
                    echo "Database insert failed! Error: " . $error_info[2] . "<br>";
                    $error = 'Failed to save notice to database.';
                }
            }
        }

        if (isset($_POST['update_notice'])) {
            $attachment_path = $_POST['existing_attachment'] ?? null;
            $attachment_type = $_POST['existing_attachment_type'] ?? null;
            
            // Check for new attachment upload during update
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['attachment'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'];

                if (in_array($ext, $allowed)) {
                    $filename = 'notice_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/notice_attachments/';

                    if (!is_dir($upload_dir_absolute)) {
                        if (!mkdir($upload_dir_absolute, 0777, true)) {
                            $error = 'Failed to create upload directory. Please check folder permissions.';
                        }
                    }

                    $target_file_absolute = $upload_dir_absolute . $filename;

                    echo "Attempting to move file from {$file['tmp_name']} to {$target_file_absolute} (Update)<br>";
                    if (move_uploaded_file($file['tmp_name'], $target_file_absolute)) {
                        echo "File moved successfully! (Update)<br>";
                        // Delete old attachment if exists
                        if ($attachment_path && file_exists($upload_dir_absolute . $attachment_path)) {
                            unlink($upload_dir_absolute . $attachment_path);
                        }
                        $attachment_path = $filename;
                        $attachment_type = ($ext === 'pdf') ? 'pdf' : (in_array($ext, ['doc', 'docx']) ? 'document' : 'image');
                    } else {
                        echo "Failed to move new attachment! (Update)<br>";
                        $last_error = error_get_last();
                        echo "PHP Error (Update): " . ($last_error['message'] ?? 'No specific error message') . "<br>";
                        $error = 'Failed to upload new attachment during update.';
                    }
                } else {
                    $error = 'Invalid file type for new attachment. Only PDF, images, and documents are allowed.';
                }
            } else if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle specific upload errors for update
                switch ($_FILES['attachment']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    // ... add other error cases if necessary, similar to add_notice
                    default:
                        $error = 'An unknown upload error occurred during update.';
                        break;
                }
            }
            
            if (empty($error)) {
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                echo "Attempting to update database (Update)...<br>";
                $stmt = $pdo->prepare("UPDATE notices_new SET title = ?, subheading = ?, content = ?, posted_by = ?, updated_at = NOW(), attachment_path = ?, attachment_type = ?, is_active = ? WHERE id = ?");
                if ($stmt->execute([
                    $_POST['title'],
                    $_POST['subheading'],
                    $_POST['content'],
                    $_POST['posted_by'],
                    $attachment_path,
                    $attachment_type,
                    $is_active,
                    $_POST['notice_id']
                ])) {
                    echo "Database update successful! (Update)<br>";
                    $message = 'Notice updated successfully!';
                } else {
                    $error_info = $stmt->errorInfo();
                    echo "Database update failed! Error: " . $error_info[2] . "<br>";
                    $error = 'Failed to update notice in database.';
                }
            }
        }
        
        if (isset($_POST['delete_notice'])) {
            // Get attachment path before deleting
            $stmt = $pdo->prepare("SELECT attachment_path FROM notices_new WHERE id = ?");
            $stmt->execute([$_POST['notice_id']]);
            $attachment = $stmt->fetchColumn();
            
            // Delete attachment file if exists
            $absolute_attachment_path = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/notice_attachments/' . $attachment;
            if ($attachment && file_exists($absolute_attachment_path)) {
                if (unlink($absolute_attachment_path)) {
                } else {
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM notices_new WHERE id = ?");
            if ($stmt->execute([$_POST['notice_id']])) {
                $message = 'Notice deleted successfully!';
            } else {
                $error_info = $stmt->errorInfo();
                $error = 'Failed to delete notice from database.';
            }
        }

    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all notices
$notices = $pdo->query("SELECT * FROM notices_new ORDER BY created_at DESC")->fetchAll();

// Get notice for editing
$edit_notice = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM notices_new WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_notice = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Board Management - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f2f7 0%, #c1e4ee 100%); /* Lighter, more modern blue gradient */
            min-height: 100vh;
            color: #333;
        }
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .admin-header {
            background: linear-gradient(135deg, #0062cc 0%, #003f8e 100%); /* Deeper blue for header */
            color: white;
            padding: 2.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .notice-card {
            background: #ffffff;
            border-left: 6px solid #28a745; /* Vibrant green for active notices */
            margin-bottom: 1.8rem;
            padding: 0;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
        }
        .notice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            border-color: #007bff; /* Blue on hover */
        }
        .notice-header {
            background: #f0f8ff; /* Light background for header */
            padding: 1.8rem;
            border-bottom: 1px solid #e9ecef;
        }
        .notice-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #343a40;
            margin-bottom: 0.6rem;
        }
        .notice-subheading {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 0.6rem;
        }
        .notice-meta {
            font-size: 0.95rem;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.8rem;
        }
        .notice-body {
            padding: 1.8rem;
        }
        .notice-content {
            font-size: 1.05rem;
            color: #495057;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        .notice-attachment {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.2rem;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .attachment-icon {
            font-size: 2.2rem;
            margin-right: 0.8rem;
        }
        .pdf-icon { color: #dc3545; }
        .image-icon { color: #28a745; }
        .document-icon { color: #007bff; }
        .notice-actions {
            padding: 1.2rem 1.8rem;
            background: #f0f8ff;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 0.7rem;
        }
        .notice-form {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 18px;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        .form-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 5px solid #17a2b8; /* Info blue for form sections */
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-section h6 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 1.2rem;
        }
        .attachment-preview {
            max-width: 180px;
            max-height: 120px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .file-upload-area {
            border: 3px dashed #cce5ff; /* Lighter blue dashed border */
            border-radius: 12px;
            padding: 2.5rem;
            text-align: center;
            background: #f0f8ff;
            transition: border-color 0.3s ease, background 0.3s ease;
            color: #666;
        }
        .file-upload-area:hover {
            border-color: #007bff;
            background: #e6f7ff;
        }
        .file-upload-area.dragover {
            border-color: #28a745;
            background: #e6ffe6;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-1px);
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-1px);
        }
        .alert {
            border-radius: 10px;
            font-size: 1rem;
            padding: 1.2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-control {
            border-radius: 8px;
            padding: 0.8rem 1rem;
            border: 1px solid #ced4da;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0,123,255,.25);
        }
        label.form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        .form-switch .form-check-input {
            height: 1.4em;
            width: 2.5em;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-bell me-3"></i>Notice Board Management</h1>
            <p class="mb-0">Add, edit, and manage website notices with attachments</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h5 class="text-primary fw-bold">Manage Notices</h5>
                <a href="index.php" class="btn btn-secondary rounded-pill px-4 py-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Notice Form -->
            <div class="notice-form">
                <h4 class="mb-4"><i class="fas fa-plus-circle text-primary me-2"></i><?= $edit_notice ? 'Edit Notice' : 'Add New Notice' ?></h4>
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_notice): ?>
                        <input type="hidden" name="notice_id" value="<?= $edit_notice['id'] ?>">
                        <input type="hidden" name="existing_attachment" value="<?= htmlspecialchars($edit_notice['attachment_path'] ?? '') ?>">
                        <input type="hidden" name="existing_attachment_type" value="<?= htmlspecialchars($edit_notice['attachment_type'] ?? '') ?>">
                    <?php endif; ?>

                    <div class="form-section">
                        <h6><i class="fas fa-edit text-success me-2"></i>Notice Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Notice Title *</label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?= htmlspecialchars($edit_notice['title'] ?? '') ?>" 
                                           placeholder="Enter notice title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Posted By *</label>
                                    <input type="text" name="posted_by" class="form-control" 
                                           value="<?= htmlspecialchars($edit_notice['posted_by'] ?? 'Principal Office') ?>" 
                                           placeholder="e.g., Principal Office" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Notice Subheading</label>
                            <input type="text" name="subheading" class="form-control" 
                                   value="<?= htmlspecialchars($edit_notice['subheading'] ?? '') ?>" 
                                   placeholder="Enter subheading (optional)">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Notice Content *</label>
                            <textarea name="content" class="form-control" rows="6" 
                                      placeholder="Enter the full notice content..." required><?= htmlspecialchars($edit_notice['content'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6><i class="fas fa-paperclip text-info me-2"></i>Attachment (Optional)</h6>
                        <input type="file" name="attachment" class="form-control mt-3" 
                                   accept=".pdf,.png,.jpg,.jpeg,.gif,.doc,.docx" 
                                   id="fileInput">
                        <small class="text-muted d-block mt-2">Supported formats: PDF, Images (JPG, PNG, GIF), Documents (DOC, DOCX). Max file size: 64MB.</small>

                        <?php if (!empty($edit_notice['attachment_path'])): ?>
                            <div class="mt-4 p-3 bg-white border rounded-lg shadow-sm d-inline-block">
                                <strong>Current Attachment:</strong>
                                <div class="notice-attachment d-inline-block ms-2">
                                    <?php if ($edit_notice['attachment_type'] === 'pdf'): ?>
                                        <i class="fas fa-file-pdf attachment-icon pdf-icon"></i>
                                        <a href="<?= $upload_dir_relative ?><?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                           target="_blank" class="text-decoration-none fw-bold">
                                            View PDF Document
                                        </a>
                                    <?php elseif ($edit_notice['attachment_type'] === 'image'): ?>
                                        <div class="mt-2">
                                            
                                            <img src="<?= $upload_dir_relative ?><?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                                 alt="Attachment" class="attachment-preview">
                                    <?php elseif ($edit_notice['attachment_type'] === 'document'): ?>
                                        <i class="fas fa-file-word attachment-icon document-icon"></i>
                                        <a href="<?= $upload_dir_relative ?><?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                           target="_blank" class="text-decoration-none fw-bold">
                                            Download Document
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Remove Visibility Section -->
                    <!-- <div class="form-section">
                        <h6><i class="fas fa-eye text-primary me-2"></i>Visibility</h6>
                        <div class="form-check form-switch fs-5">
                            <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" value="1" <?= ($edit_notice['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActiveSwitch">Show this notice on the public website</label>
                        </div>
                    </div> -->

                    <div class="text-center mt-4">
                        <?php if ($edit_notice): ?>
                            <button type="submit" name="update_notice" class="btn btn-warning btn-lg me-3 rounded-pill px-5 py-3 shadow-sm">
                                <i class="fas fa-edit me-2"></i>Update Notice
                            </button>
                            <a href="notices.php" class="btn btn-secondary btn-lg rounded-pill px-5 py-3 shadow-sm">Cancel Edit</a>
                        <?php else: ?>
                            <button type="submit" name="add_notice" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm">
                                <i class="fas fa-plus me-2"></i>Add Notice
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Existing Notices -->
            <h4 class="mt-5 mb-4 text-info fw-bold"><i class="fas fa-list me-2"></i>Published Notices (<?= count($notices) ?>)</h4>
            <?php if (empty($notices)): ?>
                <div class="alert alert-info text-center py-4 border-info">
                    <i class="fas fa-info-circle me-2"></i>No notices found. Add your first notice above.
                </div>
            <?php else: ?>
                <div class="row g-4">
                <?php foreach ($notices as $notice): ?>
                    <div class="col-md-6">
                    <div class="notice-card">
                        <div class="notice-header">
                            <div class="notice-title"><?= htmlspecialchars($notice['title']) ?></div>
                            <?php if (!empty($notice['subheading'])): ?>
                                <div class="notice-subheading"><?= htmlspecialchars($notice['subheading']) ?></div>
                            <?php endif; ?>
                            <div class="notice-meta">
                                <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($notice['posted_by']) ?></span>
                                <span><i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($notice['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="notice-body">
                            <div class="notice-content">
                                <?= nl2br(htmlspecialchars($notice['content'])) ?>
                            </div>
                            
                            <?php if (!empty($notice['attachment_path'])): ?>
                                <div class="notice-attachment">
                                    <strong><i class="fas fa-paperclip me-2"></i>Attachment:</strong>
                                    <?php if ($notice['attachment_type'] === 'pdf'): ?>
                                        <div class="d-flex align-items-center mt-2">
                                            <i class="fas fa-file-pdf attachment-icon pdf-icon"></i>
                                            <div>
                                                <a href="<?= $upload_dir_relative ?><?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                   target="_blank" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View PDF
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif ($notice['attachment_type'] === 'image'): ?>
                                        <div class="mt-2">
                                            
                                            <img src="<?= $upload_dir_relative ?><?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                 alt="Notice Image" class="attachment-preview">
                                        </div>
                                    <?php elseif ($notice['attachment_type'] === 'document'): ?>
                                        <div class="d-flex align-items-center mt-2">
                                            <i class="fas fa-file-word attachment-icon document-icon"></i>
                                            <div>
                                                <a href="<?= $upload_dir_relative ?><?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-download me-1"></i>Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notice-actions">
                            <a href="notices.php?edit=<?= $notice['id'] ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice and its attachment?')">
                                <input type="hidden" name="notice_id" value="<?= $notice['id'] ?>">
                                <button type="submit" name="delete_notice" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 