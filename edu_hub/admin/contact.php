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
    <title>Contact Management | Telangana School/College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f7f7fa 0%, #ffffff 100%);
            font-family: 'Open Sans', sans-serif;
        }
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(30,42,68,0.10);
            padding: 32px 24px 32px 24px;
        }
        .admin-header {
            background: linear-gradient(135deg, #1E2A44 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 15px 15px 0 0;
            margin-bottom: 32px;
        }
        .back-btn {
            margin: 24px 0 0 24px;
        }
        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #1E2A44;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .contact-card, .contact-form-card {
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(30,42,68,0.08);
            background: #fff;
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
        }
        .form-label, .contact-label {
            font-weight: 600;
            color: #1E2A44;
        }
        .btn-primary { background: #1E2A44; border: none; }
        .btn-primary:hover { background: #16305a; }
        .alert { border-radius: 8px; }
        .max-h-300 { max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-secondary back-btn">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="mb-0">Contact Management</h1>
            <p class="mb-0">Manage contact page information and user queries</p>
        </div>
        <div class="row g-5 align-items-stretch">
            <div class="col-md-6">
                <div class="contact-card">
                    <h2 class="section-title">Edit Contact Details</h2>
                    <?php if ($message): ?>
                        <div class="alert alert-success mb-3"> <?= htmlspecialchars($message) ?> </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3"> <?= htmlspecialchars($error) ?> </div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($contact_data['address'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($contact_data['phone'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($contact_data['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Office Hours</label>
                            <textarea name="office_hours" class="form-control" rows="3"><?= htmlspecialchars($contact_data['office_hours'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Google Maps Embed Code</label>
                            <textarea name="map_embed" class="form-control" rows="3" placeholder="Paste Google Maps embed iframe code here"><?= htmlspecialchars($contact_data['map_embed'] ?? '') ?></textarea>
                            <small class="text-muted">Paste the iframe code from Google Maps here.</small>
                        </div>
                        <button type="submit" name="update_contact" class="btn btn-primary w-100">Update Details</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-form-card">
                    <h2 class="section-title">Active Messages & Queries</h2>
                    <?php if (empty($active_messages)): ?>
                        <div class="alert alert-info">No active messages.</div>
                    <?php else: ?>
                        <div class="max-h-300">
                            <?php foreach ($active_messages as $msg): ?>
                                <div class="p-3 mb-3 bg-light rounded shadow-sm">
                                    <div class="mb-1"><span class="contact-label">Name:</span> <?= htmlspecialchars($msg['name']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Email:</span> <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-primary text-decoration-underline"><?= htmlspecialchars($msg['email']) ?></a></div>
                                    <div class="mb-1"><span class="contact-label">Subject:</span> <?= htmlspecialchars($msg['subject']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Message:</span> <?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                    <div class="mb-1 text-muted small">Date: <?= htmlspecialchars($msg['created_at']) ?></div>
                                    <form method="post" class="mt-2">
                                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                        <input type="hidden" name="to_email" value="<?= htmlspecialchars($msg['email']) ?>">
                                        <div class="mb-2">
                                            <input type="text" name="reply_subject" class="form-control" placeholder="Subject" value="Re: <?= htmlspecialchars($msg['subject']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="reply_message" class="form-control" rows="3" placeholder="Your Reply"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Send Reply</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="section-title mt-5">Resolved Messages</h2>
                    <?php if (empty($resolved_messages)): ?>
                        <div class="alert alert-info">No resolved messages.</div>
                    <?php else: ?>
                        <div class="max-h-300">
                            <?php foreach ($resolved_messages as $msg): ?>
                                <div class="p-3 mb-3 bg-light rounded shadow-sm">
                                    <div class="mb-1"><span class="contact-label">Name:</span> <?= htmlspecialchars($msg['name']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Email:</span> <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-primary text-decoration-underline"><?= htmlspecialchars($msg['email']) ?></a></div>
                                    <div class="mb-1"><span class="contact-label">Subject:</span> <?= htmlspecialchars($msg['subject']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Message:</span> <?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                    <div class="mb-1 text-muted small">Date: <?= htmlspecialchars($msg['created_at']) ?></div>
                                    <div class="mb-1"><span class="contact-label">Reply:</span> <?= nl2br(htmlspecialchars($msg['reply_text'])) ?></div>
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