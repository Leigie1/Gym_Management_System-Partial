# Database Triggers & Stored Procedures Documentation

## 📋 Overview

This document provides comprehensive documentation for the 9 triggers and 9 stored procedures implemented in the Power Fitness Gym Management System database.

**File**: `database-triggers-procedures.sql`  
**Database**: `gym_system`  
**Total Procedures**: 9  
**Total Triggers**: 9  

---

## 📚 Table of Contents

1. [Stored Procedures](#stored-procedures)
2. [Triggers](#triggers)
3. [Installation Guide](#installation-guide)
4. [Usage Examples](#usage-examples)
5. [Testing Guide](#testing-guide)
6. [Troubleshooting](#troubleshooting)

---

## 🔧 STORED PROCEDURES

### 1. sp_add_member
**Purpose**: Add a new member with auto-generated member ID and calculated expiry date

**Parameters**:
- `IN p_first_name` VARCHAR(50) - Member's first name
- `IN p_last_name` VARCHAR(50) - Member's last name
- `IN p_address` VARCHAR(200) - Member's address
- `IN p_phone` VARCHAR(20) - Phone number
- `IN p_gender` VARCHAR(10) - Gender (Male/Female)
- `IN p_date_of_birth` DATE - Date of birth
- `IN p_plan` VARCHAR(50) - Membership plan
- `IN p_duration` VARCHAR(20) - Duration (1 Month, 3 Months, 6 Months, 1 Year)
- `IN p_amount` DECIMAL(10,2) - Membership fee
- `IN p_date_enrolled` DATE - Enrollment date
- `OUT p_member_id_code` VARCHAR(20) - Generated member ID (e.g., MEM-00001)

**Usage**:
```sql
CALL sp_add_member(
    'John', 'Doe', '123 Main St', '09123456789', 'Male',
    '1995-05-15', 'Membership', '1 Year', 700.00, CURDATE(),
    @member_id
);
SELECT @member_id;
```

**Features**:
- Auto-generates sequential member IDs (MEM-00001, MEM-00002, etc.)
- Automatically calculates expiry date based on duration
- Sets initial status to 'Active'

---

### 2. sp_checkin_member
**Purpose**: Record member check-in with validation

**Parameters**:
- `IN p_member_id` INT - Member's database ID
- `OUT p_status` VARCHAR(50) - Status (SUCCESS, WARNING, ERROR)
- `OUT p_message` VARCHAR(200) - Result message

**Usage**:
```sql
CALL sp_checkin_member(1, @status, @message);
SELECT @status, @message;
```

**Features**:
- Validates member exists
- Prevents duplicate check-ins on same day
- Returns detailed status and message

**Possible Outputs**:
- SUCCESS: Check-in recorded successfully
- WARNING: Member already checked in today
- ERROR: Member not found

---

### 3. sp_get_member_stats
**Purpose**: Get comprehensive statistics for a specific member

**Parameters**:
- `IN p_member_id` INT - Member's database ID
- `OUT p_total_checkins` INT - Total number of check-ins
- `OUT p_last_checkin` DATE - Date of last check-in
- `OUT p_total_payments` DECIMAL(10,2) - Total payments made
- `OUT p_days_until_expiry` INT - Days remaining until membership expires

**Usage**:
```sql
CALL sp_get_member_stats(1, @checkins, @last, @payments, @days);
SELECT @checkins, @last, @payments, @days;
```

**Use Cases**:
- Member profile display
- Performance tracking
- Renewal reminders

---

### 4. sp_update_member_statuses
**Purpose**: Batch update all member statuses based on expiry dates

**Parameters**: None

**Returns**: Count of updated members

**Usage**:
```sql
CALL sp_update_member_statuses();
```

**Features**:
- Updates all expired members to 'Expired' status
- Should be run daily (can be scheduled as a cron job)
- Returns number of members updated

**Recommended Schedule**: Run daily at midnight

---

### 5. sp_revenue_report
**Purpose**: Generate revenue report for a date range

**Parameters**:
- `IN p_date_from` DATE - Start date
- `IN p_date_to` DATE - End date

**Returns**: Result set with daily revenue breakdown

**Usage**:
```sql
CALL sp_revenue_report('2026-01-01', '2026-01-31');
```

**Output Columns**:
- `date` - Transaction date
- `payment_method` - Payment method (Cash/GCash)
- `transaction_count` - Number of transactions
- `total_amount` - Total revenue

**Use Cases**:
- Monthly revenue reports
- Financial analysis
- Payment method comparison

---

### 6. sp_attendance_report
**Purpose**: Generate attendance report for a date range

**Parameters**:
- `IN p_date_from` DATE - Start date
- `IN p_date_to` DATE - End date

**Returns**: Result set with daily attendance breakdown

**Usage**:
```sql
CALL sp_attendance_report('2026-03-01', '2026-03-31');
```

**Output Columns**:
- `check_in_date` - Date
- `unique_members` - Number of unique members
- `total_checkins` - Total check-ins
- `members` - Comma-separated list of member names

**Use Cases**:
- Monthly attendance reports
- Peak time analysis
- Member engagement tracking

---

### 7. sp_get_expiring_memberships
**Purpose**: Get list of memberships expiring within specified days

**Parameters**:
- `IN p_days_ahead` INT - Number of days to look ahead (e.g., 30)

**Returns**: Result set with expiring memberships

**Usage**:
```sql
CALL sp_get_expiring_memberships(30);
```

**Output Columns**:
- `member_id_code` - Member ID
- `member_name` - Full name
- `phone` - Contact number
- `date_expiry` - Expiry date
- `days_remaining` - Days until expiry
- `plan` - Membership plan
- `amount` - Last payment amount

**Use Cases**:
- Renewal reminders
- Follow-up calls
- Revenue forecasting

---

### 8. sp_renew_membership
**Purpose**: Renew member's membership and record payment

**Parameters**:
- `IN p_member_id` INT - Member's database ID
- `IN p_duration` VARCHAR(20) - New duration
- `IN p_amount` DECIMAL(10,2) - Renewal fee
- `OUT p_new_expiry` DATE - New expiry date
- `OUT p_status` VARCHAR(50) - Status message

**Usage**:
```sql
CALL sp_renew_membership(1, '1 Year', 700.00, @new_expiry, @status);
SELECT @new_expiry, @status;
```

**Features**:
- Extends membership from current expiry or today (whichever is later)
- Automatically records payment
- Updates member status to 'Active'
- Returns new expiry date

**Use Cases**:
- Membership renewals
- Automated renewal processing
- Payment recording

---

### 9. sp_get_top_members
**Purpose**: Get top active members by check-in count

**Parameters**:
- `IN p_limit` INT - Number of members to return (e.g., 5, 10)
- `IN p_date_from` DATE - Start date for counting
- `IN p_date_to` DATE - End date for counting

**Returns**: Result set with top members

**Usage**:
```sql
CALL sp_get_top_members(10, '2026-01-01', '2026-03-31');
```

**Output Columns**:
- `member_id_code` - Member ID
- `member_name` - Full name
- `phone` - Contact number
- `status` - Membership status
- `checkin_count` - Number of check-ins
- `last_checkin` - Last check-in date

**Use Cases**:
- Member recognition programs
- Loyalty rewards
- Engagement analysis

---

## 🔔 TRIGGERS

### 1. trg_before_insert_member
**Type**: BEFORE INSERT  
**Table**: members  
**Purpose**: Validate member data before insertion

**Validations**:
- Phone number must be 10-11 digits
- Amount must be greater than zero
- Member must be at least 10 years old
- Auto-sets created_at timestamp

**Error Messages**:
- "Phone number must be 10-11 digits"
- "Amount must be greater than zero"
- "Member must be at least 10 years old"

**Example**:
```sql
-- This will fail
INSERT INTO members (first_name, last_name, phone, amount, date_of_birth, ...)
VALUES ('John', 'Doe', '123', 700, '2020-01-01', ...);
-- Error: Phone number must be 10-11 digits
```

---

### 2. trg_after_insert_member
**Type**: AFTER INSERT  
**Table**: members  
**Purpose**: Log member creation and update business metrics

**Actions**:
- Counts total members
- Counts active members
- Updates business_metrics table
- Sets session variables for tracking

**Metrics Updated**:
- `total_members` - Total count of all members
- `active_members` - Count of active members only

**Use Cases**:
- Real-time member count tracking
- Dashboard statistics
- Business analytics

---

### 3. trg_before_update_member
**Type**: BEFORE UPDATE  
**Table**: members  
**Purpose**: Auto-update member status and validate changes

**Actions**:
- Automatically sets status to 'Expired' if date_expiry < today
- Automatically sets status to 'Active' if expiry extended
- Validates amount is positive

**Example**:
```sql
-- If member's expiry date passes, status auto-updates to 'Expired'
UPDATE members SET date_expiry = '2026-01-01' WHERE id = 1;
-- Status automatically becomes 'Expired' if today > 2026-01-01
```

---

### 4. trg_after_insert_payment
**Type**: AFTER INSERT  
**Table**: payments  
**Purpose**: Automatically update business metrics when payment is recorded

**Actions**:
- Calculates today's revenue
- Calculates monthly revenue
- Calculates total revenue
- Counts daily transactions
- Counts monthly transactions
- Updates business_metrics table

**Metrics Updated**:
- `daily_revenue` - Total revenue for today
- `monthly_revenue` - Total revenue for current month
- `total_revenue` - All-time total revenue
- `daily_transactions` - Number of transactions today
- `monthly_transactions` - Number of transactions this month

**Features**:
- Real-time revenue tracking
- Automatic metric updates
- No manual calculation needed

**Use Cases**:
- Dashboard revenue display
- Financial reporting
- Business analytics
- Performance tracking

**Example**:
```sql
-- When you insert a payment:
INSERT INTO payments (member_id, category, amount, payment_method, payment_date)
VALUES (1, 'Membership', 700.00, 'Cash', CURDATE());

-- Trigger automatically updates business_metrics table
-- You can then query:
SELECT daily_revenue, monthly_revenue, total_revenue 
FROM business_metrics 
WHERE metric_date = CURDATE();
```

---

### 5. trg_before_insert_attendance
**Type**: BEFORE INSERT  
**Table**: attendance  
**Purpose**: Validate check-in before recording

**Validations**:
- Member must exist
- No duplicate check-ins on same day
- Auto-sets created_at timestamp

**Error Messages**:
- "Member does not exist"
- "Member already checked in today"

**Example**:
```sql
-- This will fail if member already checked in
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());
-- Error: Member already checked in today
```

---

### 6. trg_after_insert_attendance
**Type**: AFTER INSERT  
**Table**: attendance  
**Purpose**: Update business metrics when member checks in

**Actions**:
- Counts today's check-ins
- Counts monthly check-ins
- Updates business_metrics table
- Sets session variables for tracking

**Metrics Updated**:
- `daily_checkins` - Total check-ins for today
- `monthly_checkins` - Total check-ins for current month

**Features**:
- Real-time attendance tracking
- Automatic metric updates
- Dashboard integration

**Use Cases**:
- Dashboard attendance display
- Attendance analytics
- Peak time analysis

**Example**:
```sql
-- When member checks in:
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());

-- Trigger automatically updates business_metrics
-- Query current metrics:
SELECT daily_checkins, monthly_checkins 
FROM business_metrics 
WHERE metric_date = CURDATE();
```

---

### 7. trg_before_insert_payment
**Type**: BEFORE INSERT  
**Table**: payments  
**Purpose**: Validate payment before recording

**Validations**:
- Member must exist
- Amount must be greater than zero
- Quantity defaults to 1 if not provided
- Status defaults to 'Paid' if not provided
- Auto-sets created_at timestamp

**Error Messages**:
- "Member does not exist"
- "Payment amount must be greater than zero"

---

### 8. trg_before_update_inventory
**Type**: BEFORE UPDATE  
**Table**: inventory  
**Purpose**: Validate inventory updates and alert on low stock

**Validations**:
- Quantity cannot be negative
- Price cannot be negative
- Sets low stock alert if quantity drops below 5

**Error Messages**:
- "Inventory quantity cannot be negative"
- "Price cannot be negative"

**Features**:
- Low stock alert when quantity < 5
- Prevents invalid inventory data

---

### 9. trg_before_insert_announcement
**Type**: BEFORE INSERT  
**Table**: announcements  
**Purpose**: Validate announcement data

**Validations**:
- End date must be after start date
- Priority must be Normal, Important, or Urgent (defaults to Normal)
- Auto-sets created_at timestamp

**Error Message**:
- "End date must be after start date"

**Example**:
```sql
-- This will fail
INSERT INTO announcements (title, message, date_from, date_to, priority)
VALUES ('Test', 'Message', '2026-03-10', '2026-03-01', 'Normal');
-- Error: End date must be after start date
```

---

## 📥 INSTALLATION GUIDE

### Step 1: Backup Your Database
```sql
-- Create backup before installing
mysqldump -u root -p gym_system > gym_system_backup.sql
```

### Step 2: Run the SQL File
```sql
-- Method 1: Using MySQL command line
mysql -u root -p gym_system < database-triggers-procedures.sql

-- Method 2: Using phpMyAdmin
-- 1. Open phpMyAdmin
-- 2. Select gym_system database
-- 3. Click "Import" tab
-- 4. Choose database-triggers-procedures.sql
-- 5. Click "Go"

-- Method 3: Copy and paste in MySQL Workbench
-- 1. Open MySQL Workbench
-- 2. Connect to your database
-- 3. Open database-triggers-procedures.sql
-- 4. Execute the entire script
```

### Step 3: Verify Installation
```sql
-- Check stored procedures
SHOW PROCEDURE STATUS WHERE Db = 'gym_system';

-- Check triggers
SHOW TRIGGERS FROM gym_system;

-- Should see 9 procedures and 9 triggers
```

---

## 💡 USAGE EXAMPLES

### Example 1: Add New Member Using Procedure
```sql
-- Add a new member
CALL sp_add_member(
    'Jane', 'Smith', 'Davao City', '09171234567', 'Female',
    '1998-08-20', 'Membership', '6 Months', 500.00, CURDATE(),
    @new_member_id
);

-- Get the generated member ID
SELECT @new_member_id as member_id;
-- Output: MEM-00004
```

### Example 2: Check-in Member
```sql
-- Check-in member with ID 1
CALL sp_checkin_member(1, @status, @message);
SELECT @status, @message;

-- Output: SUCCESS | Check-in recorded successfully
```

### Example 3: Get Member Statistics
```sql
-- Get stats for member ID 1
CALL sp_get_member_stats(1, @checkins, @last, @payments, @days);
SELECT 
    @checkins as total_checkins,
    @last as last_checkin,
    @payments as total_paid,
    @days as days_until_expiry;
```

### Example 4: Generate Monthly Revenue Report
```sql
-- Get revenue for March 2026
CALL sp_revenue_report('2026-03-01', '2026-03-31');
```

### Example 5: Get Expiring Memberships
```sql
-- Get memberships expiring in next 30 days
CALL sp_get_expiring_memberships(30);
```

### Example 6: Renew Membership
```sql
-- Renew member 1 for 1 year
CALL sp_renew_membership(1, '1 Year', 700.00, @new_expiry, @status);
SELECT @new_expiry, @status;
```

### Example 7: Update All Member Statuses
```sql
-- Run daily to update expired members
CALL sp_update_member_statuses();
```

### Example 8: Get Top 10 Active Members
```sql
-- Get top 10 members for Q1 2026
CALL sp_get_top_members(10, '2026-01-01', '2026-03-31');
```

---

## 🧪 TESTING GUIDE

### Test 1: Validate Member Age Restriction
```sql
-- This should fail (member too young)
INSERT INTO members (
    member_id_code, first_name, last_name, phone, gender,
    date_of_birth, plan, duration, amount, date_enrolled, date_expiry, status
) VALUES (
    'MEM-TEST1', 'Child', 'Test', '09123456789', 'Male',
    '2020-01-01', 'Membership', '1 Month', 100, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'Active'
);
-- Expected: Error - Member must be at least 10 years old
```

### Test 2: Validate Duplicate Check-in Prevention
```sql
-- First check-in (should succeed)
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());

-- Second check-in same day (should fail)
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());
-- Expected: Error - Member already checked in today
```

### Test 3: Test Auto Status Update
```sql
-- Set member expiry to past date
UPDATE members SET date_expiry = '2025-01-01' WHERE id = 1;

-- Check status (should be 'Expired')
SELECT status FROM members WHERE id = 1;
-- Expected: Expired
```

### Test 4: Test Member Deletion Protection
```sql
-- Check in member today
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());

-- Try to delete (should fail)
DELETE FROM members WHERE id = 1;
-- Expected: Error - Cannot delete member with recent check-ins
```

---

## 🔧 TROUBLESHOOTING

### Issue 1: "Procedure already exists"
**Solution**:
```sql
-- Drop existing procedures first
DROP PROCEDURE IF EXISTS sp_add_member;
DROP PROCEDURE IF EXISTS sp_checkin_member;
-- ... (drop all 9 procedures)

-- Then re-run the installation script
```

### Issue 2: "Trigger already exists"
**Solution**:
```sql
-- Drop existing triggers first
DROP TRIGGER IF EXISTS trg_before_insert_member;
DROP TRIGGER IF EXISTS trg_after_insert_member;
-- ... (drop all 9 triggers)

-- Then re-run the installation script
```

### Issue 3: DELIMITER not working in phpMyAdmin
**Solution**:
- Use MySQL command line instead
- Or execute each procedure/trigger separately in phpMyAdmin
- Remove DELIMITER statements and execute one at a time

### Issue 4: Permission denied
**Solution**:
```sql
-- Grant necessary permissions
GRANT CREATE ROUTINE ON gym_system.* TO 'root'@'localhost';
GRANT TRIGGER ON gym_system.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Issue 5: Syntax errors
**Solution**:
- Ensure MySQL version is 5.7+
- Check that database name is correct (gym_system)
- Verify all tables exist before creating triggers

---

## 📊 BENEFITS

### For Developers:
- ✅ Reduced code duplication
- ✅ Centralized business logic
- ✅ Easier maintenance
- ✅ Better data integrity
- ✅ Automatic validation

### For Database:
- ✅ Data consistency
- ✅ Automatic calculations
- ✅ Referential integrity
- ✅ Audit trail capability
- ✅ Performance optimization

### For Business:
- ✅ Automated reporting
- ✅ Data accuracy
- ✅ Reduced errors
- ✅ Better insights
- ✅ Scalability

---

## 📝 MAINTENANCE

### Daily Tasks:
```sql
-- Run status update
CALL sp_update_member_statuses();
```

### Weekly Tasks:
```sql
-- Check expiring memberships
CALL sp_get_expiring_memberships(30);

-- Generate weekly attendance report
CALL sp_attendance_report(DATE_SUB(CURDATE(), INTERVAL 7 DAY), CURDATE());
```

### Monthly Tasks:
```sql
-- Generate monthly revenue report
CALL sp_revenue_report(
    DATE_FORMAT(CURDATE(), '%Y-%m-01'),
    LAST_DAY(CURDATE())
);

-- Get top members of the month
CALL sp_get_top_members(
    10,
    DATE_FORMAT(CURDATE(), '%Y-%m-01'),
    LAST_DAY(CURDATE())
);
```

---

## 🎓 LEARNING RESOURCES

### Understanding Stored Procedures:
- Stored procedures are pre-compiled SQL code stored in the database
- They improve performance and security
- Can accept parameters and return results
- Reusable across multiple applications

### Understanding Triggers:
- Triggers are automatic actions that fire on INSERT, UPDATE, or DELETE
- They enforce business rules at the database level
- Cannot be called directly (automatic execution)
- Useful for validation and audit trails

---

## ✅ CHECKLIST

Installation Complete:
- [ ] Backed up database
- [ ] Ran database-triggers-procedures.sql
- [ ] Verified 9 procedures installed
- [ ] Verified 9 triggers installed
- [ ] Tested sample procedures
- [ ] Tested trigger validations
- [ ] Documented any custom changes
- [ ] Scheduled daily status update

---

## 📞 SUPPORT

If you encounter issues:
1. Check MySQL error log
2. Verify database permissions
3. Ensure MySQL version compatibility
4. Review trigger/procedure syntax
5. Test with sample data first

---

**Version**: 1.0  
**Last Updated**: March 2026  
**Database**: gym_system  
**MySQL Version**: 5.7+

---

**Note**: Always test in a development environment before deploying to production!
