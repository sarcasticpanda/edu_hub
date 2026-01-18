<?php
session_start();
// DB connection
require_once __DIR__ . '/../admin/includes/db.php';

// Fetch all data
$hero = $pdo ? $pdo->query("SELECT * FROM about_hero_content LIMIT 1")->fetch() : null;
$about = $pdo ? $pdo->query("SELECT * FROM about_admin_panel ORDER BY id DESC LIMIT 1")->fetch() : null;
$details = $pdo ? $pdo->query("SELECT section_type, content FROM about_details")->fetchAll(PDO::FETCH_KEY_PAIR) : [];
$leadership = $pdo ? $pdo->query("SELECT * FROM leadership ORDER BY display_order ASC, created_at DESC")->fetchAll() : [];
$students = $pdo ? $pdo->query("SELECT * FROM about_students WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC")->fetchAll() : [];
$achievements = $pdo ? $pdo->query("SELECT * FROM achievements WHERE is_active = 1 ORDER BY display_order ASC, achievement_date DESC LIMIT 6")->fetchAll() : [];

// Group leadership by section
$leadershipSections = [
    'Individual' => [],
    'Primary' => [],
    'Junior' => [],
    'Senior' => [],
    'Non-Teaching' => []
];
foreach ($leadership as $l) {
    if (isset($leadershipSections[$l['section']])) {
        $leadershipSections[$l['section']][] = $l;
    }
}

// Group students by category
$studentGroups = [
    'council' => [],
    'leaders' => [],
    'clubs' => [],
    'sports' => []
];
foreach ($students as $s) {
    if (isset($studentGroups[$s['category']])) {
        $studentGroups[$s['category']][] = $s;
    }
}

// Count totals
$totalStaff = count($leadership);
$totalStudentReps = count($students);

// Default values
$heroTitle = $hero['hero_title'] ?? 'About Our School';
$heroSubtitle = $hero['hero_subtitle'] ?? 'Nurturing minds, building futures since establishment';
$heroTagline = $hero['hero_tagline'] ?? 'Discover Our Legacy';
$heroBg = $hero['background_image'] ?? 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920&h=1080&fit=crop';
$stat1Value = $hero['stat1_value'] ?? '25+';
$stat1Label = $hero['stat1_label'] ?? 'Years of Legacy';
$stat2Value = $hero['stat2_value'] ?? '1000+';
$stat2Label = $hero['stat2_label'] ?? 'Students Shaped';
$stat3Value = $hero['stat3_value'] ?? '50+';
$stat3Label = $hero['stat3_label'] ?? 'Dedicated Faculty';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>About Us | <?= htmlspecialchars($about['page_title'] ?? 'Our School') ?></title>
    <meta name="description" content="Learn about our school, leadership, faculty, and students. Nurturing minds and building futures.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Lucide Icons (needed for navbar) -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'hsl(142, 43%, 27%)',
                        'primary-foreground': 'hsl(0, 0%, 100%)',
                        saffron: 'hsl(36, 90%, 50%)',
                        muted: 'hsl(60, 4%, 95%)',
                        'muted-foreground': 'hsl(0, 0%, 45%)',
                        foreground: 'hsl(0, 0%, 8%)',
                        border: 'hsl(0, 0%, 90%)',
                        peach: 'hsl(25, 100%, 94%)',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { to { opacity: 1; } }
        
        .slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; transform: translateY(20px); }
        @keyframes slideUp { to { opacity: 1; transform: translateY(0); } }
        
        .rotate-slow { animation: rotateSlow 60s linear infinite; }
        @keyframes rotateSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        /* Filter Pills */
        .filter-pill { background: white; }
        .filter-pill.active { background: hsl(142, 43%, 27%) !important; color: white !important; border-color: hsl(142, 43%, 27%) !important; }
        .filter-pill-saffron { background: white; }
        .filter-pill-saffron.active { background: hsl(36, 90%, 50%) !important; color: white !important; border-color: hsl(36, 90%, 50%) !important; }
        
        /* Cards */
        .staff-card:hover img { transform: scale(1.1); }
        .student-card:hover img { transform: scale(1.05); }
        
        /* Modal */
        .modal-overlay { 
            display: none; 
            position: fixed; 
            inset: 0; 
            background: rgba(20, 30, 25, 0.92); 
            backdrop-filter: blur(12px); 
            z-index: 1000; 
        }
        .modal-overlay.active { display: flex; }
        
        /* Navbar Styles */
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
            flex: 1;
            text-align: center;
        }
        .gov-navbar-link:hover {
            background-color: #ff8c00 !important;
            color: white !important;
        }
    </style>
</head>
<body class="bg-white">
    
    <?php include __DIR__ . '/includes/header_navbar.php'; ?>

    <!-- Hero Section -->
    <section class="relative h-[55vh] min-h-[450px] overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($heroBg) ?>')"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/80"></div>
        
        <!-- Decorative rings -->
        <div class="absolute top-0 left-0 w-96 h-96 border border-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 rotate-slow"></div>
        
        <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
            <p class="text-white/70 text-sm uppercase tracking-[0.4em] mb-4 fade-in"><?= htmlspecialchars($heroTagline) ?></p>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 slide-up"><?= htmlspecialchars($heroTitle) ?></h1>
            <div class="flex items-center justify-center gap-4 mb-6 fade-in" style="animation-delay: 0.3s;">
                <div class="h-px w-16 bg-gradient-to-r from-transparent to-white/50"></div>
                <div class="w-2 h-2 bg-saffron rotate-45"></div>
                <div class="h-px w-16 bg-gradient-to-l from-transparent to-white/50"></div>
            </div>
            <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto fade-in" style="animation-delay: 0.4s;"><?= htmlspecialchars($heroSubtitle) ?></p>
            
            <!-- Stats -->
            <div class="absolute bottom-8 left-0 right-0 fade-in" style="animation-delay: 0.6s;">
                <div class="flex justify-center gap-12 md:gap-20">
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($stat1Value) ?></div>
                        <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($stat1Label) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($stat2Value) ?></div>
                        <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($stat2Label) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($stat3Value) ?></div>
                        <div class="text-xs md:text-sm text-white/60 uppercase tracking-wider"><?= htmlspecialchars($stat3Label) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 max-w-4xl text-center">
            <p class="text-sm uppercase tracking-[0.3em] text-primary mb-4 font-medium">Welcome to</p>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-8"><?= htmlspecialchars($about['page_title'] ?? 'Our School') ?></h2>
            <div class="flex items-center justify-center gap-3 mb-10">
                <div class="h-px w-20 bg-gradient-to-r from-transparent to-border"></div>
                <div class="w-1.5 h-1.5 bg-primary rounded-full"></div>
                <div class="h-px w-20 bg-gradient-to-l from-transparent to-border"></div>
            </div>
            <p class="text-muted-foreground text-lg leading-relaxed mb-6">
                <?= nl2br(htmlspecialchars($about['page_content'] ?? 'Welcome to our institution dedicated to providing quality education and holistic development for all students.')) ?>
            </p>
            <?php 
            $aboutImage = $about['image_path'] ?? '';
            // Fix old check/images paths to storage/images
            $aboutImage = str_replace('/check/images/', '/storage/images/', $aboutImage);
            $aboutImage = str_replace('/check/gallery/', '/storage/gallery/', $aboutImage);
            $aboutImage = str_replace('/check/leadership/', '/storage/leadership/', $aboutImage);
            
            // Check if image exists locally or is a valid URL
            $showImage = false;
            if (!empty($aboutImage)) {
                if (filter_var($aboutImage, FILTER_VALIDATE_URL)) {
                    $showImage = true;
                } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $aboutImage)) {
                    $showImage = true;
                }
            }
            if ($showImage): ?>
            <div class="mt-10">
                <img src="<?= htmlspecialchars($aboutImage) ?>" 
                     alt="<?= htmlspecialchars($about['page_title'] ?? 'School') ?>" 
                     class="w-full max-w-3xl mx-auto rounded-2xl shadow-xl"
                     onerror="this.parentElement.style.display='none'">
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Vision Section (Motto, Objectives, Values) -->
    <section class="py-24 bg-muted/50">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Motto -->
            <div class="text-center mb-20">
                <p class="text-xs uppercase tracking-[0.4em] text-muted-foreground mb-6">Our Motto</p>
                <blockquote class="text-2xl md:text-3xl lg:text-4xl font-light text-foreground italic leading-relaxed mb-6">
                    "<?= htmlspecialchars($details['motto'] ?? 'Tamaso Ma Jyotirgamaya') ?>"
                </blockquote>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                    Lead me from darkness to light. We strive to illuminate the minds of our students with knowledge, wisdom, and the spirit of inquiry.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-16">
                <!-- Objectives -->
                <div>
                    <h3 class="text-xl font-bold text-foreground mb-8 uppercase tracking-wider">Our Objectives</h3>
                    <ul class="space-y-4">
                        <?php 
                        $objectives = explode("\n", $details['objective'] ?? "Provide quality education accessible to all\nDevelop scientific temper and rational thinking\nFoster patriotism and constitutional values\nPrepare students for higher education");
                        foreach ($objectives as $obj): 
                            if (trim($obj)):
                        ?>
                        <li class="flex items-start gap-4 text-muted-foreground">
                            <span class="w-1.5 h-1.5 bg-primary rounded-full mt-2.5 flex-shrink-0"></span>
                            <span class="leading-relaxed"><?= htmlspecialchars(trim($obj)) ?></span>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
                
                <!-- Values -->
                <div>
                    <h3 class="text-xl font-bold text-foreground mb-8 uppercase tracking-wider">Our Values</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php 
                        $values = explode("\n", $details['value'] ?? "Integrity & Honesty\nRespect for All\nExcellence in Education\nCommunity Service\nEnvironmental Awareness\nCultural Heritage");
                        foreach ($values as $val): 
                            if (trim($val)):
                        ?>
                        <div class="text-muted-foreground text-sm py-2">
                            <span class="inline-block w-1 h-1 bg-saffron rounded-full mr-2 mb-0.5"></span>
                            <?= htmlspecialchars(trim($val)) ?>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-20">
                <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground mb-4">Meet the Team</p>
                <h2 class="text-4xl md:text-5xl font-bold text-foreground mb-6">Our Leadership</h2>
                <div class="flex items-center justify-center gap-4">
                    <div class="h-px w-16 bg-gradient-to-r from-transparent to-border"></div>
                    <div class="w-2 h-2 bg-saffron rotate-45"></div>
                    <div class="h-px w-16 bg-gradient-to-l from-transparent to-border"></div>
                </div>
            </div>

            <?php if (!empty($leadershipSections['Individual'])): ?>
            <!-- Principal & Administration -->
            <div class="mb-24">
                <h3 class="text-center text-sm uppercase tracking-[0.3em] text-muted-foreground mb-12">Principal & Administration</h3>
                <div class="flex flex-wrap justify-center gap-16 md:gap-28">
                    <?php foreach ($leadershipSections['Individual'] as $leader): 
                        $leaderData = json_decode($leader['modal_content'] ?? '{}', true);
                    ?>
                    <div class="text-center group cursor-pointer" onclick='openFacultyModal(<?= json_encode([
                        "name" => $leader["name"],
                        "role" => $leader["role"],
                        "image" => $leader["image_path"],
                        "section" => "Administration",
                        "department" => $leaderData["department"] ?? "",
                        "years_worked" => $leaderData["years_worked"] ?? "",
                        "contact_email" => $leaderData["contact_email"] ?? "",
                        "qualification" => $leaderData["qualification"] ?? ""
                    ]) ?>)'>
                        <div class="relative mx-auto mb-6">
                            <div class="absolute -inset-3 rounded-full border-2 border-dashed border-primary/30 rotate-slow"></div>
                            <div class="absolute -inset-1.5 rounded-full bg-gradient-to-b from-primary/20 to-primary/5"></div>
                            <div class="relative overflow-hidden rounded-full shadow-xl w-44 h-44 md:w-52 md:h-52">
                                <img src="<?= htmlspecialchars($leader['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($leader['name']) ?>" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                     onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop'">
                            </div>
                        </div>
                        <h4 class="font-bold text-xl md:text-2xl text-foreground mb-1"><?= htmlspecialchars($leader['name']) ?></h4>
                        <p class="text-base font-medium text-primary"><?= htmlspecialchars($leader['role']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Teaching & Non-Teaching Staff -->
            <?php 
            $teachingStaff = array_merge(
                $leadershipSections['Primary'], 
                $leadershipSections['Junior'], 
                $leadershipSections['Senior'], 
                $leadershipSections['Non-Teaching']
            );
            if (!empty($teachingStaff)): 
            ?>
            <div>
                <h3 class="text-center text-sm uppercase tracking-[0.3em] text-muted-foreground mb-6">Teaching & Non-Teaching Staff</h3>
                
                <!-- Stats Badge -->
                <div class="text-center mb-10">
                    <div class="inline-flex flex-col items-center px-10 py-6 bg-gradient-to-b from-white to-muted/20 rounded-2xl shadow-lg border border-border/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-px w-8 bg-gradient-to-r from-transparent to-primary"></div>
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <div class="h-px w-8 bg-gradient-to-l from-transparent to-primary"></div>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="text-4xl font-bold text-primary"><?= count($teachingStaff) ?></span>
                            <span class="text-sm text-muted-foreground uppercase tracking-widest">Dedicated Staff Members</span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">Across 4 Departments</p>
                    </div>
                </div>

                <!-- Filter Pills -->
                <div id="staff-filters" class="flex flex-wrap justify-center gap-2 mb-10">
                    <button onclick="filterStaff('all')" class="filter-pill active px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">All Departments</button>
                    <button onclick="filterStaff('primary')" class="filter-pill px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Primary</button>
                    <button onclick="filterStaff('junior')" class="filter-pill px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Junior</button>
                    <button onclick="filterStaff('senior')" class="filter-pill px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Senior</button>
                    <button onclick="filterStaff('nonteaching')" class="filter-pill px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Non-Teaching</button>
                </div>

                <!-- Staff Grid -->
                <div id="staff-grid" class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <?php foreach (['Primary', 'Junior', 'Senior', 'Non-Teaching'] as $section): 
                        $deptClass = 'dept-' . strtolower(str_replace('-', '', $section));
                        foreach ($leadershipSections[$section] as $staff): 
                            $staffData = json_decode($staff['modal_content'] ?? '{}', true);
                    ?>
                    <div class="staff-card <?= $deptClass ?> flex items-center gap-4 p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-border/20 hover:border-border/40 cursor-pointer" 
                         onclick='openFacultyModal(<?= json_encode([
                             "name" => $staff["name"],
                             "role" => $staff["role"],
                             "image" => $staff["image_path"],
                             "section" => $section . " Department",
                             "department" => $staffData["department"] ?? "",
                             "years_worked" => $staffData["years_worked"] ?? "",
                             "contact_email" => $staffData["contact_email"] ?? "",
                             "qualification" => $staffData["qualification"] ?? ""
                         ]) ?>)'>
                        <div class="w-14 h-14 rounded-full overflow-hidden shadow-sm flex-shrink-0">
                            <img src="<?= htmlspecialchars($staff['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($staff['name']) ?>"
                                 class="w-full h-full object-cover transition-transform duration-500"
                                 onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-sm text-foreground truncate"><?= htmlspecialchars($staff['name']) ?></h5>
                            <p class="text-xs text-muted-foreground truncate"><?= htmlspecialchars($staff['role']) ?></p>
                            <p class="text-xs text-muted-foreground/70 truncate"><?= htmlspecialchars($section) ?> Department</p>
                        </div>
                        <svg class="w-4 h-4 text-muted-foreground/30 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <?php endforeach; endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Students Section -->
    <?php if (!empty($students)): ?>
    <section class="py-24 bg-muted/50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-20">
                <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground mb-4">Pride of Our School</p>
                <h2 class="text-4xl md:text-5xl font-bold text-foreground mb-6">Our Students</h2>
                <div class="flex items-center justify-center gap-4">
                    <div class="h-px w-16 bg-gradient-to-r from-transparent to-border"></div>
                    <div class="w-2 h-2 bg-saffron rotate-45"></div>
                    <div class="h-px w-16 bg-gradient-to-l from-transparent to-border"></div>
                </div>
            </div>

            <?php if (!empty($studentGroups['council'])): ?>
            <!-- Student Council -->
            <div class="mb-20">
                <h3 class="text-center text-sm uppercase tracking-[0.3em] text-muted-foreground mb-12">Student Council</h3>
                <div class="flex flex-wrap justify-center gap-12 md:gap-20">
                    <?php foreach ($studentGroups['council'] as $student): ?>
                    <div class="text-center group cursor-pointer" onclick="openModal('<?= htmlspecialchars(addslashes($student['name'])) ?>', '<?= htmlspecialchars(addslashes($student['role'])) ?>', '<?= htmlspecialchars($student['image_path']) ?>', 'Student Council')">
                        <div class="relative mx-auto mb-5">
                            <div class="relative overflow-hidden rounded-2xl shadow-xl w-40 h-48 md:w-48 md:h-56 transition-transform duration-300 group-hover:-translate-y-1">
                                <img src="<?= htmlspecialchars($student['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($student['name']) ?>"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                     onerror="this.src='https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=200&h=250&fit=crop'">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                    <div class="text-xs uppercase tracking-widest opacity-80 mb-1"><?= htmlspecialchars($student['role']) ?></div>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-lg text-foreground"><?= htmlspecialchars($student['name']) ?></h4>
                        <p class="text-sm font-medium text-saffron"><?= htmlspecialchars($student['role']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Student Representatives -->
            <?php 
            $representatives = array_merge($studentGroups['leaders'], $studentGroups['clubs'], $studentGroups['sports']);
            if (!empty($representatives)): 
            ?>
            <div>
                <h3 class="text-center text-sm uppercase tracking-[0.3em] text-muted-foreground mb-6">Student Representatives</h3>
                
                <!-- Stats Badge -->
                <div class="text-center mb-10">
                    <div class="inline-flex flex-col items-center px-10 py-6 bg-gradient-to-b from-white to-muted/20 rounded-2xl shadow-lg border border-border/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-px w-8 bg-gradient-to-r from-transparent to-saffron"></div>
                            <svg class="w-6 h-6 text-saffron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                            </svg>
                            <div class="h-px w-8 bg-gradient-to-l from-transparent to-saffron"></div>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="text-4xl font-bold text-saffron"><?= count($representatives) ?></span>
                            <span class="text-sm text-muted-foreground uppercase tracking-widest">Student Representatives</span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">Across 3 Categories</p>
                    </div>
                </div>

                <!-- Filter Pills -->
                <div id="student-filters" class="flex flex-wrap justify-center gap-2 mb-10">
                    <button onclick="filterStudents('all')" class="filter-pill-saffron active px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">All Categories</button>
                    <button onclick="filterStudents('leaders')" class="filter-pill-saffron px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Class Leaders</button>
                    <button onclick="filterStudents('clubs')" class="filter-pill-saffron px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Club Secretaries</button>
                    <button onclick="filterStudents('sports')" class="filter-pill-saffron px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-muted-foreground border border-border hover:shadow-md">Sports Captains</button>
                </div>

                <!-- Student Grid -->
                <div id="student-grid" class="max-w-5xl mx-auto grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <?php foreach (['leaders', 'clubs', 'sports'] as $category): 
                        $catLabels = ['leaders' => 'Class Leaders', 'clubs' => 'Club Secretaries', 'sports' => 'Sports Captains'];
                        foreach ($studentGroups[$category] as $student): 
                    ?>
                    <div class="student-card cat-<?= $category ?> relative bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-border/20 cursor-pointer" 
                         onclick="openModal('<?= htmlspecialchars(addslashes($student['name'])) ?>', '<?= htmlspecialchars(addslashes($student['role'])) ?>', '<?= htmlspecialchars($student['image_path']) ?>', '<?= $catLabels[$category] ?>')">
                        <div class="relative aspect-[4/5] overflow-hidden">
                            <img src="<?= htmlspecialchars($student['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($student['name']) ?>"
                                 class="w-full h-full object-cover transition-transform duration-500"
                                 onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=250&fit=crop'">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                                <h5 class="font-semibold text-sm truncate"><?= htmlspecialchars($student['name']) ?></h5>
                                <p class="text-xs opacity-90 truncate"><?= htmlspecialchars($student['role']) ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-white/20 rounded text-[10px] backdrop-blur-sm"><?= $catLabels[$category] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Achievements Section -->
    <?php if (!empty($achievements)): ?>
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground mb-4">Excellence Recognized</p>
                <h2 class="text-4xl md:text-5xl font-bold text-foreground mb-6">Our Achievements</h2>
                <div class="flex items-center justify-center gap-4">
                    <div class="h-px w-16 bg-gradient-to-r from-transparent to-border"></div>
                    <div class="w-2 h-2 bg-saffron rotate-45"></div>
                    <div class="h-px w-16 bg-gradient-to-l from-transparent to-border"></div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                <?php foreach ($achievements as $achievement): ?>
                <div class="group">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-border/50 hover:shadow-lg transition-all duration-300">
                        <div class="aspect-video overflow-hidden relative">
                            <img src="<?= htmlspecialchars($achievement['image_path'] ?? 'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=400&h=300&fit=crop') ?>" 
                                 alt="<?= htmlspecialchars($achievement['title']) ?>"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 onerror="this.src='https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=400&h=300&fit=crop'">
                            <div class="absolute top-3 right-3">
                                <span class="bg-saffron text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    <?= htmlspecialchars($achievement['year'] ?? date('Y', strtotime($achievement['achievement_date'] ?? 'now'))) ?>
                                </span>
                            </div>
                            <div class="absolute top-3 left-3">
                                <div class="w-9 h-9 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-foreground text-sm leading-snug"><?= htmlspecialchars($achievement['title']) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Modal Overlay -->
    <div id="modal" class="modal-overlay items-center justify-center p-4" onclick="closeModal()">
        <div class="relative max-w-lg w-full" onclick="event.stopPropagation()">
            <button onclick="closeModal()" class="absolute -top-12 right-0 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="relative bg-gradient-to-b from-white to-gray-50 rounded-3xl overflow-hidden shadow-2xl">
                <div class="h-2 bg-gradient-to-r from-primary via-saffron to-primary"></div>
                <div class="p-8 md:p-10">
                    <div class="relative mx-auto w-44 h-44 md:w-52 md:h-52 mb-8">
                        <div class="absolute -inset-3 rounded-full border-2 border-dashed border-primary/30 rotate-slow"></div>
                        <div class="absolute -inset-1 bg-gradient-to-br from-primary/20 via-transparent to-saffron/20 rounded-full"></div>
                        <div id="modal-image" class="relative w-full h-full rounded-full overflow-hidden shadow-xl ring-4 ring-white"></div>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <div class="h-px w-12 bg-gradient-to-r from-transparent to-primary/50"></div>
                            <div class="w-1.5 h-1.5 bg-saffron rotate-45"></div>
                            <div class="h-px w-12 bg-gradient-to-l from-transparent to-primary/50"></div>
                        </div>
                        <h3 id="modal-name" class="text-2xl md:text-3xl font-bold text-foreground mb-2"></h3>
                        <p id="modal-role" class="text-primary font-semibold text-lg mb-1"></p>
                        <p id="modal-section" class="text-sm text-muted-foreground mb-4"></p>
                        
                        <!-- Faculty Details Grid -->
                        <div id="modal-details" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-left mt-6 pt-6 border-t border-border/50">
                            <div id="modal-department-row" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg" style="display: none;">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Department</p>
                                    <p id="modal-department" class="text-sm font-medium text-foreground"></p>
                                </div>
                            </div>
                            
                            <div id="modal-years-row" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg" style="display: none;">
                                <div class="w-10 h-10 bg-saffron/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-saffron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Experience</p>
                                    <p id="modal-years" class="text-sm font-medium text-foreground"></p>
                                </div>
                            </div>
                            
                            <div id="modal-email-row" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg" style="display: none;">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Email</p>
                                    <p id="modal-email" class="text-sm font-medium text-foreground"></p>
                                </div>
                            </div>
                            
                            <div id="modal-qualification-row" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg" style="display: none;">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Qualification</p>
                                    <p id="modal-qualification" class="text-sm font-medium text-foreground"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Default contact message when no details available -->
                        <div id="modal-default-contact" class="flex items-center justify-center gap-4 mt-6 pt-6 border-t border-border/50">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Contact via school office</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-primary/50 via-saffron/50 to-primary/50"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Lucide Icons for navbar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    
    <script>
        // Staff filter
        function filterStaff(filter) {
            const cards = document.querySelectorAll('#staff-grid .staff-card');
            const pills = document.querySelectorAll('#staff-filters .filter-pill');
            
            pills.forEach(p => p.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'flex';
                } else {
                    const filterClass = 'dept-' + filter.replace('-', '');
                    card.style.display = card.classList.contains(filterClass) ? 'flex' : 'none';
                }
            });
        }

        // Student filter
        function filterStudents(filter) {
            const cards = document.querySelectorAll('#student-grid .student-card');
            const pills = document.querySelectorAll('#student-filters .filter-pill-saffron');
            
            pills.forEach(p => p.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else {
                    card.style.display = card.classList.contains('cat-' + filter) ? 'block' : 'none';
                }
            });
        }

        // Faculty Modal function with all details
        function openFacultyModal(data) {
            document.getElementById('modal-name').textContent = data.name || '';
            document.getElementById('modal-role').textContent = data.role || '';
            document.getElementById('modal-section').textContent = data.section || '';
            document.getElementById('modal-image').innerHTML = '<img src="' + (data.image || '') + '" alt="' + (data.name || '') + '" class="w-full h-full object-cover" onerror="this.src=\'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop\'">';
            
            // Handle department
            const deptRow = document.getElementById('modal-department-row');
            const deptText = document.getElementById('modal-department');
            if (data.department && data.department.trim()) {
                deptText.textContent = data.department;
                deptRow.style.display = 'flex';
            } else {
                deptRow.style.display = 'none';
            }
            
            // Handle years worked
            const yearsRow = document.getElementById('modal-years-row');
            const yearsText = document.getElementById('modal-years');
            if (data.years_worked && data.years_worked.toString().trim()) {
                yearsText.textContent = data.years_worked + ' years';
                yearsRow.style.display = 'flex';
            } else {
                yearsRow.style.display = 'none';
            }
            
            // Handle email
            const emailRow = document.getElementById('modal-email-row');
            const emailText = document.getElementById('modal-email');
            if (data.contact_email && data.contact_email.trim()) {
                emailText.textContent = data.contact_email;
                emailRow.style.display = 'flex';
            } else {
                emailRow.style.display = 'none';
            }
            
            // Handle qualification
            const qualRow = document.getElementById('modal-qualification-row');
            const qualText = document.getElementById('modal-qualification');
            if (data.qualification && data.qualification.trim()) {
                qualText.textContent = data.qualification;
                qualRow.style.display = 'flex';
            } else {
                qualRow.style.display = 'none';
            }
            
            // Show/hide default contact based on whether any details are available
            const hasDetails = (data.department && data.department.trim()) || 
                              (data.years_worked && data.years_worked.toString().trim()) || 
                              (data.contact_email && data.contact_email.trim()) ||
                              (data.qualification && data.qualification.trim());
            document.getElementById('modal-default-contact').style.display = hasDetails ? 'none' : 'flex';
            document.getElementById('modal-details').style.display = hasDetails ? 'grid' : 'none';
            
            document.getElementById('modal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Legacy modal function for backward compatibility (students, etc.)
        function openModal(name, role, image, section = '') {
            openFacultyModal({
                name: name,
                role: role,
                image: image,
                section: section,
                department: '',
                years_worked: '',
                contact_email: ''
            });
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>
