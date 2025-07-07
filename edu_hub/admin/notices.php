<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_notice'])) {
            $stmt = $pdo->prepare("INSERT INTO notices (title, content, posted_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_POST['title'], $_POST['content'], $_POST['posted_by']]);
            $message = 'Notice added successfully!';
        }
        
        if (isset($_POST['update_notice'])) {
            $stmt = $pdo->prepare("UPDATE notices SET title = ?, content = ?, posted_by = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['content'], $_POST['posted_by'], $_POST['notice_id']]);
            $message = 'Notice updated successfully!';
        }
        
        if (isset($_POST['delete_notice'])) {
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
    <title>Notice Board Management - Admin Portal</title>
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
        .notice-card {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-radius: 8px;
        }
        .notice-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-bell me-3"></i>Notice Board Management</h1>
            <p class="mb-0">Add, edit, and manage website notices</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Notices</h5>
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
                <h4><i class="fas fa-plus-circle text-primary me-2"></i><?= $edit_notice ? 'Edit Notice' : 'Add New Notice' ?></h4>
                <form method="post">
                    <?php if ($edit_notice): ?>
                        <input type="hidden" name="notice_id" value="<?= $edit_notice['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Notice Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($edit_notice['title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notice Content</label>
                                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($edit_notice['content'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Posted By</label>
                                <input type="text" name="posted_by" class="form-control" 
                                       value="<?= htmlspecialchars($edit_notice['posted_by'] ?? 'Principal Office') ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <?php if ($edit_notice): ?>
                                    <button type="submit" name="update_notice" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Update Notice
                                    </button>
                                    <a href="notices.php" class="btn btn-secondary">Cancel Edit</a>
                                <?php else: ?>
                                    <button type="submit" name="add_notice" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Notice
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Notices -->
            <h4><i class="fas fa-list text-info me-2"></i>Existing Notices</h4>
            <?php if (empty($notices)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No notices found. Add your first notice above.
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
                    <div class="notice-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-2"><?= htmlspecialchars($notice['title']) ?></h5>
                                <p class="mb-2"><?= htmlspecialchars($notice['content']) ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>Posted by: <?= htmlspecialchars($notice['posted_by']) ?> | 
                                    <i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($notice['created_at'])) ?>
                                    <?php if ($notice['updated_at']): ?>
                                        | <i class="fas fa-edit me-1"></i>Updated: <?= date('M d, Y', strtotime($notice['updated_at'])) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="ms-3">
                                <a href="notices.php?edit=<?= $notice['id'] ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice?')">
                                    <input type="hidden" name="notice_id" value="<?= $notice['id'] ?>">
                                    <button type="submit" name="delete_notice" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>