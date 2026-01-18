<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Handle contact info update
$message = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_info (field, value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()");
        $stmt->execute(['address', $_POST['address']]);
        $stmt->execute(['phone', $_POST['phone']]);
        $stmt->execute(['email', $_POST['email']]);
        $stmt->execute(['office_hours', $_POST['office_hours']]);
        $stmt->execute(['map_embed', $_POST['map_embed']]);
        $message = 'Contact information updated successfully!';
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
// Handle reply to message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message']) && isset($_POST['message_id'])) {
    $reply_message = trim($_POST['reply_message']);
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $to_email = trim($_POST['to_email'] ?? '');
    $message_id = intval($_POST['message_id']);
    // Optionally, send email here using mail() or PHPMailer
    // mail($to_email, $reply_subject, $reply_message);
    // Mark as replied in DB
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_replied = 1, reply_text = ? WHERE id = ?");
    $stmt->execute([$reply_message, $message_id]);
    $message = 'Reply sent and message marked as resolved.';
    // Optionally, reload to clear POST
    header('Location: contact.php');
    exit;
}
// Fetch contact info
$contact_data = [];
$result = $pdo->query("SELECT field, value FROM contact_info");
while ($row = $result->fetch()) {
    $contact_data[$row['field']] = $row['value'];
}
// Fetch active and resolved messages
$active_messages = $pdo->query("SELECT * FROM contact_messages WHERE is_replied = 0 ORDER BY created_at DESC")->fetchAll();
$resolved_messages = $pdo->query("SELECT * FROM contact_messages WHERE is_replied = 1 ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management - Admin Portal</title>
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .contact-card, .contact-form-card {
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(30,42,68,0.08);
            background: #fff;
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }
        .contact-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        .section-subtitle {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .max-h-300 { max-height: 400px; overflow-y: auto; }
        .message-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <i class="fas fa-envelope"></i>
                <div class="admin-header-info">
                    <h1>Contact Management</h1>
                    <p>Manage contact page information and user queries</p>
                </div>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="../public/contact.php" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Site</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="content-section">
                    <h3><i class="fas fa-edit"></i> Edit Contact Details</h3>
                    <form method="post">
                        <div class="form-group">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($contact_data['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($contact_data['phone'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($contact_data['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Office Hours</label>
                            <textarea name="office_hours" class="form-control" rows="2"><?= htmlspecialchars($contact_data['office_hours'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Google Maps Embed Code</label>
                            <textarea name="map_embed" class="form-control" rows="3" placeholder="Paste Google Maps embed iframe code here"><?= htmlspecialchars($contact_data['map_embed'] ?? '') ?></textarea>
                            <div class="help-text">Paste the iframe code from Google Maps here.</div>
                        </div>
                        <button type="submit" name="update_contact" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Details
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content-section">
                    <h3><i class="fas fa-inbox" style="color: var(--accent-orange);"></i> Active Messages</h3>
                    <?php if (empty($active_messages)): ?>
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No active messages.</div>
                    <?php else: ?>
                        <div class="max-h-300">
                            <?php foreach ($active_messages as $msg): ?>
                                <div class="message-card">
                                    <div class="mb-2"><span class="contact-label">Name:</span> <?= htmlspecialchars($msg['name']) ?></div>
                                    <div class="mb-2"><span class="contact-label">Email:</span> <a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></div>
                                    <div class="mb-2"><span class="contact-label">Subject:</span> <?= htmlspecialchars($msg['subject']) ?></div>
                                    <div class="mb-2"><span class="contact-label">Message:</span> <?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                    <div class="mb-2 text-muted small"><i class="fas fa-clock me-1"></i> <?= htmlspecialchars($msg['created_at']) ?></div>
                                    <form method="post" class="mt-2">
                                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                        <input type="hidden" name="to_email" value="<?= htmlspecialchars($msg['email']) ?>">
                                        <div class="mb-2">
                                            <input type="text" name="reply_subject" class="form-control form-control-sm" placeholder="Subject" value="Re: <?= htmlspecialchars($msg['subject']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="reply_message" class="form-control form-control-sm" rows="2" placeholder="Your Reply"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-reply me-1"></i>Send Reply
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="content-section" style="border-left-color: var(--accent-green);">
                    <h3><i class="fas fa-check-circle" style="color: var(--accent-green);"></i> Resolved Messages</h3>
                    <?php if (empty($resolved_messages)): ?>
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No resolved messages.</div>
                    <?php else: ?>
                        <div class="max-h-300">
                            <?php foreach ($resolved_messages as $msg): ?>
                                <div class="message-card">
                                    <div class="mb-1"><span class="contact-label">Name:</span> <?= htmlspecialchars($msg['name']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Email:</span> <?= htmlspecialchars($msg['email']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Subject:</span> <?= htmlspecialchars($msg['subject']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Message:</span> <?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                    <div class="mb-1 text-muted small"><i class="fas fa-clock me-1"></i> <?= htmlspecialchars($msg['created_at']) ?></div>
                                    <div class="mt-2 p-2 bg-success bg-opacity-10 rounded">
                                        <span class="contact-label text-success"><i class="fas fa-reply me-1"></i>Reply:</span> <?= nl2br(htmlspecialchars($msg['reply_text'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>