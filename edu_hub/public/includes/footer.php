<?php
// Footer - Styled for Government School Website
// Connects to database for dynamic content if not already connected
if (!isset($pdo)) {
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
}

// Fetch footer data
$footer_data = [];
$school_config = null;
if ($pdo) {
    try {
        $result = $pdo->query("SELECT section, content FROM footer_content");
        while ($row = $result->fetch()) {
            $footer_data[$row['section']] = $row['content'];
        }
        $school_config = $pdo->query("SELECT * FROM school_config WHERE id = 1")->fetch();
    } catch (Exception $e) {
        // Keep defaults
    }
}

// Default values
$school_name = $school_config['school_name_english'] ?? 'ZPHS, Bommalaramaram';
$school_subtitle = $school_config['school_name_subtitle'] ?? 'Zilla Parishad High School';
$footer_description = $school_config['footer_description'] ?? 'A Government of Telangana institution dedicated to providing quality education and holistic development for all students.';
$contact_email = $footer_data['contact_email'] ?? 'zphs.bommalaramaram@telangana.gov.in';
$contact_phone = $footer_data['contact_phone'] ?? '+91 XXXXX XXXXX';
$contact_address = $footer_data['contact_address'] ?? 'Bommalaramaram, Yadadri Bhuvanagiri District, Telangana';
$copyright_text = $footer_data['copyright_text'] ?? '© ' . date('Y') . ' ' . $school_name . '. All rights reserved.';

$social_facebook = $school_config['social_facebook'] ?? '#';
$social_twitter = $school_config['social_twitter'] ?? '#';
$social_instagram = $school_config['social_instagram'] ?? '#';
$social_youtube = $school_config['social_youtube'] ?? '#';

// Quick Links from database or defaults
$quick_links = [];
for ($i = 1; $i <= 6; $i++) {
    $text = $footer_data["quick_link_{$i}_text"] ?? '';
    $url = $footer_data["quick_link_{$i}_url"] ?? '';
    if (!empty($text) && !empty($url)) {
        $quick_links[] = ['text' => $text, 'url' => $url];
    }
}
// Default quick links if none in database
if (empty($quick_links)) {
    $quick_links = [
        ['text' => 'Home', 'url' => '/2026/edu_hub/edu_hub/public/index.php'],
        ['text' => 'About Us', 'url' => '/2026/edu_hub/edu_hub/public/about.php'],
        ['text' => 'Gallery', 'url' => '/2026/edu_hub/edu_hub/public/gallery.php'],
        ['text' => 'Notices', 'url' => '/2026/edu_hub/edu_hub/public/notices.php'],
        ['text' => 'Contact Us', 'url' => '/2026/edu_hub/edu_hub/public/contact.php']
    ];
}

// Footer emblem (use school config or default)
$footer_emblem = $school_config['emblem_left_1'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Telangana_State_emblem.svg/1200px-Telangana_State_emblem.svg.png';
?>

<!-- ============ FOOTER ============ -->
<footer class="relative overflow-hidden" style="background: linear-gradient(180deg, hsl(120, 50%, 45%) 0%, hsl(120, 61%, 28%) 50%, hsl(120, 70%, 20%) 100%);">
    <!-- Decorative top border -->
    <div class="h-1 bg-gradient-to-r from-green-600 via-orange-500 to-green-600"></div>
    
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- School Info -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <img src="<?= htmlspecialchars($footer_emblem) ?>" 
                             alt="School Emblem" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg"><?= htmlspecialchars($school_name) ?></h3>
                        <p class="text-white/70 text-sm"><?= htmlspecialchars($school_subtitle) ?></p>
                    </div>
                </div>
                <p class="text-white/60 text-sm leading-relaxed">
                    <?= htmlspecialchars($footer_description) ?>
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-semibold text-base mb-4 uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2">
                    <?php foreach ($quick_links as $link): ?>
                    <li><a href="<?= htmlspecialchars($link['url']) ?>" class="text-white/70 hover:text-orange-400 transition-colors text-sm flex items-center gap-2"><span class="w-1 h-1 bg-orange-400 rounded-full"></span> <?= htmlspecialchars($link['text']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-white font-semibold text-base mb-4 uppercase tracking-wider">Contact Us</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-orange-400 mt-1 flex-shrink-0"></i>
                        <span class="text-white/70 text-sm"><?= htmlspecialchars($contact_address) ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-4 h-4 text-orange-400 flex-shrink-0"></i>
                        <span class="text-white/70 text-sm"><?= htmlspecialchars($contact_phone) ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-4 h-4 text-orange-400 flex-shrink-0"></i>
                        <span class="text-white/70 text-sm"><?= htmlspecialchars($contact_email) ?></span>
                    </li>
                </ul>
            </div>
            
            <!-- Connect -->
            <div>
                <h4 class="text-white font-semibold text-base mb-4 uppercase tracking-wider">Connect With Us</h4>
                <div class="flex gap-3 mb-6">
                    <a href="<?= htmlspecialchars($social_facebook) ?>" class="w-10 h-10 rounded-full bg-white/10 hover:bg-orange-500 flex items-center justify-center transition-colors" target="_blank">
                        <i data-lucide="facebook" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="<?= htmlspecialchars($social_twitter) ?>" class="w-10 h-10 rounded-full bg-white/10 hover:bg-orange-500 flex items-center justify-center transition-colors" target="_blank">
                        <i data-lucide="twitter" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="<?= htmlspecialchars($social_instagram) ?>" class="w-10 h-10 rounded-full bg-white/10 hover:bg-orange-500 flex items-center justify-center transition-colors" target="_blank">
                        <i data-lucide="instagram" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="<?= htmlspecialchars($social_youtube) ?>" class="w-10 h-10 rounded-full bg-white/10 hover:bg-orange-500 flex items-center justify-center transition-colors" target="_blank">
                        <i data-lucide="youtube" class="w-5 h-5 text-white"></i>
                    </a>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-white/60 text-xs">Government of Telangana</p>
                    <p class="text-white text-sm font-medium">Department of School Education</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="border-t border-white/10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-white/50 text-sm text-center md:text-left">
                    <?= htmlspecialchars($copyright_text) ?>
                </p>
                <div class="flex items-center gap-4 text-white/50 text-xs">
                    <span>Powered by</span>
                    <span class="text-white/70 font-medium">Digital Telangana</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    // Re-initialize Lucide icons for footer if needed
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script> 