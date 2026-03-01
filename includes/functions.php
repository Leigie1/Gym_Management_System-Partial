<?php
// ============================================================
// Helper Functions
// ============================================================

// Sanitize input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate unique member ID
function generate_member_id($conn) {
    $query = "SELECT member_id_code FROM members ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['member_id_code'];
        $number = intval(substr($last_id, 4)) + 1;
    } else {
        $number = 1;
    }
    
    return 'MEM-' . str_pad($number, 5, '0', STR_PAD_LEFT);
}

// Calculate expiry date based on duration
function calculate_expiry_date($start_date, $duration) {
    $date = new DateTime($start_date);
    
    switch($duration) {
        case '1 Month':
            $date->modify('+1 month');
            break;
        case '3 Months':
            $date->modify('+3 months');
            break;
        case '6 Months':
            $date->modify('+6 months');
            break;
        case '1 Year':
            $date->modify('+1 year');
            break;
    }
    
    return $date->format('Y-m-d');
}

// Check if membership is expired
function check_member_status($expiry_date) {
    $today = new DateTime();
    $expiry = new DateTime($expiry_date);
    
    if ($expiry < $today) {
        return 'Expired';
    } else {
        return 'Active';
    }
}

// Format date for display
function format_date($date) {
    return date('F d, Y', strtotime($date));
}

// Format time for display
function format_time($time) {
    return date('g:i A', strtotime($time));
}

// Redirect with message
function redirect($url, $message = '') {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit();
}

// Display flash message
function show_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return '';
}
?>
