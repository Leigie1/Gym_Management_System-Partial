# Quick Reference: Database Triggers

## For Developers

This is a quick reference guide for understanding how database triggers work in the Power Fitness Gym system.

## What Are Triggers?

Triggers are automatic database operations that run BEFORE or AFTER an INSERT, UPDATE, or DELETE operation. They enforce business rules and maintain data integrity.

## Active Triggers in System

### 1. Member Insert Validation
**Trigger**: `trg_before_insert_member`  
**When**: BEFORE inserting a new member  
**What it does**:
- Validates phone is 10-11 digits
- Validates amount > 0
- Validates age >= 10 years

**Error Messages**:
- "Phone number must be 10-11 digits"
- "Amount must be greater than zero"
- "Member must be at least 10 years old"

### 2. Member Insert Metrics
**Trigger**: `trg_after_insert_member`  
**When**: AFTER inserting a new member  
**What it does**:
- Updates total_members count
- Updates active_members count
- Records in business_metrics table

### 3. Member Update Status
**Trigger**: `trg_before_update_member`  
**When**: BEFORE updating a member  
**What it does**:
- Auto-sets status to 'Expired' if date_expiry < today
- Auto-sets status to 'Active' if date_expiry >= today
- Validates amount > 0

### 4. Payment Insert Metrics
**Trigger**: `trg_after_insert_payment`  
**When**: AFTER inserting a payment  
**What it does**:
- Calculates daily revenue
- Calculates monthly revenue
- Calculates total revenue
- Counts transactions
- Updates business_metrics table

### 5. Payment Insert Validation
**Trigger**: `trg_before_insert_payment`  
**When**: BEFORE inserting a payment  
**What it does**:
- Validates member exists
- Validates amount > 0
- Sets default quantity = 1
- Sets default status = 'Paid'

**Error Messages**:
- "Member does not exist"
- "Payment amount must be greater than zero"

### 6. Attendance Insert Validation
**Trigger**: `trg_before_insert_attendance`  
**When**: BEFORE inserting attendance  
**What it does**:
- Validates member exists
- Prevents duplicate check-in on same day

**Error Messages**:
- "Member does not exist"
- "Member already checked in today"

### 7. Attendance Insert Metrics
**Trigger**: `trg_after_insert_attendance`  
**When**: AFTER inserting attendance  
**What it does**:
- Counts daily check-ins
- Counts monthly check-ins
- Updates business_metrics table

### 8. Inventory Update Validation
**Trigger**: `trg_before_update_inventory`  
**When**: BEFORE updating inventory  
**What it does**:
- Prevents negative quantity
- Prevents negative price
- Sets low stock alert (< 5 items)

**Error Messages**:
- "Inventory quantity cannot be negative"
- "Price cannot be negative"

### 9. Announcement Insert Validation
**Trigger**: `trg_before_insert_announcement`  
**When**: BEFORE inserting announcement  
**What it does**:
- Validates date_to > date_from
- Auto-corrects invalid priority to 'Normal'

**Error Messages**:
- "End date must be after start date"

## How to Handle Trigger Errors in PHP

### Standard Pattern
```php
if (mysqli_stmt_execute($stmt)) {
    // Success
    redirect('page.php?success=Operation successful');
} else {
    // Get trigger error message
    $error_message = mysqli_error($conn);
    redirect('page.php?error=' . urlencode($error_message));
}
```

### JSON API Pattern
```php
if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Operation successful'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => mysqli_error($conn)
    ]);
}
```

## Testing Triggers

Run `test-triggers.php` in your browser to test all trigger validations.

## Common Scenarios

### Scenario 1: User enters invalid phone
1. User submits form with phone "12345"
2. PHP executes INSERT query
3. Trigger detects phone length < 10
4. Trigger throws error: "Phone number must be 10-11 digits"
5. PHP catches error with `mysqli_error($conn)`
6. User sees error message on screen

### Scenario 2: User tries to check in twice
1. User scans QR code
2. PHP executes INSERT into attendance
3. Trigger detects existing check-in for today
4. Trigger throws error: "Member already checked in today"
5. API returns JSON with error message
6. Frontend displays error to user

### Scenario 3: Payment with invalid amount
1. User submits payment form with amount = 0
2. PHP executes INSERT into payments
3. Trigger detects amount <= 0
4. Trigger throws error: "Payment amount must be greater than zero"
5. PHP catches error and redirects with message
6. User sees error and can correct the amount

## Disabling Triggers (For Testing Only)

```sql
-- Disable a specific trigger
DROP TRIGGER IF EXISTS trg_before_insert_member;

-- Re-enable by running the CREATE TRIGGER statement again
-- (found in database-triggers-procedures.sql)
```

## Viewing Active Triggers

```sql
-- Show all triggers in database
SHOW TRIGGERS FROM gym_system;

-- Show specific trigger definition
SHOW CREATE TRIGGER trg_before_insert_member;
```

## Business Metrics Table

Triggers automatically maintain the `business_metrics` table:

```sql
SELECT * FROM business_metrics WHERE metric_date = CURDATE();
```

Fields updated automatically:
- `daily_revenue` - Today's total revenue
- `monthly_revenue` - This month's total revenue
- `total_revenue` - All-time revenue
- `daily_transactions` - Today's payment count
- `monthly_transactions` - This month's payment count
- `daily_checkins` - Today's check-in count
- `monthly_checkins` - This month's check-in count
- `total_members` - Total member count
- `active_members` - Active member count

## Important Notes

1. **Triggers run automatically** - No PHP code needed to call them
2. **Triggers enforce rules** - Cannot be bypassed by PHP code
3. **Error messages are user-friendly** - Display them directly to users
4. **Triggers maintain metrics** - business_metrics table always up-to-date
5. **Triggers are fast** - Minimal performance impact

## Files to Reference

- `database-triggers-procedures.sql` - All trigger code
- `TRIGGER_ERROR_HANDLING.md` - Detailed error handling guide
- `DATABASE_TRIGGERS_PROCEDURES_DOCUMENTATION.md` - Full documentation
- `test-triggers.php` - Test all triggers

## Need Help?

1. Check error message - it tells you exactly what's wrong
2. Run `test-triggers.php` to verify triggers are working
3. Check `TRIGGER_ERROR_HANDLING.md` for detailed examples
4. View trigger code in `database-triggers-procedures.sql`
