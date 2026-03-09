# Install Triggers & Procedures - Checklist

## Quick Installation Guide

Follow these steps to install all database triggers and stored procedures.

### ✅ Step 1: Backup Your Database
Before installing triggers, backup your database:

```sql
-- In phpMyAdmin, click on your database
-- Click "Export" tab
-- Click "Go" to download backup
```

### ✅ Step 2: Open phpMyAdmin
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. Login with your MySQL credentials
4. Select database: `gym_system`

### ✅ Step 3: Import Triggers File
1. Click on "SQL" tab at the top
2. Click "Choose File" or drag and drop
3. Select file: `database-triggers-procedures.sql`
4. Click "Go" button at bottom right
5. Wait for success message

**Expected Result**: 
```
✅ 9 stored procedures created
✅ 9 triggers created
✅ business_metrics table created (if not exists)
```

### ✅ Step 4: Verify Installation

#### Check Triggers
```sql
SHOW TRIGGERS FROM gym_system;
```

**Expected**: Should show 9 triggers:
- trg_before_insert_member
- trg_after_insert_member
- trg_before_update_member
- trg_after_insert_payment
- trg_before_insert_attendance
- trg_after_insert_attendance
- trg_before_insert_payment
- trg_before_update_inventory
- trg_before_insert_announcement

#### Check Procedures
```sql
SHOW PROCEDURE STATUS WHERE Db = 'gym_system';
```

**Expected**: Should show 9 procedures:
- sp_add_member
- sp_checkin_member
- sp_get_member_stats
- sp_update_member_statuses
- sp_revenue_report
- sp_attendance_report
- sp_get_expiring_memberships
- sp_renew_membership
- sp_get_top_members

#### Check Business Metrics Table
```sql
SHOW TABLES LIKE 'business_metrics';
```

**Expected**: Should show `business_metrics` table

### ✅ Step 5: Test Triggers
1. Open browser
2. Go to: `http://localhost/Gym_Management_System/test-triggers.php`
3. Check all tests show ✅ PASSED

**Expected Results**:
- ✅ Test 1: Phone Number Validation - PASSED
- ✅ Test 2: Age Validation - PASSED
- ✅ Test 3: Amount Validation - PASSED
- ✅ Test 4: Duplicate Check-in Prevention - PASSED
- ✅ Test 5: Payment Amount Validation - PASSED
- ✅ Test 6: Announcement Date Validation - PASSED
- ✅ Test 7: Payment with Non-existent Member - PASSED
- ✅ Test 8: Inventory Quantity Validation - PASSED

### ✅ Step 6: Test in Application

#### Test Member Validation
1. Go to "Manage Members" page
2. Click "Add New Member"
3. Enter phone: "12345" (invalid)
4. Submit form
5. **Expected**: Error message "Phone number must be 10-11 digits"

#### Test Duplicate Check-in
1. Go to "Attendance" page
2. Scan a member's QR code (or enter ID manually)
3. Try to scan/enter same member again
4. **Expected**: Error message "Member already checked in today"

#### Test Payment Validation
1. Go to "Payment" page
2. Try to add payment with amount = 0
3. **Expected**: Error message "Payment amount must be greater than zero"

### ✅ Step 7: Verify Metrics Tracking
```sql
-- Check if metrics are being tracked
SELECT * FROM business_metrics WHERE metric_date = CURDATE();
```

**Expected**: Should show today's metrics with values

### ✅ Step 8: Done!
All triggers and procedures are now installed and working!

## Troubleshooting

### Problem: "Trigger already exists" error
**Solution**: The file includes DROP statements, but if you still get this error:
```sql
-- Drop all triggers manually
DROP TRIGGER IF EXISTS trg_before_insert_member;
DROP TRIGGER IF EXISTS trg_after_insert_member;
-- ... (repeat for all 9 triggers)

-- Then re-import the file
```

### Problem: "Procedure already exists" error
**Solution**: The file includes DROP statements, but if you still get this error:
```sql
-- Drop all procedures manually
DROP PROCEDURE IF EXISTS sp_add_member;
DROP PROCEDURE IF EXISTS sp_checkin_member;
-- ... (repeat for all 9 procedures)

-- Then re-import the file
```

### Problem: Test script shows errors
**Solution**: 
1. Check if triggers are installed: `SHOW TRIGGERS FROM gym_system;`
2. Check MySQL error log for details
3. Verify database name is `gym_system`
4. Ensure you have proper MySQL permissions

### Problem: Error messages not showing in application
**Solution**:
1. Verify triggers are installed
2. Check that action files have been updated (they should have `mysqli_error($conn)`)
3. Clear browser cache
4. Check PHP error log

## Quick Commands Reference

### View All Triggers
```sql
SHOW TRIGGERS FROM gym_system;
```

### View Specific Trigger
```sql
SHOW CREATE TRIGGER trg_before_insert_member;
```

### View All Procedures
```sql
SHOW PROCEDURE STATUS WHERE Db = 'gym_system';
```

### View Specific Procedure
```sql
SHOW CREATE PROCEDURE sp_add_member;
```

### Drop All Triggers (if needed)
```sql
DROP TRIGGER IF EXISTS trg_before_insert_member;
DROP TRIGGER IF EXISTS trg_after_insert_member;
DROP TRIGGER IF EXISTS trg_before_update_member;
DROP TRIGGER IF EXISTS trg_after_insert_payment;
DROP TRIGGER IF EXISTS trg_before_insert_attendance;
DROP TRIGGER IF EXISTS trg_after_insert_attendance;
DROP TRIGGER IF EXISTS trg_before_insert_payment;
DROP TRIGGER IF EXISTS trg_before_update_inventory;
DROP TRIGGER IF EXISTS trg_before_insert_announcement;
```

### Drop All Procedures (if needed)
```sql
DROP PROCEDURE IF EXISTS sp_add_member;
DROP PROCEDURE IF EXISTS sp_checkin_member;
DROP PROCEDURE IF EXISTS sp_get_member_stats;
DROP PROCEDURE IF EXISTS sp_update_member_statuses;
DROP PROCEDURE IF EXISTS sp_revenue_report;
DROP PROCEDURE IF EXISTS sp_attendance_report;
DROP PROCEDURE IF EXISTS sp_get_expiring_memberships;
DROP PROCEDURE IF EXISTS sp_renew_membership;
DROP PROCEDURE IF EXISTS sp_get_top_members;
```

## What Happens After Installation?

### Automatic Validations
- Phone numbers must be 10-11 digits
- Members must be at least 10 years old
- Amounts must be greater than zero
- No duplicate check-ins on same day
- Dates must be valid ranges

### Automatic Metrics Tracking
- Revenue calculated automatically
- Attendance counted automatically
- Member counts updated automatically
- Transaction counts tracked automatically

### Better User Experience
- Clear error messages
- Immediate feedback
- Data integrity guaranteed
- Consistent validation

## Need More Help?

- Read `TRIGGER_ERROR_HANDLING.md` for detailed guide
- Read `QUICK_REFERENCE_TRIGGERS.md` for quick reference
- Read `DATABASE_TRIGGERS_PROCEDURES_DOCUMENTATION.md` for full documentation
- Run `test-triggers.php` to verify installation

## Installation Status

After completing all steps, mark your progress:

- [ ] Step 1: Database backed up
- [ ] Step 2: phpMyAdmin opened
- [ ] Step 3: Triggers file imported
- [ ] Step 4: Installation verified
- [ ] Step 5: Test script passed
- [ ] Step 6: Application tested
- [ ] Step 7: Metrics verified
- [ ] Step 8: Installation complete ✅

---

**Version**: 1.3.0  
**Last Updated**: March 9, 2026  
**Status**: Ready for Installation
