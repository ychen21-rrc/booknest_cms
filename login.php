<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
        $_SESSION['user_name'] = $user['username']; // Set for header display
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-card">
                <div class="login-card-body">
                    <div class="text-center mb-4">
                        <h2 class="login-title">Welcome Back</h2>
                        <p class="text-muted">Sign in to your BookNest account</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 login-btn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? 
                            <a href="register.php" class="text-primary text-decoration-none">Create one here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 2rem;
}

.login-card-body {
    padding: 2.5rem;
}

.login-title {
    color: var(--dark-blue);
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background-color: var(--light-gray);
    border-color: #dee2e6;
}

.form-control:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 0.2rem rgba(255, 123, 123, 0.25);
}

.login-btn {
    background-color: var(--coral);
    border-color: var(--coral);
    padding: 0.75rem;
    font-weight: bold;
}

.login-btn:hover {
    background-color: #E66B6B;
    border-color: #E66B6B;
}
</style>

<?php include 'footer.php'; ?>