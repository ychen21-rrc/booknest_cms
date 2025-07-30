<?php
require 'includes/auth.php';
require 'includes/db.php';

// Only admin can delete reviews
if ($_SESSION['user']['role'] !== 'admin') {
    $_SESSION['error_message'] = "Access denied. Only administrators can delete reviews.";
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id) {
    $_SESSION['error_message'] = "Invalid review ID.";
    header("Location: dashboard.php");
    exit;
}

// Verify the review exists before deleting
$stmt = $pdo->prepare("SELECT id, title, image_path FROM reviews WHERE id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch();

if (!$review) {
    $_SESSION['error_message'] = "Review not found.";
    header("Location: dashboard.php");
    exit;
}

try {
    // Start a transaction to ensure data integrity
    $pdo->beginTransaction();
    
    // First, delete all comments associated with this review
    $stmt = $pdo->prepare("DELETE FROM comments WHERE review_id = ?");
    $stmt->execute([$id]);
    
    // Then delete the review itself
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    // Delete the associated image file if it exists
    if ($review['image_path'] && file_exists($review['image_path'])) {
        unlink($review['image_path']);
    }
    
    // Commit the transaction
    $pdo->commit();
    
    if ($result) {
        $_SESSION['success_message'] = "Review '" . htmlspecialchars($review['title']) . "' and all associated comments deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete review. Please try again.";
    }
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollback();
    $_SESSION['error_message'] = "Error deleting review: " . $e->getMessage();
}

header("Location: dashboard.php");
exit;
?>