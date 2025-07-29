
<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_user']) && !empty($_POST['new_pass']) && !empty($_POST['role'])) {
        $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['new_user'], $hashed, $_POST['role']]);
    }

    if (!empty($_POST['update_id']) && !empty($_POST['update_role'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$_POST['update_role'], $_POST['update_id']]);
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h2>Manage Users</h2>

<form method="post">
    Username: <input name="new_user" required>
    Password: <input name="new_pass" required>
    Role:
    <select name="role">
        <option value="editor">Editor</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Add User</button>
</form>

<h3>Existing Users</h3>
<table border="1">
<tr><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td>
        <form method="post" style="display:inline">
            <input type="hidden" name="update_id" value="<?= $u['id'] ?>">
            <select name="update_role">
                <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </td>
    <td><?= $u['created_at'] ?></td>
    <td>
        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
