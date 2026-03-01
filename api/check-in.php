<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['member_id'])) {
    $member_id_code = clean_input($_POST['member_id']);
    
    // Find member by ID code
    $query = "SELECT * FROM members WHERE member_id_code = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $member_id_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $member = mysqli_fetch_assoc($result);
        
        // Check if already checked in today
        $check_query = "SELECT * FROM attendance WHERE member_id = ? AND check_in_date = CURDATE()";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $member['id']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Member already checked in today'
            ]);
            exit();
        }
        
        // Insert attendance record
        $insert_query = "INSERT INTO attendance (member_id, check_in_date, check_in_time) VALUES (?, CURDATE(), CURTIME())";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "i", $member['id']);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo json_encode([
                'success' => true,
                'message' => 'Check-in successful',
                'member' => [
                    'name' => $member['first_name'] . ' ' . $member['last_name'],
                    'address' => $member['address'],
                    'status' => $member['status']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to record attendance'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Member ID not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
