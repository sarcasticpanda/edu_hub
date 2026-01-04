<?php
/**
 * Configuration Template File
 * 
 * Instructions:
 * 1. Copy this file and rename it to 'config.php'
 * 2. Fill in your actual credentials
 * 3. NEVER commit config.php to GitHub
 */

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost/2026/edu_hub/edu_hub/student_google_callback.php');

// Email Configuration (Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password_here'); // Use App Password, not regular password
define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'School Registration');

// Application URL (change for production)
define('APP_URL', 'http://localhost/2026/edu_hub/edu_hub');
