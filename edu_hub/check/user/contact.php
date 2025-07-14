<?php
// Connect to the same DB as admin
$host = 'localhost';
$db   = 'school_cms_system';
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
$footer_data = [];
if ($pdo) {
    $result = $pdo->query("SELECT section, content FROM footer_content");
    while ($row = $result->fetch()) {
        $footer_data[$row['section']] = $row['content'];
    }
}
// Fetch school info from homepage_content
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();
// Fetch contact info from school_config
$school_address = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_address'")->fetchColumn();
$school_phone = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_phone'")->fetchColumn();
$school_email = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_email'")->fetchColumn();
$office_hours = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'office_hours'")->fetchColumn();
$google_maps = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'google_maps'")->fetchColumn();
$school_logo = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_logo'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Xavier's College - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e6ecf4 0%, #c9d6e8 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .header-crest {
            width: 80px;
            height: 80px;
            background: url('../images/school.png') no-repeat center;
            background-size: contain;
            margin: 0 auto 1rem;
        }
        .contact-section {
            background-color: #ffffff;
            padding: 4rem 3rem;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            margin-top: 3rem;
            transition: transform 0.3s ease;
        }
        .contact-section:hover {
            transform: translateY(-5px);
        }
        h1 {
            color: #1a252f;
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            letter-spacing: 1px;
        }
        .contact-info {
            background: linear-gradient(135deg, #f9fbfd 0%, #eef2f7 100%);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2.5rem;
        }
        .contact-info p {
            color: #2d3e50;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .contact-info i {
            color: #2c82d9;
            font-size: 1.3rem;
            margin-right: 1.2rem;
            transition: color 0.3s ease;
        }
        .contact-info p:hover i {
            color: #1e5ea0;
        }
        .contact-form label {
            color: #1a252f;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .contact-form input, .contact-form textarea {
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            padding: 12px 15px;
            width: 100%;
            margin-bottom: 1.5rem;
            background: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-form input:focus, .contact-form textarea:focus {
            border-color: #2c82d9;
            box-shadow: 0 0 10px rgba(44, 130, 217, 0.2);
            outline: none;
        }
        .contact-form button {
            background: linear-gradient(135deg, #2c82d9 0%, #1e5ea0 100%);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 130, 217, 0.3);
        }
        @media (max-width: 900px) {
            .contact-section { padding: 2.5rem 1.2rem; }
            .contact-info { padding: 1.2rem; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>
    <main style="margin-top: 60px;">
    <section class="contact-section">
        <div class="container mx-auto px-4">
            <div class="header-crest"></div>
            <h1>Contact Us</h1>
            <!-- School Logo and Name -->
            <div class="text-center mb-4">
                <img src="<?= $school_logo ? htmlspecialchars($school_logo) : '../images/school.png' ?>" alt="School Logo" class="logo-img mb-2" style="max-height: 80px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #0002;">
                <h1 class="fw-bold" style="font-size: 2.2rem; color: #1E2A44; letter-spacing: 1px;">
                    <?= htmlspecialchars($school_info['title'] ?? 'Your School Name') ?>
                </h1>
            </div>
            <!-- Contact Details -->
            <div class="contact-details">
                <h3>Contact Details</h3>
                <p><strong>College Address:</strong> <?= htmlspecialchars($school_address) ?></p>
                <p><strong>Phone Number:</strong> <?= htmlspecialchars($school_phone) ?></p>
                <p><strong>Email Address:</strong> <?= htmlspecialchars($school_email) ?></p>
                <p><strong>Office Hours:</strong><br><?= nl2br(htmlspecialchars($office_hours)) ?></p>
            </div>
            <!-- Google Maps Embed -->
            <?php if ($google_maps): ?>
            <div class="google-maps-embed mt-4">
                <?= $google_maps ?>
            </div>
            <?php endif; ?>
            <!-- Contact Information -->
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($footer_data['contact_address'] ?? 'St. Xavier\'s College, 5 Mahapalika Marg, Mumbai, Maharashtra 400001, India') ?></p>
                <p><i class="fas fa-phone"></i> Phone: <?= htmlspecialchars($footer_data['contact_phone'] ?? '+91 22 2262 0662') ?></p>
                <p><i class="fas fa-envelope"></i> Email: <?= htmlspecialchars($footer_data['contact_email'] ?? 'info@stxavierscollege.edu') ?></p>
            </div>
            <!-- Contact Form -->
            <div class="contact-form">
                <form action="#" method="POST">
                    <div class="mb-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </section>
    </main>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 