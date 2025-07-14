<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_member'])) {
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'who_' . time() . '_' . uniqid() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $image_path = $target;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO who_is_who (name, position, description, image_path, color_theme, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$_POST['name'], $_POST['position'], $_POST['description'], $image_path, $_POST['color_theme']]);
            $message = 'Member added successfully!';
        }
        
        if (isset($_POST['update_member'])) {
            $image_path = $_POST['existing_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'who_' . time() . '_' . uniqid() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    // Delete old image
                    if ($image_path && file_exists($image_path)) {
                        unlink($image_path);
                    }
                    $image_path = $target;
                }
            }
            
            $stmt = $pdo->prepare("UPDATE who_is_who SET name = ?, position = ?, description = ?, image_path = ?, color_theme = ? WHERE id = ?");
            $stmt->execute([$_POST['name'], $_POST['position'], $_POST['description'], $image_path, $_POST['color_theme'], $_POST['member_id']]);
            $message = 'Member updated successfully!';
        }
        
        if (isset($_POST['delete_member'])) {
            $stmt = $pdo->prepare("SELECT image_path FROM who_is_who WHERE id = ?");
            $stmt->execute([$_POST['member_id']]);
            $image_path = $stmt->fetchColumn();
            
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            
            $stmt = $pdo->prepare("DELETE FROM who_is_who WHERE id = ?");
            $stmt->execute([$_POST['member_id']]);
            $message = 'Member deleted successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all members
$members = $pdo->query("SELECT * FROM who_is_who ORDER BY created_at DESC")->fetchAll();

// Get member for editing
$edit_member = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM who_is_who WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_member = $stmt->fetch();
}

$color_themes = ['red', 'blue', 'saffron', 'green', 'purple', 'teal', 'white'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who is Who Management - Admin Portal</title>
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
        .member-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .member-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .member-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            border: 2px solid #ddd;
        }
        .red { background-color: #FF5252; }
        .blue { background-color: #2196F3; }
        .saffron { background-color: #FF9933; }
        .green { background-color: #4CAF50; }
        .purple { background-color: #8e24aa; }
        .teal { background-color: #00897b; }
        .white { background-color: #ffffff; border-color: #ccc; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-users me-3"></i>Who is Who Management</h1>
            <p class="mb-0">Manage staff and faculty information</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Team Members</h5>
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

            <!-- Add/Edit Member Form -->
            <div class="member-form">
                <h4><i class="fas fa-user-plus text-primary me-2"></i><?= $edit_member ? 'Edit Member' : 'Add New Member' ?></h4>
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_member): ?>
                        <input type="hidden" name="member_id" value="<?= $edit_member['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= $edit_member['image_path'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?= htmlspecialchars($edit_member['name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control" 
                                       value="<?= htmlspecialchars($edit_member['position'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_member['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <?php if ($edit_member && $edit_member['image_path']): ?>
                                    <img src="<?= htmlspecialchars($edit_member['image_path']) ?>" class="d-block mb-2" style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 8px;" alt="Current Image">
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*" <?= $edit_member ? '' : 'required' ?>>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Color Theme</label>
                                <select name="color_theme" class="form-control" required>
                                    <option value="">Select Color Theme</option>
                                    <?php foreach ($color_themes as $theme): ?>
                                        <option value="<?= $theme ?>" <?= ($edit_member && $edit_member['color_theme'] === $theme) ? 'selected' : '' ?>>
                                            <?= ucfirst($theme) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <?php foreach ($color_themes as $theme): ?>
                                        <span class="color-preview <?= $theme ?>" title="<?= ucfirst($theme) ?>"></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <?php if ($edit_member): ?>
                                    <button type="submit" name="update_member" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Update Member
                                    </button>
                                    <a href="whoiswho.php" class="btn btn-secondary">Cancel Edit</a>
                                <?php else: ?>
                                    <button type="submit" name="add_member" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Member
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Members -->
            <h4><i class="fas fa-list text-info me-2"></i>Team Members (<?= count($members) ?>)</h4>
            <?php if (empty($members)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No team members found. Add your first member above.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($members as $member): ?>
                        <div class="col-md-4 mb-3">
                            <div class="member-card">
                                <?php if ($member['image_path']): ?>
                                    <img src="<?= htmlspecialchars($member['image_path']) ?>" class="member-image" alt="<?= htmlspecialchars($member['name']) ?>">
                                <?php endif; ?>
                                <div class="p-3">
                                    <h6 class="mb-1"><?= htmlspecialchars($member['name']) ?></h6>
                                    <p class="text-primary mb-1"><?= htmlspecialchars($member['position']) ?></p>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($member['description']) ?></p>
                                    <p class="mb-2">
                                        <span class="color-preview <?= $member['color_theme'] ?>"></span>
                                        <small class="text-muted"><?= ucfirst($member['color_theme']) ?> theme</small>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <a href="whoiswho.php?edit=<?= $member['id'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                            <button type="submit" name="delete_member" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>