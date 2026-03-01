<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement_id'])) {
    $announcement_id = intval($_POST['announcement_id']);
    
    $query = "DELETE FROM announcements WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $announcement_id);
    
    if (mysqli_stmt_execute($stmt)) {
        redirect('../announcement.php?success=Announcement deleted successfully');
    } else {
        redirect('../announcement.php?error=Failed to delete announcement');
    }
} else {
    redirect('../announcement.php');
}
?>
