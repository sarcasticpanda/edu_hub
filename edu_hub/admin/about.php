<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_about'])) {
            $stmt = $pdo->prepare("INSERT INTO about_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['main', $_POST['about_title'], $_POST['about_content']]);
            $message = 'About page content updated successfully!';
        }

        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['about_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'about_main_' . time() . '.' . $ext;
            $target = '../check/images/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare("INSERT INTO about_content (section, title, content, image_path, updated_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), updated_at = NOW()");
                $stmt->execute(['main', 'About Image', '', $target]);
                $message = 'About page image updated successfully!';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current content
$about_content = $pdo->query("SELECT * FROM about_content WHERE section = 'main' AND title != 'About Image' LIMIT 1")->fetch();
$about_image = $pdo->query("SELECT image_path FROM about_content WHERE section = 'main' AND title = 'About Image' LIMIT 1")->fetchColumn();
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
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .admin-container { 
            max-width: 1000px; 
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
        .content-section {
            background: #f8f9fa;
            margin: 1rem;
            padding: 2rem;
            border-radius: 10px;
            border-left: 4px solid #17a2b8;
        }
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px 0;
        }
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
                                <input type="text" name="about_title" class="form-control" 
                                       value="<?= htmlspecialchars($about_content['title'] ?? 'About St. Xavier\'s College') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">About Content</label>
                                <textarea name="about_content" class="form-control" rows="10" required><?= htmlspecialchars($about_content['content'] ?? 'St. Xavier\'s College is a premier educational institution committed to excellence in education, research, and community service. Founded with the vision of nurturing young minds and fostering holistic development, our college has been a beacon of learning for students from diverse backgrounds.

Our state-of-the-art facilities, experienced faculty, and comprehensive curriculum ensure that students receive the best possible education. We offer a wide range of undergraduate and postgraduate programs designed to meet the evolving needs of the modern world.

At St. Xavier\'s College, we believe in the power of education to transform lives and communities. Our commitment to academic excellence, combined with our focus on character development and social responsibility, prepares our students to become leaders and change-makers in their chosen fields.

Join us on this journey of discovery, growth, and achievement. Experience the difference that quality education can make in your life.') ?></textarea>
                            </div>
                            <button type="submit" name="update_about" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>Update About Content
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">About Page Image</label>
                            <?php if ($about_image): ?>
                                <img src="<?= htmlspecialchars($about_image) ?>" class="preview-image d-block" alt="Current About Image">
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