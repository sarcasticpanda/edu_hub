<?php
// Navbar snippet - only navbar HTML, no full page structure
// Assumes session is already started and $pdo is available

// Initialize variables with fallbacks
$school_name_telugu = 'జడపహచఎస, బమమలరమర';
$school_name_english = 'ZPHS, BOMMALARAMARAM';
$school_name_subtitle = 'Zilla Parishad High School';
$topbar_telugu_text = 'తలగణ పరభతవ';
$topbar_telugu_secondary = 'Government of Telangana';

$emblem_left_1 = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Telangana_State_emblem.svg/1200px-Telangana_State_emblem.svg.png';
$emblem_left_1_alt = 'Telangana Emblem';
$emblem_left_2 = 'https://telangana.gov.in/wp-content/themes/developer/assets/images/ts-rising.png';
$emblem_left_2_alt = 'Telangana Rising';

$emblem_right_1 = 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Digital_India_logo.png/640px-Digital_India_logo.png';
$emblem_right_1_alt = 'Digital India';

$social_facebook = '#';
$social_twitter = '#';
$social_instagram = '#';
$social_youtube = '#';
$social_linkedin = '#';

// Fetch data from database
if (isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM school_config WHERE id = 1");
        $school_config = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($school_config) {
            // Update variables with database values
            $school_name_telugu = $school_config['school_name_telugu'] ?? $school_name_telugu;
            $school_name_english = $school_config['school_name_english'] ?? $school_name_english;
            $school_name_subtitle = $school_config['school_name_subtitle'] ?? $school_name_subtitle;
            $topbar_telugu_text = $school_config['topbar_telugu_text'] ?? $topbar_telugu_text;
            $topbar_telugu_secondary = $school_config['topbar_telugu_secondary'] ?? $topbar_telugu_secondary;
            
            $emblem_left_1 = $school_config['emblem_left_1'] ?? $emblem_left_1;
            $emblem_left_1_alt = $school_config['emblem_left_1_alt'] ?? $emblem_left_1_alt;
            $emblem_left_2 = $school_config['emblem_left_2'] ?? $emblem_left_2;
            $emblem_left_2_alt = $school_config['emblem_left_2_alt'] ?? $emblem_left_2_alt;
            
            $emblem_right_1 = $school_config['emblem_right_1'] ?? $emblem_right_1;
            $emblem_right_1_alt = $school_config['emblem_right_1_alt'] ?? $emblem_right_1_alt;
            
            $social_facebook = $school_config['social_facebook'] ?? $social_facebook;
            $social_twitter = $school_config['social_twitter'] ?? $social_twitter;
            $social_instagram = $school_config['social_instagram'] ?? $social_instagram;
            $social_youtube = $school_config['social_youtube'] ?? $social_youtube;
            $social_linkedin = $school_config['social_linkedin'] ?? $social_linkedin;
        }
    } catch (Exception $e) {
        // Keep fallback values if database query fails
        error_log("Navbar: Database error - " . $e->getMessage());
    }
}

$student_logged_in = isset($_SESSION['student_email']);
?>
<nav class="navbar navbar-expand-lg bg-primary text-white shadow-lg py-3 fixed-top" id="navbar" style="background-color: #1E2A44 !important;">
    <div class="container-fluid">
        <!-- Brand with Dynamic Emblem -->
        <a class="navbar-brand d-flex align-items-center me-4" href="/2026/edu_hub/edu_hub/public/index.php" style="gap: 10px;">
            <img src="<?= htmlspecialchars($emblem_left_1) ?>" alt="<?= htmlspecialchars($emblem_left_1_alt) ?>" class="logo-img" style="height:44px; width:auto; border-radius:6px; object-fit:cover; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.07);" onerror="this.src='/2026/edu_hub/edu_hub/storage/images/school.png'">
            <div class="d-flex flex-column" style="line-height: 1.2;">
                <span style="font-size: 0.9rem; font-weight: 600; font-family: 'Noto Sans Telugu', sans-serif;"><?= htmlspecialchars($school_name_telugu) ?></span>
                <span style="font-size: 0.85rem; font-weight: 500;"><?= htmlspecialchars($school_name_english) ?></span>
            </div>
        </a>
        <div class="d-flex align-items-center flex-grow-1">
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/index.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px; transition: background 0.2s, color 0.2s;"><i class="fas fa-home me-2"></i>Home</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/about.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/gallery.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-images me-2"></i>Gallery</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/contact.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                <?php if ($student_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/student_dashboard.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-file-alt me-2"></i>Application</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/public/student_logout.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle" href="#" id="registrationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;">
                            <i class="fas fa-user-plus me-2"></i>Registration
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="registrationDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#authModal"><i class="fas fa-user-graduate me-2"></i>Login/Signup as Student</a></li>
                            <li><a class="dropdown-item" href="/2026/edu_hub/edu_hub/admin/index.php"><i class="fas fa-user-shield me-2"></i>Continue as Admin</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/admin/index.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-user-shield me-2"></i>Continue as Admin</a></li>
                <?php endif; ?>
            </ul>
            <!-- Static Official Images - PM and President of India (Wikipedia URLs) -->
            <div class="d-flex align-items-center ms-3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Narendra_Modi_2021.jpg/220px-Narendra_Modi_2021.jpg" alt="Prime Minister of India" class="circle-logo" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 2px solid #fff; display: inline-block; margin-left: 8px; margin-right: 8px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Draupadi_Murmu_official_portrait.jpg/220px-Draupadi_Murmu_official_portrait.jpg" alt="President of India" class="circle-logo" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 2px solid #fff; display: inline-block; margin-left: 8px; margin-right: 8px;">
            </div>
        </div>
    </div>
</nav>
<style>
.nav-link-custom:hover, .nav-link-custom:focus {
    background: #FF9933 !important;
    color: #fff !important;
}
.navbar-toggler {
    border: none;
    background: linear-gradient(135deg, #F5A623, #FFCD70);
    padding: 0.5rem;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(245, 166, 35, 0.3);
    width: 40px;
    height: 40px;
}
</style>
