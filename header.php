<?php
session_start();

// Check if user is logged in and get their role
$isLoggedIn = isset($_SESSION['user']);
$userRole = $isLoggedIn ? $_SESSION['user']['role'] : 'visitor';
$userName = $isLoggedIn ? $_SESSION['user']['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">📚 BookNest</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <!-- Home - Always visible -->
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>

                    <!-- Contact - Always visible -->
                    <a class="nav-link" href="contact.php">
                        <i class="bi bi-envelope me-1"></i>Contact
                    </a>
                    
                    <?php if ($isLoggedIn): ?>
                        <!-- User Dropdown for logged in users -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($userName) ?>
                                <span class="badge bg-secondary ms-1"><?= ucfirst($userRole) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><h6 class="dropdown-header">
                                    <i class="bi bi-shield-check me-1"></i>
                                    <?= ucfirst($userRole) ?> Account
                                </h6></li>
                                <?php if ($userRole === 'admin' || $userRole === 'editor'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="dashboard.php">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a></li>
                                <?php endif; ?>
                                
                                <?php if ($userRole === 'admin'): ?>
                                    <li><a class="dropdown-item" href="user_manage.php">
                                        <i class="bi bi-people me-2"></i>Manage Users
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Login button for visitors -->
                        <a class="btn btn-outline-primary" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">