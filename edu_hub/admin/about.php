<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Initialize message variables
$message = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

// Clear session messages after displaying
if (isset($_SESSION['success'])) unset($_SESSION['success']);
if (isset($_SESSION['error'])) unset($_SESSION['error']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['about_title'] ?? '';
        $content = $_POST['about_content'] ?? '';
        
        // Handle image upload
        $imagePath = null;
        if (!empty($_FILES['about_image']['name'])) {
            $uploadDir = 'uploads/about/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $fileName = uniqid() . '_' . basename($_FILES['about_image']['name']);
            move_uploaded_file($_FILES['about_image']['tmp_name'], $uploadDir . $fileName);
            $imagePath = $uploadDir . $fileName;
        }

        // Get existing image path if no new image uploaded
        if (empty($imagePath)) {
            $existingData = $pdo->query("SELECT image_path FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch();
            $imagePath = $existingData['image_path'] ?? null;
        }

        // Update or insert content
        $stmt = $pdo->prepare("INSERT INTO about_admin_panel (page_title, page_content, image_path)
                             VALUES (:title, :content, :image)
                             ON DUPLICATE KEY UPDATE
                             page_title = VALUES(page_title),
                             page_content = VALUES(page_content),
                             image_path = VALUES(image_path)");
        
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':image' => $imagePath
        ]);
        
        $message = 'Content updated successfully';
    } catch (PDOException $e) {
        $error = 'Error updating content: ' . $e->getMessage();
    }
}

// Get existing content
$about = $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Page Management - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .admin-container { max-width: 1000px; margin: 20px auto; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .admin-header { background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%); color: white; padding: 2rem; text-align: center; }
        .content-section { background: #f8f9fa; margin: 1rem; padding: 2rem; border-radius: 10px; border-left: 4px solid #17a2b8; }
        .preview-image { max-width: 300px; max-height: 200px; object-fit: cover; border-radius: 8px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-info-circle me-3"></i>About Page Management</h1>
            <p class="mb-0">Update about us content and information</p>
        </div>
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Edit About Page Content</h5>
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
            <div class="content-section">
                <h4><i class="fas fa-edit text-info me-2"></i>About Page Content</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Page Title</label>
                                <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($about['page_title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">About Content</label>
                                <textarea name="about_content" class="form-control" rows="10" required><?= htmlspecialchars($about['page_content'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>Update About Content
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">About Page Image</label>
                            <?php if (!empty($about['image_path'])): ?>
                                <img src="<?= htmlspecialchars($about['image_path']) ?>" class="preview-image d-block" alt="Current About Image">
                            <?php endif; ?>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload new about page image</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>