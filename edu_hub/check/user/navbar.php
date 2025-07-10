<?php
// Navbar for inclusion in all pages
// Use the same DB connection as admin/includes/db.php
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
$school_logo = '';
if ($pdo) {
    $row = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_logo'")->fetch();
    $school_logo = $row ? $row['config_value'] : '';
}
// Fetch school info from homepage_content
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();
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
            margin-right: 0 !important;
            gap: 18px;
        }
        .navbar-collapse {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
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
            <a class="navbar-brand d-flex align-items-center me-4" href="index.php" style="gap: 10px;">
                <?php
                $logo_path = '../images/' . ($school_logo ? htmlspecialchars($school_logo) : 'school.png');
                if (!file_exists(__DIR__ . '/' . $logo_path)) {
                    $logo_path = '../images/school.png';
                }
                ?>
                <img src="<?= $logo_path ?>" alt="School Logo" class="logo-img" style="height:44px; width:auto; border-radius:6px; object-fit:cover; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            </a>
            <div class="d-flex align-items-center flex-grow-1">
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="gallery.php"><i class="fas fa-images me-2"></i>Gallery</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="contact.php"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="user_portal/register_form.php"><i class="fas fa-user-plus me-2"></i>Registration</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="../../admin/login.php"><i class="fas fa-user-shield me-2"></i>Continue as Admin</a></li>
                </ul>
                <div class="d-flex align-items-center ms-3">
                    <img src="../images/flag.jpeg" alt="Telangana Flag" class="circle-logo">
                    <img src="../images/cm.jpeg" alt="CM of Telangana" class="circle-logo">
                    <img src="../images/edu.jpeg" alt="Education Minister" class="circle-logo">
                </div>
            </div>
        </div>
    </nav>
    <!-- Scripts -->
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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