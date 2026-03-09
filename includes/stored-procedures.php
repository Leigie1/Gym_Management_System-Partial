<?php
/**
 * Stored Procedures Helper Functions
 * This file contains wrapper functions for calling stored procedures
 */

require_once 'config.php';

/**
 * Get member statistics using stored procedure
 * @param int $member_id - Member ID
 * @return array - Statistics array
 */
function get_member_stats($member_id) {
    global $conn;
    
    $query = "CALL sp_get_member_stats(?, @total_checkins, @last_checkin, @total_payments, @days_until_expiry)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        
        // Get OUT parameters
        $result = mysqli_query($conn, "SELECT @total_checkins AS total_checkins, @last_checkin AS last_checkin, @total_payments AS total_payments, @days_until_expiry AS days_until_expiry");
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Update all member statuses based on expiry dates
 * @return int - Number of members updated
 */
function update_member_statuses() {
    global $conn;
    
    $query = "CALL sp_update_member_statuses()";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['updated_count'];
    }
    
    return 0;
}

/**
 * Get revenue report for date range
 * @param string $date_from - Start date (Y-m-d)
 * @param string $date_to - End date (Y-m-d)
 * @return array - Revenue report data
 */
function get_revenue_report($date_from, $date_to) {
    global $conn;
    
    $query = "CALL sp_revenue_report(?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    return [];
}

/**
 * Get attendance report for date range
 * @param string $date_from - Start date (Y-m-d)
 * @param string $date_to - End date (Y-m-d)
 * @return array - Attendance report data
 */
function get_attendance_report($date_from, $date_to) {
    global $conn;
    
    $query = "CALL sp_attendance_report(?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    return [];
}

/**
 * Get memberships expiring within specified days
 * @param int $days_ahead - Number of days to look ahead
 * @return array - Expiring memberships
 */
function get_expiring_memberships($days_ahead = 7) {
    global $conn;
    
    $query = "CALL sp_get_expiring_memberships(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $days_ahead);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    return [];
}

/**
 * Renew member membership
 * @param int $member_id - Member ID
 * @param string $duration - Duration (1 Month, 3 Months, 6 Months, 1 Year)
 * @param float $amount - Payment amount
 * @return array - Result with new_expiry and status
 */
function renew_membership($member_id, $duration, $amount) {
    global $conn;
    
    $query = "CALL sp_renew_membership(?, ?, ?, @new_expiry, @status)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isd", $member_id, $duration, $amount);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        
        // Get OUT parameters
        $result = mysqli_query($conn, "SELECT @new_expiry AS new_expiry, @status AS status");
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Get top active members
 * @param int $limit - Number of members to return
 * @param string $date_from - Start date (Y-m-d)
 * @param string $date_to - End date (Y-m-d)
 * @return array - Top members data
 */
function get_top_members($limit = 10, $date_from = null, $date_to = null) {
    global $conn;
    
    // Default to last 30 days if not specified
    if (!$date_from) $date_from = date('Y-m-d', strtotime('-30 days'));
    if (!$date_to) $date_to = date('Y-m-d');
    
    $query = "CALL sp_get_top_members(?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $limit, $date_from, $date_to);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    return [];
}
?>
