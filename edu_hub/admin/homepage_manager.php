<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_hero'])) {
            // Update hero title and subtitle
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['hero', $_POST['hero_title'], $_POST['hero_subtitle']]);
            $message = 'Hero section updated successfully!';
        }
        
        if (isset($_POST['update_about'])) {
            // Update about title and content
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['about', $_POST['about_title'], $_POST['about_content']]);
            $message = 'About section updated successfully!';
        }

        if (isset($_POST['update_school_info'])) {
            // Update school name and tagline
            $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW()");
            $stmt->execute(['school_info', $_POST['school_name'], $_POST['school_tagline']]);
            $message = 'School information updated successfully!';
        }

        // Handle hero background image upload
        if (isset($_FILES['hero_background']) && $_FILES['hero_background']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hero_background'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'hero_bg_' . time() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    // Delete old hero background if exists
                    $old_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'hero' AND title = 'Hero Background' LIMIT 1")->fetchColumn();
                    if ($old_image && file_exists($old_image)) {
                        unlink($old_image);
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, image_path, updated_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), updated_at = NOW()");
                    $stmt->execute(['hero', 'Hero Background', '', $target]);
                    $message = 'Hero background image updated successfully!';
                } else {
                    $error = 'Failed to upload hero background image!';
                }
            } else {
                $error = 'Invalid file type for hero background. Only JPG, PNG, GIF, and WebP are allowed.';
            }
        }

        // Handle about section image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['about_image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'about_' . time() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    // Delete old about image if exists
                    $old_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'about' AND title = 'About Image' LIMIT 1")->fetchColumn();
                    if ($old_image && file_exists($old_image)) {
                        unlink($old_image);
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, image_path, updated_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), updated_at = NOW()");
                    $stmt->execute(['about', 'About Image', '', $target]);
                    $message = 'About section image updated successfully!';
                } else {
                    $error = 'Failed to upload about section image!';
                }
            } else {
                $error = 'Invalid file type for about image. Only JPG, PNG, GIF, and WebP are allowed.';
            }
        }

        // Handle school logo upload
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['school_logo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'logo_' . time() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    // Update school logo in config
                    updateSchoolConfig('school_logo', $filename);
                    $message = 'School logo updated successfully!';
                } else {
                    $error = 'Failed to upload school logo!';
                }
            } else {
                $error = 'Invalid file type for logo. Only JPG, PNG, GIF, and WebP are allowed.';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current content from database
$hero_content = $pdo->query("SELECT * FROM homepage_content WHERE section = 'hero' AND title != 'Hero Background' LIMIT 1")->fetch();
$about_content = $pdo->query("SELECT * FROM homepage_content WHERE section = 'about' AND title != 'About Image' LIMIT 1")->fetch();
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();

// Get images
$hero_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'hero' AND title = 'Hero Background' LIMIT 1")->fetchColumn();
$about_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'about' AND title = 'About Image' LIMIT 1")->fetchColumn();
$school_logo = getSchoolConfig('school_logo', 'school.png');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Manager - Admin Portal</title>
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
        .content-section {
            background: #f8f9fa;
            margin: 1rem;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .hero-section {
            border-left: 4px solid #28a745;
        }
        .about-section {
            border-left: 4px solid #17a2b8;
        }
        .school-section {
            border-left: 4px solid #ffc107;
        }
        .logo-section {
            border-left: 4px solid #dc3545;
        }
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .logo-preview {
            max-width: 150px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 8px;
            margin: 10px 0;
            background: #f8f9fa;
            padding: 10px;
            border: 2px solid #dee2e6;
        }
        .form-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        .btn-update {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            color: white;
        }
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            background: #f8f9fa;
            transition: border-color 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: #007bff;
        }
        .current-content-preview {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 3px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-home me-3"></i>Homepage Content Manager</h1>
            <p class="mb-0">Edit hero section, about content, and main page elements dynamically</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Homepage Content Management</h5>
                <div>
                    <a href="../check/user/index.php" class="btn btn-outline-primary me-2" target="_blank">
                        <i class="fas fa-eye me-2"></i>Preview Website
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
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

            <!-- School Information Section -->
            <div class="content-section school-section">
                <div class="form-section-title">
                    <i class="fas fa-school text-warning me-2"></i>School Information
                </div>
                
                <div class="current-content-preview">
                    <strong>Current School Name:</strong> <?= htmlspecialchars($school_info['title'] ?? 'Not set') ?><br>
                    <strong>Current Tagline:</strong> <?= htmlspecialchars($school_info['content'] ?? 'Not set') ?>
                </div>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">School Name</label>
                                <input type="text" name="school_name" class="form-control" 
                                       value="<?= htmlspecialchars($school_info['title'] ?? 'City Montessori School') ?>" 
                                       placeholder="Enter school name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">School Tagline</label>
                                <input type="text" name="school_tagline" class="form-control" 
                                       value="<?= htmlspecialchars($school_info['content'] ?? 'Empowering Excellence, Fostering Growth') ?>" 
                                       placeholder="Enter school tagline" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_school_info" class="btn btn-update">
                        <i class="fas fa-save me-2"></i>Update School Information
                    </button>
                </form>
            </div>

            <!-- Hero Section -->
            <div class="content-section hero-section">
                <div class="form-section-title">
                    <i class="fas fa-star text-success me-2"></i>Hero Section (Main Banner)
                </div>
                
                <div class="current-content-preview">
                    <strong>Current Title:</strong> <?= htmlspecialchars($hero_content['title'] ?? 'Not set') ?><br>
                    <strong>Current Subtitle:</strong> <?= htmlspecialchars($hero_content['content'] ?? 'Not set') ?>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hero Title</label>
                                <input type="text" name="hero_title" class="form-control" 
                                       value="<?= htmlspecialchars($hero_content['title'] ?? 'WELCOME TO CITY MONTESSORI SCHOOL') ?>" 
                                       placeholder="Enter main hero title" required>
                                <small class="text-muted">This appears as the main heading on your homepage</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hero Subtitle</label>
                                <input type="text" name="hero_subtitle" class="form-control" 
                                       value="<?= htmlspecialchars($hero_content['content'] ?? 'Where Excellence Meets Opportunity') ?>" 
                                       placeholder="Enter hero subtitle" required>
                                <small class="text-muted">This appears below the main title</small>
                            </div>
                            <button type="submit" name="update_hero" class="btn btn-update">
                                <i class="fas fa-save me-2"></i>Update Hero Content
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hero Background Image</label>
                            <?php if ($hero_image): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($hero_image) ?>" class="preview-image d-block" alt="Current Hero Background">
                                    <small class="text-muted">Current background image</small>
                                </div>
                            <?php endif; ?>
                            <div class="file-upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <input type="file" name="hero_background" class="form-control" accept="image/*">
                                <small class="text-muted">Upload new hero background image (JPG, PNG, GIF, WebP)</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- About Section -->
            <div class="content-section about-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle text-info me-2"></i>About Section
                </div>
                
                <div class="current-content-preview">
                    <strong>Current Title:</strong> <?= htmlspecialchars($about_content['title'] ?? 'Not set') ?><br>
                    <strong>Current Content:</strong> <?= htmlspecialchars(substr($about_content['content'] ?? 'Not set', 0, 100)) ?>...
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">About Section Title</label>
                                <input type="text" name="about_title" class="form-control" 
                                       value="<?= htmlspecialchars($about_content['title'] ?? 'About City Montessori School') ?>" 
                                       placeholder="Enter about section title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">About Content</label>
                                <textarea name="about_content" class="form-control" rows="6" 
                                          placeholder="Enter detailed about content..." required><?= htmlspecialchars($about_content['content'] ?? 'Empowering Excellence, Fostering Growth. City Montessori School provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.') ?></textarea>
                                <small class="text-muted">This content appears in the about section on your homepage</small>
                            </div>
                            <button type="submit" name="update_about" class="btn btn-update">
                                <i class="fas fa-save me-2"></i>Update About Content
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">About Section Image</label>
                            <?php if ($about_image): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($about_image) ?>" class="preview-image d-block" alt="Current About Image">
                                    <small class="text-muted">Current about image</small>
                                </div>
                            <?php endif; ?>
                            <div class="file-upload-area">
                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                <input type="file" name="about_image" class="form-control" accept="image/*">
                                <small class="text-muted">Upload new about section image (JPG, PNG, GIF, WebP)</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- School Logo Section -->
            <div class="content-section logo-section">
                <div class="form-section-title">
                    <i class="fas fa-image text-danger me-2"></i>School Logo
                </div>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload New School Logo</label>
                                <div class="file-upload-area">
                                    <i class="fas fa-upload fa-2x text-muted mb-2"></i>
                                    <input type="file" name="school_logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Upload school logo (JPG, PNG, GIF, WebP) - appears in navbar and throughout the site</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Current Logo</label>
                            <div class="mb-2">
                                <img src="../check/images/<?= htmlspecialchars($school_logo) ?>" class="logo-preview d-block" alt="Current School Logo">
                                <small class="text-muted">Current school logo</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="content-section">
                <div class="form-section-title">
                    <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <a href="notice_manager.php" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-bell me-2"></i>Manage Notices
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="gallery_manager.php" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-images me-2"></i>Manage Gallery
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="leadership_manager.php" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-users me-2"></i>Manage Leadership
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="achievement_manager.php" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-trophy me-2"></i>Manage Achievements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit forms when files are selected
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    // Show confirmation before auto-submit
                    if (confirm('Upload this file immediately?')) {
                        this.closest('form').submit();
                    }
                }
            });
        });

        // Add visual feedback for form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
                    submitBtn.disabled = true;
                }
            });
        });
    </script>
</body>
</html>