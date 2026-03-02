# QR Code System - Usage Guide

## Overview

The gym management system now includes automatic QR code generation for member check-ins. Each member gets a unique QR code that can be scanned at the attendance page.

---

## How It Works

### 1. Automatic QR Generation (New Members)

When you add a new member through the "Manage Member" page:
- System automatically generates a unique QR code
- QR code contains the member's ID (e.g., MEM-00001)
- QR code is saved to `assets/qrcodes/MEM-00001.png`
- No manual action needed!

### 2. Viewing QR Codes

To see a member's QR code:
1. Go to **Member Status** page
2. Click on any member in the table
3. View their profile on the right panel
4. QR code appears on the membership card

### 3. Scanning QR Codes

To check in members using QR:
1. Go to **Attendance** page
2. Click "Scan QR" button (default mode)
3. Allow camera permissions when prompted
4. Point camera at member's QR code
5. System automatically checks them in!

### 4. Manual Check-In (Alternative)

If QR scanning isn't available:
1. Go to **Attendance** page
2. Click "Enter ID" button
3. Type member ID (e.g., MEM-00001)
4. Click "Check In"

---

## For Existing Members

If you already have members in the database before QR implementation:

### Option 1: Batch Generate All QR Codes

1. Open browser: `http://localhost/Gym_Management_System/generate-qr-batch.php`
2. Script will automatically generate QR codes for all members
3. Wait for completion (shows progress)
4. Done! All members now have QR codes

### Option 2: Individual Generation

QR codes will be generated automatically when:
- You edit a member (future feature)
- You manually trigger generation

---

## Technical Details

### QR Code Specifications
- **Format**: PNG image
- **Size**: 200x200 pixels
- **Content**: Member ID code (e.g., MEM-00001)
- **Location**: `assets/qrcodes/`
- **Naming**: `{MEMBER_ID}.png`

### API Used
- Google Charts API for QR generation
- No external libraries needed
- Works without internet (after initial generation)

### Camera Requirements
- Modern browser (Chrome, Firefox, Edge, Safari)
- HTTPS or localhost (for camera access)
- Camera permissions granted
- Good lighting for scanning

---

## Troubleshooting

### QR Code Not Showing
**Problem**: Member card shows "QR not generated"  
**Solution**: 
1. Run `generate-qr-batch.php` to generate missing QR codes
2. Check if `assets/qrcodes/` folder exists and is writable

### Camera Not Working
**Problem**: QR scanner shows camera error  
**Solution**:
1. Check browser permissions (allow camera access)
2. Use HTTPS or localhost (required for camera)
3. Try different browser
4. Use "Enter ID" mode as alternative

### QR Scan Not Working
**Problem**: QR code scans but doesn't check in  
**Solution**:
1. Ensure QR code contains correct member ID
2. Check if member exists in database
3. Verify member hasn't already checked in today
4. Check browser console for errors

### Batch Generator Fails
**Problem**: Some QR codes fail to generate  
**Solution**:
1. Check internet connection (needs Google Charts API)
2. Verify `assets/qrcodes/` folder permissions
3. Try generating individually
4. Check PHP error logs

---

## Best Practices

### For Gym Staff
1. **Print QR codes** on member cards for easy scanning
2. **Keep backup** of member IDs for manual entry
3. **Test camera** before busy hours
4. **Good lighting** helps scanning accuracy
5. **Clean camera lens** regularly

### For System Admins
1. **Backup QR codes** regularly (assets/qrcodes/ folder)
2. **Run batch generator** after importing members
3. **Check folder permissions** (assets/qrcodes/ must be writable)
4. **Monitor storage** (each QR is ~1-2KB)

---

## File Locations

```
Gym_Management_System/
├── includes/
│   └── qr-generator.php          # QR generation functions
├── actions/
│   └── add-member.php            # Auto-generates QR on member add
├── assets/
│   └── qrcodes/                  # QR code storage
│       ├── MEM-00001.png
│       ├── MEM-00002.png
│       └── ...
├── generate-qr-batch.php         # Batch generator utility
├── member-status.php             # Displays QR codes
└── attendance.php                # QR scanner
```

---

## Functions Available

### In `includes/qr-generator.php`:

```php
// Generate QR code for member
generate_member_qr($member_id, $save_path)

// Get QR code file path
get_qr_path($member_id)

// Check if QR exists
qr_exists($member_id)
```

---

## Future Enhancements

Potential improvements:
- [ ] Print QR codes directly from system
- [ ] Regenerate QR if member ID changes
- [ ] QR code customization (colors, logo)
- [ ] Download QR codes as PDF
- [ ] Email QR codes to members
- [ ] QR code expiry dates
- [ ] Multiple QR formats (SVG, JPG)

---

## Support

If you encounter issues:
1. Check this guide first
2. Review TROUBLESHOOTING.md
3. Check browser console for errors
4. Verify file permissions
5. Test with manual ID entry

---

**Last Updated**: March 2, 2026  
**Version**: 1.1.0
