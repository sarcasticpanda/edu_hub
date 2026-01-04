# 🔐 Security & Setup Guide

## ⚠️ Before Uploading to GitHub

This project contains sensitive credentials that **MUST NOT** be committed to GitHub. Follow these steps:

### 1. Files Already Protected (in .gitignore)

The following files are automatically excluded from Git:
- `config.php` - Contains your Google OAuth and Email credentials
- `admin/includes/db.php` - Contains database credentials
- `vendor/` - Third-party dependencies (can be reinstalled)
- `uploads/` - User uploaded files
- `composer.lock` - Lock file for dependencies

### 2. Configuration Files

#### For Local Development:
- Use `config.php` (already created with your credentials)
- Use `admin/includes/db.php` (contains your DB credentials)

#### For New Users/Deployment:
- Copy `config.example.php` → `config.php`
- Copy `admin/includes/db.example.php` → `admin/includes/db.php`
- Fill in actual credentials

---

## 📋 Database Information

**Database Name:** `school_management_system`

### Required Tables:
- `students` - Student login information
- `student_profiles` - Student profile details
- `student_applications` - Application submissions
- `application_custom_data` - Custom form field data
- `admin_student_messages` - Admin to student messages
- `student_to_admin_messages` - Student to admin messages
- `school_config` - School configuration settings
- `leadership` - Leadership team information
- `homepage_content` - Homepage dynamic content
- `gallery` - Gallery images
- `notices` - Notice board items

---

## 🚀 Installation Instructions

### Prerequisites:
- **PHP 7.4+** (or PHP 8.x)
- **MySQL 5.7+** or **MariaDB**
- **Composer** (for dependencies)
- **XAMPP/WAMP/LAMP** (recommended for local development)

### Step 1: Clone the Repository
```bash
git clone <your-repo-url>
cd edu_hub
```

### Step 2: Install Dependencies
```bash
cd edu_hub
composer install
```

### Step 3: Configure Database
1. Create a MySQL database named `school_management_system`
2. Import the database schema (SQL file should be provided separately)
3. Copy `admin/includes/db.example.php` to `admin/includes/db.php`
4. Update database credentials in `db.php`:
   ```php
   $host = 'localhost';
   $db   = 'school_management_system';
   $user = 'your_db_user';
   $pass = 'your_db_password';
   ```

### Step 4: Configure Google OAuth
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google+ API**
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://your-domain/edu_hub/student_google_callback.php`
6. Copy `config.example.php` to `config.php`
7. Update with your credentials:
   ```php
   define('GOOGLE_CLIENT_ID', 'your_client_id');
   define('GOOGLE_CLIENT_SECRET', 'your_client_secret');
   define('GOOGLE_REDIRECT_URI', 'http://your-domain/edu_hub/student_google_callback.php');
   ```

### Step 5: Configure Email (Gmail SMTP)
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an **App Password** (not your regular password)
   - Go to: Google Account → Security → 2-Step Verification → App passwords
3. Update `config.php`:
   ```php
   define('SMTP_USERNAME', 'your_email@gmail.com');
   define('SMTP_PASSWORD', 'your_app_password'); // 16-character app password
   define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
   ```

### Step 6: Set Permissions
```bash
chmod -R 755 uploads/
chmod -R 755 check/
```

### Step 7: Access the Application
- **Student Portal:** `http://localhost/2026/edu_hub/edu_hub/check/user/index.php`
- **Admin Panel:** `http://localhost/2026/edu_hub/edu_hub/admin/login.php`

---

## 🔒 Security Checklist Before GitHub Upload

- ✅ `.gitignore` file created and configured
- ✅ `config.php` excluded from Git (contains OAuth & Email credentials)
- ✅ `admin/includes/db.php` excluded from Git (contains DB credentials)
- ✅ Template files created (`config.example.php`, `db.example.php`)
- ✅ `vendor/` directory excluded (dependencies)
- ✅ `uploads/` directory excluded (user data)
- ✅ No hardcoded credentials in tracked files

### Verify Before Push:
```bash
# Check what will be committed
git status

# Make sure these are NOT in the list:
# - config.php
# - admin/includes/db.php
# - vendor/
# - uploads/
```

---

## 🌐 Production Deployment

### Environment-Specific Changes:

1. **Update config.php:**
   ```php
   define('APP_URL', 'https://your-domain.com/edu_hub');
   define('GOOGLE_REDIRECT_URI', 'https://your-domain.com/edu_hub/student_google_callback.php');
   ```

2. **Update Google Cloud Console:**
   - Add production domain to authorized redirect URIs

3. **Database Security:**
   - Use strong database password
   - Create dedicated database user with limited privileges
   - Never use 'root' in production

4. **File Permissions:**
   - Set appropriate permissions (755 for directories, 644 for files)
   - Ensure `uploads/` is writable but not executable

5. **SSL Certificate:**
   - Enable HTTPS (required for Google OAuth)
   - Update all URLs to use `https://`

---

## 📝 Environment Variables (Optional Advanced Setup)

For enhanced security, consider using environment variables instead of `config.php`:

1. Create `.env` file (also add to `.gitignore`)
2. Use libraries like `vlucas/phpdotenv`
3. Load environment variables in your PHP files

---

## 🆘 Troubleshooting

### "Headers already sent" error:
- Ensure no output before `session_start()`
- Check for BOM in PHP files

### Email not sending:
- Verify App Password (not regular Gmail password)
- Check SMTP settings and firewall
- Enable "Less secure app access" (not recommended, use App Password instead)

### Google OAuth errors:
- Verify redirect URI matches exactly in Google Console
- Ensure domain is authorized
- Check client ID and secret

### Database connection failed:
- Verify MySQL service is running
- Check database credentials
- Ensure database exists

---

## 📞 Support

For issues or questions:
- Check the main [README.md](README.md) for project overview
- Review code comments in individual files
- Ensure all prerequisites are met

---

## ⚖️ License

This project is for educational purposes. Ensure compliance with local regulations when deploying for actual school use.
