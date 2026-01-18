<?php
session_start();
require_once __DIR__ . '/../admin/includes/db.php';

// Fetch school config for page title
try {
    $stmt = $pdo->query("SELECT school_name_english, school_name_subtitle FROM school_config WHERE id = 1");
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
    $school_name = $config['school_name_english'] ?? 'School';
} catch (Exception $e) {
    $school_name = 'School';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - <?= htmlspecialchars($school_name) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .events-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
        }
        
        .events-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .events-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .events-container {
            max-width: 1200px;
            margin: -40px auto 60px;
            padding: 0 20px;
        }
        
        .event-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .event-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .event-content {
            padding: 25px;
        }
        
        .event-date {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .event-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .event-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .event-meta {
            display: flex;
            gap: 20px;
            color: #888;
            font-size: 0.9rem;
        }
        
        .event-meta i {
            color: #667eea;
        }
        
        .no-events {
            background: white;
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .no-events i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-events h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .no-events p {
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Include Header and Navbar -->
    <?php include __DIR__ . '/includes/header_navbar.php'; ?>

    <!-- Events Hero Section -->
    <div class="events-hero">
        <h1><i class="fas fa-calendar-alt me-3"></i>School Events</h1>
        <p>Stay updated with our upcoming and past events</p>
    </div>

    <!-- Events Container -->
    <div class="events-container">
        <?php
        // Fetch all active events from database - show pinned events first, then others
        try {
            $stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 ORDER BY is_pinned DESC, event_date DESC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $events = [];
        }
        
        if (empty($events)):
        ?>
        <!-- No Events Message -->
        <div class="no-events">
            <i class="fas fa-calendar-times"></i>
            <h3>No Events Available</h3>
            <p>Events will be displayed here once they are added by the administrator.</p>
        </div>
        <?php else: ?>
        <!-- Events Grid -->
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6">
                <div class="event-card">
                    <?php if (!empty($event['image_path'])): ?>
                    <img src="<?= htmlspecialchars($event['image_path']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
                    <?php else: ?>
                    <div class="event-image"></div>
                    <?php endif; ?>
                    
                    <div class="event-content">
                        <?php if (!empty($event['event_date'])): ?>
                        <span class="event-date">
                            <i class="fas fa-calendar me-2"></i><?= date('F d, Y', strtotime($event['event_date'])) ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($event['is_pinned'] == 1): ?>
                        <span class="event-date" style="background: #ff6b6b; margin-left: 10px;">
                            <i class="fas fa-thumbtack me-2"></i>Pinned
                        </span>
                        <?php endif; ?>
                        
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        
                        <?php if (!empty($event['subtitle'])): ?>
                        <p style="color: #888; font-size: 0.95rem; margin-bottom: 10px; font-style: italic;">
                            <?= htmlspecialchars($event['subtitle']) ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($event['description'])): ?>
                        <p class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
