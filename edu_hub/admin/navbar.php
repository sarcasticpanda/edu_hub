<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $target = '../images/logo_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Remove old logo if exists
            $pdo->exec("DELETE FROM navbar_logo");
            $stmt = $pdo->prepare("INSERT INTO navbar_logo (image_path) VALUES (?)");
            $stmt->execute([$target]);
            $msg = 'Logo updated!';
        } else {
            $msg = 'Upload failed!';
        }
    } else {
        $msg = 'No file uploaded!';
    }
}
$currentLogo = $pdo->query("SELECT image_path FROM navbar_logo ORDER BY id DESC LIMIT 1")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Navbar Logo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .logo-box { max-width: 400px; margin: 40px auto; background: #fff; padding: 32px 24px; border-radius: 10px; box-shadow: 0 4px 24px #0001; }
        h2 { text-align: center; color: #1E2A44; }
        img { display: block; margin: 0 auto 18px auto; max-width: 180px; border-radius: 8px; }
        form { text-align: center; }
        input[type=file] { margin-bottom: 16px; }
        button { background: #D32F2F; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: bold; }
        .msg { color: #1E2A44; text-align: center; margin-bottom: 10px; }
        a { color: #D32F2F; text-decoration: none; }
    </style>
</head>
<body>
    <div class="logo-box">
        <h2>Edit Navbar Logo</h2>
        <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($currentLogo): ?>
            <img src="<?= htmlspecialchars($currentLogo) ?>" alt="Current Logo">
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="logo" accept="image/*" required><br>
            <button type="submit">Upload New Logo</button>
        </form>
        <p style="text-align:center;"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html> 