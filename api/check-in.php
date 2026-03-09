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
        
        // Call stored procedure to check in member
        $call_query = "CALL sp_checkin_member(?, @status, @message)";
        $call_stmt = mysqli_prepare($conn, $call_query);
        mysqli_stmt_bind_param($call_stmt, "i", $member['id']);
        
        try {
            if (mysqli_stmt_execute($call_stmt)) {
                mysqli_stmt_close($call_stmt);
                
                // Get the OUT parameters
                $result = mysqli_query($conn, "SELECT @status AS status, @message AS message");
                $row = mysqli_fetch_assoc($result);
                
                if ($row['status'] === 'SUCCESS') {
                    echo json_encode([
                        'success' => true,
                        'message' => $row['message'],
                        'member' => [
                            'name' => $member['first_name'] . ' ' . $member['last_name'],
                            'address' => $member['address'],
                            'status' => $member['status']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $row['message']
                    ]);
                }
            } else {
                // Get the MySQL error message (includes trigger errors)
                $error_message = mysqli_error($conn);
                echo json_encode([
                    'success' => false,
                    'message' => $error_message
                ]);
            }
        } catch (mysqli_sql_exception $e) {
            // Catch trigger errors thrown as exceptions
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
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
