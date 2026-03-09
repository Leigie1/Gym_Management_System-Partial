<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/qr-generator.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $address = clean_input($_POST['address']);
    $gender = clean_input($_POST['gender']);
    $phone = clean_input($_POST['phone']);
    $date_of_birth = $_POST['date_of_birth'];
    $plan = clean_input($_POST['plan']);
    $duration = clean_input($_POST['duration']);
    $amount = floatval($_POST['amount']);
    $date_enrolled = $_POST['date_enrolled'];
    
    // Call stored procedure to add member
    $query = "CALL sp_add_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @member_id_code)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssds", 
        $first_name, $last_name, $address, $phone, $gender, 
        $date_of_birth, $plan, $duration, $amount, $date_enrolled
    );
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            
            // Get the generated member ID from OUT parameter
            $result = mysqli_query($conn, "SELECT @member_id_code AS member_id_code");
            $row = mysqli_fetch_assoc($result);
            $member_id_code = $row['member_id_code'];
            
            // Generate QR code for member
            $qr_save_path = '../' . get_qr_path($member_id_code);
            $qr_generated = generate_member_qr($member_id_code, $qr_save_path);
            
            if ($qr_generated) {
                redirect('../manage-member.php?success=Member added successfully! ID: ' . $member_id_code);
            } else {
                redirect('../manage-member.php?success=Member added but QR generation failed. ID: ' . $member_id_code);
            }
        } else {
            // Get the MySQL error message (includes trigger errors)
            $error_message = mysqli_error($conn);
            redirect('../manage-member.php?error=' . urlencode($error_message));
        }
    } catch (mysqli_sql_exception $e) {
        // Catch trigger errors thrown as exceptions
        redirect('../manage-member.php?error=' . urlencode($e->getMessage()));
    }
} else {
    redirect('../manage-member.php');
}
?>
