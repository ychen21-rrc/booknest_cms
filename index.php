<?php
require 'includes/db.php';

// Get all categories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// Get filter parameters
$selectedCategory = $_GET['category'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'title-az';
$searchTerm = $_GET['search'] ?? '';

// Build the query
$sql = "SELECT r.*, c.name AS category_name, c.id AS category_id 
        FROM reviews r 
        JOIN categories c ON r.category_id = c.id";

$params = [];
$conditions = [];

// Add category filter
if ($selectedCategory !== 'all') {
    $conditions[] = "r.category_id = ?";
    $params[] = $selectedCategory;
}

// Add search filter
if (!empty($searchTerm)) {
    $conditions[] = "(r.title LIKE ? OR r.content LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

// Add WHERE clause if conditions exist
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Add sorting
switch ($sortBy) {
    case 'title-za':
        $sql .= " ORDER BY r.title DESC";
        break;
    case 'date-new':
        $sql .= " ORDER BY r.created_at DESC";
        break;
    case 'date-old':
        $sql .= " ORDER BY r.created_at ASC";
        break;
    default: // title-az
        $sql .= " ORDER BY r.title ASC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>
    <!-- Page Title -->
    <h1 class="page-title">Our Products</h1>

    <!-- Search and Sort Form -->
    <form method="GET" action="" class="container mb-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <input type="text" class="form-control search-bar" name="search" 
                        placeholder="Search books by title or content..." 
                        value="<?= htmlspecialchars($searchTerm) ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select sort-dropdown" name="sort" onchange="this.form.submit()">
                    <option value="title-az" <?= $sortBy === 'title-az' ? 'selected' : '' ?>>Name: A to Z</option>
                    <option value="title-za" <?= $sortBy === 'title-za' ? 'selected' : '' ?>>Name: Z to A</option>
                    <option value="date-new" <?= $sortBy === 'date-new' ? 'selected' : '' ?>>Date: Newest First</option>
                    <option value="date-old" <?= $sortBy === 'date-old' ? 'selected' : '' ?>>Date: Oldest First</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
            </div>
        </div>
        <!-- Hidden field to preserve category selection -->
        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
    </form>

    <div class="container">
        <div class="row">
            <!-- Category Filter -->
            <div class="col-lg-3">
                <div class="category-sidebar">
                    <form method="GET" action="">
                        <select class="form-select sort-dropdown" name="category" onchange="this.form.submit()">
                            <option value="all" <?= $selectedCategory === 'all' ? 'selected' : '' ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Hidden fields to preserve other filters -->
                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchTerm) ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                    </form>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="col-lg-9">
                <?php if (empty($reviews)): ?>
                    <div class="no-results">
                        <p>No books found matching your criteria.</p>
                        <a href="?" class="btn btn-outline-secondary">Show All Books</a>
                    </div>
                <?php else: ?>
                    <div class="book-grid">
                        <?php foreach ($reviews as $review): ?>
                            <div class="book-card">
                                <div class="book-image" 
                                        style="background-image: url('<?= $review['image_path'] ? htmlspecialchars($review['image_path']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop' ?>')">
                                    <div class="book-category">
                                        <?= htmlspecialchars($review['category_name']) ?>
                                    </div>
                                </div>
                                <div class="book-info">
                                    <h3 class="book-title"><?= htmlspecialchars($review['title']) ?></h3>
                                    <div class="book-date">
                                        Published: <?= date('M j, Y', strtotime($review['created_at'])) ?>
                                    </div>
                                    <p class="book-description">
                                        <?= htmlspecialchars(substr(strip_tags($review['content']), 0, 150)) ?>...
                                    </p>
                                    <a href="review.php?id=<?= $review['id'] ?>" class="view-details-btn">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>