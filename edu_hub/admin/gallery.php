<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_image']) && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                $target = '../check/images/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $stmt = $pdo->prepare("INSERT INTO gallery_images (image_path, title, category, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$target, $_POST['title'], $_POST['category']]);
                    $message = 'Image uploaded successfully!';
                }
            }
        }
        
        if (isset($_POST['update_image'])) {
            $stmt = $pdo->prepare("UPDATE gallery_images SET title = ?, category = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['category'], $_POST['image_id']]);
            $message = 'Image updated successfully!';
        }
        
        if (isset($_POST['delete_image'])) {
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
            $stmt->execute([$_POST['image_id']]);
            $image_path = $stmt->fetchColumn();
            
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            
            $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
            $stmt->execute([$_POST['image_id']]);
            $message = 'Image deleted successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all gallery images
$images = $pdo->query("SELECT * FROM gallery_images ORDER BY created_at DESC")->fetchAll();

// Get categories
$categories = ['photography', 'travel', 'nature', 'fashion', 'lifestyle', 'events', 'campus'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Admin Portal</title>
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
        .upload-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        .gallery-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-item-content {
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-images me-3"></i>Gallery Management</h1>
            <p class="mb-0">Upload and manage gallery images</p>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Gallery</h5>
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

            <!-- Upload Form -->
            <div class="upload-form">
                <h4><i class="fas fa-upload text-success me-2"></i>Upload New Image</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Image File</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Image Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_image" class="btn btn-success">
                        <i class="fas fa-upload me-2"></i>Upload Image
                    </button>
                </form>
            </div>

            <!-- Gallery Grid -->
            <h4><i class="fas fa-th text-info me-2"></i>Gallery Images (<?= count($images) ?>)</h4>
            <?php if (empty($images)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No images found. Upload your first image above.
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="gallery-item">
                            <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
                            <div class="gallery-item-content">
                                <h6><?= htmlspecialchars($image['title']) ?></h6>
                                <p class="text-muted small mb-2">
                                    <span class="badge bg-primary"><?= ucfirst($image['category']) ?></span>
                                </p>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($image['created_at'])) ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $image['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                        <button type="submit" name="delete_image" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $image['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Image Title</label>
                                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($image['title']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select name="category" class="form-control" required>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat ?>" <?= $cat === $image['category'] ? 'selected' : '' ?>>
                                                            <?= ucfirst($cat) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_image" class="btn btn-warning">Update Image</button>
                                        </div>
                                    </form>
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