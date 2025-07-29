<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_user']) && !empty($_POST['new_pass']) && !empty($_POST['role'])) {
        $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['new_user'], $hashed, $_POST['role']]);
        $success = "User created successfully!";
    }

    if (!empty($_POST['update_id']) && !empty($_POST['update_role'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$_POST['update_role'], $_POST['update_id']]);
        $success = "User role updated successfully!";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "User deleted successfully!";
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Manage Users</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Add New User Card -->
    <div class="dashboard-card mb-4">
        <div class="dashboard-card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-person-plus me-2"></i>Add New User
            </h5>
            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <label for="new_user" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" id="new_user" name="new_user" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="new_pass" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="new_pass" name="new_pass" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" name="role" id="role">
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="dashboard-card">
        <div class="dashboard-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Existing Users
                </h5>
                <span class="badge bg-primary"><?= count($users) ?> Total</span>
            </div>

            <?php if (empty($users)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h4 class="mt-3">No Users Found</h4>
                    <p class="text-muted">Create your first user to get started.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                        <strong><?= htmlspecialchars($u['username']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <form method="post" class="d-flex align-items-center">
                                        <input type="hidden" name="update_id" value="<?= $u['id'] ?>">
                                        <select name="update_role" class="form-select form-select-sm me-2" style="width: auto;">
                                            <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= date('M j, Y', strtotime($u['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="?delete=<?= $u['id'] ?>" 
                                       class="btn btn-outline-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.dashboard-card-body {
    padding: 1.5rem;
}

.card-title {
    color: var(--dark-blue);
    font-weight: bold;
    display: flex;
    align-items: center;
}

.input-group-text {
    background-color: var(--light-gray);
    border-color: #dee2e6;
}

.form-control:focus, .form-select:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 0.2rem rgba(255, 123, 123, 0.25);
}

.btn-primary {
    background-color: var(--coral);
    border-color: var(--coral);
}

.btn-primary:hover {
    background-color: #E66B6B;
    border-color: #E66B6B;
}

.table th {
    background-color: var(--light-gray);
    color: var(--dark-blue);
    font-weight: 600;
    border: none;
}

.table td {
    vertical-align: middle;
    border-color: #f0f0f0;
}

.badge {
    background-color: var(--coral) !important;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>

<?php include 'footer.php'; ?>