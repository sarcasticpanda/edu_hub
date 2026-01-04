<?php
// Student Email Registration with OTP
session_start();
require_once __DIR__ . '/vendor/autoload.php';

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

// Ensure students table has all required columns
$pdo->exec("CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255)
)");
// Add missing columns if not exist
$columns = $pdo->query("SHOW COLUMNS FROM students")->fetchAll(PDO::FETCH_COLUMN, 0);
if (!in_array('password_hash', $columns)) {
    $pdo->exec("ALTER TABLE students ADD COLUMN password_hash VARCHAR(255)");
}
if (!in_array('otp', $columns)) {
    $pdo->exec("ALTER TABLE students ADD COLUMN otp VARCHAR(10)");
}
if (!in_array('otp_verified', $columns)) {
    $pdo->exec("ALTER TABLE students ADD COLUMN otp_verified TINYINT(1) DEFAULT 0");
}
if (!in_array('google_id', $columns)) {
    $pdo->exec("ALTER TABLE students ADD COLUMN google_id VARCHAR(50)");
}
if (!in_array('created_at', $columns)) {
    $pdo->exec("ALTER TABLE students ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name && $email && $password) {
        // Check if email is already registered and verified
        $stmt = $pdo->prepare("SELECT otp_verified FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();
        if ($existing && $existing['otp_verified'] == 1) {
            $msg = 'This email is already registered and verified. Please login.';
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            // Insert or update student with OTP
            $stmt = $pdo->prepare("INSERT INTO students (email, name, password_hash, otp, otp_verified) VALUES (?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE name = VALUES(name), password_hash = VALUES(password_hash), otp = VALUES(otp), otp_verified = 0");
            $stmt->execute([$email, $name, $password_hash, $otp]);
            $_SESSION['pending_email'] = $email;
            $_SESSION['pending_otp'] = $otp;
            // Send OTP email
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
            require_once __DIR__ . '/config.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port = SMTP_PORT;
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email, $name);
            $mail->Subject = 'Your OTP for School Registration';
            $mail->Body = "Hello $name,\nYour OTP for registration is: $otp";
            if ($mail->send()) {
                $msg = 'OTP sent to your email. Please enter it below to verify.';
            } else {
                $msg = 'Failed to send OTP email: ' . $mail->ErrorInfo;
            }
        }
    } else {
        $msg = 'Please fill all fields.';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $otp_input = trim($_POST['otp'] ?? '');
    $email = $_SESSION['pending_email'] ?? '';
    if ($otp_input && $email) {
        $stmt = $pdo->prepare("SELECT otp FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row && $otp_input === $row['otp']) {
            $stmt = $pdo->prepare("UPDATE students SET otp_verified = 1 WHERE email = ?");
            $stmt->execute([$email]);
            $msg = 'Registration complete! You can now login.';
            unset($_SESSION['pending_email'], $_SESSION['pending_otp']);
        } else {
            $msg = 'Invalid OTP. Please try again.';
        }
    } else {
        $msg = 'Invalid OTP. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Email Registration with OTP</title>
</head>
<body>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f8fa; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,42,68,0.10); padding: 32px 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { font-weight: 600; margin-bottom: 6px; display: block; }
        .form-control { width: 100%; padding: 10px 12px; border-radius: 6px; border: 1.5px solid #e0e0e0; font-size: 1rem; }
        .form-control:focus { border-color: #1E2A44; outline: none; }
        .btn { background: #1E2A44; color: #fff; border: none; border-radius: 6px; padding: 10px 0; width: 100%; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #16305a; }
        .msg { margin: 18px 0; padding: 10px; border-radius: 6px; background: #f0f4ff; color: #1E2A44; font-weight: 500; }
    </style>
    <div class="container">
        <h2 style="text-align:center; margin-bottom: 24px;">Student Registration</h2>
        <?php if (!isset($_SESSION['pending_email'])): ?>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" type="text" name="name" id="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" type="password" name="password" id="password" placeholder="Create a password" required>
            </div>
            <button class="btn" type="submit" name="register">Register</button>
        </form>
        <?php else: ?>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="otp">Enter OTP sent to your email</label>
                <input class="form-control" type="text" name="otp" id="otp" placeholder="6-digit OTP" required>
            </div>
            <button class="btn" type="submit" name="verify">Verify OTP</button>
        </form>
        <?php endif; ?>
        <?php if ($msg): ?>
        <div class="msg"> <?= htmlspecialchars($msg) ?> </div>
        <?php endif; ?>
    </div>
</body>
</html>
