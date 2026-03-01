<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['member_id'])) {
    $member_id = intval($_POST['member_id']);
    
    // Delete member (cascade will delete related records)
    $query = "DELETE FROM members WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    
    if (mysqli_stmt_execute($stmt)) {
        redirect('../manage-member.php?success=Member deleted successfully');
    } else {
        redirect('../manage-member.php?error=Failed to delete member');
    }
} else {
    redirect('../manage-member.php');
}
?>
