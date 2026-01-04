<?php
// Professional Student Dashboard - Redesigned
// IMPORTANT: Start session and handle all logic BEFORE any output or includes
session_start();

// Database connection
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
$pdo = new PDO($dsn, $user, $pass, $options);

// Ensure tables exist with updated schema
$pdo->exec("CREATE TABLE IF NOT EXISTS student_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(255),
    dob DATE,
    gender VARCHAR(20),
    contact VARCHAR(20),
    address TEXT,
    profile_photo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS student_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL UNIQUE,
    father_name VARCHAR(255),
    father_contact VARCHAR(20),
    mother_name VARCHAR(255),
    mother_contact VARCHAR(20),
    emergency_contact VARCHAR(20),
    id_document VARCHAR(255),
    certificates TEXT,
    medical_info TEXT,
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'revision_required') DEFAULT 'pending',
    admin_message TEXT,
    status_updated_at TIMESTAMP NULL,
    reviewed_by VARCHAR(100),
    submission_count INT DEFAULT 1,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add columns if they don't exist
$pdo->exec("ALTER TABLE student_applications 
    ADD COLUMN IF NOT EXISTS reviewed_by VARCHAR(100),
    ADD COLUMN IF NOT EXISTS submission_count INT DEFAULT 1");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin_student_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL,
    message_type ENUM('general', 'application_feedback', 'urgent') DEFAULT 'general',
    subject VARCHAR(255),
    message TEXT,
    sent_by VARCHAR(100) DEFAULT 'Admin',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS student_admin_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    attachment VARCHAR(255),
    status ENUM('open', 'replied', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS application_custom_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL,
    field_name VARCHAR(255) NOT NULL,
    field_value TEXT,
    file_path VARCHAR(255),
    UNIQUE KEY unique_student_field (student_email, field_name)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS student_to_admin_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL,
    application_id INT,
    subject VARCHAR(255),
    message TEXT,
    attachment VARCHAR(255),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT 0,
    admin_reply TEXT,
    replied_at TIMESTAMP NULL
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS application_additional_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(255) NOT NULL,
    application_id INT,
    document_name VARCHAR(255),
    document_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Check login

$student_email = $_SESSION['student_email'] ?? null;
$student_name = $_SESSION['student_name'] ?? 'Student';
if (!$student_email) {
    header('Location: check/user/index.php');
    exit;
}
// Fetch data
$profile_stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE student_email = ?");
$profile_stmt->execute([$student_email]);
$profile = $profile_stmt->fetch();

$app = $pdo->prepare("SELECT * FROM student_applications WHERE student_email = ?");
$app->execute([$student_email]);
$app = $app->fetch();

// Fetch student-specific messages (not school notices)
$messages = $pdo->prepare("SELECT * FROM admin_student_messages WHERE student_email = ? ORDER BY sent_at DESC LIMIT 5");
$messages->execute([$student_email]);
$messages = $messages->fetchAll();

// Fetch custom application form fields
$custom_fields = $pdo->query("SELECT * FROM application_form_fields WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

// Fetch student's messages to admin
$student_messages = $pdo->prepare("SELECT * FROM student_to_admin_messages WHERE student_email = ? ORDER BY sent_at DESC");
$student_messages->execute([$student_email]);
$student_messages = $student_messages->fetchAll();

// Fetch additional documents uploaded by student
$additional_docs = $pdo->prepare("SELECT * FROM application_additional_documents WHERE student_email = ? ORDER BY uploaded_at DESC");
$additional_docs->execute([$student_email]);
$additional_docs = $additional_docs->fetchAll();

// Calculate profile completion
$profile_completion = 0;
if ($profile) {
    $fields = ['full_name', 'dob', 'gender', 'contact', 'address'];
    $completed = 0;
    foreach ($fields as $field) {
        if (!empty($profile[$field])) $completed++;
    }
    $profile_completion = ($completed / count($fields)) * 100;
}

$msg = $_SESSION['login_message'] ?? '';
$error_msg = $_SESSION['error_message'] ?? '';
unset($_SESSION['login_message'], $_SESSION['error_message']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['save_profile']) || isset($_POST['update_profile']))) {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';
    $profile_photo = $profile['profile_photo'] ?? '';
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $target = $upload_dir . time() . '_' . basename($_FILES['profile_photo']['name']);
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
            $profile_photo = $target;
        }
    }
    $stmt = $pdo->prepare("INSERT INTO student_profiles (student_email, full_name, dob, gender, contact, address, profile_photo) 
                          VALUES (?, ?, ?, ?, ?, ?, ?) 
                          ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), dob=VALUES(dob), gender=VALUES(gender), 
                          contact=VALUES(contact), address=VALUES(address), profile_photo=VALUES(profile_photo)");
    $stmt->execute([$student_email, $full_name, $dob, $gender, $contact, $address, $profile_photo]);
    $_SESSION['login_message'] = '<i class="fas fa-check-circle"></i> Profile saved successfully!';
    header('Location: student_dashboard.php');
    exit;
}

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_app'])) {
    // Check if student can reapply after rejection
    $can_reapply = false;
    if ($app && $app['status'] === 'rejected') {
        $submission_count = $app['submission_count'] ?? 1;
        if ($submission_count < 4) {
            $can_reapply = true;
            // Delete old application to allow resubmission
            $pdo->prepare("DELETE FROM student_applications WHERE student_email = ?")->execute([$student_email]);
            $app = null;
        } else {
            $_SESSION['error_message'] = 'Maximum resubmission limit reached (4 submissions total).';
            header('Location: student_dashboard.php');
            exit;
        }
    }
    
    if ($app && !$can_reapply) {
        $_SESSION['error_message'] = 'You have already submitted an application.';
    } elseif ($profile_completion < 100) {
        $_SESSION['error_message'] = 'Please complete your profile (100%) before submitting an application.';
    } else {
        $father_name = trim($_POST['father_name'] ?? '');
        $father_contact = $_POST['father_contact'] ?? '';
        $mother_name = trim($_POST['mother_name'] ?? '');
        $mother_contact = $_POST['mother_contact'] ?? '';
        $emergency_contact = $_POST['emergency_contact'] ?? '';
        $medical_info = trim($_POST['medical_info'] ?? '');
        
        $id_document = '';
        if (isset($_FILES['id_document']) && $_FILES['id_document']['size'] > 0) {
            $upload_dir = 'uploads/documents/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $target = $upload_dir . time() . '_id_' . basename($_FILES['id_document']['name']);
            if (move_uploaded_file($_FILES['id_document']['tmp_name'], $target)) {
                $id_document = $target;
            }
        }
        
        $certificates = [];
        if (isset($_FILES['certificates'])) {
            $upload_dir = 'uploads/certificates/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            foreach ($_FILES['certificates']['tmp_name'] as $i => $tmp_name) {
                if ($_FILES['certificates']['size'][$i] > 0) {
                    $target = $upload_dir . time() . '_cert_' . basename($_FILES['certificates']['name'][$i]);
                    if (move_uploaded_file($tmp_name, $target)) {
                        $certificates[] = $target;
                    }
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO student_applications 
                              (student_email, father_name, father_contact, mother_name, mother_contact, emergency_contact, id_document, certificates, medical_info, submission_count) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $new_submission_count = ($can_reapply ? ($app['submission_count'] ?? 1) + 1 : 1);
        $stmt->execute([$student_email, $father_name, $father_contact, $mother_name, $mother_contact, $emergency_contact, $id_document, json_encode($certificates), $medical_info, $new_submission_count]);
        
        // Handle custom fields
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'custom_') === 0) {
                $field_name = substr($key, 7); // Remove 'custom_' prefix
                $field_value = trim($value);
                $file_path = '';
                
                // Check if it's a file upload field
                if (isset($_FILES[$key]) && $_FILES[$key]['size'] > 0) {
                    $upload_dir = 'uploads/custom_fields/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $target = $upload_dir . time() . '_' . basename($_FILES[$key]['name']);
                    if (move_uploaded_file($_FILES[$key]['tmp_name'], $target)) {
                        $file_path = $target;
                    }
                }
                
                // Save to database
                $stmt = $pdo->prepare("INSERT INTO application_custom_data (student_email, field_name, field_value, file_path) 
                                      VALUES (?, ?, ?, ?) 
                                      ON DUPLICATE KEY UPDATE field_value=VALUES(field_value), file_path=VALUES(file_path)");
                $stmt->execute([$student_email, $field_name, $field_value, $file_path]);
            }
        }
        
        $_SESSION['login_message'] = '<i class="fas fa-check-circle"></i> Application submitted successfully!';
        header('Location: student_dashboard.php');
        exit;
    }
    header('Location: student_dashboard.php');
    exit;
}

// Handle send message to admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message_to_admin'])) {
    $subject = trim($_POST['message_subject'] ?? '');
    $message = trim($_POST['message_content'] ?? '');
    $attachment = '';
    
    if (isset($_FILES['message_attachment']) && $_FILES['message_attachment']['size'] > 0) {
        $upload_dir = 'uploads/student_messages/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $target = $upload_dir . time() . '_' . basename($_FILES['message_attachment']['name']);
        if (move_uploaded_file($_FILES['message_attachment']['tmp_name'], $target)) {
            $attachment = $target;
        }
    }
    
    $app_id = $app['id'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO student_to_admin_messages (student_email, application_id, subject, message, attachment) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$student_email, $app_id, $subject, $message, $attachment]);
    
    $_SESSION['login_message'] = 'Message sent to admin successfully!';
    header('Location: student_dashboard.php');
    exit;
}

// Handle upload additional document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_additional_doc'])) {
    if (isset($_FILES['additional_document']) && $_FILES['additional_document']['size'] > 0) {
        $upload_dir = 'uploads/additional_documents/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $doc_name = $_POST['document_name'] ?? 'Additional Document';
        $target = $upload_dir . time() . '_' . basename($_FILES['additional_document']['name']);
        if (move_uploaded_file($_FILES['additional_document']['tmp_name'], $target)) {
            $app_id = $app['id'] ?? null;
            $stmt = $pdo->prepare("INSERT INTO application_additional_documents (student_email, application_id, document_name, document_path) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_email, $app_id, $doc_name, $target]);
            $_SESSION['login_message'] = 'Document uploaded successfully!';
        }
    }
    header('Location: student_dashboard.php');
    exit;
}

// Handle contact admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_admin'])) {
    $subject = trim($_POST['inquiry_subject'] ?? '');
    $message = trim($_POST['inquiry_message'] ?? '');
    
    $attachment = '';
    if (isset($_FILES['inquiry_attachment']) && $_FILES['inquiry_attachment']['size'] > 0) {
        $upload_dir = 'uploads/inquiries/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $target = $upload_dir . time() . '_' . basename($_FILES['inquiry_attachment']['name']);
        if (move_uploaded_file($_FILES['inquiry_attachment']['tmp_name'], $target)) {
            $attachment = $target;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO student_admin_inquiries (student_email, subject, message, attachment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$student_email, $subject, $message, $attachment]);
    
    $_SESSION['login_message'] = '<i class="fas fa-paper-plane"></i> Your message has been sent to the admin!';
    header('Location: student_dashboard.php');
    exit;
}

// Status colors
function getStatusColor($status) {
    $colors = [
        'pending' => '#FF9933',
        'under_review' => '#4A90E2',
        'approved' => '#28a745',
        'rejected' => '#dc3545',
        'revision_required' => '#4A90E2'
    ];
    return $colors[$status] ?? '#6c757d';
}

function getStatusIcon($status) {
    $icons = [
        'pending' => 'clock',
        'under_review' => 'eye',
        'approved' => 'check-circle',
        'rejected' => 'times-circle',
        'revision_required' => 'eye'
    ];
    return $icons[$status] ?? 'question-circle';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="check/user/style.css">
    <style>
        :root {
            --primary-color: #1E2A44;
            --accent-color: #FF9933;
            --danger-color: #D32F2F;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --info-color: #4A90E2;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(30, 42, 68, 0.08);
            --card-hover-shadow: 0 8px 24px rgba(30, 42, 68, 0.12);
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f5f7fa;
            min-height: 100vh;
            padding-top: 100px;
            padding-bottom: 40px;
        }
        
        .dashboard-container {
            max-width: 100%;
            margin: 0;
            padding: 0 40px;
        }
        
        .welcome-header {
            background: white;
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .welcome-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-color);
        }
        
        .welcome-text h2 {
            color: var(--primary-color);
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        
        .welcome-text p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 13px;
        }
        
        .progress-card {
            background: linear-gradient(135deg, var(--accent-color), #FFB84D);
            color: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
        }
        
        .progress-card h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .progress-bar-custom {
            background: rgba(255, 255, 255, 0.3);
            height: 12px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: white;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        
        .progress-text {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .card-custom {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
        }
        
        .card-custom:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-4px);
        }
        
        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .card-header-custom i {
            font-size: 24px;
            color: var(--accent-color);
        }
        
        .card-header-custom h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .form-label-custom {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }
        
        .form-control-custom {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s;
        }
        
        .form-control-custom:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 153, 51, 0.15);
            outline: none;
        }
        
        .btn-custom {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: #2a3f5f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 42, 68, 0.3);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            color: white;
        }
        
        .message-card {
            background: #f8f9fa;
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .message-card.urgent {
            border-left-color: var(--danger-color);
            background: #fff5f5;
        }
        
        .message-card h5 {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }
        
        .message-card p {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
        }
        
        .message-card small {
            font-size: 11px;
            color: #999;
        }
        
        .alert-custom {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-success-custom {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }
        
        .alert-danger-custom {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
        }
        
        .locked-card {
            position: relative;
        }
        
        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            z-index: 10;
        }
        
        .locked-message {
            text-align: center;
            padding: 30px;
        }
        
        .locked-message i {
            font-size: 48px;
            color: var(--warning-color);
            margin-bottom: 15px;
        }
        
        .locked-message h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 80px;
            }
            
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }
            
            .welcome-info {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar_snippet.php'; ?>
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-info">
                <?php if ($profile && $profile['profile_photo']): ?>
                    <img src="<?= htmlspecialchars($profile['profile_photo']) ?>" alt="Profile" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar" style="background: var(--accent-color); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: 700;">
                        <?= strtoupper(substr($student_name, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="welcome-text">
                    <h2>Welcome back, <?= htmlspecialchars($student_name) ?>!</h2>
                    <p><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($student_email) ?></p>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($msg): ?>
            <div class="alert-custom alert-success-custom"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert-custom alert-danger-custom"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-user-circle"></i>
                        <h3>My Profile</h3>
                    </div>
                    <form method="post" enctype="multipart/form-data" id="profileForm">
                        <div class="mb-3">
                            <label class="form-label-custom">Full Name *</label>
                            <input type="text" name="full_name" class="form-control form-control-custom" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required <?= $profile ? 'readonly' : '' ?> >
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control form-control-custom" value="<?= htmlspecialchars($profile['dob'] ?? '') ?>" required <?= $profile ? 'readonly' : '' ?> >
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Gender *</label>
                            <select name="gender" class="form-control form-control-custom" required <?= $profile ? 'disabled' : '' ?> >
                                <option value="">Select</option>
                                <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Contact Number *</label>
                            <input type="tel" name="contact" class="form-control form-control-custom" value="<?= htmlspecialchars($profile['contact'] ?? '') ?>" required <?= $profile ? 'readonly' : '' ?> >
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Address *</label>
                            <textarea name="address" class="form-control form-control-custom" rows="3" required <?= $profile ? 'readonly' : '' ?> ><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Profile Photo (Optional)</label>
                            <input type="file" name="profile_photo" class="form-control form-control-custom" accept="image/*" <?= $profile ? 'disabled' : '' ?> >
                        </div>
                        <?php if (!$profile): ?>
                            <button type="submit" name="save_profile" class="btn btn-custom btn-primary-custom w-100" id="saveBtn">
                                <i class="fas fa-save me-2"></i>Save Profile
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-warning w-100" id="editBtn"><i class="fas fa-edit me-2"></i>Edit</button>
                            <button type="submit" name="update_profile" class="btn btn-success w-100 d-none" id="updateBtn"><i class="fas fa-save me-2"></i>Update</button>
                        <?php endif; ?>
                    </form>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var editBtn = document.getElementById('editBtn');
                        var updateBtn = document.getElementById('updateBtn');
                        var form = document.getElementById('profileForm');
                        if (editBtn) {
                            editBtn.addEventListener('click', function() {
                                form.querySelectorAll('input, textarea, select').forEach(function(el) {
                                    if (el.name !== 'email' && el.type !== 'hidden') {
                                        el.removeAttribute('readonly');
                                        el.removeAttribute('disabled');
                                    }
                                });
                                editBtn.classList.add('d-none');
                                updateBtn.classList.remove('d-none');
                            });
                        }
                    });
                    </script>
                </div>
            </div>

            <!-- Application Card -->
            <div class="col-md-6">
                <div class="card-custom <?= $profile_completion < 100 ? 'locked-card' : '' ?>">
                    <?php if ($profile_completion < 100): ?>
                        <div class="locked-overlay">
                            <div class="locked-message">
                                <i class="fas fa-lock"></i>
                                <h4>Complete Your Profile First</h4>
                                <p>You need to complete your profile (100%) before submitting an application.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$app): ?>
                        <div class="card-header-custom">
                            <i class="fas fa-file-alt"></i>
                            <h3>Submit Application</h3>
                        </div>
                        <!-- Application Form -->
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label-custom">Father's Name *</label>
                                <input type="text" name="father_name" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Father's Contact *</label>
                                <input type="tel" name="father_contact" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Mother's Name *</label>
                                <input type="text" name="mother_name" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Mother's Contact *</label>
                                <input type="tel" name="mother_contact" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Emergency Contact *</label>
                                <input type="tel" name="emergency_contact" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">ID Document (Upload) *</label>
                                <input type="file" name="id_document" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Certificates (Multiple files)</label>
                                <input type="file" name="certificates[]" class="form-control form-control-custom" multiple>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Medical Information (Optional)</label>
                                <textarea name="medical_info" class="form-control form-control-custom" rows="3"></textarea>
                            </div>
                            
                            <?php if (!empty($custom_fields)): ?>
                                <hr class="my-4">
                                <h5 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Additional Information</h5>
                                <?php foreach ($custom_fields as $field): ?>
                                    <div class="mb-3">
                                        <label class="form-label-custom">
                                            <?= htmlspecialchars($field['field_label']) ?>
                                            <?= $field['is_required'] ? '*' : '' ?>
                                        </label>
                                        
                                        <?php if ($field['field_type'] === 'textarea'): ?>
                                            <textarea 
                                                name="custom_<?= htmlspecialchars($field['field_name']) ?>" 
                                                class="form-control form-control-custom" 
                                                rows="3"
                                                <?= $field['is_required'] ? 'required' : '' ?>
                                            ></textarea>
                                            
                                        <?php elseif ($field['field_type'] === 'file'): ?>
                                            <input 
                                                type="file" 
                                                name="custom_<?= htmlspecialchars($field['field_name']) ?>" 
                                                class="form-control form-control-custom"
                                                <?= $field['is_required'] ? 'required' : '' ?>
                                            >
                                            
                                        <?php elseif ($field['field_type'] === 'select'): ?>
                                            <select 
                                                name="custom_<?= htmlspecialchars($field['field_name']) ?>" 
                                                class="form-control form-control-custom"
                                                <?= $field['is_required'] ? 'required' : '' ?>
                                            >
                                                <option value="">-- Select --</option>
                                                <?php 
                                                $options = explode(',', $field['field_options']);
                                                foreach ($options as $option): 
                                                    $option = trim($option);
                                                ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            
                                        <?php else: ?>
                                            <input 
                                                type="<?= htmlspecialchars($field['field_type']) ?>" 
                                                name="custom_<?= htmlspecialchars($field['field_name']) ?>" 
                                                class="form-control form-control-custom"
                                                <?= $field['is_required'] ? 'required' : '' ?>
                                            >
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <button type="submit" name="submit_app" class="btn btn-custom btn-primary-custom w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="card-header-custom">
                            <i class="fas fa-file-alt"></i>
                            <h3>Application Status</h3>
                        </div>
                        <!-- Application Submitted - Show Details -->
                        <div class="mb-4">
                            <p class="text-muted"><strong>Application Submitted:</strong> <?= date('M d, Y h:i A', strtotime($app['submitted_at'])) ?></p>
                            
                            <?php if ($app['admin_message']): ?>
                                <div class="message-card <?= $app['status'] === 'rejected' ? 'urgent' : '' ?> mb-3">
                                    <h5><i class="fas fa-comment-dots me-2"></i>Admin Feedback</h5>
                                    <p><?= nl2br(htmlspecialchars($app['admin_message'])) ?></p>
                                    <small><?= $app['status_updated_at'] ? date('M d, Y h:i A', strtotime($app['status_updated_at'])) : '' ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Application Status at Bottom -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Application Status</h5>
                            <div class="text-center mb-3">
                                <div class="status-badge" style="background: <?= getStatusColor($app['status']) ?>;">
                                    <i class="fas fa-<?= getStatusIcon($app['status']) ?>"></i>
                                    <?= $app['status'] === 'revision_required' ? 'Under Review' : ucwords(str_replace('_', ' ', $app['status'])) ?>
                                </div>
                            </div>
                            
                            <?php if ($app['status'] === 'approved'): ?>
                                <div class="alert-custom alert-success-custom">
                                    <i class="fas fa-check-circle me-2"></i>Congratulations! Your application has been approved.
                                </div>
                            <?php elseif ($app['status'] === 'rejected'): ?>
                                <div class="alert-custom alert-danger-custom">
                                    <i class="fas fa-times-circle me-2"></i>Your application was not approved. Please see admin feedback above.
                                </div>
                            <?php elseif ($app['status'] === 'under_review' || $app['status'] === 'revision_required'): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-eye me-2"></i>Your application is currently under review by admin.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Send Message to Admin Section - Hidden when approved -->
                        <?php if ($app['status'] !== 'approved'): ?>
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3">Send Message to Admin</h5>
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label-custom">Subject *</label>
                                    <input type="text" name="message_subject" class="form-control form-control-custom" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-custom">Message *</label>
                                    <textarea name="message_content" class="form-control form-control-custom" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-custom">Attachment (Optional)</label>
                                    <input type="file" name="message_attachment" class="form-control form-control-custom">
                                </div>
                                <button type="submit" name="send_message_to_admin" class="btn btn-custom btn-primary-custom w-100">
                                    Send Message
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Upload Additional Documents - Hidden when approved -->
                        <?php if ($app['status'] !== 'approved'): ?>
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3">Upload Additional Documents</h5>
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label-custom">Document Name *</label>
                                    <input type="text" name="document_name" class="form-control form-control-custom" placeholder="e.g., Updated Certificate" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-custom">Document File *</label>
                                    <input type="file" name="additional_document" class="form-control form-control-custom" required>
                                </div>
                                <button type="submit" name="upload_additional_doc" class="btn btn-custom btn-primary-custom w-100">
                                    Upload Document
                                </button>
                            </form>
                            
                            <?php if (!empty($additional_docs)): ?>
                                <div class="mt-3">
                                    <h6>Your Uploaded Documents:</h6>
                                    <?php foreach ($additional_docs as $doc): ?>
                                        <div class="message-card">
                                            <strong><?= htmlspecialchars($doc['document_name']) ?></strong>
                                            <br><small>Uploaded: <?= date('M d, Y h:i A', strtotime($doc['uploaded_at'])) ?></small>
                                            <br><a href="<?= htmlspecialchars($doc['document_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">View Document</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Reapply Section (if rejected) -->
                        <?php if ($app['status'] === 'rejected'): ?>
                            <?php $submission_count = $app['submission_count'] ?? 1; ?>
                            <?php if ($submission_count < 4): ?>
                                <div class="mt-4 pt-3 border-top">
                                    <div class="alert alert-warning">
                                        <strong>You can reapply!</strong> You have <?= (4 - $submission_count) ?> more chance(s) to submit your application.
                                        <br><small>Total submissions: <?= $submission_count ?> / 4</small>
                                    </div>
                                    <form method="post">
                                        <button type="submit" name="submit_app" class="btn btn-warning w-100">
                                            Reapply Now
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="mt-4 pt-3 border-top">
                                    <div class="alert alert-danger">
                                        Maximum resubmission limit reached (4 submissions total).
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Admin Messages Only -->
        <div class="row">
            <!-- Messages from Admin -->
            <div class="col-md-12">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-bell"></i>
                        <h3>Messages from Admin</h3>
                    </div>
                    <?php if (count($messages) > 0): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card <?= $message['message_type'] === 'urgent' ? 'urgent' : '' ?>">
                                <h5>
                                    <?php if ($message['message_type'] === 'urgent'): ?>
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($message['subject']) ?>
                                </h5>
                                <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                <small><?= date('M d, Y h:i A', strtotime($message['sent_at'])) ?> by <?= htmlspecialchars($message['sent_by']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-inbox fa-3x mb-3" style="color: #e0e0e0;"></i><br>
                            No messages yet.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
