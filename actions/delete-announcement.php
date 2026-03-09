<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement_id'])) {
    $announcement_id = intval($_POST['announcement_id']);
    
    $query = "DELETE FROM announcements WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $announcement_id);
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            redirect('../announcement.php?success=Announcement deleted successfully');
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
