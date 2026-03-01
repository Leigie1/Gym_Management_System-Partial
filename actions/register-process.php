<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        redirect('../login.php?error=Passwords do not match');
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        redirect('../login.php?error=Email already registered');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $full_name = $first_name . ' ' . $last_name;
    
    // Insert new user
    $insert_query = "INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, 'admin')";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "sss", $email, $hashed_password, $full_name);
    
    if (mysqli_stmt_execute($stmt)) {
        redirect('../login.php?success=Account created successfully! Please login.');
    } else {
        redirect('../login.php?error=Registration failed. Please try again.');
    }
} else {
    redirect('../login.php');
}
?>
