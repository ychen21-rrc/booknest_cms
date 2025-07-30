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

require 'includes/auth.php';
require 'includes/db.php';

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $user_id = $_SESSION['user']['id'];

    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $tmp = $_FILES['image']['tmp_name'];
        $imgName = basename($_FILES['image']['name']);
        $dest = "uploads/images/" . $imgName;
        if (getimagesize($tmp)) {
            if (move_uploaded_file($tmp, $dest)) resizeImage($dest, $dest);
            $image_path = "uploads/images/" . $imgName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO reviews (title, content, image_path, category_id, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $image_path, $category_id, $user_id]);
    header("Location: dashboard.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Create New Review</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Create Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-plus-circle text-primary me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="card-title mb-0">Book Review Details</h5>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-book me-2"></i>Review Title
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   placeholder="Enter the book title or review title" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">
                                <i class="bi bi-tags me-2"></i>Category
                            </label>
                            <select class="form-select" name="category_id" id="category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="bi bi-image me-2"></i>Cover Image
                            </label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload a book cover image (JPG, PNG, or GIF)</div>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="bi bi-card-text me-2"></i>Review Content
                            </label>
                            <textarea name="content" id="content" class="form-control" rows="10" placeholder="Write your book review here..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Publish Review
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-lightbulb me-2"></i>Writing Tips
                    </h5>
                    <div class="tips-list">
                        <div class="tip-item">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <div>
                                <strong>Be Descriptive</strong>
                                <p class="small text-muted mb-2">Include details about plot, characters, and your personal experience</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <div>
                                <strong>Avoid Spoilers</strong>
                                <p class="small text-muted mb-2">Keep major plot points hidden to preserve the reading experience</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <div>
                                <strong>Share Your Opinion</strong>
                                <p class="small text-muted mb-2">Explain what you liked or disliked and why</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <div>
                                <strong>Rate Appropriately</strong>
                                <p class="small text-muted mb-0">Consider the book's genre and target audience</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-info-circle me-2"></i>Image Guidelines
                    </h5>
                    <div class="small text-muted">
                        <p class="mb-2">• Recommended size: 600x400 pixels or similar ratio</p>
                        <p class="mb-2">• Supported formats: JPG, PNG, GIF</p>
                        <p class="mb-2">• Maximum file size: 5MB</p>
                        <p class="mb-0">• Use high-quality book cover images when possible</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.dashboard-card-body {
    padding: 1.5rem;
}

.card-title {
    color: var(--dark-blue);
    font-weight: bold;
    display: flex;
    align-items: center;
}

.form-label {
    color: var(--dark-blue);
    font-weight: 600;
    display: flex;
    align-items: center;
}

.form-control:focus, .form-select:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 0.2rem rgba(255, 123, 123, 0.25);
}

.btn-primary {
    background-color: var(--coral);
    border-color: var(--coral);
}

.btn-primary:hover {
    background-color: #E66B6B;
    border-color: #E66B6B;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    padding: 0.75rem;
    background-color: var(--light-gray);
    border-radius: 8px;
}

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
}
</style>

<script src="https://cdn.tiny.cloud/1/inec7l6556ljszce65ato9jdui6eq0hnk0fwvef4rxb498sh/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
tinymce.init({
    selector: '#content',
    plugins: 'link image code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | code',
    height: 300,
    menubar: false,
    statusbar: false
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (e) {
        const content = tinymce.get('content').getContent({ format: 'text' }).trim();
        if (!content) {
            alert("Content cannot be empty.");
            e.preventDefault();
        }
    });
});
</script>

<?php include 'footer.php'; ?>