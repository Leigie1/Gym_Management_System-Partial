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
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            redirect('../payment.php?success=Payment recorded successfully!');
        } else {
            // Get the MySQL error message (includes trigger errors)
            $error_message = mysqli_error($conn);
            redirect('../payment.php?error=' . urlencode($error_message));
        }
    } catch (mysqli_sql_exception $e) {
        // Catch trigger errors thrown as exceptions
        redirect('../payment.php?error=' . urlencode($e->getMessage()));
    }
} else {
    redirect('../payment.php');
}
?>
