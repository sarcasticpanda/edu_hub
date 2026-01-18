<?php
// Student Login/Signup Page (Email/Google)
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$googleClientID = GOOGLE_CLIENT_ID;
$googleRedirectUri = GOOGLE_REDIRECT_URI;
$googleClient = new Google_Client();
$googleClient->setClientId($googleClientID);
$googleClient->setRedirectUri($googleRedirectUri);
$googleClient->addScope('email');
$googleClient->addScope('profile');
$googleClient->setAccessType('offline');
$googleClient->setPrompt('select_account');
$googleAuthUrl = $googleClient->createAuthUrl();

$msg = '';
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
$pdo = new PDO($dsn, $user, $pass, $options);

// Handle email login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $return_url = $_POST['return_url'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND otp_verified = 1");
    $stmt->execute([$email]);
    $student = $stmt->fetch();
    if ($student && password_verify($password, $student['password_hash'])) {
        $_SESSION['student_email'] = $email;
        $_SESSION['student_name'] = $student['full_name'] ?? explode('@', $email)[0];
        $_SESSION['student_id'] = $student['id'] ?? null;
        
        // Redirect to return URL if provided, otherwise to dashboard
        if (!empty($return_url) && strpos($return_url, 'localhost') !== false) {
            header('Location: ' . $return_url);
        } else {
            header('Location: student_dashboard.php');
        }
        exit;
    } else {
        $msg = 'Invalid credentials or email not verified.';
    }
}
// Redirect if already logged in
if (isset($_SESSION['student_email'])) {
    header('Location: student_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login/Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,42,68,0.10); padding: 32px 28px; }
        .login-title { text-align: center; margin-bottom: 24px; color: var(--color-red); }
        .google-btn { width: 100%; margin-bottom: 18px; border-radius: 6px; border: 1px solid #e0e0e0; background: #fff; transition: box-shadow 0.2s; }
        .google-btn:hover { box-shadow: 0 2px 8px rgba(66,133,244,0.15); }
        .or-divider { text-align: center; margin: 18px 0; color: #888; font-weight: 600; }
        .msg { margin: 18px 0; padding: 10px; border-radius: 6px; background: #f0f4ff; color: #1E2A44; font-weight: 500; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Student Login / Signup</h2>
        <?php if ($msg): ?><div class="msg"> <?= htmlspecialchars($msg) ?> </div><?php endif; ?>
        <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn google-btn">
            <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" style="width:100%;max-width:220px;display:block;margin:auto;">
        </a>
        <div class="or-divider">or</div>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="or-divider">New user?</div>
        <a href="student_email_register.php" class="btn btn-secondary w-100">Sign up with Email</a>
    </div>
</body>
</html>
