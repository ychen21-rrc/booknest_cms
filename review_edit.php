<?php

require 'vendor/autoload.php';
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

function resizeImage($src, $dest, $maxWidth = 600) {
    try {
        // Create ImageManager instance with GD driver
        $manager = new ImageManager(new Driver());
        
        // Read the image
        $image = $manager->read($src);
        
        // Get original dimensions
        $originalWidth = $image->width();
        $originalHeight = $image->height();
        
        // Resize if width exceeds maximum
        if ($originalWidth > $maxWidth) {
            $image->scale(width: $maxWidth);
        }
        
        // Save the image with auto-format detection
        $image->save($dest);
        
        return true;
    } catch (Exception $e) {
        error_log("Image resize error: " . $e->getMessage());
        return false;
    }
}

require 'includes/auth.php';
require 'includes/db.php';

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch();

if (!$review) {
    $_SESSION['error_message'] = "Review not found.";
    header("Location: dashboard.php");
    exit;
}

// Check if user owns this review or is admin
if ($review['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['error_message'] = "Access denied. You can only edit your own reviews.";
    header("Location: dashboard.php");
    exit;
}

$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? null;

    $image_path = $review['image_path'];
    $deleteOldImage = false;
    
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'];
        $originalName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedTypes)) {
            // Generate unique filename
            $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
            $dest = "uploads/images/" . $uniqueName;
            
            // Ensure upload directory exists
            if (!is_dir('uploads/images/')) {
                mkdir('uploads/images/', 0755, true);
            }
            
            // Move file and resize
            if (move_uploaded_file($tmp, $dest)) {
                if (resizeImage($dest, $dest)) {
                    $deleteOldImage = true;
                    $image_path = $dest;
                } else {
                    // If resize fails, remove the file
                    unlink($dest);
                    $_SESSION['error_message'] = "Failed to process the uploaded image.";
                }
            } else {
                $_SESSION['error_message'] = "Failed to upload the image file.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
        }
    }

    // Only proceed if no errors occurred
    if (!isset($_SESSION['error_message'])) {
        $stmt = $pdo->prepare("UPDATE reviews SET title=?, content=?, image_path=?, category_id=?, updated_at=NOW() WHERE id=?");
        $result = $stmt->execute([$title, $content, $image_path, $category_id, $id]);
        
        if ($result) {
            // Delete old image if a new one was uploaded
            if ($deleteOldImage && $review['image_path'] && file_exists($review['image_path'])) {
                unlink($review['image_path']);
            }
            
            $_SESSION['success_message'] = "Review updated successfully!";
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Failed to update review. Please try again.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Edit Review</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-pencil-square text-primary me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="card-title mb-0">Edit Review Details</h5>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="editForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-book me-2"></i>Review Title
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?= htmlspecialchars($review['title']) ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">
                                <i class="bi bi-tags me-2"></i>Category
                            </label>
                            <select class="form-select" name="category_id" id="category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $review['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="bi bi-image me-2"></i>Cover Image
                            </label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image. Supported: JPG, PNG, GIF, WebP (Max 5MB)</div>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="bi bi-card-text me-2"></i>Review Content
                            </label>
                            <textarea name="content" id="content" class="form-control" rows="10"><?= htmlspecialchars($review['content']) ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Review
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-eye me-2"></i>Current Image
                    </h5>
                    <?php if ($review['image_path']): ?>
                        <img src="<?= htmlspecialchars($review['image_path']) ?>" 
                             class="img-fluid rounded preview-image" 
                             alt="Current book cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="preview-placeholder" style="display: none;">
                            <i class="bi bi-image-fill display-4 text-muted"></i>
                            <p class="text-muted mt-2">Image not found</p>
                        </div>
                    <?php else: ?>
                        <div class="preview-placeholder">
                            <i class="bi bi-image display-4 text-muted"></i>
                            <p class="text-muted mt-2">No image uploaded</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <h6 class="text-muted">Review Info</h6>
                        <div class="small text-muted">
                            <div class="mb-1">
                                <i class="bi bi-calendar3 me-1"></i>
                                Created: <?= date('M j, Y', strtotime($review['created_at'])) ?>
                            </div>
                            <div>
                                <i class="bi bi-clock me-1"></i>
                                Last Updated: <?= date('M j, Y g:i A', strtotime($review['updated_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Upload Tips -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-info-circle me-2"></i>Image Guidelines
                    </h5>
                    <div class="small text-muted">
                        <p class="mb-2">• Recommended size: 600x400 pixels or similar ratio</p>
                        <p class="mb-2">• Supported formats: JPG, PNG, GIF, WebP</p>
                        <p class="mb-2">• Maximum file size: 5MB</p>
                        <p class="mb-0">• Images will be automatically resized to 600px width</p>
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

.preview-image {
    max-height: 300px;
    object-fit: cover;
    border: 2px solid #f0f0f0;
}

.preview-placeholder {
    height: 200px;
    background-color: var(--light-gray);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px dashed #dee2e6;
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
    const form = document.getElementById('editForm');
    const imageInput = document.getElementById('image');
    
    // File size validation
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File size must be less than 5MB.');
                e.target.value = '';
                return;
            }
        }
    });
    
    form.addEventListener("submit", function (e) {
        const content = tinymce.get('content').getContent({ format: 'text' }).trim();
        if (!content) {
            alert("Content cannot be empty.");
            e.preventDefault();
        }
    });
});
</script>

<?php include 'footer.php'; ?>