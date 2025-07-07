<?php include 'navbar.php'; ?>
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
            background: linear-gradient(135deg, #f8fafc 60%, #e0e7ef 100%);
            border-radius: 32px;
            box-shadow: 0 8px 32px rgba(0,83,156,0.10), 0 1.5px 8px rgba(0,0,0,0.04);
            padding: 2.5rem 1.5rem 2.5rem 1.5rem;
            margin-bottom: 2.5rem;
            position: relative;
        }
        .gallery-title-funky {
            font-family: 'Luckiest Guy', cursive, sans-serif;
            font-size: 2.7rem;
            color: #4CAF50;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 #00539C, 0 2px 8px #fff3;
            margin-bottom: 1.5rem;
        }
        .gallery-row-wrapper {
            overflow: hidden;
            width: 100%;
            position: relative;
            margin-bottom: 1.5rem;
        }
        .gallery-row {
            display: flex;
            width: max-content;
            animation-timing-function: linear;
        }
        .gallery-row-1 {
            animation: scroll-left 30s linear infinite;
        }
        .gallery-row-2 {
            animation: scroll-right 36s linear infinite;
        }
        .gallery-img {
            height: 180px;
            width: 320px;
            border-radius: 16px;
            margin: 0 16px;
            box-shadow: 0 2px 8px rgba(0,83,156,0.10);
            object-fit: cover;
            background: #eee;
            transition: transform 0.3s, box-shadow 0.3s, filter 0.3s;
            border: 3px solid transparent;
        }
        .gallery-img:hover {
            transform: scale(1.08) rotate(-2deg);
            box-shadow: 0 8px 32px rgba(0,83,156,0.18);
            filter: brightness(1.1) saturate(1.2);
            border: 3px solid #FF9933;
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
            background: #FF9933;
            color: #fff;
            border-radius: 24px;
            padding: 12px 36px;
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 18px;
            box-shadow: 0 2px 8px rgba(0,83,156,0.10);
            border: none;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
            letter-spacing: 1px;
        }
        .explore-btn:hover {
            background: #D32F2F;
            color: #fff;
            box-shadow: 0 8px 32px rgba(211,47,47,0.18);
            transform: scale(1.05);
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
    <header class="bg-cover bg-center min-h-[600px] flex items-center mt-0 relative w-full" style="background-image: linear-gradient(to right, rgba(30, 42, 68, 0.7), rgba(31, 47, 77, 0.7)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); padding-top: 0;">
        <div class="container px-4 text-center animate-fade-in">
            <h1 class="text-5xl md:text-6xl font-poppins font-extrabold text-white mb-4 drop-shadow-lg">WELCOME TO ST. XAVIER'S <span style="color: #D32F2F;">COLLEGE</span></h1>
            <p class="text-xl md:text-2xl font-open-sans text-white mb-6 drop-shadow-md">Where <span style="color: #D32F2F;">Excellence</span> Meets Opportunity</p>
            <a href="about.php" class="btn btn-primary rounded-full px-6 py-3 text-lg bg-[#F5A623] hover:bg-[#D32F2F] text-white transition-transform duration-300 hover:scale-110 animate-pulse-slow">Learn More</a>
        </div>
        <div class="absolute bottom-0 w-full h-1 bg-gradient-to-r from-[#D32F2F] to-transparent"></div>
    </header>

    <!-- Introduction and Images -->
    <section class="container px-4 py-12 bg-white text-center">
        <div class="row align-items-center justify-content-center py-4 about-main-card" style="min-height: 420px;">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="../images/bitcblog1.jpg" alt="College Campus" class="rounded-lg shadow-lg w-100 transition-transform duration-300 hover:scale-105" style="max-height: 400px; object-fit: cover; opacity: 1; display: block;">
            </div>
            <div class="col-md-6 text-left">
                <h2 class="text-4xl font-poppins font-bold text-[#D32F2F] mb-4 animate-slide-in">About St. Xavier's <span style="color: #D32F2F;">College</span></h2>
                <p class="about-bold-text text-gray-700 leading-relaxed mb-2">St. Xavier's College is a premier institution dedicated to fostering academic <span style="color: #D32F2F;">excellence</span>, innovation, and personal growth. With a rich history and a vibrant community, we offer diverse programs and opportunities for students to thrive in a supportive environment.</p>
                <p class="about-bold-text text-gray-700 leading-relaxed mb-2">Our campus is equipped with state-of-the-art facilities, modern classrooms, and a dynamic faculty committed to nurturing talent. We believe in holistic development, encouraging students to participate in extracurricular activities, research, and community service.</p>
                <p class="about-bold-text text-gray-700 leading-relaxed mb-2">Join us at St. Xavier's College to embark on a journey of knowledge, growth, and lifelong friendships. Discover your potential and become a part of our legacy of excellence.</p>
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
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal1">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-calendar-alt me-2"></i>Exam Dates</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: July 1, 2025</div>
                                <p class="text-gray-600 mb-0">Mid-term exams: July 15-20, 2025. Final exams: August 10-15, 2025.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal2">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-holiday me-2"></i>Upcoming Holidays</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: June 28, 2025</div>
                                <p class="text-gray-600 mb-0">Independence Day: August 15, 2025. Dasara Break: October 1-5, 2025.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal3">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-bullhorn me-2"></i>New Admissions</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: June 20, 2025</div>
                                <p class="text-gray-600 mb-0">Admissions for the 2025-26 academic year are open until July 31, 2025.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal4">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-bus me-2"></i>Transport Notice</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: June 18, 2025</div>
                                <p class="text-gray-600 mb-0">Bus routes have been updated. Check the transport section for new timings.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal5">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-bolt me-2"></i>Power Shutdown</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: June 15, 2025</div>
                                <p class="text-gray-600 mb-0">Scheduled power shutdown on July 10, 2025, from 10 AM to 1 PM.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-white shadow-sm text-left h-100 notice-card" data-bs-toggle="modal" data-bs-target="#noticeModal6">
                                <h3 class="text-xl font-semibold text-[#1E2A44] mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Parent-Teacher Meeting</h3>
                                <div class="text-sm text-gray-500 mb-1">Posted: June 10, 2025</div>
                                <p class="text-gray-600 mb-0">Parent-Teacher meeting scheduled for July 18, 2025, at 11 AM in the main hall.</p>
                            </div>
                </div>
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

    <!-- Gallery and Principal Placeholder -->
    <section class="container px-4 py-12 gallery-section text-center">
        <h2 class="gallery-title-funky animate-slide-in">Gallery</h2>
        <div class="gallery-row-wrapper mb-4">
            <div class="gallery-row gallery-row-1 d-flex align-items-center">
                <img src="../images/bitcblog1.jpg" class="gallery-img" alt="Campus 1">
                <img src="../images/161024-duke-university-submitted.jpg" class="gallery-img" alt="Campus 2">
                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" class="gallery-img" alt="Campus 3">
                <img src="../images/school.png" class="gallery-img" alt="School">
                <img src="../images/edu.jpeg" class="gallery-img" alt="Edu">
                <img src="../images/bitcblog1.jpg" class="gallery-img" alt="Campus 1">
                <img src="../images/161024-duke-university-submitted.jpg" class="gallery-img" alt="Campus 2">
                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" class="gallery-img" alt="Campus 3">
                <img src="../images/school.png" class="gallery-img" alt="School">
                <img src="../images/edu.jpeg" class="gallery-img" alt="Edu">
            </div>
        </div>
        <div class="gallery-row-wrapper">
            <div class="gallery-row gallery-row-2 d-flex align-items-center">
                <img src="../images/cm.jpeg" class="gallery-img" alt="CM">
                <img src="../images/flag.jpeg" class="gallery-img" alt="Flag">
                <img src="../images/edu.jpeg" class="gallery-img" alt="Edu 2">
                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" class="gallery-img" alt="Campus 3 Again">
                <img src="../images/bitcblog1.jpg" class="gallery-img" alt="Campus 1 Again">
                <img src="../images/cm.jpeg" class="gallery-img" alt="CM">
                <img src="../images/flag.jpeg" class="gallery-img" alt="Flag">
                <img src="../images/edu.jpeg" class="gallery-img" alt="Edu 2">
                <img src="../images/berry-college-historic-campus-at-twilight-royalty-free-image-1652127954.avif" class="gallery-img" alt="Campus 3 Again">
                <img src="../images/bitcblog1.jpg" class="gallery-img" alt="Campus 1 Again">
            </div>
        </div>
        <a href="view_gallery.php" class="explore-btn">Explore More</a>
    </section>

     <!-- Who is Who Section -->
     <section class="container px-4 py-12 who-section text-center position-relative">
        <h2 class="who-title">Who is Who</h2>
        <div class="who-carousel-row">
            <div class="who-card red">
                <div class="who-bg" style="background-image: url('../images/cm.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Dr. John Doe</div>
                    <div class="who-title-role">Principal</div>
                    <div class="who-desc">Leading with vision and dedication.</div>
                </div>
            </div>
            <div class="who-card blue">
                <div class="who-bg" style="background-image: url('../images/edu.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Ms. Priya Sharma</div>
                    <div class="who-title-role">Vice Principal</div>
                    <div class="who-desc">Academic excellence and discipline.</div>
                </div>
            </div>
            <div class="who-card saffron">
                <div class="who-bg" style="background-image: url('../images/flag.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mrs. Anita Rao</div>
                    <div class="who-title-role">Headmistress</div>
                    <div class="who-desc">Nurturing young minds.</div>
                </div>
            </div>
            <div class="who-card green">
                <div class="who-bg" style="background-image: url('../images/bitcblog1.jpg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mr. Ravi Kumar</div>
                    <div class="who-title-role">Coordinator</div>
                    <div class="who-desc">Connecting students and faculty.</div>
                </div>
            </div>
            <div class="who-card purple">
                <div class="who-bg" style="background-image: url('../images/school.png');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Ms. Sunita Verma</div>
                    <div class="who-title-role">Counselor</div>
                    <div class="who-desc">Guiding and supporting students.</div>
                </div>
            </div>
            <div class="who-card teal">
                <div class="who-bg" style="background-image: url('../images/161024-duke-university-submitted.jpg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mr. Ajay Singh</div>
                    <div class="who-title-role">Sports Head</div>
                    <div class="who-desc">Promoting fitness and teamwork.</div>
                </div>
            </div>
            <!-- Duplicate cards for infinite scroll effect -->
            <div class="who-card red">
                <div class="who-bg" style="background-image: url('../images/cm.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Dr. John Doe</div>
                    <div class="who-title-role">Principal</div>
                    <div class="who-desc">Leading with vision and dedication.</div>
                </div>
            </div>
            <div class="who-card blue">
                <div class="who-bg" style="background-image: url('../images/edu.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Ms. Priya Sharma</div>
                    <div class="who-title-role">Vice Principal</div>
                    <div class="who-desc">Academic excellence and discipline.</div>
                </div>
            </div>
            <div class="who-card saffron">
                <div class="who-bg" style="background-image: url('../images/flag.jpeg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mrs. Anita Rao</div>
                    <div class="who-title-role">Headmistress</div>
                    <div class="who-desc">Nurturing young minds.</div>
                </div>
            </div>
            <div class="who-card green">
                <div class="who-bg" style="background-image: url('../images/bitcblog1.jpg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mr. Ravi Kumar</div>
                    <div class="who-title-role">Coordinator</div>
                    <div class="who-desc">Connecting students and faculty.</div>
                </div>
            </div>
            <div class="who-card purple">
                <div class="who-bg" style="background-image: url('../images/school.png');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Ms. Sunita Verma</div>
                    <div class="who-title-role">Counselor</div>
                    <div class="who-desc">Guiding and supporting students.</div>
                </div>
            </div>
            <div class="who-card teal">
                <div class="who-bg" style="background-image: url('../images/161024-duke-university-submitted.jpg');"></div>
                <div class="who-darken"></div>
                <div class="who-card-content">
                    <div class="who-name">Mr. Ajay Singh</div>
                    <div class="who-title-role">Sports Head</div>
                    <div class="who-desc">Promoting fitness and teamwork.</div>
                </div>
            </div>
        </div>
        <a href="#" class="know-more-btn mt-3">Know More</a>
    </section>

    <!-- Additional Content: Achievements -->
    <section class="container px-4 py-12 bg-[#F5F5F5] text-center">
        <h2 class="text-3xl font-poppins font-bold text-[#D32F2F] mb-6 animate-slide-in">Our <span style="color: #D32F2F;">Achievements</span></h2>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="card p-4 bg-white shadow-md hover:shadow-xl transition-shadow duration-300 text-center">
                    <p class="text-gray-600 flex items-center justify-center"><i class="fas fa-trophy me-2"></i>100% Placement Rate in 2024</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4 bg-white shadow-md hover:shadow-xl transition-shadow duration-300 text-center">
                    <p class="text-gray-600 flex items-center justify-center"><i class="fas fa-award me-2"></i>Awarded <span style="color: #D32F2F;">Best College</span> 2023</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4 bg-white shadow-md hover:shadow-xl transition-shadow duration-300 text-center">
                    <p class="text-gray-600 flex items-center justify-center"><i class="fas fa-users me-2"></i>Over 5000 <span style="color: #D32F2F;">Alumni</span> Worldwide</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->

    <!-- Bootstrap JS and Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
