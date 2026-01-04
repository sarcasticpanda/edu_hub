# Dashboard Integration Plan

## Issues Identified:
1. ❌ Google login redirects to separate page (student_google_login.php)
2. ❌ Dashboard missing navbar at top
3. ❌ Logout button in dashboard content instead of navbar only
4. ❌ Dashboard structure doesn't match website pages

## Implementation Plan:

### Step 1: Fix Modal Google Login Link
- Modal should link directly to Google OAuth URL (not student_google_login.php)
- Keep everything in the modal/navbar context

### Step 2: Include Navbar in Dashboard
- Add `<?php include 'check/user/navbar.php'; ?>` at top of dashboard
- Navbar already has dynamic logic for logged-in users (Application + Logout buttons)
- Remove duplicate Home/Logout buttons from dashboard content

### Step 3: Adjust Dashboard Layout
- Add proper top padding for fixed navbar (navbar is 56px high + fixed)
- Change background to white/light (match website, not gradient)
- Keep card-based design but integrate into website structure
- Remove welcome header's Home/Logout buttons (already in navbar)

### Step 4: Consistent Styling
- Dashboard should feel like part of the website, not a separate app
- Use website's container structure
- Maintain professional CSS but within website layout
- Keep gradient cards but on white background

### Step 5: Fix Modal Google Auth URL
- Update modal to use direct Google OAuth URL from student_google_callback.php
- Ensure proper redirect after Google login

## File Changes Required:
1. `check/user/navbar.php` - Fix modal Google login link
2. `student_dashboard.php` - Include navbar, remove duplicate buttons, adjust layout
3. Keep all existing functionality intact

## Expected Result:
- Click "Login as Student" in navbar → Modal opens
- Click "Sign in with Google" in modal → Google OAuth → Redirect to dashboard
- Dashboard shows with navbar at top (Application + Logout visible)
- Dashboard content follows website structure
- Seamless integration with website
