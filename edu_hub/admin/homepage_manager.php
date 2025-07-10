<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_hero'])) {
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
            $stmt->execute(['hero', $_POST['hero_title'], $_POST['hero_subtitle']]);
            $message = 'Hero section updated successfully!';
        }
        
        if (isset($_POST['update_about'])) {
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
            $stmt->execute(['about', $_POST['about_title'], $_POST['about_content']]);
            $message = 'About section updated successfully!';
        }

        // Handle hero background upload
        if (isset($_FILES['hero_background']) && $_FILES['hero_background']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hero_background'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'hero_bg_' . time() . '.' . $ext;
            $target = '../check/images/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, image_path) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path)");
                $stmt->execute(['hero', 'Hero Background', '', $target]);
                $message = 'Hero background updated successfully!';
            } else {
                $error = 'Failed to upload hero background!';
            }
        }

        // Handle about image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['about_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'about_' . time() . '.' . $ext;
            $target = '../check/images/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, image_path) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path)");
                $stmt->execute(['about', 'About Image', '', $target]);
                $message = 'About image updated successfully!';
            } else {
                $error = 'Failed to upload about image!';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current content
$hero_content = $pdo->query("SELECT * FROM homepage_content WHERE section = 'hero' AND title != 'Hero Background' LIMIT 1")->fetch();
$about_content = $pdo->query("SELECT * FROM homepage_content WHERE section = 'about' AND title != 'About Image' LIMIT 1")->fetch();
$hero_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'hero' AND title = 'Hero Background' LIMIT 1")->fetchColumn();
$about_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'about' AND title = 'About Image' LIMIT 1")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Manager</title>
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
            border-left: 4px solid #28a745;
        }
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-home me-3"></i>Homepage Manager</h1>
            <p class="mb-0">Edit hero section, about content, and main page elements</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Homepage Content Management</h5>
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

            <!-- Hero Section -->
            <div class="content-section">
                <h4><i class="fas fa-star text-warning me-2"></i>Hero Section</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Hero Title</label>
                                <input type="text" name="hero_title" class="form-control" 
                                       value="<?= htmlspecialchars($hero_content['title'] ?? 'WELCOME TO CITY MONTESSORI SCHOOL') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hero Subtitle</label>
                                <input type="text" name="hero_subtitle" class="form-control" 
                                       value="<?= htmlspecialchars($hero_content['content'] ?? 'Where Excellence Meets Opportunity') ?>" required>
                            </div>
                            <button type="submit" name="update_hero" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Update Hero Section
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hero Background Image</label>
                            <?php if ($hero_image): ?>
                                <img src="<?= htmlspecialchars($hero_image) ?>" class="preview-image d-block" alt="Current Hero Background">
                            <?php endif; ?>
                            <input type="file" name="hero_background" class="form-control" accept="image/*">
                            <small class="text-muted">Upload new hero background image</small>
                        </div>
                    </div>
                </form>
            </div>

            <!-- About Section -->
            <div class="content-section">
                <h4><i class="fas fa-info-circle text-success me-2"></i>About Section</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">About Title</label>
                                <input type="text" name="about_title" class="form-control" 
                                       value="<?= htmlspecialchars($about_content['title'] ?? 'About City Montessori School') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">About Content</label>
                                <textarea name="about_content" class="form-control" rows="6" required><?= htmlspecialchars($about_content['content'] ?? 'Empowering Excellence, Fostering Growth. City Montessori School provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.') ?></textarea>
                            </div>
                            <button type="submit" name="update_about" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update About Section
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">About Section Image</label>
                            <?php if ($about_image): ?>
                                <img src="<?= htmlspecialchars($about_image) ?>" class="preview-image d-block" alt="Current About Image">
                            <?php endif; ?>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload new about section image</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>