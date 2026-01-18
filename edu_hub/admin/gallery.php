<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';
$edit_image = null;

// Correct upload directory for 2026 structure
$upload_dir_absolute = $_SERVER['DOCUMENT_ROOT'] . '/2026/edu_hub/edu_hub/storage/gallery/';
$upload_path_relative = '/2026/edu_hub/edu_hub/storage/gallery/'; // URL path

// Create upload directory if it doesn't exist
if (!is_dir($upload_dir_absolute)) {
    mkdir($upload_dir_absolute, 0777, true);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ADD NEW IMAGE
        if (isset($_POST['add_image'])) {
            $title = trim($_POST['title']);
            $category = $_POST['category'];
            $display_location = $_POST['display_location'];
            
            // Prevent campus category (managed in homepage manager)
            if ($category === 'campus') {
                throw new Exception('Campus/Infrastructure images must be managed through Homepage Manager');
            }
            $image_source = $_POST['image_source'] ?? 'upload';
            $image_path = '';
            
            if ($image_source === 'url') {
                // Use URL directly
                $image_url = trim($_POST['image_url']);
                if (empty($image_url)) {
                    throw new Exception('Please provide an image URL');
                }
                $image_path = $image_url;
            } else {
                // Upload file
                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Please select an image file to upload');
                }
                
                $file = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('Invalid file type. Please upload JPG, PNG, GIF or WebP');
                }
                
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                $target_file = $upload_dir_absolute . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $image_path = $upload_path_relative . $filename;
                } else {
                    throw new Exception('Failed to upload file. Please check folder permissions.');
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO gallery_images (image_path, title, category, display_location, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$image_path, $title, $category, $display_location]);
            $message = 'Image added successfully!';
            header("Location: gallery.php?success=added");
            exit;
        }
        
        // UPDATE IMAGE
        if (isset($_POST['update_image'])) {
            $image_id = $_POST['image_id'];
            $title = trim($_POST['title']);
            $category = $_POST['category'];
            $display_location = $_POST['display_location'];
            
            // Prevent campus category (managed in homepage manager)
            if ($category === 'campus') {
                throw new Exception('Campus/Infrastructure images must be managed through Homepage Manager');
            }
            $image_source = $_POST['image_source'] ?? 'keep';
            
            // Get current image path
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
            $stmt->execute([$image_id]);
            $current_path = $stmt->fetchColumn();
            
            $new_image_path = $current_path; // Keep existing by default
            
            if ($image_source === 'url') {
                $image_url = trim($_POST['image_url']);
                if (!empty($image_url)) {
                    $new_image_path = $image_url;
                }
            } elseif ($image_source === 'upload' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (in_array($file['type'], $allowed_types)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                    $target_file = $upload_dir_absolute . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        // Delete old file if it was a local upload
                        if ($current_path && strpos($current_path, '/2026/') === 0) {
                            $old_file = $_SERVER['DOCUMENT_ROOT'] . $current_path;
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                        $new_image_path = $upload_path_relative . $filename;
                    }
                }
            }
            
            $stmt = $pdo->prepare("UPDATE gallery_images SET image_path = ?, title = ?, category = ?, display_location = ? WHERE id = ?");
            $stmt->execute([$new_image_path, $title, $category, $display_location, $image_id]);
            $message = 'Image updated successfully!';
            header("Location: gallery.php?success=updated");
            exit;
        }
        
        // DELETE IMAGE
        if (isset($_POST['delete_image'])) {
            $image_id = $_POST['image_id'];
            
            // Get image path
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
            $stmt->execute([$image_id]);
            $image_path = $stmt->fetchColumn();
            
            // Delete local file if exists
            if ($image_path && strpos($image_path, '/2026/') === 0) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . $image_path;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
            $stmt->execute([$image_id]);
            $message = 'Image deleted successfully!';
            header("Location: gallery.php?success=deleted");
            exit;
        }

        // UPDATE GALLERY HERO SECTION
        if (isset($_POST['update_hero'])) {
            $hero_title = trim($_POST['hero_title']);
            $hero_subtitle = trim($_POST['hero_subtitle']);
            $stat1_value = trim($_POST['stat1_value']);
            $stat1_label = trim($_POST['stat1_label']);
            $stat2_value = trim($_POST['stat2_value']);
            $stat2_label = trim($_POST['stat2_label']);
            $stat3_value = trim($_POST['stat3_value']);
            $stat3_label = trim($_POST['stat3_label']);
            
            // Handle background image
            $background_image = $_POST['current_bg_image'];
            
            if (isset($_POST['hero_bg_source']) && $_POST['hero_bg_source'] === 'url' && !empty($_POST['hero_bg_url'])) {
                $background_image = trim($_POST['hero_bg_url']);
            } elseif (isset($_FILES['hero_bg_file']) && $_FILES['hero_bg_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['hero_bg_file'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (in_array($file['type'], $allowed_types)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'gallery_hero_' . time() . '.' . $ext;
                    $target_file = $upload_dir_absolute . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $background_image = $upload_path_relative . $filename;
                    }
                }
            }
            
            // Update or insert hero content
            $stmt = $pdo->prepare("INSERT INTO gallery_hero_content (id, hero_title, hero_subtitle, background_image, stat1_value, stat1_label, stat2_value, stat2_label, stat3_value, stat3_label) 
                                   VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE 
                                   hero_title = VALUES(hero_title),
                                   hero_subtitle = VALUES(hero_subtitle),
                                   background_image = VALUES(background_image),
                                   stat1_value = VALUES(stat1_value),
                                   stat1_label = VALUES(stat1_label),
                                   stat2_value = VALUES(stat2_value),
                                   stat2_label = VALUES(stat2_label),
                                   stat3_value = VALUES(stat3_value),
                                   stat3_label = VALUES(stat3_label)");
            $stmt->execute([$hero_title, $hero_subtitle, $background_image, $stat1_value, $stat1_label, $stat2_value, $stat2_label, $stat3_value, $stat3_label]);
            $message = 'Gallery hero section updated successfully!';
            header("Location: gallery.php?success=hero_updated");
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle success messages from redirect
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added': $message = 'Image added successfully!'; break;
        case 'updated': $message = 'Image updated successfully!'; break;
        case 'deleted': $message = 'Image deleted successfully!'; break;
        case 'hero_updated': $message = 'Gallery hero section updated successfully!'; break;
    }
}

// Get all gallery images (excluding campus - managed in homepage manager)
$images = $pdo->query("SELECT * FROM gallery_images WHERE category != 'campus' ORDER BY created_at DESC")->fetchAll();

// Get categories - matching the new gallery design (campus managed separately in homepage manager)
$categories = ['events', 'sports', 'cultural', 'academic'];

// Get display locations
$display_locations = ['homepage', 'gallery', 'both'];

// Get gallery hero content
$hero_content = $pdo->query("SELECT * FROM gallery_hero_content LIMIT 1")->fetch();
if (!$hero_content) {
    $hero_content = [
        'hero_title' => 'Our Gallery',
        'hero_subtitle' => 'Celebrating decades of academic excellence, cultural heritage, and sporting achievements',
        'background_image' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920&h=1080&fit=crop',
        'stat1_value' => '50+',
        'stat1_label' => 'Years of Legacy',
        'stat2_value' => '100+',
        'stat2_label' => 'Memories Captured',
        'stat3_value' => '5+',
        'stat3_label' => 'Categories'
    ];
}

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
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .upload-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
        }
        .source-toggle {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .source-toggle .btn {
            flex: 1;
            padding: 0.75rem;
            font-weight: 600;
        }
        .source-toggle .btn.active {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
        }
        .source-section {
            display: none;
        }
        .source-section.active {
            display: block;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .gallery-item {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eee;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        .gallery-item-content {
            padding: 1.25rem;
        }
        .gallery-item-content h6 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .badge-category {
            background: var(--accent-green) !important;
        }
        .badge-location {
            background: var(--accent-blue) !important;
        }
        .image-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        .gallery-item-wrapper {
            position: relative;
        }
        .url-preview {
            max-height: 150px;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-images"></i>
                <div class="admin-header-info">
                    <h1>Gallery Management</h1>
                    <p>Upload images or use URLs to manage your gallery</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/gallery.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Gallery</a>
            </div>
        </div>

        <div class="container-fluid p-4">
            <!-- Info Banner -->
            <div class="alert alert-info mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Campus life and infrastructure images are managed separately through the 
                <a href="homepage.php" class="alert-link"><i class="fas fa-home me-1"></i>Homepage Manager</a>.
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-success fs-6"><?= count($images) ?> Images</span>
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

            <!-- Gallery Hero Section Management -->
            <div class="card mb-4" style="border-left: 4px solid #1976d2;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Gallery Hero Section</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Customize the hero section that appears at the top of the gallery page - title, subtitle, stats, and background image.
                    </p>
                    
                    <form method="post" enctype="multipart/form-data" id="heroForm">
                        <div class="row">
                            <!-- Hero Title & Subtitle -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hero Title <span class="text-danger">*</span></label>
                                <input type="text" name="hero_title" class="form-control" required
                                       value="<?= htmlspecialchars($hero_content['hero_title']) ?>"
                                       placeholder="Our Gallery">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hero Subtitle <span class="text-danger">*</span></label>
                                <input type="text" name="hero_subtitle" class="form-control" required
                                       value="<?= htmlspecialchars($hero_content['hero_subtitle']) ?>"
                                       placeholder="Celebrating decades of excellence...">
                            </div>

                            <!-- Stats Row 1 -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 1 Value</label>
                                <input type="text" name="stat1_value" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat1_value']) ?>"
                                       placeholder="50+">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 1 Label</label>
                                <input type="text" name="stat1_label" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat1_label']) ?>"
                                       placeholder="Years of Legacy">
                            </div>

                            <!-- Stats Row 2 -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 2 Value</label>
                                <input type="text" name="stat2_value" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat2_value']) ?>"
                                       placeholder="100+">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 2 Label</label>
                                <input type="text" name="stat2_label" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat2_label']) ?>"
                                       placeholder="Memories Captured">
                            </div>

                            <!-- Stats Row 3 -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 3 Value</label>
                                <input type="text" name="stat3_value" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat3_value']) ?>"
                                       placeholder="5+">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stat 3 Label</label>
                                <input type="text" name="stat3_label" class="form-control"
                                       value="<?= htmlspecialchars($hero_content['stat3_label']) ?>"
                                       placeholder="Categories">
                            </div>

                            <!-- Background Image Section -->
                            <div class="col-12 mb-3">
                                <hr>
                                <h6 class="mb-3"><i class="fas fa-image me-2 text-primary"></i>Hero Background Image</h6>
                                
                                <!-- Current Background Preview -->
                                <?php if (!empty($hero_content['background_image'])): ?>
                                <div class="mb-3">
                                    <img src="<?= htmlspecialchars($hero_content['background_image']) ?>" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px; object-fit: cover;"
                                         alt="Current background">
                                    <small class="d-block text-muted mt-1">Current background image</small>
                                </div>
                                <?php endif; ?>
                                
                                <input type="hidden" name="current_bg_image" value="<?= htmlspecialchars($hero_content['background_image']) ?>">
                                
                                <!-- Source Toggle -->
                                <div class="btn-group w-100 mb-3" role="group">
                                    <button type="button" class="btn btn-outline-primary active" onclick="setHeroBgSource('url')" id="btnHeroBgUrl">
                                        <i class="fas fa-link me-2"></i>Use URL
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setHeroBgSource('upload')" id="btnHeroBgUpload">
                                        <i class="fas fa-upload me-2"></i>Upload File
                                    </button>
                                </div>
                                <input type="hidden" name="hero_bg_source" id="heroBgSource" value="url">
                                
                                <!-- URL Input -->
                                <div id="heroBgUrlSection">
                                    <input type="url" name="hero_bg_url" class="form-control" 
                                           placeholder="https://example.com/background.jpg" id="heroBgUrlInput"
                                           value="<?= htmlspecialchars($hero_content['background_image']) ?>">
                                    <small class="text-muted">Enter a direct link to the background image</small>
                                    <div id="heroBgUrlPreview" class="mt-2"></div>
                                </div>
                                
                                <!-- File Upload -->
                                <div id="heroBgUploadSection" style="display: none;">
                                    <input type="file" name="hero_bg_file" class="form-control" accept="image/*">
                                    <small class="text-muted">Upload background image (JPG, PNG, GIF, WebP)</small>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="update_hero" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Hero Section
                        </button>
                        <a href="../public/gallery.php" class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-eye me-2"></i>Preview Gallery Page
                        </a>
                    </form>
                </div>
            </div>

            <!-- Add/Edit Form -->
            <div class="upload-form">
                <h5 class="mb-3">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    <?= $edit_image ? 'Edit Image' : 'Add New Image' ?>
                </h5>
                
                <form method="post" enctype="multipart/form-data" id="galleryForm">
                    <?php if ($edit_image): ?>
                        <input type="hidden" name="image_id" value="<?= $edit_image['id'] ?>">
                    <?php endif; ?>
                    
                    <!-- Image Source Toggle -->
                    <div class="source-toggle">
                        <button type="button" class="btn btn-outline-success active" onclick="setSource('upload')" id="btnUpload">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="setSource('url')" id="btnUrl">
                            <i class="fas fa-link me-2"></i>Use URL
                        </button>
                    </div>
                    
                    <input type="hidden" name="image_source" id="imageSource" value="upload">
                    
                    <div class="row">
                        <!-- Upload Section -->
                        <div class="col-md-6 source-section active" id="uploadSection">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Image File</label>
                                <?php if ($edit_image): ?>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($edit_image['image_path']) ?>" 
                                             class="url-preview" alt="Current image" 
                                             onerror="this.src='https://via.placeholder.com/150?text=Preview'">
                                        <small class="d-block text-muted mt-1">Current image (leave empty to keep)</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*" id="fileInput">
                                <small class="text-muted">Supported: JPG, PNG, GIF, WebP (Max 5MB)</small>
                            </div>
                        </div>
                        
                        <!-- URL Section -->
                        <div class="col-md-6 source-section" id="urlSection">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image URL</label>
                                <input type="url" name="image_url" class="form-control" 
                                       placeholder="https://example.com/image.jpg" id="urlInput"
                                       value="<?= ($edit_image && strpos($edit_image['image_path'], 'http') === 0) ? htmlspecialchars($edit_image['image_path']) : '' ?>">
                                <small class="text-muted">Enter a direct link to the image</small>
                                <div id="urlPreview" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <!-- Common Fields -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required
                                       placeholder="e.g., Annual Day 2024"
                                       value="<?= htmlspecialchars($edit_image['title'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= ($edit_image && $edit_image['category'] === $cat) ? 'selected' : '' ?>>
                                            <?= ucfirst($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Display Location <span class="text-danger">*</span></label>
                                <select name="display_location" class="form-select" required>
                                    <option value="">Select Location</option>
                                    <?php 
                                    $location_labels = [
                                        'homepage' => '🏠 Homepage Only',
                                        'gallery' => '🖼️ Gallery Page Only',
                                        'both' => '✨ Homepage & Gallery'
                                    ];
                                    foreach ($display_locations as $loc): 
                                    ?>
                                        <option value="<?= $loc ?>" <?= ($edit_image && strtolower($edit_image['display_location']) === $loc) ? 'selected' : '' ?>>
                                            <?= $location_labels[$loc] ?? ucfirst($loc) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Choose where this image appears</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 justify-content-end">
                        <?php if ($edit_image): ?>
                            <a href="gallery.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update_image" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Update Image
                            </button>
                        <?php else: ?>
                            <button type="submit" name="add_image" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Add Image
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Gallery Grid -->
            <h5 class="mb-3"><i class="fas fa-th me-2 text-primary"></i>Gallery Images</h5>
            
            <?php if (empty($images)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No images in gallery. Add your first image above!
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($images as $image): 
                        $is_url = (strpos($image['image_path'], 'http') === 0);
                    ?>
                        <div class="gallery-item">
                            <div class="gallery-item-wrapper">
                                <img src="<?= htmlspecialchars($image['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($image['title']) ?>"
                                     onerror="this.src='https://via.placeholder.com/300x200?text=Image+Not+Found'">
                                <span class="badge image-type-badge <?= $is_url ? 'bg-info' : 'bg-secondary' ?>">
                                    <?= $is_url ? 'URL' : 'Upload' ?>
                                </span>
                            </div>
                            <div class="gallery-item-content">
                                <h6 title="<?= htmlspecialchars($image['title']) ?>"><?= htmlspecialchars($image['title']) ?></h6>
                                <div class="mb-2">
                                    <span class="badge badge-category"><?= ucfirst($image['category']) ?></span>
                                    <?php 
                                    $loc = strtolower($image['display_location']);
                                    $loc_display = [
                                        'homepage' => '🏠 Homepage Only',
                                        'gallery' => '🖼️ Gallery Only', 
                                        'both' => '✨ Homepage & Gallery'
                                    ];
                                    $loc_class = [
                                        'homepage' => 'bg-primary',
                                        'gallery' => 'bg-info',
                                        'both' => 'bg-success'
                                    ];
                                    ?>
                                    <span class="badge <?= $loc_class[$loc] ?? 'bg-secondary' ?>">
                                        <?= $loc_display[$loc] ?? ucfirst($image['display_location']) ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($image['created_at'])) ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="gallery.php?edit=<?= $image['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this image?')">
                                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                        <button type="submit" name="delete_image" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
    <script>
        function setSource(source) {
            document.getElementById('imageSource').value = source;
            
            // Toggle buttons
            document.getElementById('btnUpload').classList.toggle('active', source === 'upload');
            document.getElementById('btnUrl').classList.toggle('active', source === 'url');
            
            // Toggle sections
            document.getElementById('uploadSection').classList.toggle('active', source === 'upload');
            document.getElementById('urlSection').classList.toggle('active', source === 'url');
        }

        // Hero background source toggle
        function setHeroBgSource(source) {
            document.getElementById('heroBgSource').value = source;
            
            // Toggle buttons
            document.getElementById('btnHeroBgUrl').classList.toggle('active', source === 'url');
            document.getElementById('btnHeroBgUpload').classList.toggle('active', source === 'upload');
            
            // Toggle sections
            document.getElementById('heroBgUrlSection').style.display = source === 'url' ? 'block' : 'none';
            document.getElementById('heroBgUploadSection').style.display = source === 'upload' ? 'block' : 'none';
        }
        
        // URL Preview
        document.getElementById('urlInput')?.addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('urlPreview');
            if (url && url.match(/^https?:\/\/.+/)) {
                preview.innerHTML = `<img src="${url}" class="url-preview" alt="Preview" onerror="this.parentElement.innerHTML='<span class=\\'text-danger\\'>Invalid image URL</span>'">`;
            } else {
                preview.innerHTML = '';
            }
        });

        // Hero Background URL Preview
        document.getElementById('heroBgUrlInput')?.addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('heroBgUrlPreview');
            if (url && url.match(/^https?:\/\/.+/)) {
                preview.innerHTML = `<img src="${url}" class="img-fluid rounded mt-2" style="max-height: 200px; object-fit: cover;" alt="Preview" onerror="this.parentElement.innerHTML='<span class=\\'text-danger\\'>Invalid image URL</span>'">`;
            } else {
                preview.innerHTML = '';
            }
        });
        
        // Auto-detect if editing URL-based image
        <?php if ($edit_image && strpos($edit_image['image_path'], 'http') === 0): ?>
        setSource('url');
        <?php endif; ?>
    </script>
</body>
</html>
