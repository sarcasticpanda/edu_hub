<?php
// Navbar snippet - only navbar HTML, no full page structure
// Assumes session is already started and $pdo is available
$school_logo = '';
if (isset($pdo) && $pdo) {
    try {
        $row = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_logo'")->fetch();
        $school_logo = $row ? $row['config_value'] : '';
    } catch (Exception $e) {
        $school_logo = '';
    }
}
$student_logged_in = isset($_SESSION['student_email']);
?>
<nav class="navbar navbar-expand-lg bg-primary text-white shadow-lg py-3 fixed-top" id="navbar" style="background-color: #1E2A44 !important;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center me-4" href="/2026/edu_hub/edu_hub/check/user/index.php" style="gap: 10px;">
            <?php
            $logo_path = '/2026/edu_hub/edu_hub/check/images/' . ($school_logo ? htmlspecialchars($school_logo) : 'school.png');
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $logo_path)) {
                $logo_path = '/2026/edu_hub/edu_hub/check/images/school.png';
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
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/index.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px; transition: background 0.2s, color 0.2s;"><i class="fas fa-home me-2"></i>Home</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/about.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/gallery.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-images me-2"></i>Gallery</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/check/user/contact.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                <?php if ($student_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/student_dashboard.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-file-alt me-2"></i>Application</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="/2026/edu_hub/edu_hub/student_logout.php" style="color: #fff !important; font-weight: 500; margin: 0 6px; border-radius: 6px; padding: 8px 16px;"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
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
            <div class="d-flex align-items-center ms-3">
                <img src="/2026/edu_hub/edu_hub/check/images/flag.jpeg" alt="Telangana Flag" class="circle-logo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 2px solid #fff; display: inline-block; margin-left: 10px; margin-right: 10px;">
                <img src="/2026/edu_hub/edu_hub/check/images/cm.jpeg" alt="CM of Telangana" class="circle-logo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 2px solid #fff; display: inline-block; margin-left: 10px; margin-right: 10px;">
                <img src="/2026/edu_hub/edu_hub/check/images/edu.jpeg" alt="Education Minister" class="circle-logo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 2px solid #fff; display: inline-block; margin-left: 10px; margin-right: 10px;">
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
