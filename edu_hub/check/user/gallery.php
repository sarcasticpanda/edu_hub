<?php
require_once 'navbar.php';
require_once '../../admin/includes/db.php';

$school_name = getSchoolConfig('school_name', 'School CMS');
$logo_path = getSchoolConfig('logo_path', '../images/logo_placeholder.png');

// Always fetch all images for filtering and display
$stmt = $pdo->query("SELECT * FROM gallery_images WHERE display_location IN ('Main Gallery', 'Both') ORDER BY created_at DESC");
$images = $stmt->fetchAll();

// Get categories for filtering (for the buttons)
$all_categories_for_filter = [];
$stmt = $pdo->query("SELECT DISTINCT category FROM gallery_images WHERE display_location IN ('Main Gallery', 'Both') AND category IS NOT NULL AND category != '' ORDER BY category ASC");
$all_categories_for_filter = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; }
        .gallery-header {
            text-align: center;
            margin-top: 100px;
            margin-bottom: 20px;
        }
        .gallery-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.7rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #D32F2F;
            text-shadow: 0 2px 8px #fff3, 0 1px 0 #fff;
        }
        .gallery-header p {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            color: #00539C;
            font-weight: 600;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .gallery-filters {
            text-align: center;
            margin-bottom: 30px;
        }
        .gallery-filters .btn {
            margin: 0 8px 8px 0;
            border-radius: 20px;
            font-weight: 600;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            padding: 0 20px 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(30,42,68,0.10);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.08);
        }
        .gallery-item .overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: rgba(30,42,68,0.6);
            color: #fff;
            padding: 10px 16px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 1rem;
        }
        .gallery-item:hover .overlay {
            opacity: 1;
        }
        @media (max-width: 600px) {
            .gallery-item img { height: 120px; }
        }
        /* Scroll Animation */
        .scroll-fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .scroll-fade-in.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <main style="margin-top: 56px;">
    <div class="gallery-header">
        <h1 style="font-family: 'Poppins', sans-serif; font-size: 2.8rem; font-weight: 800; letter-spacing: 1.5px; color: #1E2A44; text-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            PHOTO GALLERY OF <span style="color: #D32F2F;"><?= htmlspecialchars($school_name) ?></span>
        </h1>
    </div>

    <div class="container text-center mb-4">
        <div class="gallery-filters">
            <button class="btn btn-primary filter-btn" data-filter="all">All</button>
            <?php foreach ($all_categories_for_filter as $cat): ?>
                <button class="btn btn-outline-primary filter-btn" data-filter="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucfirst($cat)) ?></button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="gallery-grid">
        <?php if (empty($images)): ?>
            <div class="alert alert-info col-12">No images found in the gallery.</div>
        <?php else: ?>
            <?php foreach ($images as $image): ?>
                <div class="gallery-item scroll-fade-in" data-category="<?= htmlspecialchars($image['category']) ?>">
                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
                    <div class="overlay">
                        <?= htmlspecialchars($image['title']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightboxModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100vw; height:100vh; background:rgba(30,42,68,0.85); align-items:center; justify-content:center;">
        <span id="lightboxClose" style="position:absolute; top:30px; right:40px; font-size:2.5rem; color:#fff; cursor:pointer; z-index:10001;">&times;</span>
        <img id="lightboxImg" src="" alt="Large Preview" style="max-width:90vw; max-height:80vh; border-radius:32px; box-shadow:0 4px 32px #000a; display:block; margin:auto; transform: scale(0.95); opacity:0; transition: all 0.35s cubic-bezier(.4,2,.6,1);">
    </div>
    <?php include 'footer.php'; ?>
    </main>
    <script>
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('btn-primary'));
                filterBtns.forEach(b => b.classList.add('btn-outline-primary'));
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                const filter = this.getAttribute('data-filter');
                galleryItems.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category').includes(filter)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        // Lightbox functionality
        const lightboxModal = document.getElementById('lightboxModal');
        const lightboxImg = document.getElementById('lightboxImg');
        const lightboxClose = document.getElementById('lightboxClose');
        galleryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const img = item.querySelector('img');
                lightboxImg.src = img.src;
                lightboxModal.style.display = 'flex';
                setTimeout(() => {
                    lightboxImg.style.transform = 'scale(1) rotate(0deg)';
                    lightboxImg.style.opacity = '1';
                }, 10);
            });
        });
        function closeLightbox() {
            lightboxImg.style.transform = 'scale(0.95)';
            lightboxImg.style.opacity = '0';
            setTimeout(() => {
                lightboxModal.style.display = 'none';
                lightboxImg.src = '';
            }, 300);
        }
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxModal.addEventListener('click', function(e) {
            if (e.target === lightboxModal) {
                closeLightbox();
            }
        });

        // Scroll animation with Intersection Observer
        const observerOptions = {
            root: null, // viewport
            rootMargin: '0px',
            threshold: 0.1 // 10% of item visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        galleryItems.forEach(item => {
            observer.observe(item);
        });
    </script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript for filtering functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-link');
        const grids = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const category = this.getAttribute('data-bs-target');
                grids.forEach(grid => {
                    if (grid.id === category.substring(1)) {
                        grid.classList.add('show', 'active');
                    } else {
                        grid.classList.remove('show', 'active');
                    }
                });
            });
        });
    });
</script>