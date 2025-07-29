<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
            $stmt->execute([$username, $hashed]);
            $success = "Account created successfully! Please login.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="register-card">
                <div class="register-card-body">
                    <div class="text-center mb-4">
                        <h2 class="register-title">Join BookNest</h2>
                        <p class="text-muted">Create your account to start reviewing books</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?= $success ?>
                            <div class="mt-2">
                                <a href="login.php" class="btn btn-success btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login Now
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($success)): ?>
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

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control" id="confirm" name="confirm" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 register-btn">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? 
                            <a href="login.php" class="text-primary text-decoration-none">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.register-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 2rem;
}

.register-card-body {
    padding: 2.5rem;
}

.register-title {
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

.register-btn {
    background-color: var(--coral);
    border-color: var(--coral);
    padding: 0.75rem;
    font-weight: bold;
}

.register-btn:hover {
    background-color: #E66B6B;
    border-color: #E66B6B;
}
</style>

<?php include 'footer.php'; ?>