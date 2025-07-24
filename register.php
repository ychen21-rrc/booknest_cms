<?php include 'includes/header.php'; ?>

<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
            $stmt->execute([$username, $hashed]);
            header("Location: login.php");
            exit;
        }
    }
}
?>
<form method="post">
    Username: <input name="username" required><br>
    Password: <input name="password" type="password" required><br>
    Confirm Password: <input name="confirm" type="password" required><br>
    <button type="submit">Register</button>
</form>
<?php if (!empty($error)) echo "<p>$error</p>"; ?>

<?php include 'includes/footer.php'; ?>
