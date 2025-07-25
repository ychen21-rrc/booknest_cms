

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
<h1><?= htmlspecialchars($r['title']) ?></h1>
<p><strong>Author:</strong> <?= htmlspecialchars($r['username']) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($r['category']) ?></p>
<p><strong>Posted:</strong> <?= $r['created_at'] ?></p>
<?php if ($r['image_path']): ?>
    <img src="<?= $r['image_path'] ?>" width="300"><br><br>
<?php endif; ?>
<div class="review-content">
  <?= $r['content'] ?>
</div>
<div style="margin: 20px 0; border-top: 1px solid #ccc;"></div>

<?php include 'comments.php'; ?>


