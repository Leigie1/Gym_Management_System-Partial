# Installation Guide

## Quick Start (5 minutes)

### Step 1: Setup Web Server
1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services

### Step 2: Place Files
1. Copy the `Gym_Management_System` folder to:
   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

### Step 3: Create Database
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" to create database
3. Name it: `gym_system`
4. Click "Import" tab
5. Choose file: `database.sql`
6. Click "Go"

### Step 4: Configure (Optional)
Only if your MySQL password is NOT empty:
1. Open `includes/config.php`
2. Change line:
   ```php
   define('DB_PASS', 'your_mysql_password');
   ```

### Step 5: Access System
1. Open browser: `http://localhost/Gym_Management_System/`
2. Login with:
   - Email: `admin@powergym.com`
   - Password: `admin123`

### Step 6: Generate QR Codes (Optional)
If you imported sample members, generate their QR codes:
1. Visit: `http://localhost/Gym_Management_System/generate-qr-batch.php`
2. Wait for completion
3. All members now have scannable QR codes!

Note: New members automatically get QR codes when added.

## Done! 🎉

You should now see the dashboard.

---

## Detailed Setup

### Database Configuration

The system uses these default settings:
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (empty)
DB_NAME: gym_system
```

### Default Admin Account

After importing `database.sql`, you'll have:
- Email: admin@powergym.com
- Password: admin123
- Role: Admin

**⚠️ Change this password after first login!**

### File Permissions

Ensure these folders are writable:
- `assets/qrcodes/` (for QR code generation)

### Testing the System

1. **Login Test**
   - Go to login page
   - Use default credentials
   - Should redirect to dashboard

2. **Add Member Test**
   - Go to Manage Member
   - Fill form and submit
   - Check if member appears in list

3. **Attendance Test**
   - Go to Attendance
   - Click "Enter ID"
   - Type: MEM-00001
   - Click Check In

4. **QR Scanner Test** (requires camera)
   - Go to Attendance
   - Click "Scan QR"
   - Allow camera permission
   - Scanner should activate

5. **QR Generation Test**
   - Visit: `http://localhost/Gym_Management_System/test-qr.php`
   - Should generate a test QR code
   - Verifies QR system is working

### Common Issues

**Issue**: "Connection failed" error
- **Fix**: Check MySQL is running in XAMPP Control Panel

**Issue**: "Access denied for user 'root'"
- **Fix**: Update password in `includes/config.php`

**Issue**: CSS not loading
- **Fix**: Check folder structure, ensure `assets/css/` exists

**Issue**: QR scanner not working
- **Fix**: Use Chrome browser, allow camera permissions, ensure HTTPS or localhost

**Issue**: QR codes not generating
- **Fix**: Check internet connection (uses Google Charts API), verify `assets/qrcodes/` folder is writable

**Issue**: QR codes not displaying
- **Fix**: Run `generate-qr-batch.php` to create missing QR codes

### Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache 2.4 or higher
- Modern web browser

### Optional: Production Deployment

For live server deployment:

1. Update `includes/config.php` with production database credentials
2. Change default admin password
3. Enable HTTPS for QR scanning
4. Set proper file permissions (755 for folders, 644 for files)
5. Disable error display in PHP
6. Regular database backups

---

Need help? Check README.md for full documentation.
