
<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_category'])) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$_POST['new_category']]);
    }
    if (!empty($_POST['update_id']) && !empty($_POST['update_name'])) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$_POST['update_name'], $_POST['update_id']]);
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<h2>Manage Categories</h2>

<form method="post">
    <input name="new_category" placeholder="New category name" required>
    <button type="submit">Add Category</button>
</form>

<h3>Existing Categories</h3>
<table border="1">
<tr><th>Name</th><th>Actions</th></tr>
<?php foreach ($categories as $cat): ?>
<tr>
    <td>
        <form method="post" style="display:inline">
            <input type="hidden" name="update_id" value="<?= $cat['id'] ?>">
            <input name="update_name" value="<?= htmlspecialchars($cat['name']) ?>" required>
            <button type="submit">Update</button>
        </form>
    </td>
    <td>
        <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('Delete this category?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
