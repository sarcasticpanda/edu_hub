<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Get quick stats
$stats = [];
try {
    $stats['notices'] = $pdo->query("SELECT COUNT(*) FROM notices WHERE is_active = 1")->fetchColumn();
    $stats['gallery_images'] = $pdo->query("SELECT COUNT(*) FROM gallery_images WHERE is_active = 1")->fetchColumn();
    $stats['who_members'] = $pdo->query("SELECT COUNT(*) FROM who_is_who WHERE is_active = 1")->fetchColumn();
    $stats['achievements'] = $pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();
} catch (Exception $e) {
    $stats = ['notices' => 0, 'gallery_images' => 0, 'who_members' => 0, 'achievements' => 0];
}

// Fetch school info from homepage_content
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();

$school_name = getSchoolConfig('school_name', 'School Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - <?= htmlspecialchars($school_name) ?></title>
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
        .admin-nav {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        .nav-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 0.5rem;
            text-align: center;
        }
        .quick-actions {
            background: #f8f9fa;
            padding: 2rem;
            margin: 1rem;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <a href="../check/user/index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back to User Portal</a>
    </div>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="display-4 mb-2 fw-bold">
                <?= htmlspecialchars($school_info['title'] ?? 'Your School Name') ?>
            </h1>
            <p class="lead mb-4">
                <?= htmlspecialchars($school_info['content'] ?? 'Excellence in Education') ?>
            </p>
        </div>

        <div class="admin-nav">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dashboard</h5>
                <div>
                    <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                    <a href="logout.php" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-bell"></i>
                        <h3><?= $stats['notices'] ?></h3>
                        <p>Active Notices</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-images"></i>
                        <h3><?= $stats['gallery_images'] ?></h3>
                        <p>Gallery Images</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-users"></i>
                        <h3><?= $stats['who_members'] ?></h3>
                        <p>Team Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-trophy"></i>
                        <h3><?= $stats['achievements'] ?></h3>
                        <p>Achievements</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Cards -->
            <div class="row">
                <div class="col-md-4">
                    <a href="school_config.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-cog text-success"></i>
                            <h5>School Configuration</h5>
                            <p class="text-muted">Configure school name, logo, and basic settings</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="homepage.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-home text-primary"></i>
                            <h5>Homepage Content</h5>
                            <p class="text-muted">Edit hero section, about content, and main page elements</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="notices.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-bell text-warning"></i>
                            <h5>Notice Board</h5>
                            <p class="text-muted">Manage announcements and important notices</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="gallery.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-images text-success"></i>
                            <h5>Gallery Management</h5>
                            <p class="text-muted">Upload and organize gallery images</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="about.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-info-circle text-info"></i>
                            <h5>About Page</h5>
                            <p class="text-muted">Update about us content and information</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="whoiswho.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-users text-purple"></i>
                            <h5>Who is Who</h5>
                            <p class="text-muted">Manage staff and faculty information</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="footer.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-grip-horizontal text-secondary"></i>
                            <h5>Footer Content</h5>
                            <p class="text-muted">Edit footer links and information</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="achievements.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-trophy text-warning"></i>
                            <h5>Achievements</h5>
                            <p class="text-muted">Manage school achievements and awards</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="contact.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-envelope text-danger"></i>
                            <h5>Contact Information</h5>
                            <p class="text-muted">Update contact details and address</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="row">
                    <div class="col-md-3">
                        <a href="notices.php?action=add" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus me-2"></i>Add New Notice
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="gallery.php?action=upload" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-upload me-2"></i>Upload Gallery Images
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="whoiswho.php?action=add" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-user-plus me-2"></i>Add Team Member
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../check/user/index.php" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>