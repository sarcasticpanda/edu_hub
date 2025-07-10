<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_notice'])) {
            $attachment_path = null;
            $attachment_type = null;
            
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['attachment'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'];
                
                if (in_array($ext, $allowed)) {
                    $filename = 'notice_' . time() . '_' . uniqid() . '.' . $ext;
                    $attachments_dir = '../check/notice_attachments/';
                    
                    if (!is_dir($attachments_dir)) {
                        mkdir($attachments_dir, 0755, true);
                    }
                    
                    $target = $attachments_dir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        $attachment_path = $filename;
                        $attachment_type = ($ext === 'pdf') ? 'pdf' : (in_array($ext, ['doc', 'docx']) ? 'document' : 'image');
                    } else {
                        $error = 'Failed to upload attachment. Please check folder permissions.';
                    }
                } else {
                    $error = 'Invalid file type. Only PDF, images, and documents are allowed.';
                }
            }
            
            if (empty($error)) {
                $stmt = $pdo->prepare("INSERT INTO notices (title, subheading, content, posted_by, attachment_path, attachment_type, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['subheading'],
                    $_POST['content'],
                    $_POST['posted_by'],
                    $attachment_path,
                    $attachment_type
                ]);
                $message = 'Notice added successfully' . ($attachment_path ? ' with attachment!' : '!');
            }
        }
        
        if (isset($_POST['update_notice'])) {
            $attachment_path = $_POST['existing_attachment'] ?? null;
            $attachment_type = $_POST['existing_attachment_type'] ?? null;
            
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['attachment'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'];
                
                if (in_array($ext, $allowed)) {
                    $filename = 'notice_' . time() . '_' . uniqid() . '.' . $ext;
                    $attachments_dir = '../check/notice_attachments/';
                    
                    if (!is_dir($attachments_dir)) {
                        mkdir($attachments_dir, 0755, true);
                    }
                    
                    $target = $attachments_dir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        // Delete old attachment if exists
                        if ($attachment_path && file_exists($attachments_dir . $attachment_path)) {
                            unlink($attachments_dir . $attachment_path);
                        }
                        $attachment_path = $filename;
                        $attachment_type = ($ext === 'pdf') ? 'pdf' : (in_array($ext, ['doc', 'docx']) ? 'document' : 'image');
                    }
                }
            }
            
            $stmt = $pdo->prepare("UPDATE notices SET title = ?, subheading = ?, content = ?, posted_by = ?, attachment_path = ?, attachment_type = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $_POST['title'],
                $_POST['subheading'],
                $_POST['content'],
                $_POST['posted_by'],
                $attachment_path,
                $attachment_type,
                $_POST['notice_id']
            ]);
            $message = 'Notice updated successfully!';
        }
        
        if (isset($_POST['delete_notice'])) {
            // Get attachment path before deleting
            $stmt = $pdo->prepare("SELECT attachment_path FROM notices WHERE id = ?");
            $stmt->execute([$_POST['notice_id']]);
            $attachment = $stmt->fetchColumn();
            
            // Delete attachment file if exists
            if ($attachment && file_exists('../check/notice_attachments/' . $attachment)) {
                unlink('../check/notice_attachments/' . $attachment);
            }
            
            $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
            $stmt->execute([$_POST['notice_id']]);
            $message = 'Notice deleted successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all notices
$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC")->fetchAll();

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
    <title>Notice Board Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .admin-container { 
            max-width: 1200px; 
            margin: 20px auto; 
            background: #fff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .admin-header {
            background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .notice-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border-left: 4px solid #ffc107;
        }
        .notice-card {
            background: #fff;
            border-left: 4px solid #007bff;
            margin-bottom: 1.5rem;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .notice-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .notice-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .notice-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1E2A44;
            margin-bottom: 0.5rem;
        }
        .notice-subheading {
            font-size: 1.1rem;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 0.5rem;
        }
        .notice-meta {
            font-size: 0.9rem;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notice-body {
            padding: 1.5rem;
        }
        .notice-content {
            font-size: 1rem;
            color: #495057;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .notice-attachment {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .attachment-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        .pdf-icon { color: #dc3545; }
        .image-icon { color: #28a745; }
        .document-icon { color: #007bff; }
        .notice-actions {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            transition: border-color 0.3s ease;
            cursor: pointer;
        }
        .file-upload-area:hover {
            border-color: #007bff;
        }
        .file-upload-area.dragover {
            border-color: #28a745;
            background: #f0fff4;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-bell me-3"></i>Notice Board Manager</h1>
            <p class="mb-0">Manage notices with PDF and image uploads</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Notice Management</h5>
                <a href="index.php" class="btn btn-secondary">
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
                <h4><i class="fas fa-plus-circle text-warning me-2"></i><?= $edit_notice ? 'Edit Notice' : 'Add New Notice' ?></h4>
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_notice): ?>
                        <input type="hidden" name="notice_id" value="<?= $edit_notice['id'] ?>">
                        <input type="hidden" name="existing_attachment" value="<?= htmlspecialchars($edit_notice['attachment_path'] ?? '') ?>">
                        <input type="hidden" name="existing_attachment_type" value="<?= htmlspecialchars($edit_notice['attachment_type'] ?? '') ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Notice Title *</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($edit_notice['title'] ?? '') ?>" 
                                       placeholder="Enter notice title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Notice Subheading</label>
                                <input type="text" name="subheading" class="form-control" 
                                       value="<?= htmlspecialchars($edit_notice['subheading'] ?? '') ?>" 
                                       placeholder="Enter subheading (optional)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Posted By *</label>
                                <input type="text" name="posted_by" class="form-control" 
                                       value="<?= htmlspecialchars($edit_notice['posted_by'] ?? 'Principal Office') ?>" 
                                       placeholder="e.g., Principal Office" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Notice Content *</label>
                                <textarea name="content" class="form-control" rows="5" 
                                          placeholder="Enter the full notice content..." required><?= htmlspecialchars($edit_notice['content'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Attachment (Optional)</label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Drag and drop files here or click to browse</p>
                                <p class="text-muted small">Supported: PDF, Images (JPG, PNG, GIF), Documents (DOC, DOCX)</p>
                                <input type="file" name="attachment" class="form-control" 
                                       accept=".pdf,.png,.jpg,.jpeg,.gif,.doc,.docx" 
                                       style="display: none;" id="fileInput">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-folder-open me-2"></i>Choose File
                                </button>
                            </div>
                            
                            <?php if (!empty($edit_notice['attachment_path'])): ?>
                                <div class="mt-3">
                                    <strong>Current Attachment:</strong>
                                    <div class="notice-attachment d-inline-block ms-2">
                                        <?php if ($edit_notice['attachment_type'] === 'pdf'): ?>
                                            <i class="fas fa-file-pdf attachment-icon pdf-icon"></i>
                                            <a href="../check/notice_attachments/<?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                               target="_blank" class="text-decoration-none">
                                                View PDF Document
                                            </a>
                                        <?php elseif ($edit_notice['attachment_type'] === 'image'): ?>
                                            <img src="../check/notice_attachments/<?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                                 alt="Attachment" style="max-width: 150px; max-height: 100px; border-radius: 6px; object-fit: cover;">
                                        <?php elseif ($edit_notice['attachment_type'] === 'document'): ?>
                                            <i class="fas fa-file-word attachment-icon document-icon"></i>
                                            <a href="../check/notice_attachments/<?= htmlspecialchars($edit_notice['attachment_path']) ?>" 
                                               target="_blank" class="text-decoration-none">
                                                Download Document
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <?php if ($edit_notice): ?>
                            <button type="submit" name="update_notice" class="btn btn-warning btn-lg me-2">
                                <i class="fas fa-edit me-2"></i>Update Notice
                            </button>
                            <a href="notice_manager.php" class="btn btn-secondary btn-lg">Cancel Edit</a>
                        <?php else: ?>
                            <button type="submit" name="add_notice" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add Notice
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Existing Notices -->
            <h4><i class="fas fa-list text-info me-2"></i>Published Notices (<?= count($notices) ?>)</h4>
            <?php if (empty($notices)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No notices found. Add your first notice above.
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
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
                                                <a href="../check/notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                   target="_blank" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View PDF
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif ($notice['attachment_type'] === 'image'): ?>
                                        <div class="mt-2">
                                            <img src="../check/notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                 alt="Notice Image" style="max-width: 200px; max-height: 150px; border-radius: 8px; object-fit: cover;">
                                        </div>
                                    <?php elseif ($notice['attachment_type'] === 'document'): ?>
                                        <div class="d-flex align-items-center mt-2">
                                            <i class="fas fa-file-word attachment-icon document-icon"></i>
                                            <div>
                                                <a href="../check/notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" 
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
                            <a href="notice_manager.php?edit=<?= $notice['id'] ?>" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice and its attachment?')">
                                <input type="hidden" name="notice_id" value="<?= $notice['id'] ?>">
                                <button type="submit" name="delete_notice" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload drag and drop functionality
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');

        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileDisplay(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                updateFileDisplay(e.target.files[0]);
            }
        });

        function updateFileDisplay(file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileUploadArea.innerHTML = `
                <i class="fas fa-file-check fa-3x text-success mb-3"></i>
                <p class="mb-2"><strong>${fileName}</strong></p>
                <p class="text-muted small">Size: ${fileSize} MB</p>
                <button type="button" class="btn btn-outline-secondary" onclick="resetFileUpload()">
                    <i class="fas fa-times me-2"></i>Remove File
                </button>
            `;
        }

        function resetFileUpload() {
            fileInput.value = '';
            fileUploadArea.innerHTML = `
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                <p class="mb-2">Drag and drop files here or click to browse</p>
                <p class="text-muted small">Supported: PDF, Images (JPG, PNG, GIF), Documents (DOC, DOCX)</p>
                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-folder-open me-2"></i>Choose File
                </button>
            `;
        }
    </script>
</body>
</html>