<?php
session_start();
// Connect to the same DB as admin
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
    $pdo = null;
}
// Fetch school info from homepage manager
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();
$school_name = $school_info['title'] ?? 'Your School Name';
$school_tagline = $school_info['content'] ?? '';
// Fetch contact info from contact_info table
$contact_data = [];
if ($pdo) {
    $result = $pdo->query("SELECT field, value FROM contact_info");
    while ($row = $result->fetch()) {
        $contact_data[$row['field']] = $row['value'];
    }
}
$address = $contact_data['address'] ?? '';
$phone = $contact_data['phone'] ?? '';
$email = $contact_data['email'] ?? '';
$office_hours = $contact_data['office_hours'] ?? '';
$map_embed = $contact_data['map_embed'] ?? '';

// Handle contact form submission
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $success_message = 'Your message has been sent successfully!';
        } else {
            $error_message = 'There was an error sending your message. Please try again later.';
        }
    } else {
        $error_message = 'Please fill in all fields.';
    }
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
    <title>Contact Us - <?= htmlspecialchars($school_name) ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    <!-- Bootstrap for existing content -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/2026/edu_hub/edu_hub/assets/css/gov-theme.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'hsl(120, 61%, 34%)',
                        'primary-foreground': 'hsl(0, 0%, 100%)',
                        'gov-green': 'hsl(120, 61%, 28%)',
                        background: 'hsl(0, 0%, 100%)',
                        foreground: 'hsl(0, 0%, 15%)',
                        border: 'hsl(0, 0%, 90%)',
                        peach: 'hsl(25, 100%, 94%)',
                        saffron: 'hsl(25, 95%, 53%)',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Roboto', 'Noto Sans Telugu', 'Open Sans', Arial, sans-serif;
            background: #f7f8fa;
        }
        
        /* Contact page specific styles */
        .contact-section { margin-top: 90px; margin-bottom: 40px; }
        .contact-card { border-radius: 18px; box-shadow: 0 4px 24px rgba(30,42,68,0.08); }
        .contact-form-card { border-radius: 18px; box-shadow: 0 4px 24px rgba(30,42,68,0.10); }
        .form-control:focus { box-shadow: 0 0 0 2px #1E2A4422; border-color: #1E2A44; }
        .btn-primary { background: #1E2A44; border: none; }
        .btn-primary:hover { background: #16305a; }
        .contact-label { font-weight: 600; }
        .contact-link { color: #1E2A44; font-weight: 500; }
        .contact-link:hover { color: #D32F2F; text-decoration: underline; }
        .map-responsive { overflow: hidden; padding-bottom: 56.25%; position: relative; height: 0; border-radius: 12px; }
        .map-responsive iframe { left: 0; top: 0; height: 100%; width: 100%; position: absolute; border-radius: 12px; }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header_navbar.php'; ?>

<main class="container contact-section" style="margin-top: 0;">
        <div class="row g-5 align-items-stretch">
            <!-- Map and Contact Details -->
            <div class="col-md-6">
                <div class="contact-card bg-white p-4 mb-4">
                    <h2 class="h4 mb-4"><i class="fas fa-map-marker-alt me-2"></i>Our Location</h2>
                    <?php if ($map_embed): ?>
                        <div class="map-responsive mb-4">
                            <?= $map_embed ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="h4 mb-3"><i class="fas fa-phone-alt me-2"></i>Get in Touch</h2>
                    <p class="mb-2">We'd love to hear from you! Reach out with any questions or inquiries.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="contact-label">Address:</span><br><?= htmlspecialchars($address) ?></li>
                        <li class="mb-2"><span class="contact-label">Phone:</span><br><a href="tel:<?= htmlspecialchars($phone) ?>" class="contact-link"><?= htmlspecialchars($phone) ?></a></li>
                        <li class="mb-2"><span class="contact-label">Email:</span><br><a href="mailto:<?= htmlspecialchars($email) ?>" class="contact-link"><?= htmlspecialchars($email) ?></a></li>
                        <li><span class="contact-label">Office Hours:</span><br><?= nl2br(htmlspecialchars($office_hours)) ?></li>
                    </ul>
                </div>
            </div>
            <!-- Contact Form -->
            <div class="col-md-6">
                <div class="contact-form-card bg-white p-4 h-100 d-flex flex-column justify-content-center">
                    <h2 class="h4 mb-4 text-center">Send Us a Message</h2>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php elseif ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
    lucide.createIcons();
</script>

</body>
</html> 