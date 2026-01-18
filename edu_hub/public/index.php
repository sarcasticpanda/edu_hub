<?php
// Fetch all data from database with fallbacks
require_once __DIR__ . '/includes/fetch_data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo htmlspecialchars($school_name_english); ?> - <?php echo htmlspecialchars($school_name_subtitle); ?></title>
    <meta name="description" content="Zilla Parishad High School, Bommalaramaram - Government of Telangana. Quality education for all students.">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'hsl(120, 61%, 34%)',
                        'primary-foreground': 'hsl(0, 0%, 100%)',
                        'gov-green': 'hsl(120, 61%, 28%)',
                        'gov-green-light': 'hsl(120, 50%, 45%)',
                        'gov-green-dark': 'hsl(120, 70%, 20%)',
                        saffron: 'hsl(25, 95%, 53%)',
                        'saffron-light': 'hsl(35, 100%, 65%)',
                        peach: 'hsl(25, 100%, 94%)',
                        'peach-dark': 'hsl(20, 80%, 88%)',
                        orange: 'hsl(30, 100%, 50%)',
                        background: 'hsl(0, 0%, 100%)',
                        foreground: 'hsl(0, 0%, 15%)',
                        'section-alt': 'hsl(0, 0%, 97%)',
                        card: 'hsl(0, 0%, 100%)',
                        'card-foreground': 'hsl(0, 0%, 15%)',
                        muted: 'hsl(0, 0%, 96%)',
                        'muted-foreground': 'hsl(0, 0%, 45%)',
                        accent: 'hsl(120, 61%, 95%)',
                        'accent-foreground': 'hsl(120, 61%, 28%)',
                        border: 'hsl(0, 0%, 90%)',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Base reset for consistent scaling */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Roboto', 'Noto Sans Telugu', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
            width: 100%;
        }
        
        /* Remove extra whitespace from sections */
        section, .container {
            margin-top: 0;
            margin-bottom: 0;
        }
        
        /* Responsive images */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Government Navbar Gradient */
        .gov-navbar {
            background: linear-gradient(180deg, hsl(120, 50%, 45%) 0%, hsl(120, 61%, 28%) 50%, hsl(120, 70%, 20%) 100%);
        }
        
        .gov-navbar-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s;
            color: white;
        }
        
        .gov-navbar-link:hover {
            background-color: #ff8c00;
            color: white;
        }
        
        /* Section Header */
        .section-header {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-header::before {
            content: '';
            width: 0.25rem;
            height: 2rem;
            background-color: hsl(120, 61%, 34%);
            border-radius: 9999px;
        }
        
        /* Ticker Animation */
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        .animate-ticker {
            animation: ticker 30s linear infinite;
        }
        
        /* Hero Fade Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(1.05); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .animate-hero-fade {
            animation: fadeIn 0.8s ease-out;
        }
        
        /* Gov Card */
        .gov-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid hsl(0, 0%, 90%);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .gov-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            transform: translateY(-0.25rem);
        }
        
        /* Faculty Card */
        .faculty-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid hsl(0, 0%, 90%);
            transition: all 0.3s;
        }
        
        .faculty-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        /* Scrollbar hiding for carousel */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Dropdown */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.25rem;
            width: 12rem;
            background: white;
            border: 1px solid hsl(0, 0%, 90%);
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 50;
        }
        
        .dropdown-menu.show {
            display: block;
        }

        /* ============ GALLERY 1: Student Grid Gallery ============ */
        .student-gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: 50px 150px 50px 150px 50px;
            gap: 1rem;
        }

        .student-gallery-cell {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .student-gallery-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .student-gallery-cell:hover img {
            transform: scale(1.05);
        }

        .student-gallery-cell[data-index="0"] {
            grid-column: 2 / 3;
            grid-row: 1 / 3;
        }

        .student-gallery-cell[data-index="1"] {
            grid-column: 1 / 2;
            grid-row: 2 / 4;
        }

        .student-gallery-cell[data-index="2"] {
            grid-column: 1 / 2;
            grid-row: 4 / 6;
        }

        .student-gallery-cell[data-index="3"] {
            grid-column: 2 / 3;
            grid-row: 3 / 5;
        }

        .student-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .student-gallery-cell:hover .student-overlay {
            transform: translateY(0);
        }

        /* ============ FACULTY GALLERY: Same Bento Style as Student Gallery ============ */
        .faculty-gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: 50px 150px 50px 150px 50px;
            gap: 1rem;
        }

        .faculty-gallery-cell {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .faculty-gallery-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .faculty-gallery-cell:hover img {
            transform: scale(1.05);
        }

        .faculty-gallery-cell[data-index="0"] {
            grid-column: 2 / 3;
            grid-row: 1 / 3;
        }

        .faculty-gallery-cell[data-index="1"] {
            grid-column: 1 / 2;
            grid-row: 2 / 4;
        }

        .faculty-gallery-cell[data-index="2"] {
            grid-column: 1 / 2;
            grid-row: 4 / 6;
        }

        .faculty-gallery-cell[data-index="3"] {
            grid-column: 2 / 3;
            grid-row: 3 / 5;
        }

        .faculty-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .faculty-gallery-cell:hover .faculty-overlay {
            transform: translateY(0);
        }

        /* ============ GALLERY 2: Bento Gallery (Horizontal Scrolling) ============ */
        .bento-gallery-container {
            position: relative;
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            cursor: grab;
            scrollbar-width: thin;
            scroll-behavior: smooth;
        }

        .bento-gallery-container::-webkit-scrollbar {
            height: 8px;
        }

        .bento-gallery-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .bento-gallery-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .bento-gallery-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .bento-gallery-container:active {
            cursor: grabbing;
        }

        .bento-gallery-grid {
            display: inline-grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(15rem, 1fr);
            gap: 1rem;
            padding: 1rem 2rem;
        }

        .bento-card {
            position: relative;
            min-height: 15rem;
            min-width: 15rem;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
        }

        .bento-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .bento-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .bento-card:hover img {
            transform: scale(1.05);
        }

        .bento-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.4), transparent);
            opacity: 0;
            transition: opacity 0.5s ease;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
        }

        .bento-card:hover .bento-card-overlay {
            opacity: 1;
        }

        .bento-card-content {
            transform: translateY(1rem);
            transition: transform 0.5s ease;
        }

        .bento-card:hover .bento-card-content {
            transform: translateY(0);
        }

        .bento-card-content h3 {
            color: white;
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .bento-card-content p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }

        /* Modal Styles */
        .gallery-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .gallery-modal.active {
            display: flex;
        }

        .modal-content-wrapper {
            position: relative;
            max-width: 90%;
            max-height: 90vh;
            animation: scaleIn 0.3s ease;
        }

        .modal-content-wrapper img {
            width: 100%;
            height: auto;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from { transform: scale(0.9) translateY(20px); }
            to { transform: scale(1) translateY(0); }
        }
        
        .modal-overlay-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.7) 70%, transparent 100%);
            color: white;
            padding: 2rem 1.5rem 1.5rem;
            border-radius: 0 0 0.5rem 0.5rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .modal-content-wrapper:hover .modal-overlay-info {
            transform: translateY(0);
        }
        
        .modal-overlay-info h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .modal-overlay-info p {
            font-size: 0.95rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* Fade in animations */
        .fade-in-section {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .student-gallery-grid {
                grid-template-rows: 40px 120px 40px 120px 40px;
            }
            
            .faculty-gallery-grid {
                grid-template-rows: 40px 120px 40px 120px 40px;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-background" style="margin: 0; padding: 0;">

    <?php include __DIR__ . '/includes/header_navbar.php'; ?>

    <!-- ============ HERO SECTION ============ -->
    <section class="relative h-[300px] md:h-[400px] lg:h-[450px] overflow-hidden" id="heroSection">
        <?php $event = $featured_events[0]; ?>
        <div class="absolute inset-0 animate-hero-fade" id="heroSlide">
            <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="w-full h-full object-cover" id="heroImage">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 h-full container mx-auto px-4 flex items-center">
            <div class="max-w-xl text-white">
                <span class="inline-block bg-saffron text-white px-4 py-1 text-sm font-bold rounded-full mb-4 uppercase" id="heroTag">
                    Featured Event
                </span>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-2" id="heroTitle">
                    <?= htmlspecialchars($event['title']) ?>
                </h2>
                <p class="text-xl md:text-2xl mb-2 text-white/90" id="heroSubtitle">
                    <?= htmlspecialchars($event['subtitle']) ?>
                </p>
                <p class="text-lg text-white/80 flex items-center gap-2" id="heroDesc">
                    <?= htmlspecialchars($event['description']) ?>
                </p>
            </div>
        </div>
        
        <!-- Slide Indicators -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
            <?php for ($i = 0; $i < count($featured_events); $i++): ?>
            <button onclick="goToSlide(<?= $i ?>)" class="w-3 h-3 rounded-full transition-all <?= $i === 0 ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/75' ?>" id="indicator<?= $i ?>"></button>
            <?php endfor; ?>
        </div>
    </section>

    <!-- ============ NEWS TICKER ============ -->
    <div class="overflow-hidden bg-primary text-white py-2">
        <div class="container mx-auto px-4 flex items-center">
            <div class="flex items-center gap-2 bg-saffron px-4 py-1 mr-4 shrink-0">
                <i data-lucide="volume-2" class="w-4 h-4"></i>
                <span class="font-bold text-sm">Latest News</span>
            </div>
            
            <div class="overflow-hidden flex-1">
                <div class="flex animate-ticker whitespace-nowrap">
                    <?php 
                    $all_news = array_merge($news_items, $news_items);
                    foreach ($all_news as $item): 
                    ?>
                    <span class="mx-8 whitespace-nowrap">
                        <?= htmlspecialchars($item) ?>
                        <span class="mx-4 text-white/50">•</span>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ EVENTS CAROUSEL ============ -->
    <section class="py-8 bg-section-alt">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-foreground mb-1">
                        <i data-lucide="calendar" class="inline-block w-6 h-6 mr-2 text-primary"></i>
                        School Events
                    </h2>
                    <p class="text-sm text-muted-foreground">Discover our latest activities and upcoming events</p>
                </div>
                <span class="text-sm text-muted-foreground px-3 py-1 bg-white rounded-full border border-border">
                    <?= count($events) ?> Events
                </span>
            </div>
            
            <!-- Events Carousel Container -->
            <?php if (!empty($events)): ?>
            <div class="relative">
                <!-- Scroll Hint -->
                <div class="absolute right-4 top-1/2 -translate-y-1/2 z-10 pointer-events-none">
                    <div class="bg-primary text-white px-3 py-1.5 rounded-full text-xs font-medium shadow-lg flex items-center gap-1 animate-pulse">
                        Scroll <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                    </div>
                </div>
                
                <!-- Events Grid (Horizontally Scrollable) -->
                <div class="flex gap-4 overflow-x-auto py-3 cursor-grab active:cursor-grabbing hide-scrollbar" 
                     style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;" 
                     id="eventsCarousel">
                    <?php foreach ($events as $event): ?>
                    <div class="flex-shrink-0 w-48 border border-border bg-card cursor-pointer hover:shadow-lg transition-all duration-300 rounded-lg overflow-hidden hover:scale-105" 
                         style="scroll-snap-align: start; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <div class="h-32 overflow-hidden bg-gradient-to-br from-primary/10 to-primary/5" style="transition: transform 0.3s ease;">
                            <img src="<?= htmlspecialchars($event['image']) ?>" 
                                 alt="<?= htmlspecialchars($event['title']) ?>" 
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                                 draggable="false"
                                 loading="lazy">
                        </div>
                        <div class="p-3">
                            <p class="text-xs text-foreground font-medium line-clamp-2 mb-2"><?= htmlspecialchars($event['title']) ?></p>
                            <?php if (!empty($event['date'])): ?>
                            <p class="text-[10px] text-muted-foreground flex items-center gap-1">
                                <i data-lucide="calendar-days" class="w-3 h-3"></i>
                                <?= date('M d, Y', strtotime($event['date'])) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-12 bg-white rounded-lg border border-border">
                <i data-lucide="calendar-x" class="w-16 h-16 mx-auto mb-4 text-muted-foreground opacity-50"></i>
                <p class="text-muted-foreground">No events available at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============ NOTICE SECTION WITH OFFICIALS ============ -->
    <section class="py-6 bg-background">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-[45%_55%] gap-5 items-stretch">
                <!-- Notices Column -->
                <div class="border border-border bg-card overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="bg-primary text-white px-4 py-2.5 border-b-2 border-saffron">
                        <h3 class="font-bold text-sm uppercase tracking-wide">Notice Board</h3>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <!-- Pinned Notices Section -->
                        <?php 
                        $pinned = array_filter($notices_db, fn($n) => $n['is_pinned'] == 1);
                        $regular = array_filter($notices_db, fn($n) => $n['is_pinned'] == 0);
                        
                        // Helper function to check if notice is new (within last 7 days)
                        function isNoticeNew($date) {
                            if (empty($date)) return false;
                            $noticeTime = strtotime($date);
                            $weekAgo = strtotime('-7 days');
                            return $noticeTime >= $weekAgo;
                        }
                        
                        // Helper function to format date
                        function formatNoticeDate($date) {
                            if (empty($date)) return 'N/A';
                            return date('d M Y', strtotime($date));
                        }
                        ?>
                        <?php if (count($pinned) > 0): ?>
                        <div class="border-b border-border">
                            <div class="px-3 py-2 border-b border-border">
                                <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Pinned</span>
                            </div>
                            <?php foreach ($pinned as $notice): ?>
                            <div class="px-4 py-3 border-b border-border last:border-b-0">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <?php if (isNoticeNew($notice['created_at'] ?? null)): ?>
                                        <span class="text-[9px] px-1.5 py-0.5 bg-red-600 text-white font-bold uppercase">New</span>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-muted-foreground uppercase font-medium"><?= htmlspecialchars($notice['subheading'] ?? 'Notice') ?></span>
                                    </div>
                                    <p class="text-sm text-foreground leading-snug"><?= htmlspecialchars($notice['title']) ?></p>
                                    <div class="flex items-center justify-between gap-2 mt-1">
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            <span><?= formatNoticeDate($notice['created_at'] ?? null) ?></span>
                                            <span>•</span>
                                            <span><?= htmlspecialchars($notice['posted_by'] ?? 'Administration') ?></span>
                                        </div>
                                        <a href="/2026/edu_hub/edu_hub/public/notices.php#notice-<?= $notice['id'] ?>" 
                                           class="text-xs text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Regular Notices - Hidden on homepage, only pinned shown -->
                        <?php if (false && count($regular) > 0): ?>
                            <?php foreach ($regular as $notice): ?>
                            <div class="px-4 py-2.5 border-b border-border last:border-b-0 hover:bg-muted/20 transition-colors cursor-pointer">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <?php if (isNoticeNew($notice['created_at'] ?? null)): ?>
                                            <span class="text-[9px] px-1.5 py-0.5 bg-red-600 text-white font-bold uppercase">New</span>
                                            <?php endif; ?>
                                            <span class="text-[10px] text-muted-foreground uppercase font-medium"><?= htmlspecialchars($notice['subheading'] ?? 'Notice') ?></span>
                                        </div>
                                        <p class="text-sm text-foreground hover:text-primary transition-colors leading-snug"><?= htmlspecialchars($notice['title']) ?></p>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-muted-foreground">
                                            <span><?= formatNoticeDate($notice['created_at'] ?? null) ?></span>
                                            <span>•</span>
                                            <span><?= htmlspecialchars($notice['posted_by'] ?? 'Administration') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-4 py-8 text-center text-muted-foreground">
                                <p class="text-sm">No notices available at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="px-4 py-3 border-t border-border mt-auto">
                        <a href="notices.php" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                            View All Notices
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Government Officials Column -->
                <div class="flex flex-col gap-4">
                    <?php foreach ($officials as $official): 
                        // Limit bio to 50 words
                        $bioWords = !empty($official['bio']) ? explode(' ', $official['bio']) : [];
                        $limitedBio = count($bioWords) > 50 ? implode(' ', array_slice($bioWords, 0, 50)) . '...' : $official['bio'];
                    ?>
                    <div class="border-l-4 border-primary flex-1 flex items-stretch min-h-[280px]" style="background-color: #fafaf8;">
                        <div class="flex gap-6 p-6 w-full">
                            <!-- Photo with border -->
                            <div class="shrink-0">
                                <div class="w-40 h-56 overflow-hidden border-2 border-primary bg-white shadow-md">
                                    <img src="<?= htmlspecialchars($official['image']) ?>" alt="<?= htmlspecialchars($official['name']) ?>" class="w-full h-full object-cover object-top">
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 border-l-2 border-primary/30 pl-6 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-semibold text-sm uppercase tracking-wider text-primary/90 mb-2 letter-spacing-wide"><?= htmlspecialchars($official['designation']) ?></h4>
                                    <p class="text-2xl font-bold text-gray-900 mb-3 leading-tight tracking-tight"><?= htmlspecialchars($official['name']) ?></p>
                                    
                                    <!-- Bio/Description (inline, limited to 50 words) -->
                                    <?php if (!empty($limitedBio)): ?>
                                    <p class="text-sm text-gray-600 leading-loose tracking-wide mb-4" style="line-height: 1.75;">
                                        <?= nl2br(htmlspecialchars($limitedBio)) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Profile & Contact Links -->
                                <div class="flex items-center gap-5 text-sm mb-2">
                                    <a href="<?= htmlspecialchars($official['profileLink']) ?>" class="text-primary hover:underline flex items-center gap-1.5 font-medium">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                        Profile
                                    </a>
                                    <a href="<?= htmlspecialchars($official['contactLink']) ?>" class="text-primary hover:underline flex items-center gap-1.5 font-medium">
                                        <i data-lucide="phone" class="w-4 h-4"></i>
                                        Contact
                                    </a>
                                </div>
                                
                                <!-- Social Icons -->
                                <div class="flex gap-4 mt-2">
                                    <a href="<?= htmlspecialchars($official['facebook']) ?>" class="text-primary hover:text-primary/70">
                                        <i data-lucide="facebook" class="w-5 h-5"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($official['linkedin']) ?>" class="text-primary hover:text-primary/70">
                                        <i data-lucide="linkedin" class="w-5 h-5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ OUR STUDENTS - Gallery 1 (CTA Section with Grid Gallery) ============ -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Text Content -->
                <div class="fade-in-section">
                    <span class="block text-xs md:text-sm font-medium text-primary mb-4">
                        Excellence & Achievement
                    </span>
                    <h2 class="text-4xl md:text-5xl font-semibold tracking-tight text-gray-900">
                        <?= htmlspecialchars($students_title) ?>
                    </h2>
                    <p class="my-4 md:my-6 text-base md:text-lg text-gray-700">
                        <?= htmlspecialchars($students_description) ?>
                    </p>
                    <a href="#students-showcase" class="inline-block bg-primary text-white px-6 py-3 rounded-md font-medium hover:opacity-90 transition">
                        Meet Our Students
                    </a>
                </div>

                <!-- Gallery Grid - Student Images -->
                <div class="student-gallery-grid fade-in-section">
                    <?php 
                    // Get 4 students for the grid
                    $grid_students = array_slice($students, 0, 4);
                    foreach ($grid_students as $index => $student): 
                    ?>
                    <div class="student-gallery-cell" data-index="<?= $index ?>" 
                         onclick="openImageModal('<?= htmlspecialchars($student['image']) ?>', '<?= htmlspecialchars($student['name']) ?>', '<?= htmlspecialchars($student['role']) ?>')" 
                         style="cursor: pointer;">
                        <img src="<?= htmlspecialchars($student['image']) ?>" 
                             alt="<?= htmlspecialchars($student['name']) ?>" 
                             loading="lazy">
                        <div class="student-overlay">
                            <h3 class="text-white font-bold text-sm"><?= htmlspecialchars($student['name']) ?></h3>
                            <p class="text-white/80 text-xs"><?= htmlspecialchars($student['role']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SCHOOL AT A GLANCE & INFRASTRUCTURE - Gallery 2 (Bento Gallery) ============ -->
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4 text-center mb-12 fade-in-section">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">
                <?= htmlspecialchars($infrastructure_title) ?>
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                <?= htmlspecialchars($infrastructure_description) ?>
            </p>
        </div>

        <div class="bento-gallery-container" id="bentoGallery">
            <div class="bento-gallery-grid">
                <?php 
                // Merge school overview and infrastructure
                $combined_gallery = array_merge($school_gallery, $facilities);
                foreach ($combined_gallery as $index => $item): 
                    // Determine grid span based on index
                    $span_class = '';
                    if ($index % 6 == 0) {
                        $span_class = 'style="grid-column: span 2; grid-row: span 2;"';
                    } elseif ($index % 6 == 3) {
                        $span_class = 'style="grid-row: span 2;"';
                    } elseif ($index % 6 == 5) {
                        $span_class = 'style="grid-column: span 2;"';
                    }
                    
                    $title = $item['title'] ?? $item['name'] ?? 'Campus View';
                    $desc = $item['description'] ?? '';
                ?>
                <div class="bento-card fade-in-section" <?= $span_class ?>
                     data-image="<?= htmlspecialchars($item['image']) ?>"
                     data-title="<?= htmlspecialchars($title) ?>"
                     data-desc="<?= htmlspecialchars($desc) ?>">
                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                         alt="<?= htmlspecialchars($title) ?>" 
                         loading="lazy">
                    <div class="bento-card-overlay">
                        <div class="bento-card-content">
                            <h3><?= htmlspecialchars($title) ?></h3>
                            <?php if ($desc): ?>
                            <p><?= htmlspecialchars($desc) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Image Modal -->
    <div class="gallery-modal" id="imageModal">
        <button class="modal-close-btn" onclick="closeGalleryModal()">×</button>
        <div class="modal-content-wrapper">
            <img id="modalImage" src="" alt="">
            <div class="modal-overlay-info" id="modalOverlayInfo">
                <h3 id="modalTitle"></h3>
                <p id="modalDescription"></p>
            </div>
        </div>
    </div>

    <!-- ============ GALLERY ============ -->
    <section class="py-10 bg-background">
        <div class="container mx-auto px-4">
            <h2 class="section-header">Gallery</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($gallery_items as $index => $image): ?>
                <div class="aspect-[4/3] overflow-hidden rounded-lg shadow-md cursor-pointer group" 
                     onclick="openImageModal('<?= htmlspecialchars($image) ?>', 'Gallery Image <?= $index + 1 ?>', '')">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Gallery <?= $index + 1 ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-6">
                <a href="/2026/edu_hub/edu_hub/public/gallery.php" class="inline-block px-6 py-2 bg-primary text-white rounded-md font-medium hover:opacity-90 transition-colors">
                    View All Photos
                </a>
            </div>
        </div>
    </section>

    <!-- ============ FACULTY MEMBERS ============ -->
    <section class="py-16 bg-section-alt">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Text Content (Left Side) -->
                <div class="fade-in-section text-center md:text-left">
                    <span class="block text-xs md:text-sm font-medium text-primary mb-4">
                        Excellence in Teaching
                    </span>
                    <h2 class="text-4xl md:text-5xl font-semibold tracking-tight text-gray-900">
                        <?= htmlspecialchars($faculty_title) ?>
                    </h2>
                    <p class="my-4 md:my-6 text-base md:text-lg text-gray-700">
                        <?= htmlspecialchars($faculty_description) ?>
                    </p>
                </div>

                <!-- Faculty Gallery Grid (Right Side - Bento Style) -->
                <div class="faculty-gallery-grid fade-in-section">
                    <?php 
                    // Flatten all teachers from all departments and get first 4
                    $all_teachers = [];
                    foreach ($departments as $dept) {
                        foreach ($dept['teachers'] as $teacher) {
                            $teacher['department'] = $dept['name'];
                            $all_teachers[] = $teacher;
                        }
                    }
                    $grid_teachers = array_slice($all_teachers, 0, 4);
                    foreach ($grid_teachers as $index => $teacher): 
                    ?>
                    <div class="faculty-gallery-cell" data-index="<?= $index ?>" 
                         onclick="openImageModal('<?= htmlspecialchars($teacher['image']) ?>', '<?= htmlspecialchars($teacher['name']) ?>', '<?= htmlspecialchars($teacher['subject']) ?>')" 
                         style="cursor: pointer;">
                        <img src="<?= htmlspecialchars($teacher['image']) ?>" 
                             alt="<?= htmlspecialchars($teacher['name']) ?>" 
                             loading="lazy">
                        <div class="faculty-overlay">
                            <h3 class="text-white font-bold text-sm"><?= htmlspecialchars($teacher['name']) ?></h3>
                            <p class="text-white/90 text-xs font-medium"><?= htmlspecialchars($teacher['subject']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- ============ JAVASCRIPT ============ -->
    <script>
        // Initialize Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Login Dropdown Toggle
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const chevron = document.getElementById('dropdownChevron');
            menu.classList.toggle('show');
            chevron.style.transform = menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('loginDropdown');
            if (!dropdown.contains(e.target)) {
                document.getElementById('dropdownMenu').classList.remove('show');
                document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
            }
        });
        
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        // Hero Slider
        const featuredEvents = <?= json_encode($featured_events) ?>;
        let currentSlide = 0;
        
        function goToSlide(index) {
            currentSlide = index;
            updateHero();
        }
        
        function updateHero() {
            const event = featuredEvents[currentSlide];
            document.getElementById('heroImage').src = event.image;
            document.getElementById('heroTitle').textContent = event.title;
            document.getElementById('heroSubtitle').textContent = event.subtitle;
            document.getElementById('heroDesc').textContent = event.description;
            
            // Update indicators
            for (let i = 0; i < featuredEvents.length; i++) {
                const indicator = document.getElementById('indicator' + i);
                if (i === currentSlide) {
                    indicator.classList.add('bg-white', 'w-8');
                    indicator.classList.remove('bg-white/50');
                } else {
                    indicator.classList.remove('bg-white', 'w-8');
                    indicator.classList.add('bg-white/50');
                }
            }
        }
        
        // Auto-rotate slides
        setInterval(function() {
            currentSlide = (currentSlide + 1) % featuredEvents.length;
            updateHero();
        }, 5000);

        // ============ GALLERY FUNCTIONALITY ============
        
        // Fade-in on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-section').forEach(el => {
            fadeObserver.observe(el);
        });

        // Bento Gallery - Drag to scroll functionality
        const bentoGallery = document.getElementById('bentoGallery');
        if (bentoGallery) {
            let isDown = false;
            let startX;
            let scrollLeft;

            bentoGallery.addEventListener('mousedown', (e) => {
                isDown = true;
                bentoGallery.style.cursor = 'grabbing';
                startX = e.pageX - bentoGallery.offsetLeft;
                scrollLeft = bentoGallery.scrollLeft;
            });

            bentoGallery.addEventListener('mouseleave', () => {
                isDown = false;
                bentoGallery.style.cursor = 'grab';
            });

            bentoGallery.addEventListener('mouseup', () => {
                isDown = false;
                bentoGallery.style.cursor = 'grab';
            });

            bentoGallery.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - bentoGallery.offsetLeft;
                const walk = (x - startX) * 2;
                bentoGallery.scrollLeft = scrollLeft - walk;
            });
        }

        // Events Carousel - Drag to scroll functionality
        const eventsCarousel = document.getElementById('eventsCarousel');
        if (eventsCarousel) {
            let isDown = false;
            let startX;
            let scrollLeft;

            eventsCarousel.addEventListener('mousedown', (e) => {
                isDown = true;
                eventsCarousel.style.cursor = 'grabbing';
                startX = e.pageX - eventsCarousel.offsetLeft;
                scrollLeft = eventsCarousel.scrollLeft;
            });

            eventsCarousel.addEventListener('mouseleave', () => {
                isDown = false;
                eventsCarousel.style.cursor = 'grab';
            });

            eventsCarousel.addEventListener('mouseup', () => {
                isDown = false;
                eventsCarousel.style.cursor = 'grab';
            });

            eventsCarousel.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - eventsCarousel.offsetLeft;
                const walk = (x - startX) * 2;
                eventsCarousel.scrollLeft = scrollLeft - walk;
            });
        }

        // Modal functionality for gallery cards
        const bentoCards = document.querySelectorAll('.bento-card');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        bentoCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Prevent modal from opening during drag
                if (bentoGallery && bentoGallery.style.cursor === 'grabbing') return;
                
                const imageSrc = this.dataset.image;
                const title = this.dataset.title || '';
                const desc = this.dataset.desc || '';
                openImageModal(imageSrc, title, desc);
            });
        });

        function openImageModal(imageSrc, title = '', description = '') {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            const modalDescription = document.getElementById('modalDescription');
            const modalOverlayInfo = document.getElementById('modalOverlayInfo');
            
            modalImage.src = imageSrc;
            modalTitle.textContent = title;
            modalDescription.textContent = description;
            
            // Show or hide overlay based on content
            if (title || description) {
                modalOverlayInfo.style.display = 'block';
            } else {
                modalOverlayInfo.style.display = 'none';
            }
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeGalleryModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal on background click
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeGalleryModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeGalleryModal();
            }
        });
    </script>

</body>
</html>
