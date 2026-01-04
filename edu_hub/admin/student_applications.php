<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Get statistics
$stats = [];
try {
    $stats['total'] = $pdo->query("SELECT COUNT(*) FROM student_applications")->fetchColumn();
    $stats['pending'] = $pdo->query("SELECT COUNT(*) FROM student_applications WHERE status = 'pending'")->fetchColumn();
    $stats['under_review'] = $pdo->query("SELECT COUNT(*) FROM student_applications WHERE status = 'under_review'")->fetchColumn();
    $stats['approved'] = $pdo->query("SELECT COUNT(*) FROM student_applications WHERE status = 'approved'")->fetchColumn();
    $stats['rejected'] = $pdo->query("SELECT COUNT(*) FROM student_applications WHERE status = 'rejected'")->fetchColumn();
} catch (Exception $e) {
    $stats = ['total' => 0, 'pending' => 0, 'under_review' => 0, 'approved' => 0, 'rejected' => 0];
}

// Build query
$where = [];
$params = [];

if ($status_filter !== 'all') {
    $where[] = "a.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where[] = "(p.full_name LIKE ? OR a.student_email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get applications with student profile data
$query = "SELECT a.*, p.full_name, p.profile_photo 
          FROM student_applications a 
          LEFT JOIN student_profiles p ON a.student_email = p.student_email 
          $where_clause 
          ORDER BY a.submitted_at DESC 
          LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// Get total for pagination
$count_query = "SELECT COUNT(*) FROM student_applications a 
                LEFT JOIN student_profiles p ON a.student_email = p.student_email 
                $where_clause";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning',
        'under_review' => 'bg-info',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'revision_required' => 'bg-info'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

$school_name = getSchoolConfig('school_name', 'School CMS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Applications - <?= htmlspecialchars($school_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa;
        }
        .header-section {
            background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .stats-row {
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .stat-card.active {
            border-color: #667eea;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            margin: 0.5rem 0;
            font-weight: bold;
        }
        .stat-card.total { color: #667eea; }
        .stat-card.pending { color: #ffc107; }
        .stat-card.under-review { color: #17a2b8; }
        .stat-card.approved { color: #28a745; }
        .stat-card.rejected { color: #dc3545; }
        
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            margin: 0 0.2rem;
        }
        .pagination-container {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Student Application Manager</h1>
                    <p class="mb-0">Manage and review all student applications</p>
                </div>
                <div>
                    <a href="application_form_manager.php" class="btn btn-secondary me-2">
                        Manage Form Fields
                    </a>
                    <a href="index.php" class="btn btn-outline-light">Back to Dashboard</a>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Statistics -->
            <div class="row stats-row">
                <div class="col-md">
                    <a href="?status=all" class="text-decoration-none">
                        <div class="stat-card total <?= $status_filter === 'all' ? 'active' : '' ?>">
                            <h3><?= $stats['total'] ?></h3>
                            <p class="mb-0">Total Applications</p>
                        </div>
                    </a>
                </div>
                <div class="col-md">
                    <a href="?status=pending" class="text-decoration-none">
                        <div class="stat-card pending <?= $status_filter === 'pending' ? 'active' : '' ?>">
                            <h3><?= $stats['pending'] ?></h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </a>
                </div>
                <div class="col-md">
                    <a href="?status=under_review" class="text-decoration-none">
                        <div class="stat-card under-review <?= $status_filter === 'under_review' ? 'active' : '' ?>">
                            <h3><?= $stats['under_review'] ?></h3>
                            <p class="mb-0">Under Review</p>
                        </div>
                    </a>
                </div>
                <div class="col-md">
                    <a href="?status=approved" class="text-decoration-none">
                        <div class="stat-card approved <?= $status_filter === 'approved' ? 'active' : '' ?>">
                            <h3><?= $stats['approved'] ?></h3>
                            <p class="mb-0">Approved</p>
                        </div>
                    </a>
                </div>
                <div class="col-md">
                    <a href="?status=rejected" class="text-decoration-none">
                        <div class="stat-card rejected <?= $status_filter === 'rejected' ? 'active' : '' ?>">
                            <h3><?= $stats['rejected'] ?></h3>
                            <p class="mb-0">Rejected</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="filter-section">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="under_review" <?= $status_filter === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                    </div>
                </form>
            </div>

            <!-- Applications Table -->
            <div class="table-container">
                <?php if (count($applications) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($app['profile_photo']): ?>
                                                    <img src="../<?= htmlspecialchars($app['profile_photo']) ?>" alt="Profile" class="student-avatar me-2">
                                                <?php else: ?>
                                                    <div class="student-avatar me-2 bg-primary text-white d-flex align-items-center justify-content-center">
                                                        <?= strtoupper(substr($app['full_name'] ?? 'S', 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <strong><?= htmlspecialchars($app['full_name'] ?? 'N/A') ?></strong>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($app['student_email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                        <td>
                                            <span class="status-badge <?= getStatusBadgeClass($app['status']) ?> text-white">
                                                <?= $app['status'] === 'revision_required' ? 'Under Review' : ucwords(str_replace('_', ' ', $app['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_application.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary action-btn">
                                                View
                                            </a>
                                            <button class="btn btn-sm btn-success action-btn" onclick="quickAction(<?= $app['id'] ?>, 'approved')">
                                                Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger action-btn" onclick="quickAction(<?= $app['id'] ?>, 'rejected')">
                                                Reject
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="pagination-container">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <h4>No Applications Found</h4>
                        <p class="text-muted">There are no student applications matching your filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function quickAction(appId, status) {
            if (confirm(`Are you sure you want to ${status} this application?`)) {
                window.location.href = `update_application_status.php?id=${appId}&status=${status}&quick=1`;
            }
        }
    </script>
</body>
</html>
