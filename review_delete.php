<?php
require 'includes/auth.php';
require 'includes/db.php';

// Only admin can delete reviews
requireAdmin();

$id = $_GET['id'] ?? 0;

// Verify the review exists before deleting
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error_message'] = "Review not found.";
    header("Location: dashboard.php");
    exit;
}

// Delete the review (comments will be deleted automatically due to foreign key constraints)
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['success_message'] = "Review deleted successfully!";
header("Location: dashboard.php");
exit;
?>