<?php
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
    die('Database connection failed: ' . $e->getMessage());
}
$footer_data = [];
$result = $pdo->query("SELECT section, content FROM footer_content");
while ($row = $result->fetch()) {
    $footer_data[$row['section']] = $row['content'];
}
?>
<footer class="footer mt-auto py-4 bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Contact Us</h5>
                <p><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($footer_data['contact_email'] ?? 'info@stxaviercollege.in') ?></p>
                <p><i class="fas fa-phone me-2"></i><?= htmlspecialchars($footer_data['contact_phone'] ?? '+91 12345 67890') ?></p>
                <p><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($footer_data['contact_address'] ?? 'Hyderabad, Telangana, India') ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Follow Us</h5>
                <a href="<?= htmlspecialchars($footer_data['facebook_link'] ?? '#') ?>" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="<?= htmlspecialchars($footer_data['twitter_link'] ?? '#') ?>" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="<?= htmlspecialchars($footer_data['linkedin_link'] ?? '#') ?>" class="text-white me-3"><i class="fab fa-linkedin fa-lg"></i></a>
            </div>
            <div class="col-md-4 mb-3 text-end">
                <h5>&nbsp;</h5>
                <p class="mb-0"><i class="fas fa-copyright me-2"></i><?= htmlspecialchars($footer_data['copyright_text'] ?? '© 2025 St. Xavier\'s College. All rights reserved.') ?></p>
            </div>
        </div>
    </div>
</footer> 