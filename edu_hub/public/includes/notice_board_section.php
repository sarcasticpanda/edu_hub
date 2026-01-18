<?php
// Fetch notices for homepage
try {
    $stmt = $pdo->query("SELECT * FROM notices WHERE is_active = 1 AND show_on_homepage = 1 ORDER BY is_pinned DESC, posted_on DESC LIMIT 4");
    $homepage_notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $homepage_notices = [];
}
?>

<!-- Notice Board Section -->
<section class="notice-board-section" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 60px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-header" style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.5rem; font-weight: 700; color: #2d3748; margin-bottom: 10px;">
                <i class="fas fa-bullhorn" style="color: #667eea; margin-right: 15px;"></i>
                Notice Board
            </h2>
            <p style="color: #718096; font-size: 1.1rem;">Stay updated with our latest announcements and circulars</p>
        </div>

        <div class="notices-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 30px;">
            <?php foreach ($homepage_notices as $notice): ?>
            <div class="notice-card" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden;" 
                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.15)';" 
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)';">
                
                <?php if ($notice['is_pinned']): ?>
                <div style="position: absolute; top: 15px; right: 15px; background: #f56565; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-thumbtack"></i>
                    <span>Pinned</span>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <div style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; 
                        <?php 
                        if ($notice['notice_type'] === 'circular') {
                            echo 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);';
                        } elseif ($notice['notice_type'] === 'announcement') {
                            echo 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);';
                        } else {
                            echo 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);';
                        }
                        ?>
                        color: white;">
                        <?php if ($notice['notice_type'] === 'circular'): ?>
                            <i class="fas fa-file-circle-check"></i>
                        <?php elseif ($notice['notice_type'] === 'announcement'): ?>
                            <i class="fas fa-megaphone"></i>
                        <?php else: ?>
                            <i class="fas fa-bell"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; 
                            <?php 
                            if ($notice['notice_type'] === 'circular') {
                                echo 'color: #667eea;';
                            } elseif ($notice['notice_type'] === 'announcement') {
                                echo 'color: #f5576c;';
                            } else {
                                echo 'color: #00f2fe;';
                            }
                            ?>">
                            <?= ucfirst($notice['notice_type']) ?>
                        </span>
                    </div>
                </div>

                <h3 style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 12px; line-height: 1.4;">
                    <?= htmlspecialchars($notice['title']) ?>
                </h3>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #718096; font-size: 0.85rem;">
                        <i class="fas fa-calendar-alt" style="color: #667eea;"></i>
                        <span><?= date('d M Y', strtotime($notice['posted_on'])) ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; color: #718096; font-size: 0.85rem;">
                        <i class="fas fa-user" style="color: #667eea;"></i>
                        <span><?= htmlspecialchars($notice['posted_by']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center;">
            <a href="/2026/edu_hub/edu_hub/public/notices.php" 
               style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.5)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(102, 126, 234, 0.4)';">
                <i class="fas fa-arrow-right" style="margin-right: 10px;"></i>
                View All Notices
            </a>
        </div>
    </div>
</section>
