<?php
session_start();
require_once 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .login-box { max-width: 350px; margin: 80px auto; background: #fff; padding: 32px 24px; border-radius: 10px; box-shadow: 0 4px 24px #0001; }
        h2 { text-align: center; color: #1E2A44; }
        input[type=email], input[type=password] { width: 100%; padding: 10px; margin: 10px 0 18px 0; border: 1px solid #ccc; border-radius: 6px; }
        button { width: 100%; background: #D32F2F; color: #fff; border: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 1.1em; }
        .error { color: #D32F2F; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html> 