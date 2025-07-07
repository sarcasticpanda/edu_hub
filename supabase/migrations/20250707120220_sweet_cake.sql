-- Multi-School Educational Website Database Setup
-- Database: school_management_system

CREATE DATABASE IF NOT EXISTS school_management_system;
USE school_management_system;

-- School Configuration Table
CREATE TABLE IF NOT EXISTS school_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default school configuration
INSERT INTO school_config (config_key, config_value) VALUES
('school_name', 'St. Xavier\'s College'),
('school_tagline', 'Where Excellence Meets Opportunity'),
('school_address', 'St. Xavier\'s College, 5 Mahapalika Marg, Mumbai, Maharashtra 400001, India'),
('school_phone', '+91 22 2262 0662'),
('school_email', 'info@stxavierscollege.edu'),
('school_logo', ''),
('hero_background', ''),
('about_image', '');

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

-- Notices Table
CREATE TABLE IF NOT EXISTS notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    posted_by VARCHAR(100) DEFAULT 'Principal Office',
    attachment_path VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample notices
INSERT INTO notices (title, content, posted_by) VALUES
('Exam Dates', 'Mid-term exams: July 15-20, 2025. Final exams: August 10-15, 2025. All students are required to check the exam schedule on the student portal.', 'Examination Cell'),
('Upcoming Holidays', 'Independence Day: August 15, 2025. Dasara Break: October 1-5, 2025. The college will remain closed on the mentioned dates.', 'Principal Office'),
('New Admissions', 'Admissions for the 2025-26 academic year are open until July 31, 2025. Application forms are available online and at the college office.', 'Admissions Office'),
('Transport Notice', 'Bus routes have been updated. Check the transport section for new timings. The new bus schedule is effective from July 5, 2025.', 'Transport Department'),
('Power Shutdown', 'Scheduled power shutdown on July 10, 2025, from 10 AM to 1 PM. All departments are requested to save their work and shut down computers.', 'Maintenance Team'),
('Parent-Teacher Meeting', 'Parent-Teacher meeting scheduled for July 18, 2025, at 11 AM in the main hall. All parents are requested to attend.', 'Principal Office');

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

-- Who is Who Table
CREATE TABLE IF NOT EXISTS who_is_who (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(500),
    color_theme VARCHAR(50) DEFAULT 'blue',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample team members
INSERT INTO who_is_who (name, position, description, color_theme, display_order) VALUES
('Dr. John Doe', 'Principal', 'Leading with vision and dedication.', 'red', 1),
('Ms. Priya Sharma', 'Vice Principal', 'Academic excellence and discipline.', 'blue', 2),
('Mrs. Anita Rao', 'Headmistress', 'Nurturing young minds.', 'saffron', 3),
('Mr. Ravi Kumar', 'Coordinator', 'Connecting students and faculty.', 'green', 4),
('Ms. Sunita Verma', 'Counselor', 'Guiding and supporting students.', 'purple', 5),
('Mr. Ajay Singh', 'Sports Head', 'Promoting fitness and teamwork.', 'teal', 6);

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
('contact_email', 'info@school.edu'),
('contact_phone', '+91 12345 67890'),
('contact_address', 'Your City, Your State, India'),
('facebook_link', '#'),
('twitter_link', '#'),
('linkedin_link', '#'),
('instagram_link', '#'),
('copyright_text', '© 2025 School Name. All rights reserved.');

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
('main', 'About Our School', 'Our school is a premier educational institution committed to excellence in education, research, and community service. Founded with the vision of nurturing young minds and fostering holistic development, our school has been a beacon of learning for students from diverse backgrounds.

Our state-of-the-art facilities, experienced faculty, and comprehensive curriculum ensure that students receive the best possible education. We offer a wide range of programs designed to meet the evolving needs of the modern world.

At our school, we believe in the power of education to transform lives and communities. Our commitment to academic excellence, combined with our focus on character development and social responsibility, prepares our students to become leaders and change-makers in their chosen fields.

Join us on this journey of discovery, growth, and achievement. Experience the difference that quality education can make in your life.');

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
('100% Placement Rate in 2024', 'All our graduating students secured excellent job placements in top companies.', 'fas fa-trophy', TRUE),
('Awarded Best School 2023', 'Recognized as the best educational institution in the region for academic excellence.', 'fas fa-award', TRUE),
('Over 5000 Alumni Worldwide', 'Our graduates are making a difference in various fields across the globe.', 'fas fa-users', TRUE),
('State Level Sports Champions', 'Our sports teams have won multiple state-level championships.', 'fas fa-medal', FALSE),
('Research Excellence Award', 'Recognized for outstanding research contributions in education.', 'fas fa-star', FALSE);

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
('address', 'School Address, City, State, Country'),
('phone', '+91 12345 67890'),
('email', 'info@school.edu'),
('office_hours', 'Monday - Friday: 9:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nSunday: Closed'),
('map_embed', '');

-- Create indexes for better performance
CREATE INDEX idx_notices_active ON notices(is_active);
CREATE INDEX idx_gallery_category ON gallery_images(category);
CREATE INDEX idx_gallery_active ON gallery_images(is_active);
CREATE INDEX idx_who_display_order ON who_is_who(display_order);
CREATE INDEX idx_who_active ON who_is_who(is_active);
CREATE INDEX idx_achievements_featured ON achievements(is_featured);