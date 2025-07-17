<?php
// admin/section_manager.php
// Simple CRUD for leadership/gallery sections

session_start();
// Basic auth check – adjust per your auth system
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// DB connection (reuse parameters from other admin pages)
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
    die('DB Connection failed');
}

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS leadership_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Handle add new section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_section'])) {
    $name = trim($_POST['new_section']);
    if ($name !== '') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO leadership_sections (name) VALUES (?)');
        $stmt->execute([$name]);
    }
    header('Location: section_manager.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM leadership_sections WHERE id = ?')->execute([$id]);
    header('Location: section_manager.php');
    exit;
}

// Fetch all sections
$sections = $pdo->query('SELECT * FROM leadership_sections ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-5">
    <h1 class="mb-4">Gallery / Leadership Sections</h1>
    <form class="row gy-2 gx-3 align-items-center mb-4" method="POST">
        <div class="col-auto">
            <input type="text" class="form-control" name="new_section" placeholder="New Section Name" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Add Section</button>
        </div>
    </form>

    <table class="table table-bordered bg-white shadow-sm">
        <thead>
            <tr>
                <th style="width:60px">#</th>
                <th>Section Name</th>
                <th style="width:100px">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$sections): ?>
                <tr><td colspan="3" class="text-center">No sections found.</td></tr>
            <?php else: ?>
                <?php foreach ($sections as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td>
                            <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this section?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
