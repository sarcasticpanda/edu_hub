<?php
// Start session and connect to database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/includes/db.php';

// Get filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filters
$sql = "SELECT * FROM notices WHERE is_active = 1";
$params = [];

if ($selectedCategory !== 'all') {
    $sql .= " AND notice_type = ?";
    $params[] = $selectedCategory;
}

if (!empty($searchQuery)) {
    $sql .= " AND (title LIKE ? OR content LIKE ? OR posted_by LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY is_pinned DESC, created_at DESC";

// Fetch notices
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $notices = [];
}

// Get category counts
$categoryCounts = [
    'all' => 0,
    'circular' => 0,
    'announcement' => 0,
    'event' => 0,
    'general' => 0
];

try {
    $countStmt = $pdo->query("SELECT notice_type, COUNT(*) as count FROM notices WHERE is_active = 1 GROUP BY notice_type");
    while ($row = $countStmt->fetch(PDO::FETCH_ASSOC)) {
        $categoryCounts[$row['notice_type']] = $row['count'];
        $categoryCounts['all'] += $row['count'];
    }
} catch (Exception $e) {
    // Use default counts
}

// Separate pinned and regular notices
$pinnedNotices = array_filter($notices, fn($n) => $n['is_pinned'] == 1);
$regularNotices = array_filter($notices, fn($n) => $n['is_pinned'] == 0);

// Helper functions
function formatDate($dateStr) {
    return date('d M Y', strtotime($dateStr));
}

function isNew($dateStr) {
    $noticeDate = strtotime($dateStr);
    $weekAgo = strtotime('-7 days');
    return $noticeDate >= $weekAgo;
}

function getCategoryColor($type) {
    $colors = [
        'circular' => ['bg' => 'bg-primary', 'light' => 'bg-primary bg-opacity-10 text-primary border-primary'],
        'announcement' => ['bg' => 'bg-warning', 'light' => 'bg-warning bg-opacity-10 text-warning border-warning'],
        'event' => ['bg' => 'bg-info', 'light' => 'bg-info bg-opacity-10 text-info border-info'],
        'general' => ['bg' => 'bg-success', 'light' => 'bg-success bg-opacity-10 text-success border-success']
    ];
    return $colors[$type] ?? ['bg' => 'bg-secondary', 'light' => 'bg-secondary bg-opacity-10 text-secondary border-secondary'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Notice Board - Official Announcements & Circulars</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#166534',
                        saffron: '#ff8c00',
                        background: '#ffffff',
                        foreground: '#1a1a1a',
                        border: '#e5e7eb',
                        'muted-foreground': '#6b7280',
                        peach: '#fff7ed'
                    }
                }
            }
        }
    </script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Noto+Sans+Telugu:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        /* Ensure Tailwind utilities work */
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar specific fixes - Match main website styling */
        .gov-navbar {
            background: linear-gradient(180deg, hsl(120, 50%, 45%) 0%, hsl(120, 61%, 28%) 50%, hsl(120, 70%, 20%) 100%) !important;
            border-top: 2px solid #ff8c00;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .gov-navbar .container {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 100%;
        }
        
        .gov-navbar .hidden.md\:flex {
            display: flex !important;
            align-items: center;
            justify-content: space-evenly;
            gap: 0;
            flex-wrap: nowrap;
            width: 100%;
        }
        
        .gov-navbar-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            color: white !important;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s !important;
            white-space: nowrap;
            flex: 1;
            text-align: center;
        }
        
        .gov-navbar-link:last-child {
            border-right: none;
        }
        
        .gov-navbar-link:hover {
            background-color: #ff8c00 !important;
            color: white !important;
        }
        
        .notice-hero {
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 50%, #be185d 100%);
            color: white;
            padding: 60px 0 50px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 0;
        }
        
        .notice-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }
        
        .notice-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .notice-card.pinned {
            border-left: 5px solid #f97316;
            background: linear-gradient(to right, #fff7ed 0%, white 5%);
        }
        
        .category-btn {
            transition: all 0.2s ease;
            border: none;
        }
        
        .category-btn:hover {
            transform: translateX(4px);
            background: #e9ecef;
        }
        
        .category-btn.active {
            background: #166534;
            color: white;
        }
        
        .category-btn.active:hover {
            background: #14532d;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .color-bar {
            height: 4px;
            width: 100%;
        }
        
        .badge-new {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        .search-box {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .search-box:focus {
            border-color: #166534;
            box-shadow: 0 0 0 0.2rem rgba(22, 101, 52, 0.25);
        }
        
        /* Smooth scroll for anchor links */
        html {
            scroll-behavior: smooth;
        }
        
        /* Highlight target notice */
        .notice-card:target {
            animation: highlightNotice 2s ease;
        }
        
        @keyframes highlightNotice {
            0% {
                background-color: #fff3cd;
                transform: scale(1.02);
            }
            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Include Navbar -->
    <?php include __DIR__ . '/includes/header_navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow-1 py-5">
        <div class="container">
            <div class="row g-4">
                
                <!-- Sidebar -->
                <aside class="col-lg-3">
                    <!-- Search Box -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-search me-2"></i>Search Notices
                            </h6>
                            <form method="GET" action="">
                                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                                <div class="mb-2">
                                    <input 
                                        type="text" 
                                        name="search"
                                        class="form-control search-box" 
                                        placeholder="Search by keyword..." 
                                        value="<?= htmlspecialchars($searchQuery) ?>"
                                    >
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-filter me-2"></i>Filter by Category
                            </h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="?category=all<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                               class="list-group-item list-group-item-action category-btn d-flex justify-content-between align-items-center <?= $selectedCategory === 'all' ? 'active' : '' ?>">
                                <span><i class="fas fa-list me-2"></i>All Notices</span>
                                <span class="badge bg-secondary rounded-pill"><?= $categoryCounts['all'] ?></span>
                            </a>
                            <a href="?category=circular<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                               class="list-group-item list-group-item-action category-btn d-flex justify-content-between align-items-center <?= $selectedCategory === 'circular' ? 'active' : '' ?>">
                                <span><i class="fas fa-file-circle-check me-2"></i>Circulars</span>
                                <span class="badge bg-secondary rounded-pill"><?= $categoryCounts['circular'] ?></span>
                            </a>
                            <a href="?category=announcement<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                               class="list-group-item list-group-item-action category-btn d-flex justify-content-between align-items-center <?= $selectedCategory === 'announcement' ? 'active' : '' ?>">
                                <span><i class="fas fa-bullhorn me-2"></i>Announcements</span>
                                <span class="badge bg-secondary rounded-pill"><?= $categoryCounts['announcement'] ?></span>
                            </a>
                            <a href="?category=event<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                               class="list-group-item list-group-item-action category-btn d-flex justify-content-between align-items-center <?= $selectedCategory === 'event' ? 'active' : '' ?>">
                                <span><i class="fas fa-calendar-star me-2"></i>Events</span>
                                <span class="badge bg-secondary rounded-pill"><?= $categoryCounts['event'] ?></span>
                            </a>
                            <a href="?category=general<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                               class="list-group-item list-group-item-action category-btn d-flex justify-content-between align-items-center <?= $selectedCategory === 'general' ? 'active' : '' ?>">
                                <span><i class="fas fa-bell me-2"></i>General</span>
                                <span class="badge bg-secondary rounded-pill"><?= $categoryCounts['general'] ?></span>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="card shadow-sm mt-4 border-warning">
                        <div class="card-body bg-warning bg-opacity-10">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="fas fa-info-circle me-2"></i>Quick Info
                            </h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li class="mb-1">Notices updated regularly</li>
                                <li class="mb-1">Download PDF attachments</li>
                                <li class="mb-1">Pinned notices are important</li>
                                <li>Use search for quick access</li>
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    <!-- Results Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <p class="text-muted mb-0">
                                Showing <strong class="text-dark"><?= count($notices) ?></strong> notice<?= count($notices) != 1 ? 's' : '' ?>
                                <?php if ($selectedCategory !== 'all'): ?>
                                    in <strong class="text-dark text-capitalize"><?= $selectedCategory ?></strong>
                                <?php endif; ?>
                                <?php if ($searchQuery): ?>
                                    for "<strong class="text-dark"><?= htmlspecialchars($searchQuery) ?></strong>"
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php if ($searchQuery || $selectedCategory !== 'all'): ?>
                            <a href="?" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($notices)): ?>
                        <!-- No Results -->
                        <div class="card shadow-sm text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-clipboard-list fa-5x text-muted mb-4 opacity-50"></i>
                                <h4 class="fw-bold text-dark mb-3">No Notices Found</h4>
                                <p class="text-muted mb-4">We couldn't find any notices matching your criteria. Try adjusting your filters.</p>
                                <a href="?" class="btn btn-success">
                                    <i class="fas fa-redo me-2"></i>Reset Filters
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <!-- Pinned Notices -->
                        <?php if (!empty($pinnedNotices)): ?>
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-4">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-warning">Important Notices</h5>
                                        <small class="text-muted">Priority announcements requiring immediate attention</small>
                                    </div>
                                </div>
                                
                                <?php foreach ($pinnedNotices as $notice): 
                                    $colors = getCategoryColor($notice['notice_type']);
                                ?>
                                    <div class="notice-card pinned" id="notice-<?= $notice['id'] ?>">
                                        <div class="color-bar <?= $colors['bg'] ?>"></div>
                                        <div class="p-4">
                                            <!-- Badges -->
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <?php if (isNew($notice['created_at'])): ?>
                                                    <span class="badge bg-danger badge-new">
                                                        NEW
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge <?= $colors['light'] ?> border fw-semibold">
                                                    <?= strtoupper($notice['notice_type']) ?>
                                                </span>
                                                <span class="badge bg-warning text-dark fw-semibold">
                                                    PINNED
                                                </span>
                                                <?php if (!empty($notice['attachment_path'])): ?>
                                                    <span class="badge bg-info text-white">
                                                        Attachment
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Title -->
                                            <h5 class="fw-bold mb-3 text-dark"><?= htmlspecialchars($notice['title']) ?></h5>
                                            
                                            <!-- Content -->
                                            <p class="text-muted mb-3 lh-lg"><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
                                            
                                            <!-- Meta Info -->
                                            <div class="d-flex flex-wrap gap-4 text-sm text-muted mb-3 pb-3 border-bottom">
                                                <span>
                                                    <strong><?= formatDate($notice['created_at']) ?></strong>
                                                </span>
                                                <span>
                                                    <strong><?= htmlspecialchars($notice['posted_by']) ?></strong>
                                                </span>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <?php if (!empty($notice['attachment_path'])): ?>
                                                <div class="mt-3 p-3 bg-light rounded">
                                                    <strong class="d-block mb-2"><i class="fas fa-paperclip me-2"></i>Attachment</strong>
                                                    <a href="/2026/edu_hub/edu_hub/storage/notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                       target="_blank"
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-download me-2"></i>View/Download Attachment
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Regular Notices -->
                        <?php if (!empty($regularNotices)): ?>
                            <div>
                                <?php if (!empty($pinnedNotices)): ?>
                                    <div class="d-flex align-items-center mb-4">
                                        <div>
                                            <h5 class="fw-bold mb-1 text-success">All Notices</h5>
                                            <small class="text-muted">Complete list of official notices</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php foreach ($regularNotices as $notice): 
                                    $colors = getCategoryColor($notice['notice_type']);
                                ?>
                                    <div class="notice-card" id="notice-<?= $notice['id'] ?>">
                                        <div class="color-bar <?= $colors['bg'] ?>"></div>
                                        <div class="p-4">
                                            <!-- Badges -->
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <?php if (isNew($notice['created_at'])): ?>
                                                    <span class="badge bg-danger badge-new">
                                                        NEW
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge <?= $colors['light'] ?> border fw-semibold">
                                                    <?= strtoupper($notice['notice_type']) ?>
                                                </span>
                                                <?php if (!empty($notice['attachment_path'])): ?>
                                                    <span class="badge bg-info text-white">
                                                        Attachment
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Title -->
                                            <h5 class="fw-bold mb-3 text-dark"><?= htmlspecialchars($notice['title']) ?></h5>
                                            
                                            <!-- Content -->
                                            <p class="text-muted mb-3 lh-lg"><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
                                            
                                            <!-- Meta Info -->
                                            <div class="d-flex flex-wrap gap-4 text-sm text-muted mb-3 pb-3 border-bottom">
                                                <span>
                                                    <strong><?= formatDate($notice['created_at']) ?></strong>
                                                </span>
                                                <span>
                                                    <strong><?= htmlspecialchars($notice['posted_by']) ?></strong>
                                                </span>
                                            </div>
                                            
                                            <!-- Attachment Section -->
                                            <?php if (!empty($notice['attachment_path'])): ?>
                                                <div class="mt-3 p-3 bg-light rounded">
                                                    <strong class="d-block mb-2">Attachment</strong>
                                                    <a href="/2026/edu_hub/edu_hub/storage/notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                       target="_blank"
                                                       class="btn btn-success btn-sm">
                                                        View/Download Attachment
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer -->
    <?php 
    // Force fresh load to avoid old footer cache
    include __DIR__ . '/includes/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
        
        // Dropdown toggle
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const chevron = document.getElementById('dropdownChevron');
            if (menu) {
                menu.classList.toggle('show');
            }
            if (chevron) {
                chevron.style.transform = menu && menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
        
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('mobileMenuIcon');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
        
        // Student login modal
        function openStudentLoginModal() {
            const modal = document.getElementById('studentLoginModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            }
        }
        
        function closeStudentLoginModal(event) {
            if (event && event.target.id === 'studentLoginModal') {
                const modal = document.getElementById('studentLoginModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('loginDropdown');
            const menu = document.getElementById('dropdownMenu');
            if (dropdown && menu && !dropdown.contains(event.target)) {
                menu.classList.remove('show');
                const chevron = document.getElementById('dropdownChevron');
                if (chevron) {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        });
    </script>
</body>
</html>
