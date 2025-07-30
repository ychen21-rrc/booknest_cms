<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Function to check if user has required role
function requireRole($requiredRole) {
    if ($_SESSION['user']['role'] !== $requiredRole) {
        // Redirect to dashboard with error message
        $_SESSION['error_message'] = "Access denied. You don't have permission to access this page.";
        header("Location: dashboard.php");
        exit;
    }
}

// Function to check if user has admin role
function requireAdmin() {
    requireRole('admin');
}
?>