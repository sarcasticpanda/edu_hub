<?php
session_start();
require_once __DIR__ . '/../admin/includes/db.php';

$school_name = getSchoolConfig('school_name', 'School CMS');
$logo_path = getSchoolConfig('logo_path', '../images/logo_placeholder.png');

// Fetch gallery hero content
$hero_content = $pdo->query("SELECT * FROM gallery_hero_content LIMIT 1")->fetch();
if (!$hero_content) {
    // Default values if not set
    $hero_content = [
        'hero_title' => 'Our Gallery',
        'hero_subtitle' => 'Celebrating decades of academic excellence, cultural heritage, and sporting achievements',
        'background_image' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920&h=1080&fit=crop',
        'stat1_value' => '50+',
        'stat1_label' => 'Years of Legacy',
        'stat2_value' => '100+',
        'stat2_label' => 'Memories Captured',
        'stat3_value' => '5+',
        'stat3_label' => 'Categories'
    ];
}

// Always fetch all images for filtering and display
$stmt = $pdo->query("SELECT * FROM gallery_images WHERE display_location IN ('Main Gallery', 'Both', 'gallery', 'both') ORDER BY created_at DESC");
$images = $stmt->fetchAll();

// Get total images count
$total_images = count($images);

// Get categories for filtering (for the buttons)
$all_categories_for_filter = [];
$stmt = $pdo->query("SELECT DISTINCT category FROM gallery_images WHERE display_location IN ('Main Gallery', 'Both', 'gallery', 'both') AND category IS NOT NULL AND category != '' ORDER BY category ASC");
$all_categories_for_filter = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Photo Gallery</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    <!-- Bootstrap for existing content -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/2026/edu_hub/edu_hub/assets/css/gov-theme.css">
    
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
                        foreground: 'hsl(0, 0%, 10%)',
                        card: 'hsl(0, 0%, 100%)',
                        'card-foreground': 'hsl(0, 0%, 10%)',
                        muted: 'hsl(0, 0%, 96%)',
                        'muted-foreground': 'hsl(0, 0%, 45%)',
                        accent: 'hsl(120, 61%, 95%)',
                        'accent-foreground': 'hsl(120, 61%, 28%)',
                        border: 'hsl(0, 0%, 90%)',
                    },
                    fontFamily: {
                        sans: ['Roboto', 'Noto Sans Telugu', 'sans-serif'],
                        telugu: ['Noto Sans Telugu', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Roboto', 'Noto Sans Telugu', sans-serif;
        }
        
        /* Government Navbar Gradient - must match main site */
        .gov-navbar {
            background: linear-gradient(180deg, hsl(120, 50%, 45%) 0%, hsl(120, 61%, 28%) 50%, hsl(120, 70%, 20%) 100%) !important;
        }
        
        .gov-navbar-link {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            transition: all 0.2s !important;
            color: white !important;
        }
        
        .gov-navbar-link:hover {
            background-color: #ff8c00 !important;
            color: white !important;
        }
        
        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes rotateCircle {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes rotateCircleReverse {
            from { transform: translate(25%, 25%) rotate(0deg); }
            to { transform: translate(25%, 25%) rotate(-360deg); }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%); }
            100% { transform: translateX(100%) translateY(100%); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.5s ease-out forwards;
        }

        .animate-rotate {
            animation: rotateCircle 60s linear infinite;
        }

        .animate-rotate-reverse {
            animation: rotateCircleReverse 80s linear infinite;
        }

        /* Stagger delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }

        /* Gallery item hover effects */
        .gallery-item {
            perspective: 1000px;
            opacity: 0;
            position: relative;
        }

        .gallery-item.visible {
            animation: scaleIn 0.6s ease-out forwards;
        }

        .gallery-item-inner {
            position: relative;
            transition: transform 0.4s ease-out, box-shadow 0.4s ease-out;
            transform-style: preserve-3d;
        }

        .gallery-item:hover .gallery-item-inner {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }

        .gallery-item img {
            position: relative;
            z-index: 1;
            transition: transform 0.6s ease-out, filter 0.8s ease-out;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item .overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.3s ease-out;
            pointer-events: none;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
        }

        .gallery-item .content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 3;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.4s ease-out;
            pointer-events: none;
        }

        .gallery-item:hover .content {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery-item .zoom-icon {
            transform: scale(0) rotate(-180deg);
            transition: all 0.4s ease-out 0.15s;
        }

        .gallery-item:hover .zoom-icon {
            transform: scale(1) rotate(0);
        }

        .gallery-item .shimmer {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(to top right, transparent, rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transform: translateX(-100%) translateY(-100%);
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .gallery-item:hover .shimmer {
            opacity: 1;
            animation: shimmer 0.8s ease-in-out;
        }

        /* Category tab */
        .category-tab {
            position: relative;
            transition: color 0.3s ease;
        }

        .category-tab.active {
            color: hsl(120, 61%, 34%);
        }

        .category-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: hsl(120, 61%, 34%);
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox.visible {
            opacity: 1;
        }

        .lightbox-content {
            background: rgba(245, 245, 245, 0.95);
            backdrop-filter: blur(40px);
            max-width: 72rem;
            width: 95%;
            max-height: 95vh;
            border-radius: 0.75rem;
            overflow: hidden;
            transform: scale(0.9) translateY(30px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .lightbox.visible .lightbox-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .lightbox-image {
            max-height: 65vh;
            object-fit: contain;
            border-radius: 0.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }

        .nav-btn {
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            transform: scale(1.1);
        }

        .close-btn:hover {
            transform: scale(1.1) rotate(90deg);
        }

        /* Scrollbar hide */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Masonry layout */
        .masonry {
            column-count: 2;
            column-gap: 1.25rem;
        }

        @media (min-width: 768px) {
            .masonry {
                column-count: 3;
            }
        }

        @media (min-width: 1024px) {
            .masonry {
                column-count: 4;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header_navbar.php'; ?>

<?php
// Get unique categories from database
$categories = [['id' => 'all', 'label' => 'All Photos']];
foreach ($all_categories_for_filter as $cat) {
    $categories[] = ['id' => $cat, 'label' => ucfirst($cat)];
}

// Calculate total images for stats
$total_images = count($images);

// Function to get category label by id
function getCategoryLabel($categories, $id) {
    foreach ($categories as $cat) {
        if ($cat['id'] === $id) {
            return $cat['label'];
        }
    }
    return '';
}
?>

<!-- Grand Hero Section -->
<section class="relative h-[50vh] min-h-[400px] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($hero_content['background_image']) ?>')"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-foreground/70 via-foreground/50 to-foreground/80"></div>
    
    <!-- Decorative circles -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 border border-white/10 rounded-full animate-rotate" style="transform: translate(-50%, -50%)"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] border border-white/5 rounded-full animate-rotate-reverse" style="transform: translate(25%, 25%)"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
        <div class="animate-fade-in-up">
            <!-- Small label -->
            <p class="text-white/70 text-sm uppercase tracking-[0.4em] mb-4" style="animation: fadeIn 0.6s ease-out 0.3s forwards; opacity: 0;">
                <?= htmlspecialchars($school_name) ?>
            </p>
            
            <!-- Main Title -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4" style="animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0;">
                <?= htmlspecialchars($hero_content['hero_title']) ?>
            </h1>
            
            <!-- Decorative line -->
            <div class="flex items-center justify-center gap-4 mb-6" style="animation: fadeIn 0.6s ease-out 0.6s forwards; opacity: 0;">
                <div class="h-px w-16 bg-gradient-to-r from-transparent to-white/50"></div>
                <div class="w-2 h-2 bg-primary rotate-45"></div>
                <div class="h-px w-16 bg-gradient-to-l from-transparent to-white/50"></div>
            </div>
            
            <!-- Subtitle -->
            <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto" style="animation: fadeIn 0.6s ease-out 0.7s forwards; opacity: 0;">
                <?= htmlspecialchars($hero_content['hero_subtitle']) ?>
            </p>
        </div>

        <!-- Stats -->
        <div class="absolute bottom-8 left-0 right-0" style="animation: fadeInUp 0.8s ease-out 1s forwards; opacity: 0;">
            <div class="flex justify-center gap-12 md:gap-20">
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($hero_content['stat1_value']) ?></div>
                    <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($hero_content['stat1_label']) ?></div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($hero_content['stat2_value']) ?></div>
                    <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($hero_content['stat2_label']) ?></div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($hero_content['stat3_value']) ?></div>
                    <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($hero_content['stat3_label']) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Navigation -->
<section class="bg-card border-b border-border sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex overflow-x-auto py-1 gap-1 scrollbar-hide" id="categoryNav" style="animation: fadeIn 0.6s ease-out 0.5s forwards; opacity: 0;">
            <?php foreach ($categories as $index => $category): ?>
                <button
                    onclick="filterGallery('<?= htmlspecialchars($category['id']) ?>')"
                    class="category-tab px-5 py-4 text-sm font-medium whitespace-nowrap text-muted-foreground hover:text-foreground <?= $category['id'] === 'all' ? 'active' : '' ?>"
                    data-category="<?= htmlspecialchars($category['id']) ?>"
                >
                    <?= htmlspecialchars($category['label']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-12 bg-muted/30">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="mb-10" style="animation: fadeInUp 0.8s ease-out 0.3s forwards; opacity: 0;">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-foreground" id="sectionTitle">
                        All Photographs
                    </h2>
                    <p class="text-muted-foreground mt-1" id="photoCount">
                        <?= $total_images ?> photographs from our school archives
                    </p>
                </div>
            </div>
        </div>

        <!-- Masonry Grid -->
        <div class="masonry" id="galleryGrid">
            <?php if (empty($images)): ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-muted-foreground text-lg">No images found in the gallery.</p>
                </div>
            <?php else: ?>
                <?php foreach ($images as $index => $image): ?>
                    <div 
                        class="masonry-item gallery-item" 
                        data-category="<?= htmlspecialchars($image['category']) ?>"
                        data-id="<?= $image['id'] ?>"
                        data-title="<?= htmlspecialchars($image['title']) ?>"
                        data-src="<?= htmlspecialchars($image['image_path']) ?>"
                        data-category-label="<?= htmlspecialchars(ucfirst($image['category'])) ?>"
                        onclick="openLightbox(<?= $image['id'] ?>)"
                        style="animation-delay: <?= $index * 0.08 ?>s"
                    >
                        <div class="gallery-item-inner relative cursor-pointer rounded-xl overflow-hidden bg-muted shadow-lg">
                            <!-- Image -->
                            <div class="overflow-hidden">
                                <img
                                    src="<?= htmlspecialchars($image['image_path']) ?>"
                                    alt="<?= htmlspecialchars($image['title']) ?>"
                                    class="w-full h-auto object-cover"
                                    loading="lazy"
                                >
                            </div>
                            
                            <!-- Shimmer effect -->
                            <div class="shimmer"></div>
                            
                            <!-- Gradient overlay -->
                            <div class="overlay absolute inset-0 bg-gradient-to-t from-foreground/90 via-foreground/20 to-transparent"></div>

                            <!-- Content -->
                            <div class="content absolute bottom-0 left-0 right-0 p-4">
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-white font-semibold text-sm md:text-base"><?= htmlspecialchars($image['title']) ?></p>
                                        <p class="text-white/60 text-xs md:text-sm"><?= htmlspecialchars(ucfirst($image['category'])) ?></p>
                                    </div>
                                    <div class="zoom-icon p-2.5 bg-white/20 backdrop-blur-md rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="m21 21-4.3-4.3"></path>
                                            <path d="M11 8v6"></path>
                                            <path d="M8 11h6"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
    <div class="lightbox-content relative p-4 pt-12 pb-6" onclick="event.stopPropagation()">
        <!-- Close button -->
        <button
            onclick="closeLightbox()"
            class="close-btn absolute top-4 right-4 z-50 p-3 bg-foreground/10 hover:bg-foreground/20 rounded-full text-foreground transition-all"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>

        <!-- Navigation buttons -->
        <button
            id="prevBtn"
            onclick="navigateLightbox(-1)"
            class="nav-btn absolute left-4 top-1/2 -translate-y-1/2 z-50 p-4 bg-foreground/10 hover:bg-foreground/20 rounded-full text-foreground transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </button>
        <button
            id="nextBtn"
            onclick="navigateLightbox(1)"
            class="nav-btn absolute right-4 top-1/2 -translate-y-1/2 z-50 p-4 bg-foreground/10 hover:bg-foreground/20 rounded-full text-foreground transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </button>

        <!-- Image container -->
        <div class="flex flex-col items-center px-12">
            <img
                id="lightboxImage"
                src=""
                alt=""
                class="lightbox-image"
            >
            
            <!-- Caption -->
            <div class="py-6 text-center w-full">
                <!-- Decorative line -->
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-foreground/30"></div>
                    <div class="w-1.5 h-1.5 bg-primary rotate-45"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-foreground/30"></div>
                </div>
                
                <h3 id="lightboxTitle" class="font-semibold text-foreground text-xl"></h3>
                <p id="lightboxMeta" class="text-sm text-muted-foreground mt-2 uppercase tracking-wider"></p>
                
                <!-- Image counter -->
                <p id="lightboxCounter" class="text-xs text-muted-foreground/70 mt-4"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Gallery data for JavaScript
const galleryImages = <?= json_encode(array_map(function($img) {
    return [
        'id' => $img['id'],
        'src' => $img['image_path'],
        'category' => $img['category'],
        'title' => $img['title']
    ];
}, $images)) ?>;
const categories = <?= json_encode($categories) ?>;

let currentCategory = 'all';
let filteredImages = [...galleryImages];
let currentLightboxIndex = -1;

// Initialize gallery items visibility on scroll
function initGalleryAnimations() {
    const items = document.querySelectorAll('.gallery-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    items.forEach(item => observer.observe(item));
}

// Filter gallery by category
function filterGallery(category) {
    currentCategory = category;
    
    // Update active tab
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.category === category) {
            tab.classList.add('active');
        }
    });

    // Filter images
    filteredImages = category === 'all' 
        ? [...galleryImages] 
        : galleryImages.filter(img => img.category === category);

    // Update section title
    const sectionTitle = document.getElementById('sectionTitle');
    if (category === 'all') {
        sectionTitle.textContent = 'All Photographs';
    } else {
        const cat = categories.find(c => c.id === category);
        sectionTitle.textContent = cat ? cat.label : 'Photographs';
    }

    // Update photo count
    document.getElementById('photoCount').textContent = 
        `${filteredImages.length} photographs from our school archives`;

    // Show/hide gallery items
    document.querySelectorAll('.gallery-item').forEach(item => {
        const itemCategory = item.dataset.category;
        if (category === 'all' || itemCategory === category) {
            item.style.display = 'block';
            item.classList.remove('visible');
            setTimeout(() => item.classList.add('visible'), 50);
        } else {
            item.style.display = 'none';
        }
    });
}

// Open lightbox
function openLightbox(imageId) {
    const index = filteredImages.findIndex(img => img.id === imageId);
    if (index === -1) return;
    
    currentLightboxIndex = index;
    updateLightboxContent();
    
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        lightbox.classList.add('visible');
    }, 10);
}

// Close lightbox
function closeLightbox(event) {
    if (event && event.target !== document.getElementById('lightbox')) return;
    
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('visible');
    
    setTimeout(() => {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }, 300);
}

// Navigate lightbox
function navigateLightbox(direction) {
    const newIndex = currentLightboxIndex + direction;
    if (newIndex >= 0 && newIndex < filteredImages.length) {
        currentLightboxIndex = newIndex;
        updateLightboxContent();
    }
}

// Update lightbox content
function updateLightboxContent() {
    const image = filteredImages[currentLightboxIndex];
    if (!image) return;
    
    document.getElementById('lightboxImage').src = image.src;
    document.getElementById('lightboxImage').alt = image.title;
    document.getElementById('lightboxTitle').textContent = image.title;
    
    const categoryLabel = categories.find(c => c.id === image.category)?.label || image.category;
    document.getElementById('lightboxMeta').textContent = categoryLabel;
    document.getElementById('lightboxCounter').textContent = 
        `${currentLightboxIndex + 1} of ${filteredImages.length}`;
    
    // Update navigation buttons visibility
    document.getElementById('prevBtn').style.visibility = 
        currentLightboxIndex > 0 ? 'visible' : 'hidden';
    document.getElementById('nextBtn').style.visibility = 
        currentLightboxIndex < filteredImages.length - 1 ? 'visible' : 'hidden';
}

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox.classList.contains('active')) return;
    
    if (e.key === 'ArrowLeft') navigateLightbox(-1);
    if (e.key === 'ArrowRight') navigateLightbox(1);
    if (e.key === 'Escape') closeLightbox();
});

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    initGalleryAnimations();
});
</script>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>