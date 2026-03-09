<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = clean_input($_POST['item_name']);
    $category = clean_input($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    
    $query = "INSERT INTO inventory (item_name, category, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssid", $item_name, $category, $quantity, $price);
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            redirect('../inventory.php?success=Item added successfully');
        } else {
            // Get the MySQL error message (includes trigger errors)
            $error_message = mysqli_error($conn);
            redirect('../inventory.php?error=' . urlencode($error_message));
        }
    } catch (mysqli_sql_exception $e) {
        // Catch trigger errors thrown as exceptions
        redirect('../inventory.php?error=' . urlencode($e->getMessage()));
    }
} else {
    redirect('../inventory.php');
}
?>
