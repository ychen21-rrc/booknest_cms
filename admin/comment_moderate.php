
<?php
require '../includes/auth.php';
require '../includes/db.php';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

$stmt = $pdo->query("SELECT comments.id, commenter_name, comment, reviews.title 
                     FROM comments 
                     JOIN reviews ON comments.review_id = reviews.id 
                     ORDER BY comments.created_at DESC");
$comments = $stmt->fetchAll();
?>
<h2>Moderate Comments</h2>
<table border="1">
<tr><th>Review</th><th>Name</th><th>Comment</th><th>Action</th></tr>
<?php foreach ($comments as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['title']) ?></td>
    <td><?= htmlspecialchars($c['commenter_name']) ?></td>
    <td><?= htmlspecialchars($c['comment']) ?></td>
    <td><a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Delete this comment?');">Delete</a></td>
</tr>
<?php endforeach; ?>
</table>
