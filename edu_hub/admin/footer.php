<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_footer'])) {
            $stmt = $pdo->prepare("INSERT INTO footer_content (section, content, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
            
            // Update each section
            $stmt->execute(['contact_email', $_POST['contact_email']]);
            $stmt->execute(['contact_phone', $_POST['contact_phone']]);
            $stmt->execute(['contact_address', $_POST['contact_address']]);
            $stmt->execute(['facebook_link', $_POST['facebook_link']]);
            $stmt->execute(['twitter_link', $_POST['twitter_link']]);
            $stmt->execute(['linkedin_link', $_POST['linkedin_link']]);
            $stmt->execute(['copyright_text', $_POST['copyright_text']]);
            
            $message = 'Footer content updated successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current footer content
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
    <title>Footer Management - Admin Portal</title>
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
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-grip-horizontal me-3"></i>Footer Management</h1>
            <p class="mb-0">Update footer content and social links</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Edit Footer Content</h5>
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
                <h4><i class="fas fa-edit text-primary me-2"></i>Footer Information</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Contact Information</h5>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                <input type="email" name="contact_email" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['contact_email'] ?? 'info@stxaviercollege.in') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone me-2"></i>Phone Number</label>
                                <input type="text" name="contact_phone" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['contact_phone'] ?? '+91 12345 67890') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                                <textarea name="contact_address" class="form-control" rows="3" required><?= htmlspecialchars($footer_data['contact_address'] ?? 'Hyderabad, Telangana, India') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Social Media Links</h5>
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook URL</label>
                                <input type="url" name="facebook_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['facebook_link'] ?? '#') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter URL</label>
                                <input type="url" name="twitter_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['twitter_link'] ?? '#') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-linkedin me-2"></i>LinkedIn URL</label>
                                <input type="url" name="linkedin_link" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['linkedin_link'] ?? '#') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-copyright me-2"></i>Copyright Text</label>
                                <input type="text" name="copyright_text" class="form-control" 
                                       value="<?= htmlspecialchars($footer_data['copyright_text'] ?? '© 2025 St. Xavier\'s College. All rights reserved.') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="update_footer" class="btn btn-primary btn-lg">
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