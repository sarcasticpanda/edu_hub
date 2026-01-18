<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Create application_fields table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS application_form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_name VARCHAR(255) NOT NULL,
    field_label VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'email', 'tel', 'date', 'textarea', 'file', 'select') DEFAULT 'text',
    is_required BOOLEAN DEFAULT 1,
    field_options TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle Add Field
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_field'])) {
    $field_name = trim($_POST['field_name']);
    $field_label = trim($_POST['field_label']);
    $field_type = $_POST['field_type'];
    $is_required = isset($_POST['is_required']) ? 1 : 0;
    $field_options = trim($_POST['field_options'] ?? '');
    
    // Get max order
    $max_order = $pdo->query("SELECT MAX(display_order) FROM application_form_fields")->fetchColumn();
    $display_order = ($max_order ?? 0) + 1;
    
    $stmt = $pdo->prepare("INSERT INTO application_form_fields (field_name, field_label, field_type, is_required, field_options, display_order) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$field_name, $field_label, $field_type, $is_required, $field_options, $display_order]);
    
    $_SESSION['success_message'] = 'Field added successfully!';
    header('Location: application_form_manager.php');
    exit;
}

// Handle Delete Field
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM application_form_fields WHERE id = ?")->execute([$id]);
    $_SESSION['success_message'] = 'Field deleted successfully!';
    header('Location: application_form_manager.php');
    exit;
}

// Handle Toggle Active
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $pdo->prepare("UPDATE application_form_fields SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    $_SESSION['success_message'] = 'Field status updated!';
    header('Location: application_form_manager.php');
    exit;
}

// Handle Update Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $orders = $_POST['order'];
    foreach ($orders as $id => $order) {
        $pdo->prepare("UPDATE application_form_fields SET display_order = ? WHERE id = ?")->execute([$order, $id]);
    }
    $_SESSION['success_message'] = 'Field order updated!';
    header('Location: application_form_manager.php');
    exit;
}

// Fetch all fields
$fields = $pdo->query("SELECT * FROM application_form_fields ORDER BY display_order ASC")->fetchAll();

$school_name = getSchoolConfig('school_name', 'School CMS');
$success_msg = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form Manager - <?= htmlspecialchars($school_name) ?></title>
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .field-item {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .field-item:hover {
            border-color: var(--accent-blue);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }
        .field-item.inactive {
            opacity: 0.6;
            background: #e9ecef;
        }
        .badge-type {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .drag-handle {
            cursor: move;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-edit"></i>
                <div class="admin-header-info">
                    <h1>Application Form Manager</h1>
                    <p>Customize application form fields</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="student_applications.php" class="btn-back"><i class="fas fa-arrow-left"></i> Applications</a>
                <a href="index.php" class="btn-view-site"><i class="fas fa-home"></i> Dashboard</a>
            </div>
        </div>

    <div class="container-fluid p-4">
        <?php if ($success_msg): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i><?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add New Field -->
            <div class="col-md-4">
                <div class="card-custom">
                    <h4 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Add New Field</h4>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Field Name <small class="text-muted">(database column)</small></label>
                            <input type="text" name="field_name" class="form-control" placeholder="e.g., guardian_occupation" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Field Label <small class="text-muted">(visible to students)</small></label>
                            <input type="text" name="field_label" class="form-control" placeholder="e.g., Guardian's Occupation" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Field Type</label>
                            <select name="field_type" class="form-select" required>
                                <option value="text">Text</option>
                                <option value="email">Email</option>
                                <option value="tel">Phone Number</option>
                                <option value="date">Date</option>
                                <option value="textarea">Textarea</option>
                                <option value="file">File Upload</option>
                                <option value="select">Dropdown</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Options <small class="text-muted">(for dropdown, comma-separated)</small></label>
                            <input type="text" name="field_options" class="form-control" placeholder="e.g., Option1, Option2, Option3">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_required" class="form-check-input" id="isRequired" checked>
                            <label class="form-check-label" for="isRequired">Required Field</label>
                        </div>
                        <button type="submit" name="add_field" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Field
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Default Fields</h6>
                        <p class="mb-0 small">The following core fields are always included:</p>
                        <ul class="small mb-0 mt-2">
                            <li>Father's Name</li>
                            <li>Father's Contact</li>
                            <li>Mother's Name</li>
                            <li>Mother's Contact</li>
                            <li>Emergency Contact</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Existing Fields -->
            <div class="col-md-8">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-list me-2"></i>Current Form Fields (<?= count($fields) ?>)</h4>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
                            <i class="fas fa-sort me-2"></i>Reorder Fields
                        </button>
                    </div>

                    <?php if (count($fields) > 0): ?>
                        <?php foreach ($fields as $field): ?>
                            <div class="field-item <?= $field['is_active'] ? '' : 'inactive' ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">
                                            <?= htmlspecialchars($field['field_label']) ?>
                                            <?php if ($field['is_required']): ?>
                                                <span class="badge bg-danger">Required</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Optional</span>
                                            <?php endif; ?>
                                            <?php if (!$field['is_active']): ?>
                                                <span class="badge bg-warning">Inactive</span>
                                            <?php endif; ?>
                                        </h5>
                                        <p class="text-muted mb-2">
                                            <strong>Name:</strong> <code><?= htmlspecialchars($field['field_name']) ?></code>
                                            <span class="badge badge-type bg-info text-white ms-2"><?= ucfirst($field['field_type']) ?></span>
                                        </p>
                                        <?php if ($field['field_options']): ?>
                                            <p class="text-muted small mb-0">
                                                <strong>Options:</strong> <?= htmlspecialchars($field['field_options']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ms-3">
                                        <a href="?toggle=<?= $field['id'] ?>" class="btn btn-sm btn-outline-warning me-1" title="Toggle Active">
                                            <i class="fas fa-<?= $field['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteField(<?= $field['id'] ?>, '<?= htmlspecialchars($field['field_label']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5>No Custom Fields Yet</h5>
                            <p class="text-muted">Add custom fields to extend the application form</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reorder Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-sort me-2"></i>Reorder Fields</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Change the order number to reorder fields (lower numbers appear first)</p>
                        <?php foreach ($fields as $field): ?>
                            <div class="mb-3">
                                <label class="form-label"><?= htmlspecialchars($field['field_label']) ?></label>
                                <input type="number" name="order[<?= $field['id'] ?>]" class="form-control" value="<?= $field['display_order'] ?>" min="1">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_order" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteField(id, label) {
            if (confirm(`Are you sure you want to delete the field "${label}"?\n\nThis will remove it from all future application forms.`)) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</body>
</html>
