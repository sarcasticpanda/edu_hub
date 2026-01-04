<?php
session_start();
// DB connection
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
    $pdo = null;
}
$about = $pdo ? $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch() : null;
$details = $pdo ? $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR) : [];
$leadership = $pdo ? $pdo->query("SELECT * FROM leadership ORDER BY created_at DESC")->fetchAll() : [];

// Group leadership by section
$sections = [
    'Individual' => [],
    'Primary' => [],
    'Junior' => [],
    'Senior' => [],
    'Non-Teaching' => []
];
foreach ($leadership as $l) {
    if (isset($sections[$l['section']])) {
        $sections[$l['section']][] = $l;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Telangana School/College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-teal: #1abc9c;
            --secondary-blue: #00539C;
            --accent-red: #D32F2F;
            --light-gray: #f7f7fa;
            --dark-gray: #232e47;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Open Sans', sans-serif;
        }
        .about-reveal-container {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto 60px auto;
            padding: 0 16px;
        }
        .about-reveal-block {
            opacity: 0;
            transform: translateY(60px);
            filter: blur(8px);
            transition: opacity 1.2s cubic-bezier(.22,1,.36,1), transform 1.2s cubic-bezier(.22,1,.36,1), filter 1.2s cubic-bezier(.22,1,.36,1);
            margin-bottom: 70px;
            width: 100%;
        }
        .about-reveal-block.visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
        .about-reveal-block.left-align {
            text-align: left;
            margin-left: 0;
            margin-right: auto;
            max-width: 600px;
        }
        .about-reveal-block.right-align {
            text-align: right;
            margin-left: auto;
            margin-right: 0;
            max-width: 600px;
        }
        .about-reveal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5em;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 1px;
            line-height: 1.1;
            color: var(--secondary-blue);
        }
        .about-reveal-title.motto { color: var(--primary-teal); }
        .about-reveal-title.objectives { color: var(--accent-red); }
        .about-reveal-title.values { color: var(--primary-teal); }
        .about-reveal-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25em;
            color: var(--dark-gray);
            font-weight: 500;
            line-height: 1.6;
            margin: 0;
            white-space: pre-line;
        }
        .leadership-heading-dominant {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 2.8rem;
            line-height: 1.2;
            margin-bottom: 3rem;
            color: #1E2A44;
            text-transform: capitalize;
            position: relative;
        }
        .leadership-heading-dominant::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: #00539C;
            border-radius: 2px;
        }
        .leadership-management {
            max-width: 1300px;
            margin: 0 auto;
            padding: 50px 30px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            border: none;
        }
        .leadership-card {
            display: block;
            width: 100%;
            height: 380px;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            position: relative;
        }
        .leadership-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.02) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        .leadership-card:hover::before {
            opacity: 1;
        }
        .leadership-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            filter: grayscale(0%);
        }
        .leadership-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.06);
        }
        .leadership-card:hover img {
            transform: scale(1.05);
        }
        .leadership-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem;
            background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.95) 30%, rgba(255,255,255,0.98) 100%);
            color: #1E2A44;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            z-index: 2;
            backdrop-filter: blur(8px);
        }
        .leadership-overlay .name {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            letter-spacing: 0.3px;
            line-height: 1.3;
            color: #1E2A44;
        }
        .leadership-overlay .role {
            font-family: 'Open Sans', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            color: #00539C;
            letter-spacing: 0.2px;
        }
        .gallery-modal .modal-dialog {
            max-width: 900px;
        }
        .gallery-modal .modal-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1060;
            max-height: 80vh;
            overflow-y: auto;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
            justify-content: center;
            position: relative;
            z-index: 1061;
        }
        .gallery-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            height: 260px;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }
        .gallery-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.06);
        }
        .gallery-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-card:hover img {
            transform: scale(1.08);
        }
        .gallery-card-content {
            padding: 1.2rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }
        .gallery-card-content h5 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1E2A44;
            margin-bottom: 0.4rem;
            letter-spacing: 0.2px;
        }
        .gallery-card-content p {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #00539C;
            margin: 0;
            letter-spacing: 0.2px;
        }
        .leadership-modal .modal-content {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            text-align: center;
            overflow: hidden;
        }
        .leadership-modal .modal-header {
            background: #f8f9fa;
            color: #1E2A44;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
        }
        .leadership-modal .modal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #1E2A44;
        }
        .leadership-modal .modal-body {
            background: #ffffff;
            padding: 2.5rem;
        }
        .leadership-modal .modal-details {
            max-width: 550px;
            margin: 0 auto;
        }
        .leadership-modal h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.2rem;
            color: #1E2A44;
            margin-bottom: 1rem;
            letter-spacing: 0.3px;
        }
        .leadership-modal p {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.05rem;
            color: #495057;
            line-height: 1.6;
            margin-bottom: 0.8rem;
        }
        .leadership-modal p strong {
            color: #1E2A44;
            font-weight: 600;
        }
        .leadership-modal img {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .leadership-management {
                padding: 16px;
            }
            .leadership-card {
                width: 200px;
                height: 200px;
                margin-right: 16px;
            }
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            .gallery-card {
                height: 220px;
            }
            .gallery-card img {
                height: 140px;
            }
        }
    /* overrides */
        .leadership-management {
            background: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            gap: 40px !important;
        }
        .achievements-heading {
            font-family: 'Poppins', sans-serif;
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #D32F2F;
            text-shadow: 0 2px 8px rgba(211,47,47,0.08);
        }
        .achievement-card {
            background: #fff9f6;
            border-left: 6px solid #D32F2F;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(211,47,47,0.08);
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .achievement-card:hover {
            box-shadow: 0 8px 32px rgba(211,47,47,0.18);
            transform: scale(1.03);
        }
        .achievement-card p {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #D32F2F !important;
            margin-bottom: 0.5rem;
        }
        .achievement-card i {
            font-size: 1.7rem !important;
            color: #D32F2F !important;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body class="font-open-sans text-gray-800 bg-gray-50" style="background: linear-gradient(135deg, #f0f4f8 0%, #ffffff 100%);">
    <?php include 'navbar.php'; ?>
    <main style="margin-top: 60px; flex: 1;">
        <div class="site-zoom-wrapper" style="margin-left: auto; margin-right: auto;">
            <section class="container py-5 text-center mx-auto" style="width: 100%;">
                <div class="mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.15rem; background: linear-gradient(90deg, var(--primary-teal) 40%, var(--accent-red) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-fill-color: transparent; letter-spacing: 2px; text-transform: uppercase; display: inline-block;">Know Everything... About Us</div>
                <h1 class="mb-2" style="font-family: 'Poppins', sans-serif; font-size: 2.5rem; font-weight: 700; color: var(--dark-gray);">
                    <span style="color: #D32F2F;">About</span> <span style="color: var(--dark-gray);"><?= htmlspecialchars($about['page_title'] ?? 'Your School Name') ?></span>
                </h1>
                <div class="mb-4" style="font-size: 1.15rem; color: #444; font-family: 'Roboto', sans-serif; font-weight: 500; max-width: 700px; margin: 0 auto; background: transparent; padding: 1rem; border-radius: 8px;">
                    <?= nl2br(htmlspecialchars($about['page_content'] ?? 'Empowering Excellence, Fostering Growth. Your School Name provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.')) ?>
                </div>
                <?php if (!empty($about['image_path'])): ?>
                    <img src="<?= htmlspecialchars($about['image_path']) ?>" alt="About Image" style="width: 70%; max-width: 900px; border-radius: 18px; box-shadow: 0 4px 24px rgba(30,42,68,0.13); margin: 0 auto; display: block; border: 4px solid rgba(26, 188, 156, 0.2);">
                <?php else: ?>
                <img src="../images/bitcblog1.jpg" alt="St. Xavier's College Campus" style="width: 70%; max-width: 900px; border-radius: 18px; box-shadow: 0 4px 24px rgba(30,42,68,0.13); margin: 0 auto; display: block; border: 4px solid rgba(26, 188, 156, 0.2);">
                <?php endif; ?>
            </section>
            <section class="container py-5 text-center">
                <div class="mov-section about-reveal-container">
                    <div class="about-reveal-block left-align">
                        <div class="about-reveal-title motto">Motto</div>
                        <p class="about-reveal-text"><?= htmlspecialchars($details['motto'] ?? 'Motto not set.') ?></p>
                    </div>
                    <div class="about-reveal-block right-align">
                        <div class="about-reveal-title objectives">Objectives</div>
                        <p class="about-reveal-text"><?= htmlspecialchars($details['objective'] ?? 'Objectives not set.') ?></p>
                    </div>
                    <div class="about-reveal-block left-align">
                        <div class="about-reveal-title values">Values</div>
                        <p class="about-reveal-text"><?= htmlspecialchars($details['value'] ?? 'Values not set.') ?></p>
                    </div>
                </div>
            </section>
            <section class="container py-5 text-center" id="leadership">
                <h2 class="leadership-heading-dominant">Leadership Management</h2>
                <div class="leadership-management">
                    <?php foreach ($sections['Individual'] as $leader): ?>
                        <div class="leadership-card" data-leader-modal="#leaderModal<?= $leader['id'] ?>">
                            <img src="<?= htmlspecialchars($leader['image_path']) ?>" alt="<?= htmlspecialchars($leader['name']) ?>" onerror="this.onerror=null;this.src='../images/default_leader.png';">
                            <div class="leadership-overlay">
                                <div class="name"><?= htmlspecialchars($leader['name']) ?></div>
                                <div class="role"><?= htmlspecialchars($leader['role']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ([
                        'Primary' => 'Primary',
                        'Junior' => 'Junior',
                        'Senior' => 'Senior',
                        'Non-Teaching' => 'NonTeaching'
                    ] as $sectionKey => $modalKey):
                        if (!empty($sections[$sectionKey])): ?>
                            <div class="leadership-card" data-gallery-modal="#<?= $modalKey ?>GalleryModal">
                                <img src="<?= htmlspecialchars($sections[$sectionKey][0]['image_path']) ?>" alt="<?= htmlspecialchars($sectionKey) ?> Gallery" onerror="this.onerror=null;this.src='../images/default_leader.png';">
                                <div class="leadership-overlay">
                                    <div class="name"><?= htmlspecialchars($sectionKey) ?> Section</div>
                                    <div class="role">View All</div>
                        </div>
                    </div>
                        <?php endif; endforeach; ?>
                </div>

                <!-- Gallery Modals -->
                <?php foreach (['Primary' => 'Primary', 'Junior' => 'Junior', 'Senior' => 'Senior', 'Non-Teaching' => 'NonTeaching'] as $sectionKey => $modalKey):
                    if (!empty($sections[$sectionKey])): ?>
                        <div class="modal fade gallery-modal" id="<?= $modalKey ?>GalleryModal" tabindex="-1" aria-labelledby="<?= $modalKey ?>GalleryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="<?= $modalKey ?>GalleryModalLabel"><?= htmlspecialchars($sectionKey) ?> Section</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="gallery-grid">
                                            <?php foreach ($sections[$sectionKey] as $leader): ?>
                                                <div class="gallery-card" data-leader-modal="#leaderModal<?= $leader['id'] ?>">
                                                    <img src="<?= htmlspecialchars($leader['image_path']) ?>" alt="<?= htmlspecialchars($leader['name']) ?>" onerror="this.onerror=null;this.src='../images/default_leader.png';">
                                                    <div class="gallery-card-content">
                                                        <h5><?= htmlspecialchars($leader['name']) ?></h5>
                                                        <p><?= htmlspecialchars($leader['role']) ?></p>
                        </div>
                    </div>
                                            <?php endforeach; ?>
                        </div>
                    </div>
                        </div>
                    </div>
                        </div>
                    <?php endif; endforeach; ?>

                <!-- Leader Modals -->
                <?php foreach ($leadership as $leader): ?>
                    <div class="modal fade leadership-modal" id="leaderModal<?= $leader['id'] ?>" tabindex="-1" aria-labelledby="leaderModalLabel<?= $leader['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leaderModalLabel<?= $leader['id'] ?>"><?= htmlspecialchars($leader['name']) ?> - <?= htmlspecialchars($leader['role']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col-12 col-md-5 text-center mb-3 mb-md-0">
                                                <img src="<?= htmlspecialchars($leader['image_path']) ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="img-fluid rounded shadow leader-modal-img" onerror="this.onerror=null;this.src='../images/default_leader.png';">
                                            </div>
                                            <div class="col-12 col-md-7 text-start">
                                                <div class="modal-details">
                                                    <h5><?= htmlspecialchars($leader['name']) ?></h5>
                                                    <?php
                                                    $details = json_decode($leader['modal_content'], true) ?: [];
                                                    foreach ($details as $key => $value): ?>
                                                        <p><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong> <?= htmlspecialchars($value) ?></p>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
            <!-- Achievements Section -->
            <section class="container py-5">
                <h2 class="achievements-heading text-center mb-5">Our Achievements</h2>
                <div class="row justify-content-center">
                    <?php
                    $achievements = $pdo ? $pdo->query("SELECT * FROM achievements ORDER BY created_at DESC LIMIT 6")->fetchAll() : [];
                    ?>
                    <?php if (empty($achievements)): ?>
                        <div class="alert alert-info">No achievements found.</div>
                    <?php else: ?>
                        <?php foreach ($achievements as $achievement): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card achievement-card p-4 text-center border-0" style="min-height:120px;">
                                <p class="mb-0" style="font-size:1.3rem; font-weight:600; color:#D32F2F;">
                                    <i class="<?= htmlspecialchars($achievement['icon']) ?> me-2" style="color:#2ec4b6; font-size:2rem;"></i>
                                    <?= htmlspecialchars_decode($achievement['title']) ?>
                                </p>
                                <?php if (!empty($achievement['description'])): ?>
                                    <div class="text-muted small mt-2" style="font-size:1rem; color:#888;">
                                        <?= htmlspecialchars($achievement['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reveal animations
            const blocks = document.querySelectorAll('.about-reveal-block');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });
            blocks.forEach(block => observer.observe(block));

            const cards = document.querySelectorAll('.leadership-card');
            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.25 });
            cards.forEach(card => cardObserver.observe(card));

            // Modal logic for all leadership cards
            let currentlyOpenModal = null;
            document.querySelectorAll('.leadership-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const galleryModalSelector = card.getAttribute('data-gallery-modal');
                    const leaderModalSelector = card.getAttribute('data-leader-modal');
                    let targetModalSelector = galleryModalSelector || leaderModalSelector;
                    if (!targetModalSelector) return;
                    // Robust: Only close and wait if a modal is actually open
                    if (currentlyOpenModal instanceof HTMLElement) {
                        // Preserve reference to the previously open modal to avoid mutation issues
                        const previousModal = currentlyOpenModal;
                        previousModal.addEventListener('hidden.bs.modal', function handler() {
                            // Detach the listener from the correct modal element
                            previousModal.removeEventListener('hidden.bs.modal', handler);
                            const nextModal = document.querySelector(targetModalSelector);
                            if (nextModal) {
                                bootstrap.Modal.getOrCreateInstance(nextModal).show();
                                currentlyOpenModal = nextModal;
                            }
                                }, { once: true });

                        // Safely obtain or create the bootstrap modal instance before hiding
                        const prevInstance = bootstrap.Modal.getInstance(previousModal) || bootstrap.Modal.getOrCreateInstance(previousModal);
                        prevInstance.hide();
                    } else {
                        // If no modal is open, just open the target modal
                        const modal = document.querySelector(targetModalSelector);
                        if (modal) {
                            bootstrap.Modal.getOrCreateInstance(modal).show();
                            currentlyOpenModal = modal;
                        }
                    }
                });
            });
            // Track modal open/close
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('show.bs.modal', function() {
                    currentlyOpenModal = modal;
                });
                modal.addEventListener('hidden.bs.modal', function() {
                    if (currentlyOpenModal === modal) currentlyOpenModal = null;
                });
            });

            // Gallery card click: close gallery modal, then open leader modal
            document.querySelectorAll('.gallery-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetModalSelector = card.getAttribute('data-leader-modal');
                    const parentModal = card.closest('.modal');
                    if (parentModal) {
                        const parentModalInstance = bootstrap.Modal.getInstance(parentModal);
                        parentModal.addEventListener('hidden.bs.modal', function handler() {
                            const leaderModal = document.querySelector(targetModalSelector);
                            if (leaderModal) {
                                bootstrap.Modal.getOrCreateInstance(leaderModal).show();
                            }
                            parentModal.removeEventListener('hidden.bs.modal', handler);
                        }, { once: true });
                        parentModalInstance.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>