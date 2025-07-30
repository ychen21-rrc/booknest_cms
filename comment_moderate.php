<?php
require 'includes/auth.php';
require 'includes/db.php';

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

$userRole = $_SESSION['user']['role'];

if (isset($_GET['delete']) && $userRole === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Comment deleted successfully!";
}

$stmt = $pdo->query("SELECT comments.id, comments.commenter_name, comments.comment, comments.created_at, reviews.title 
                     FROM comments 
                     JOIN reviews ON comments.review_id = reviews.id 
                     ORDER BY comments.created_at DESC");
$comments = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Moderate Comments</h1>
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

    <?php if ($userRole !== 'admin'): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> You can view comments but only administrators can delete them.
        </div>
    <?php endif; ?>

    <!-- Comments Card -->
    <div class="dashboard-card">
        <div class="dashboard-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-dots me-2"></i>All Comments
                </h5>
                <span class="badge bg-primary"><?= count($comments) ?> Total</span>
            </div>

            <?php if (empty($comments)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots display-1 text-muted"></i>
                    <h4 class="mt-3">No Comments Yet</h4>
                    <p class="text-muted">Comments from readers will appear here for moderation.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Review</th>
                                <th>Commenter</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comments as $c): ?>
                            <tr>
                                <td>
                                    <div class="review-title-cell">
                                        <i class="bi bi-book me-2 text-muted"></i>
                                        <strong><?= htmlspecialchars($c['title']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="commenter-cell">
                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                        <?= htmlspecialchars($c['commenter_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="comment-content">
                                        <?= htmlspecialchars(substr($c['comment'], 0, 100)) ?>
                                        <?php if (strlen($c['comment']) > 100): ?>
                                            <span class="text-muted">...</span>
                                            <div class="full-comment d-none">
                                                <?= nl2br(htmlspecialchars($c['comment'])) ?>
                                            </div>
                                            <button class="btn btn-link btn-sm p-0 mt-1 show-more-btn" 
                                                    onclick="toggleComment(this)">
                                                Show More
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= date('M j, Y', strtotime($c['created_at'])) ?>
                                        <br>
                                        <i class="bi bi-clock me-1"></i>
                                        <?= date('g:i A', strtotime($c['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($userRole === 'admin'): ?>
                                        <a href="?delete=<?= $c['id'] ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this comment?');">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Admin only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-bar-chart me-2"></i>Comment Statistics
                    </h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-primary mb-0"><?= count($comments) ?></h3>
                                <small class="text-muted">Total Comments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <?php
                                $recentComments = array_filter($comments, function($c) {
                                    return strtotime($c['created_at']) > strtotime('-7 days');
                                });
                                ?>
                                <h3 class="text-success mb-0"><?= count($recentComments) ?></h3>
                                <small class="text-muted">This Week</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-shield-check me-2"></i>Moderation Guidelines
                    </h5>
                    <div class="small text-muted">
                        <p class="mb-2">• Remove spam or inappropriate content</p>
                        <p class="mb-2">• Delete comments with offensive language</p>
                        <p class="mb-0">• Keep constructive criticism and genuine reviews</p>
                        <?php if ($userRole !== 'admin'): ?>
                            <hr class="my-2">
                            <p class="mb-0 text-info">
                                <i class="bi bi-info-circle me-1"></i>
                                Contact an administrator to delete comments
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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

.review-title-cell, .commenter-cell {
    display: flex;
    align-items: center;
}

.comment-content {
    max-width: 300px;
    line-height: 1.4;
}

.show-more-btn {
    color: var(--coral);
    text-decoration: none;
    font-size: 0.875rem;
}

.show-more-btn:hover {
    color: #E66B6B;
    text-decoration: underline;
}

.badge {
    background-color: var(--coral) !important;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background-color: var(--light-gray);
    border-radius: 10px;
}

.stat-item h3 {
    font-size: 2rem;
    font-weight: bold;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>

<script>
function toggleComment(button) {
    const commentContent = button.closest('.comment-content');
    const fullComment = commentContent.querySelector('.full-comment');
    const truncatedText = commentContent.childNodes[0];
    
    if (fullComment.classList.contains('d-none')) {
        fullComment.classList.remove('d-none');
        truncatedText.style.display = 'none';
        button.textContent = 'Show Less';
    } else {
        fullComment.classList.add('d-none');
        truncatedText.style.display = 'inline';
        button.textContent = 'Show More';
    }
}
</script>

<?php include 'footer.php'; ?>