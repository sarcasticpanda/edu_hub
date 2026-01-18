<?php
// Data fetching with fallbacks for index.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../admin/includes/db.php';

// Helper function to fix old image paths (check/ -> storage/)
function fixImagePath($path) {
    if (empty($path)) return $path;
    $path = str_replace('/check/images/', '/storage/images/', $path);
    $path = str_replace('/check/gallery/', '/storage/gallery/', $path);
    $path = str_replace('/check/leadership/', '/storage/leadership/', $path);
    $path = str_replace('/check/notice_attachments/', '/storage/notice_attachments/', $path);
    return $path;
}

try {
    // School config
    $school_config = $pdo->query("SELECT * FROM school_config WHERE id = 1")->fetch();
    $school_name_telugu = $school_config['school_name_telugu'] ?? 'జెడ్‌పీహెచ్‌ఎస్, బొమ్మలరామారం';
    $school_name_english = $school_config['school_name_english'] ?? 'ZPHS, BOMMALARAMARAM';
    $school_name_subtitle = $school_config['school_name_subtitle'] ?? 'Zilla Parishad High School';
    
    // Section descriptions
    $students_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'students_section'")->fetch();
    $faculty_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'faculty_section'")->fetch();
    $infrastructure_section = $pdo->query("SELECT * FROM homepage_content WHERE section = 'infrastructure_section'")->fetch();
    
    $students_title = $students_section['title'] ?? 'Our Students: Future Leaders';
    $students_description = $students_section['content'] ?? 'At Government High School, we nurture young minds to become confident, capable, and compassionate leaders. Our students excel in academics, sports, and extracurricular activities, making us proud every day.';
    
    $faculty_title = $faculty_section['title'] ?? 'Our Faculty Members';
    $faculty_description = $faculty_section['content'] ?? 'Our dedicated and experienced faculty members are committed to nurturing young minds. They bring passion, expertise, and innovation to create an inspiring learning environment.';
    
    $infrastructure_title = $infrastructure_section['title'] ?? 'Our Campus & Infrastructure';
    $infrastructure_description = $infrastructure_section['content'] ?? 'Explore our world-class facilities and vibrant campus. Drag to explore, click to expand.';
    
    // Student login status
    $student_logged_in = isset($_SESSION['student_email']) || isset($_SESSION['student_id']);
    $student_name = $_SESSION['student_name'] ?? ($_SESSION['student_email'] ?? '');
    
    // Footer data
    $footer_data = [];
    $result = $pdo->query("SELECT section, content FROM footer_content");
    while ($row = $result->fetch()) {
        $footer_data[$row['section']] = $row['content'];
    }
    
    // Top bar officials (3 small icons)
    $officials_topbar_db = $pdo->query("SELECT * FROM government_officials WHERE is_active = 1 ORDER BY display_order ASC LIMIT 3")->fetchAll();
    
    // Featured events for hero carousel
    $featured_events_db = $pdo->query("SELECT * FROM events WHERE is_pinned = 1 ORDER BY display_order DESC LIMIT 3")->fetchAll();
    if (empty($featured_events_db)) {
        $featured_events_db = $pdo->query("SELECT * FROM events ORDER BY event_date DESC LIMIT 3")->fetchAll();
    }
    
    // News ticker
    $newsItems_db = $pdo->query("SELECT * FROM news_ticker WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    
    // Faculty from new faculty table (for homepage)
    $faculty_homepage = $pdo->query("SELECT * FROM faculty WHERE is_featured = 1 AND is_active = 1 ORDER BY display_order ASC LIMIT 4")->fetchAll();
    
    // Infrastructure from new infrastructure table (for homepage)
    $infrastructure_homepage = $pdo->query("SELECT * FROM infrastructure WHERE is_active = 1 ORDER BY display_order ASC LIMIT 8")->fetchAll();
    
    // Events carousel (no limit - show all unpinned events)
    $events_db = $pdo->query("SELECT * FROM events WHERE is_pinned = 0 AND is_active = 1 ORDER BY event_date DESC")->fetchAll();
    
    // Notices
    $notices_db = $pdo->query("SELECT * FROM notices WHERE is_active = 1 ORDER BY is_pinned DESC, created_at DESC LIMIT 6")->fetchAll();
    
    // Government officials cards
    $officials_cards_db = $pdo->query("SELECT * FROM government_officials WHERE is_active = 1 ORDER BY display_order ASC LIMIT 2")->fetchAll();
    
    // School at Glance - Get images marked for homepage display (case-insensitive)
    $schoolOverview_db = $pdo->query("SELECT * FROM gallery_images 
                                       WHERE LOWER(display_location) IN ('homepage', 'both') 
                                       ORDER BY created_at DESC 
                                       LIMIT 6")->fetchAll();
    
    // Students
    $students_db = $pdo->query("SELECT * FROM student_showcase WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6")->fetchAll();
    
    // Infrastructure - Get campus/infrastructure category images for homepage
    $infrastructure_db = $pdo->query("SELECT * FROM gallery_images 
                                       WHERE category = 'campus' AND LOWER(display_location) IN ('homepage', 'both') 
                                       ORDER BY created_at DESC 
                                       LIMIT 6")->fetchAll();
    
    // Gallery - Get images marked for homepage (capped at 8 max)
    $galleryGeneral_db = $pdo->query("SELECT * FROM gallery_images 
                                       WHERE LOWER(display_location) IN ('homepage', 'both') 
                                       ORDER BY created_at DESC 
                                       LIMIT 8")->fetchAll();
    
    // Faculty
    $faculty_by_dept = [];
    $facultyDepts = ['Primary', 'Junior', 'Senior', 'Non-Teaching'];
    foreach ($facultyDepts as $dept) {
        $faculty = $pdo->query("SELECT * FROM leadership WHERE department = '$dept' AND is_featured = 1 ORDER BY feature_order")->fetchAll();
        if (!empty($faculty)) {
            $faculty_by_dept[$dept] = $faculty;
        }
    }
    
    // Achievements
    $achievements_db = $pdo->query("SELECT * FROM achievements WHERE is_active = 1 ORDER BY achievement_date DESC LIMIT 3")->fetchAll();
    
} catch (Exception $e) {
    $school_name_telugu = 'జెడ్‌పీహెచ్‌ఎస్, బొమ్మలరామారం';
    $school_name_english = 'ZPHS, BOMMALARAMARAM';
    $school_name_subtitle = 'Zilla Parishad High School';
    $student_logged_in = false;
    $student_name = '';
    $footer_data = [];
    $officials_topbar_db = [];
    $featured_events_db = [];
    $newsItems_db = [];
    $events_db = [];
    $notices_db = [];
    $officials_cards_db = [];
    $schoolOverview_db = [];
    $students_db = [];
    $infrastructure_db = [];
    $galleryGeneral_db = [];
    $faculty_by_dept = [];
    $achievements_db = [];
    $faculty_homepage = [];
    $infrastructure_homepage = [];
}

// Apply fallbacks
$officials_topbar = !empty($officials_topbar_db) ? array_map(function($item) {
    return ['id' => $item['id'], 'name' => $item['name'], 'image' => fixImagePath($item['image_path'])];
}, $officials_topbar_db) : [
    ['id' => 1, 'name' => 'Indian National Flag', 'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/41/Flag_of_India.svg/1200px-Flag_of_India.svg.png'],
    ['id' => 2, 'name' => 'Shri A. Revanth Reddy', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Revanth_Reddy_official_portrait.jpg/440px-Revanth_Reddy_official_portrait.jpg'],
    ['id' => 3, 'name' => 'Education Minister', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face']
];

$nav_items = [
    ['label' => 'HOME', 'href' => '/2026/edu_hub/edu_hub/public/index.php'],
    ['label' => 'ABOUT US', 'href' => '/2026/edu_hub/edu_hub/public/about.php'],
    ['label' => 'ADMINISTRATION', 'href' => '#'],
    ['label' => 'ACADEMICS', 'href' => '#'],
    ['label' => 'GALLERY', 'href' => '/2026/edu_hub/edu_hub/public/gallery.php'],
    ['label' => 'NOTICES', 'href' => '/2026/edu_hub/edu_hub/public/notices.php'],
    ['label' => 'FORMS', 'href' => '#'],
    ['label' => 'CONTACT US', 'href' => '/2026/edu_hub/edu_hub/public/contact.php']
];

$featured_events = !empty($featured_events_db) ? array_map(function($item) {
    return [
        'id' => $item['id'],
        'title' => $item['title'],
        'subtitle' => date('F d, Y', strtotime($item['event_date'])),
        'description' => substr($item['description'] ?? '', 0, 100),
        'image' => fixImagePath($item['image_path']),
        'isPinned' => true
    ];
}, $featured_events_db) : [
    ['id' => 1, 'title' => 'Welcome to Our School', 'subtitle' => 'Quality Education', 'description' => 'Providing quality education to all students', 'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=500&fit=crop', 'isPinned' => true],
    ['id' => 2, 'title' => 'Annual Day Celebrations', 'subtitle' => 'January 26th, 2026', 'description' => 'Republic Day Special Program', 'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&h=500&fit=crop', 'isPinned' => true],
    ['id' => 3, 'title' => 'Academic Excellence', 'subtitle' => 'February 15th, 2026', 'description' => 'Showcasing Student Achievements', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&h=500&fit=crop', 'isPinned' => true]
];

$news_items = !empty($newsItems_db) ? array_map(function($item) {
    return $item['content'] ?? $item['news_text'] ?? '';
}, $newsItems_db) : [
    'Welcome to our school website',
    'Admission open for 2026-2027 academic year',
    'Quality education for all students',
    'Parent-Teacher meeting updates will be posted here',
    'Check notices section for important announcements'
];

$events = !empty($events_db) ? array_map(function($item) {
    return ['id' => $item['id'], 'title' => $item['title'], 'image' => $item['image_path']];
}, $events_db) : [
    ['id' => 1, 'title' => 'School Assembly', 'image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=300&h=200&fit=crop'],
    ['id' => 2, 'title' => 'Sports Day Event', 'image' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=300&h=200&fit=crop'],
    ['id' => 3, 'title' => 'Science Exhibition', 'image' => 'https://images.unsplash.com/photo-1567168544813-cc03465b4fa8?w=300&h=200&fit=crop'],
    ['id' => 4, 'title' => 'Cultural Programs', 'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=300&h=200&fit=crop'],
    ['id' => 5, 'title' => 'Annual Day', 'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=300&h=200&fit=crop'],
    ['id' => 6, 'title' => 'Independence Day', 'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=300&h=200&fit=crop'],
    ['id' => 7, 'title' => 'Republic Day', 'image' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=300&h=200&fit=crop']
];

$notices = !empty($notices_db) ? array_map(function($item) {
    return [
        'id' => $item['id'],
        'title' => $item['title'],
        'date' => $item['created_at'],
        'isPinned' => $item['is_pinned'] == 1,
        'category' => $item['category'] ?? 'notice',
        'issuedBy' => $item['posted_by'] ?? 'Administration',
        'isNew' => (strtotime($item['created_at']) > strtotime('-7 days'))
    ];
}, $notices_db) : [
    ['id' => 1, 'title' => 'Annual Academic Calendar 2026-2027 Released', 'date' => '2026-01-08', 'isPinned' => true, 'category' => 'circular', 'issuedBy' => 'Principal Office', 'isNew' => true],
    ['id' => 2, 'title' => 'Public Examination Schedule - Class X & XII', 'date' => '2026-01-05', 'isPinned' => true, 'category' => 'announcement', 'issuedBy' => 'Examination Cell', 'isNew' => true],
    ['id' => 3, 'title' => 'Revised Timetable for Board Examinations 2026', 'date' => '2026-01-03', 'isPinned' => false, 'category' => 'circular', 'issuedBy' => 'Examination Cell', 'isNew' => false],
    ['id' => 4, 'title' => 'Admission Open for Academic Year 2026-2027', 'date' => '2026-01-01', 'isPinned' => false, 'category' => 'announcement', 'issuedBy' => 'Admission Office', 'isNew' => false]
];

$officials = !empty($officials_cards_db) ? array_map(function($item) {
    return [
        'id' => $item['id'],
        'name' => $item['name'],
        'designation' => $item['position'],
        'bio' => $item['bio'] ?? '',
        'image' => $item['image_path'],
        'profileLink' => '#',
        'contactLink' => '#',
        'facebook' => '#',
        'linkedin' => '#'
    ];
}, $officials_cards_db) : [
    ['id' => 1, 'name' => 'Sri Anumula Revanth Reddy', 'designation' => 'The Hon\'ble Chief Minister', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Revanth_Reddy_official_portrait.jpg/440px-Revanth_Reddy_official_portrait.jpg', 'profileLink' => '#', 'contactLink' => '#', 'facebook' => '#', 'linkedin' => '#'],
    ['id' => 2, 'name' => 'District Collector', 'designation' => 'Collector & District Magistrate', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face', 'profileLink' => '#', 'contactLink' => '#', 'facebook' => '#', 'linkedin' => '#']
];

$school_gallery = !empty($schoolOverview_db) ? array_map(function($item) {
    return ['id' => $item['id'], 'title' => $item['title'], 'image' => $item['image_path']];
}, $schoolOverview_db) : [
    ['id' => 1, 'title' => 'Main Building', 'image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&h=300&fit=crop'],
    ['id' => 2, 'title' => 'Classrooms', 'image' => 'https://images.unsplash.com/photo-1580537659466-0a9bfa916a54?w=400&h=300&fit=crop'],
    ['id' => 3, 'title' => 'Playground', 'image' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=400&h=300&fit=crop'],
    ['id' => 4, 'title' => 'Library', 'image' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=400&h=300&fit=crop'],
    ['id' => 5, 'title' => 'Computer Lab', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop'],
    ['id' => 6, 'title' => 'Science Lab', 'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&h=300&fit=crop']
];

$students = !empty($students_db) ? array_map(function($item) {
    return ['id' => $item['id'], 'name' => $item['name'], 'role' => $item['role'], 'image' => $item['image_path']];
}, $students_db) : [
    ['id' => 1, 'name' => 'Ravi Kumar', 'role' => 'Head Boy', 'image' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=200&h=250&fit=crop&crop=face'],
    ['id' => 2, 'name' => 'Priya Sharma', 'role' => 'Head Girl', 'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=250&fit=crop&crop=face'],
    ['id' => 3, 'name' => 'Arun Reddy', 'role' => 'Sports Captain', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=250&fit=crop&crop=face'],
    ['id' => 4, 'name' => 'Lakshmi Devi', 'role' => 'Cultural Secretary', 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=250&fit=crop&crop=face'],
    ['id' => 5, 'name' => 'Venkat Rao', 'role' => 'Class Monitor', 'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=250&fit=crop&crop=face'],
    ['id' => 6, 'name' => 'Anjali Singh', 'role' => 'Literary Captain', 'image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&h=250&fit=crop&crop=face']
];

$facilities = !empty($infrastructure_homepage) ? array_map(function($item) {
    return ['id' => $item['id'], 'name' => $item['title'], 'description' => $item['description'] ?? $item['title'], 'image' => $item['image_path']];
}, $infrastructure_homepage) : (!empty($infrastructure_db) ? array_map(function($item) {
    return ['id' => $item['id'], 'name' => $item['title'], 'description' => $item['description'] ?? $item['title'], 'image' => $item['image_path']];
}, $infrastructure_db) : [
    ['id' => 1, 'name' => 'Smart Classrooms', 'description' => 'Well-ventilated smart classrooms', 'image' => 'https://images.unsplash.com/photo-1580537659466-0a9bfa916a54?w=400&h=300&fit=crop'],
    ['id' => 2, 'name' => 'Science Laboratory', 'description' => 'Fully equipped science lab', 'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&h=300&fit=crop'],
    ['id' => 3, 'name' => 'Library', 'description' => 'Extensive book collection', 'image' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=400&h=300&fit=crop'],
    ['id' => 4, 'name' => 'Sports Ground', 'description' => 'Large playground area', 'image' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=400&h=300&fit=crop'],
    ['id' => 5, 'name' => 'Computer Lab', 'description' => 'Modern computer facilities', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop'],
    ['id' => 6, 'name' => 'Assembly Hall', 'description' => 'Multi-purpose auditorium', 'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&h=300&fit=crop'],
    ['id' => 7, 'name' => 'Cafeteria', 'description' => 'Hygienic dining facilities', 'image' => 'https://images.unsplash.com/photo-1567521464027-f127ff144326?w=400&h=300&fit=crop'],
    ['id' => 8, 'name' => 'Art Room', 'description' => 'Creative arts and crafts studio', 'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&h=300&fit=crop'],
    ['id' => 9, 'name' => 'Music Room', 'description' => 'Music and dance practice hall', 'image' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=400&h=300&fit=crop'],
    ['id' => 10, 'name' => 'Indoor Stadium', 'description' => 'Badminton and indoor sports', 'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?w=400&h=300&fit=crop'],
    ['id' => 11, 'name' => 'Medical Room', 'description' => 'First aid and health checkup', 'image' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=400&h=300&fit=crop'],
    ['id' => 12, 'name' => 'Activity Center', 'description' => 'Student club activities hub', 'image' => 'https://images.unsplash.com/photo-1497366412874-3415097a27e7?w=400&h=300&fit=crop']
]);

$gallery_items = !empty($galleryGeneral_db) ? array_map(function($item) {
    return $item['image_path'];
}, $galleryGeneral_db) : [
    'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1562774053-701939374585?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=300&fit=crop',
    'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&h=300&fit=crop'
];

// Faculty is already structured by department - Use new faculty table if available
if (!empty($faculty_homepage)) {
    // Group by department from new faculty table
    $faculty_grouped = [];
    foreach ($faculty_homepage as $f) {
        $dept = $f['department'] ?? 'Primary';
        if (!isset($faculty_grouped[$dept])) {
            $faculty_grouped[$dept] = [];
        }
        $faculty_grouped[$dept][] = $f;
    }
    $departments = [];
    foreach ($faculty_grouped as $deptName => $teachers) {
        $departments[] = [
            'name' => $deptName . ' Department',
            'teachers' => array_map(function($t) {
                return ['id' => $t['id'], 'name' => $t['name'], 'subject' => $t['position'], 'image' => $t['image_path']];
            }, $teachers)
        ];
    }
} elseif (!empty($faculty_by_dept)) {
    $departments = [];
    foreach ($faculty_by_dept as $deptName => $teachers) {
        $departments[] = [
            'name' => $deptName . ' Department',
            'teachers' => array_map(function($t) {
                return ['id' => $t['id'], 'name' => $t['name'], 'subject' => $t['position'], 'image' => $t['photo']];
            }, $teachers)
        ];
    }
} else {
    $departments = [
        ['name' => 'Primary Department', 'teachers' => [
            ['id' => 1, 'name' => 'Mrs. Lakshmi Devi', 'subject' => 'Class Teacher - I', 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face'],
            ['id' => 2, 'name' => 'Mrs. Sarojini', 'subject' => 'Class Teacher - II', 'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face']
        ]],
        ['name' => 'Junior Department', 'teachers' => [
            ['id' => 3, 'name' => 'Ms. Priya Sharma', 'subject' => 'English Literature', 'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&h=200&fit=crop&crop=face'],
            ['id' => 4, 'name' => 'Mr. Venkat Rao', 'subject' => 'Mathematics', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face']
        ]]
    ];
}

$achievements = !empty($achievements_db) ? array_map(function($item) {
    $year = !empty($item['achievement_date']) ? date('Y', strtotime($item['achievement_date'])) : date('Y');
    return ['id' => $item['id'], 'title' => $item['title'], 'year' => $year, 'image' => $item['image_path']];
}, $achievements_db) : [
    ['id' => 1, 'title' => 'State Level Science Exhibition - First Prize', 'year' => '2025', 'image' => 'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=400&h=300&fit=crop'],
    ['id' => 2, 'title' => 'District Sports Championship Winner', 'year' => '2025', 'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=300&fit=crop'],
    ['id' => 3, 'title' => 'Best Government School Award', 'year' => '2024', 'image' => 'https://images.unsplash.com/photo-1569937756447-1d44f657dc69?w=400&h=300&fit=crop']
];

// Helper function to format date
function formatDate($dateStr) {
    try {
        $date = new DateTime($dateStr);
        return $date->format('d M Y');
    } catch (Exception $e) {
        return $dateStr;
    }
}
?>
