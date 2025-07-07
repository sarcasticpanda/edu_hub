<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_config'])) {
            // Update text configurations
            updateSchoolConfig('school_name', $_POST['school_name']);
            updateSchoolConfig('school_tagline', $_POST['school_tagline']);
            updateSchoolConfig('school_address', $_POST['school_address']);
            updateSchoolConfig('school_phone', $_POST['school_phone']);
            updateSchoolConfig('school_email', $_POST['school_email']);
            
            $message = 'School configuration updated successfully!';
        }

        // Handle logo upload
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['school_logo'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'logo_' . time() . '.' . $ext;
            $target = '../check/images/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                updateSchoolConfig('school_logo', $target);
                $message = 'School logo updated successfully!';
            } else {
                $error = 'Failed to upload logo!';
            }
        }

        // Handle hero background upload
        if (isset($_FILES['hero_background']) && $_FILES['hero_background']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hero_background'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'hero_bg_' . time() . '.' . $ext;
            $target = '../check/images/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                updateSchoolConfig('hero_background', $target);
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
                updateSchoolConfig('about_image', $target);
                $message = 'About image updated successfully!';
            } else {
                $error = 'Failed to upload about image!';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current configuration
$school_name = getSchoolConfig('school_name', 'Your School Name');
$school_tagline = getSchoolConfig('school_tagline', 'Excellence in Education');
$school_address = getSchoolConfig('school_address', 'Your School Address');
$school_phone = getSchoolConfig('school_phone', '+91 12345 67890');
$school_email = getSchoolConfig('school_email', 'info@school.edu');
$school_logo = getSchoolConfig('school_logo');
$hero_background = getSchoolConfig('hero_background');
$about_image = getSchoolConfig('about_image');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Configuration - Admin Portal</title>
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
        .logo-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 8px;
            margin: 10px 0;
            background: #f8f9fa;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-cog me-3"></i>School Configuration</h1>
            <p class="mb-0">Configure school details, logo, and branding</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>School Settings</h5>
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

            <!-- Basic Information -->
            <div class="content-section">
                <h4><i class="fas fa-info-circle text-success me-2"></i>Basic Information</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">School Name</label>
                                <input type="text" name="school_name" class="form-control" 
                                       value="<?= htmlspecialchars($school_name) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">School Tagline</label>
                                <input type="text" name="school_tagline" class="form-control" 
                                       value="<?= htmlspecialchars($school_tagline) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">School Address</label>
                                <textarea name="school_address" class="form-control" rows="3" required><?= htmlspecialchars($school_address) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="school_phone" class="form-control" 
                                       value="<?= htmlspecialchars($school_phone) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="school_email" class="form-control" 
                                       value="<?= htmlspecialchars($school_email) ?>" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_config" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Update Basic Information
                    </button>
                </form>
            </div>

            <!-- Logo and Images -->
            <div class="content-section">
                <h4><i class="fas fa-image text-primary me-2"></i>Logo and Images</h4>
                
                <!-- School Logo -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6>School Logo</h6>
                        <?php if ($school_logo): ?>
                            <img src="<?= htmlspecialchars($school_logo) ?>" class="logo-preview d-block" alt="Current Logo">
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" class="mt-2">
                            <input type="file" name="school_logo" class="form-control mb-2" accept="image/*">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-upload me-1"></i>Upload Logo
                            </button>
                        </form>
                    </div>
                    
                    <!-- Hero Background -->
                    <div class="col-md-4">
                        <h6>Hero Background</h6>
                        <?php if ($hero_background): ?>
                            <img src="<?= htmlspecialchars($hero_background) ?>" class="preview-image d-block" alt="Current Hero Background">
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" class="mt-2">
                            <input type="file" name="hero_background" class="form-control mb-2" accept="image/*">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-upload me-1"></i>Upload Background
                            </button>
                        </form>
                    </div>
                    
                    <!-- About Image -->
                    <div class="col-md-4">
                        <h6>About Section Image</h6>
                        <?php if ($about_image): ?>
                            <img src="<?= htmlspecialchars($about_image) ?>" class="preview-image d-block" alt="Current About Image">
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" class="mt-2">
                            <input type="file" name="about_image" class="form-control mb-2" accept="image/*">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-upload me-1"></i>Upload Image
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>