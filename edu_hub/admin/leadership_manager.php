<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Ensure sections table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS leadership_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed default sections if they do not already exist
$defaultSections = ['Primary', 'Junior', 'Senior', 'Non-Teaching'];
$stmtSeed = $pdo->prepare('INSERT IGNORE INTO leadership_sections (name) VALUES (?)');
foreach ($defaultSections as $defSec) {
    $stmtSeed->execute([$defSec]);
}

// Handle section addition
if (isset($_POST['add_section']) && !empty(trim($_POST['new_section'] ?? ''))) {
    $newSec = trim($_POST['new_section']);
    $stmt = $pdo->prepare('INSERT IGNORE INTO leadership_sections (name) VALUES (?)');
    $stmt->execute([$newSec]);
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $role = $_POST['role'] ?? '';
        $section = $_POST['section'] ?? 'Individual';
        $years_worked = $_POST['years_worked'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';
        $modal_content = json_encode([
            'name' => $name,
            'designation' => $role,
            'years_worked' => $years_worked,
            'contact_email' => $contact_email
        ]) ?: '{}';

        // Handle image upload
        $image_path = $_POST['image_path'] ?? '';
        if (!empty($_FILES['image_upload']['name'])) {
            $uploadDir = '../check/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = 'leader_' . time() . '_' . basename($_FILES['image_upload']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $targetPath)) {
                $image_path = '/seqto_edu_share/edu_hub/edu_hub/check/images/' . $fileName;
            }
        } elseif ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("SELECT image_path FROM leadership WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $image_path = $existing ? $existing['image_path'] : '';
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO leadership (name, role, section, image_path, modal_content) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $role, $section, $image_path, $modal_content]);
            $message = "Leadership entry added successfully.";
        } elseif ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("UPDATE leadership SET name = ?, role = ?, section = ?, image_path = ?, modal_content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$name, $role, $section, $image_path, $modal_content, $id]);
            $message = "Leadership entry updated successfully.";
        } elseif ($action === 'delete' && $id) {
            $stmt = $pdo->prepare("DELETE FROM leadership WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Leadership entry deleted successfully.";
        }
    }
}

// Fetch available sections for dropdown
$sections_db = $pdo->query('SELECT name FROM leadership_sections ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
if (!$sections_db) {
    // no custom sections yet; use defaults (excluding 'Individual' which is always present)
    $sections_db = ['Primary','Junior','Senior','Non-Teaching'];
}
// Always include the built-in "Individual" option
$sections = array_unique(array_merge(['Individual'], $sections_db));

// Fetch all leadership entries
$leadership = $pdo ? $pdo->query("SELECT * FROM leadership ORDER BY created_at DESC")->fetchAll() : [];
$edit_data = [];
$edit_content = [];
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    foreach ($leadership as $l) {
        if ($l['id'] == $edit_id) {
            $edit_data = $l;
            $edit_content = json_decode($l['modal_content'] ?? '{}', true);
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leadership Management - Admin Portal</title>
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
        .back-btn {
            margin: 20px 0 0 20px;
        }
        .member-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .member-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .member-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-secondary back-btn">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Leadership Management</h1>
            <p class="mb-0">Manage leadership sections and members</p>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="card p-4 mb-4">
            <h4 style="font-family: 'Poppins', sans-serif;"><?php echo isset($_GET['edit_id']) ? 'Edit Leadership' : 'Add New Leadership'; ?></h4>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo isset($_GET['edit_id']) ? 'edit' : 'add'; ?>">
                <input type="hidden" name="id" value="<?php echo isset($_GET['edit_id']) ? htmlspecialchars($_GET['edit_id']) : ''; ?>">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_data['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($edit_data['role'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Section</label>
                    <select name="section" class="form-select" required>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= htmlspecialchars($sec) ?>" <?= (isset($edit_data['section']) && $edit_data['section'] === $sec) ? 'selected' : '' ?>><?= htmlspecialchars($sec) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="mt-2">
                        <input type="text" class="form-control" name="new_section" placeholder="Add new section">
                        <button type="submit" name="add_section" class="btn btn-sm btn-secondary mt-1">Add Section</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image Path</label>
                    <input type="text" name="image_path" class="form-control" value="<?php echo htmlspecialchars($edit_data['image_path'] ?? ''); ?>">
                    <input type="file" name="image_upload" class="form-control mt-2" accept="image/*">
                    <small class="text-muted">Upload new image or provide a path</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Years Worked</label>
                    <input type="text" name="years_worked" class="form-control" value="<?php echo htmlspecialchars($edit_content['years_worked'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($edit_content['contact_email'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?php echo isset($_GET['edit_id']) ? 'Update' : 'Add'; ?></button>
                <?php if (isset($_GET['edit_id'])): ?>
                    <a href="leadership_manager.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Leadership List -->
        <div class="card p-4">
            <h4 style="font-family: 'Poppins', sans-serif;">Leadership Entries</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Section</th>
                            <th>Image Path</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leadership as $leader): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leader['name']); ?></td>
                                <td><?php echo htmlspecialchars($leader['role']); ?></td>
                                <td><?php echo htmlspecialchars($leader['section']); ?></td>
                                <td><?php echo htmlspecialchars($leader['image_path']); ?></td>
                                <td>
                                    <a href="?edit_id=<?php echo $leader['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $leader['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>