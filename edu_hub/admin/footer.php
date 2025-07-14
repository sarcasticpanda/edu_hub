<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_footer'])) {
            foreach ($_POST['footer_sections'] as $section => $content) {
            $stmt = $pdo->prepare("INSERT INTO footer_content (section, content, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()");
                $stmt->execute([$section, $content]);
            }
            $message = 'Footer content updated successfully!';
        }
        if (isset($_POST['add_section'])) {
            $new_section = trim($_POST['new_section']);
            if ($new_section !== '') {
                $stmt = $pdo->prepare("INSERT IGNORE INTO footer_content (section, content, updated_at) VALUES (?, '', NOW())");
                $stmt->execute([$new_section]);
                $message = 'New section added!';
            }
        }
        if (isset($_POST['delete_section'])) {
            $section_to_delete = $_POST['delete_section'];
            $stmt = $pdo->prepare("DELETE FROM footer_content WHERE section = ?");
            $stmt->execute([$section_to_delete]);
            $message = 'Section deleted!';
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

// Fetch school info from homepage_content
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();
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
                <h4><i class="fas fa-edit text-primary me-2"></i>Footer Sections</h4>
                <form method="post" class="mb-4 d-flex align-items-center gap-2">
                    <input type="text" name="new_section" class="form-control" placeholder="New Section Name (e.g. instagram_link)" required style="max-width:300px;">
                    <button type="submit" name="add_section" class="btn btn-success"><i class="fas fa-plus me-1"></i>Add Section</button>
                </form>
                <form method="post">
                    <div class="row">
                        <?php foreach ($footer_data as $section => $content): ?>
                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text" style="min-width:140px;"> <?= htmlspecialchars($section) ?> </span>
                                    <input type="text" name="footer_sections[<?= htmlspecialchars($section) ?>]" class="form-control" value="<?= htmlspecialchars($content) ?>">
                                    <button type="submit" name="delete_section" value="<?= htmlspecialchars($section) ?>" class="btn btn-danger" onclick="return confirm('Delete this section?')"><i class="fas fa-trash"></i></button>
                            </div>
                            </div>
                        <?php endforeach; ?>
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

    <footer class="footer mt-auto py-3 bg-light text-center">
        <div class="container">
            <span class="text-muted">&copy; <?= date('Y') ?> <?= htmlspecialchars($school_info['title'] ?? 'Your School Name') ?>. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>