<?php
// Fetch navbar data from database with fallbacks
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pdo)) {
    require_once __DIR__ . '/../../admin/includes/db.php';
}

// Enable debug mode - set to false to disable debugging
$debug_mode = false;
$debug_output = [];

try {
    // Fetch school configuration
    $stmt = $pdo->query("SELECT * FROM school_config WHERE id = 1");
    $school_config = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($debug_mode) {
        $debug_output[] = "Database connection: SUCCESS";
        $debug_output[] = "School config fetched: " . ($school_config ? "YES" : "NO");
        if ($school_config) {
            $debug_output[] = "School Name Telugu from DB: " . ($school_config['school_name_telugu'] ?? 'NULL');
            $debug_output[] = "School Name English from DB: " . ($school_config['school_name_english'] ?? 'NULL');
            $debug_output[] = "School Name Subtitle from DB: " . ($school_config['school_name_subtitle'] ?? 'NULL');
        }
    }
    
    // Top bar text
    $topbar_telugu_text = $school_config['topbar_telugu_text'] ?? '???? ?????';
    $topbar_telugu_secondary = $school_config['topbar_telugu_secondary'] ?? 'Government of Telangana';
    
    // School names
    $school_name_telugu = $school_config['school_name_telugu'] ?? '???????, ???????';
    $school_name_english = $school_config['school_name_english'] ?? 'ZPHS, BOMMALARAMARAM';
    $school_name_subtitle = $school_config['school_name_subtitle'] ?? 'Zilla Parishad High School';
    
    if ($debug_mode) {
        $debug_output[] = "Final School Name Telugu: " . $school_name_telugu;
        $debug_output[] = "Final School Name English: " . $school_name_english;
        $debug_output[] = "Final School Name Subtitle: " . $school_name_subtitle;
    }
    
    // Fetch logo/emblem paths from school_config
    $emblem_left_1 = $school_config['emblem_left_1'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Telangana_State_emblem.svg/1200px-Telangana_State_emblem.svg.png';
    $emblem_left_1_alt = $school_config['emblem_left_1_alt'] ?? 'Telangana Emblem';
    $emblem_left_2 = $school_config['emblem_left_2'] ?? 'https://telangana.gov.in/wp-content/themes/developer/assets/images/ts-rising.png';
    $emblem_left_2_alt = $school_config['emblem_left_2_alt'] ?? 'Telangana Rising';
    $emblem_left_2_text = $school_config['emblem_left_2_text'] ?? 'PURE  PURE  RARE';
    
    $emblem_right_1 = $school_config['emblem_right_1'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Digital_India_logo.png/640px-Digital_India_logo.png';
    $emblem_right_1_alt = $school_config['emblem_right_1_alt'] ?? 'Digital India';
    $emblem_right_2 = $school_config['emblem_right_2'] ?? '';
    $emblem_right_2_alt = $school_config['emblem_right_2_alt'] ?? 'Digital Telangana';
    $emblem_right_2_title = $school_config['emblem_right_2_title'] ?? 'DIGITAL';
    $emblem_right_2_telugu = $school_config['emblem_right_2_telugu'] ?? '????';
    $emblem_right_2_subtitle = $school_config['emblem_right_2_subtitle'] ?? 'Power To Empower';
    
    // Static official portraits - PM and President of India (using local images)
    $officials_topbar = [
        ['id' => 1, 'name' => 'Hon\'ble Prime Minister - Shri Narendra Modi', 'image_path' => '/2026/edu_hub/modi.jpg'],
        ['id' => 2, 'name' => 'Hon\'ble President - Smt. Droupadi Murmu', 'image_path' => '/2026/edu_hub/draupadi.jpg']
    ];
    
    // Fetch social media links
    $social_facebook = $school_config['social_facebook'] ?? '#';
    $social_twitter = $school_config['social_twitter'] ?? '#';
    $social_instagram = $school_config['social_instagram'] ?? '#';
    $social_youtube = $school_config['social_youtube'] ?? '#';
    $social_linkedin = $school_config['social_linkedin'] ?? '#';
    
    // Check student login status
    $student_logged_in = isset($_SESSION['student_email']) || isset($_SESSION['student_id']);
    $student_name = $_SESSION['student_name'] ?? ($_SESSION['student_email'] ?? 'Student');
    
} catch (Exception $e) {
    // Fallback values if database fails
    if ($debug_mode) {
        $debug_output[] = "DATABASE ERROR: " . $e->getMessage();
        $debug_output[] = "Using fallback values";
    }
    
    $topbar_telugu_text = '???? ?????';
    $topbar_telugu_secondary = 'Government of Telangana';
    
    $school_name_telugu = '???????, ???????';
    $school_name_english = 'ZPHS, BOMMALARAMARAM';
    $school_name_subtitle = 'Zilla Parishad High School';
    
    $emblem_left_1 = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Telangana_State_emblem.svg/1200px-Telangana_State_emblem.svg.png';
    $emblem_left_1_alt = 'Telangana Emblem';
    $emblem_left_2 = 'https://telangana.gov.in/wp-content/themes/developer/assets/images/ts-rising.png';
    $emblem_left_2_alt = 'Telangana Rising';
    $emblem_left_2_text = 'PURE  PURE  RARE';
    
    $emblem_right_1 = 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Digital_India_logo.png/640px-Digital_India_logo.png';
    $emblem_right_1_alt = 'Digital India';
    $emblem_right_2 = '';
    $emblem_right_2_alt = 'Digital Telangana';
    $emblem_right_2_title = 'DIGITAL';
    $emblem_right_2_telugu = '????';
    $emblem_right_2_subtitle = 'Power To Empower';
    
    // Static official portraits - PM and President of India (using local images)
    $officials_topbar = [
        ['id' => 1, 'name' => 'Hon\'ble Prime Minister - Shri Narendra Modi', 'image_path' => '/2026/edu_hub/modi.jpg'],
        ['id' => 2, 'name' => 'Hon\'ble President - Smt. Droupadi Murmu', 'image_path' => '/2026/edu_hub/draupadi.jpg']
    ];
    
    $social_facebook = '#';
    $social_twitter = '#';
    $social_instagram = '#';
    $social_youtube = '#';
    $social_linkedin = '#';
    
    // Check student login status even in fallback
    $student_logged_in = isset($_SESSION['student_email']) || isset($_SESSION['student_id']);
    $student_name = $_SESSION['student_name'] ?? ($_SESSION['student_email'] ?? 'Student');
}

// Navigation menu items
$nav_items = [
    ['label' => 'HOME', 'href' => '/2026/edu_hub/edu_hub/public/index.php'],
    ['label' => 'ABOUT US', 'href' => '/2026/edu_hub/edu_hub/public/about.php'],
    ['label' => 'GALLERY', 'href' => '/2026/edu_hub/edu_hub/public/gallery.php'],
    ['label' => 'NOTICES', 'href' => '/2026/edu_hub/edu_hub/public/notices.php'],
    ['label' => 'FORMS', 'href' => '/2026/edu_hub/edu_hub/public/student_dashboard.php'],
    ['label' => 'CONTACT US', 'href' => '/2026/edu_hub/edu_hub/public/contact.php']
];
?>

<style>
    /* Reset and scaling fixes for consistent display across devices */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    html {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }
    
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        min-height: 100vh;
        width: 100%;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Container width control */
    .container {
        width: 100%;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Responsive image scaling */
    img {
        max-width: 100%;
        height: auto;
    }
    
    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 200px;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
        margin-top: 0.5rem;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    /* Government Navbar - Consistent Styling */
    .gov-navbar {
        background: linear-gradient(180deg, hsl(120, 50%, 45%) 0%, hsl(120, 61%, 28%) 50%, hsl(120, 70%, 20%) 100%) !important;
    }
    
    .gov-navbar-link {
        padding: 0.5rem 1rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        transition: all 0.2s ease !important;
        color: white !important;
        text-decoration: none !important;
    }
    
    .gov-navbar-link:hover {
        background-color: #ff8c00 !important;
        color: white !important;
    }
    
    /* Official Portrait Image Fallback */
    .official-portrait {
        background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%);
    }
    
    .official-portrait img {
        object-fit: cover;
    }
</style>

<!-- ============ TOP BAR ============ -->
<div class="bg-white border-b border-border py-1 text-sm" style="position: relative; z-index: 60; background-color: white !important;">
    <div class="w-full flex items-center justify-between px-4" style="position: relative; z-index: 60; max-width: 100%;">
        <!-- Left side: Text first, then Official Portraits on right -->
        <div class="flex items-center gap-2 text-foreground" style="margin-left: 0;">
            <span style="font-family: 'Noto Sans Telugu', sans-serif;"><?= htmlspecialchars($topbar_telugu_text) ?></span>
            <span class="text-muted-foreground">|</span>
            <span><?= htmlspecialchars($topbar_telugu_secondary) ?></span>
            <span class="text-muted-foreground ml-2">|</span>
            <!-- Official Portraits - on right side of text -->
            <div class="flex items-center gap-1 ml-2">
                <?php $index = 0; foreach ($officials_topbar as $official): ?>
                <div class="official-portrait <?= $index === 1 ? 'w-10 h-10' : 'w-9 h-9' ?> rounded-full overflow-hidden border-2 border-green-600 shadow-sm hover:shadow-md transition-shadow" title="<?= htmlspecialchars($official['name']) ?>">
                    <img src="<?= htmlspecialchars($official['image_path']) ?>" 
                         alt="<?= htmlspecialchars($official['name']) ?>" 
                         class="w-full h-full object-cover"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/80/2d7a4e/ffffff?text=<?= $index === 0 ? 'PM' : 'P' ?>';">
                </div>
                <?php $index++; endforeach; ?>
            </div>
        </div>
        
        <!-- Right side: Social icons and Login at extreme right corner -->
        <div class="flex items-center gap-2" style="margin-right: 0;">
            <!-- Social Media Icons -->
            <div class="flex items-center gap-2">
                <a href="<?= htmlspecialchars($social_facebook) ?>" class="p-1.5 rounded-full text-white hover:opacity-80 transition-opacity" style="background-color: #3b5998;" target="_blank">
                    <i data-lucide="facebook" class="w-3.5 h-3.5"></i>
                </a>
                <a href="<?= htmlspecialchars($social_twitter) ?>" class="p-1.5 rounded-full text-white hover:opacity-80 transition-opacity" style="background-color: #1da1f2;" target="_blank">
                    <i data-lucide="twitter" class="w-3.5 h-3.5"></i>
                </a>
                <a href="<?= htmlspecialchars($social_instagram) ?>" class="p-1.5 rounded-full text-white hover:opacity-80 transition-opacity" style="background: linear-gradient(135deg, #f09433, #e6683c, #bc1888);" target="_blank">
                    <i data-lucide="instagram" class="w-3.5 h-3.5"></i>
                </a>
                <a href="<?= htmlspecialchars($social_youtube) ?>" class="p-1.5 rounded-full text-white hover:opacity-80 transition-opacity" style="background-color: #ff0000;" target="_blank">
                    <i data-lucide="youtube" class="w-3.5 h-3.5"></i>
                </a>
                <a href="<?= htmlspecialchars($social_linkedin) ?>" class="p-1.5 rounded-full text-white hover:opacity-80 transition-opacity" style="background-color: #0077b5;" target="_blank">
                    <i data-lucide="linkedin" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            
            <span class="text-muted-foreground">|</span>
            
            <!-- Login/Logout Button -->
            <div class="relative" id="loginDropdown">
                <?php if ($student_logged_in): ?>
                    <button onclick="toggleDropdown()" style="background-color: #ff8c00; color: white;" class="px-4 py-1 rounded text-sm font-medium hover:opacity-90 transition-colors flex items-center gap-1">
                        <?= htmlspecialchars($student_name) ?>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" id="dropdownChevron"></i>
                    </button>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="/2026/edu_hub/edu_hub/student_dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm text-foreground hover:bg-peach transition-colors border-b border-border">
                            <i data-lucide="layout-dashboard" class="w-4.5 h-4.5 text-primary"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="/2026/edu_hub/edu_hub/student_logout.php" class="flex items-center gap-3 px-4 py-3 text-sm text-foreground hover:bg-peach transition-colors">
                            <i data-lucide="log-out" class="w-4.5 h-4.5 text-saffron"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                <?php else: ?>
                    <button onclick="toggleDropdown()" style="background-color: #ff8c00; color: white;" class="px-4 py-1 rounded text-sm font-medium hover:opacity-90 transition-colors flex items-center gap-1">
                        Login
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" id="dropdownChevron"></i>
                    </button>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="javascript:void(0);" onclick="openStudentLoginModal();" class="flex items-center gap-3 px-4 py-3 text-sm text-foreground hover:bg-peach transition-colors w-full text-left border-b border-border">
                            <i data-lucide="graduation-cap" class="w-4.5 h-4.5 text-primary"></i>
                            <span>Login as Student</span>
                        </a>
                        <a href="/2026/edu_hub/edu_hub/admin/index.php" class="flex items-center gap-3 px-4 py-3 text-sm text-foreground hover:bg-peach transition-colors w-full text-left">
                            <i data-lucide="shield" class="w-4.5 h-4.5 text-saffron"></i>
                            <span>Login as Admin</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============ HEADER ============ -->
<header class="bg-background py-3 border-b border-border" style="margin: 0; padding-top: 0.75rem; padding-bottom: 0.75rem; position: relative; z-index: 50; background-color: white !important;">
    <div class="w-full px-4" style="max-width: 100%;">
        <div class="flex items-center justify-between" style="position: relative; z-index: 50;">
            <!-- Left - Government Emblem & Telangana Rising - at extreme left -->
            <div class="hidden sm:flex items-center gap-3 shrink-0" style="margin-left: 0;">
                <div class="flex flex-col items-center">
                    <img src="<?= htmlspecialchars($emblem_left_1) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($emblem_left_1_alt) ?>" class="h-16 md:h-20 w-auto object-contain" loading="lazy" style="position: relative; z-index: 10;">
                </div>
                <div class="flex flex-col items-center">
                    <img src="<?= htmlspecialchars($emblem_left_2) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($emblem_left_2_alt) ?>" class="h-16 md:h-20 w-auto object-contain" loading="lazy" style="position: relative; z-index: 10;">
                </div>
            </div>
            
            <!-- Center - School Name -->
            <div class="text-center flex-1 min-w-0 px-4" style="position: relative; z-index: 10;">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold" style="font-family: 'Noto Sans Telugu', sans-serif; color: #ff8c00; position: relative; z-index: 10; margin-bottom: 8px; text-shadow: 0 2px 4px rgba(0,0,0,0.1); letter-spacing: 0.5px;" id="schoolNameTelugu">
                    <?= htmlspecialchars($school_name_telugu) ?>
                </h1>
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-foreground" style="position: relative; z-index: 10; color: #1a365d; font-weight: 800; margin-bottom: 4px; letter-spacing: 1px; text-transform: uppercase; font-family: Georgia, serif;" id="schoolNameEnglish">
                    <?= htmlspecialchars($school_name_english) ?>
                </h2>
                <p class="text-xs sm:text-sm text-muted-foreground" style="position: relative; z-index: 10; color: #64748b; font-weight: 500; font-style: italic; font-size: 0.875rem;" id="schoolNameSubtitle">
                    <?= htmlspecialchars($school_name_subtitle) ?>
                </p>
            </div>
            
            <!-- Right - Digital India & Digital Telangana - at extreme right -->
            <div class="hidden sm:flex items-center gap-3 shrink-0" style="margin-right: 0;">
                <div class="flex flex-col items-center">
                    <?php if (!empty($emblem_right_2)): ?>
                        <img src="<?= htmlspecialchars($emblem_right_2) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($emblem_right_2_alt) ?>" class="h-14 md:h-16 w-auto object-contain" loading="lazy" style="position: relative; z-index: 10;">
                    <?php else: ?>
                        <div class="text-primary font-bold text-sm flex flex-col items-center" style="position: relative; z-index: 10;">
                            <span class="text-base md:text-lg"><?= htmlspecialchars($emblem_right_2_title) ?></span>
                            <span class="text-xs" style="font-family: 'Noto Sans Telugu', sans-serif;"><?= htmlspecialchars($emblem_right_2_telugu) ?></span>
                            <span class="text-[10px] text-muted-foreground"><?= htmlspecialchars($emblem_right_2_subtitle) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col items-center">
                    <img src="<?= htmlspecialchars($emblem_right_1) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($emblem_right_1_alt) ?>" class="h-14 md:h-16 w-auto object-contain" loading="lazy" style="position: relative; z-index: 10;">
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ============ GOVERNMENT NAVBAR (Sticky) ============ -->
<nav class="gov-navbar sticky top-0 z-50 shadow-lg" style="margin: 0;">
    <div class="container mx-auto px-4">
        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center justify-between">
            <?php foreach ($nav_items as $item): ?>
            <div class="relative flex-1 text-center">
                <a href="<?= htmlspecialchars($item['href']) ?>" class="gov-navbar-link inline-flex items-center justify-center gap-1 w-full">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Mobile Navigation -->
        <div class="md:hidden flex items-center justify-between py-2">
            <span class="text-white font-bold">Menu</span>
            <button onclick="toggleMobileMenu()" class="text-white p-2">
                <i data-lucide="menu" class="w-6 h-6" id="mobileMenuIcon"></i>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div class="md:hidden hidden" id="mobileMenu" style="position: absolute; left: 0; right: 0; background-color: hsl(120, 61%, 28%); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
            <?php foreach ($nav_items as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="block px-4 py-3 text-white hover:bg-white/10 border-b border-white/10">
                <?= htmlspecialchars($item['label']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<!-- Student Login Modal -->
<div id="studentLoginModal" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden flex items-center justify-center" onclick="closeStudentLoginModal(event)">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Student Login / Signup</h3>
                <button onclick="closeStudentLoginModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Google Login Button -->
            <a href="/2026/edu_hub/edu_hub/student_login_signup.php" class="block w-full mb-4 p-3 border-2 border-gray-200 rounded-lg hover:shadow-md transition-all">
                <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" class="w-full max-w-[220px] mx-auto">
            </a>
            
            <div class="text-center my-4 text-gray-500 font-semibold">or</div>
            
            <!-- Email Login Form -->
            <form method="post" action="/2026/edu_hub/edu_hub/student_login_signup.php" id="studentLoginForm">
                <input type="hidden" name="return_url" id="loginReturnUrl" value="">
                <div class="mb-4">
                    <label for="student_email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                    <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="student_email" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="student_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="student_password" name="password" required>
                </div>
                <button type="submit" name="login" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">Login</button>
            </form>
            
            <script>
                // Set return URL when form is shown
                document.getElementById('studentLoginForm').addEventListener('submit', function() {
                    document.getElementById('loginReturnUrl').value = sessionStorage.getItem('returnUrl') || window.location.href;
                });
            </script>
            
            <div class="text-center my-4 text-gray-500 font-semibold">New user?</div>
            <a href="/2026/edu_hub/edu_hub/student_email_register.php" class="block w-full bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium text-center">Sign up with Email</a>
        </div>
    </div>
</div>

<script>
// Open login choice modal
function openLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close login choice modal
function closeLoginModal(event) {
    if (!event || event.target.id === 'loginModal') {
        document.getElementById('loginModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Open student login modal
function openStudentLoginModal() {
    // Close dropdown first
    const dropdown = document.getElementById('dropdownMenu');
    if (dropdown) dropdown.classList.remove('show');
    const chevron = document.getElementById('dropdownChevron');
    if (chevron) chevron.style.transform = 'rotate(0deg)';
    
    // Store current page URL for return after login
    sessionStorage.setItem('returnUrl', window.location.href);
    
    // Show student login modal
    document.getElementById('studentLoginModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close student login modal
function closeStudentLoginModal(event) {
    if (!event || event.target.id === 'studentLoginModal') {
        document.getElementById('studentLoginModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Dropdown toggle function
function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    const chevron = document.getElementById('dropdownChevron');
    if (menu && chevron) {
        menu.classList.toggle('show');
        chevron.style.transform = menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
}

// Mobile menu toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('loginDropdown');
    const menu = document.getElementById('dropdownMenu');
    if (dropdown && menu && !dropdown.contains(event.target)) {
        menu.classList.remove('show');
        const chevron = document.getElementById('dropdownChevron');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
});

// Debug: Log school name elements on page load
console.log('=== NAVBAR DEBUG INFO ===');
console.log('School Name Telugu Element:', document.getElementById('schoolNameTelugu'));
console.log('School Name English Element:', document.getElementById('schoolNameEnglish'));
console.log('School Name Subtitle Element:', document.getElementById('schoolNameSubtitle'));
if (document.getElementById('schoolNameTelugu')) {
    console.log('Telugu Text:', document.getElementById('schoolNameTelugu').textContent);
    console.log('Telugu Visibility:', window.getComputedStyle(document.getElementById('schoolNameTelugu')).visibility);
    console.log('Telugu Display:', window.getComputedStyle(document.getElementById('schoolNameTelugu')).display);
    console.log('Telugu Opacity:', window.getComputedStyle(document.getElementById('schoolNameTelugu')).opacity);
}
if (document.getElementById('schoolNameEnglish')) {
    console.log('English Text:', document.getElementById('schoolNameEnglish').textContent);
    console.log('English Visibility:', window.getComputedStyle(document.getElementById('schoolNameEnglish')).visibility);
    console.log('English Display:', window.getComputedStyle(document.getElementById('schoolNameEnglish')).display);
    console.log('English Opacity:', window.getComputedStyle(document.getElementById('schoolNameEnglish')).opacity);
}
console.log('=========================');
</script>

<?php if ($debug_mode && !empty($debug_output)): ?>
<!-- PHP Debug Panel -->
<div style="position: fixed; bottom: 20px; right: 20px; background: #fff; border: 3px solid #ff0000; padding: 20px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); max-width: 400px; z-index: 9999; font-family: monospace; font-size: 12px;">
    <div style="background: #ff0000; color: white; padding: 10px; margin: -20px -20px 10px -20px; border-radius: 7px 7px 0 0; font-weight: bold;">
        ?? NAVBAR DEBUG PANEL
    </div>
    <?php foreach ($debug_output as $line): ?>
        <div style="margin: 5px 0; padding: 5px; background: #f0f0f0; border-left: 3px solid #007bff;">
            <?= htmlspecialchars($line) ?>
        </div>
    <?php endforeach; ?>
    <div style="margin-top: 15px; padding-top: 10px; border-top: 2px solid #ccc;">
        <strong>Browser Info:</strong><br>
        <span style="color: #666;">Check Console (F12) for JavaScript debug output</span>
    </div>
</div>
<?php endif; ?>
