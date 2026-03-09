# Stored Procedures Usage Guide

## Overview

The Power Fitness Gym system now uses stored procedures for key database operations. This provides better performance, security, and maintainability.

## Implemented Stored Procedures

### ✅ Currently Used in System

#### 1. sp_add_member
**File**: `actions/add-member.php`  
**Purpose**: Add new member with auto-generated ID and expiry calculation

**Usage**:
```php
$query = "CALL sp_add_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @member_id_code)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssssssds", 
    $first_name, $last_name, $address, $phone, $gender, 
    $date_of_birth, $plan, $duration, $amount, $date_enrolled
);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Get generated member ID
$result = mysqli_query($conn, "SELECT @member_id_code AS member_id_code");
$row = mysqli_fetch_assoc($result);
$member_id_code = $row['member_id_code'];
```

#### 2. sp_checkin_member
**File**: `api/check-in.php`  
**Purpose**: Record member check-in with validation

**Usage**:
```php
$query = "CALL sp_checkin_member(?, @status, @message)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $member_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Get result status
$result = mysqli_query($conn, "SELECT @status AS status, @message AS message");
$row = mysqli_fetch_assoc($result);

if ($row['status'] === 'SUCCESS') {
    // Check-in successful
} else {
    // Handle error
}
```

### 📦 Available via Helper Functions

All other stored procedures can be called using the helper functions in `includes/stored-procedures.php`:

#### 3. sp_get_member_stats
**Function**: `get_member_stats($member_id)`  
**Purpose**: Get comprehensive member statistics

**Usage**:
```php
require_once 'includes/stored-procedures.php';

$stats = get_member_stats(1);
echo "Total Check-ins: " . $stats['total_checkins'];
echo "Last Check-in: " . $stats['last_checkin'];
echo "Total Payments: ₱" . number_format($stats['total_payments'], 2);
echo "Days Until Expiry: " . $stats['days_until_expiry'];
```

**Returns**:
```php
[
    'total_checkins' => 45,
    'last_checkin' => '2026-03-09',
    'total_payments' => 5000.00,
    'days_until_expiry' => 25
]
```

#### 4. sp_update_member_statuses
**Function**: `update_member_statuses()`  
**Purpose**: Update all expired member statuses

**Usage**:
```php
require_once 'includes/stored-procedures.php';

$updated_count = update_member_statuses();
echo "Updated $updated_count members";
```

**Use Case**: Run this daily via cron job or manually to update expired memberships

#### 5. sp_revenue_report
**Function**: `get_revenue_report($date_from, $date_to)`  
**Purpose**: Generate revenue report for date range

**Usage**:
```php
require_once 'includes/stored-procedures.php';

$date_from = '2026-03-01';
$date_to = '2026-03-31';
$report = get_revenue_report($date_from, $date_to);

foreach ($report as $row) {
    echo "Date: " . $row['date'];
    echo "Method: " . $row['payment_method'];
    echo "Transactions: " . $row['transaction_count'];
    echo "Amount: ₱" . number_format($row['total_amount'], 2);
}
```

**Returns**: Array of daily revenue grouped by payment method

#### 6. sp_attendance_report
**Function**: `get_attendance_report($date_from, $date_to)`  
**Purpose**: Generate attendance report for date range

**Usage**:
```php
require_once 'includes/stored-procedures.php';

$date_from = '2026-03-01';
$date_to = '2026-03-09';
$report = get_attendance_report($date_from, $date_to);

foreach ($report as $row) {
    echo "Date: " . $row['check_in_date'];
    echo "Unique Members: " . $row['unique_members'];
    echo "Total Check-ins: " . $row['total_checkins'];
}
```

**Returns**: Array of daily attendance statistics

#### 7. sp_get_expiring_memberships
**Function**: `get_expiring_memberships($days_ahead = 7)`  
**Purpose**: Find memberships expiring soon

**Usage**:
```php
require_once 'includes/stored-procedures.php';

// Get memberships expiring in next 30 days
$expiring = get_expiring_memberships(30);

foreach ($expiring as $member) {
    echo "Member: " . $member['member_name'];
    echo "Expires: " . $member['date_expiry'];
    echo "Days Remaining: " . $member['days_remaining'];
    
    // Send reminder email/SMS
    if ($member['days_remaining'] <= 7) {
        // Urgent reminder
    }
}
```

**Returns**: Array of expiring memberships with member details

**Use Case**: 
- Daily reminder system
- Renewal notifications
- Dashboard alerts

#### 8. sp_renew_membership
**Function**: `renew_membership($member_id, $duration, $amount)`  
**Purpose**: Renew member subscription

**Usage**:
```php
require_once 'includes/stored-procedures.php';

$member_id = 1;
$duration = '3 Months';
$amount = 3000;

$result = renew_membership($member_id, $duration, $amount);

if ($result && strpos($result['status'], 'SUCCESS') !== false) {
    echo "Membership renewed!";
    echo "New expiry date: " . $result['new_expiry'];
} else {
    echo "Error: " . $result['status'];
}
```

**Returns**:
```php
[
    'new_expiry' => '2026-06-09',
    'status' => 'SUCCESS: Membership renewed'
]
```

**Features**:
- Automatically calculates new expiry from current expiry or today
- Records payment transaction
- Updates member status to Active

#### 9. sp_get_top_members
**Function**: `get_top_members($limit = 10, $date_from = null, $date_to = null)`  
**Purpose**: Get most active members

**Usage**:
```php
require_once 'includes/stored-procedures.php';

// Get top 5 members for last 30 days
$top_members = get_top_members(5);

foreach ($top_members as $member) {
    echo "Member: " . $member['member_name'];
    echo "Check-ins: " . $member['checkin_count'];
    echo "Last Check-in: " . $member['last_checkin'];
}
```

**Returns**: Array of top members sorted by check-in count

**Use Case**:
- Member of the month
- Loyalty rewards
- Dashboard statistics

## Testing Stored Procedures

Run the test script to verify all stored procedures:

```bash
http://localhost/Gym_Management_System/test-stored-procedures.php
```

This will:
- Test all 9 stored procedures
- Display sample output
- Show usage examples
- Verify functionality

## Benefits of Using Stored Procedures

### 1. Performance
- Compiled and cached by MySQL
- Reduced network traffic
- Faster execution

### 2. Security
- Prevents SQL injection
- Encapsulates business logic
- Controlled data access

### 3. Maintainability
- Centralized business logic
- Easier to update
- Consistent behavior

### 4. Code Reusability
- Call from multiple places
- Consistent implementation
- Less code duplication

## Migration Guide

### Before (Direct SQL):
```php
$query = "INSERT INTO members (...) VALUES (...)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sss...", $var1, $var2, ...);
mysqli_stmt_execute($stmt);
```

### After (Stored Procedure):
```php
$query = "CALL sp_add_member(?, ?, ?, @member_id_code)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sss", $var1, $var2, $var3);
mysqli_stmt_execute($stmt);
```

## Common Patterns

### Pattern 1: Procedure with OUT Parameters
```php
// Call procedure
$query = "CALL sp_procedure(?, @out_param)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $input);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Get OUT parameter
$result = mysqli_query($conn, "SELECT @out_param AS value");
$row = mysqli_fetch_assoc($result);
$output = $row['value'];
```

### Pattern 2: Procedure Returning Result Set
```php
// Call procedure
$query = "CALL sp_procedure(?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $param1, $param2);
mysqli_stmt_execute($stmt);

// Get result set
$result = mysqli_stmt_get_result($stmt);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
mysqli_stmt_close($stmt);
```

### Pattern 3: Procedure with Error Handling
```php
try {
    $query = "CALL sp_procedure(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $param);
    
    if (mysqli_stmt_execute($stmt)) {
        // Success
    } else {
        $error = mysqli_error($conn);
        // Handle error
    }
} catch (mysqli_sql_exception $e) {
    // Handle exception
    $error = $e->getMessage();
}
```

## Future Enhancements

### Potential New Procedures:
1. `sp_generate_invoice` - Generate payment invoice
2. `sp_member_activity_log` - Log member activities
3. `sp_bulk_renewal` - Renew multiple memberships
4. `sp_inventory_restock` - Restock inventory items
5. `sp_monthly_summary` - Generate monthly reports

## Troubleshooting

### Issue: "Procedure does not exist"
**Solution**: Import `database-triggers-procedures.sql`

### Issue: "OUT parameter not working"
**Solution**: Close statement before reading OUT parameters
```php
mysqli_stmt_close($stmt);
$result = mysqli_query($conn, "SELECT @param");
```

### Issue: "Commands out of sync"
**Solution**: Always close statements and free results
```php
mysqli_stmt_close($stmt);
mysqli_free_result($result);
```

## Related Files

- `database-triggers-procedures.sql` - All procedure definitions
- `includes/stored-procedures.php` - Helper functions
- `test-stored-procedures.php` - Test script
- `actions/add-member.php` - Example usage
- `api/check-in.php` - Example usage

## Version Information

- **Version**: 1.3.0
- **Date**: March 9, 2026
- **Status**: Implemented ✅
- **Procedures**: 9 total (2 in use, 7 available)
