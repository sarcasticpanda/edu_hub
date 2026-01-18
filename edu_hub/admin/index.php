<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Get quick stats - with proper error handling for each query
$stats = [
    'notices' => 0,
    'gallery_images' => 0,
    'leadership' => 0,
    'achievements' => 0,
    'total_applications' => 0,
    'events' => 0,
    'students_showcase' => 0,
    'officials' => 0
];

// Check each table and count records (without is_active filter for reliability)
try {
    // Check if notices table exists
    $check = $pdo->query("SHOW TABLES LIKE 'notices'");
    if ($check->rowCount() > 0) {
        $stats['notices'] = (int)$pdo->query("SELECT COUNT(*) FROM notices")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if gallery_images table exists
    $check = $pdo->query("SHOW TABLES LIKE 'gallery_images'");
    if ($check->rowCount() > 0) {
        $stats['gallery_images'] = (int)$pdo->query("SELECT COUNT(*) FROM gallery_images")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if leadership table exists
    $check = $pdo->query("SHOW TABLES LIKE 'leadership'");
    if ($check->rowCount() > 0) {
        $stats['leadership'] = (int)$pdo->query("SELECT COUNT(*) FROM leadership")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if achievements table exists
    $check = $pdo->query("SHOW TABLES LIKE 'achievements'");
    if ($check->rowCount() > 0) {
        $stats['achievements'] = (int)$pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if student_applications table exists
    $check = $pdo->query("SHOW TABLES LIKE 'student_applications'");
    if ($check->rowCount() > 0) {
        $stats['total_applications'] = (int)$pdo->query("SELECT COUNT(*) FROM student_applications")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if events table exists
    $check = $pdo->query("SHOW TABLES LIKE 'events'");
    if ($check->rowCount() > 0) {
        $stats['events'] = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if student_showcase table exists
    $check = $pdo->query("SHOW TABLES LIKE 'student_showcase'");
    if ($check->rowCount() > 0) {
        $stats['students_showcase'] = (int)$pdo->query("SELECT COUNT(*) FROM student_showcase")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

try {
    // Check if government_officials table exists
    $check = $pdo->query("SHOW TABLES LIKE 'government_officials'");
    if ($check->rowCount() > 0) {
        $stats['officials'] = (int)$pdo->query("SELECT COUNT(*) FROM government_officials")->fetchColumn();
    }
} catch (Exception $e) { /* table doesn't exist */ }

$school_name = getSchoolConfig('school_name', 'School CMS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-orange: #f59e0b;
            --accent-purple: #8b5cf6;
            --accent-red: #ef4444;
            --accent-teal: #14b8a6;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        .admin-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .admin-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2.5rem 3rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .admin-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .header-left h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .header-left p {
            font-size: 1rem;
            opacity: 0.85;
            font-weight: 400;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .admin-info {
            text-align: right;
        }
        
        .admin-info .welcome {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        .admin-info .name {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-back, .btn-logout {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            color: white;
        }
        
        .btn-logout {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.4);
            color: white;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, #e2e8f0, transparent);
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        
        .nav-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-accent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .nav-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-hover-shadow);
            text-decoration: none;
            color: inherit;
        }
        
        .nav-card:hover::before {
            transform: scaleX(1);
        }
        
        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.75rem;
            transition: transform 0.3s ease;
        }
        
        .nav-card:hover .card-icon {
            transform: scale(1.1);
        }
        
        .nav-card h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        
        .nav-card p {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0;
        }
        
        /* Card color variants */
        .nav-card.blue { --card-accent: var(--accent-blue); }
        .nav-card.blue .card-icon { background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); }
        
        .nav-card.green { --card-accent: var(--accent-green); }
        .nav-card.green .card-icon { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        
        .nav-card.orange { --card-accent: var(--accent-orange); }
        .nav-card.orange .card-icon { background: rgba(245, 158, 11, 0.1); color: var(--accent-orange); }
        
        .nav-card.purple { --card-accent: var(--accent-purple); }
        .nav-card.purple .card-icon { background: rgba(139, 92, 246, 0.1); color: var(--accent-purple); }
        
        .nav-card.red { --card-accent: var(--accent-red); }
        .nav-card.red .card-icon { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); }
        
        .nav-card.teal { --card-accent: var(--accent-teal); }
        .nav-card.teal .card-icon { background: rgba(20, 184, 166, 0.1); color: var(--accent-teal); }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2.5rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .stat-info h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }
        
        .stat-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .admin-wrapper {
                padding: 1rem;
            }
            
            .admin-header {
                padding: 1.5rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }
            
            .header-right {
                flex-direction: column;
                gap: 1rem;
            }
            
            .admin-info {
                text-align: center;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-shield-alt me-2"></i>Admin Dashboard</h1>
                    <p>Content Management System</p>
                </div>
                <div class="header-right">
                    <div class="admin-info">
                        <div class="welcome">Welcome back,</div>
                        <div class="name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></div>
                    </div>
                    <a href="../public/index.php" class="btn-back">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="section-title">
            <i class="fas fa-chart-bar"></i> Quick Overview
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--accent-blue);">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h4><?= $stats['notices'] ?></h4>
                    <p>Notices</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--accent-purple);">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-info">
                    <h4><?= $stats['gallery_images'] ?></h4>
                    <p>Gallery</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-green);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h4><?= $stats['leadership'] ?></h4>
                    <p>Faculty</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--accent-orange);">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-info">
                    <h4><?= $stats['total_applications'] ?></h4>
                    <p>Applications</p>
                </div>
            </div>
        </div>

        <!-- Management Cards -->
        <div class="section-title">
            <i class="fas fa-cogs"></i> Management Modules
        </div>
        <div class="cards-grid">
            <a href="school_branding.php" class="nav-card blue">
                <div class="card-icon">
                    <i class="fas fa-school"></i>
                </div>
                <h5>School Branding</h5>
                <p>Manage school logo, name, colors and footer content</p>
            </a>

            <a href="homepage.php" class="nav-card green">
                <div class="card-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h5>Homepage Manager</h5>
                <p>Edit hero section, events, officials and featured content</p>
            </a>

            <a href="notices.php" class="nav-card orange">
                <div class="card-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h5>Notice Board</h5>
                <p>Manage notices with PDF and image attachments</p>
            </a>

            <a href="gallery.php" class="nav-card purple">
                <div class="card-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h5>Gallery Manager</h5>
                <p>Upload and organize gallery images by category</p>
            </a>

            <a href="about.php" class="nav-card teal">
                <div class="card-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h5>About Page Manager</h5>
                <p>Edit about page, leadership, students and achievements</p>
            </a>

            <a href="contact.php" class="nav-card red">
                <div class="card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h5>Contact Manager</h5>
                <p>Update contact information and location details</p>
            </a>

            <a href="student_applications.php" class="nav-card blue">
                <div class="card-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h5>Student Applications</h5>
                <p>Review and manage student admission applications</p>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>