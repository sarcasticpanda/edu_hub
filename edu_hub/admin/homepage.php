<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';
$active_tab = $_GET['tab'] ?? 'events';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            // ============ EVENTS ============
            case 'add_event':
                $stmt = $pdo->prepare("INSERT INTO events (title, subtitle, description, event_date, image_path, is_pinned, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['subtitle'],
                    $_POST['description'],
                    $_POST['event_date'],
                    $_POST['image_path'],
                    isset($_POST['is_pinned']) && $_POST['is_pinned'] ? 1 : 0,
                    $_POST['display_order'] ?? 0
                ]);
                echo json_encode(['success' => true, 'message' => 'Event added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_event':
                $stmt = $pdo->prepare("UPDATE events SET title=?, subtitle=?, description=?, event_date=?, image_path=?, is_pinned=?, display_order=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['subtitle'],
                    $_POST['description'],
                    $_POST['event_date'],
                    $_POST['image_path'],
                    isset($_POST['is_pinned']) && $_POST['is_pinned'] ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Event updated successfully!']);
                break;
                
            case 'delete_event':
                $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Event deleted successfully!']);
                break;
                
            case 'toggle_pin_event':
                $stmt = $pdo->prepare("UPDATE events SET is_pinned = NOT is_pinned WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Pin status toggled!']);
                break;
                
            // ============ OFFICIALS ============
            case 'add_official':
                $stmt = $pdo->prepare("INSERT INTO government_officials (name, position, designation, bio, image_path, profile_link, contact_link, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['position'],
                    $_POST['designation'],
                    $_POST['bio'] ?? '',
                    $_POST['image_path'],
                    $_POST['profile_link'] ?? '#',
                    $_POST['contact_link'] ?? '#',
                    $_POST['display_order'] ?? 0
                ]);
                echo json_encode(['success' => true, 'message' => 'Official added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_official':
                $stmt = $pdo->prepare("UPDATE government_officials SET name=?, position=?, designation=?, bio=?, image_path=?, profile_link=?, contact_link=?, display_order=? WHERE id=?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['position'],
                    $_POST['designation'],
                    $_POST['bio'] ?? '',
                    $_POST['image_path'],
                    $_POST['profile_link'] ?? '#',
                    $_POST['contact_link'] ?? '#',
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Official updated successfully!']);
                break;
                
            case 'delete_official':
                $stmt = $pdo->prepare("DELETE FROM government_officials WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Official deleted successfully!']);
                break;
                
            // ============ STUDENTS ============
            case 'add_student':
                $stmt = $pdo->prepare("INSERT INTO student_showcase (name, role, image_path, display_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['role'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0
                ]);
                echo json_encode(['success' => true, 'message' => 'Student added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_student':
                $stmt = $pdo->prepare("UPDATE student_showcase SET name=?, role=?, image_path=?, display_order=? WHERE id=?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['role'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Student updated successfully!']);
                break;
                
            case 'delete_student':
                $stmt = $pdo->prepare("DELETE FROM student_showcase WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Student deleted successfully!']);
                break;
                
            // ============ FACULTY ============
            case 'add_faculty':
                $stmt = $pdo->prepare("INSERT INTO faculty (name, position, department, image_path, display_order, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['position'],
                    $_POST['department'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0,
                    isset($_POST['is_featured']) && $_POST['is_featured'] ? 1 : 0
                ]);
                echo json_encode(['success' => true, 'message' => 'Faculty added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_faculty':
                $stmt = $pdo->prepare("UPDATE faculty SET name=?, position=?, department=?, image_path=?, display_order=?, is_featured=? WHERE id=?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['position'],
                    $_POST['department'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0,
                    isset($_POST['is_featured']) && $_POST['is_featured'] ? 1 : 0,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Faculty updated successfully!']);
                break;
                
            case 'delete_faculty':
                $stmt = $pdo->prepare("DELETE FROM faculty WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Faculty deleted successfully!']);
                break;
                
            // ============ INFRASTRUCTURE ============
            case 'add_infrastructure':
                $stmt = $pdo->prepare("INSERT INTO infrastructure (title, description, image_path, display_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0
                ]);
                echo json_encode(['success' => true, 'message' => 'Infrastructure added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_infrastructure':
                $stmt = $pdo->prepare("UPDATE infrastructure SET title=?, description=?, image_path=?, display_order=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['image_path'],
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Infrastructure updated successfully!']);
                break;
                
            case 'delete_infrastructure':
                $stmt = $pdo->prepare("DELETE FROM infrastructure WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Infrastructure deleted successfully!']);
                break;
                
            // ============ NEWS TICKER ============
            case 'add_news':
                $stmt = $pdo->prepare("INSERT INTO news_ticker (content, display_order) VALUES (?, ?)");
                $stmt->execute([$_POST['content'], $_POST['display_order'] ?? 0]);
                echo json_encode(['success' => true, 'message' => 'News item added successfully!', 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'update_news':
                $stmt = $pdo->prepare("UPDATE news_ticker SET content=?, display_order=? WHERE id=?");
                $stmt->execute([$_POST['content'], $_POST['display_order'] ?? 0, $_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'News item updated successfully!']);
                break;
                
            case 'delete_news':
                $stmt = $pdo->prepare("DELETE FROM news_ticker WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'News item deleted successfully!']);
                break;
                
            // ============ SECTION DESCRIPTIONS ============
            case 'update_section_description':
                $stmt = $pdo->prepare("INSERT INTO homepage_content (section, title, content, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW()");
                $stmt->execute([$_POST['section'], $_POST['title'], $_POST['content']]);
                echo json_encode(['success' => true, 'message' => 'Section description updated successfully!']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch all data for display
try {
    $events = $pdo->query("SELECT * FROM events ORDER BY is_pinned DESC, display_order ASC")->fetchAll();
} catch (Exception $e) { $events = []; }

try {
    $officials = $pdo->query("SELECT * FROM government_officials ORDER BY display_order ASC")->fetchAll();
} catch (Exception $e) { $officials = []; }

try {
    $students = $pdo->query("SELECT * FROM student_showcase ORDER BY display_order ASC")->fetchAll();
} catch (Exception $e) { $students = []; }

try {
    $faculty = $pdo->query("SELECT * FROM faculty ORDER BY display_order ASC")->fetchAll();
} catch (Exception $e) { $faculty = []; }

try {
    $infrastructure = $pdo->query("SELECT * FROM infrastructure ORDER BY display_order ASC")->fetchAll();
} catch (Exception $e) { $infrastructure = []; }

try {
    $news_items = $pdo->query("SELECT * FROM news_ticker ORDER BY display_order ASC")->fetchAll();
} catch (Exception $e) { $news_items = []; }

// Fetch section descriptions
try {
    $students_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'students_section'")->fetch();
    $faculty_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'faculty_section'")->fetch();
    $infrastructure_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'infrastructure_section'")->fetch();
} catch (Exception $e) {
    $students_section = $faculty_section = $infrastructure_section = null;
}

// Count pinned events
$pinned_count = count(array_filter($events, fn($e) => $e['is_pinned']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Manager - Admin Portal</title>
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            padding: 1rem 1.5rem;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: var(--accent-blue);
            border-bottom: 3px solid var(--accent-blue);
            background: transparent;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 10px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        .table-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .badge-pinned {
            background: var(--accent-orange);
            color: white;
        }
        .item-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .item-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .item-card .content {
            flex: 1;
        }
        .item-card .actions {
            display: flex;
            gap: 0.5rem;
        }
        .modal-header {
            background: var(--primary-gradient);
            color: white;
        }
        .featured-badge {
            background: var(--accent-orange);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-home"></i>
                <div class="admin-header-info">
                    <h1>Homepage Content Manager</h1>
                    <p>Manage all homepage sections dynamically</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/index.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Site</a>
            </div>
        </div>

    <div class="container-fluid px-4 py-4">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="contentTabs">
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'events' ? 'active' : '' ?>" href="?tab=events">
                    <i class="fas fa-calendar-alt me-2"></i>Events (<?= count($events) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'officials' ? 'active' : '' ?>" href="?tab=officials">
                    <i class="fas fa-user-tie me-2"></i>Officials (<?= count($officials) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'students' ? 'active' : '' ?>" href="?tab=students">
                    <i class="fas fa-user-graduate me-2"></i>Students (<?= count($students) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'faculty' ? 'active' : '' ?>" href="?tab=faculty">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Faculty (<?= count($faculty) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'infrastructure' ? 'active' : '' ?>" href="?tab=infrastructure">
                    <i class="fas fa-building me-2"></i>Infrastructure (<?= count($infrastructure) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'news' ? 'active' : '' ?>" href="?tab=news">
                    <i class="fas fa-newspaper me-2"></i>News Ticker (<?= count($news_items) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'sections' ? 'active' : '' ?>" href="?tab=sections">
                    <i class="fas fa-edit me-2"></i>Section Texts
                </a>
            </li>
        </ul>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- ============ EVENTS TAB ============ -->
        <?php if ($active_tab === 'events'): ?>
        <?php 
        $pinned_events = array_filter($events, fn($e) => $e['is_pinned']);
        $unpinned_events = array_filter($events, fn($e) => !$e['is_pinned']);
        ?>
        
        <!-- Pinned Events Section (Featured/Hero) -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-star text-warning me-2"></i>Featured Events (Hero Section)</span>
                <div>
                    <span class="badge bg-warning me-2"><?= count($pinned_events) ?>/3 Pinned</span>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="clearEventForm(true)">
                        <i class="fas fa-plus me-1"></i>Add Featured Event
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Featured/Pinned events</strong> appear in the Hero carousel at the top of the homepage (max 3 recommended). These are highlighted prominently.
                </div>
                
                <?php if (empty($pinned_events)): ?>
                    <p class="text-muted text-center py-3">No featured events yet. Pin events to display them in the hero section.</p>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($pinned_events as $event): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <img src="<?= htmlspecialchars($event['image_path']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="">
                            <div class="card-body">
                                <span class="badge badge-pinned mb-2"><i class="fas fa-star me-1"></i>Featured</span>
                                <h6 class="card-title"><?= htmlspecialchars($event['title']) ?></h6>
                                <p class="card-text text-muted small"><?= htmlspecialchars($event['subtitle'] ?? '') ?></p>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-calendar me-1"></i><?= $event['event_date'] ? date('M d, Y', strtotime($event['event_date'])) : '-' ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="togglePinEvent(<?= $event['id'] ?>)" title="Unpin">
                                        <i class="fas fa-thumbtack"></i> Unpin
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editEvent(<?= json_encode($event) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('event', <?= $event['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Unpinned Events Section (Scrollable Cards) -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-alt text-primary me-2"></i>Event Cards (Scrollable Section)</span>
                <div>
                    <span class="badge bg-primary me-2"><?= count($unpinned_events) ?> Cards</span>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="clearEventForm(false)">
                        <i class="fas fa-plus me-1"></i>Add Event Card
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Event cards</strong> appear in the horizontally scrollable events section on the homepage. You can add unlimited events - all will be displayed in a scrollable carousel.
                </div>
                
                <?php if (empty($unpinned_events)): ?>
                    <p class="text-muted text-center py-3">No event cards yet. Add events to display them in the scrollable section.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unpinned_events as $event): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($event['image_path']) ?>" class="table-img" alt=""></td>
                                <td>
                                    <strong><?= htmlspecialchars($event['title']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($event['subtitle'] ?? '') ?></small>
                                </td>
                                <td><?= $event['event_date'] ? date('M d, Y', strtotime($event['event_date'])) : '-' ?></td>
                                <td><?= $event['display_order'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="togglePinEvent(<?= $event['id'] ?>)" title="Pin to Featured">
                                        <i class="fas fa-thumbtack"></i> Pin
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editEvent(<?= json_encode($event) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('event', <?= $event['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ OFFICIALS TAB ============ -->
        <?php if ($active_tab === 'officials'): ?>
        <?php 
        // Get only first 2 officials for display
        $display_officials = array_slice($officials, 0, 2);
        ?>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-gradient" style="background: linear-gradient(135deg, var(--primary) 0%, #1a472a 100%); color: white;">
                <div>
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Featured Government Officials</h5>
                    <small style="opacity: 0.9;">Two officials displayed prominently on the homepage</small>
                </div>
                <div>
                    <span class="badge bg-white text-primary me-2" style="font-weight: 600;"><?= min(count($officials), 2) ?>/2 Featured</span>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#officialModal" onclick="clearOfficialForm()">
                        <i class="fas fa-plus me-1"></i>Add Official
                    </button>
                </div>
            </div>
            <div class="card-body p-4" style="background: #f8fafc;">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Display Priority:</strong> Only the first 2 officials (by display order) are featured on the homepage. 
                    Assign display order 1 and 2 to your featured officials.
                </div>
                
                <?php if (count($display_officials) < 2): ?>
                    <div class="alert alert-warning border-0 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Action Required:</strong> Add at least 2 officials for optimal homepage display. Currently showing <?= count($display_officials) ?> official(s).
                    </div>
                <?php endif; ?>
                
                <!-- Two Officials Display -->
                <div class="row g-4 mb-4">
                    <?php 
                    // Professional color schemes
                    $themes = [
                        [
                            'gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                            'light_bg' => '#eff6ff',
                            'heading' => '#1e3a8a',
                            'text' => '#374151',
                            'badge' => '#3b82f6',
                            'badge_text' => '#ffffff',
                            'border' => '#3b82f6'
                        ],
                        [
                            'gradient' => 'linear-gradient(135deg, #991b1b 0%, #dc2626 100%)',
                            'light_bg' => '#fef2f2',
                            'heading' => '#991b1b',
                            'text' => '#374151',
                            'badge' => '#dc2626',
                            'badge_text' => '#ffffff',
                            'border' => '#dc2626'
                        ]
                    ];
                    
                    foreach ($display_officials as $index => $official): 
                        $theme = $themes[$index];
                    ?>
                    <div class="col-md-6">
                        <div style="
                            background: white;
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            border: 2px solid <?= $theme['border'] ?>20;
                            height: 550px;
                            display: flex;
                        " 
                        onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.12)';" 
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)';">
                            
                            <!-- Image Column (Full Height Coverage) -->
                            <div style="
                                width: 50%;
                                position: relative;
                                overflow: hidden;
                                background: <?= $theme['gradient'] ?>;
                            ">
                                <!-- Official Number Badge -->
                                <div style="
                                    position: absolute;
                                    top: 15px;
                                    left: 15px;
                                    background: rgba(255,255,255,0.95);
                                    color: <?= $theme['heading'] ?>;
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-weight: 700;
                                    font-size: 1rem;
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                    z-index: 2;
                                ">
                                    <?= $index + 1 ?>
                                </div>
                                
                                <!-- Image with Full Height Coverage -->
                                <img src="<?= htmlspecialchars($official['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($official['name']) ?>"
                                     style="
                                        width: 100%;
                                        height: 100%;
                                        object-fit: cover;
                                        object-position: center top;
                                        display: block;
                                     ">
                                     
                                <!-- Gradient Overlay for Depth -->
                                <div style="
                                    position: absolute;
                                    inset: 0;
                                    background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
                                "></div>
                            </div>
                            
                            <!-- Content Column -->
                            <div style="
                                width: 50%;
                                padding: 28px 24px;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-between;
                                background: <?= $theme['light_bg'] ?>;
                            ">
                                <!-- Top Section -->
                                <div>
                                    <!-- Designation Badge -->
                                    <div style="
                                        display: inline-block;
                                        background: <?= $theme['badge'] ?>;
                                        color: <?= $theme['badge_text'] ?>;
                                        padding: 7px 16px;
                                        border-radius: 8px;
                                        font-size: 10px;
                                        font-weight: 700;
                                        text-transform: uppercase;
                                        letter-spacing: 0.8px;
                                        margin-bottom: 16px;
                                        box-shadow: 0 2px 8px <?= $theme['badge'] ?>40;
                                    ">
                                        <i class="fas fa-award me-1"></i>
                                        <?= htmlspecialchars($official['designation'] ?? $official['position'] ?? 'Official') ?>
                                    </div>
                                    
                                    <!-- Name -->
                                    <h3 style="
                                        color: <?= $theme['heading'] ?>;
                                        font-weight: 700;
                                        font-size: 1.65rem;
                                        margin-bottom: 10px;
                                        line-height: 1.25;
                                        letter-spacing: -0.5px;
                                    ">
                                        <?= htmlspecialchars($official['name']) ?>
                                    </h3>
                                    
                                    <!-- Position (if different) -->
                                    <?php if (!empty($official['position']) && $official['position'] != $official['designation']): ?>
                                    <p style="
                                        color: <?= $theme['text'] ?>;
                                        font-size: 0.95rem;
                                        margin-bottom: 16px;
                                        font-weight: 500;
                                        opacity: 0.85;
                                    ">
                                        <?= htmlspecialchars($official['position']) ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <!-- Bio/Description -->
                                    <?php if (!empty($official['bio'])): ?>
                                    <div style="
                                        color: <?= $theme['text'] ?>;
                                        font-size: 0.9rem;
                                        line-height: 1.6;
                                        margin-bottom: 16px;
                                        opacity: 0.8;
                                        max-height: 90px;
                                        overflow-y: auto;
                                        padding: 12px;
                                        background: white;
                                        border-radius: 8px;
                                        border-left: 3px solid <?= $theme['border'] ?>;
                                    ">
                                        <?= nl2br(htmlspecialchars($official['bio'])) ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Display Order Info Box -->
                                    <div style="
                                        background: white;
                                        padding: 10px 14px;
                                        border-radius: 8px;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 8px;
                                        margin-top: 12px;
                                        border-left: 4px solid <?= $theme['border'] ?>;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                                    ">
                                        <i class="fas fa-sort-numeric-down" style="color: <?= $theme['badge'] ?>; font-size: 0.9rem;"></i>
                                        <span style="color: <?= $theme['text'] ?>; font-size: 0.85rem; font-weight: 500;">
                                            Display Order: 
                                        </span>
                                        <strong style="
                                            color: <?= $theme['heading'] ?>; 
                                            font-size: 1.1rem;
                                            font-weight: 700;
                                        "><?= $official['display_order'] ?></strong>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div style="margin-top: 20px; display: flex; gap: 10px;">
                                    <button class="btn btn-sm flex-fill" 
                                            onclick='editOfficial(<?= json_encode($official) ?>)'
                                            style="
                                                background: <?= $theme['badge'] ?>;
                                                color: white;
                                                border: none;
                                                padding: 10px 18px;
                                                border-radius: 8px;
                                                font-weight: 600;
                                                font-size: 0.875rem;
                                                transition: all 0.2s ease;
                                                box-shadow: 0 2px 8px <?= $theme['badge'] ?>30;
                                            "
                                            onmouseover="this.style.background='<?= $theme['heading'] ?>'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px <?= $theme['badge'] ?>40';"
                                            onmouseout="this.style.background='<?= $theme['badge'] ?>'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px <?= $theme['badge'] ?>30';">
                                        <i class="fas fa-edit me-1"></i>Edit Details
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger flex-fill" 
                                            onclick="if(confirm('Are you sure you want to delete this official?')) deleteItem('official', <?= $official['id'] ?>)"
                                            style="
                                                padding: 10px 18px;
                                                border-radius: 8px;
                                                font-weight: 600;
                                                font-size: 0.875rem;
                                                transition: all 0.2s ease;
                                            ">
                                        <i class="fas fa-trash-alt me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Empty State -->
                    <?php if (count($display_officials) == 0): ?>
                    <div class="col-12">
                        <div class="text-center py-5" style="background: white; border-radius: 16px; border: 2px dashed #d1d5db;">
                            <i class="fas fa-user-tie" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1.5rem;"></i>
                            <h4 style="color: #6b7280; font-weight: 600;">No Officials Added</h4>
                            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Start by adding your first government official</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#officialModal" onclick="clearOfficialForm()">
                                <i class="fas fa-plus me-2"></i>Add First Official
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- All Officials List Table -->
                <?php if (count($officials) > 0): ?>
                <div class="mt-5 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="color: #374151; font-weight: 600;">
                            <i class="fas fa-list me-2"></i>All Officials Management
                            <span class="badge bg-secondary ms-2"><?= count($officials) ?> Total</span>
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            First 2 by display order are featured
                        </small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="background: white; border-radius: 12px; overflow: hidden;">
                            <thead style="background: #f8fafc;">
                                <tr>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">ORDER</th>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">IMAGE</th>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">NAME</th>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">DESIGNATION</th>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">STATUS</th>
                                    <th style="padding: 14px; font-weight: 600; color: #374151; font-size: 0.875rem;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($officials as $i => $official): ?>
                                <tr style="<?= $i < 2 ? 'background: #f0fdf4;' : '' ?>">
                                    <td style="padding: 12px;">
                                        <strong style="color: #059669; font-size: 1.1rem;"><?= $official['display_order'] ?></strong>
                                    </td>
                                    <td style="padding: 12px;">
                                        <img src="<?= htmlspecialchars($official['image_path']) ?>" 
                                             style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb;" 
                                             alt="">
                                    </td>
                                    <td style="padding: 12px;">
                                        <strong style="color: #1f2937;"><?= htmlspecialchars($official['name']) ?></strong>
                                    </td>
                                    <td style="padding: 12px; color: #6b7280;">
                                        <?= htmlspecialchars($official['designation'] ?? $official['position']) ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($i < 2): ?>
                                            <span class="badge" style="background: #059669; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                <i class="fas fa-eye me-1"></i>Featured
                                            </span>
                                        <?php else: ?>
                                            <span class="badge" style="background: #6b7280; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                <i class="fas fa-eye-slash me-1"></i>Hidden
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick='editOfficial(<?= json_encode($official) ?>)'
                                                    style="border-radius: 6px 0 0 6px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="if(confirm('Delete this official?')) deleteItem('official', <?= $official['id'] ?>)"
                                                    style="border-radius: 0 6px 6px 0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ STUDENTS TAB ============ -->
        <?php if ($active_tab === 'students'): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-graduate text-primary me-2"></i>Student Showcase (Max 4 displayed)</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="clearStudentForm()">
                    <i class="fas fa-plus me-1"></i>Add Student
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($students as $student): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="item-card flex-column text-center">
                            <img src="<?= htmlspecialchars($student['image_path']) ?>" alt="" style="width:100px;height:100px;">
                            <div class="content text-center mt-2">
                                <h6 class="mb-1"><?= htmlspecialchars($student['name']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($student['role']) ?></small>
                            </div>
                            <div class="actions mt-2">
                                <button class="btn btn-sm btn-outline-primary" onclick='editStudent(<?= json_encode($student) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('student', <?= $student['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ FACULTY TAB ============ -->
        <?php if ($active_tab === 'faculty'): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Faculty Members (Max 4 featured displayed)</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#facultyModal" onclick="clearFacultyForm()">
                    <i class="fas fa-plus me-1"></i>Add Faculty
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($faculty as $f): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="item-card flex-column text-center">
                            <img src="<?= htmlspecialchars($f['image_path']) ?>" alt="" style="width:100px;height:100px;">
                            <div class="content text-center mt-2">
                                <h6 class="mb-1"><?= htmlspecialchars($f['name']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($f['position']) ?></small>
                                <br><small class="text-primary"><?= htmlspecialchars($f['department']) ?></small>
                                <?php if ($f['is_featured']): ?>
                                    <br><span class="featured-badge">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="actions mt-2">
                                <button class="btn btn-sm btn-outline-primary" onclick='editFaculty(<?= json_encode($f) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('faculty', <?= $f['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ INFRASTRUCTURE TAB ============ -->
        <?php if ($active_tab === 'infrastructure'): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-building text-primary me-2"></i>Infrastructure & Facilities</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#infrastructureModal" onclick="clearInfrastructureForm()">
                    <i class="fas fa-plus me-1"></i>Add Infrastructure
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($infrastructure as $infra): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="item-card flex-column">
                            <img src="<?= htmlspecialchars($infra['image_path']) ?>" alt="" style="width:100%;height:120px;">
                            <div class="content mt-2">
                                <h6 class="mb-1"><?= htmlspecialchars($infra['title']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars(substr($infra['description'] ?? '', 0, 50)) ?>...</small>
                            </div>
                            <div class="actions mt-2">
                                <button class="btn btn-sm btn-outline-primary" onclick='editInfrastructure(<?= json_encode($infra) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('infrastructure', <?= $infra['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ NEWS TICKER TAB ============ -->
        <?php if ($active_tab === 'news'): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-newspaper text-primary me-2"></i>News Ticker Items</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newsModal" onclick="clearNewsForm()">
                    <i class="fas fa-plus me-1"></i>Add News Item
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>News Ticker</strong> is the scrolling text banner at the top of the homepage that displays announcements and important messages to visitors.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Content</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news_items as $news): ?>
                            <tr>
                                <td><?= htmlspecialchars($news['content']) ?></td>
                                <td><?= $news['display_order'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editNews(<?= json_encode($news) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('news', <?= $news['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- ============ SECTION TEXTS TAB ============ -->
        <?php if ($active_tab === 'sections'): ?>
        <div class="row g-4">
            <!-- Students Section -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, var(--primary) 0%, #1a472a 100%); color: white;">
                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Students Section Description</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="studentsForm" onsubmit="updateSectionDescription(event, 'students_section')">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($students_section['title'] ?? 'Our Students: Future Leaders') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Description</label>
                                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($students_section['content'] ?? 'At Government High School, we nurture young minds to become confident, capable, and compassionate leaders. Our students excel in academics, sports, and extracurricular activities, making us proud every day.') ?></textarea>
                                <small class="text-muted">This text appears in the Students section on the homepage</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Students Section
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Faculty Section -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Faculty Section Description</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="facultyForm" onsubmit="updateSectionDescription(event, 'faculty_section')">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($faculty_section['title'] ?? 'Our Faculty Members') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Description</label>
                                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($faculty_section['content'] ?? 'Our dedicated and experienced faculty members are committed to nurturing young minds. They bring passion, expertise, and innovation to create an inspiring learning environment.') ?></textarea>
                                <small class="text-muted">This text appears in the Faculty section on the homepage</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Faculty Section
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Infrastructure Section -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); color: white;">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Infrastructure Section Description</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="infrastructureForm" onsubmit="updateSectionDescription(event, 'infrastructure_section')">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($infrastructure_section['title'] ?? 'Our Campus & Infrastructure') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Description</label>
                                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($infrastructure_section['content'] ?? 'Explore our world-class facilities and vibrant campus. Drag to explore, click to expand.') ?></textarea>
                                <small class="text-muted">This text appears in the Infrastructure section on the homepage</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Infrastructure Section
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </div>

    <!-- ============ MODALS ============ -->
    
    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar-alt me-2"></i>Event Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="eventForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="event_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Title *</label>
                                <input type="text" name="title" id="event_title" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitle</label>
                                <input type="text" name="subtitle" id="event_subtitle" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="event_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Event Date</label>
                                <input type="date" name="event_date" id="event_date" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" id="event_order" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="is_pinned" id="event_pinned" class="form-check-input">
                                    <label class="form-check-label" for="event_pinned">Pin to Featured (Hero)</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="image_file" id="event_image_file" class="form-control" accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="uploadImage('event_image_file', 'event_image', 'events')">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-muted">OR</small>
                            </div>
                            <input type="text" name="image_path" id="event_image" class="form-control" placeholder="Enter image URL or path" required>
                            <small class="text-muted">Upload an image or paste URL</small>
                            <div id="event_image_preview" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Official Modal -->
    <div class="modal fade" id="officialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-tie me-2"></i>Official Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="officialForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="official_id">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" id="official_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" id="official_position" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" id="official_designation" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio/Description</label>
                            <textarea name="bio" id="official_bio" class="form-control" rows="4" placeholder="Enter a brief bio or description..." oninput="updateOfficialWordCount()"></textarea>
                            <small class="text-muted">Recommended: 150 words or less</small>
                            <div id="official_bio_warning" class="mt-2" style="display: none;">
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i> <span id="official_word_count">0</span> words - Consider shortening for better readability
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="image_file" id="official_image_file" class="form-control" accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="uploadImage('official_image_file', 'official_image', 'officials')">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-muted">OR</small>
                            </div>
                            <input type="text" name="image_path" id="official_image" class="form-control" placeholder="Enter image URL or path" required>
                            <small class="text-muted">Upload an image or paste URL</small>
                            <div id="official_image_preview" class="mt-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Link</label>
                                <input type="text" name="profile_link" id="official_profile" class="form-control" value="#">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Link</label>
                                <input type="text" name="contact_link" id="official_contact" class="form-control" value="#">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="official_order" class="form-control" value="0">
                            <small class="text-muted">1-3 for header bar, 4+ for homepage cards</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Official</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-graduate me-2"></i>Student Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="studentForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="student_id">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" id="student_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <input type="text" name="role" id="student_role" class="form-control" required placeholder="e.g., Head Boy, Sports Captain">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="image_file" id="student_image_file" class="form-control" accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="uploadImage('student_image_file', 'student_image', 'students')">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-muted">OR</small>
                            </div>
                            <input type="text" name="image_path" id="student_image" class="form-control" placeholder="Enter image URL or path" required>
                            <small class="text-muted">Upload an image or paste URL</small>
                            <div id="student_image_preview" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="student_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Faculty Modal -->
    <div class="modal fade" id="facultyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-chalkboard-teacher me-2"></i>Faculty Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="facultyForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="faculty_id">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" id="faculty_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position/Subject *</label>
                            <input type="text" name="position" id="faculty_position" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department" id="faculty_department" class="form-select">
                                <option value="Primary">Primary</option>
                                <option value="Junior">Junior</option>
                                <option value="Senior">Senior</option>
                                <option value="Non-Teaching">Non-Teaching</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="image_file" id="faculty_image_file" class="form-control" accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="uploadImage('faculty_image_file', 'faculty_image', 'faculty')">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-muted">OR</small>
                            </div>
                            <input type="text" name="image_path" id="faculty_image" class="form-control" placeholder="Enter image URL or path" required>
                            <small class="text-muted">Upload an image or paste URL</small>
                            <div id="faculty_image_preview" class="mt-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" id="faculty_order" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="is_featured" id="faculty_featured" class="form-check-input">
                                    <label class="form-check-label" for="faculty_featured">Featured on Homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Faculty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Infrastructure Modal -->
    <div class="modal fade" id="infrastructureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-building me-2"></i>Infrastructure Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="infrastructureForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="infrastructure_id">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" id="infrastructure_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="infrastructure_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="image_file" id="infrastructure_image_file" class="form-control" accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="uploadImage('infrastructure_image_file', 'infrastructure_image', 'infrastructure')">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="text-center mb-2">
                                <small class="text-muted">OR</small>
                            </div>
                            <input type="text" name="image_path" id="infrastructure_image" class="form-control" placeholder="Enter image URL or path" required>
                            <small class="text-muted">Upload an image or paste URL</small>
                            <div id="infrastructure_image_preview" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="infrastructure_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Infrastructure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- News Modal -->
    <div class="modal fade" id="newsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-newspaper me-2"></i>News Ticker Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="newsForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="news_id">
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <input type="text" name="content" id="news_content" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="news_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save News</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showAlert(message, type = 'success') {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.getElementById('alertContainer').appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        async function submitForm(action, data) {
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', action);
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            const response = await fetch('', { method: 'POST', body: formData });
            return response.json();
        }
        
        // Section description update function
        async function updateSectionDescription(e, section) {
            e.preventDefault();
            const form = e.target;
            const data = {
                section: section,
                title: form.querySelector('[name="title"]').value,
                content: form.querySelector('[name="content"]').value
            };
            const result = await submitForm('update_section_description', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
        }

        async function uploadImage(fileInputId, targetInputId, type) {
            const fileInput = document.getElementById(fileInputId);
            const targetInput = document.getElementById(targetInputId);
            const previewDiv = document.getElementById(targetInputId + '_preview');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                showAlert('Please select a file first', 'warning');
                return;
            }
            
            const file = fileInput.files[0];
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('File too large. Maximum size is 5MB', 'danger');
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showAlert('Invalid file type. Please upload JPG, PNG, GIF, or WEBP', 'danger');
                return;
            }
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('type', type);
            
            try {
                // Show loading
                if (previewDiv) previewDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Uploading...</div>';
                
                const response = await fetch('upload_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    targetInput.value = result.path;
                    targetInput.removeAttribute('required');
                    showAlert(result.message, 'success');
                    
                    // Show preview
                    if (previewDiv) {
                        previewDiv.innerHTML = '<img src="' + result.path + '" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #ddd;" alt="Preview">';
                    }
                } else {
                    showAlert(result.message, 'danger');
                    if (previewDiv) previewDiv.innerHTML = '';
                }
            } catch (error) {
                showAlert('Upload failed: ' + error.message, 'danger');
                if (previewDiv) previewDiv.innerHTML = '';
            }
        }

        // Events
        function clearEventForm(isPinned = false) { 
            document.getElementById('eventForm').reset(); 
            document.getElementById('event_id').value = ''; 
            document.getElementById('event_image_preview').innerHTML = '';
            // Set pinned checkbox based on which section user is adding from
            document.getElementById('event_pinned').checked = isPinned;
        }
        function editEvent(event) {
            document.getElementById('event_id').value = event.id;
            document.getElementById('event_title').value = event.title;
            document.getElementById('event_subtitle').value = event.subtitle || '';
            document.getElementById('event_description').value = event.description || '';
            document.getElementById('event_date').value = event.event_date || '';
            document.getElementById('event_order').value = event.display_order || 0;
            document.getElementById('event_pinned').checked = event.is_pinned == 1;
            document.getElementById('event_image').value = event.image_path || '';
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        }
        function togglePinEvent(id) { submitForm('toggle_pin_event', { id }).then(r => { showAlert(r.message, r.success ? 'success' : 'danger'); if (r.success) location.reload(); }); }
        document.getElementById('eventForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { title: form.querySelector('[name="title"]').value, subtitle: form.querySelector('[name="subtitle"]').value, description: form.querySelector('[name="description"]').value, event_date: form.querySelector('[name="event_date"]').value, display_order: form.querySelector('[name="display_order"]').value, is_pinned: form.querySelector('[name="is_pinned"]').checked ? 1 : 0, image_path: form.querySelector('[name="image_path"]').value };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_event' : 'add_event', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // Officials
        function clearOfficialForm() { 
            document.getElementById('officialForm').reset(); 
            document.getElementById('official_id').value = ''; 
            document.getElementById('official_image_preview').innerHTML = '';
            document.getElementById('official_bio_warning').style.display = 'none';
        }
        
        function updateOfficialWordCount() {
            const bioText = document.getElementById('official_bio').value;
            const words = bioText.trim().split(/\s+/).filter(word => word.length > 0);
            const wordCount = words.length;
            const warningDiv = document.getElementById('official_bio_warning');
            const countSpan = document.getElementById('official_word_count');
            
            if (wordCount > 150) {
                countSpan.textContent = wordCount;
                warningDiv.style.display = 'block';
            } else {
                warningDiv.style.display = 'none';
            }
        }
        
        function editOfficial(o) {
            document.getElementById('official_id').value = o.id;
            document.getElementById('official_name').value = o.name;
            document.getElementById('official_position').value = o.position || '';
            document.getElementById('official_designation').value = o.designation || '';
            document.getElementById('official_bio').value = o.bio || '';
            document.getElementById('official_image').value = o.image_path || '';
            document.getElementById('official_profile').value = o.profile_link || '#';
            document.getElementById('official_contact').value = o.contact_link || '#';
            document.getElementById('official_order').value = o.display_order || 0;
            updateOfficialWordCount(); // Update word count when editing
            new bootstrap.Modal(document.getElementById('officialModal')).show();
        }
        document.getElementById('officialForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { 
                name: form.querySelector('[name="name"]').value, 
                position: form.querySelector('[name="position"]').value, 
                designation: form.querySelector('[name="designation"]').value, 
                bio: form.querySelector('[name="bio"]').value,
                image_path: form.querySelector('[name="image_path"]').value, 
                profile_link: form.querySelector('[name="profile_link"]').value, 
                contact_link: form.querySelector('[name="contact_link"]').value, 
                display_order: form.querySelector('[name="display_order"]').value 
            };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_official' : 'add_official', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // Students
        function clearStudentForm() { 
            document.getElementById('studentForm').reset(); 
            document.getElementById('student_id').value = ''; 
            document.getElementById('student_image_preview').innerHTML = '';
        }
        function editStudent(s) {
            document.getElementById('student_id').value = s.id;
            document.getElementById('student_name').value = s.name;
            document.getElementById('student_role').value = s.role || '';
            document.getElementById('student_image').value = s.image_path || '';
            document.getElementById('student_order').value = s.display_order || 0;
            new bootstrap.Modal(document.getElementById('studentModal')).show();
        }
        document.getElementById('studentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { name: form.querySelector('[name="name"]').value, role: form.querySelector('[name="role"]').value, image_path: form.querySelector('[name="image_path"]').value, display_order: form.querySelector('[name="display_order"]').value };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_student' : 'add_student', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // Faculty
        function clearFacultyForm() { 
            document.getElementById('facultyForm').reset(); 
            document.getElementById('faculty_id').value = ''; 
            document.getElementById('faculty_image_preview').innerHTML = '';
        }
        function editFaculty(f) {
            document.getElementById('faculty_id').value = f.id;
            document.getElementById('faculty_name').value = f.name;
            document.getElementById('faculty_position').value = f.position || '';
            document.getElementById('faculty_department').value = f.department || 'Primary';
            document.getElementById('faculty_image').value = f.image_path || '';
            document.getElementById('faculty_order').value = f.display_order || 0;
            document.getElementById('faculty_featured').checked = f.is_featured == 1;
            new bootstrap.Modal(document.getElementById('facultyModal')).show();
        }
        document.getElementById('facultyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { name: form.querySelector('[name="name"]').value, position: form.querySelector('[name="position"]').value, department: form.querySelector('[name="department"]').value, image_path: form.querySelector('[name="image_path"]').value, display_order: form.querySelector('[name="display_order"]').value, is_featured: form.querySelector('[name="is_featured"]').checked ? 1 : 0 };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_faculty' : 'add_faculty', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // Infrastructure
        function clearInfrastructureForm() { 
            document.getElementById('infrastructureForm').reset(); 
            document.getElementById('infrastructure_id').value = ''; 
            document.getElementById('infrastructure_image_preview').innerHTML = '';
        }
        function editInfrastructure(i) {
            document.getElementById('infrastructure_id').value = i.id;
            document.getElementById('infrastructure_title').value = i.title;
            document.getElementById('infrastructure_description').value = i.description || '';
            document.getElementById('infrastructure_image').value = i.image_path || '';
            document.getElementById('infrastructure_order').value = i.display_order || 0;
            new bootstrap.Modal(document.getElementById('infrastructureModal')).show();
        }
        document.getElementById('infrastructureForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { title: form.querySelector('[name="title"]').value, description: form.querySelector('[name="description"]').value, image_path: form.querySelector('[name="image_path"]').value, display_order: form.querySelector('[name="display_order"]').value };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_infrastructure' : 'add_infrastructure', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // News
        function clearNewsForm() { document.getElementById('newsForm').reset(); document.getElementById('news_id').value = ''; }
        function editNews(n) {
            document.getElementById('news_id').value = n.id;
            document.getElementById('news_content').value = n.content;
            document.getElementById('news_order').value = n.display_order || 0;
            new bootstrap.Modal(document.getElementById('newsModal')).show();
        }
        document.getElementById('newsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const id = form.querySelector('[name="id"]').value;
            const data = { content: form.querySelector('[name="content"]').value, display_order: form.querySelector('[name="display_order"]').value };
            if (id) data.id = id;
            const result = await submitForm(id ? 'update_news' : 'add_news', data);
            showAlert(result.message, result.success ? 'success' : 'danger');
            if (result.success) location.reload();
        });

        // Delete
        function deleteItem(type, id) {
            if (!confirm(`Are you sure you want to delete this ${type}?`)) return;
            submitForm(`delete_${type}`, { id }).then(r => { showAlert(r.message, r.success ? 'success' : 'danger'); if (r.success) location.reload(); });
        }
    </script>
</body>
</html>
