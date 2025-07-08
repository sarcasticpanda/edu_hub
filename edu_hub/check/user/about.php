<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Telangana School/College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
        }
        footer {
            margin-bottom: 0;
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
        .leadership-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px 24px;
            justify-content: space-between;
            align-items: stretch;
            margin: 0 auto;
            max-width: 1300px;
            padding: 24px;
            background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
            border-radius: 16px;
        }
        .leadership-img-card {
            transition: transform 0.6s cubic-bezier(0.33, 1, 0.68, 1), box-shadow 0.6s cubic-bezier(0.33, 1, 0.68, 1), opacity 0.6s cubic-bezier(0.33, 1, 0.68, 1), filter 0.6s cubic-bezier(0.33, 1, 0.68, 1);
            width: 100%;
            max-width: 340px;
            min-width: 220px;
            height: 240px;
            border-radius: 14px;
            overflow: hidden;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 12px 0 rgba(30,42,68,0.10), 0 6px 24px 0 rgba(255,255,255,0.10);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            opacity: 0;
            transform: translateY(40px);
            filter: blur(12px);
            cursor: pointer;
            border: 2px solid rgba(26, 188, 156, 0.2);
        }
        .leadership-img-card.visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
            transition-delay: var(--stagger-delay, 0ms);
        }
        .leadership-img-card:hover {
            transform: scale(1.07) translateY(-8px);
            box-shadow: 0 16px 48px rgba(30,42,68,0.22), 0 2px 16px rgba(0,0,0,0.10);
            z-index: 3;
            filter: brightness(1.04) saturate(1.08);
            border-color: var(--primary-teal);
        }
        .leadership-overlay {
            background: linear-gradient(180deg, rgba(0,0,0,0.01) 60%, rgba(0,83,156,0.75) 100%);
            padding: 0.7rem 1.2rem 0.7rem 1.2rem;
            left: 0;
            bottom: 0;
            top: unset !important;
            right: unset !important;
            width: 100%;
            height: auto;
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-end;
        }
        .leadership-overlay .name {
            color: #f8f9fa;
            font-weight: 800;
            font-size: 1.13rem;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 0.1rem;
            text-shadow: 0 2px 8px #000a;
        }
        .leadership-overlay .role {
            color: #eaeaea;
            font-weight: 700;
            font-size: 1.01rem;
            font-family: 'Montserrat', sans-serif;
            text-shadow: 0 2px 8px #000a;
        }
        .non-teaching-card {
            background: var(--light-gray);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(30,42,68,0.13);
            max-width: 1300px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .leadership-heading-dominant {
            font-family: 'Montserrat', 'Poppins', Arial, sans-serif;
            font-weight: 900;
            letter-spacing: 2px;
            font-size: 3.2rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--primary-teal) 40%, var(--accent-red) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gallery-modal .modal-content {
            background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%);
            border: 2px solid rgba(26, 188, 156, 0.1);
        }
        .gallery-modal .modal-header {
            background: var(--secondary-blue);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gallery-modal .modal-body {
            padding: 1.5rem;
            background: #fff;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            justify-content: center;
        }
        .gallery-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(30,42,68,0.10);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 260px;
            display: flex;
            flex-direction: column;
            border: 2px solid rgba(26, 188, 156, 0.15);
        }
        .gallery-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.3s;
            background: linear-gradient(45deg, var(--light-gray), #ffffff);
        }
        .gallery-card:hover img {
            transform: scale(1.05);
        }
        .gallery-card-content {
            padding: 1rem;
            text-align: left;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, #ffffff 70%, var(--light-gray) 100%);
        }
        .gallery-card-content h5 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }
        .gallery-card-content p {
            font-size: 0.95rem;
            color: #555;
            margin: 0;
        }
        .info-modal .modal-content {
            background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%);
            border: 2px solid rgba(26, 188, 156, 0.1);
            z-index: 1060;
        }
        .info-modal .modal-header {
            background: var(--secondary-blue);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .info-modal .modal-body {
            background: #fff;
        }
        .modal-backdrop {
            z-index: 1050;
        }
        .gallery-modal {
            z-index: 1055;
        }
        .info-modal {
            z-index: 1060;
        }
        @media (max-width: 768px) {
            .leadership-gallery {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
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
        .achievement-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(30,42,68,0.10);
            padding: 2rem 1rem;
            transition: box-shadow 0.3s, transform 0.3s;
        }
        .achievement-card:hover {
            box-shadow: 0 8px 32px rgba(211,47,47,0.18), 0 2px 8px rgba(30,42,68,0.10);
            transform: scale(1.05);
        }
        .achievements-heading {
            font-size: 2.7rem;
            font-weight: 1000;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #20c997 0%, #D32F2F 70%, #ff1744 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            margin-bottom: 2.5rem;
            font-family: 'Poppins', 'Montserrat', Arial, sans-serif;
        }
    </style>
</head>
<body class="font-open-sans text-gray-800 bg-gray-50" style="background: linear-gradient(135deg, #f0f4f8 0%, #ffffff 100%);">
    <?php include 'navbar.php'; ?>
    <main style="margin-top: 60px; flex: 1;">
        <div class="site-zoom-wrapper" style="transform: scale(0.96); transform-origin: top center; margin-left: auto; margin-right: auto;">
            <section class="container py-5 text-center mx-auto" style="width: 100%;">
                <div class="mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.15rem; background: linear-gradient(90deg, var(--primary-teal) 40%, var(--accent-red) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-fill-color: transparent; letter-spacing: 2px; text-transform: uppercase; display: inline-block;">Know Everything... About Us</div>
                <h1 class="mb-2" style="font-family: 'Poppins', sans-serif; font-size: 2.5rem; font-weight: 700; color: var(--dark-gray);">
                    <span style="color: #D32F2F;">About</span> <span style="color: var(--dark-gray);"><?= htmlspecialchars($school_info['title'] ?? 'Your School Name') ?></span>
                </h1>
                <div class="mb-4" style="font-size: 1.15rem; color: #444; font-family: 'Roboto', sans-serif; font-weight: 500; max-width: 700px; margin: 0 auto; background: transparent; padding: 1rem; border-radius: 8px;">
                    <?= 'Empowering Excellence, Fostering Growth. ' . htmlspecialchars($school_info['title'] ?? 'Your School Name') . ' provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.' ?>
                </div>
                <img src="../images/bitcblog1.jpg" alt="St. Xavier's College Campus" style="width: 70%; max-width: 900px; border-radius: 18px; box-shadow: 0 4px 24px rgba(30,42,68,0.13); margin: 0 auto; display: block; border: 4px solid rgba(26, 188, 156, 0.2);">
            </section>
            <section class="container py-5 text-center">
                <div class="mov-section about-reveal-container">
                    <div class="about-reveal-block left-align">
                        <div class="about-reveal-title motto">Motto</div>
                        <p class="about-reveal-text">Empowering individuals to achieve excellence and inspiring growth in every endeavor.</p>
                    </div>
                    <div class="about-reveal-block right-align">
                        <div class="about-reveal-title objectives">Objectives</div>
                        <p class="about-reveal-text">
Foster academic and personal excellence.  Encourage innovation and critical thinking.  Promote holistic development.
Build leadership and teamwork skills.  Engage in community and social responsibility.
                        </p>
                    </div>
                    <div class="about-reveal-block left-align">
                        <div class="about-reveal-title values">Values</div>
                        <p class="about-reveal-text">
Integrity & Honesty.  Respect & Inclusivity.  Excellence & Innovation.
Responsibility & Service.  Lifelong Learning.
                        </p>
                    </div>
                </div>
            </section>
            <section class="container py-5 text-center">
                <h2 class="leadership-heading-dominant">Leadership</h2>
                <div class="leadership-gallery">
                    <!-- Principal -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#principalModal">
                        <img src="../images/cm.jpeg" alt="Principal" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Dr. John Doe</div>
                            <div class="role">Principal</div>
                        </div>
                    </div>
                    <!-- Vice Principal -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#vicePrincipalModal">
                        <img src="../images/edu.jpeg" alt="Vice Principal" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Ms. Priya Sharma</div>
                            <div class="role">Vice Principal</div>
                        </div>
                    </div>
                    <!-- Coordinator -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#coordinatorModal">
                        <img src="../images/bitcblog1.jpg" alt="Coordinator" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Mr. Ravi Kumar</div>
                            <div class="role">Coordinator</div>
                        </div>
                    </div>
                    <!-- Primary -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#primaryGalleryModal">
                        <img src="../images/flag.jpeg" alt="Primary" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Primary</div>
                            <div class="role">Grades 1-5</div>
                        </div>
                    </div>
                    <!-- Junior -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#juniorGalleryModal">
                        <img src="../images/school.png" alt="Junior" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Junior</div>
                            <div class="role">Grades 6-8</div>
                        </div>
                    </div>
                    <!-- Senior -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#seniorGalleryModal">
                        <img src="../images/161024-duke-university-submitted.jpg" alt="Senior" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Senior</div>
                            <div class="role">Grades 9-12</div>
                        </div>
                    </div>
                    <!-- Non-Teaching Staff -->
                    <div class="leadership-img-card" data-bs-toggle="modal" data-bs-target="#nonTeachingGalleryModal">
                        <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="leadership-overlay position-absolute bottom-0 start-0">
                            <div class="name">Non-Teaching Staff</div>
                            <div class="role">Support Team</div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Achievements Section -->
            <section class="container py-5">
                <h2 class="achievements-heading text-center mb-5">Our Achievements</h2>
                <div class="row justify-content-center">
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
                        $pdo = null;
                    }
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

    <!-- Individual Modals -->
    <div class="modal fade" id="principalModal" tabindex="-1" aria-labelledby="principalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%); border: 2px solid rgba(26, 188, 156, 0.1);">
                <div class="modal-header" style="background: var(--secondary-blue); color: #fff; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 class="modal-title" id="principalModalLabel">Dr. John Doe - Principal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background: #fff;">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/cm.jpeg" alt="Dr. John Doe" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Dr. John Doe</h5>
                                    <p class="mb-1"><strong>Name:</strong> Dr. John Doe</p>
                                    <p class="mb-1"><strong>Designation:</strong> Principal</p>
                                    <p class="mb-1"><strong>Years Worked:</strong> 15 years</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> john.doe@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="vicePrincipalModal" tabindex="-1" aria-labelledby="vicePrincipalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%); border: 2px solid rgba(26, 188, 156, 0.1);">
                <div class="modal-header" style="background: var(--secondary-blue); color: #fff; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 class="modal-title" id="vicePrincipalModalLabel">Ms. Priya Sharma - Vice Principal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background: #fff;">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/edu.jpeg" alt="Ms. Priya Sharma" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Ms. Priya Sharma</h5>
                                    <p class="mb-1"><strong>Name:</strong> Ms. Priya Sharma</p>
                                    <p class="mb-1"><strong>Designation:</strong> Vice Principal</p>
                                    <p class="mb-1"><strong>Years Worked:</strong> 10 years</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> priya.sharma@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="coordinatorModal" tabindex="-1" aria-labelledby="coordinatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%); border: 2px solid rgba(26, 188, 156, 0.1);">
                <div class="modal-header" style="background: var(--secondary-blue); color: #fff; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 class="modal-title" id="coordinatorModalLabel">Mr. Ravi Kumar - Coordinator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background: #fff;">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/bitcblog1.jpg" alt="Mr. Ravi Kumar" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Mr. Ravi Kumar</h5>
                                    <p class="mb-1"><strong>Name:</strong> Mr. Ravi Kumar</p>
                                    <p class="mb-1"><strong>Designation:</strong> Coordinator</p>
                                    <p class="mb-1"><strong>Years Worked:</strong> 8 years</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> ravi.kumar@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sophisticated Gallery Modals -->
    <div class="modal fade gallery-modal" id="primaryGalleryModal" tabindex="-1" aria-labelledby="primaryGalleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="primaryGalleryModalLabel">Primary Section - Grades 1-5</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="gallery-grid">
                        <div class="gallery-card">
                            <img src="../images/flag.jpeg" alt="Primary Student 1" data-bs-toggle="modal" data-bs-target="#primaryInfoModal1">
                            <div class="gallery-card-content">
                                <h5>Primary Student 1</h5>
                                <p>Grade 2</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/flag.jpeg" alt="Primary Student 2" data-bs-toggle="modal" data-bs-target="#primaryInfoModal2">
                            <div class="gallery-card-content">
                                <h5>Primary Student 2</h5>
                                <p>Grade 3</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/flag.jpeg" alt="Primary Student 3" data-bs-toggle="modal" data-bs-target="#primaryInfoModal3">
                            <div class="gallery-card-content">
                                <h5>Primary Student 3</h5>
                                <p>Grade 4</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade gallery-modal" id="juniorGalleryModal" tabindex="-1" aria-labelledby="juniorGalleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="juniorGalleryModalLabel">Junior Section - Grades 6-8</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="gallery-grid">
                        <div class="gallery-card">
                            <img src="../images/school.png" alt="Junior Student 1" data-bs-toggle="modal" data-bs-target="#juniorInfoModal1">
                            <div class="gallery-card-content">
                                <h5>Junior Student 1</h5>
                                <p>Grade 6</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/school.png" alt="Junior Student 2" data-bs-toggle="modal" data-bs-target="#juniorInfoModal2">
                            <div class="gallery-card-content">
                                <h5>Junior Student 2</h5>
                                <p>Grade 7</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/school.png" alt="Junior Student 3" data-bs-toggle="modal" data-bs-target="#juniorInfoModal3">
                            <div class="gallery-card-content">
                                <h5>Junior Student 3</h5>
                                <p>Grade 8</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade gallery-modal" id="seniorGalleryModal" tabindex="-1" aria-labelledby="seniorGalleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seniorGalleryModalLabel">Senior Section - Grades 9-12</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="gallery-grid">
                        <div class="gallery-card">
                            <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 1" data-bs-toggle="modal" data-bs-target="#seniorInfoModal1">
                            <div class="gallery-card-content">
                                <h5>Senior Student 1</h5>
                                <p>Grade 10</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 2" data-bs-toggle="modal" data-bs-target="#seniorInfoModal2">
                            <div class="gallery-card-content">
                                <h5>Senior Student 2</h5>
                                <p>Grade 11</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 3" data-bs-toggle="modal" data-bs-target="#seniorInfoModal3">
                            <div class="gallery-card-content">
                                <h5>Senior Student 3</h5>
                                <p>Grade 12</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade gallery-modal" id="nonTeachingGalleryModal" tabindex="-1" aria-labelledby="nonTeachingGalleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nonTeachingGalleryModalLabel">Non-Teaching Staff - Support Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="gallery-grid">
                        <div class="gallery-card">
                            <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 1" data-bs-toggle="modal" data-bs-target="#nonTeachingInfoModal1">
                            <div class="gallery-card-content">
                                <h5>Non-Teaching Staff 1</h5>
                                <p>Support Team</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 2" data-bs-toggle="modal" data-bs-target="#nonTeachingInfoModal2">
                            <div class="gallery-card-content">
                                <h5>Non-Teaching Staff 2</h5>
                                <p>Support Team</p>
                            </div>
                        </div>
                        <div class="gallery-card">
                            <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 3" data-bs-toggle="modal" data-bs-target="#nonTeachingInfoModal3">
                            <div class="gallery-card-content">
                                <h5>Non-Teaching Staff 3</h5>
                                <p>Support Team</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modals for Gallery Images -->
    <div class="modal fade info-modal" id="primaryInfoModal1" tabindex="-1" aria-labelledby="primaryInfoModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="primaryInfoModalLabel1">Primary Student 1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/flag.jpeg" alt="Primary Student 1" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Primary Student 1</h5>
                                    <p class="mb-1"><strong>Name:</strong> Primary Student 1</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 2</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> primary1@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="primaryInfoModal2" tabindex="-1" aria-labelledby="primaryInfoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="primaryInfoModalLabel2">Primary Student 2</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/flag.jpeg" alt="Primary Student 2" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Primary Student 2</h5>
                                    <p class="mb-1"><strong>Name:</strong> Primary Student 2</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 3</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> primary2@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="primaryInfoModal3" tabindex="-1" aria-labelledby="primaryInfoModalLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="primaryInfoModalLabel3">Primary Student 3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/flag.jpeg" alt="Primary Student 3" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Primary Student 3</h5>
                                    <p class="mb-1"><strong>Name:</strong> Primary Student 3</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 4</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> primary3@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade info-modal" id="juniorInfoModal1" tabindex="-1" aria-labelledby="juniorInfoModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="juniorInfoModalLabel1">Junior Student 1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/school.png" alt="Junior Student 1" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Junior Student 1</h5>
                                    <p class="mb-1"><strong>Name:</strong> Junior Student 1</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 6</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> junior1@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="juniorInfoModal2" tabindex="-1" aria-labelledby="juniorInfoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="juniorInfoModalLabel2">Junior Student 2</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/school.png" alt="Junior Student 2" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Junior Student 2</h5>
                                    <p class="mb-1"><strong>Name:</strong> Junior Student 2</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 7</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> junior2@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="juniorInfoModal3" tabindex="-1" aria-labelledby="juniorInfoModalLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="juniorInfoModalLabel3">Junior Student 3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/school.png" alt="Junior Student 3" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Junior Student 3</h5>
                                    <p class="mb-1"><strong>Name:</strong> Junior Student 3</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 8</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> junior3@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="seniorInfoModal1" tabindex="-1" aria-labelledby="seniorInfoModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seniorInfoModalLabel1">Senior Student 1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 1" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Senior Student 1</h5>
                                    <p class="mb-1"><strong>Name:</strong> Senior Student 1</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 10</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> senior1@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="seniorInfoModal2" tabindex="-1" aria-labelledby="seniorInfoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seniorInfoModalLabel2">Senior Student 2</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 2" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Senior Student 2</h5>
                                    <p class="mb-1"><strong>Name:</strong> Senior Student 2</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 11</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> senior2@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="seniorInfoModal3" tabindex="-1" aria-labelledby="seniorInfoModalLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seniorInfoModalLabel3">Senior Student 3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/161024-duke-university-submitted.jpg" alt="Senior Student 3" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Senior Student 3</h5>
                                    <p class="mb-1"><strong>Name:</strong> Senior Student 3</p>
                                    <p class="mb-1"><strong>Grade:</strong> Grade 12</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> senior3@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade info-modal" id="nonTeachingInfoModal1" tabindex="-1" aria-labelledby="nonTeachingInfoModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nonTeachingInfoModalLabel1">Non-Teaching Staff 1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 1" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Non-Teaching Staff 1</h5>
                                    <p class="mb-1"><strong>Name:</strong> Non-Teaching Staff 1</p>
                                    <p class="mb-1"><strong>Role:</strong> Support Team</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> staff1@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="nonTeachingInfoModal2" tabindex="-1" aria-labelledby="nonTeachingInfoModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nonTeachingInfoModalLabel2">Non-Teaching Staff 2</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 2" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Non-Teaching Staff 2</h5>
                                    <p class="mb-1"><strong>Name:</strong> Non-Teaching Staff 2</p>
                                    <p class="mb-1"><strong>Role:</strong> Support Team</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> staff2@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade info-modal" id="nonTeachingInfoModal3" tabindex="-1" aria-labelledby="nonTeachingInfoModalLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nonTeachingInfoModalLabel3">Non-Teaching Staff 3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" alt="Non-Teaching Staff 3" class="img-fluid rounded shadow" style="max-width: 220px; max-height: 220px; object-fit: cover; border: 2px solid rgba(26, 188, 156, 0.2);">
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="modal-details text-center text-md-start">
                                    <h5 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; text-transform: uppercase; color: var(--dark-gray);">Non-Teaching Staff 3</h5>
                                    <p class="mb-1"><strong>Name:</strong> Non-Teaching Staff 3</p>
                                    <p class="mb-1"><strong>Role:</strong> Support Team</p>
                                    <p class="mb-1"><strong>Contact Email:</strong> staff3@stxavierscollege.edu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // About section reveal
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

            // Leadership card reveal animation
            const cards = document.querySelectorAll('.leadership-img-card');
            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const index = Array.from(cards).indexOf(el);
                        el.style.setProperty('--stagger-delay', `${index * 80}ms`);
                        el.classList.add('visible');
                        cardObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.25 });
            cards.forEach(card => cardObserver.observe(card));

            // Debug modal triggers
            document.querySelectorAll('.leadership-img-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    console.log('Modal trigger clicked:', this.getAttribute('data-bs-target'));
                    e.stopPropagation();
                });
            });

            // Fallback to ensure Bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap JS not loaded. Check network tab or CDN.');
            } else {
                console.log('Bootstrap JS loaded successfully.');
            }

            // Gallery image click: manage modal transitions
            document.querySelectorAll('.gallery-card img').forEach(function(img) {
                img.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const galleryModal = img.closest('.modal');
                    const targetModalId = img.getAttribute('data-bs-target');
                    if (galleryModal && targetModalId) {
                        const galleryModalInstance = bootstrap.Modal.getInstance(galleryModal);
                        if (galleryModalInstance) {
                            galleryModalInstance.hide();
                            setTimeout(() => {
                                const infoModal = new bootstrap.Modal(document.querySelector(targetModalId));
                                infoModal.show();
                                const infoModalElement = document.querySelector(targetModalId);
                                infoModalElement.addEventListener('hidden.bs.modal', function() {
                                    galleryModalInstance.show();
                                }, { once: true });
                            }, 150);
                        }
                    }
                });
            });

            // Ensure proper modal dismissal
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.classList.remove('modal-open');
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                });
            });
        });
    </script>
</body>
</html>