
<?php
require 'includes/db.php';
session_start();

$review_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Anonymous';
    $comment = $_POST['comment'] ?? '';
    if ($comment && $review_id) {
        $stmt = $pdo->prepare("INSERT INTO comments (review_id, commenter_name, comment) VALUES (?, ?, ?)");
        $stmt->execute([$review_id, $name, $comment]);
    }
}

// Fetch comments
$stmt = $pdo->prepare("SELECT commenter_name, comment, created_at FROM comments WHERE review_id = ? ORDER BY created_at DESC");
$stmt->execute([$review_id]);
$comments = $stmt->fetchAll();
?>

<h3>Leave a Comment</h3>
<form method="post">
    Name: <input name="name"><br>
    Comment:<br>
    <textarea name="comment" required></textarea><br>
    <img src="captcha.php" alt="CAPTCHA"><br>
    Enter CAPTCHA: <input name="captcha" required><br>
    <button type="submit">Post Comment</button>
</form>
<!--add line break and gray line div -->
<!--if comments size is greater than 0 ,show below content -->
<div style="margin: 20px 0; border-top: 1px solid #ccc;"></div>
<h3>Comments</h3>
<?php foreach ($comments as $c): ?>
    <p><strong><?= htmlspecialchars($c['commenter_name']) ?></strong> (<?= $c['created_at'] ?>)</p>
    <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
    <hr>
<?php endforeach; ?>
