# Edu Hub – School Website & Communication Platform

Edu Hub is a **PHP-based school management and communication platform** designed for **government-run schools and colleges in Telangana**.  
It enables **principals (admins)** to fully customize their school’s website and communicate with students, parents, and staff from a single dashboard.

This project was **actively used by multiple schools**, not just a prototype.

---

## 🚀 Key Objectives

- Give schools a **customizable digital presence**
- Enable **non-technical principals** to manage content
- Centralize **student communication**
- Support **multiple schools** using a single codebase

---

## 👥 User Roles

### 🔑 Admin (Principal / School Authority)
Admins have **full control** over their school’s data and website.

**Admin capabilities:**
- Login via admin panel
- Update homepage & school information
- Manage gallery (add/remove images)
- Send emails to selected students or groups
- Create and distribute online forms
- Post announcements and notices

---

### 👤 User (Student / Parent / Staff)
Users can access school-related information and submit data.

**User capabilities:**
- Sign up / Login
- View announcements & notices
- Access and submit forms
- Receive emails from school administration
- View gallery and updates

---

## ✨ Major Features

### 🔐 Authentication & Access Control
- Separate login flows for Admin and User
- Session-based authentication
- Role-based access restrictions

---

### 🏫 School-Specific Customization
- Each school has **independent content**
- Admins can modify:
  - Text content
  - Images
  - Announcements
  - Gallery
- Changes affect **only that school**

> Single codebase, multiple schools (data isolated using `school_id`).

---

### 🖼️ Gallery Management
- Admins can upload, update, or delete images
- Gallery images displayed dynamically on website
- Stored securely on server with database mapping

---

### 📧 Email Communication
- Admins can send emails to:
  - Individual students
  - Selected groups
  - Entire school
- Used for notices, forms, and event updates

---

### 📝 Online Forms
- Admins can create forms
- Users can fill and submit forms online
- Responses stored in database for review

---

### 📢 Announcements & Notices
- Admin-managed announcement board
- Used for:
  - Exams
  - Holidays
  - Circulars
  - Events
  - Registrations

---

## 🛠️ Tech Stack

| Layer       | Technology |
|------------|------------|
| Backend     | PHP |
| Database    | MySQL |
| Frontend    | HTML, CSS, JavaScript |
| Auth        | PHP Sessions |
| Email       | PHP Mail / SMTP |
| Hosting     | Shared Hosting / Local Server |

---

## 📁 Project str

