<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Get current school info
$school_name = getSchoolConfig('school_name', 'Your School Name');
$school_tagline = getSchoolConfig('school_tagline', 'Excellence in Education');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_hero'])) {
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['hero', 'Hero Title', $_POST['hero_title']]);
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['hero', 'Hero Subtitle', $_POST['hero_subtitle']]);
            $message = 'Hero section updated successfully!';
        }
        if (isset($_POST['update_about'])) {
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['about', 'About Title', $_POST['about_title']]);
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['about', 'About Content', $_POST['about_content']]);
            $message = 'About section updated successfully!';
        }
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hero_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'hero_' . time() . '.' . $ext;
            $upload_target = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/images/' . $filename;
            $db_image_path = '../check/images/' . $filename; // Path to store in DB for display
            if (move_uploaded_file($file['tmp_name'], $upload_target)) {
                $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, image_path, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), updated_at = NOW()");
                $stmt->execute(['hero', 'Hero Image', $db_image_path]);
                $message = 'Hero image updated successfully!';
            }
        }
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['about_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'about_' . time() . '.' . $ext;
            $upload_target = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/images/' . $filename;
            $db_image_path = '../check/images/' . $filename; // Path to store in DB for display
            if (move_uploaded_file($file['tmp_name'], $upload_target)) {
                $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, image_path, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), updated_at = NOW()");
                $stmt->execute(['about', 'About Image', $db_image_path]);
                $message = 'About image updated successfully!';
            }
        }
        if (isset($_POST['update_school_info'])) {
            updateSchoolConfig('school_name', $_POST['school_name']);
            updateSchoolConfig('school_tagline', $_POST['school_tagline']);
            $message = 'School information updated successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current content
$hero_title = $pdo->query("SELECT content FROM homepage_content WHERE section = 'hero' AND title = 'Hero Title' LIMIT 1")->fetchColumn();
$hero_subtitle = $pdo->query("SELECT content FROM homepage_content WHERE section = 'hero' AND title = 'Hero Subtitle' LIMIT 1")->fetchColumn();
$hero_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'hero' AND title = 'Hero Image' LIMIT 1")->fetchColumn();
$about_title = $pdo->query("SELECT content FROM homepage_content WHERE section = 'about' AND title = 'About Title' LIMIT 1")->fetchColumn();
$about_content = $pdo->query("SELECT content FROM homepage_content WHERE section = 'about' AND title = 'About Content' LIMIT 1")->fetchColumn();
$about_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'about' AND title = 'About Image' LIMIT 1")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Management - Admin Portal</title>
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
            border-left: 4px solid #007bff;
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
            <h1><i class="fas fa-home me-3"></i>Homepage Management</h1>
            <p class="mb-0">Update hero section, about content, and images</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Edit Homepage Content</h5>
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
                                <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($hero_title ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hero Subtitle</label>
                                <input type="text" name="hero_subtitle" class="form-control" value="<?= htmlspecialchars($hero_subtitle ?? '') ?>" required>
                            </div>
                            <button type="submit" name="update_hero" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Hero Text
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hero Background Image</label>
                            <?php if ($hero_image): ?>
                                <img src="<?= htmlspecialchars($hero_image) ?>" class="preview-image d-block" alt="Current Hero Image">
                            <?php endif; ?>
                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload new hero background image</small>
                        </div>
                    </div>
                </form>
            </div>

            <!-- About Section -->
            <div class="content-section">
                <h4><i class="fas fa-info-circle text-info me-2"></i>About Section</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">About Title</label>
                                <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($about_title ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">About Content</label>
                                <textarea name="about_content" class="form-control" rows="6" required><?= htmlspecialchars($about_content ?? '') ?></textarea>
                            </div>
                            <button type="submit" name="update_about" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>Update About Content
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

            <!-- School Info Section -->
            <div class="content-section">
                <h4><i class="fas fa-school text-primary me-2"></i>School Information</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">School Name</label>
                                <input type="text" name="school_name" class="form-control" value="<?= htmlspecialchars(getSchoolConfig('school_name', 'Your School Name')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">School Tagline</label>
                                <input type="text" name="school_tagline" class="form-control" value="<?= htmlspecialchars(getSchoolConfig('school_tagline', 'Excellence in Education')) ?>" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_school_info" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update School Info
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>