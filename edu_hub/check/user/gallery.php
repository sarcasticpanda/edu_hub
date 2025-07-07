<?php
require_once '../admin/includes/db.php';

// Get all gallery images
$images = [];
try {
    $stmt = $pdo->query("SELECT * FROM gallery_images WHERE is_active = 1 ORDER BY created_at DESC");
    $images = $stmt->fetchAll();
} catch (Exception $e) {
    $images = [];
}

// Get categories
$categories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM gallery_images WHERE is_active = 1");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $categories = ['photography', 'travel', 'nature', 'fashion', 'lifestyle'];
}

include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - <?= getSchoolConfig('school_name', 'School') ?></title>
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
            transform: scale(1.08) rotate(-2deg);
        }
        .gallery-item .overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: rgba(30,42,68,0.7);
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
        .no-images {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="gallery-header">
        <h1>WELCOME TO <?= strtoupper(getSchoolConfig('school_name', 'SCHOOL')) ?> PHOTO GALLERY</h1>
        <p>CAPTURING MOMENTS & MEMORIES</p>
    </div>
    
    <div class="gallery-filters">
        <button class="btn btn-dark filter-btn" data-filter="all">All</button>
        <?php foreach ($categories as $category): ?>
            <button class="btn btn-outline-dark filter-btn" data-filter="<?= htmlspecialchars($category) ?>">
                <?= ucfirst(htmlspecialchars($category)) ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <div class="gallery-grid">
        <?php if (empty($images)): ?>
            <div class="no-images col-12">
                <i class="fas fa-images fa-3x mb-3"></i>
                <h4>No Images Available</h4>
                <p>Gallery images will appear here once they are uploaded by the administrator.</p>
            </div>
        <?php else: ?>
            <?php foreach ($images as $image): ?>
                <div class="gallery-item" data-category="<?= htmlspecialchars($image['category']) ?>">
                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
                    <div class="overlay">
                        <strong><?= htmlspecialchars($image['title']) ?></strong>
                        <?php if ($image['description']): ?>
                            <br><small><?= htmlspecialchars($image['description']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Lightbox Modal -->
    <div id="lightboxModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100vw; height:100vh; background:rgba(30,42,68,0.85); align-items:center; justify-content:center;">
        <span id="lightboxClose" style="position:absolute; top:30px; right:40px; font-size:2.5rem; color:#fff; cursor:pointer; z-index:10001;">&times;</span>
        <img id="lightboxImg" src="" alt="Large Preview" style="max-width:90vw; max-height:80vh; border-radius:32px; box-shadow:0 4px 32px #000a; display:block; margin:auto; transform: scale(0.85) rotate(-3deg); opacity:0; transition: all 0.35s cubic-bezier(.4,2,.6,1);">
        <div id="lightboxInfo" style="position:absolute; bottom:50px; left:50%; transform:translateX(-50%); color:#fff; text-align:center; max-width:80%;"></div>
    </div>
    
    <script>
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('btn-dark'));
                filterBtns.forEach(b => b.classList.add('btn-outline-dark'));
                this.classList.remove('btn-outline-dark');
                this.classList.add('btn-dark');
                
                const filter = this.getAttribute('data-filter');
                galleryItems.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
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
        const lightboxInfo = document.getElementById('lightboxInfo');
        const lightboxClose = document.getElementById('lightboxClose');
        
        galleryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const img = item.querySelector('img');
                const overlay = item.querySelector('.overlay');
                
                lightboxImg.src = img.src;
                lightboxInfo.innerHTML = overlay.innerHTML;
                lightboxModal.style.display = 'flex';
                
                setTimeout(() => {
                    lightboxImg.style.transform = 'scale(1) rotate(0deg)';
                    lightboxImg.style.opacity = '1';
                }, 10);
            });
        });
        
        function closeLightbox() {
            lightboxImg.style.transform = 'scale(0.85) rotate(-3deg)';
            lightboxImg.style.opacity = '0';
            setTimeout(() => {
                lightboxModal.style.display = 'none';
                lightboxImg.src = '';
                lightboxInfo.innerHTML = '';
            }, 300);
        }
        
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxModal.addEventListener('click', function(e) {
            if (e.target === lightboxModal) {
                closeLightbox();
            }
        });
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>