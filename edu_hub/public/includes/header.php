<?php
// Government School Website - Header Component
// Fetch school configuration from database
try {
    $school_config = [];
    $stmt = $pdo->query("SELECT config_key, config_value FROM school_config");
    while ($row = $stmt->fetch()) {
        $school_config[$row['config_key']] = $row['config_value'];
    }
} catch (Exception $e) {
    $school_config = [];
}

// Set defaults if not in database
$school_name_telugu = $school_config['school_name_telugu'] ?? 'జెడ్పీహెచ్ఎస్, బొమ్మలరామారం';
$school_name_english = $school_config['school_name_english'] ?? 'ZPHS, BOMMALARAMARAM';
$school_subtitle_telugu = $school_config['school_subtitle_telugu'] ?? '';
$school_subtitle_english = $school_config['school_subtitle_english'] ?? '';
$official_img_1 = $school_config['official_image_1'] ?? 'flag.jpeg';
$official_img_2 = $school_config['official_image_2'] ?? 'cm.jpeg';
$official_img_3 = $school_config['official_image_3'] ?? 'edu.jpeg';

// Check if student is logged in
$student_logged_in = isset($_SESSION['student_email']) || isset($_SESSION['student_id']);
$student_name = $_SESSION['student_name'] ?? ($_SESSION['student_email'] ?? '');
?>
<style>
        /* Very Top Bar - Government of Telangana */
        .gov-top-bar {
            background: #FFFFFF;
            border-bottom: 1px solid #E0E0E0;
            padding: 8px 0;
            font-size: 14px;
        }
        
        /* Official circles inside top bar */
        .gov-top-bar .official-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FF9933;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        
        .gov-top-bar .right-section {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .social-icons {
            display: flex;
            gap: 12px;
        }
        
        .social-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0D5C3F;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .social-icon:hover {
            background: #FF9933;
            transform: scale(1.1);
        }
        
        .login-btn-top {
            background: #FF9933;
            color: white;
            padding: 6px 20px;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s;
            font-size: 14px;
        }
        
        .login-btn-top:hover {
            background: #e68a1f;
            color: white;
        }
        
        /* Header Section with School Name and Logos */
        .school-header {
            background: #FFFFFF;
            padding: 20px 0;
            border-bottom: 3px solid #FF9933;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        
        .header-logos-left {
            display: flex;
            gap: 15px;
            align-items: center;
            flex: 0 0 auto;
        }
        
        .header-logo {
            height: 80px;
            width: auto;
        }
        
        .school-name-block {
            text-align: center;
            flex: 1;
        }
        
        .school-name-telugu {
            font-size: 22px;
            font-weight: 700;
            color: #1E2A44;
            margin-bottom: 3px;
            font-family: 'Noto Sans Telugu', sans-serif;
        }
        
        .school-name-english {
            font-size: 26px;
            font-weight: 700;
            color: #0D5C3F;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        
        .school-subtitle {
            font-size: 13px;
            color: #666;
            font-style: italic;
        }
        
        .header-logos-right {
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: flex-end;
            flex: 0 0 auto;
        }
        
        /* Three Official Images */
        .three-officials {
            background: #F8F9FA;
            padding: 12px 0;
            text-align: center;
            border-bottom: 2px solid #0D5C3F;
        }
        
        .officials-container {
            display: flex;
            justify-content: center;
            gap: 25px;
        }
        
        .official-circle {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #FF9933;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }
        
        .official-circle:hover {
            transform: scale(1.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .header-logos-left,
            .header-logos-right {
                justify-content: center;
            }
            
            .header-logo {
                height: 60px;
            }
            
            .school-name-telugu {
                font-size: 18px;
            }
            
            .school-name-english {
                font-size: 20px;
            }
            
            .official-circle {
                width: 50px;
                height: 50px;
            }
            
            .gov-top-bar {
                font-size: 12px;
            }
            
            .social-icons {
                gap: 8px;
            }
        }
    </style>

    <!-- Very Top Bar -->
    <div class="gov-top-bar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="left-section d-flex align-items-center" style="gap:12px;">
                    <img src="/2026/edu_hub/edu_hub/storage/images/<?= htmlspecialchars($official_img_1) ?>" alt="Official 1" class="official-circle" onerror="this.src='/2026/edu_hub/edu_hub/storage/images/flag.jpeg'">
                    <img src="/2026/edu_hub/edu_hub/storage/images/<?= htmlspecialchars($official_img_2) ?>" alt="Official 2" class="official-circle" onerror="this.src='/2026/edu_hub/edu_hub/storage/images/cm.jpeg'">
                    <img src="/2026/edu_hub/edu_hub/storage/images/<?= htmlspecialchars($official_img_3) ?>" alt="Official 3" class="official-circle" onerror="this.src='/2026/edu_hub/edu_hub/storage/images/edu.jpeg'">
                </div>
                <div class="right-section">
                    <div class="social-icons">
                        <a href="#" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                    <?php if ($student_logged_in): ?>
                        <a href="/2026/edu_hub/edu_hub/student_dashboard.php" class="login-btn-top">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($student_name) ?>
                        </a>
                    <?php else: ?>
                        <a href="#" class="login-btn-top" data-bs-toggle="modal" data-bs-target="#authModal">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Header Section with Logos and School Name -->
    <div class="school-header">
        <div class="container-fluid">
            <div class="header-content">
                <!-- Left Logos -->
                <div class="header-logos-left">
                    <img src="/2026/edu_hub/edu_hub/storage/images/govt-emblem.png" alt="Government Emblem" class="header-logo" 
                         onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Emblem_of_India.svg/150px-Emblem_of_India.svg.png'">
                    <img src="/2026/edu_hub/edu_hub/storage/images/telangana-rising.png" alt="Telangana Rising" class="header-logo"
                         onerror="this.style.display='none'">
                </div>
                
                <!-- School Name Block -->
                <div class="school-name-block">
                    <div class="school-name-telugu"><?= htmlspecialchars($school_name_telugu) ?></div>
                    <div class="school-name-english"><?= htmlspecialchars($school_name_english) ?></div>
                    <?php if ($school_subtitle_english): ?>
                        <div class="school-subtitle"><?= htmlspecialchars($school_subtitle_english) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Logos -->
                <div class="header-logos-right">
                    <img src="/2026/edu_hub/edu_hub/storage/images/digital-india.png" alt="Digital India" class="header-logo"
                         onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Digital_India_logo.svg/150px-Digital_India_logo.svg.png'">
                    <img src="/2026/edu_hub/edu_hub/storage/images/digital-telangana.png" alt="Digital Telangana" class="header-logo"
                         onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Three Official Images moved to top bar; removed duplicate band -->
    
    <!-- Login/Signup Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="authModalLabel">Student Login / Signup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    // Generate Google OAuth URL
                    if (file_exists(__DIR__ . '/../../vendor/autoload.php') && file_exists(__DIR__ . '/../../config.php')) {
                        require_once __DIR__ . '/../../vendor/autoload.php';
                        require_once __DIR__ . '/../../config.php';
                        $googleClient = new Google_Client();
                        $googleClient->setClientId(GOOGLE_CLIENT_ID);
                        $googleClient->setClientSecret(GOOGLE_CLIENT_SECRET);
                        $googleClient->setRedirectUri(GOOGLE_REDIRECT_URI);
                        $googleClient->addScope('email');
                        $googleClient->addScope('profile');
                        $googleAuthUrl = $googleClient->createAuthUrl();
                    ?>
                        <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn btn-light w-100 mb-3" style="border:1px solid #ddd;">
                            <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" style="width:100%;max-width:220px;display:block;margin:auto;">
                        </a>
                        <div class="text-center my-2" style="color:#666;">or</div>
                    <?php } ?>
                    <form method="post" action="/2026/edu_hub/edu_hub/student_login_signup.php">
                        <div class="mb-3">
                            <label for="modalEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="modalEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="modalPassword" name="password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center my-2" style="color:#666;">New user?</div>
                    <a href="/2026/edu_hub/edu_hub/student_email_register.php" class="btn btn-secondary w-100">Sign up with Email</a>
                </div>
            </div>
        </div>
    </div>
