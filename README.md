# Power Fitness Gym Management System

A complete gym management system built with PHP and MySQL featuring member management, attendance tracking with QR scanning, payment processing, inventory management, and more.

## Features

- 🔐 **User Authentication** - Secure login/register system with sessions
- 👥 **Member Management** - Add, view, delete members with auto-generated IDs
- 📊 **Dashboard** - Real-time statistics and overview
- ✅ **Attendance Tracking** - QR code scanning + manual check-in (auto-generates QR codes)
- 💳 **Payment Processing** - Record transactions (Cash/GCash)
- 📦 **Inventory Management** - Track gym products and stock levels
- 📢 **Announcements** - Create and manage gym announcements
- 💬 **Feedback System** - Collect and view member feedback
- 🔍 **Member Status** - View detailed member profiles and membership cards
- 📈 **Business Metrics** - Real-time analytics with automatic tracking
- 🔧 **Database Triggers** - Automatic validation and data integrity (9 triggers)
- 🎯 **Stored Procedures** - Optimized database operations (9 procedures)

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Icons**: Lucide Icons
- **QR Scanning**: html5-qrcode library

## Documentation

### Getting Started
- **README.md** - This file (overview and features)
- **INSTALLATION.md** - Step-by-step setup guide
- **TROUBLESHOOTING.md** - Common issues and solutions
- **CHANGELOG.md** - Version history and updates

### Project Documentation
- **PROJECT_SUMMARY.md** - Complete project documentation
- **PROJECT_COMPLETE.md** - Final project status

### QR Code System
- **QUICK_START_QR.md** - Quick start guide for QR system
- **QR_USAGE_GUIDE.md** - Complete QR code user guide
- **QR_IMPLEMENTATION_SUMMARY.md** - Technical QR implementation details

### Database Triggers & Procedures
- **DATABASE_TRIGGERS_PROCEDURES_DOCUMENTATION.md** - Complete trigger/procedure documentation
- **TRIGGER_ERROR_HANDLING.md** - Error handling guide
- **QUICK_REFERENCE_TRIGGERS.md** - Quick reference for developers
- **TRIGGER_INTEGRATION_SUMMARY.md** - Integration summary
- **BUSINESS_METRICS_TABLE_GUIDE.md** - Business metrics guide
- **TRIGGERS_PROCEDURES_IMPACT_ANALYSIS.md** - Impact analysis

### Database Management
- **DATABASE_RESET_GUIDE.md** - How to reset database

## Installation

### Prerequisites
- XAMPP/WAMP/LAMP (Apache + PHP + MySQL)
- Web browser with camera access (for QR scanning)

### Setup Steps

1. **Clone/Download** the project to your web server directory
   ```
   E:\Xamppy\htdocs\Gym_Management_System\
   ```

2. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database or use the SQL file
   - Import `database.sql`

3. **Configure Database Connection**
   - Open `includes/config.php`
   - Update credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'gym_system');
     ```

4. **Access the System**
   - Open browser: `http://localhost/Gym_Management_System/`
   - Default login:
     - Email: `admin@powergym.com`
     - Password: `admin123`

5. **Generate QR Codes for Existing Members** (Optional)
   - If you imported the database with sample members
   - Visit: `http://localhost/Gym_Management_System/generate-qr-batch.php`
   - This will create QR codes for all existing members
   - New members will automatically get QR codes when added

## Project Structure

```
Gym_Management_System/
├── index.php                 # Redirects to login
├── login.php                 # Login/Register page
├── logout.php                # Logout handler
├── dashboard.php             # Main dashboard
├── manage-member.php         # Member management
├── member-status.php         # Member profiles
├── attendance.php            # Attendance tracking
├── payment.php               # Payment processing
├── inventory.php             # Inventory management
├── announcement.php          # Announcements
├── feedback.php              # Feedback system
├── database.sql              # Database schema
├── README.md                 # This file
│
├── includes/                 # Core PHP files
│   ├── config.php           # Database connection
│   ├── auth.php             # Authentication check
│   └── functions.php        # Helper functions
│
├── actions/                  # Form processing
│   ├── login-process.php
│   ├── register-process.php
│   ├── add-member.php
│   ├── delete-member.php
│   ├── add-payment.php
│   ├── add-inventory.php
│   ├── delete-inventory.php
│   ├── add-announcement.php
│   ├── delete-announcement.php
│   └── add-feedback.php
│
├── api/                      # AJAX endpoints
│   └── check-in.php         # Attendance check-in
│
└── assets/                   # Static files
    └── css/                  # Stylesheets
        ├── global.css
        ├── login.css
        ├── dashboard.css
        ├── manage-member.css
        ├── member-status.css
        ├── attendance.css
        ├── payment.css
        ├── inventory.css
        ├── announcement.css
        └── feedback.css
```

## Usage Guide

### Adding a Member
1. Go to **Manage Member**
2. Fill in member details (name, address, phone, plan, etc.)
3. Click "Add Member"
4. Member ID is auto-generated (e.g., MEM-00001)

### Recording Attendance
1. Go to **Attendance**
2. **Option A**: Click "Scan QR" and use webcam to scan member's QR code
3. **Option B**: Click "Enter ID" and manually type member ID
4. Member is checked in and appears in today's list

### Processing Payments
1. Go to **Payment**
2. Select member from dropdown
3. Choose category (Membership/Supplements/Merchandise)
4. Enter amount and quantity
5. Select payment method (Cash or GCash - display only)
6. Click "Make Payment"

### Managing Inventory
1. Go to **Inventory**
2. Add items with name, category, quantity, and price
3. View all items in the table
4. Delete items as needed

### Creating Announcements
1. Go to **Announcements**
2. Enter title, message, date range, and priority
3. Click "Post Announcement"
4. Announcements appear on dashboard

### Viewing Member Status
1. Go to **Member Status**
2. Click on any member to view their profile
3. See membership card with QR code
4. Filter by status or search by name

## Database Schema

### Tables
- `users` - Admin accounts
- `members` - Gym members
- `attendance` - Check-in records
- `payments` - Transaction history
- `inventory` - Products and stock
- `announcements` - Gym announcements
- `feedback` - Member feedback

## Security Features

- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- Session-based authentication
- Input sanitization
- XSS protection

## Browser Compatibility

- Chrome (recommended for QR scanning)
- Firefox
- Edge
- Safari

**Note**: QR scanning requires HTTPS or localhost and camera permissions.

## Future Enhancements

- QR code generation for member cards
- Email notifications
- Reports and analytics
- Member portal
- Mobile app
- Attendance reports export

## Troubleshooting

### Database Connection Error
- Check `includes/config.php` credentials
- Ensure MySQL service is running
- Verify database name exists

### QR Scanner Not Working
- Allow camera permissions in browser
- Use HTTPS or localhost
- Try manual ID entry as alternative

### CSS Not Loading
- Check file paths in PHP files
- Ensure `assets/css/` folder exists
- Clear browser cache

## Credits

- **Design**: Power Fitness Gym UI
- **Icons**: Lucide Icons
- **QR Library**: html5-qrcode
- **Fonts**: Google Fonts (Bebas Neue, DM Sans)

## License

This project is for educational purposes.

## Support

For issues or questions, please check the code comments or contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: March 2026
