<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update Header/Navbar Configuration
        if (isset($_POST['update_header'])) {
            // Get current values from database first
            $current = $pdo->query("SELECT * FROM school_config WHERE id = 1")->fetch();
            
            // Handle image uploads
            $uploads_dir = __DIR__ . '/../uploads/emblems/';
            if (!file_exists($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }
            
            // Use posted values or keep existing ones if empty
            $emblem_left_1 = !empty($_POST['emblem_left_1']) ? $_POST['emblem_left_1'] : ($current['emblem_left_1'] ?? '');
            $emblem_left_2 = !empty($_POST['emblem_left_2']) ? $_POST['emblem_left_2'] : ($current['emblem_left_2'] ?? '');
            $emblem_right_1 = !empty($_POST['emblem_right_1']) ? $_POST['emblem_right_1'] : ($current['emblem_right_1'] ?? '');
            
            // Handle emblem_left_1 upload
            if (isset($_FILES['emblem_left_1_upload']) && $_FILES['emblem_left_1_upload']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['emblem_left_1_upload']['name'], PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'emblem_left_1_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['emblem_left_1_upload']['tmp_name'], $uploads_dir . $filename)) {
                        $emblem_left_1 = '/2026/edu_hub/edu_hub/uploads/emblems/' . $filename;
                    }
                }
            }
            
            // Handle emblem_left_2 upload
            if (isset($_FILES['emblem_left_2_upload']) && $_FILES['emblem_left_2_upload']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['emblem_left_2_upload']['name'], PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'emblem_left_2_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['emblem_left_2_upload']['tmp_name'], $uploads_dir . $filename)) {
                        $emblem_left_2 = '/2026/edu_hub/edu_hub/uploads/emblems/' . $filename;
                    }
                }
            }
            
            // Handle emblem_right_1 upload
            if (isset($_FILES['emblem_right_1_upload']) && $_FILES['emblem_right_1_upload']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['emblem_right_1_upload']['name'], PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'emblem_right_1_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['emblem_right_1_upload']['tmp_name'], $uploads_dir . $filename)) {
                        $emblem_right_1 = '/2026/edu_hub/edu_hub/uploads/emblems/' . $filename;
                    }
                }
            }
            
            // Handle emblem_right_2 upload
            $emblem_right_2 = !empty($_POST['emblem_right_2']) ? $_POST['emblem_right_2'] : ($current['emblem_right_2'] ?? '');
            if (isset($_FILES['emblem_right_2_upload']) && $_FILES['emblem_right_2_upload']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['emblem_right_2_upload']['name'], PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'emblem_right_2_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['emblem_right_2_upload']['tmp_name'], $uploads_dir . $filename)) {
                        $emblem_right_2 = '/2026/edu_hub/edu_hub/uploads/emblems/' . $filename;
                    }
                }
            }
            
            $stmt = $pdo->prepare("UPDATE school_config SET 
                topbar_telugu_text = ?,
                topbar_telugu_secondary = ?,
                school_name_telugu = ?,
                school_name_english = ?,
                school_name_subtitle = ?,
                emblem_left_1 = ?,
                emblem_left_1_alt = ?,
                emblem_left_2 = ?,
                emblem_left_2_text = ?,
                emblem_left_2_alt = ?,
                emblem_right_1 = ?,
                emblem_right_1_alt = ?,
                emblem_right_2 = ?,
                emblem_right_2_alt = ?,
                emblem_right_2_title = ?,
                emblem_right_2_telugu = ?,
                emblem_right_2_subtitle = ?,
                social_facebook = ?,
                social_twitter = ?,
                social_instagram = ?,
                social_youtube = ?,
                social_linkedin = ?
                WHERE id = 1");
            
            $stmt->execute([
                $_POST['topbar_telugu_text'],
                $_POST['topbar_telugu_secondary'],
                $_POST['school_name_telugu'],
                $_POST['school_name_english'],
                $_POST['school_name_subtitle'],
                $emblem_left_1,
                $_POST['emblem_left_1_alt'],
                $emblem_left_2,
                $_POST['emblem_left_2_text'],
                $_POST['emblem_left_2_alt'],
                $emblem_right_1,
                $_POST['emblem_right_1_alt'],
                $emblem_right_2,
                isset($_POST['emblem_right_2_alt']) ? $_POST['emblem_right_2_alt'] : '',
                $_POST['emblem_right_2_title'],
                $_POST['emblem_right_2_telugu'],
                $_POST['emblem_right_2_subtitle'],
                $_POST['social_facebook'],
                $_POST['social_twitter'],
                $_POST['social_instagram'],
                $_POST['social_youtube'],
                $_POST['social_linkedin']
            ]);
            
            // Log the update for debugging
            error_log("School branding updated: " . date('Y-m-d H:i:s') . " - School Name: " . $_POST['school_name_english']);
            
            // Verify the update by fetching the record
            $verify = $pdo->query("SELECT school_name_telugu, school_name_english, school_name_subtitle FROM school_config WHERE id = 1")->fetch();
            error_log("Verification - Telugu: " . ($verify['school_name_telugu'] ?? 'NULL') . ", English: " . ($verify['school_name_english'] ?? 'NULL') . ", Subtitle: " . ($verify['school_name_subtitle'] ?? 'NULL'));
            
            $message = 'Header and navbar configuration updated successfully! <br><small>Telugu: ' . htmlspecialchars($_POST['school_name_telugu']) . '<br>English: ' . htmlspecialchars($_POST['school_name_english']) . '<br>Subtitle: ' . htmlspecialchars($_POST['school_name_subtitle']) . '</small>';
        }
        
        // Update Footer Configuration
        if (isset($_POST['update_footer'])) {
            // Update school config footer description
            $stmt = $pdo->prepare("UPDATE school_config SET footer_description = ? WHERE id = 1");
            $stmt->execute([$_POST['footer_description']]);
            
            // Update footer content
            $stmt = $pdo->prepare("INSERT INTO footer_content (section, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");
            
            // Contact Info
            $stmt->execute(['contact_address', $_POST['contact_address']]);
            $stmt->execute(['contact_phone', $_POST['contact_phone']]);
            $stmt->execute(['contact_email', $_POST['contact_email']]);
            $stmt->execute(['office_hours', $_POST['office_hours']]);
            $stmt->execute(['copyright_text', $_POST['copyright_text']]);
            
            // Quick Links
            for ($i = 1; $i <= 6; $i++) {
                if (isset($_POST["quick_link_{$i}_text"])) {
                    $stmt->execute(["quick_link_{$i}_text", $_POST["quick_link_{$i}_text"]]);
                    $stmt->execute(["quick_link_{$i}_url", $_POST["quick_link_{$i}_url"]]);
                }
            }
            
            // Important Links
            for ($i = 1; $i <= 4; $i++) {
                if (isset($_POST["important_link_{$i}_text"])) {
                    $stmt->execute(["important_link_{$i}_text", $_POST["important_link_{$i}_text"]]);
                    $stmt->execute(["important_link_{$i}_url", $_POST["important_link_{$i}_url"]]);
                }
            }
            
            $message = 'Footer configuration updated successfully!';
        }
        
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch current configuration
$config = $pdo->query("SELECT * FROM school_config WHERE id = 1")->fetch() ?: [];

// Fetch footer content
$footer_data = [];
$result = $pdo->query("SELECT section, content FROM footer_content");
while ($row = $result->fetch()) {
    $footer_data[$row['section']] = $row['content'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Branding Manager</title>
    <?php include 'includes/admin_styles.php'; ?>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-palette"></i>
                <div class="admin-header-info">
                    <h1>School Branding Manager</h1>
                    <p>Manage school name, emblems, social media, and footer content</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/index.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Site</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i><?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <!-- HEADER & NAVBAR SECTION -->
        <div class="content-section">
            <h3><i class="fas fa-heading"></i> Header & Navbar Configuration</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Top Bar -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-bars me-2"></i>Top Bar</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Telugu Text (Top Bar) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control telugu-input" name="topbar_telugu_text" value="<?= htmlspecialchars($config['topbar_telugu_text'] ?? 'తలగణ పరభతవ') ?>" required>
                                <div class="help-text">Example: తలగణ పరభతవ</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">English Text (Top Bar)</label>
                                <input type="text" class="form-control" name="topbar_telugu_secondary" value="<?= htmlspecialchars($config['topbar_telugu_secondary'] ?? 'Government of Telangana') ?>">
                                <div class="help-text">Example: Government of Telangana</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- School Names -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-graduation-cap me-2"></i>School Names</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">School Name (Telugu) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control telugu-input" name="school_name_telugu" value="<?= htmlspecialchars($config['school_name_telugu'] ?? 'జడపహచఎస, బమమలరమర') ?>" required>
                                <div class="help-text">Example: జడపహచఎస, బమమలరమర</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">School Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="school_name_english" value="<?= htmlspecialchars($config['school_name_english'] ?? 'ZPHS, BOMMALARAMARAM') ?>" required>
                                <div class="help-text">Example: ZPHS, BOMMALARAMARAM</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">School Subtitle</label>
                                <input type="text" class="form-control" name="school_name_subtitle" value="<?= htmlspecialchars($config['school_name_subtitle'] ?? 'Zilla Parishad High School') ?>">
                                <div class="help-text">Example: Zilla Parishad High School</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Header Emblems/Logos -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-image me-2"></i>Header Emblems / Logos</h5>
                    
                    <!-- Left Side Emblems -->
                    <div class="card p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <h6 class="mb-3" style="color: var(--accent-blue);"><i class="fas fa-arrow-left me-2"></i>Left Side Emblems</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Emblem 1</strong> (Telangana State Emblem)</label>
                                    <?php if (!empty($config['emblem_left_1'])): ?>
                                        <img src="<?= htmlspecialchars($config['emblem_left_1']) ?>" class="preview-image d-block mb-2" alt="Preview">
                                    <?php endif; ?>
                                    <input type="file" class="form-control mb-2" name="emblem_left_1_upload" accept="image/*">
                                    <small class="text-muted d-block mb-2">Or paste image URL below:</small>
                                    <input type="text" class="form-control" name="emblem_left_1" value="<?= htmlspecialchars($config['emblem_left_1'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Telangana_State_emblem.svg/1200px-Telangana_State_emblem.svg.png') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Alt Text</label>
                                    <input type="text" class="form-control" name="emblem_left_1_alt" value="<?= htmlspecialchars($config['emblem_left_1_alt'] ?? 'Telangana Emblem') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Emblem 2</strong> (Telangana Rising)</label>
                                    <?php if (!empty($config['emblem_left_2'])): ?>
                                        <img src="<?= htmlspecialchars($config['emblem_left_2']) ?>" class="preview-image d-block mb-2" alt="Preview">
                                    <?php endif; ?>
                                    <input type="file" class="form-control mb-2" name="emblem_left_2_upload" accept="image/*">
                                    <small class="text-muted d-block mb-2">Or paste image URL below:</small>
                                    <input type="text" class="form-control" name="emblem_left_2" value="<?= htmlspecialchars($config['emblem_left_2'] ?? 'https://telangana.gov.in/wp-content/themes/developer/assets/images/ts-rising.png') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Text Below Logo</label>
                                    <input type="text" class="form-control" name="emblem_left_2_text" value="<?= htmlspecialchars($config['emblem_left_2_text'] ?? 'PURE  PURE  RARE') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Alt Text</label>
                                    <input type="text" class="form-control" name="emblem_left_2_alt" value="<?= htmlspecialchars($config['emblem_left_2_alt'] ?? 'Telangana Rising') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Emblems -->
                    <div class="card p-3" style="background: #fffbeb; border: 1px solid #fef3c7;">
                        <h6 class="mb-3" style="color: var(--accent-orange);"><i class="fas fa-arrow-right me-2"></i>Right Side Emblems</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Emblem 1</strong> (Digital India)</label>
                                    <?php if (!empty($config['emblem_right_1'])): ?>
                                        <img src="<?= htmlspecialchars($config['emblem_right_1']) ?>" class="preview-image d-block mb-2" alt="Preview">
                                    <?php endif; ?>
                                    <input type="file" class="form-control mb-2" name="emblem_right_1_upload" accept="image/*">
                                    <small class="text-muted d-block mb-2">Or paste image URL below:</small>
                                    <input type="text" class="form-control" name="emblem_right_1" value="<?= htmlspecialchars($config['emblem_right_1'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Digital_India_logo.png/640px-Digital_India_logo.png') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Alt Text</label>
                                    <input type="text" class="form-control" name="emblem_right_1_alt" value="<?= htmlspecialchars($config['emblem_right_1_alt'] ?? 'Digital India') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12 mb-2">
                                <strong>Emblem 2 (Digital Telangana)</strong>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Image</strong> (Optional - Use image OR text fields below)</label>
                                    <?php if (!empty($config['emblem_right_2'])): ?>
                                        <img src="<?= htmlspecialchars($config['emblem_right_2']) ?>" class="preview-image d-block mb-2" alt="Preview">
                                    <?php endif; ?>
                                    <input type="file" class="form-control mb-2" name="emblem_right_2_upload" accept="image/*">
                                    <small class="text-muted d-block mb-2">Or paste image URL below:</small>
                                    <input type="text" class="form-control" name="emblem_right_2" value="<?= htmlspecialchars($config['emblem_right_2'] ?? '') ?>" placeholder="Leave empty to use text fields">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Alt Text</label>
                                    <input type="text" class="form-control" name="emblem_right_2_alt" value="<?= htmlspecialchars($config['emblem_right_2_alt'] ?? 'Digital Telangana') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Title (English)</label>
                                    <input type="text" class="form-control" name="emblem_right_2_title" value="<?= htmlspecialchars($config['emblem_right_2_title'] ?? 'DIGITAL') ?>">
                                    <div class="help-text">Used if no image uploaded</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Title (Telugu)</label>
                                    <input type="text" class="form-control telugu-input" name="emblem_right_2_telugu" value="<?= htmlspecialchars($config['emblem_right_2_telugu'] ?? 'టలగణ') ?>">
                                    <div class="help-text">Used if no image uploaded</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Subtitle</label>
                                    <input type="text" class="form-control" name="emblem_right_2_subtitle" value="<?= htmlspecialchars($config['emblem_right_2_subtitle'] ?? 'Power To Empower') ?>">
                                    <div class="help-text">Used if no image uploaded</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Social Media -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-share-alt me-2"></i>Social Media Links</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fab fa-facebook me-2" style="color: #1877f2;"></i>Facebook URL</label>
                                <input type="text" class="form-control" name="social_facebook" value="<?= htmlspecialchars($config['social_facebook'] ?? '#') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fab fa-twitter me-2" style="color: #1da1f2;"></i>Twitter URL</label>
                                <input type="text" class="form-control" name="social_twitter" value="<?= htmlspecialchars($config['social_twitter'] ?? '#') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fab fa-instagram me-2" style="color: #e4405f;"></i>Instagram URL</label>
                                <input type="text" class="form-control" name="social_instagram" value="<?= htmlspecialchars($config['social_instagram'] ?? '#') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fab fa-youtube me-2" style="color: #ff0000;"></i>YouTube URL</label>
                                <input type="text" class="form-control" name="social_youtube" value="<?= htmlspecialchars($config['social_youtube'] ?? '#') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><i class="fab fa-linkedin me-2" style="color: #0077b5;"></i>LinkedIn URL</label>
                                <input type="text" class="form-control" name="social_linkedin" value="<?= htmlspecialchars($config['social_linkedin'] ?? '#') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" name="update_header" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save Header & Navbar Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- FOOTER SECTION -->
        <div class="content-section">
            <h3><i class="fas fa-stream" style="color: var(--accent-green);"></i> Footer Configuration</h3>
            
            <form method="POST">
                <!-- Footer Description -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>About Section</h5>
                    <div class="form-group">
                        <label class="form-label">Footer Description</label>
                        <textarea class="form-control" name="footer_description" rows="3"><?= htmlspecialchars($config['footer_description'] ?? 'Zilla Parishad High School committed to providing quality education to students in Telangana.') ?></textarea>
                        <div class="help-text">Short description about the school shown in footer</div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Contact Information -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-address-book me-2"></i>Contact Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                                <textarea class="form-control" name="contact_address" rows="2"><?= htmlspecialchars($footer_data['contact_address'] ?? 'Bommalaramaram, Yadadri Bhuvanagiri District, Telangana - 508126') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone me-2"></i>Phone</label>
                                <input type="text" class="form-control" name="contact_phone" value="<?= htmlspecialchars($footer_data['contact_phone'] ?? '+91 98765 43210') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                                <input type="email" class="form-control" name="contact_email" value="<?= htmlspecialchars($footer_data['contact_email'] ?? 'zphs.bommalaramaram@edu.in') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-clock me-2"></i>Office Hours</label>
                                <input type="text" class="form-control" name="office_hours" value="<?= htmlspecialchars($footer_data['office_hours'] ?? 'Mon - Sat: 9:00 AM - 4:30 PM') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Quick Links -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-link me-2"></i>Quick Links</h5>
                    <div class="row">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card p-3" style="background: #f8fafc;">
                                    <div class="form-group mb-2">
                                        <label class="form-label">Link <?= $i ?> Text</label>
                                        <input type="text" class="form-control form-control-sm" name="quick_link_<?= $i ?>_text" value="<?= htmlspecialchars($footer_data["quick_link_{$i}_text"] ?? '') ?>">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label">Link <?= $i ?> URL</label>
                                        <input type="text" class="form-control form-control-sm" name="quick_link_<?= $i ?>_url" value="<?= htmlspecialchars($footer_data["quick_link_{$i}_url"] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Important Links -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-external-link-alt me-2"></i>Important Links</h5>
                    <div class="row">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card p-3" style="background: #f8fafc;">
                                    <div class="form-group mb-2">
                                        <label class="form-label">Link <?= $i ?> Text</label>
                                        <input type="text" class="form-control form-control-sm" name="important_link_<?= $i ?>_text" value="<?= htmlspecialchars($footer_data["important_link_{$i}_text"] ?? '') ?>">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label">Link <?= $i ?> URL</label>
                                        <input type="text" class="form-control form-control-sm" name="important_link_<?= $i ?>_url" value="<?= htmlspecialchars($footer_data["important_link_{$i}_url"] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Copyright -->
                <div class="mb-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-copyright me-2"></i>Copyright Text</h5>
                    <div class="form-group">
                        <label class="form-label">Copyright Notice</label>
                        <input type="text" class="form-control" name="copyright_text" value="<?= htmlspecialchars($footer_data['copyright_text'] ?? '© 2026 ZPHS Bommalaramaram. All Rights Reserved.') ?>">
                        <div class="help-text">Text displayed at the bottom of the footer</div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" name="update_footer" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Save Footer Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>