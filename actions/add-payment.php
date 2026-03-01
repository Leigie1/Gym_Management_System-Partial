<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = intval($_POST['member_id']);
    $category = clean_input($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $amount = floatval($_POST['amount']);
    $payment_method = clean_input($_POST['payment_method']);
    $payment_date = date('Y-m-d');
    $status = 'Paid';
    
    // Insert payment
    $query = "INSERT INTO payments (member_id, category, quantity, amount, payment_method, payment_date, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isidsss", $member_id, $category, $quantity, $amount, $payment_method, $payment_date, $status);
    
    if (mysqli_stmt_execute($stmt)) {
        redirect('../payment.php?success=Payment recorded successfully!');
    } else {
        redirect('../payment.php?error=Failed to record payment');
    }
} else {
    redirect('../payment.php');
}
?>
