<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .dashboard-box { max-width: 700px; margin: 40px auto; background: #fff; padding: 32px 24px; border-radius: 10px; box-shadow: 0 4px 24px #0001; }
        h2 { text-align: center; color: #1E2A44; }
        nav { text-align: center; margin-bottom: 24px; }
        nav a { margin: 0 10px; color: #D32F2F; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .quick-edit { background: #f9f9f9; border-radius: 8px; padding: 18px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="dashboard-box">
        <h2>Admin Dashboard</h2>
        <nav>
            <a href="navbar.php">Edit Navbar Logo</a> |
            <a href="footer.php">Edit Footer</a> |
            <a href="index.php">Edit Homepage</a> |
            <a href="about.php">Edit About</a> |
            <a href="gallery.php">Edit Gallery</a> |
            <a href="whoiswho.php">Edit Who is Who</a> |
            <a href="notices.php">Edit Notices</a> |
            <a href="logout.php">Logout</a>
        </nav>
        <div class="quick-edit">
            <h3>Quick Edit: Homepage Content</h3>
            <p>(You can add a form here to quickly edit homepage content, or click "Edit Homepage" for full editing.)</p>
        </div>
    </div>
</body>
</html> 