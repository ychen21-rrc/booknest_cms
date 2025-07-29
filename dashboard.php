<?php
require 'includes/auth.php';
require 'includes/db.php';

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

$stmt = $pdo->query("SELECT r.id, r.title, r.created_at, u.username, c.name as category_name 
                     FROM reviews r 
                     JOIN users u ON r.user_id = u.id 
                     JOIN categories c ON r.category_id = c.id 
                     ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();

// Get counts for dashboard stats
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Admin Dashboard</h1>
        <div class="dashboard-actions">
            <a href="review_create.php" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle me-2"></i>Create New Review
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon bg-primary">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0"><?= $totalReviews ?></h3>
                            <p class="text-muted mb-0">Total Reviews</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon bg-success">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0"><?= $totalCategories ?></h3>
                            <p class="text-muted mb-0">Categories</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon bg-warning">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0"><?= $totalUsers ?></h3>
                            <p class="text-muted mb-0">Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon bg-info">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0"><?= $totalComments ?></h3>
                            <p class="text-muted mb-0">Comments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">Management Tools</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="category_manage.php" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-tags me-2"></i>Manage Categories
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="comment_moderate.php" class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-chat-dots me-2"></i>Moderate Comments
                            </a>
                        </div>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <div class="col-md-3">
                            <a href="user_manage.php" class="btn btn-outline-warning w-100 mb-2">
                                <i class="bi bi-people me-2"></i>Manage Users
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <a href="index.php" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="bi bi-house-door me-2"></i>View Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="dashboard-card">
        <div class="dashboard-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Recent Reviews</h5>
                <span class="badge bg-primary"><?= count($reviews) ?> Total</span>
            </div>
            
            <?php if (empty($reviews)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">No Reviews Yet</h4>
                    <p class="text-muted">Create your first book review to get started.</p>
                    <a href="review_create.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Review
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($r['title']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($r['category_name']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($r['username']) ?></td>
                                <td>
                                    <small class="text-muted"><?= date('M j, Y', strtotime($r['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="review.php?id=<?= $r['id'] ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="review_edit.php?id=<?= $r['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="review_delete.php?id=<?= $r['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this review?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
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

<?php include 'footer.php'; ?>