<?php
require 'includes/auth.php';
require 'includes/db.php';

// Set user name in session for header display
if (isset($_SESSION['user'])) {
    $_SESSION['user_name'] = $_SESSION['user']['username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_category'])) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$_POST['new_category']]);
        $success = "Category created successfully!";
    }
    if (!empty($_POST['update_id']) && !empty($_POST['update_name'])) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$_POST['update_name'], $_POST['update_id']]);
        $success = "Category updated successfully!";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Category deleted successfully!";
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Manage Categories</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Add New Category Card -->
    <div class="dashboard-card mb-4">
        <div class="dashboard-card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-plus-circle me-2"></i>Add New Category
            </h5>
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <label for="new_category" class="form-label">Category Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-tag"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="new_category" 
                               name="new_category" 
                               placeholder="Enter category name (e.g., Fiction, Mystery, Romance)" 
                               required>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories List Card -->
    <div class="dashboard-card">
        <div class="dashboard-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tags me-2"></i>Existing Categories
                </h5>
                <span class="badge bg-primary"><?= count($categories) ?> Total</span>
            </div>

            <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-tags display-1 text-muted"></i>
                    <h4 class="mt-3">No Categories Yet</h4>
                    <p class="text-muted">Create your first category to organize your book reviews.</p>
                </div>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-item">
                            <div class="category-header">
                                <div class="category-info">
                                    <i class="bi bi-tag-fill text-primary me-2"></i>
                                    <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
                                </div>
                                <div class="category-actions">
                                    <button class="btn btn-outline-primary btn-sm edit-btn" 
                                            onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="?delete=<?= $cat['id'] ?>" 
                                       class="btn btn-outline-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this category? This will affect all reviews in this category.');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Edit Form (Initially Hidden) -->
                            <div class="edit-form d-none" id="edit-form-<?= $cat['id'] ?>">
                                <form method="post" class="d-flex gap-2 mt-2">
                                    <input type="hidden" name="update_id" value="<?= $cat['id'] ?>">
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           name="update_name" 
                                           value="<?= htmlspecialchars($cat['name']) ?>" 
                                           required>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" 
                                            onclick="cancelEdit(<?= $cat['id'] ?>)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Category Stats -->
                            <div class="category-stats mt-2">
                                <?php
                                $reviewCount = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE category_id = ?");
                                $reviewCount->execute([$cat['id']]);
                                $count = $reviewCount->fetchColumn();
                                ?>
                                <small class="text-muted">
                                    <i class="bi bi-book me-1"></i>
                                    <?= $count ?> review<?= $count != 1 ? 's' : '' ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Guidelines Card -->
    <div class="dashboard-card mt-4">
        <div class="dashboard-card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-info-circle me-2"></i>Category Guidelines
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">✓ Good Category Names</h6>
                    <ul class="small text-muted">
                        <li>Fiction</li>
                        <li>Mystery & Thriller</li>
                        <li>Science Fiction</li>
                        <li>Biography</li>
                        <li>Self-Help</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger">✗ Avoid</h6>
                    <ul class="small text-muted">
                        <li>Too specific categories</li>
                        <li>Duplicate or similar names</li>
                        <li>Very long category names</li>
                        <li>Special characters</li>
                    </ul>
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

.input-group-text {
    background-color: var(--light-gray);
    border-color: #dee2e6;
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

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.category-item {
    background-color: var(--light-gray);
    border-radius: 10px;
    padding: 1rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.category-item:hover {
    border-color: var(--coral);
    box-shadow: 0 2px 10px rgba(255, 123, 123, 0.1);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.category-name {
    font-weight: 600;
    color: var(--dark-blue);
}

.category-actions {
    display: flex;
    gap: 0.5rem;
}

.category-stats {
    padding-top: 0.5rem;
    border-top: 1px solid #dee2e6;
}

.badge {
    background-color: var(--coral) !important;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .category-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .category-actions {
        align-self: flex-end;
    }
}
</style>

<script>
function editCategory(id, currentName) {
    // Hide the category header and show edit form
    const categoryItem = document.getElementById('edit-form-' + id).closest('.category-item');
    const header = categoryItem.querySelector('.category-header');
    const editForm = document.getElementById('edit-form-' + id);
    
    header.style.display = 'none';
    editForm.classList.remove('d-none');
    
    // Focus on the input field
    editForm.querySelector('input[name="update_name"]').focus();
}

function cancelEdit(id) {
    // Show the category header and hide edit form
    const categoryItem = document.getElementById('edit-form-' + id).closest('.category-item');
    const header = categoryItem.querySelector('.category-header');
    const editForm = document.getElementById('edit-form-' + id);
    
    header.style.display = 'flex';
    editForm.classList.add('d-none');
}
</script>

<?php include 'footer.php'; ?>