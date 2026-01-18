<?php
session_start();
// DB connection
$host = 'localhost';
$db   = 'school_management_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    $pdo = null;
    error_log("Database connection failed: " . $e->getMessage());
}

$message = '';
$error = '';

// Ensure leadership_sections table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS leadership_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed default sections if they do not already exist
$defaultSections = ['Individual', 'Primary', 'Junior', 'Senior', 'Non-Teaching'];
$stmtSeed = $pdo->prepare('INSERT IGNORE INTO leadership_sections (name) VALUES (?)');
foreach ($defaultSections as $defSec) {
    $stmtSeed->execute([$defSec]);
}

// Handle Leadership Section Addition
if (isset($_POST['add_leadership_section']) && !empty(trim($_POST['new_section'] ?? ''))) {
    $newSec = trim($_POST['new_section']);
    $stmt = $pdo->prepare('INSERT IGNORE INTO leadership_sections (name) VALUES (?)');
    $stmt->execute([$newSec]);
    $message = 'New section "' . htmlspecialchars($newSec) . '" added successfully!';
}

// Handle Leadership Section Deletion
if (isset($_POST['delete_section']) && !empty($_POST['delete_section'])) {
    $secToDelete = $_POST['delete_section'];
    $defaultSections = ['Individual', 'Primary', 'Junior', 'Senior', 'Non-Teaching'];
    if (!in_array($secToDelete, $defaultSections)) {
        // Check if section has members
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM leadership WHERE section = ?");
        $checkStmt->execute([$secToDelete]);
        if ($checkStmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare('DELETE FROM leadership_sections WHERE name = ?');
            $stmt->execute([$secToDelete]);
            $message = 'Section "' . htmlspecialchars($secToDelete) . '" deleted successfully!';
        } else {
            $error = 'Cannot delete section with existing members. Move or delete members first.';
        }
    } else {
        $error = 'Cannot delete default sections.';
    }
}

// Handle Leadership Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_leadership'])) {
    try {
        $leaderId = $_POST['leader_id'] ?? null;
        $name = $_POST['leader_name'] ?? '';
        $role = $_POST['leader_role'] ?? '';
        $section = $_POST['leader_section'] ?? 'Individual';
        $department = $_POST['leader_department'] ?? '';
        $years_worked = $_POST['years_worked'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';
        $qualification = $_POST['qualification'] ?? '';
        $display_order = $_POST['leader_order'] ?? 0;
        
        $modal_content = json_encode([
            'name' => $name,
            'designation' => $role,
            'department' => $department,
            'years_worked' => $years_worked,
            'contact_email' => $contact_email,
            'qualification' => $qualification
        ]) ?: '{}';

        // Handle image
        $image_path = $_POST['existing_leader_image'] ?? '';
        if (isset($_POST['leader_image_source'])) {
            if ($_POST['leader_image_source'] === 'url' && !empty($_POST['leader_image_url'])) {
                $image_path = $_POST['leader_image_url'];
            } elseif ($_POST['leader_image_source'] === 'upload' && !empty($_FILES['leader_image_file']['name']) && $_FILES['leader_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../storage/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = 'leader_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['leader_image_file']['name']));
                if (move_uploaded_file($_FILES['leader_image_file']['tmp_name'], $uploadDir . $fileName)) {
                    $image_path = '/2026/edu_hub/edu_hub/storage/images/' . $fileName;
                }
            }
        }
        
        if ($leaderId) {
            $stmt = $pdo->prepare("UPDATE leadership SET name = ?, role = ?, section = ?, image_path = ?, modal_content = ?, display_order = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$name, $role, $section, $image_path, $modal_content, $display_order, $leaderId]);
            $message = 'Leadership entry updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO leadership (name, role, section, image_path, modal_content, display_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $role, $section, $image_path, $modal_content, $display_order]);
            $message = 'Leadership entry added successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error saving leadership: ' . $e->getMessage();
    }
}

// Handle Leadership Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_leadership'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM leadership WHERE id = ?");
        $stmt->execute([$_POST['delete_leadership']]);
        $message = 'Leadership entry deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting leadership: ' . $e->getMessage();
    }
}

// Fetch available sections for dropdown
$sections_db = $pdo->query('SELECT name FROM leadership_sections ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
if (!$sections_db) {
    $sections_db = ['Individual', 'Primary', 'Junior', 'Senior', 'Non-Teaching'];
}
$leadershipSections = $sections_db;

// Fetch all data
$hero = $pdo ? $pdo->query("SELECT * FROM about_hero_content LIMIT 1")->fetch() : null;
$about = $pdo ? $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch() : null;
$details = $pdo ? $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR) : [];
$leadership = $pdo ? $pdo->query("SELECT * FROM leadership ORDER BY display_order ASC, created_at DESC")->fetchAll() : [];
$students = $pdo ? $pdo->query("SELECT * FROM about_students ORDER BY display_order ASC, created_at DESC")->fetchAll() : [];
$achievements = $pdo ? $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, achievement_date DESC")->fetchAll() : [];

// Initialize defaults
if (empty($details)) {
    $details = ['motto' => '', 'objective' => '', 'value' => ''];
}

// Handle Hero Section Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    try {
        // Handle background image first
        $bgImage = $hero['background_image'] ?? 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920&h=1080&fit=crop';
        if (isset($_POST['hero_bg_source'])) {
            if ($_POST['hero_bg_source'] === 'url' && !empty($_POST['hero_bg_url'])) {
                $bgImage = $_POST['hero_bg_url'];
            } elseif ($_POST['hero_bg_source'] === 'upload' && !empty($_FILES['hero_bg_file']['name'])) {
                $uploadDir = __DIR__ . '/../storage/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = 'about_hero_' . time() . '.' . pathinfo($_FILES['hero_bg_file']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['hero_bg_file']['tmp_name'], $uploadDir . $fileName)) {
                    $bgImage = '/2026/edu_hub/edu_hub/storage/images/' . $fileName;
                }
            }
        }
        
        // Prepare data in the correct order matching SQL columns
        $hero_title = $_POST['hero_title'] ?? 'About Our School';
        $hero_subtitle = $_POST['hero_subtitle'] ?? '';
        $hero_tagline = $_POST['hero_tagline'] ?? 'Discover Our Legacy';
        $stat1_value = $_POST['stat1_value'] ?? '25+';
        $stat1_label = $_POST['stat1_label'] ?? 'Years of Legacy';
        $stat2_value = $_POST['stat2_value'] ?? '1000+';
        $stat2_label = $_POST['stat2_label'] ?? 'Students Shaped';
        $stat3_value = $_POST['stat3_value'] ?? '50+';
        $stat3_label = $_POST['stat3_label'] ?? 'Dedicated Faculty';
        
        if ($hero) {
            $stmt = $pdo->prepare("UPDATE about_hero_content SET 
                hero_title = ?, hero_subtitle = ?, hero_tagline = ?, background_image = ?,
                stat1_value = ?, stat1_label = ?, stat2_value = ?, stat2_label = ?, stat3_value = ?, stat3_label = ?
                WHERE id = ?");
            $stmt->execute([$hero_title, $hero_subtitle, $hero_tagline, $bgImage, 
                           $stat1_value, $stat1_label, $stat2_value, $stat2_label, $stat3_value, $stat3_label, 
                           $hero['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO about_hero_content 
                (hero_title, hero_subtitle, hero_tagline, background_image, stat1_value, stat1_label, stat2_value, stat2_label, stat3_value, stat3_label)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$hero_title, $hero_subtitle, $hero_tagline, $bgImage, 
                           $stat1_value, $stat1_label, $stat2_value, $stat2_label, $stat3_value, $stat3_label]);
        }
        $message = 'Hero section updated successfully!';
        $hero = $pdo->query("SELECT * FROM about_hero_content LIMIT 1")->fetch();
    } catch (Exception $e) {
        $error = 'Error updating hero section: ' . $e->getMessage();
    }
}

// Handle About Content Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_about'])) {
    try {
        $page_title = $_POST['page_title'] ?? '';
        $page_content = $_POST['page_content'] ?? '';
        $image_path = $about['image_path'] ?? '';
        
        // Debug: Check if file was uploaded
        if (!empty($_FILES['about_image']['name'])) {
            if ($_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../storage/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = 'about_main_' . time() . '.' . pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['about_image']['tmp_name'], $targetPath)) {
                    $image_path = '/2026/edu_hub/edu_hub/storage/images/' . $fileName;
                } else {
                    $error = 'Failed to move uploaded file';
                }
            } else {
                $error = 'File upload error code: ' . $_FILES['about_image']['error'];
            }
        }
        
        // Check if we should update or insert - use ORDER BY id DESC to match the display query
        $checkExisting = $pdo->query("SELECT id FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch();
        if ($checkExisting) {
            $stmt = $pdo->prepare("UPDATE about_admin_panel SET page_title = ?, page_content = ?, image_path = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$page_title, $page_content, $image_path, $checkExisting['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO about_admin_panel (page_title, page_content, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$page_title, $page_content, $image_path]);
        }
        
        // Update motto, objectives, values
        foreach (['motto', 'objective', 'value'] as $section) {
            $contentValue = $_POST[$section] ?? '';
            $existing = $pdo->prepare("SELECT id FROM about_details WHERE section_type = ? LIMIT 1");
            $existing->execute([$section]);
            if ($existing->fetch()) {
                $stmt = $pdo->prepare("UPDATE about_details SET content = ? WHERE section_type = ?");
                $stmt->execute([$contentValue, $section]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO about_details (section_type, content) VALUES (?, ?)");
                $stmt->execute([$section, $contentValue]);
            }
        }
        
        $message = 'About content updated successfully!';
        $about = $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch();
        $details = $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Exception $e) {
        $error = 'Error updating about content: ' . $e->getMessage();
    }
}

// Handle Student Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_student'])) {
    try {
        $studentId = $_POST['student_id'] ?? null;
        $name = $_POST['student_name'] ?? '';
        $role = $_POST['student_role'] ?? '';
        $category = $_POST['student_category'] ?? 'leaders';
        $display_order = $_POST['student_order'] ?? 0;
        $is_featured = isset($_POST['student_featured']) ? 1 : 0;
        
        // Handle image
        $image_path = $_POST['existing_student_image'] ?? '';
        if (isset($_POST['student_image_source'])) {
            if ($_POST['student_image_source'] === 'url' && !empty($_POST['student_image_url'])) {
                $image_path = $_POST['student_image_url'];
            } elseif ($_POST['student_image_source'] === 'upload' && !empty($_FILES['student_image_file']['name']) && $_FILES['student_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../storage/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = 'student_' . time() . '.' . pathinfo($_FILES['student_image_file']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['student_image_file']['tmp_name'], $uploadDir . $fileName)) {
                    $image_path = '/2026/edu_hub/edu_hub/storage/images/' . $fileName;
                }
            }
        }
        
        if ($studentId) {
            $stmt = $pdo->prepare("UPDATE about_students SET name = ?, role = ?, category = ?, image_path = ?, display_order = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([$name, $role, $category, $image_path, $display_order, $is_featured, $studentId]);
            $message = 'Student updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO about_students (name, role, category, image_path, display_order, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $role, $category, $image_path, $display_order, $is_featured]);
            $message = 'Student added successfully!';
        }
        $students = $pdo->query("SELECT * FROM about_students ORDER BY display_order ASC, created_at DESC")->fetchAll();
    } catch (Exception $e) {
        $error = 'Error saving student: ' . $e->getMessage();
    }
}

// Handle Student Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM about_students WHERE id = ?");
        $stmt->execute([$_POST['delete_student']]);
        $message = 'Student deleted successfully!';
        $students = $pdo->query("SELECT * FROM about_students ORDER BY display_order ASC, created_at DESC")->fetchAll();
    } catch (Exception $e) {
        $error = 'Error deleting student: ' . $e->getMessage();
    }
}

// Handle Achievement Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_achievement'])) {
    try {
        $achievementId = $_POST['achievement_id'] ?? null;
        $title = $_POST['achievement_title'] ?? '';
        $year = $_POST['achievement_year'] ?? date('Y');
        $display_order = $_POST['achievement_order'] ?? 0;
        $is_active = isset($_POST['achievement_active']) ? 1 : 0;
        
        // Handle image
        $image_path = $_POST['existing_achievement_image'] ?? '';
        if (isset($_POST['achievement_image_source'])) {
            if ($_POST['achievement_image_source'] === 'url' && !empty($_POST['achievement_image_url'])) {
                $image_path = $_POST['achievement_image_url'];
            } elseif ($_POST['achievement_image_source'] === 'upload' && !empty($_FILES['achievement_image_file']['name']) && $_FILES['achievement_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../storage/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = 'achievement_' . time() . '.' . pathinfo($_FILES['achievement_image_file']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['achievement_image_file']['tmp_name'], $uploadDir . $fileName)) {
                    $image_path = '/2026/edu_hub/edu_hub/storage/images/' . $fileName;
                }
            }
        }
        
        if ($achievementId) {
            $stmt = $pdo->prepare("UPDATE achievements SET title = ?, year = ?, image_path = ?, display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $year, $image_path, $display_order, $is_active, $achievementId]);
            $message = 'Achievement updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO achievements (title, year, image_path, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $year, $image_path, $display_order, $is_active]);
            $message = 'Achievement added successfully!';
        }
        $achievements = $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, achievement_date DESC")->fetchAll();
    } catch (Exception $e) {
        $error = 'Error saving achievement: ' . $e->getMessage();
    }
}

// Handle Achievement Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_achievement'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
        $stmt->execute([$_POST['delete_achievement']]);
        $message = 'Achievement deleted successfully!';
        $achievements = $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, achievement_date DESC")->fetchAll();
    } catch (Exception $e) {
        $error = 'Error deleting achievement: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Page Manager - Admin Portal</title>
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .section-card { 
            background: #f8fafc; 
            margin: 0 0 1.5rem 0; 
            padding: 1.5rem; 
            border-radius: 10px; 
            border-left: 4px solid var(--accent-teal); 
        }
        .section-card.hero { border-left-color: var(--accent-purple); }
        .section-card.about { border-left-color: var(--accent-green); }
        .section-card.students { border-left-color: var(--accent-orange); }
        .section-card.achievements { border-left-color: var(--accent-red); }
        .nav-pills .nav-link { 
            color: #495057; 
            border-radius: 8px; 
            margin: 0 5px; 
            padding: 0.75rem 1rem;
        }
        .nav-pills .nav-link.active { 
            background: var(--primary-gradient); 
        }
        .card-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 1rem; 
        }
        .item-card { 
            background: white; 
            border-radius: 10px; 
            padding: 1rem; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            border: 1px solid #e2e8f0;
        }
        .stat-input-group { 
            display: grid; 
            grid-template-columns: 100px 1fr; 
            gap: 0.5rem; 
            margin-bottom: 1rem; 
        }
        .btn-icon { 
            padding: 0.25rem 0.5rem; 
            font-size: 0.875rem; 
        }
        .toggle-source { 
            cursor: pointer; 
            padding: 0.5rem 1rem; 
            border-radius: 5px; 
            background: #e9ecef; 
        }
        .toggle-source.active { 
            background: var(--accent-teal); 
            color: white; 
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-info-circle"></i>
                <div class="admin-header-info">
                    <h1>About Page Manager</h1>
                    <p>Manage all about page content, students, and achievements</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/about.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Site</a>
            </div>
        </div>
        
        <div class="container-fluid p-4">
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills mb-4 justify-content-center" id="aboutTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#heroTab"><i class="fas fa-image me-2"></i>Hero Section</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#aboutTab"><i class="fas fa-school me-2"></i>About Content</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#leadershipTab"><i class="fas fa-users me-2"></i>Leadership</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#studentsTab"><i class="fas fa-user-graduate me-2"></i>Students</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#achievementsTab"><i class="fas fa-trophy me-2"></i>Achievements</a></li>
            </ul>

            <div class="tab-content">
                <!-- Hero Section Tab -->
                <div class="tab-pane fade show active" id="heroTab">
                    <div class="section-card hero">
                        <h4><i class="fas fa-star me-2" style="color: var(--accent-purple);"></i>Hero Section</h4>
                        <form method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Hero Title</label>
                                        <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($hero['hero_title'] ?? 'About Our School') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tagline (above title)</label>
                                        <input type="text" name="hero_tagline" class="form-control" value="<?= htmlspecialchars($hero['hero_tagline'] ?? 'Discover Our Legacy') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Subtitle</label>
                                        <textarea name="hero_subtitle" class="form-control" rows="2"><?= htmlspecialchars($hero['hero_subtitle'] ?? 'Nurturing minds, building futures') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Background Image</label>
                                    <div class="mb-2">
                                        <span class="toggle-source active" onclick="setHeroSource('url')">URL</span>
                                        <span class="toggle-source" onclick="setHeroSource('upload')">Upload</span>
                                        <input type="hidden" name="hero_bg_source" id="hero_bg_source" value="url">
                                    </div>
                                    <div id="hero_url_input">
                                        <input type="text" name="hero_bg_url" class="form-control" placeholder="Image URL" value="<?= htmlspecialchars($hero['background_image'] ?? '') ?>">
                                    </div>
                                    <div id="hero_upload_input" style="display:none;">
                                        <input type="file" name="hero_bg_file" class="form-control" accept="image/*">
                                    </div>
                                    <?php if (!empty($hero['background_image'])): ?>
                                        <img src="<?= htmlspecialchars($hero['background_image']) ?>" class="preview-image d-block mt-2">
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <h5 class="mt-4 mb-3">Statistics (shown at bottom of hero)</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-input-group">
                                        <input type="text" name="stat1_value" class="form-control" placeholder="25+" value="<?= htmlspecialchars($hero['stat1_value'] ?? '25+') ?>">
                                        <input type="text" name="stat1_label" class="form-control" placeholder="Years of Legacy" value="<?= htmlspecialchars($hero['stat1_label'] ?? 'Years of Legacy') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-input-group">
                                        <input type="text" name="stat2_value" class="form-control" placeholder="1000+" value="<?= htmlspecialchars($hero['stat2_value'] ?? '1000+') ?>">
                                        <input type="text" name="stat2_label" class="form-control" placeholder="Students Shaped" value="<?= htmlspecialchars($hero['stat2_label'] ?? 'Students Shaped') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-input-group">
                                        <input type="text" name="stat3_value" class="form-control" placeholder="50+" value="<?= htmlspecialchars($hero['stat3_value'] ?? '50+') ?>">
                                        <input type="text" name="stat3_label" class="form-control" placeholder="Dedicated Faculty" value="<?= htmlspecialchars($hero['stat3_label'] ?? 'Dedicated Faculty') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="update_hero" class="btn btn-primary mt-3">
                                <i class="fas fa-save me-2"></i>Update Hero Section
                            </button>
                        </form>
                    </div>
                </div>

                <!-- About Content Tab -->
                <div class="tab-pane fade" id="aboutTab">
                    <div class="section-card about">
                        <h4><i class="fas fa-edit text-success me-2"></i>About Content</h4>
                        <form method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">School/Institution Name</label>
                                        <input type="text" name="page_title" class="form-control" value="<?= htmlspecialchars($about['page_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">About Description</label>
                                        <textarea name="page_content" class="form-control" rows="6" required><?= htmlspecialchars($about['page_content'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Main Image</label>
                                    <?php if (!empty($about['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($about['image_path']) ?>" class="preview-image d-block">
                                    <?php endif; ?>
                                    <input type="file" name="about_image" class="form-control mt-2" accept="image/*">
                                </div>
                            </div>
                            
                            <h5 class="mt-4 mb-3">Motto, Objectives & Values</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-success fw-bold">Motto</label>
                                        <textarea name="motto" class="form-control" rows="3" placeholder="School motto or tagline"><?= htmlspecialchars($details['motto'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-danger fw-bold">Objectives</label>
                                        <textarea name="objective" class="form-control" rows="3" placeholder="One objective per line"><?= htmlspecialchars($details['objective'] ?? '') ?></textarea>
                                        <small class="text-muted">Enter one objective per line</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-primary fw-bold">Values</label>
                                        <textarea name="value" class="form-control" rows="3" placeholder="One value per line"><?= htmlspecialchars($details['value'] ?? '') ?></textarea>
                                        <small class="text-muted">Enter one value per line</small>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="update_about" class="btn btn-success mt-3">
                                <i class="fas fa-save me-2"></i>Update About Content
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Leadership Tab -->
                <div class="tab-pane fade" id="leadershipTab">
                    <div class="section-card" style="border-left-color: #17a2b8;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0"><i class="fas fa-users text-info me-2"></i>Leadership & Faculty</h4>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#leaderModal" onclick="clearLeaderForm()">
                                <i class="fas fa-plus me-2"></i>Add Faculty Member
                            </button>
                        </div>
                        
                        <?php 
                        // Get member count per section early for display
                        $leadershipCount = $pdo->query("SELECT section, COUNT(*) as cnt FROM leadership GROUP BY section")->fetchAll(PDO::FETCH_KEY_PAIR);
                        ?>
                        
                        <!-- Section Management -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h6><i class="fas fa-folder me-2"></i>Manage Sections</h6>
                            <form method="post" class="d-flex gap-2 align-items-end mb-3">
                                <div class="flex-grow-1">
                                    <input type="text" name="new_section" class="form-control form-control-sm" placeholder="New section name (e.g., Administration)">
                                </div>
                                <button type="submit" name="add_leadership_section" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-plus me-1"></i>Add Section
                                </button>
                            </form>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($leadershipSections as $sec): 
                                    $isDefault = in_array($sec, ['Individual', 'Primary', 'Junior', 'Senior', 'Non-Teaching']);
                                    $hasMembers = ($leadershipCount[$sec] ?? 0) > 0;
                                ?>
                                <span class="badge bg-info d-inline-flex align-items-center gap-1 py-2 px-3">
                                    <?= htmlspecialchars($sec) ?>
                                    <?php if (!$isDefault && !$hasMembers): ?>
                                    <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete section \'<?= htmlspecialchars($sec) ?>\'?')">
                                        <input type="hidden" name="delete_section" value="<?= htmlspecialchars($sec) ?>">
                                        <button type="submit" class="btn btn-link p-0 text-white" style="font-size: 10px;"><i class="fas fa-times"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted d-block mt-2">Default sections cannot be deleted. Custom sections can only be deleted when empty.</small>
                        </div>
                        
                        <!-- Summary Stats -->
                        <div class="row mb-4">
                            <?php foreach ($leadershipSections as $sec): ?>
                            <div class="col text-center">
                                <div class="p-2 bg-light rounded">
                                    <h4 class="text-primary mb-0"><?= $leadershipCount[$sec] ?? 0 ?></h4>
                                    <small class="text-muted"><?= htmlspecialchars($sec) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Leadership List -->
                        <div class="card-grid">
                            <?php foreach ($leadership as $leader): 
                                $leaderContent = json_decode($leader['modal_content'] ?? '{}', true);
                            ?>
                            <div class="item-card">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?= htmlspecialchars($leader['image_path']) ?>" class="preview-image-small me-3" onerror="this.src='https://via.placeholder.com/80'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= htmlspecialchars($leader['name']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($leader['role']) ?></small>
                                        <br><span class="badge bg-info"><?= htmlspecialchars($leader['section']) ?></span>
                                    </div>
                                </div>
                                <div class="small text-muted mb-2">
                                    <?php if (!empty($leaderContent['department'])): ?>
                                        <i class="fas fa-building me-1"></i><?= htmlspecialchars($leaderContent['department']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($leaderContent['years_worked'])): ?>
                                        <i class="fas fa-clock me-1"></i><?= htmlspecialchars($leaderContent['years_worked']) ?> years<br>
                                    <?php endif; ?>
                                    <?php if (!empty($leaderContent['contact_email'])): ?>
                                        <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($leaderContent['contact_email']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($leaderContent['qualification'])): ?>
                                        <i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($leaderContent['qualification']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-sm btn-outline-primary btn-icon" onclick="editLeader(<?= htmlspecialchars(json_encode(array_merge($leader, ['content' => $leaderContent]))) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this faculty member?')">
                                        <input type="hidden" name="delete_leadership" value="<?= $leader['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (empty($leadership)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-tie fa-3x mb-3 opacity-50"></i>
                            <p>No faculty members added yet. Click "Add Faculty Member" to get started.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Students Tab -->
                <div class="tab-pane fade" id="studentsTab">
                    <div class="section-card students">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0"><i class="fas fa-user-graduate text-warning me-2"></i>Student Representatives</h4>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="clearStudentForm()">
                                <i class="fas fa-plus me-2"></i>Add Student
                            </button>
                        </div>
                        
                        <div class="card-grid">
                            <?php foreach ($students as $student): ?>
                            <div class="item-card">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?= htmlspecialchars($student['image_path']) ?>" class="preview-image-small me-3" onerror="this.src='https://via.placeholder.com/80'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= htmlspecialchars($student['name']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($student['role']) ?></small>
                                        <br><span class="badge bg-secondary"><?= htmlspecialchars($student['category']) ?></span>
                                        <?php if ($student['is_featured']): ?><span class="badge bg-warning">Featured</span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-sm btn-outline-primary btn-icon" onclick="editStudent(<?= htmlspecialchars(json_encode($student)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this student?')">
                                        <input type="hidden" name="delete_student" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Achievements Tab -->
                <div class="tab-pane fade" id="achievementsTab">
                    <div class="section-card achievements">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0"><i class="fas fa-trophy text-danger me-2"></i>Achievements</h4>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#achievementModal" onclick="clearAchievementForm()">
                                <i class="fas fa-plus me-2"></i>Add Achievement
                            </button>
                        </div>
                        
                        <div class="card-grid">
                            <?php foreach ($achievements as $achievement): ?>
                            <div class="item-card">
                                <img src="<?= htmlspecialchars($achievement['image_path'] ?? 'https://via.placeholder.com/200x150') ?>" class="preview-image w-100 mb-2" onerror="this.src='https://via.placeholder.com/200x150'">
                                <h6 class="mb-1"><?= htmlspecialchars($achievement['title']) ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary"><?= htmlspecialchars($achievement['year'] ?? '') ?></span>
                                    <?php if ($achievement['is_active']): ?><span class="badge bg-success">Active</span><?php else: ?><span class="badge bg-secondary">Inactive</span><?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-sm btn-outline-primary btn-icon" onclick="editAchievement(<?= htmlspecialchars(json_encode($achievement)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this achievement?')">
                                        <input type="hidden" name="delete_achievement" value="<?= $achievement['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership Modal -->
    <div class="modal fade" id="leaderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="leaderModalTitle">Add Faculty Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="leader_id" id="leader_id">
                        <input type="hidden" name="existing_leader_image" id="existing_leader_image">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="leader_name" id="leader_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role/Designation <span class="text-danger">*</span></label>
                                    <input type="text" name="leader_role" id="leader_role" class="form-control" placeholder="e.g., Principal, Math Teacher" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Section <span class="text-danger">*</span></label>
                                    <select name="leader_section" id="leader_section" class="form-select" required>
                                        <?php foreach ($leadershipSections as $sec): ?>
                                            <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="leader_department" id="leader_department" class="form-control" placeholder="e.g., Science, Administration">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Years Worked</label>
                                    <input type="text" name="years_worked" id="years_worked" class="form-control" placeholder="e.g., 10">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control" placeholder="faculty@school.edu">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" id="qualification" class="form-control" placeholder="e.g., M.Ed, Ph.D, B.Sc">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <div class="mb-2">
                                <span class="toggle-source active" onclick="setLeaderSource('url')">URL</span>
                                <span class="toggle-source" onclick="setLeaderSource('upload')">Upload</span>
                                <input type="hidden" name="leader_image_source" id="leader_image_source" value="url">
                            </div>
                            <div id="leader_url_input">
                                <input type="text" name="leader_image_url" id="leader_image_url" class="form-control" placeholder="Image URL">
                            </div>
                            <div id="leader_upload_input" style="display:none;">
                                <input type="file" name="leader_image_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="leader_order" id="leader_order" class="form-control" value="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_leadership" class="btn btn-info">Save Faculty Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="studentModalTitle">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="student_id" id="student_id">
                        <input type="hidden" name="existing_student_image" id="existing_student_image">
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="student_name" id="student_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role/Position</label>
                            <input type="text" name="student_role" id="student_role" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="student_category" id="student_category" class="form-select">
                                <option value="council">Student Council</option>
                                <option value="leaders">Class Leaders</option>
                                <option value="clubs">Club Secretaries</option>
                                <option value="sports">Sports Captains</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <div class="mb-2">
                                <span class="toggle-source active" onclick="setStudentSource('url')">URL</span>
                                <span class="toggle-source" onclick="setStudentSource('upload')">Upload</span>
                                <input type="hidden" name="student_image_source" id="student_image_source" value="url">
                            </div>
                            <div id="student_url_input">
                                <input type="text" name="student_image_url" id="student_image_url" class="form-control" placeholder="Image URL">
                            </div>
                            <div id="student_upload_input" style="display:none;">
                                <input type="file" name="student_image_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="student_order" id="student_order" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3 pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="student_featured" id="student_featured" class="form-check-input">
                                        <label class="form-check-label">Featured</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_student" class="btn btn-warning">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Achievement Modal -->
    <div class="modal fade" id="achievementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="achievementModalTitle">Add Achievement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="achievement_id" id="achievement_id">
                        <input type="hidden" name="existing_achievement_image" id="existing_achievement_image">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="achievement_title" id="achievement_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="text" name="achievement_year" id="achievement_year" class="form-control" value="<?= date('Y') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <div class="mb-2">
                                <span class="toggle-source active" onclick="setAchievementSource('url')">URL</span>
                                <span class="toggle-source" onclick="setAchievementSource('upload')">Upload</span>
                                <input type="hidden" name="achievement_image_source" id="achievement_image_source" value="url">
                            </div>
                            <div id="achievement_url_input">
                                <input type="text" name="achievement_image_url" id="achievement_image_url" class="form-control" placeholder="Image URL">
                            </div>
                            <div id="achievement_upload_input" style="display:none;">
                                <input type="file" name="achievement_image_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="achievement_order" id="achievement_order" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3 pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="achievement_active" id="achievement_active" class="form-check-input" checked>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_achievement" class="btn btn-danger">Save Achievement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hero source toggle
        function setHeroSource(source) {
            document.getElementById('hero_bg_source').value = source;
            document.querySelectorAll('#heroTab .toggle-source').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('hero_url_input').style.display = source === 'url' ? 'block' : 'none';
            document.getElementById('hero_upload_input').style.display = source === 'upload' ? 'block' : 'none';
        }

        // Leadership functions
        function clearLeaderForm() {
            document.getElementById('leaderModalTitle').textContent = 'Add Faculty Member';
            document.getElementById('leader_id').value = '';
            document.getElementById('leader_name').value = '';
            document.getElementById('leader_role').value = '';
            document.getElementById('leader_section').value = 'Individual';
            document.getElementById('leader_department').value = '';
            document.getElementById('years_worked').value = '';
            document.getElementById('contact_email').value = '';
            document.getElementById('qualification').value = '';
            document.getElementById('leader_image_url').value = '';
            document.getElementById('existing_leader_image').value = '';
            document.getElementById('leader_order').value = '0';
        }

        function editLeader(leader) {
            document.getElementById('leaderModalTitle').textContent = 'Edit Faculty Member';
            document.getElementById('leader_id').value = leader.id;
            document.getElementById('leader_name').value = leader.name || '';
            document.getElementById('leader_role').value = leader.role || '';
            document.getElementById('leader_section').value = leader.section || 'Individual';
            document.getElementById('leader_department').value = leader.content?.department || '';
            document.getElementById('years_worked').value = leader.content?.years_worked || '';
            document.getElementById('contact_email').value = leader.content?.contact_email || '';
            document.getElementById('qualification').value = leader.content?.qualification || '';
            document.getElementById('leader_image_url').value = leader.image_path || '';
            document.getElementById('existing_leader_image').value = leader.image_path || '';
            document.getElementById('leader_order').value = leader.display_order || 0;
            new bootstrap.Modal(document.getElementById('leaderModal')).show();
        }

        function setLeaderSource(source) {
            document.getElementById('leader_image_source').value = source;
            document.querySelectorAll('#leaderModal .toggle-source').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('leader_url_input').style.display = source === 'url' ? 'block' : 'none';
            document.getElementById('leader_upload_input').style.display = source === 'upload' ? 'block' : 'none';
        }

        // Student functions
        function clearStudentForm() {
            document.getElementById('studentModalTitle').textContent = 'Add Student';
            document.getElementById('student_id').value = '';
            document.getElementById('student_name').value = '';
            document.getElementById('student_role').value = '';
            document.getElementById('student_category').value = 'leaders';
            document.getElementById('student_image_url').value = '';
            document.getElementById('existing_student_image').value = '';
            document.getElementById('student_order').value = '0';
            document.getElementById('student_featured').checked = false;
        }

        function editStudent(student) {
            document.getElementById('studentModalTitle').textContent = 'Edit Student';
            document.getElementById('student_id').value = student.id;
            document.getElementById('student_name').value = student.name;
            document.getElementById('student_role').value = student.role;
            document.getElementById('student_category').value = student.category;
            document.getElementById('student_image_url').value = student.image_path || '';
            document.getElementById('existing_student_image').value = student.image_path || '';
            document.getElementById('student_order').value = student.display_order || 0;
            document.getElementById('student_featured').checked = student.is_featured == 1;
            new bootstrap.Modal(document.getElementById('studentModal')).show();
        }

        function setStudentSource(source) {
            document.getElementById('student_image_source').value = source;
            document.querySelectorAll('#studentModal .toggle-source').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('student_url_input').style.display = source === 'url' ? 'block' : 'none';
            document.getElementById('student_upload_input').style.display = source === 'upload' ? 'block' : 'none';
        }

        // Achievement functions
        function clearAchievementForm() {
            document.getElementById('achievementModalTitle').textContent = 'Add Achievement';
            document.getElementById('achievement_id').value = '';
            document.getElementById('achievement_title').value = '';
            document.getElementById('achievement_year').value = '<?= date('Y') ?>';
            document.getElementById('achievement_image_url').value = '';
            document.getElementById('existing_achievement_image').value = '';
            document.getElementById('achievement_order').value = '0';
            document.getElementById('achievement_active').checked = true;
        }

        function editAchievement(achievement) {
            document.getElementById('achievementModalTitle').textContent = 'Edit Achievement';
            document.getElementById('achievement_id').value = achievement.id;
            document.getElementById('achievement_title').value = achievement.title;
            document.getElementById('achievement_year').value = achievement.year || '';
            document.getElementById('achievement_image_url').value = achievement.image_path || '';
            document.getElementById('existing_achievement_image').value = achievement.image_path || '';
            document.getElementById('achievement_order').value = achievement.display_order || 0;
            document.getElementById('achievement_active').checked = achievement.is_active == 1;
            new bootstrap.Modal(document.getElementById('achievementModal')).show();
        }

        function setAchievementSource(source) {
            document.getElementById('achievement_image_source').value = source;
            document.querySelectorAll('#achievementModal .toggle-source').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('achievement_url_input').style.display = source === 'url' ? 'block' : 'none';
            document.getElementById('achievement_upload_input').style.display = source === 'upload' ? 'block' : 'none';
        }
    </script>
</body>
</html>
