<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Get quick stats
$stats = [];
try {
    $stats['notices'] = $pdo->query("SELECT COUNT(*) FROM notices WHERE is_active = 1")->fetchColumn();
    $stats['gallery_images'] = $pdo->query("SELECT COUNT(*) FROM gallery_images WHERE is_active = 1")->fetchColumn();
    $stats['leadership'] = $pdo->query("SELECT COUNT(*) FROM leadership WHERE is_active = 1")->fetchColumn();
    $stats['achievements'] = $pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();
} catch (Exception $e) {
    $stats = ['notices' => 0, 'gallery_images' => 0, 'leadership' => 0, 'achievements' => 0];
}

$school_name = getSchoolConfig('school_name', 'School CMS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= htmlspecialchars($school_name) ?></title>
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <a href="../check/user/index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back to Website</a>
    </div>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="display-4 mb-2 fw-bold">
                <?= htmlspecialchars($school_name) ?> - Admin Dashboard
            </h1>
            <p class="lead mb-4">
                Content Management System
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
                        <h3><?= $stats['leadership'] ?></h3>
                        <p>Leadership Team</p>
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
                    <a href="school_branding.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-school text-primary"></i>
                            <h5>School Branding</h5>
                            <p class="text-muted">Manage school logo, name, and footer content</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="homepage.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-home text-success"></i>
                            <h5>Homepage Manager</h5>
                            <p class="text-muted">Edit hero section, about content, and main page</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="notices.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-bell text-warning"></i>
                            <h5>Notice Board</h5>
                            <p class="text-muted">Manage notices with PDF and image uploads</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="whoiswho.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-users text-info"></i>
                            <h5>Leadership Manager</h5>
                            <p class="text-muted">Manage who is who section with images</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="gallery.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-images text-purple"></i>
                            <h5>Gallery Manager</h5>
                            <p class="text-muted">Upload and organize gallery images</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="about.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-info-circle text-secondary"></i>
                            <h5>About Page Manager</h5>
                            <p class="text-muted">Edit about page content and values</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="achievement_manager.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-trophy text-warning"></i>
                            <h5>Achievement Manager</h5>
                            <p class="text-muted">Manage school achievements and awards</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="contact_manager.php" class="nav-card">
                        <div class="text-center">
                            <i class="fas fa-envelope text-danger"></i>
                            <h5>Contact Manager</h5>
                            <p class="text-muted">Update contact page information</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>