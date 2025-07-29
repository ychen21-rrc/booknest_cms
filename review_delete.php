
<?php
require 'includes/auth.php';
require 'includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->execute([$id]);
header("Location: dashboard.php");
exit;
?>
