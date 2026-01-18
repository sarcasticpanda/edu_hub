# 🏫 Edu Hub – School Website & Management Platform

A **PHP-based school management and communication platform** designed for **government-run schools and colleges in Telangana**. It enables **principals (admins)** to fully customize their school's website and communicate with students, parents, and staff from a single dashboard.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0-06B6D4?style=flat&logo=tailwindcss&logoColor=white)

---

## 🚀 Key Features

### 🔐 Multi-Role Authentication
- **Admin Panel** - Secure login for principals/school authorities
- **Student Portal** - Google OAuth and email-based registration
- Session-based authentication with role-based access control

### 🏫 School Branding & Customization
- Dynamic school name, logo, and emblems
- Customizable color schemes and footer content
- Multi-language support (Telugu & English)

### 📢 Notice Board System
- Create, pin, and manage school announcements
- File attachments support (PDF, images, documents)
- Category-based filtering (Important, General, Events)

### 🖼️ Gallery Management
- Upload and organize school images
- Category-based galleries (Events, Infrastructure, Activities)
- Responsive image display

### 📅 Events Management
- Create and showcase school events
- Event images and descriptions
- Automatic date sorting

### 👨‍🏫 Leadership & Faculty Management
- Add faculty members with photos and designations
- Section-based organization (Management, Teaching Staff, etc.)
- Dynamic display on About page

### 📝 Student Applications
- Online application form system
- Application status tracking (Pending, Approved, Rejected)
- Admin review and management interface

---

## 📁 Project Structure

```
edu_hub/
├── admin/                      # Admin Panel
│   ├── index.php              # Admin Dashboard
│   ├── login.php              # Admin Login
│   ├── school_branding.php    # School Settings
│   ├── homepage.php           # Homepage Manager
│   ├── about.php              # About Page Manager
│   ├── gallery.php            # Gallery Manager
│   ├── notices.php            # Notice Manager
│   ├── contact.php            # Contact Page Manager
│   ├── student_applications.php # Applications Manager
│   ├── application_form_manager.php
│   └── includes/
│       ├── auth.php           # Authentication
│       ├── db.php             # Database Connection
│       └── admin_styles.php   # Unified Admin Styling
│
├── public/                     # Public Frontend
│   ├── index.php              # Homepage
│   ├── about.php              # About Page
│   ├── gallery.php            # Gallery Page
│   ├── notices.php            # Notices Page
│   ├── contact.php            # Contact Page
│   ├── events.php             # Events Page
│   ├── student_login_signup.php
│   ├── student_dashboard.php
│   ├── student_logout.php
│   ├── student_email_register.php
│   ├── student_google_login.php
│   ├── student_google_callback.php
│   ├── includes/
│   │   ├── header.php         # Page Header
│   │   ├── footer.php         # Page Footer
│   │   ├── navbar.php         # Navigation Bar
│   │   ├── header_navbar.php  # Combined Header/Navbar
│   │   ├── fetch_data.php     # Data Fetching Functions
│   │   └── notice_board_section.php
│   └── assets/
│       ├── css/
│       │   └── style.css      # Frontend Styles
│       └── js/
│           └── script.js      # Frontend Scripts
│
├── storage/                    # Uploaded Files
│   ├── images/                # General Images
│   ├── gallery/               # Gallery Images
│   └── notice_attachments/    # Notice Files
│
├── database/                   # Database Scripts
│   ├── create_admin.php       # Create Admin User
│   ├── cleanup_leadership.php
│   └── cleanup_sections.php
│
├── includes/                   # Shared Includes
│   └── navbar_snippet.php     # Reusable Navbar
│
├── uploads/                    # Additional Uploads
│   ├── certificates/
│   ├── documents/
│   ├── emblems/
│   └── profiles/
│
├── vendor/                     # Composer Dependencies
├── config.php                  # Database Configuration
├── config.example.php          # Configuration Template
├── composer.json               # PHP Dependencies
└── composer.lock
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 8.0+ |
| **Frontend** | HTML5, CSS3, JavaScript |
| **CSS Framework** | Bootstrap 5.3, TailwindCSS |
| **Icons** | Font Awesome 6.4 |
| **Auth** | PHP Sessions, Google OAuth 2.0 |
| **Email** | PHPMailer |

---

## ⚙️ Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server (XAMPP recommended for local development)
- Composer (for dependencies)

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/edu_hub.git
cd edu_hub
```

### Step 2: Install Dependencies
```bash
cd edu_hub
composer install
```

### Step 3: Configure Database
1. Create a MySQL database named `school_management_system`
2. Copy `config.example.php` to `config.php`
3. Update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management_system');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Step 4: Create Admin User
```bash
php database/create_admin.php
```
Default credentials:
- **Username**: `admin`
- **Password**: `admin123`

### Step 5: Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 uploads/
```

### Step 6: Access the Application
- **Public Site**: `http://localhost/edu_hub/edu_hub/public/`
- **Admin Panel**: `http://localhost/edu_hub/edu_hub/admin/`

---

## 👥 User Roles

### 🔑 Admin (Principal / School Authority)
- Full control over school's data and website
- Update homepage & school information
- Manage gallery, notices, and events
- Review and approve student applications
- Configure school branding and settings

### 👤 Student / Parent
- Sign up / Login via email or Google
- View announcements & notices
- Access and submit application forms
- View gallery and school updates

---

## 🔒 Security Features

- Password hashing with `password_hash()`
- Prepared statements for SQL queries (PDO)
- Session-based authentication
- Input sanitization and validation
- CSRF protection on forms

---

## 📱 Responsive Design

The platform is fully responsive and works on:
- 💻 Desktop computers
- 📱 Mobile phones
- 📟 Tablets

---

## 🎨 Admin Panel Features

| Module | Description |
|--------|-------------|
| **Dashboard** | Overview with quick stats and navigation |
| **School Branding** | Logo, name, colors, footer content |
| **Homepage Manager** | Hero section, events, officials |
| **About Page** | School info, leadership, achievements |
| **Gallery** | Image upload and management |
| **Notices** | Announcements with attachments |
| **Contact** | Contact page configuration |
| **Applications** | Student application management |

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**Made with ❤️ for Government Schools in Telangana**

