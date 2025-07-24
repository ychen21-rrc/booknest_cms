<?php include 'includes/header.php'; ?>


<?php
require 'includes/db.php';

$category_id = $_GET['category'] ?? null;

if ($category_id) {
    $stmt = $pdo->prepare("SELECT r.id, r.title, r.image_path, c.name AS category 
                           FROM reviews r 
                           JOIN categories c ON r.category_id = c.id 
                           WHERE c.id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("SELECT r.id, r.title, r.image_path, c.name AS category 
                         FROM reviews r 
                         JOIN categories c ON r.category_id = c.id 
                         ORDER BY r.created_at DESC");
}
$reviews = $stmt->fetchAll();
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<h1>BookNest CMS</h1>
<p>Categories:</p>
<ul>
    <li><a href="index.php">All</a></li>
    <?php foreach ($cats as $cat): ?>
        <li><a href="index.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
    <?php endforeach; ?>
</ul>

<?php foreach ($reviews as $r): ?>
    <div style="margin-bottom: 20px;">
        <h2><a href="review.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h2>
        <p><em>Category: <?= htmlspecialchars($r['category']) ?></em></p>
        <?php if ($r['image_path']): ?>
            <img src="<?= $r['image_path'] ?>" width="200">
        <?php endif; ?>
    </div>
<?php endforeach; ?>


<?php include 'includes/footer.php'; ?>