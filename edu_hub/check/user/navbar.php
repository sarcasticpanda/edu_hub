<?php
// Navbar for inclusion in all pages
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
$school_logo = '';
if ($pdo) {
    $row = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_logo'")->fetch();
    $school_logo = $row ? $row['config_value'] : '';
}
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();
$student_logged_in = isset($_SESSION['student_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .bg-primary {
            background-color: #1E2A44 !important;
        }
        .logo-img {
            height: 32px;
            width: auto;
            border-radius: 6px;
            object-fit: cover;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .btn-accent {
            background-color: #FF9933 !important;
            border: none;
        }
        .btn-accent:hover {
            background-color: #e67c00 !important;
        }
        .nav-link-custom {
            color: #fff !important;
            font-weight: 500;
            margin: 0 6px;
            border-radius: 6px;
            padding: 8px 16px;
            transition: background 0.2s, color 0.2s;
        }
        .nav-link-custom:hover, .nav-link-custom:focus {
            background: #FF9933 !important;
            color: #fff !important;
        }
        @media (max-width: 991px) {
            .logo-img { height: 24px; }
            .nav-link-custom { padding: 8px 10px; }
            .circle-logo { width: 32px; height: 32px; margin-left: 6px; margin-right: 6px; }
        }
        .circle-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            border: 2px solid #fff;
            display: inline-block;
            margin-left: 10px;
            margin-right: 10px;
        }
        #navbar {
            background: #1E2A44 !important;
            opacity: 0.97;
            z-index: 1050;
            transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        .hide-navbar {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }
        :root {
            --color-primary: #1E2A44;
            --color-accent: #F5A623;
            --color-red: #D32F2F;
        }
        .navbar {
            background-color: var(--color-primary);
            color: #fff;
            box-shadow: 0 4px 16px rgba(30, 42, 68, 0.3);
            transition: height 0.3s ease, box-shadow 0.3s ease;
        }
        #navbar {
            height: 56px;
        }
        #navbar.navbar-shrink {
            height: 48px;
            box-shadow: 0 6px 20px rgba(30, 42, 68, 0.4);
        }
        .navbar-brand img {
            max-height: 100%;
            opacity: 1 !important;
            display: block !important;
            visibility: visible !important;
            transition: transform 0.3s ease;
        }
        .navbar-toggler {
            border: none;
            background: linear-gradient(135deg, var(--color-accent), #FFCD70);
            padding: 0.5rem;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(245, 166, 35, 0.3);
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .navbar-toggler:hover {
            transform: scale(1.1);
            background: var(--color-red);
        }
        .navbar-toggler-icon-custom {
            width: 20px;
            height: 20px;
            position: relative;
            background: var(--color-red);
        }
        .navbar-toggler-icon-custom::before,
        .navbar-toggler-icon-custom::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            background: #fff;
            transition: all 0.3s ease;
        }
        .navbar-toggler-icon-custom::before {
            top: 50%;
            transform: translateY(-50%);
        }
        .navbar-toggler-icon-custom::after {
            bottom: 50%;
            transform: translateY(50%);
        }
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon-custom::before {
            transform: translateY(-50%) rotate(45deg);
        }
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon-custom::after {
            transform: translateY(50%) rotate(-45deg);
        }
        /* Fix for Bootstrap nav-pills and nav-tabs active state */
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: #D32F2F !important;
            color: #fff !important;
        }
        .nav-tabs .nav-link.active, .nav-tabs .show > .nav-link {
            color: #D32F2F !important;
            border-color: #D32F2F #D32F2F #fff !important;
            background: #fff !important;
            font-weight: 700;
        }
        .nav-tabs .nav-link {
            color: #1E2A44;
            font-weight: 600;
        }
        .navbar-nav {
            margin-left: 0 !important;
            margin-right: auto !important;
            gap: 18px;
        }
        .navbar-collapse {
            display: flex !important;
            justify-content: flex-start !important;
            align-items: center !important;
            padding-left: 120px;
        }
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-primary text-white shadow-lg py-3 fixed-top" id="navbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center me-4" href="/2026/edu_hub/edu_hub/check/user/index.php" style="gap: 10px;">
                <?php
                $logo_path = '/2026/edu_hub/edu_hub/check/user/../images/' . ($school_logo ? htmlspecialchars($school_logo) : 'school.png');
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $logo_path)) {
                    $logo_path = '/2026/edu_hub/edu_hub/check/user/../images/school.png';
                }
                ?>
                <img src="<?= $logo_path ?>" alt="School Logo" class="logo-img" style="height:44px; width:auto; border-radius:6px; object-fit:cover; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            </a>
            <div class="d-flex align-items-center flex-grow-1">
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/index.php"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/gallery.php"><i class="fas fa-images me-2"></i>Gallery</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/contact.php"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                    <?php if ($student_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/student_dashboard.php"><i class="fas fa-file-alt me-2"></i>Application</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-custom dropdown-toggle" href="#" id="registrationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-plus me-2"></i>Registration
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="registrationDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#authModal"><i class="fas fa-user-graduate me-2"></i>Login/Signup as Student</a></li>
                                <li><a class="dropdown-item" href="/2026/edu_hub/edu_hub/admin/index.php"><i class="fas fa-user-shield me-2"></i>Continue as Admin</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center ms-3">
                    <img src="/2026/edu_hub/edu_hub/check/user/../images/flag.jpeg" alt="Telangana Flag" class="circle-logo">
                    <img src="/2026/edu_hub/edu_hub/check/user/../images/cm.jpeg" alt="CM of Telangana" class="circle-logo">
                    <img src="/2026/edu_hub/edu_hub/check/user/../images/edu.jpeg" alt="Education Minister" class="circle-logo">
                </div>
            </div>
        </div>
    </nav>
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
                        // Generate Google OAuth URL directly
                        require_once __DIR__ . '/../../vendor/autoload.php';
                        require_once __DIR__ . '/../../config.php';
                        $googleClient = new Google_Client();
                        $googleClient->setClientId(GOOGLE_CLIENT_ID);
                        $googleClient->setClientSecret(GOOGLE_CLIENT_SECRET);
                        $googleClient->setRedirectUri(GOOGLE_REDIRECT_URI);
                        $googleClient->addScope('email');
                        $googleClient->addScope('profile');
                        $googleClient->setAccessType('offline');
                        $googleClient->setPrompt('select_account');
                        $googleAuthUrl = $googleClient->createAuthUrl();
                        ?>
                        <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn google-btn w-100 mb-2">
                            <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" style="width:100%;max-width:220px;display:block;margin:auto;">
                        </a>
                        <div class="or-divider text-center my-2">or</div>
                        <form id="modalLoginForm" method="post" action="/2026/edu_hub/edu_hub/student_login_signup.php">
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
                        <div class="or-divider text-center my-2">New user?</div>
                        <a href="/2026/edu_hub/edu_hub/student_email_register.php" class="btn btn-secondary w-100">Sign up with Email</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Scripts -->
    <link rel="stylesheet" href="style.css">
        <style>
            /* Ensure dropdown-menu displays correctly */
            .dropdown-menu { display: none; position: absolute; background: #fff; min-width: 180px; box-shadow: 0 4px 16px rgba(30,42,68,0.10); z-index: 2000; }
            .dropdown.show .dropdown-menu { display: block; }
        </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Fallback: force dropdown to work if Bootstrap JS is loaded
            document.addEventListener('DOMContentLoaded', function() {
                var dropdownToggles = document.querySelectorAll('.dropdown-toggle');
                dropdownToggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        var parent = this.closest('.dropdown');
                        if (parent.classList.contains('show')) {
                            parent.classList.remove('show');
                        } else {
                            document.querySelectorAll('.dropdown.show').forEach(function(open) { open.classList.remove('show'); });
                            parent.classList.add('show');
                        }
                    });
                });
                // Close dropdown on outside click
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown')) {
                        document.querySelectorAll('.dropdown.show').forEach(function(open) { open.classList.remove('show'); });
                    }
                });
            });
        </script>
    <script>
        // Consolidated scroll event listener
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.getElementById('navbar');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                // Handle navbar hide/show
                if (currentScroll > lastScrollY && currentScroll > 80) {
                    navbar.classList.add('hide-navbar');
                } else {
                    navbar.classList.remove('hide-navbar');
                }
                // Update box-shadow on scroll for subtle effect
                if (currentScroll > 100) {
                    navbar.style.boxShadow = '0 6px 20px rgba(30, 42, 68, 0.4)';
                } else {
                    navbar.style.boxShadow = '0 4px 16px rgba(30, 42, 68, 0.3)';
                }
                lastScrollY = currentScroll;
            });

            // Modal cleanup
            const authModal = document.getElementById('authModal');
            authModal.addEventListener('hidden.bs.modal', () => {
                // Remove modal backdrop
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                // Reset modal classes and styles
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                console.log('Auth modal closed and cleaned up');
            });

            authModal.addEventListener('shown.bs.modal', () => {
                console.log('Auth modal opened');
            });
        });

        // Move these functions OUTSIDE so they are global!
        function handleLogin(type) {
            console.log(`Login attempt: ${type}`);
            // Add your login logic here (e.g., AJAX request)
            const authModal = document.getElementById('authModal');
            const modal = bootstrap.Modal.getInstance(authModal);
            modal.hide();
        }

        function handleSignup() {
            console.log('Signup attempt');
            // Add your signup logic here (e.g., AJAX request)
            const authModal = document.getElementById('authModal');
            const modal = bootstrap.Modal.getInstance(authModal);
            modal.hide();
        }
    </script>
</body>
</html>