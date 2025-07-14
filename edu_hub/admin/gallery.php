<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';
$edit_image = null; // Initialize $edit_image

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_image']) && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/images/';

                if (!is_dir($upload_dir_absolute)) {
                    if (!mkdir($upload_dir_absolute, 0777, true)) {
                        $error = 'Failed to create upload directory. Please check folder permissions.';
                    }
                }
                $target_file_absolute = $upload_dir_absolute . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_file_absolute)) {
                    $image_path = '../images/' . $filename; // Path relative to edu_hub/edu_hub/check/user/
                    $stmt = $pdo->prepare("INSERT INTO gallery_images (image_path, title, category, display_location, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$image_path, $_POST['title'], $_POST['category'], $_POST['display_location']]);
                    $message = 'Image uploaded successfully!';
                }
            }
        }
        
        if (isset($_POST['update_image'])) {
            // For updates, we only update title and category, image is handled on add
            $stmt = $pdo->prepare("UPDATE gallery_images SET title = ?, category = ?, display_location = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['category'], $_POST['display_location'], $_POST['image_id']]);
            $message = 'Image updated successfully!';
        }
        
        if (isset($_POST['delete_image'])) {
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
            $stmt->execute([$_POST['image_id']]);
            $image_path_from_db = $stmt->fetchColumn();
            
            // Delete image file if exists, constructing absolute path from stored relative path
            if ($image_path_from_db && file_exists($_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/images/' . basename($image_path_from_db))) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/seqto_edu_share/edu_hub/edu_hub/check/images/' . basename($image_path_from_db));
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

// Get display locations
$display_locations = ['Homepage', 'Main Gallery', 'Both'];

// Get image for editing
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_image = $stmt->fetch();
}
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
            background: linear-gradient(135deg, #e0f2f7 0%, #c1e4ee 100%); /* Lighter, more modern blue gradient */
            min-height: 100vh;
            color: #333;
        }
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .admin-header {
            background: linear-gradient(135deg, #0062cc 0%, #003f8e 100%); /* Deeper blue for header */
            color: white;
            padding: 2.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .upload-form {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 18px;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        .upload-form h4 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 1.5rem;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Adjusted for slightly larger cards */
            gap: 1.5rem;
        }
        .gallery-item {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eee;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .gallery-item img {
            width: 100%;
            height: 220px; /* Slightly increased height */
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        .gallery-item-content {
            padding: 1.5rem;
        }
        .gallery-item-content h6 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        .badge-primary {
            background-color: #007bff !important;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-1px);
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-danger:hover {
            background-color: #bd2130;
            border-color: #bd2130;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-1px);
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
                <h4><i class="fas fa-upload text-success me-2"></i><?= $edit_image ? 'Edit Image' : 'Upload New Image' ?></h4>
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_image): ?>
                        <input type="hidden" name="image_id" value="<?= htmlspecialchars($edit_image['id']) ?>">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_image['image_path']) ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Image File</label>
                                <?php if ($edit_image && $edit_image['image_path']): ?>
                                    <img src="<?= htmlspecialchars($edit_image['image_path']) ?>" class="d-block mb-2" style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 8px;" alt="Current Image">
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*" <?= $edit_image ? '' : 'required' ?>>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Image Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_image['title'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php
                                        $selected_category = '';
                                        if ($edit_image !== null && isset($edit_image['category']) && $edit_image['category'] === $cat) {
                                            $selected_category = 'selected';
                                        }
                                        ?>
                                        <option value="<?= htmlspecialchars($cat) ?>" <?= $selected_category ?> >
                                            <?= htmlspecialchars(ucfirst($cat)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Display Location</label>
                                <select name="display_location" class="form-control" required>
                                    <option value="">Select Display Location</option>
                                    <?php foreach ($display_locations as $loc): ?>
                                        <?php
                                        $selected_location = '';
                                        if ($edit_image !== null && isset($edit_image['display_location']) && $edit_image['display_location'] === $loc) {
                                            $selected_location = 'selected';
                                        }
                                        ?>
                                        <option value="<?= htmlspecialchars($loc) ?>" <?= $selected_location ?> >
                                            <?= htmlspecialchars(ucfirst($loc)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <?php if ($edit_image): ?>
                        <button type="submit" name="update_image" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Update Image
                        </button>
                        <a href="gallery.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php else: ?>
                        <button type="submit" name="add_image" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Upload Image
                        </button>
                    <?php endif; ?>
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
                            <?php
                            $admin_display_image_path = str_replace('../images/', '../check/images/', $image['image_path']);
                            ?>
                            <img src="<?= htmlspecialchars($admin_display_image_path) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
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
                                            <input type="hidden" name="image_id" value="<?= htmlspecialchars($image['id']) ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Image Title</label>
                                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($image['title']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select name="category" class="form-control" required>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($image['category']) && $image['category'] === $cat) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars(ucfirst($cat)) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Display Location</label>
                                                <select name="display_location" class="form-control" required>
                                                    <?php foreach ($display_locations as $loc): ?>
                                                        <option value="<?= htmlspecialchars($loc) ?>" <?= (isset($image['display_location']) && $image['display_location'] === $loc) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars(ucfirst($loc)) ?>
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