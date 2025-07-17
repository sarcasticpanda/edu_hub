<?php
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
$about = $pdo ? $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch() : null;
$details = $pdo ? $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR) : [];
$leadership = $pdo ? $pdo->query("SELECT * FROM leadership ORDER BY created_at DESC")->fetchAll() : [];

// Group leadership by section
$sections = [
    'Individual' => [],
    'Primary' => [],
    'Junior' => [],
    'Senior' => [],
    'Non-Teaching' => []
];
foreach ($leadership as $l) {
    if (isset($sections[$l['section']])) {
        $sections[$l['section']][] = $l;
    } else {
        error_log("Invalid section found: " . $l['section']);
    }
}

// Handle About Us form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_about'])) {
    // Update about_admin_panel (title, content, image)
    $page_title = $_POST['page_title'] ?? '';
    $page_content = $_POST['page_content'] ?? '';
    $image_path = $about['image_path'] ?? '';
    if (!empty($_FILES['about_image']['name'])) {
        $uploadDir = '../check/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $fileName = 'about_main_' . time() . '.' . pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION);
        $localPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['about_image']['tmp_name'], $localPath);
        // Store the web-accessible path (not the local file path)
        $imagePath = '/seqto_edu_share/edu_hub/edu_hub/check/images/' . $fileName;
        $image_path = $imagePath; // Ensure the correct path is saved
    }
    // Insert or update about_admin_panel
    if ($pdo) {
        $stmt = $pdo->prepare("INSERT INTO about_admin_panel (page_title, page_content, image_path) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE page_title=VALUES(page_title), page_content=VALUES(page_content), image_path=VALUES(image_path)");
        $stmt->execute([$page_title, $page_content, $image_path]);
    }
    // Update motto, objectives, and values
    $sections = ['motto', 'objective', 'value'];
    foreach ($sections as $section) {
        $contentValue = $_POST[$section] ?? '';
        $existingSection = $pdo->prepare("SELECT id FROM about_details WHERE section_type = ? LIMIT 1");
        $existingSection->execute([$section]);
        $existingSection = $existingSection->fetch();
        if ($existingSection) {
            $stmt = $pdo->prepare("UPDATE about_details SET content = ? WHERE section_type = ?");
            $stmt->execute([$contentValue, $section]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO about_details (section_type, content) VALUES (?, ?)");
            $stmt->execute([$section, $contentValue]);
        }
    }
    // Refresh data after update
    $about = $pdo ? $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch() : null;
    $details = $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Telangana School/College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-teal: #1abc9c;
            --secondary-blue: #00539C;
            --accent-red: #D32F2F;
            --light-gray: #f7f7fa;
            --dark-gray: #232e47;
        }
        body { 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Open Sans', sans-serif;
        }
        .about-reveal-container {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto 60px auto;
            padding: 0 16px;
        }
        .about-reveal-block {
            opacity: 0;
            transform: translateY(60px);
            filter: blur(8px);
            transition: opacity 1.2s cubic-bezier(.22,1,.36,1), transform 1.2s cubic-bezier(.22,1,.36,1), filter 1.2s cubic-bezier(.22,1,.36,1);
            margin-bottom: 70px;
            width: 100%;
        }
        .about-reveal-block.visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
        .about-reveal-block.left-align {
            text-align: left;
            margin-left: 0;
            margin-right: auto;
            max-width: 600px;
        }
        .about-reveal-block.right-align {
            text-align: right;
            margin-left: auto;
            margin-right: 0;
            max-width: 600px;
        }
        .about-reveal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5em;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 1px;
            line-height: 1.1;
            color: var(--secondary-blue);
        }
        .about-reveal-title.motto { color: var(--primary-teal); }
        .about-reveal-title.objectives { color: var(--accent-red); }
        .about-reveal-title.values { color: var(--primary-teal); }
        .about-reveal-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25em;
            color: var(--dark-gray);
            font-weight: 500;
            line-height: 1.6;
            margin: 0;
            white-space: pre-line;
        }
        .leadership-heading-dominant {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            letter-spacing: 2px;
            font-size: 3.2rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--primary-teal) 40%, var(--accent-red) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .leadership-management {
            max-width: 1300px;
            margin: 0 auto;
            padding: 24px;
            overflow-x: auto;
            white-space: nowrap;
            background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(30,42,68,0.13);
        }
        .leadership-card {
            display: inline-block;
            width: 260px;
            height: 240px;
            margin-right: 24px;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 12px 0 rgba(30,42,68,0.10);
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid rgba(26, 188, 156, 0.2);
            vertical-align: top;
            position: relative;
        }
        .leadership-card:last-child {
            margin-right: 0;
        }
        .leadership-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .leadership-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 32px rgba(30,42,68,0.22);
        }
        .leadership-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 0.7rem 1.2rem;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,83,156,0.75) 100%);
            color: #fff;
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
        }
        .leadership-overlay .name {
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 0.1rem;
        }
        .leadership-overlay .role {
            font-weight: 700;
            font-size: 1rem;
        }
        .gallery-modal .modal-dialog {
            max-width: 900px;
        }
        .gallery-modal .modal-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1060;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            justify-content: center;
            position: relative;
            z-index: 1061;
        }
        .gallery-card {
            background: #fff; 
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(30,42,68,0.10);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 260px;
            border: 2px solid rgba(26, 188, 156, 0.15);
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }
        .gallery-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-card:hover img {
            transform: scale(1.05);
        }
        .gallery-card-content {
            padding: 1rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, #ffffff 70%, var(--light-gray) 100%);
        }
        .gallery-card-content h5 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }
        .gallery-card-content p {
            font-size: 0.95rem;
            color: #555;
            margin: 0;
        }
        .leadership-modal .modal-content {
            background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%);
            border: 2px solid rgba(26, 188, 156, 0.1);
            text-align: center;
        }
        .leadership-modal .modal-header {
            background: var(--secondary-blue);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .leadership-modal .modal-body {
            background: #fff;
            padding: 2rem;
        }
        .leadership-modal .modal-details {
            max-width: 500px;
            margin: 0 auto;
        }
        .leadership-modal h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--dark-gray);
            text-transform: uppercase;
        }
        .leadership-modal p {
            font-size: 1rem;
            color: #444;
        }
        .leadership-modal img {
            border: 2px solid rgba(26, 188, 156, 0.2);
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            .leadership-management {
                padding: 16px;
            }
            .leadership-card {
                width: 200px;
                height: 200px;
                margin-right: 16px;
            }
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            .gallery-card {
                height: 220px;
            }
            .gallery-card img {
                height: 140px;
            }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .admin-container { max-width: 1000px; margin: 20px auto; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .admin-header { background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%); color: white; padding: 2rem; text-align: center; }
        .content-section { background: #f8f9fa; margin: 1rem; padding: 2rem; border-radius: 10px; border-left: 4px solid #17a2b8; }
        .preview-image { max-width: 300px; max-height: 200px; object-fit: cover; border-radius: 8px; margin: 10px 0; }
        .about-separator-row {
            display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;
        }
        .about-card {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(30,42,68,0.07);
            border-left: 5px solid #1abc9c;
            padding: 1.2rem 1.2rem 1rem 1.2rem;
            margin-bottom: 1.2rem;
        }
        .about-card.motto { border-left-color: #1abc9c; }
        .about-card.objective { border-left-color: #D32F2F; }
        .about-card.value { border-left-color: #00539C; }
        .about-card h5 { font-weight: 700; margin-bottom: 0.7rem; letter-spacing: 1px; }
        @media (max-width: 900px) {
            .about-separator-row { flex-direction: column; gap: 0.5rem; }
        }
    </style>
</head>
<body class="font-open-sans text-gray-800 bg-gray-50" style="background: linear-gradient(135deg, #f0f4f8 0%, #ffffff 100%);">
    
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-info-circle me-3"></i>About Page Management</h1>
            <p class="mb-0">Update about us content and information</p>
        </div>
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Edit About Page Content</h5>
                <div>
                    <a href="../check/user/about.php" class="btn btn-outline-primary me-2" target="_blank">
                        <i class="fas fa-eye me-2"></i>Preview About Us
                    </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
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
            <div class="content-section">
                <h4><i class="fas fa-edit text-info me-2"></i>About Page Content</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Page Title</label>
                                <input type="text" name="page_title" class="form-control" value="<?= htmlspecialchars($about['page_title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">About Content</label>
                                <textarea name="page_content" class="form-control" rows="10" required><?= htmlspecialchars($about['page_content'] ?? '') ?></textarea>
                            </div>
                            <div class="about-separator-row">
                                <div class="about-card motto">
                                    <h5>Motto</h5>
                                    <textarea name="motto" class="form-control" rows="3" style="resize:vertical;"><?= htmlspecialchars($details['motto'] ?? '') ?></textarea>
                                </div>
                                <div class="about-card objective">
                                    <h5>Objectives</h5>
                                    <textarea name="objective" class="form-control" rows="5" style="resize:vertical;"><?= htmlspecialchars($details['objective'] ?? '') ?></textarea>
                                </div>
                                <div class="about-card value">
                                    <h5>Values</h5>
                                    <textarea name="value" class="form-control" rows="5" style="resize:vertical;"><?= htmlspecialchars($details['value'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update_about" class="btn btn-info mt-3">
                                <i class="fas fa-save me-2"></i>Update About Content
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">About Page Image</label>
                            <?php if (!empty($about['image_path'])): ?>
                                <img src="<?= htmlspecialchars($about['image_path']) ?>" class="preview-image d-block" alt="Current About Image">
                            <?php endif; ?>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload new about page image</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>