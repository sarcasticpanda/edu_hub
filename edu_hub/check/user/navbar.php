<?php
// Include database connection
require_once '../admin/includes/db.php';

// Get school configuration
$school_name = getSchoolConfig('school_name', 'School Name');
$school_logo = getSchoolConfig('school_logo', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .bg-primary {
            background-color: #1E2A44 !important;
        }
        .logo-img {
            height: 44px;
            width: auto;
            border-radius: 6px;
            object-fit: contain;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            padding: 4px;
        }
        .school-name {
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
            margin-left: 10px;
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
            .logo-img { height: 32px; }
            .nav-link-custom { padding: 8px 10px; }
            .school-name { font-size: 1rem; }
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
        .navbar {
            background-color: #1E2A44;
            color: #fff;
            box-shadow: 0 4px 16px rgba(30, 42, 68, 0.3);
            transition: height 0.3s ease, box-shadow 0.3s ease;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-primary text-white shadow-lg py-3 fixed-top" id="navbar">
        <div class="container-fluid">
            <div class="navbar-brand d-flex align-items-center">
                <?php if ($school_logo): ?>
                    <img src="<?= htmlspecialchars($school_logo) ?>" alt="School Logo" class="logo-img">
                <?php endif; ?>
                <span class="school-name"><?= htmlspecialchars($school_name) ?></span>
            </div>
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="gallery.php"><i class="fas fa-images me-2"></i>Gallery</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="contact.php"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="user_portal/register_form.php"><i class="fas fa-user-plus me-2"></i>Registration</a></li>
                </ul>
                <div class="d-flex align-items-center ms-3">
                    <img src="../images/flag.jpeg" alt="Flag" class="circle-logo">
                    <img src="../images/cm.jpeg" alt="CM" class="circle-logo">
                    <img src="../images/edu.jpeg" alt="Education Minister" class="circle-logo">
                    <button id="authButton" class="btn btn-accent text-white fw-semibold ms-3" data-bs-toggle="modal" data-bs-target="#authModal">Login / Signup</button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Login/Signup Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="authModalLabel">Login / Signup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills mb-3 justify-content-center" id="authTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab" data-bs-toggle="pill" data-bs-target="#user-auth" type="button" role="tab" aria-controls="user-auth" aria-selected="true">User Login/Signup</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admin-tab" data-bs-toggle="pill" data-bs-target="#admin-auth" type="button" role="tab" aria-controls="admin-auth" aria-selected="false">Admin Login</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabContent">
                        <div class="tab-pane fade show active" id="user-auth" role="tabpanel" aria-labelledby="user-tab">
                            <ul class="nav nav-tabs mb-3 justify-content-center" id="userAuthTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="user-login-tab" data-bs-toggle="tab" data-bs-target="#user-login" type="button" role="tab" aria-controls="user-login" aria-selected="true">Login</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="user-signup-tab" data-bs-toggle="tab" data-bs-target="#user-signup" type="button" role="tab" aria-controls="user-signup" aria-selected="false">Signup</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="userAuthTabContent">
                                <div class="tab-pane fade show active" id="user-login" role="tabpanel" aria-labelledby="user-login-tab">
                                    <form>
                                        <div class="mb-3">
                                            <label for="user-login-email" class="form-label">Email address</label>
                                            <input type="email" class="form-control" id="user-login-email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user-login-password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="user-login-password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Login as User</button>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="user-signup" role="tabpanel" aria-labelledby="user-signup-tab">
                                    <form>
                                        <div class="mb-3">
                                            <label for="user-signup-email" class="form-label">Email address</label>
                                            <input type="email" class="form-control" id="user-signup-email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user-signup-password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="user-signup-password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user-signup-confirm-password" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="user-signup-confirm-password" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">Signup as User</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="admin-auth" role="tabpanel" aria-labelledby="admin-tab">
                            <form action="../admin/login.php" method="post">
                                <div class="mb-3">
                                    <label for="admin-login-email" class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" name="email" id="admin-login-email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin-login-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="admin-login-password" required>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Login as Admin</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll behavior
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.getElementById('navbar');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                if (currentScroll > lastScrollY && currentScroll > 80) {
                    navbar.classList.add('hide-navbar');
                } else {
                    navbar.classList.remove('hide-navbar');
                }
                lastScrollY = currentScroll;
            });
        });
    </script>
</body>
</html>