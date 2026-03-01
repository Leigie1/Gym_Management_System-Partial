<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

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
    
    // Generate member ID
    $member_id_code = generate_member_id($conn);
    
    // Calculate expiry date
    $date_expiry = calculate_expiry_date($date_enrolled, $duration);
    
    // Status is Active by default
    $status = 'Active';
    
    // Insert member
    $query = "INSERT INTO members (member_id_code, first_name, last_name, address, phone, gender, date_of_birth, plan, duration, amount, date_enrolled, date_expiry, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssdsss", 
        $member_id_code, $first_name, $last_name, $address, $phone, $gender, 
        $date_of_birth, $plan, $duration, $amount, $date_enrolled, $date_expiry, $status
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $member_id = mysqli_insert_id($conn);
        
        // TODO: Generate QR code here (we'll add this later)
        
        redirect('../manage-member.php?success=Member added successfully! ID: ' . $member_id_code);
    } else {
        redirect('../manage-member.php?error=Failed to add member');
    }
} else {
    redirect('../manage-member.php');
}
?>
