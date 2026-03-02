# QR Code System - Quick Start

## 🚀 Get Started in 3 Steps

### Step 1: Generate QR Codes for Existing Members

If you already have members in your database:

1. Open your browser
2. Go to: `http://localhost/Gym_Management_System/generate-qr-batch.php`
3. Wait for the script to finish (shows progress)
4. Done! All members now have QR codes

**Note**: New members automatically get QR codes when you add them.

---

### Step 2: View Member QR Codes

To see a member's QR code:

1. Go to **Member Status** page
2. Click on any member in the table
3. Their profile appears on the right
4. QR code is displayed on the membership card

You can:
- Print the QR code
- Take a screenshot
- Display it on screen for scanning

---

### Step 3: Scan QR Codes for Check-In

To check in members using QR:

1. Go to **Attendance** page
2. Click **"Scan QR"** button (should be active by default)
3. Allow camera permissions when browser asks
4. Point camera at member's QR code
5. ✅ Automatic check-in!

**Alternative**: Click **"Enter ID"** button and type member ID manually (e.g., MEM-00001)

---

## 📱 Camera Permissions

### First Time Setup

When you click "Scan QR" for the first time:
1. Browser will ask: "Allow camera access?"
2. Click **"Allow"** or **"Yes"**
3. Camera activates immediately
4. You're ready to scan!

### If Camera Doesn't Work

- Use Chrome, Firefox, or Edge (recommended)
- Make sure you're on localhost or HTTPS
- Check browser settings → Site permissions → Camera
- Try refreshing the page
- Use "Enter ID" mode as backup

---

## 🧪 Test It Out

### Quick Test

1. Visit: `http://localhost/Gym_Management_System/test-qr.php`
2. This generates a test QR code
3. Verifies everything is working
4. Shows any errors if something is wrong

### Full Test

1. Add a test member (Manage Member page)
2. View their QR code (Member Status page)
3. Scan their QR code (Attendance page)
4. Check if they appear in today's attendance

---

## 💡 Tips

### For Best Results

- **Good lighting** helps scanning accuracy
- **Hold steady** for 1-2 seconds
- **Clean camera lens** if having issues
- **Print QR codes** on member cards for convenience
- **Keep backup** of member IDs for manual entry

### Common Mistakes

❌ Trying to scan without camera permissions  
✅ Allow camera access first

❌ Using QR scanner on HTTP (not localhost)  
✅ Use localhost or HTTPS

❌ Scanning blurry or damaged QR codes  
✅ Regenerate QR if needed

---

## 🆘 Need Help?

### Diagnostic Tools

**Step 1: Check PHP Configuration**
→ Run `check-php-config.php` first to diagnose issues

**Step 2: Test QR Generation**
→ Run `test-qr.php` to test if QR codes can be generated

### Quick Fixes

**QR generation failing?**
→ Run check-php-config.php to see what's wrong

**QR not showing?**
→ Run generate-qr-batch.php

**Camera not working?**
→ Use "Enter ID" mode instead

**Scan not working?**
→ Check browser console for errors

### Full Documentation

- **QR_USAGE_GUIDE.md** - Complete user guide
- **QR_IMPLEMENTATION_SUMMARY.md** - Technical details
- **TROUBLESHOOTING.md** - General troubleshooting

---

## ✅ You're Ready!

That's it! Your QR code system is ready to use.

**Next**: Start adding members and scanning QR codes for check-ins!

---

**Version**: 1.1.0  
**Last Updated**: March 2, 2026
