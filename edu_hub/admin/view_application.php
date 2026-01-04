<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$app_id = $_GET['id'] ?? 0;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $admin_message = $_POST['admin_message'] ?? '';
    $reviewed_by = $_SESSION['admin_name'] ?? 'Admin';
    
    $stmt = $pdo->prepare("UPDATE student_applications 
                          SET status = ?, admin_message = ?, reviewed_by = ?, status_updated_at = NOW() 
                          WHERE id = ?");
    $stmt->execute([$new_status, $admin_message, $reviewed_by, $app_id]);
    
    $_SESSION['success_message'] = 'Application status updated successfully!';
    header('Location: view_application.php?id=' . $app_id);
    exit;
}

// Handle send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $subject = $_POST['message_subject'];
    $message = $_POST['message_text'];
    $message_type = $_POST['message_type'] ?? 'general';
    
    $stmt = $pdo->prepare("INSERT INTO admin_student_messages (student_email, message_type, subject, message, sent_by) 
                          VALUES ((SELECT student_email FROM student_applications WHERE id = ?), ?, ?, ?, ?)");
    $stmt->execute([$app_id, $message_type, $subject, $message, $_SESSION['admin_name'] ?? 'Admin']);
    
    $_SESSION['success_message'] = 'Message sent to student successfully!';
    header('Location: view_application.php?id=' . $app_id);
    exit;
}

// Fetch application details
$stmt = $pdo->prepare("SELECT a.*, p.* 
                      FROM student_applications a 
                      LEFT JOIN student_profiles p ON a.student_email = p.student_email 
                      WHERE a.id = ?");
$stmt->execute([$app_id]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: student_applications.php');
    exit;
}

$certificates = !empty($application['certificates']) ? json_decode($application['certificates'], true) : [];

// Fetch custom field definitions
$custom_fields = $pdo->query("SELECT * FROM application_form_fields ORDER BY display_order ASC")->fetchAll();

// Fetch student messages to admin
$student_messages = $pdo->prepare("SELECT * FROM student_to_admin_messages WHERE student_email = ? ORDER BY sent_at DESC");
$student_messages->execute([$application['student_email']]);
$student_messages = $student_messages->fetchAll();

// Fetch additional documents uploaded by student
$additional_docs = $pdo->prepare("SELECT * FROM application_additional_documents WHERE student_email = ? ORDER BY uploaded_at DESC");
$additional_docs->execute([$application['student_email']]);
$additional_docs = $additional_docs->fetchAll();

function getStatusColor($status) {
    $colors = [
        'pending' => '#ffc107',
        'under_review' => '#17a2b8',
        'approved' => '#28a745',
        'rejected' => '#dc3545',
        'revision_required' => '#6c757d'
    ];
    return $colors[$status] ?? '#6c757d';
}

$school_name = getSchoolConfig('school_name', 'School CMS');
$success_msg = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - <?= htmlspecialchars($school_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa;
            padding-bottom: 3rem;
        }
        .header-section {
            background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .profile-card, .details-card, .actions-card, .documents-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
        }
        .info-row {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .status-badge-large {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-block;
        }
        .document-link {
            display: block;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s;
        }
        .document-link:hover {
            background: #e9ecef;
            color: #1E2A44;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-file-alt me-3"></i>Application Details</h1>
                    <p class="mb-0">Review and manage student application</p>
                </div>
                <a href="student_applications.php" class="btn btn-light"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column - Student Profile -->
            <div class="col-md-4">
                <div class="profile-card text-center">
                    <h4 class="mb-4"><i class="fas fa-user-circle me-2"></i>Student Profile</h4>
                    <?php if ($application['profile_photo']): ?>
                        <img src="../<?= htmlspecialchars($application['profile_photo']) ?>" alt="Profile" class="profile-avatar-large">
                    <?php else: ?>
                        <div class="profile-avatar-large mx-auto bg-primary text-white d-flex align-items-center justify-content-center" style="font-size: 3rem;">
                            <?= strtoupper(substr($application['full_name'] ?? 'S', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="mt-3"><?= htmlspecialchars($application['full_name'] ?? 'N/A') ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($application['student_email']) ?></p>
                    
                    <div class="info-row text-start">
                        <span class="info-label">Date of Birth:</span>
                        <span class="float-end"><?= $application['dob'] ? date('M d, Y', strtotime($application['dob'])) : 'N/A' ?></span>
                    </div>
                    <div class="info-row text-start">
                        <span class="info-label">Gender:</span>
                        <span class="float-end"><?= htmlspecialchars($application['gender'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row text-start">
                        <span class="info-label">Contact:</span>
                        <span class="float-end"><?= htmlspecialchars($application['contact'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row text-start">
                        <span class="info-label">Address:</span>
                        <div class="mt-2"><?= nl2br(htmlspecialchars($application['address'] ?? 'N/A')) ?></div>
                    </div>
                    <div class="info-row text-start">
                        <span class="info-label">Submitted:</span>
                        <span class="float-end"><?= date('M d, Y h:i A', strtotime($application['submitted_at'])) ?></span>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="actions-card">
                    <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Current Status</h5>
                    <div class="text-center mb-3">
                        <span class="status-badge-large text-white" style="background: <?= getStatusColor($application['status']) ?>">
                            <?= $application['status'] === 'revision_required' ? 'Under Review' : ucwords(str_replace('_', ' ', $application['status'])) ?>
                        </span>
                    </div>
                    <?php if (!empty($application['reviewed_by'])): ?>
                        <p class="text-muted text-center small mb-0">Reviewed by: <?= htmlspecialchars($application['reviewed_by']) ?></p>
                        <?php if ($application['status_updated_at']): ?>
                            <p class="text-muted text-center small"><?= date('M d, Y h:i A', strtotime($application['status_updated_at'])) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Middle Column - Application Details -->
            <div class="col-md-5">
                <div class="details-card">
                    <h4 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Application Information</h4>
                    
                    <h6 class="text-primary mt-4 mb-3">Parent/Guardian Information</h6>
                    <div class="info-row">
                        <span class="info-label">Father's Name:</span>
                        <span class="float-end"><?= htmlspecialchars($application['father_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Father's Contact:</span>
                        <span class="float-end"><?= htmlspecialchars($application['father_contact'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mother's Name:</span>
                        <span class="float-end"><?= htmlspecialchars($application['mother_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mother's Contact:</span>
                        <span class="float-end"><?= htmlspecialchars($application['mother_contact'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Emergency Contact:</span>
                        <span class="float-end"><?= htmlspecialchars($application['emergency_contact'] ?? 'N/A') ?></span>
                    </div>
                    
                    <?php if ($application['medical_info']): ?>
                        <h6 class="text-primary mt-4 mb-3">Medical Information</h6>
                        <div class="alert alert-info">
                            <?= nl2br(htmlspecialchars($application['medical_info'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['admin_message']): ?>
                        <h6 class="text-danger mt-4 mb-3">Admin Feedback</h6>
                        <div class="alert alert-warning">
                            <?= nl2br(htmlspecialchars($application['admin_message'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($custom_fields)): ?>
                        <h6 class="text-primary mt-4 mb-3">Additional Information</h6>
                        <?php foreach ($custom_fields as $field): ?>
                            <?php 
                            // Get value from database
                            $field_name = $field['field_name'];
                            $custom_val_stmt = $pdo->prepare("SELECT field_value, file_path FROM application_custom_data WHERE student_email = ? AND field_name = ?");
                            $custom_val_stmt->execute([$application['student_email'], $field_name]);
                            $custom_val = $custom_val_stmt->fetch();
                            
                            if ($custom_val && ($custom_val['field_value'] || $custom_val['file_path'])):
                            ?>
                                <div class="info-row">
                                    <span class="info-label"><?= htmlspecialchars($field['field_label']) ?>:</span>
                                    <?php if ($custom_val['file_path']): ?>
                                        <div class="mt-2">
                                            <a href="../<?= htmlspecialchars($custom_val['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file me-2"></i>View File
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="float-end"><?= htmlspecialchars($custom_val['field_value']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Documents -->
                <div class="documents-card">
                    <h5 class="mb-3"><i class="fas fa-file-upload me-2"></i>Uploaded Documents</h5>
                    
                    <?php if ($application['id_document']): ?>
                        <h6 class="mt-3">ID Document</h6>
                        <a href="../<?= htmlspecialchars($application['id_document']) ?>" target="_blank" class="document-link">
                            <i class="fas fa-file-pdf me-2"></i><?= basename($application['id_document']) ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($certificates)): ?>
                        <h6 class="mt-3">Certificates (<?= count($certificates) ?>)</h6>
                        <?php foreach ($certificates as $cert): ?>
                            <a href="../<?= htmlspecialchars($cert) ?>" target="_blank" class="document-link">
                                <i class="fas fa-file-alt me-2"></i><?= basename($cert) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!$application['id_document'] && empty($certificates)): ?>
                        <p class="text-muted">No documents uploaded</p>
                    <?php endif; ?>
                </div>
                
                <!-- Additional Documents from Student -->
                <?php if (!empty($additional_docs)): ?>
                <div class="documents-card mt-3">
                    <h5 class="mb-3">Additional Documents from Student</h5>
                    <?php foreach ($additional_docs as $doc): ?>
                        <div class="mb-2 p-2 border rounded">
                            <strong><?= htmlspecialchars($doc['document_name']) ?></strong>
                            <br><small class="text-muted">Uploaded: <?= date('M d, Y h:i A', strtotime($doc['uploaded_at'])) ?></small>
                            <br><a href="../<?= htmlspecialchars($doc['document_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fas fa-file me-1"></i>View
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Student Messages to Admin -->
                <?php if (!empty($student_messages)): ?>
                <div class="details-card mt-3">
                    <h5 class="mb-3">Messages from Student</h5>
                    <?php foreach ($student_messages as $msg): ?>
                        <div class="mb-3 p-3 border rounded <?= $msg['is_read'] ? 'bg-light' : 'bg-warning bg-opacity-10' ?>">
                            <h6 class="mb-1"><?= htmlspecialchars($msg['subject']) ?></h6>
                            <p class="mb-2"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                            <?php if ($msg['attachment']): ?>
                                <a href="../<?= htmlspecialchars($msg['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-paperclip me-1"></i>Attachment
                                </a>
                            <?php endif; ?>
                            <br><small class="text-muted">Sent: <?= date('M d, Y h:i A', strtotime($msg['sent_at'])) ?></small>
                            <?php if ($msg['admin_reply']): ?>
                                <div class="mt-2 p-2 bg-success bg-opacity-10 rounded">
                                    <strong>Your Reply:</strong>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($msg['admin_reply'])) ?></p>
                                    <small class="text-muted">Replied: <?= date('M d, Y h:i A', strtotime($msg['replied_at'])) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Actions -->
            <div class="col-md-3">
                <div class="actions-card">
                    <h5 class="mb-4"><i class="fas fa-cog me-2"></i>Admin Actions</h5>
                    
                    <!-- Update Status Form -->
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" <?= $application['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="under_review" <?= $application['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                                <option value="approved" <?= $application['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $application['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="revision_required" <?= $application['status'] === 'revision_required' ? 'selected' : '' ?>>Revision Required</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Feedback</label>
                            <textarea name="admin_message" class="form-control" rows="4" placeholder="Add message for student..."><?= htmlspecialchars($application['admin_message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </form>
                </div>

                <!-- Send Message -->
                <div class="actions-card">
                    <h5 class="mb-4"><i class="fas fa-envelope me-2"></i>Send Message</h5>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Message Type</label>
                            <select name="message_type" class="form-select">
                                <option value="general">General</option>
                                <option value="application_feedback">Application Feedback</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <input type="text" name="message_subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
