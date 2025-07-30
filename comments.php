<?php
require 'includes/db.php';
session_start();

$review_id = $_GET['id'] ?? 0;

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);
$currentUser = $isLoggedIn ? $_SESSION['user']['username'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    
    // Determine commenter name based on login status
    if ($isLoggedIn) {
        $name = $currentUser; // Use logged-in user's name
    } else {
        $name = $_POST['name'] ?? 'Anonymous'; // Allow visitors to set their name
    }
    
    // Simple captcha validation
    if ($comment && $review_id && isset($_SESSION['captcha']) && $_SESSION['captcha'] === $captcha) {
        $stmt = $pdo->prepare("INSERT INTO comments (review_id, commenter_name, comment) VALUES (?, ?, ?)");
        $stmt->execute([$review_id, $name, $comment]);
        $success = "Comment posted successfully!";
        unset($_SESSION['captcha']); // Clear captcha after use
    } elseif ($captcha !== $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA. Please try again.";
    }
}

// Fetch comments
$stmt = $pdo->prepare("SELECT commenter_name, comment, created_at FROM comments WHERE review_id = ? ORDER BY created_at DESC");
$stmt->execute([$review_id]);
$comments = $stmt->fetchAll();
?>

<div class="comments-section">
    <!-- Comment Form -->
    <div class="comment-form-card">
        <h4 class="comment-section-title">
            <i class="bi bi-chat-left-text me-2"></i>Leave a Comment
            <?php if ($isLoggedIn): ?>
                <span class="user-status-badge">
                    <i class="bi bi-person-check me-1"></i>Logged in as <?= htmlspecialchars($currentUser) ?>
                </span>
            <?php endif; ?>
        </h4>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="post" class="comment-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        <?= $isLoggedIn ? 'Your Name (Logged in)' : 'Your Name' ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person<?= $isLoggedIn ? '-check' : '' ?>"></i>
                        </span>
                        <?php if ($isLoggedIn): ?>
                            <!-- Logged-in users: Display name as readonly -->
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($currentUser) ?>"
                                   readonly
                                   style="background-color: #e9ecef;">
                        <?php else: ?>
                            <!-- Visitors: Allow name input -->
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Enter your name (optional)"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <div class="form-text text-success">
                            <i class="bi bi-shield-check me-1"></i>
                            Your username will be displayed automatically
                        </div>
                    <?php else: ?>
                        <div class="form-text">
                            Leave empty to post as "Anonymous" • 
                            <a href="login.php" class="text-decoration-none">Login</a> to use your account name
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="captcha" class="form-label">Security Check</label>
                    <div class="captcha-group">
                        <img src="captcha.php" alt="CAPTCHA" class="captcha-image">
                        <input type="text" 
                               class="form-control" 
                               id="captcha" 
                               name="captcha" 
                               placeholder="Enter code shown above" 
                               required>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <label for="comment" class="form-label">Your Comment</label>
                <textarea class="form-control" 
                          id="comment" 
                          name="comment" 
                          rows="4" 
                          placeholder="Share your thoughts about this book..." 
                          required><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
            </div>
            
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Post Comment
                </button>
                <?php if (!$isLoggedIn): ?>
                    <div class="login-prompt">
                        <small class="text-muted">
                            Want to comment as a registered user? 
                            <a href="login.php" class="text-decoration-none">Login here</a>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <?php if (!empty($comments)): ?>
        <div class="comments-divider">
            <span class="comments-count">
                <i class="bi bi-chat-dots me-2"></i><?= count($comments) ?> Comment<?= count($comments) != 1 ? 's' : '' ?>
            </span>
        </div>

        <div class="comments-list">
            <?php foreach ($comments as $c): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="commenter-info">
                            <div class="commenter-avatar">
                                <?php
                                // Check if this is a registered user comment by checking if name exists in users table
                                $userCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                                $userCheck->execute([$c['commenter_name']]);
                                $isRegisteredUser = $userCheck->fetch();
                                ?>
                                <i class="bi bi-person-<?= $isRegisteredUser ? 'check-fill' : 'circle' ?>"></i>
                            </div>
                            <div class="commenter-details">
                                <h6 class="commenter-name">
                                    <?= htmlspecialchars($c['commenter_name']) ?>
                                    <?php if ($isRegisteredUser): ?>
                                        <span class="verified-badge">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </span>
                                    <?php endif; ?>
                                </h6>
                                <small class="comment-date">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('M j, Y \a\t g:i A', strtotime($c['created_at'])) ?>
                                    <?php if ($isRegisteredUser): ?>
                                        <span class="ms-2 badge bg-success badge-sm">Verified User</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="comment-content">
                        <?= nl2br(htmlspecialchars($c['comment'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-comments">
            <div class="no-comments-icon">
                <i class="bi bi-chat-left-text"></i>
            </div>
            <h5>No Comments Yet</h5>
            <p class="text-muted">Be the first to share your thoughts about this book!</p>
            <?php if (!$isLoggedIn): ?>
                <div class="mt-3">
                    <a href="login.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login to Comment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.comments-section {
    background-color: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 2rem;
}

.comment-form-card {
    background-color: var(--light-gray);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.comment-section-title {
    color: var(--dark-blue);
    font-weight: bold;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.user-status-badge {
    background-color: var(--coral);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
}

.comment-form .form-label {
    color: var(--dark-blue);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background-color: white;
    border-color: #dee2e6;
}

.form-control:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 0.2rem rgba(255, 123, 123, 0.25);
}

.form-control[readonly] {
    background-color: #e9ecef !important;
    border-color: #ced4da;
}

.captcha-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.captcha-image {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    background-color: white;
}

.btn-primary {
    background-color: var(--coral);
    border-color: var(--coral);
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-primary:hover {
    background-color: #E66B6B;
    border-color: #E66B6B;
}

.login-prompt {
    text-align: right;
}

.comments-divider {
    text-align: center;
    margin: 2rem 0;
    position: relative;
}

.comments-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #dee2e6;
    z-index: 1;
}

.comments-count {
    background-color: white;
    color: var(--dark-blue);
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 2px solid #dee2e6;
    position: relative;
    z-index: 2;
    display: inline-flex;
    align-items: center;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.comment-item {
    background-color: var(--light-gray);
    border-radius: 12px;
    padding: 1.5rem;
    border-left: 4px solid var(--coral);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.comment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.comment-header {
    margin-bottom: 1rem;
}

.commenter-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.commenter-avatar {
    font-size: 2rem;
    color: var(--coral);
}

.commenter-name {
    color: var(--dark-blue);
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.verified-badge {
    color: #28a745;
    font-size: 1rem;
}

.comment-date {
    color: #6c757d;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
}

.comment-content {
    color: #333;
    line-height: 1.6;
    font-size: 0.95rem;
}

.no-comments {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.no-comments-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.no-comments h5 {
    color: var(--dark-blue);
    margin-bottom: 0.5rem;
}

.alert {
    border-radius: 10px;
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.form-text {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-text.text-success {
    color: #28a745 !important;
}

@media (max-width: 768px) {
    .comments-section {
        padding: 1rem;
    }
    
    .comment-form-card {
        padding: 1rem;
    }
    
    .comment-section-title {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .captcha-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .commenter-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .commenter-avatar {
        font-size: 1.5rem;
    }
    
    .login-prompt {
        text-align: left;
        margin-top: 1rem;
    }
    
    .mt-3.d-flex {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>