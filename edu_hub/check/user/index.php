<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include 'navbar.php'; ?>
<?php
// Connect to the same DB as admin
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
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch dynamic content for homepage sections
$hero_title = $pdo->query("SELECT content FROM homepage_content WHERE section = 'hero' AND title = 'Hero Title' LIMIT 1")->fetchColumn();
$hero_subtitle = $pdo->query("SELECT content FROM homepage_content WHERE section = 'hero' AND title = 'Hero Subtitle' LIMIT 1")->fetchColumn();
$hero_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'hero' AND title = 'Hero Image' LIMIT 1")->fetchColumn();
$about_title = $pdo->query("SELECT content FROM homepage_content WHERE section = 'about' AND title = 'About Title' LIMIT 1")->fetchColumn();
$about_content = $pdo->query("SELECT content FROM homepage_content WHERE section = 'about' AND title = 'About Content' LIMIT 1")->fetchColumn();
$about_image = $pdo->query("SELECT image_path FROM homepage_content WHERE section = 'about' AND title = 'About Image' LIMIT 1")->fetchColumn();
$notices = $pdo->query("SELECT * FROM notices_new WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
$who_members = $pdo->query("SELECT * FROM who_is_who WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC LIMIT 12")->fetchAll();
$achievements = $pdo->query("SELECT * FROM achievements ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Fetch gallery images for homepage
$home_gallery_images = $pdo->query("SELECT image_path FROM gallery_images WHERE display_location IN ('Homepage', 'Both') ORDER BY created_at DESC LIMIT 10")->fetchAll();
// Fetch school config for branding and images
$school_config = [];
foreach ($pdo->query("SELECT config_key, config_value FROM school_config") as $row) {
    $school_config[$row['config_key']] = $row['config_value'];
}

// Fetch school info from homepage_content
$school_info = $pdo->query("SELECT * FROM homepage_content WHERE section = 'school_info' LIMIT 1")->fetch();

function render_hero_title($title) {
    $title_trim = ltrim($title);
    if (stripos($title_trim, 'WELCOME TO') === 0) {
        // Already starts with 'WELCOME TO', highlight it
        $rest = trim(substr($title_trim, strlen('WELCOME TO')));
        return '<span style="color: #D32F2F;">WELCOME TO</span> <span class="text-white">' . htmlspecialchars($rest) . '</span>';
    } else {
        return '<span style="color: #D32F2F;">WELCOME TO</span> <span class="text-white">' . htmlspecialchars($title) . '</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Xavier's College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/custom.css">
    <style>
        .bg-primary {
            background-color: #00539C !important;
        }
        .logo-img {
            height: 44px;
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
        .btn-success {
            background-color: #4CAF50 !important;
            border: none;
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
        }
        header.bg-cover {
            margin-top: 20px !important;
            min-height: 600px;
            width: 100vw;
            left: 0;
            right: 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
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
        @media (max-width: 991px) {
            .circle-logo { width: 32px; height: 32px; margin-left: 6px; margin-right: 6px; }
        }
        .notice-board-main-card {
            border-radius: 28px !important;
            max-width: 1200px;
            margin: 0 auto;
            background: #f8fafc;
            box-shadow: 0 8px 32px rgba(0,83,156,0.10), 0 1.5px 8px rgba(0,0,0,0.04);
            transition: box-shadow 0.3s, background 0.3s;
        }
        .notice-card {
            border-radius: 20px !important;
            cursor: pointer;
            transition: box-shadow 0.25s, transform 0.25s, background 0.25s;
            background: #fff;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 1.2rem 1.2rem 1.1rem 1.2rem;
            text-align: left;
            box-shadow: 0 2px 12px 0 rgba(30,42,68,0.10), 0 6px 24px 0 rgba(255,255,255,0.10);
            margin-bottom: 0.5rem;
        }
        .notice-title {
            font-family: 'Roboto Slab', 'Merriweather', 'Georgia', serif;
            font-size: 1.5rem;
            font-weight: 900;
            margin-bottom: 0.25rem;
            margin-top: -0.3rem;
            text-align: left;
            color: #D32F2F;
            letter-spacing: 0.5px;
            text-shadow: none;
            text-transform: none;
            line-height: 1.4;
        }
        .notice-subheading {
            font-family: 'Roboto', 'Poppins', Arial, sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #00539C;
            margin-bottom: 0.18rem;
            text-align: left;
            letter-spacing: 0.5px;
            opacity: 1;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .notice-preview {
            font-family: 'Segoe UI', 'Open Sans', Arial, sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1E2A44;
            text-align: left;
            opacity: 0.96;
            margin-top: 0.5rem;
            line-height: 1.5;
        }
        .notice-card:hover {
            box-shadow: 0 12px 36px rgba(0,83,156,0.18);
            transform: translateY(-3px) scale(1.025);
            background: #f3f4f6;
        }
        .modal.fade .modal-dialog {
            transition: transform 0.25s cubic-bezier(.4,2,.6,1), opacity 0.25s;
            transform: scale(0.97);
            opacity: 0.7;
        }
        .modal.fade.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }
        .modal-content {
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(0,83,156,0.13);
            border: none;
        }
        .hide-navbar {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }
        .gallery-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e8eef3 100%);
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(30,42,68,0.12), 0 2px 8px rgba(0,0,0,0.04);
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(0,83,156,0.08);
        }
        .gallery-section::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -15%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(0,83,156,0.06) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        .gallery-section::after {
            content: '';
            position: absolute;
            bottom: -25%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(211,47,47,0.05) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-15px) translateX(8px); }
        }
        .gallery-title-funky {
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            color: #1E2A44;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            text-transform: uppercase;
        }
        .gallery-title-funky::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #D32F2F 0%, #FF9933 50%, #00539C 100%);
            border-radius: 2px;
        }
        .gallery-row-wrapper {
            overflow: hidden;
            width: 100%;
            position: relative;
            margin-bottom: 2rem;
            border-radius: 20px;
            padding: 10px 0;
            z-index: 1;
        }
        .gallery-row {
            display: flex;
            width: max-content;
            animation-timing-function: linear;
            will-change: transform;
        }
        .gallery-row-1 {
            animation: scroll-left 40s linear infinite;
        }
        .gallery-row-2 {
            animation: scroll-right 45s linear infinite;
        }
        .gallery-row:hover {
            animation-play-state: paused;
        }
        .gallery-img {
            height: 220px;
            width: 380px;
            border-radius: 16px;
            margin: 0 20px;
            box-shadow: 0 4px 16px rgba(30,42,68,0.1), 0 2px 6px rgba(0,0,0,0.04);
            object-fit: cover;
            background: #f0f4f8;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid #ffffff;
            position: relative;
            cursor: pointer;
            overflow: hidden;
        }
        .gallery-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30,42,68,0.7) 0%, rgba(211,47,47,0.6) 100%);
            opacity: 0;
            transition: opacity 0.35s ease;
            z-index: 1;
        }
        .gallery-img:hover::before {
            opacity: 1;
        }
        .gallery-img::after {
            content: '\f00e';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            font-size: 2.5rem;
            color: #ffffff;
            text-shadow: 0 4px 12px rgba(0,0,0,0.4);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
        }
        .gallery-img:hover {
            transform: scale(1.08) translateY(-8px);
            box-shadow: 0 16px 48px rgba(30,42,68,0.2), 0 6px 20px rgba(0,0,0,0.08);
            border-color: #D32F2F;
            z-index: 10;
        }
        .gallery-img:hover::after {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        .gallery-img:active {
            transform: scale(1.03) translateY(-4px);
        }
        @keyframes scroll-left {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes scroll-right {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0); }
        }
        .explore-btn {
            background: linear-gradient(135deg, #D32F2F 0%, #1E2A44 100%);
            color: #fff;
            border-radius: 28px;
            padding: 14px 42px;
            font-size: 1.2rem;
            font-weight: 700;
            margin-top: 1.5rem;
            box-shadow: 0 4px 16px rgba(211,47,47,0.25), 0 2px 6px rgba(0,0,0,0.08);
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 1.2px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .explore-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #00539C 0%, #FF9933 100%);
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
        }
        .explore-btn:hover::before {
            left: 0;
        }
        .explore-btn:hover {
            color: #fff;
            box-shadow: 0 8px 32px rgba(0,83,156,0.3), 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px) scale(1.03);
            border-color: rgba(255,255,255,0.4);
        }
        .explore-btn:active {
            transform: translateY(0) scale(1);
        }
        .who-section {
            background: linear-gradient(135deg, #fff6f6 60%, #fbeaea 100%);
            border-radius: 32px;
            box-shadow: 0 8px 32px rgba(211,47,47,0.10), 0 1.5px 8px rgba(0,0,0,0.04);
            padding: 2.5rem 1.5rem 2.5rem 1.5rem;
            margin-bottom: 2.5rem;
            position: relative;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
            overflow: hidden;
            min-height: 420px;
            margin-top: -2px;
        }
        .who-title {
            font-family: 'Montserrat', 'Poppins', Arial, sans-serif;
            font-size: 2.3rem;
            color: #D32F2F;
            font-weight: 800;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
            background: none;
            border: none;
            box-shadow: none;
            padding: 0;
            display: block;
        }
        .know-more-btn, .explore-btn {
            background: #D32F2F;
            color: #fff;
            border-radius: 24px;
            padding: 12px 36px;
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 18px;
            box-shadow: none;
            border: none;
            transition: background 0.2s, color 0.2s, transform 0.2s;
            letter-spacing: 1px;
            display: inline-block;
        }
        .know-more-btn:hover, .explore-btn:hover {
            background: #FF9933;
            color: #fff;
            transform: scale(1.05);
        }
        .who-carousel-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 2.5rem;
            width: max-content;
            animation: who-scroll 30s linear infinite;
        }
        @keyframes who-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .who-card {
            min-width: 320px;
            max-width: 340px;
            height: 300px;
            flex: 0 0 auto;
            border-radius: 36px;
            background: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            margin-bottom: 0;
            box-shadow: 0 2px 12px 0 rgba(30,42,68,0.10), 0 6px 24px 0 rgba(255,255,255,0.10);
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .who-card:hover {
            box-shadow: 0 8px 32px 0 rgba(30,42,68,0.18), 0 -12px 32px 0 rgba(255,255,255,0.18);
            transform: scale(1.06);
        }
        .who-card .who-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 1;
        }
        .who-card .who-darken {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background: rgba(30,42,68,0.38);
            z-index: 2;
            pointer-events: none;
        }
        .who-card-content {
            position: relative;
            z-index: 3;
            color: #fff;
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 1.2rem;
            text-align: left;
        }
        .who-name {
            font-size: 1.4rem;
            font-weight: 900;
            margin-bottom: 0.2rem;
            color: #fff;
            text-shadow: 0 2px 8px #0008;
        }
        .who-title-role {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #FF9933;
            text-shadow: 0 2px 8px #0008;
        }
        .who-desc {
            font-size: 1rem;
            color: #fff;
            font-weight: 700;
            text-shadow: 0 2px 8px #0008;
        }
        .who-card.red .who-title-role { color: #FF5252; }
        .who-card.blue .who-title-role { color: #2196F3; }
        .who-card.saffron .who-title-role { color: #FF9933; }
        .who-card.green .who-title-role { color: #4CAF50; }
        .who-card.purple .who-title-role { color: #8e24aa; }
        .who-card.teal .who-title-role { color: #00897b; }
        @media (max-width: 991px) {
            .who-section {
                min-height: 220px;
            }
            .who-card {
                min-width: 180px;
                max-width: 200px;
                height: 170px;
                border-radius: 20px;
            }
            .who-card-content {
                padding: 0.7rem 0.7rem 0.7rem 0.7rem;
            }
        }
        .achievements-section {
            background: linear-gradient(135deg, #f6fff6 60%, #eafbf0 100%);
            border-radius: 32px;
            box-shadow: 0 8px 32px rgba(76,175,80,0.10), 0 1.5px 8px rgba(0,0,0,0.04);
            padding: 2.5rem 1.5rem 2.5rem 1.5rem;
            margin-bottom: 2.5rem;
            position: relative;
        }
        .achievements-title {
            font-family: 'Poppins', cursive, sans-serif;
            font-size: 2.2rem;
            color: #4CAF50;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .achievement-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(76,175,80,0.10);
            padding: 1.5rem 1rem 1.2rem 1rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.25s, transform 0.25s;
            text-align: center;
            position: relative;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .achievement-card:hover {
            box-shadow: 0 8px 32px rgba(76,175,80,0.18);
            transform: translateY(-3px) scale(1.03);
        }
        .about-bold-text {
            font-family: 'Montserrat', 'Poppins', Arial, sans-serif;
            font-weight: 600;
            color: #222;
        }
        .about-main-card {
            border-radius: 32px;
            box-shadow: 0 4px 24px 0 rgba(30,42,68,0.13);
            background: #fff;
            padding: 2.2rem 2.2rem;
            margin-bottom: 2.5rem;
            width: 100%;
            max-width: 100%;
        }
        footer {
            background: #1E2A44 !important;
            color: #fff !important;
        }
        footer a {
            color: #FFD700;
            transition: color 0.2s;
        }
        footer a:hover {
            color: #FF9933;
        }
    </style>
</head>
<body class="font-open-sans text-gray-800 bg-gray-50 min-h-screen flex flex-col">
    <!-- Extended Hero Section with Photo -->
    <header class="bg-cover bg-center min-h-[600px] flex items-center mt-0 relative w-full" style="background-image: linear-gradient(to right, rgba(30, 42, 68, 0.7), rgba(31, 47, 77, 0.7)), url('<?= $hero_image ? htmlspecialchars('/seqto_edu_share/edu_hub/edu_hub/check/images/' . basename($hero_image)) : '../images/bitcblog1.jpg' ?>'); padding-top: 0;">
        <div class="container px-4 text-center animate-fade-in">
            <h1 class="text-5xl md:text-6xl font-poppins font-extrabold mb-4 drop-shadow-lg">
                <?= render_hero_title($hero_title ?? 'Your School Name') ?>
            </h1>
            <p class="text-xl md:text-2xl font-open-sans text-white mb-6 drop-shadow-md">
                <?= htmlspecialchars($hero_subtitle ?? 'Where Excellence Meets Opportunity') ?>
            </p>
            <a href="about.php" class="btn btn-primary rounded-full px-6 py-3 text-lg bg-[#F5A623] hover:bg-[#D32F2F] text-white transition-transform duration-300 hover:scale-110 animate-pulse-slow">Learn More</a>
        </div>
        <div class="absolute bottom-0 w-full h-1 bg-gradient-to-r from-[#D32F2F] to-transparent"></div>
    </header>

    <!-- Introduction and Images -->
    <section class="container px-4 py-12 bg-white text-center">
        <div class="row align-items-center justify-content-center py-4 about-main-card" style="min-height: 420px;">
            <div class="col-md-6 mb-4 mb-md-0">
                <?php if (!empty(
                    $school_config['about_image'])): ?>
                    <img src="<?= htmlspecialchars('../images/' . basename($school_config['about_image'])) ?>" alt="About Image" class="img-fluid rounded-lg shadow-lg" style="max-height: 350px; width: 95%; object-fit: cover; opacity: 1; display: block; margin: 0 auto;">
                <?php elseif (
                    $about_image): ?>
                    <img src="<?= htmlspecialchars('../images/' . basename($about_image)) ?>" alt="About Image" class="img-fluid rounded-lg shadow-lg" style="max-height: 350px; width: 95%; object-fit: cover; opacity: 1; display: block; margin: 0 auto;">
                <?php else: ?>
                    <img src="../images/bitcblog1.jpg" alt="College Campus" class="img-fluid rounded-lg shadow-lg" style="max-height: 350px; width: 95%; object-fit: cover; opacity: 1; display: block; margin: 0 auto;">
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-left">
                <h2 class="text-4xl font-poppins font-bold mb-4 animate-slide-in">
                    <span style="color: #D32F2F;">About</span> <span class="text-[#1E2A44]"><?= htmlspecialchars($about_title ?? 'Your School Name') ?></span>
                </h2>
                <p class="about-bold-text text-gray-700 leading-relaxed mb-2">
                    <?= nl2br(htmlspecialchars($about_content ?? "St. Xavier's College is a premier institution dedicated to fostering academic <span style='color: #D32F2F;'>excellence</span>, innovation, and personal growth. With a rich history and a vibrant community, we offer diverse programs and opportunities for students to thrive in a supportive environment.")) ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Notice Board -->
    <section class="container px-4 py-12 bg-[#F5F5F5] text-center">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card p-4 shadow-lg mb-4 notice-board-main-card">
                    <h2 class="text-3xl font-poppins font-bold text-[#D32F2F] mb-4 animate-slide-in">Notice Board</h2>
                    <div class="row g-3">
                        <?php if (empty($notices)): ?>
                            <div class="alert alert-info">No notices found.</div>
                        <?php else: ?>
                            <?php foreach ($notices as $notice): ?>
                            <div class="col-md-6">
                                <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal<?= $notice['id'] ?>">
                                    <h5 class="notice-title mb-2"><?= htmlspecialchars($notice['title']) ?></h5>
                                    <?php if (!empty($notice['subheading'])): ?>
                                        <div class="notice-subheading mb-2" style="font-size: 1rem; color: #00539C; font-weight: 600;"><?= htmlspecialchars($notice['subheading']) ?></div>
                                    <?php endif; ?>
                                    <div class="notice-preview text-muted" style="font-size: 0.9rem;">
                                        <?= strlen($notice['content']) > 100 ? substr(htmlspecialchars($notice['content']), 0, 100) . '...' : htmlspecialchars($notice['content']) ?>
                                    </div>
                                    <div class="notice-meta mt-2" style="font-size: 0.8rem; color: #6c757d;">
                                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($notice['posted_by']) ?>
                                        <span class="ms-2"><i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($notice['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal for full notice -->
                            <div class="modal fade" id="noticeModal<?= $notice['id'] ?>" tabindex="-1" aria-labelledby="noticeModalLabel<?= $notice['id'] ?>" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="noticeModalLabel<?= $notice['id'] ?>" style="color:#D32F2F; font-family:'Roboto Slab','Merriweather',serif; letter-spacing:0.5px;">
                                      <?= htmlspecialchars($notice['title']) ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <?php if (!empty($notice['subheading'])): ?>
                                      <div class="mb-3 text-muted" style="font-family:'Roboto',Arial,sans-serif; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; font-size:0.95rem; color:#555 !important;">
                                        <?= htmlspecialchars($notice['subheading']) ?>
                                      </div>
                                    <?php endif; ?>
                                    <div class="mb-3" style="font-family:'Segoe UI','Open Sans',Arial,sans-serif; font-size:1.05rem; color:#222; line-height:1.6;">
                                      <?= nl2br(htmlspecialchars($notice['content'])) ?>
                                    </div>
                                    <hr class="my-4">
                                    <div class="mb-2" style="font-size:0.9rem; color:#777;">
                                      <strong>Posted by:</strong> <?= htmlspecialchars($notice['posted_by']) ?> | 
                                      <strong>Date:</strong> <?= date('M d, Y', strtotime($notice['created_at'])) ?>
                                    </div>
                                    <?php if (!empty($notice['attachment_path'])): ?>
                                      <div class="mt-3 p-3 bg-light border rounded-lg d-inline-block shadow-sm w-100">
                                        <strong>Attachment:</strong>
                                        <button class="btn btn-sm btn-outline-info ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#attachmentContent<?= $notice['id'] ?>" aria-expanded="false" aria-controls="attachmentContent<?= $notice['id'] ?>">
                                            <i class="fas fa-eye me-1"></i> View Attachment
                                        </button>
                                        <div class="collapse mt-3" id="attachmentContent<?= $notice['id'] ?>">
                                            <?php if ($notice['attachment_type'] === 'pdf'): ?>
                                                <iframe src="../notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" width="100%" height="400px" style="border:none; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></iframe>
                                                <a href="../notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" target="_blank" class="btn btn-danger btn-sm mt-2"><i class="fas fa-external-link-alt me-1"></i>Open PDF in New Tab</a>
                                            <?php elseif ($notice['attachment_type'] === 'image'): ?>
                                                <img src="../notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" alt="Notice Attachment" style="max-width: 100%; max-height: 380px; border-radius: 8px; margin-top: 10px; object-fit: contain; border: 1px solid #eee; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                            <?php elseif ($notice['attachment_type'] === 'document'): ?>
                                                <p>Document available for download:</p>
                                                <a href="../notice_attachments/<?= htmlspecialchars($notice['attachment_path']) ?>" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fas fa-download me-1"></i>Download Document</a>
                                            <?php endif; ?>
                                        </div>
                                      </div>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notice Modals -->
    <div class="modal fade" id="noticeModal1" tabindex="-1" aria-labelledby="noticeModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel1">Exam Dates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: July 1, 2025</div>
                    <p>Mid-term exams: July 15-20, 2025.<br>Final exams: August 10-15, 2025.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> All students are required to check the exam schedule on the student portal. Hall tickets will be issued one week before the exams. Please contact the exam cell for any queries.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Examination Cell</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Download Schedule (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noticeModal2" tabindex="-1" aria-labelledby="noticeModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel2">Upcoming Holidays</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: June 28, 2025</div>
                    <p>Independence Day: August 15, 2025.<br>Dasara Break: October 1-5, 2025.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> The college will remain closed on the mentioned dates. Students are encouraged to participate in Independence Day celebrations on campus.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Principal Office</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Holiday Circular (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noticeModal3" tabindex="-1" aria-labelledby="noticeModalLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel3">New Admissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: June 20, 2025</div>
                    <p>Admissions for the 2025-26 academic year are open until July 31, 2025.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> Application forms are available online and at the college office. For eligibility and required documents, visit the admissions page.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Admissions Office</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Prospectus (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noticeModal4" tabindex="-1" aria-labelledby="noticeModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel4">Transport Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: June 18, 2025</div>
                    <p>Bus routes have been updated. Check the transport section for new timings.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> The new bus schedule is effective from July 5, 2025. Please check your route and timing in advance.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Transport Department</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Bus Schedule (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noticeModal5" tabindex="-1" aria-labelledby="noticeModalLabel5" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel5">Power Shutdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: June 15, 2025</div>
                    <p>Scheduled power shutdown on July 10, 2025, from 10 AM to 1 PM.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> All departments are requested to save their work and shut down computers before the scheduled time.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Maintenance Team</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Shutdown Notice (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noticeModal6" tabindex="-1" aria-labelledby="noticeModalLabel6" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeModalLabel6">Parent-Teacher Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-sm text-gray-500 mb-2">Posted: June 10, 2025</div>
                    <p>Parent-Teacher meeting scheduled for July 18, 2025, at 11 AM in the main hall.</p>
                    <hr>
                    <div class="mb-2"><strong>Details:</strong> All parents are requested to attend. Progress reports will be distributed after the meeting.</div>
                    <div class="mb-2"><strong>Posted by:</strong> Principal Office</div>
                    <div class="mb-2"><strong>Attachments:</strong> <a href="#" class="text-primary">Meeting Agenda (PDF)</a></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Section with Lightbox -->
    <section class="container px-4 py-12 gallery-section text-center">
        <h2 class="gallery-title-funky animate-slide-in">Gallery</h2>
        <div class="gallery-row-wrapper mb-4">
            <div class="gallery-row gallery-row-1 d-flex align-items-center">
                <?php foreach ($home_gallery_images as $index => $image): ?>
                    <?php if ($index % 2 == 0): // For alternating rows, take even indices for row 1 ?>
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="gallery-img" alt="Gallery Image" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ($home_gallery_images as $index => $image): ?>
                    <?php if ($index % 2 == 0): ?>
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="gallery-img" alt="Gallery Image" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="gallery-row-wrapper">
            <div class="gallery-row gallery-row-2 d-flex align-items-center">
                <?php foreach ($home_gallery_images as $index => $image): ?>
                    <?php if ($index % 2 != 0): // For alternating rows, take odd indices for row 2 ?>
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="gallery-img" alt="Gallery Image" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ($home_gallery_images as $index => $image): ?>
                    <?php if ($index % 2 != 0): ?>
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="gallery-img" alt="Gallery Image" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="gallery.php" class="explore-btn"><i class="fas fa-images me-2"></i>Explore More</a>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img class="lightbox-content" id="lightboxImg">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>

     <!-- Who is Who Section -->
     <section class="container px-4 py-12 who-section text-center position-relative">
        <h2 class="who-title">Who is Who</h2>
        <div class="who-carousel-row">
            <?php if (empty($who_members)): ?>
                <div class="alert alert-info">No team members found.</div>
            <?php else: ?>
                <?php foreach ($who_members as $member): ?>
                <div class="who-card <?= htmlspecialchars($member['color_theme']) ?>">
                    <?php
                    $display_image_path = str_replace('../check/images/', '../images/', $member['image_path']);
                    ?>
                    <div class="who-bg" style="background-image: url('<?= htmlspecialchars($display_image_path) ?>');"></div>
                    <div class="who-darken"></div>
                    <div class="who-card-content">
                        <div class="who-name"><?= htmlspecialchars($member['name']) ?></div>
                        <div class="who-title-role"><?= htmlspecialchars($member['position']) ?></div>
                        <div class="who-desc"><?= htmlspecialchars($member['description']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="about.php#leadership" class="know-more-btn mt-3"><i class="fas fa-users me-2"></i>Know More</a>
    </section>

    <!-- Additional Content: Achievements -->
    <section class="container px-4 py-12 bg-[#F5F5F5] text-center">
        <h2 class="text-3xl font-poppins font-bold text-[#D32F2F] mb-6 animate-slide-in">Our <span style="color: #D32F2F;">Achievements</span></h2>
        <div class="row justify-content-center">
            <?php if (empty($achievements)): ?>
                <div class="alert alert-info">No achievements found.</div>
            <?php else: ?>
                <?php foreach ($achievements as $achievement): ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-4 bg-white shadow-md hover:shadow-xl transition-shadow duration-300 text-center">
                        <p class="text-gray-600 flex items-center justify-center">
                            <i class="<?= htmlspecialchars($achievement['icon']) ?> me-2"></i><?= htmlspecialchars($achievement['title']) ?>
                        </p>
                        <?php if (!empty($achievement['description'])): ?>
                            <div class="text-muted small mt-2"><?= htmlspecialchars($achievement['description']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->

    <!-- Bootstrap JS and Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Lightbox Modal CSS and JavaScript -->
    <style>
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding: 40px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(30,42,68,0.96);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .lightbox-content {
            margin: auto;
            display: block;
            max-width: 85%;
            max-height: 85vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 8px 24px rgba(0,0,0,0.2);
            border: 3px solid rgba(255,255,255,0.1);
            animation: zoomIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            transition: transform 0.3s ease;
        }
        
        .lightbox-content:hover {
            transform: scale(1.02);
        }
        
        @keyframes zoomIn {
            from { 
                transform: scale(0.5); 
                opacity: 0;
            }
            to { 
                transform: scale(1); 
                opacity: 1;
            }
        }
        
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 40px;
            color: #ffffff;
            font-size: 48px;
            font-weight: 700;
            transition: all 0.3s ease;
            cursor: pointer;
            z-index: 10000;
            text-shadow: 0 4px 12px rgba(0,0,0,0.6);
            background: rgba(211,47,47,0.8);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        
        .lightbox-close:hover,
        .lightbox-close:focus {
            background: rgba(211,47,47,1);
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 4px 16px rgba(211,47,47,0.5);
        }
        
        .lightbox-caption {
            margin: auto;
            display: block;
            max-width: 80%;
            text-align: center;
            color: #fff;
            padding: 15px 0;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .lightbox-content {
                max-width: 95%;
                max-height: 70vh;
            }
            .lightbox-close {
                top: 10px;
                right: 20px;
                font-size: 35px;
            }
        }
    </style>
    
    <script>
        function openLightbox(imageSrc) {
            const modal = document.getElementById('lightboxModal');
            const modalImg = document.getElementById('lightboxImg');
            const caption = document.getElementById('lightboxCaption');
            
            modal.style.display = 'block';
            modalImg.src = imageSrc;
            caption.innerHTML = 'Gallery Image';
            
            // Prevent body scrolling
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            const modal = document.getElementById('lightboxModal');
            modal.style.display = 'none';
            
            // Restore body scrolling
            document.body.style.overflow = 'auto';
        }
        
        // Close lightbox on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLightbox();
            }
        });
        
        // Prevent closing when clicking on image
        document.getElementById('lightboxImg')?.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>