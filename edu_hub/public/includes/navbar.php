<?php
// Green Government Navigation Bar Component (clean include)
require_once __DIR__ . '/../../admin/includes/db.php';

$student_logged_in = isset($_SESSION['student_email']) || isset($_SESSION['student_id']);
$student_name = $_SESSION['student_name'] ?? ($_SESSION['student_email'] ?? '');
?>

<!-- GREEN GOVERNMENT NAVIGATION BAR -->
<nav class="gov-navbar navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <button class="gov-navbar-toggle navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#govNavbarNav" aria-controls="govNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="govNavbarNav">
            <ul class="gov-navbar-nav nav-menu" role="menubar" aria-label="Main Navigation">
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="/2026/edu_hub/edu_hub/public/index.php">Home</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="/2026/edu_hub/edu_hub/public/about.php">About Us</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="#">Administration</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="#">Academics</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="/2026/edu_hub/edu_hub/public/notices.php">Notices</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="/2026/edu_hub/edu_hub/public/gallery.php">Gallery</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="#">Forms</a>
                </li>
                <li class="gov-navbar-item" role="none">
                    <a class="gov-navbar-link" role="menuitem" href="/2026/edu_hub/edu_hub/public/contact.php">Contact</a>
                </li>

                <?php if ($student_logged_in): ?>
                    <li class="gov-navbar-item ms-auto">
                        <a class="gov-navbar-link" href="/2026/edu_hub/edu_hub/student_dashboard.php">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($student_name) ?>
                        </a>
                    </li>
                    <li class="gov-navbar-item">
                        <a class="gov-navbar-link" href="/2026/edu_hub/edu_hub/student_logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="gov-navbar-item ms-auto">
                        <a class="gov-navbar-link" href="/2026/edu_hub/edu_hub/student_login_signup.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
</nav>