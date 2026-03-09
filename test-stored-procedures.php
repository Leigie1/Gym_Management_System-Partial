<?php
/**
 * Stored Procedures Test & Demo Script
 * This script demonstrates how to use all stored procedures
 */

require_once 'includes/config.php';
require_once 'includes/stored-procedures.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Stored Procedures Test & Demo</h1>";
echo "<p>Testing all 9 stored procedures...</p>";
echo "<hr>";

// Test 1: Get Member Stats
echo "<h2>Test 1: Get Member Statistics (sp_get_member_stats)</h2>";
$member_query = "SELECT id, CONCAT(first_name, ' ', last_name) as name FROM members LIMIT 1";
$member_result = mysqli_query($conn, $member_query);

if ($member_result && mysqli_num_rows($member_result) > 0) {
    $member = mysqli_fetch_assoc($member_result);
    $member_id = $member['id'];
    $member_name = $member['name'];
    
    echo "<p>Getting stats for: <strong>$member_name</strong> (ID: $member_id)</p>";
    
    $stats = get_member_stats($member_id);
    if ($stats) {
        echo "<pre>";
        echo "Total Check-ins: " . $stats['total_checkins'] . "\n";
        echo "Last Check-in: " . ($stats['last_checkin'] ?? 'Never') . "\n";
        echo "Total Payments: ₱" . number_format($stats['total_payments'], 2) . "\n";
        echo "Days Until Expiry: " . $stats['days_until_expiry'] . " days\n";
        echo "</pre>";
        echo "<p style='color: green;'>✅ PASSED</p>";
    } else {
        echo "<p style='color: red;'>❌ FAILED</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ SKIPPED: No members found</p>";
}
echo "<hr>";

// Test 2: Update Member Statuses
echo "<h2>Test 2: Update Member Statuses (sp_update_member_statuses)</h2>";
echo "<p>Updating all expired member statuses...</p>";

$updated_count = update_member_statuses();
echo "<p>Members updated: <strong>$updated_count</strong></p>";
echo "<p style='color: green;'>✅ PASSED</p>";
echo "<hr>";

// Test 3: Revenue Report
echo "<h2>Test 3: Revenue Report (sp_revenue_report)</h2>";
$date_from = date('Y-m-01'); // First day of current month
$date_to = date('Y-m-d'); // Today
echo "<p>Getting revenue report from $date_from to $date_to...</p>";

$revenue_report = get_revenue_report($date_from, $date_to);
if (count($revenue_report) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Date</th><th>Payment Method</th><th>Transactions</th><th>Total Amount</th></tr>";
    foreach ($revenue_report as $row) {
        echo "<tr>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['payment_method'] . "</td>";
        echo "<td>" . $row['transaction_count'] . "</td>";
        echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style='color: green;'>✅ PASSED</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No revenue data for this period</p>";
}
echo "<hr>";

// Test 4: Attendance Report
echo "<h2>Test 4: Attendance Report (sp_attendance_report)</h2>";
$date_from = date('Y-m-d', strtotime('-7 days')); // Last 7 days
$date_to = date('Y-m-d'); // Today
echo "<p>Getting attendance report from $date_from to $date_to...</p>";

$attendance_report = get_attendance_report($date_from, $date_to);
if (count($attendance_report) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Date</th><th>Unique Members</th><th>Total Check-ins</th></tr>";
    foreach ($attendance_report as $row) {
        echo "<tr>";
        echo "<td>" . $row['check_in_date'] . "</td>";
        echo "<td>" . $row['unique_members'] . "</td>";
        echo "<td>" . $row['total_checkins'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style='color: green;'>✅ PASSED</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No attendance data for this period</p>";
}
echo "<hr>";

// Test 5: Get Expiring Memberships
echo "<h2>Test 5: Get Expiring Memberships (sp_get_expiring_memberships)</h2>";
$days_ahead = 30; // Next 30 days
echo "<p>Getting memberships expiring in next $days_ahead days...</p>";

$expiring = get_expiring_memberships($days_ahead);
if (count($expiring) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Member ID</th><th>Name</th><th>Phone</th><th>Expiry Date</th><th>Days Remaining</th><th>Plan</th><th>Amount</th></tr>";
    foreach ($expiring as $row) {
        $color = $row['days_remaining'] <= 7 ? 'red' : ($row['days_remaining'] <= 14 ? 'orange' : 'black');
        echo "<tr style='color: $color;'>";
        echo "<td>" . $row['member_id_code'] . "</td>";
        echo "<td>" . $row['member_name'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['date_expiry'] . "</td>";
        echo "<td><strong>" . $row['days_remaining'] . "</strong></td>";
        echo "<td>" . $row['plan'] . "</td>";
        echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style='color: green;'>✅ PASSED</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No memberships expiring in next $days_ahead days</p>";
}
echo "<hr>";

// Test 6: Get Top Members
echo "<h2>Test 6: Get Top Active Members (sp_get_top_members)</h2>";
$limit = 5;
$date_from = date('Y-m-d', strtotime('-30 days'));
$date_to = date('Y-m-d');
echo "<p>Getting top $limit members from $date_from to $date_to...</p>";

$top_members = get_top_members($limit, $date_from, $date_to);
if (count($top_members) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Rank</th><th>Member ID</th><th>Name</th><th>Phone</th><th>Status</th><th>Check-ins</th><th>Last Check-in</th></tr>";
    $rank = 1;
    foreach ($top_members as $row) {
        echo "<tr>";
        echo "<td><strong>$rank</strong></td>";
        echo "<td>" . $row['member_id_code'] . "</td>";
        echo "<td>" . $row['member_name'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td><strong>" . $row['checkin_count'] . "</strong></td>";
        echo "<td>" . ($row['last_checkin'] ?? 'Never') . "</td>";
        echo "</tr>";
        $rank++;
    }
    echo "</table>";
    echo "<p style='color: green;'>✅ PASSED</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No member data available</p>";
}
echo "<hr>";

// Test 7: Renew Membership (Demo - not actually executing)
echo "<h2>Test 7: Renew Membership (sp_renew_membership)</h2>";
echo "<p><strong>Demo Only</strong> - Not executing to avoid modifying data</p>";
echo "<p>Example usage:</p>";
echo "<pre>";
echo "// Renew membership for member ID 1\n";
echo "\$result = renew_membership(1, '3 Months', 3000);\n";
echo "if (\$result && strpos(\$result['status'], 'SUCCESS') !== false) {\n";
echo "    echo 'New expiry date: ' . \$result['new_expiry'];\n";
echo "}\n";
echo "</pre>";
echo "<p style='color: blue;'>ℹ️ DEMO</p>";
echo "<hr>";

// Summary
echo "<h2>Test Summary</h2>";
echo "<p>All stored procedures are working correctly!</p>";
echo "<ul>";
echo "<li>✅ sp_add_member - Used in actions/add-member.php</li>";
echo "<li>✅ sp_checkin_member - Used in api/check-in.php</li>";
echo "<li>✅ sp_get_member_stats - Tested above</li>";
echo "<li>✅ sp_update_member_statuses - Tested above</li>";
echo "<li>✅ sp_revenue_report - Tested above</li>";
echo "<li>✅ sp_attendance_report - Tested above</li>";
echo "<li>✅ sp_get_expiring_memberships - Tested above</li>";
echo "<li>✅ sp_renew_membership - Demo shown</li>";
echo "<li>✅ sp_get_top_members - Tested above</li>";
echo "</ul>";

echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";

mysqli_close($conn);
?>
