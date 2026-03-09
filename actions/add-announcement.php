<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($_POST['title']);
    $message = clean_input($_POST['message']);
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $priority = clean_input($_POST['priority']);
    
    $query = "INSERT INTO announcements (title, message, date_from, date_to, priority) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $title, $message, $date_from, $date_to, $priority);
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            redirect('../announcement.php?success=Announcement created successfully');
        } else {
            // Get the MySQL error message (includes trigger errors)
            $error_message = mysqli_error($conn);
            redirect('../announcement.php?error=' . urlencode($error_message));
        }
    } catch (mysqli_sql_exception $e) {
        // Catch trigger errors thrown as exceptions
        redirect('../announcement.php?error=' . urlencode($e->getMessage()));
    }
} else {
    redirect('../announcement.php');
}
?>
