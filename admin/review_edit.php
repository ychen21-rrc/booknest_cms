<?php

function resizeImage($src, $dest, $maxWidth = 600) {
    list($width, $height, $type) = getimagesize($src);
    $newWidth = $maxWidth;
    $newHeight = floor($height * ($maxWidth / $width));

    $srcImg = null;
    switch ($type) {
        case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($src); break;
        case IMAGETYPE_PNG: $srcImg = imagecreatefrompng($src); break;
        case IMAGETYPE_GIF: $srcImg = imagecreatefromgif($src); break;
        default: return false;
    }

    $dstImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG: imagejpeg($dstImg, $dest); break;
        case IMAGETYPE_PNG: imagepng($dstImg, $dest); break;
        case IMAGETYPE_GIF: imagegif($dstImg, $dest); break;
    }

    imagedestroy($srcImg);
    imagedestroy($dstImg);
    return true;
}

?>p
require '../includes/auth.php';
require '../includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch();

$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? null;

    $image_path = $review['image_path'];
    if (!empty($_FILES['image']['name'])) {
        $tmp = $_FILES['image']['tmp_name'];
        $imgName = basename($_FILES['image']['name']);
        $dest = "../uploads/images/" . $imgName;
        if (getimagesize($tmp)) {
            if (move_uploaded_file($tmp, $dest)) resizeImage($dest, $dest);
            $image_path = "uploads/images/" . $imgName;
        }
    }

    $stmt = $pdo->prepare("UPDATE reviews SET title=?, content=?, image_path=?, category_id=? WHERE id=?");
    $stmt->execute([$title, $content, $image_path, $category_id, $id]);
    header("Location: dashboard.php");
    exit;
}
?>
<h2>Edit Review</h2>
<form method="post" enctype="multipart/form-data">
    Title: <input name="title" value="<?= htmlspecialchars($review['title']) ?>" required><br>
    Content:<br><textarea name="content" required><?= htmlspecialchars($review['content']) ?></textarea><br>
    Category:
    <select name="category_id">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $review['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    Cover Image: <input type="file" name="image"><br>
    <button type="submit">Update</button>
</form>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'link image code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | code'
  });
</script>

