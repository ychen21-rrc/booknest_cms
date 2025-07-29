<?php
require 'includes/db.php';
$review_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT r.*, c.name AS category, u.username 
                       FROM reviews r 
                       JOIN categories c ON r.category_id = c.id 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.id = ?");
$stmt->execute([$review_id]);
$r = $stmt->fetch();
if (!$r) {
    echo "Review not found.";
    exit;
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Books
        </a>
    </div>

    <div class="row">
        <!-- Book Image -->
        <div class="col-md-4">
            <div class="review-image-container">
                <?php if ($r['image_path']): ?>
                    <img src="<?= htmlspecialchars($r['image_path']) ?>" class="review-image" alt="Book Cover">
                <?php else: ?>
                    <div class="review-image-placeholder">
                        <i class="bi bi-book display-1 text-muted"></i>
                        <p class="text-muted mt-2">No Cover Image</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Book Details -->
        <div class="col-md-8">
            <div class="review-details">
                <div class="review-meta mb-3">
                    <span class="badge bg-primary category-badge"><?= htmlspecialchars($r['category']) ?></span>
                </div>
                
                <h1 class="review-title"><?= htmlspecialchars($r['title']) ?></h1>
                
                <div class="review-info mb-4">
                    <div class="info-item">
                        <i class="bi bi-person-circle text-muted me-2"></i>
                        <strong>Reviewed by:</strong> <?= htmlspecialchars($r['username']) ?>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-calendar-event text-muted me-2"></i>
                        <strong>Published:</strong> <?= date('F j, Y', strtotime($r['created_at'])) ?>
                    </div>
                </div>

                <!-- Review Content -->
                <div class="review-content">
                    <h4 class="mb-3">Review</h4>
                    <div class="content-text">
                        <?= $r['content'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section mt-5">
        <hr class="my-4">
        <?php 
        // Include comments with the review ID
        $_GET['id'] = $review_id;
        include 'comments.php'; 
        ?>
    </div>
</div>

<style>
.review-image-container {
    position: sticky;
    top: 20px;
}

.review-image {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.review-image-placeholder {
    width: 100%;
    max-width: 300px;
    height: 400px;
    background-color: var(--light-gray);
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px dashed #dee2e6;
}

.review-details {
    padding-left: 1rem;
}

.category-badge {
    background-color: var(--coral) !important;
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.review-title {
    color: var(--dark-blue);
    font-weight: bold;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.review-info {
    background-color: var(--light-gray);
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid var(--coral);
}

.info-item {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.info-item:last-child {
    margin-bottom: 0;
}

.review-content h4 {
    color: var(--dark-blue);
    border-bottom: 2px solid var(--coral);
    padding-bottom: 0.5rem;
}

.content-text {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #333;
}

.content-text p {
    margin-bottom: 1rem;
}

.comments-section {
    background-color: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .review-details {
        padding-left: 0;
        margin-top: 2rem;
    }
    
    .review-image-container {
        position: relative;
        text-align: center;
    }
}
</style>

<?php include 'footer.php'; ?>