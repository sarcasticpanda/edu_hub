-- Complete School CMS Database Setup
-- Database: school_cms_system

CREATE DATABASE IF NOT EXISTS school_cms_system;
USE school_cms_system;

-- School Configuration Table
CREATE TABLE IF NOT EXISTS school_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default school configuration
INSERT INTO school_config (config_key, config_value) VALUES
('school_name', 'City Montessori School'),
('school_tagline', 'Empowering Excellence, Fostering Growth'),
('school_address', 'City Montessori School, Station Road, Lucknow, Uttar Pradesh 226001, India'),
('school_phone', '+91 522 2638000'),
('school_email', 'info@cmseducation.org'),
('school_logo', 'school.png'),
('hero_background', ''),
('about_image', ''),
('office_hours', 'Monday - Friday: 8:00 AM - 4:00 PM\nSaturday: 8:00 AM - 12:00 PM\nSunday: Closed'),
('google_maps', '');

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admins (email, password_hash, name) VALUES
('admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Homepage Content Table
CREATE TABLE IF NOT EXISTS homepage_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(50) NOT NULL,
    title VARCHAR(255),
    content TEXT,
    image_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default homepage content
INSERT INTO homepage_content (section, title, content) VALUES
('hero', 'WELCOME TO CITY MONTESSORI SCHOOL', 'Where Excellence Meets Opportunity'),
('about', 'About City Montessori School', 'Empowering Excellence, Fostering Growth. City Montessori School provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.'),
('school_info', 'City Montessori School', 'Empowering Excellence, Fostering Growth');

-- Notices Table with proper structure
CREATE TABLE IF NOT EXISTS notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subheading VARCHAR(255),
    content TEXT NOT NULL,
    posted_by VARCHAR(100) DEFAULT 'Principal Office',
    attachment_path VARCHAR(500),
    attachment_type VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample notices
INSERT INTO notices (title, subheading, content, posted_by) VALUES
('Annual Examination Schedule', 'Academic Session 2024-25', 'The annual examinations for all classes will commence from March 15, 2025. Students are advised to prepare well and follow the examination guidelines.', 'Examination Department'),
('Sports Day Celebration', 'Inter-House Competition', 'Annual Sports Day will be held on February 20, 2025. All students must participate in their respective house events. Parents are cordially invited.', 'Sports Department'),
('Admission Open for Session 2025-26', 'New Admissions', 'Admissions are now open for the academic session 2025-26. Application forms are available online and at the school office.', 'Admission Office'),
('Parent-Teacher Meeting', 'Academic Progress Discussion', 'Parent-Teacher meeting is scheduled for January 25, 2025. Parents are requested to attend and discuss their ward\'s academic progress.', 'Academic Department');

-- Gallery Images Table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Who is Who Table (Leadership)
CREATE TABLE IF NOT EXISTS leadership (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(500),
    color_theme VARCHAR(50) DEFAULT 'blue',
    card_type ENUM('full', 'support') DEFAULT 'full',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample leadership data
INSERT INTO leadership (name, position, description, color_theme, card_type, display_order) VALUES
('Dr. John Doe', 'Principal', 'Leading with vision and dedication to educational excellence.', 'red', 'full', 1),
('Ms. Priya Sharma', 'Vice Principal', 'Academic excellence and student development.', 'blue', 'full', 2),
('Mrs. Anita Rao', 'Academic Coordinator', 'Coordinating academic activities and curriculum.', 'green', 'full', 3),
('Mr. Ravi Kumar', 'Sports Coordinator', 'Promoting physical fitness and sportsmanship.', 'purple', 'support', 4),
('Ms. Sunita Verma', 'Counselor', 'Student guidance and psychological support.', 'teal', 'support', 5);

-- Footer Content Table
CREATE TABLE IF NOT EXISTS footer_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(100) NOT NULL,
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_section (section)
);

-- Insert default footer content
INSERT INTO footer_content (section, content) VALUES
('contact_email', 'info@cmseducation.org'),
('contact_phone', '+91 522 2638000'),
('contact_address', 'City Montessori School, Station Road, Lucknow, Uttar Pradesh 226001, India'),
('facebook_link', 'https://facebook.com/cmseducation'),
('twitter_link', 'https://twitter.com/cmseducation'),
('linkedin_link', 'https://linkedin.com/company/cmseducation'),
('copyright_text', '© 2025 City Montessori School. All rights reserved.');

-- About Content Table
CREATE TABLE IF NOT EXISTS about_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    content TEXT,
    image_path VARCHAR(500),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_section_title (section, title)
);

-- Insert default about content
INSERT INTO about_content (section, title, content) VALUES
('main', 'About City Montessori School', 'Empowering Excellence, Fostering Growth. City Montessori School provides your academic journey with the environment, resources, and inspiration needed to achieve your highest potential.'),
('motto', 'Our Motto', 'To provide quality education that empowers students to become responsible global citizens and lifelong learners.'),
('objective', 'Our Objective', 'To create an environment where students can develop their full potential academically, socially, and emotionally while maintaining the highest standards of integrity and excellence.'),
('values', 'Our Values', 'Excellence, Integrity, Compassion, Innovation, and Service to humanity are the core values that guide our educational philosophy and daily practices.');

-- Achievements Table
CREATE TABLE IF NOT EXISTS achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'fas fa-trophy',
    achievement_date DATE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample achievements
INSERT INTO achievements (title, description, icon, is_featured) VALUES
('100% Board Results 2024', 'All our students achieved excellent results in board examinations with 100% pass rate.', 'fas fa-trophy', TRUE),
('Best School Award 2023', 'Recognized as the best educational institution in the region for academic excellence.', 'fas fa-award', TRUE),
('Over 10,000 Alumni Worldwide', 'Our graduates are making a difference in various fields across the globe.', 'fas fa-users', TRUE),
('State Level Sports Champions', 'Our sports teams have won multiple state-level championships in various disciplines.', 'fas fa-medal', FALSE),
('Excellence in Science Fair', 'Students won first prize in the state-level science exhibition.', 'fas fa-star', FALSE);

-- Contact Information Table
CREATE TABLE IF NOT EXISTS contact_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    field VARCHAR(100) NOT NULL,
    value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_field (field)
);

-- Insert default contact information
INSERT INTO contact_info (field, value) VALUES
('address', 'City Montessori School, Station Road, Lucknow, Uttar Pradesh 226001, India'),
('phone', '+91 522 2638000'),
('email', 'info@cmseducation.org'),
('office_hours', 'Monday - Friday: 8:00 AM - 4:00 PM\nSaturday: 8:00 AM - 12:00 PM\nSunday: Closed'),
('map_embed', '');

-- Create indexes for better performance
CREATE INDEX idx_notices_active ON notices(is_active);
CREATE INDEX idx_gallery_category ON gallery_images(category);
CREATE INDEX idx_gallery_active ON gallery_images(is_active);
CREATE INDEX idx_leadership_display_order ON leadership(display_order);
CREATE INDEX idx_leadership_active ON leadership(is_active);
CREATE INDEX idx_achievements_featured ON achievements(is_featured);