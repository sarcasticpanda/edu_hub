# 🚀 Quick GitHub Upload Guide

## Step-by-Step Instructions

### 1. Initialize Git Repository (if not already done)
```bash
cd c:\xampp\htdocs\2026\edu_hub
git init
```

### 2. Verify .gitignore is Working
```bash
git status
```

**You should NOT see these files in the list:**
- ❌ `config.php`
- ❌ `admin/includes/db.php`
- ❌ `vendor/` folder
- ❌ `uploads/` folder

**You SHOULD see these files:**
- ✅ `.gitignore`
- ✅ `config.example.php`
- ✅ `admin/includes/db.example.php`
- ✅ All `.php` files in `edu_hub/`
- ✅ `README.md`, `SECURITY_SETUP.md`

### 3. Add Files to Git
```bash
git add .
```

### 4. Commit Your Code
```bash
git commit -m "Initial commit: School Management System"
```

### 5. Create GitHub Repository
1. Go to [GitHub.com](https://github.com)
2. Click "New Repository"
3. Name it: `edu-hub-school-management`
4. **Do NOT** initialize with README (you already have one)
5. Click "Create Repository"

### 6. Connect to GitHub
```bash
# Replace YOUR_USERNAME with your GitHub username
git remote add origin https://github.com/YOUR_USERNAME/edu-hub-school-management.git
```

### 7. Push to GitHub
```bash
# For first time
git branch -M main
git push -u origin main

# For subsequent updates
git push
```

---

## 🔍 Double-Check Security

### Before Pushing, Verify No Secrets:
```bash
# Search for your email in tracked files
git grep "your_email@gmail.com"

# Search for your app password
git grep "your_app_password"

# Search for Google Client ID
git grep "your_client_id"

# Search for Google Client Secret
git grep "your_client_secret"
```

**If any of these return results, STOP! Those files need to be fixed.**

---

## ✅ What's Protected

### Files Excluded (in .gitignore):
- `config.php` - Your OAuth & Email credentials
- `admin/includes/db.php` - Database credentials
- `vendor/` - Composer dependencies
- `uploads/` - User uploaded files
- `.env` - Environment variables
- Various cache and temp files

### Files Included (Template Examples):
- `config.example.php` - Template for OAuth/Email config
- `admin/includes/db.example.php` - Template for database config
- All source code files
- Documentation files

---

## 🔄 After Cloning (For New Users)

When someone clones your repository, they need to:

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Create config files:**
   ```bash
   cp config.example.php config.php
   cp admin/includes/db.example.php admin/includes/db.php
   ```

3. **Fill in their credentials** in:
   - `config.php`
   - `admin/includes/db.php`

4. **Create database:**
   - Import SQL schema (you should export and provide this separately)

---

## 📝 Updating Your Repository

### Making Changes:
```bash
# Check what changed
git status

# See the changes
git diff

# Add specific files
git add filename.php

# Or add all changes
git add .

# Commit with message
git commit -m "Description of changes"

# Push to GitHub
git push
```

---

## 🆘 Emergency: Accidentally Committed Secrets?

### If you committed sensitive data:

1. **Remove from Git history:**
   ```bash
   git rm --cached config.php
   git commit -m "Remove config.php from tracking"
   git push
   ```

2. **Change all credentials immediately:**
   - Generate new Google OAuth credentials
   - Change Gmail App Password
   - Update database password
   - Update all services using old credentials

3. **For complete history cleanup (advanced):**
   ```bash
   # Use BFG Repo-Cleaner or git filter-branch
   # This rewrites Git history - use with caution
   ```

---

## 📋 Pre-Push Checklist

- [ ] Verified `config.php` is NOT in `git status`
- [ ] Verified `admin/includes/db.php` is NOT in `git status`
- [ ] Verified `vendor/` is NOT in `git status`
- [ ] Template files (`config.example.php`, `db.example.php`) ARE included
- [ ] `.gitignore` file is committed
- [ ] `SECURITY_SETUP.md` is committed
- [ ] Tested that code works without hardcoded credentials
- [ ] Searched for any remaining hardcoded emails/passwords

---

## 🎯 Quick Reference

```bash
# Check status
git status

# Add all changes
git add .

# Commit
git commit -m "Your message"

# Push
git push

# Pull latest changes
git pull

# View commit history
git log

# Create new branch
git checkout -b feature-name

# Switch branches
git checkout main
```

---

## ✨ Best Practices

1. **Never commit secrets** - Use `.gitignore` and template files
2. **Commit often** - Small, logical commits with clear messages
3. **Write good commit messages** - Explain what and why
4. **Review before pushing** - Always check `git status` and `git diff`
5. **Keep main branch stable** - Use feature branches for experiments
6. **Update documentation** - Keep README and guides current

---

## 🔗 Useful Links

- [GitHub Docs](https://docs.github.com/)
- [Git Cheat Sheet](https://education.github.com/git-cheat-sheet-education.pdf)
- [Managing Sensitive Data](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/removing-sensitive-data-from-a-repository)

---

**Ready to push? Follow the steps above and you're all set!** 🚀
