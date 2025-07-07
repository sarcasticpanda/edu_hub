<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_contact'])) {
            $stmt = $pdo->prepare("INSERT INTO contact_info (field, value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()");
            
            // Update each field
            $stmt->execute(['address', $_POST['address']]);
            $stmt->execute(['phone', $_POST['phone']]);
            $stmt->execute(['email', $_POST['email']]);
            $stmt->execute(['office_hours', $_POST['office_hours']]);
            $stmt->execute(['map_embed', $_POST['map_embed']]);
            
            $message = 'Contact information updated successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get current contact info
$contact_data = [];
$result = $pdo->query("SELECT field, value FROM contact_info");
while ($row = $result->fetch()) {
    $contact_data[$row['field']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Information Management - Admin Portal</title>
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
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-envelope me-3"></i>Contact Information Management</h1>
            <p class="mb-0">Update contact details and address information</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Edit Contact Information</h5>
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
                <h4><i class="fas fa-edit text-danger me-2"></i>Contact Details</h4>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>College Address</label>
                                <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($contact_data['address'] ?? 'St. Xavier\'s College, 5 Mahapalika Marg, Mumbai, Maharashtra 400001, India') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone me-2"></i>Phone Number</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?= htmlspecialchars($contact_data['phone'] ?? '+91 22 2262 0662') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($contact_data['email'] ?? 'info@stxavierscollege.edu') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-clock me-2"></i>Office Hours</label>
                                <textarea name="office_hours" class="form-control" rows="3"><?= htmlspecialchars($contact_data['office_hours'] ?? 'Monday - Friday: 9:00 AM - 5:00 PM
Saturday: 9:00 AM - 1:00 PM
Sunday: Closed') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-map me-2"></i>Google Maps Embed Code</label>
                                <textarea name="map_embed" class="form-control" rows="4" placeholder="Paste Google Maps embed iframe code here"><?= htmlspecialchars($contact_data['map_embed'] ?? '') ?></textarea>
                                <small class="text-muted">Go to Google Maps, search for your location, click Share > Embed a map, and paste the iframe code here.</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="update_contact" class="btn btn-danger btn-lg">
                            <i class="fas fa-save me-2"></i>Update Contact Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>