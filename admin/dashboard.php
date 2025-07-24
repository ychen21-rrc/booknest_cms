
<?php include '../includes/header.php'; ?>

<?php
require '../includes/auth.php';
require '../includes/db.php';

$stmt = $pdo->query("SELECT r.id, r.title, r.created_at, u.username FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();
?>

<div style="display: flex; flex-direction: row; gap: 30px; align-items: center;">
    <h2>Admin Dashboard</h2>
    </p><a href="../logout.php">Logout</a></p>
</div>
<p><a href="review_create.php">+ Create New Review</a></p>
<table border="1">
<tr><th>Title</th><th>Author</th><th>Created</th><th>Actions</th></tr>
<?php foreach ($reviews as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['title']) ?></td>
    <td><?= htmlspecialchars($r['username']) ?></td>
    <td><?= $r['created_at'] ?></td>
    <td>
        <a href="review_edit.php?id=<?= $r['id'] ?>">Edit</a> | 
        <a href="review_delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete review?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php include '../includes/footer.php'; ?>