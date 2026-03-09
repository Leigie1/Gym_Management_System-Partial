<?php
/**
 * Trigger Error Handling Test Script
 * This script tests all trigger validations and error handling
 */

require_once 'includes/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Trigger Error Handling Test</h1>";
echo "<p>Testing all database triggers and error handling...</p>";
echo "<hr>";

// Test 1: Phone Number Validation (Member Trigger)
echo "<h2>Test 1: Phone Number Validation</h2>";
echo "<p>Attempting to insert member with invalid phone (5 digits)...</p>";

$query = "INSERT INTO members (member_id_code, first_name, last_name, phone, amount, date_of_birth, date_enrolled, date_expiry, status) 
          VALUES ('MEM-TEST1', 'Test', 'User', '12345', 1000, '2000-01-01', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'Active')";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
} else {
    $error = mysqli_error($conn);
    echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
}
echo "<hr>";

// Test 2: Age Validation (Member Trigger)
echo "<h2>Test 2: Age Validation</h2>";
echo "<p>Attempting to insert member under 10 years old...</p>";

$recent_date = date('Y-m-d', strtotime('-5 years'));
$query = "INSERT INTO members (member_id_code, first_name, last_name, phone, amount, date_of_birth, date_enrolled, date_expiry, status) 
          VALUES ('MEM-TEST2', 'Test', 'Child', '1234567890', 1000, '$recent_date', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'Active')";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
} else {
    $error = mysqli_error($conn);
    echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
}
echo "<hr>";

// Test 3: Amount Validation (Member Trigger)
echo "<h2>Test 3: Amount Validation (Member)</h2>";
echo "<p>Attempting to insert member with zero amount...</p>";

$query = "INSERT INTO members (member_id_code, first_name, last_name, phone, amount, date_of_birth, date_enrolled, date_expiry, status) 
          VALUES ('MEM-TEST3', 'Test', 'User', '1234567890', 0, '2000-01-01', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'Active')";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
} else {
    $error = mysqli_error($conn);
    echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
}
echo "<hr>";

// Test 4: Duplicate Check-in (Attendance Trigger)
echo "<h2>Test 4: Duplicate Check-in Prevention</h2>";
echo "<p>Attempting to check in same member twice today...</p>";

// First, get a valid member ID
$member_query = "SELECT id FROM members LIMIT 1";
$member_result = mysqli_query($conn, $member_query);

if ($member_result && mysqli_num_rows($member_result) > 0) {
    $member = mysqli_fetch_assoc($member_result);
    $member_id = $member['id'];
    
    // Delete any existing check-in for today (cleanup)
    mysqli_query($conn, "DELETE FROM attendance WHERE member_id = $member_id AND check_in_date = CURDATE()");
    
    // First check-in (should succeed)
    $query1 = "INSERT INTO attendance (member_id, check_in_date, check_in_time) VALUES ($member_id, CURDATE(), CURTIME())";
    if (mysqli_query($conn, $query1)) {
        echo "<p style='color: blue;'>First check-in successful</p>";
        
        // Second check-in (should fail)
        $query2 = "INSERT INTO attendance (member_id, check_in_date, check_in_time) VALUES ($member_id, CURDATE(), CURTIME())";
        if (mysqli_query($conn, $query2)) {
            echo "<p style='color: red;'>❌ FAILED: Duplicate check-in should have been rejected</p>";
        } else {
            $error = mysqli_error($conn);
            echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
        }
        
        // Cleanup
        mysqli_query($conn, "DELETE FROM attendance WHERE member_id = $member_id AND check_in_date = CURDATE()");
    } else {
        echo "<p style='color: orange;'>⚠️ SKIPPED: Could not insert first check-in</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ SKIPPED: No members found in database</p>";
}
echo "<hr>";

// Test 5: Invalid Payment Amount (Payment Trigger)
echo "<h2>Test 5: Payment Amount Validation</h2>";
echo "<p>Attempting to insert payment with negative amount...</p>";

$member_query = "SELECT id FROM members LIMIT 1";
$member_result = mysqli_query($conn, $member_query);

if ($member_result && mysqli_num_rows($member_result) > 0) {
    $member = mysqli_fetch_assoc($member_result);
    $member_id = $member['id'];
    
    $query = "INSERT INTO payments (member_id, category, quantity, amount, payment_method, payment_date, status) 
              VALUES ($member_id, 'Test', 1, -100, 'Cash', CURDATE(), 'Paid')";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
    } else {
        $error = mysqli_error($conn);
        echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ SKIPPED: No members found in database</p>";
}
echo "<hr>";

// Test 6: Invalid Date Range (Announcement Trigger)
echo "<h2>Test 6: Announcement Date Validation</h2>";
echo "<p>Attempting to insert announcement with end date before start date...</p>";

$query = "INSERT INTO announcements (title, message, date_from, date_to, priority) 
          VALUES ('Test', 'Test message', '2024-12-31', '2024-01-01', 'Normal')";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
    // Cleanup
    mysqli_query($conn, "DELETE FROM announcements WHERE title = 'Test' AND message = 'Test message'");
} else {
    $error = mysqli_error($conn);
    echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
}
echo "<hr>";

// Test 7: Non-existent Member (Payment Trigger)
echo "<h2>Test 7: Payment with Non-existent Member</h2>";
echo "<p>Attempting to insert payment for member ID 999999...</p>";

$query = "INSERT INTO payments (member_id, category, quantity, amount, payment_method, payment_date, status) 
          VALUES (999999, 'Test', 1, 100, 'Cash', CURDATE(), 'Paid')";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
} else {
    $error = mysqli_error($conn);
    echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
}
echo "<hr>";

// Test 8: Negative Inventory Quantity (Inventory Trigger)
echo "<h2>Test 8: Inventory Quantity Validation</h2>";
echo "<p>Attempting to update inventory with negative quantity...</p>";

// First, insert a test item
$insert_query = "INSERT INTO inventory (item_name, category, quantity, price) VALUES ('Test Item', 'Equipment', 10, 100)";
if (mysqli_query($conn, $insert_query)) {
    $item_id = mysqli_insert_id($conn);
    
    // Try to update with negative quantity
    $update_query = "UPDATE inventory SET quantity = -5 WHERE id = $item_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<p style='color: red;'>❌ FAILED: Should have been rejected by trigger</p>";
    } else {
        $error = mysqli_error($conn);
        echo "<p style='color: green;'>✅ PASSED: " . htmlspecialchars($error) . "</p>";
    }
    
    // Cleanup
    mysqli_query($conn, "DELETE FROM inventory WHERE id = $item_id");
} else {
    echo "<p style='color: orange;'>⚠️ SKIPPED: Could not insert test item</p>";
}
echo "<hr>";

echo "<h2>Test Summary</h2>";
echo "<p>All trigger validations have been tested. Check the results above.</p>";
echo "<p><strong>Note:</strong> All tests should show ✅ PASSED with appropriate error messages.</p>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";

mysqli_close($conn);
?>
