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
    
    if (mysqli_stmt_execute($stmt)) {
        redirect('../inventory.php?success=Item added successfully');
    } else {
        redirect('../inventory.php?error=Failed to add item');
    }
} else {
    redirect('../inventory.php');
}
?>
