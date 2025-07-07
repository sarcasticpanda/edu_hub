<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_achievement'])) {
            $stmt = $pdo->prepare("INSERT INTO achievements (title, description, icon, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['icon']]);
            $message = 'Achievement added successfully!';
        }
        
        if (isset($_POST['update_achievement'])) {
            $stmt = $pdo->prepare("UPDATE achievements SET title = ?, description = ?, icon = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['icon'], $_POST['achievement_id']]);
            $message = 'Achievement updated successfully!';
        }
        
        if (isset($_POST['delete_achievement'])) {
            $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
            $stmt->execute([$_POST['achievement_id']]);
            $message = 'Achievement deleted successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all achievements
$achievements = $pdo->query("SELECT * FROM achievements ORDER BY created_at DESC")->fetchAll();

// Get achievement for editing
$edit_achievement = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_achievement = $stmt->fetch();
}

$icons = ['fas fa-trophy', 'fas fa-award', 'fas fa-medal', 'fas fa-star', 'fas fa-users', 'fas fa-graduation-cap', 'fas fa-certificate', 'fas fa-crown'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements Management - Admin Portal</title>
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
        .achievement-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .achievement-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #28a745;
        }
        .icon-preview {
            font-size: 2rem;
            margin-right: 10px;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-trophy me-3"></i>Achievements Management</h1>
            <p class="mb-0">Manage college achievements and awards</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Achievements</h5>
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

            <!-- Add/Edit Achievement Form -->
            <div class="achievement-form">
                <h4><i class="fas fa-plus-circle text-success me-2"></i><?= $edit_achievement ? 'Edit Achievement' : 'Add New Achievement' ?></h4>
                <form method="post">
                    <?php if ($edit_achievement): ?>
                        <input type="hidden" name="achievement_id" value="<?= $edit_achievement['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Achievement Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($edit_achievement['title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_achievement['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Icon</label>
                                <select name="icon" class="form-control" required>
                                    <option value="">Select Icon</option>
                                    <?php foreach ($icons as $icon): ?>
                                        <option value="<?= $icon ?>" <?= ($edit_achievement && $edit_achievement['icon'] === $icon) ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace(['fas fa-', '-'], ['', ' '], $icon)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <small class="text-muted">Icon Preview:</small>
                                    <div id="iconPreview" class="mt-1">
                                        <?php if ($edit_achievement): ?>
                                            <i class="<?= $edit_achievement['icon'] ?> icon-preview"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <?php if ($edit_achievement): ?>
                                    <button type="submit" name="update_achievement" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Update Achievement
                                    </button>
                                    <a href="achievements.php" class="btn btn-secondary">Cancel Edit</a>
                                <?php else: ?>
                                    <button type="submit" name="add_achievement" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Add Achievement
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Achievements -->
            <h4><i class="fas fa-list text-info me-2"></i>Existing Achievements (<?= count($achievements) ?>)</h4>
            <?php if (empty($achievements)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No achievements found. Add your first achievement above.
                </div>
            <?php else: ?>
                <?php foreach ($achievements as $achievement): ?>
                    <div class="achievement-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 d-flex align-items-start">
                                <i class="<?= htmlspecialchars($achievement['icon']) ?> icon-preview"></i>
                                <div>
                                    <h5 class="mb-2"><?= htmlspecialchars($achievement['title']) ?></h5>
                                    <p class="mb-2"><?= htmlspecialchars($achievement['description']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>Added: <?= date('M d, Y', strtotime($achievement['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="ms-3">
                                <a href="achievements.php?edit=<?= $achievement['id'] ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this achievement?')">
                                    <input type="hidden" name="achievement_id" value="<?= $achievement['id'] ?>">
                                    <button type="submit" name="delete_achievement" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Icon preview functionality
        document.querySelector('select[name="icon"]').addEventListener('change', function() {
            const preview = document.getElementById('iconPreview');
            if (this.value) {
                preview.innerHTML = '<i class="' + this.value + ' icon-preview"></i>';
            } else {
                preview.innerHTML = '';
            }
        });
    </script>
</body>
</html>