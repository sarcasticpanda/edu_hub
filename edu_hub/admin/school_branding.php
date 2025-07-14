<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_branding'])) {
            updateSchoolConfig('school_name', $_POST['school_name']);
            $message = 'School branding updated successfully!';
        }

        if (isset($_POST['update_footer'])) {
            $stmt = $pdo->prepare("INSERT INTO footer_content (section, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");
            $stmt->execute(['contact_email', $_POST['contact_email']]);
            $stmt->execute(['contact_phone', $_POST['contact_phone']]);
            $stmt->execute(['contact_address', $_POST['contact_address']]);
            $stmt->execute(['facebook_link', $_POST['facebook_link']]);
            $stmt->execute(['twitter_link', $_POST['twitter_link']]);
            $stmt->execute(['linkedin_link', $_POST['linkedin_link']]);
            $stmt->execute(['copyright_text', $_POST['copyright_text']]);
            $message = 'Footer content updated successfully!';
        }

        // Handle logo upload
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['school_logo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['png', 'jpg', 'jpeg', 'gif'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'logo_' . time() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    updateSchoolConfig('school_logo', $filename);
                    $message = 'School logo updated successfully!';
                } else {
                    $error = 'Failed to upload logo!';
                }
            } else {
                $error = 'Invalid file type. Only PNG, JPG, JPEG, and GIF are allowed.';
            }
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current configuration
$school_name = getSchoolConfig('school_name', 'City Montessori School');
$school_tagline = getSchoolConfig('school_tagline', 'Empowering Excellence, Fostering Growth');
$school_logo = getSchoolConfig('school_logo', 'school.png');

// Get footer content
$footer_data = [];
$result = $pdo->query("SELECT section, content FROM footer_content");
while ($row = $result->fetch()) {
    $footer_data[$row['section']] = $row['content'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Branding Manager</title>
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
        .logo-preview {
            max-width: 150px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 10px;
            border: 2px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-school me-3"></i>School Branding Manager</h1>
            <p class="mb-0">Manage school logo, name, and footer content</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>School Branding Settings</h5>
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

            <!-- School Branding Section -->
            <div class="content-section">
                <h4><i class="fas fa-graduation-cap text-primary me-2"></i>School Information</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">School Name</label>
                                <input type="text" name="school_name" class="form-control" 
                                       value="<?= htmlspecialchars($school_name) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">School Logo</label>
                            <div class="mb-3">
                                <img src="../check/images/<?= htmlspecialchars($school_logo) ?>" 
                                     class="logo-preview d-block mb-2" alt="Current Logo">
                            </div>
                            <input type="file" name="school_logo" class="form-control mb-2" accept="image/*">
                            <small class="text-muted">Upload new school logo (PNG, JPG, JPEG, GIF)</small>
                        </div>
                    </div>
                    <button type="submit" name="update_branding" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update School Information
                    </button>
                </form>
            </div>

            <!-- Footer Content Section -->
            <div class="content-section">
                <h4><i class="fas fa-grip-horizontal text-success me-2"></i>Footer Content</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="contact_email" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['contact_email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="contact_phone" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['contact_phone'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="contact_address" class="form-control" rows="3" required><?= htmlspecialchars($footer_data['contact_address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Social Media Links</h6>
                            <div class="mb-3">
                                <label class="form-label">Facebook URL</label>
                                <input type="url" name="facebook_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['facebook_link'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Twitter URL</label>
                                <input type="url" name="twitter_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['twitter_link'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="url" name="linkedin_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['linkedin_link'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Copyright Text</label>
                                <input type="text" name="copyright_text" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['copyright_text'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="update_footer" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Update Footer Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>