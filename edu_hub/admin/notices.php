<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

$upload_dir_relative = '../storage/notice_attachments/';

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
                    $upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/2026/edu_hub/edu_hub/storage/notice_attachments/';
                    
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
                $is_active = isset($_POST['is_active']) ? 1 : 1; // Default to active
                echo "Attempting to insert into database...<br>";
                $stmt = $pdo->prepare("INSERT INTO notices (title, subheading, content, notice_type, posted_by, attachment_path, attachment_type, is_active, is_pinned) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([
                    $_POST['title'],
                    $_POST['subheading'],
                    $_POST['content'],
                    $_POST['notice_type'] ?? 'general',
                    $_POST['posted_by'],
                    $attachment_path,
                    $attachment_type,
                    $is_active,
                    isset($_POST['is_pinned']) ? 1 : 0
                ])) {
                    echo "Database insert successful!<br>";
                    if (empty($message)) {
                        $message = 'Notice added successfully!';
                    }
                    header('Location: notices.php?success=added');
                    exit;
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
                    $upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/2026/edu_hub/edu_hub/storage/notice_attachments/';

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
                $is_active = isset($_POST['is_active']) ? 1 : 1; // Default to active
                $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
                echo "Attempting to update database (Update)...<br>";
                $stmt = $pdo->prepare("UPDATE notices SET title = ?, subheading = ?, content = ?, posted_by = ?, notice_type = ?, is_pinned = ?, attachment_path = ?, attachment_type = ?, is_active = ? WHERE id = ?");
                if ($stmt->execute([
                    $_POST['title'],
                    $_POST['subheading'],
                    $_POST['content'],
                    $_POST['posted_by'],
                    $_POST['notice_type'],
                    $is_pinned,
                    $attachment_path,
                    $attachment_type,
                    $is_active,
                    $_POST['notice_id']
                ])) {
                    echo "Database update successful! (Update)<br>";
                    $message = 'Notice updated successfully!';
                    header('Location: notices.php?success=updated');
                    exit;
                } else {
                    $error_info = $stmt->errorInfo();
                    echo "Database update failed! Error: " . $error_info[2] . "<br>";
                    $error = 'Failed to update notice in database.';
                }
            }
        }
        
        if (isset($_POST['delete_notice'])) {
            // Get attachment path before deleting
            $stmt = $pdo->prepare("SELECT attachment_path FROM notices WHERE id = ?");
            $stmt->execute([$_POST['notice_id']]);
            $attachment = $stmt->fetchColumn();
            
            // Delete attachment file if exists
            $absolute_attachment_path = $_SERVER['DOCUMENT_ROOT'] . '/2026/edu_hub/edu_hub/storage/notice_attachments/' . $attachment;
            if ($attachment && file_exists($absolute_attachment_path)) {
                unlink($absolute_attachment_path);
            }
            
            $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
            if ($stmt->execute([$_POST['notice_id']])) {
                $message = 'Notice deleted successfully!';
                header('Location: notices.php?success=deleted');
                exit;
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
$notices = $pdo->query("SELECT * FROM notices ORDER BY is_pinned DESC, created_at DESC")->fetchAll();

// Handle success messages from redirects
if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'added':
            $message = 'Notice added successfully!';
            break;
        case 'updated':
            $message = 'Notice updated successfully!';
            break;
        case 'deleted':
            $message = 'Notice deleted successfully!';
            break;
    }
}

// Get notice for editing
$edit_notice = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
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
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .notice-card {
            background: #ffffff;
            border-left: 6px solid var(--accent-green);
            margin-bottom: 1.5rem;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .notice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .notice-header {
            background: #f8fafc;
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .notice-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        .notice-subheading {
            font-size: 1rem;
            font-weight: 600;
            color: var(--accent-blue);
            margin-bottom: 0.5rem;
        }
        .notice-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }
        .notice-body {
            padding: 1.5rem;
        }
        .notice-content {
            font-size: 0.95rem;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .notice-attachment {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .attachment-icon {
            font-size: 1.75rem;
        }
        .pdf-icon { color: var(--accent-red); }
        .image-icon { color: var(--accent-green); }
        .document-icon { color: var(--accent-blue); }
        .notice-actions {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .notice-form {
            background: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        .form-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--accent-teal);
        }
        .form-section h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        .attachment-preview {
            max-width: 150px;
            max-height: 100px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: var(--text-muted);
        }
        .file-upload-area:hover {
            border-color: var(--accent-blue);
            background: #eff6ff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-bell"></i>
                <div class="admin-header-info">
                    <h1>Notice Board Management</h1>
                    <p>Add, edit, and manage website notices with attachments</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/notices.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Notices</a>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary">
                        <i class="fas fa-list me-1"></i><span id="noticeCount"><?= count($notices) ?></span> Total
                    </span>
                    <span class="badge bg-success">
                        <i class="fas fa-thumbtack me-1"></i><span id="pinnedCount"><?= count(array_filter($notices, fn($n) => $n['is_pinned'])) ?></span> Pinned
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <label class="me-2 text-muted small">Filter:</label>
                    <select id="categoryFilter" class="form-select form-select-sm" style="width: auto;" onchange="filterNotices()">
                        <option value="all">All Categories</option>
                        <option value="general">General</option>
                        <option value="circular">Circular</option>
                        <option value="announcement">Announcement</option>
                        <option value="event">Event</option>
                    </select>
                </div>
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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Notice Category *</label>
                                    <select name="notice_type" class="form-select" required>
                                        <option value="general" <?= ($edit_notice['notice_type'] ?? 'general') == 'general' ? 'selected' : '' ?>>General</option>
                                        <option value="circular" <?= ($edit_notice['notice_type'] ?? '') == 'circular' ? 'selected' : '' ?>>Circular</option>
                                        <option value="announcement" <?= ($edit_notice['notice_type'] ?? '') == 'announcement' ? 'selected' : '' ?>>Announcement</option>
                                        <option value="event" <?= ($edit_notice['notice_type'] ?? '') == 'event' ? 'selected' : '' ?>>Event</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Pin Notice</label>
                                    <div class="form-check form-switch fs-5 mt-2">
                                        <input class="form-check-input" type="checkbox" id="isPinnedSwitch" name="is_pinned" value="1" <?= ($edit_notice['is_pinned'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isPinnedSwitch">
                                            <i class="fas fa-thumbtack me-2"></i>Pin this notice to top & show on homepage
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                    <div class="col-md-6 notice-item" data-category="<?= htmlspecialchars($notice['notice_type'] ?? 'general') ?>" data-pinned="<?= $notice['is_pinned'] ? '1' : '0' ?>">
                    <div class="notice-card">
                        <div class="notice-header">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <?php if ($notice['is_pinned']): ?>
                                        <span class="badge bg-success me-2">
                                            <i class="fas fa-thumbtack me-1"></i>Pinned
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary">
                                        <?= ucfirst($notice['notice_type'] ?? 'general') ?>
                                    </span>
                                </div>
                            </div>
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
    <script>
        function filterNotices() {
            const selectedCategory = document.getElementById('categoryFilter').value;
            const noticeItems = document.querySelectorAll('.notice-item');
            let visibleCount = 0;
            let pinnedCount = 0;
            
            noticeItems.forEach(item => {
                const category = item.getAttribute('data-category');
                const isPinned = item.getAttribute('data-pinned') === '1';
                
                if (selectedCategory === 'all' || category === selectedCategory) {
                    item.style.display = 'block';
                    visibleCount++;
                    if (isPinned) pinnedCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update counts
            document.getElementById('noticeCount').textContent = visibleCount;
            document.getElementById('pinnedCount').textContent = pinnedCount;
        }
    </script>
</body>
</html> 