# Troubleshooting Guide

## Check-in Not Working

### Step 1: Verify You Have Members

1. Go to **Manage Member** page
2. Check if you have any members in the list
3. Note down a member ID (e.g., MEM-00001)

**If no members:** Add a member first before testing check-in!

### Step 2: Test Check-in Manually

1. Open: `http://localhost/Gym_Management_System/test-checkin.php`
2. This test page will show:
   - All members in database
   - Manual check-in form
   - Today's attendance
3. Try checking in with a member ID

### Step 3: Check Browser Console

1. Open attendance page
2. Press **F12** to open Developer Tools
3. Click **Console** tab
4. Click "Enter ID" button
5. Enter a member ID
6. Click "Check In"
7. Look for any error messages in console

**Common errors:**
- `fetch is not defined` - Browser too old
- `404 Not Found` - Check api/check-in.php exists
- `500 Internal Server Error` - Check PHP error log

### Step 4: Check Network Tab

1. Open Developer Tools (F12)
2. Click **Network** tab
3. Try check-in again
4. Look for `check-in.php` request
5. Click on it to see:
   - Request payload (member_id sent?)
   - Response (what did server return?)

### Step 5: Verify API File Exists

Check that this file exists:
```
E:\Xamppy\htdocs\Gym_Management_System\api\check-in.php
```

### Step 6: Test API Directly

Open in browser:
```
http://localhost/Gym_Management_System/api/check-in.php
```

You should see:
```json
{"success":false,"message":"Invalid request"}
```

If you see a blank page or error, the API file has issues.

### Step 7: Check PHP Errors

1. Open XAMPP Control Panel
2. Click "Logs" button next to Apache
3. Look for recent errors
4. Common issues:
   - Syntax errors in PHP files
   - Database connection errors
   - Missing functions

### Step 8: Enable Error Display

Add to top of `attendance.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

Reload page and try again. Errors will show on screen.

---

## Quick Fixes

### Fix 1: Button Not Responding

**Problem:** Nothing happens when clicking "Check In"

**Solution:**
1. Make sure you clicked "Enter ID" button first
2. The manual entry form should be visible
3. Enter a valid member ID
4. Then click "Check In"

### Fix 2: Member ID Not Found

**Problem:** Alert says "Member ID not found"

**Solution:**
1. Go to Manage Member page
2. Check the exact member ID format
3. It should be: MEM-00001 (with leading zeros)
4. Copy-paste the ID to avoid typos

### Fix 3: Already Checked In

**Problem:** Alert says "Already checked in today"

**Solution:**
- Each member can only check in once per day
- This is working correctly!
- Try with a different member ID
- Or wait until tomorrow

### Fix 4: AJAX Not Working

**Problem:** Page reloads or nothing happens

**Solution:**
1. Check browser console for JavaScript errors
2. Make sure `api/check-in.php` file exists
3. Verify database connection in `includes/config.php`

---

## Still Not Working?

### Check These Files:

1. **attendance.php** - Main page
2. **api/check-in.php** - AJAX endpoint
3. **includes/config.php** - Database connection
4. **includes/functions.php** - Helper functions

### Database Check:

Run in phpMyAdmin:
```sql
-- Check if members exist
SELECT * FROM members;

-- Check if attendance table exists
DESCRIBE attendance;

-- Check today's attendance
SELECT * FROM attendance WHERE check_in_date = CURDATE();
```

### Manual Check-in (Bypass JavaScript):

Create a simple form:
```php
<form method="POST" action="api/check-in-simple.php">
  <input type="text" name="member_id" placeholder="Member ID"/>
  <button type="submit">Check In</button>
</form>
```

---

## Contact Support

If none of these work, provide:
1. Browser console errors (screenshot)
2. Network tab response (screenshot)
3. PHP error log
4. What happens when you click "Check In"
