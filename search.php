
<?php
require 'includes/db.php';

$keyword = $_GET['q'] ?? '';
$category_id = $_GET['category'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

$params = [];
$where = "WHERE 1=1";

if ($keyword) {
    $where .= " AND r.title LIKE ?";
    $params[] = "%" . $keyword . "%";
}
if ($category_id) {
    $where .= " AND r.category_id = ?";
    $params[] = $category_id;
}

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews r $where");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("SELECT r.id, r.title, c.name AS category 
                       FROM reviews r 
                       JOIN categories c ON r.category_id = c.id 
                       $where 
                       ORDER BY r.created_at DESC 
                       LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$results = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<h1>Search Reviews</h1>
<form method="get">
    Keyword: <input name="q" value="<?= htmlspecialchars($keyword) ?>">
    Category:
    <select name="category">
        <option value="">-- All --</option>
        <?php foreach ($cats as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
</form>

<?php foreach ($results as $r): ?>
    <div>
        <h2><a href="review.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h2>
        <p><em>Category: <?= htmlspecialchars($r['category']) ?></em></p>
    </div>
<?php endforeach; ?>

<?php if ($totalPages > 1): ?>
    <p>Pages:
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?q=<?= urlencode($keyword) ?>&category=<?= $category_id ?>&page=<?= $i ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
    </p>
<?php endif; ?>
