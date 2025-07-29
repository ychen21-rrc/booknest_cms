<?php
require 'includes/db.php';
require 'header.php';

$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();
?>

<div class="container my-4">
<h2 class="fw-bold mb-3">BookNest CMS</h2>

<ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
    <li class="nav-item" role="presentation">
    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
    </li>
    <?php foreach ($categories as $cat): ?>
    <li class="nav-item" role="presentation">
    <button class="nav-link" id="cat-<?= $cat['id'] ?>-tab" data-bs-toggle="tab" data-bs-target="#cat-<?= $cat['id'] ?>" type="button" role="tab">
        <?= htmlspecialchars($cat['name']) ?>
    </button>
    </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content" id="categoryTabsContent">
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
    <?php
    $stmt = $pdo->query("SELECT r.*, c.name AS category FROM reviews r JOIN categories c ON r.category_id = c.id ORDER BY r.created_at DESC");
    while ($r = $stmt->fetch()):
    ?>
        <div class="mb-3">
        <h5>
            <a href="review.php?id=<?= $r['id'] ?>">
            <?= htmlspecialchars($r['title']) ?>
            </a>
            <small class="text-muted">(<?= htmlspecialchars($r['category']) ?>)</small>
        </h5>
        </div>
    <?php endwhile; ?>
    </div>

    <?php foreach ($categories as $cat): ?>
    <div class="tab-pane fade" id="cat-<?= $cat['id'] ?>" role="tabpanel" aria-labelledby="cat-<?= $cat['id'] ?>-tab">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE category_id = ? ORDER BY created_at DESC");
    $stmt->execute([$cat['id']]);
    while ($r = $stmt->fetch()):
    ?>
        <div class="mb-3">
        <h5>
            <a href="review.php?id=<?= $r['id'] ?>">
            <?= htmlspecialchars($r['title']) ?>
            </a>
        </h5>
        </div>
    <?php endwhile; ?>
    </div>
    <?php endforeach; ?>
</div>
</div>
<?php require 'footer.php'; ?>
