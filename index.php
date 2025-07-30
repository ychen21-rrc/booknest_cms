<?php
require 'includes/db.php';

// Get all categories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// Get filter parameters
$selectedCategory = $_GET['category'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'date-new';
$searchTerm = $_GET['search'] ?? '';

// Pagination settings
$itemsPerPage = 12; // 4 columns x 3 rows
$currentPage = $_GET['page'] ?? 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Build the query for counting total results
$countSql = "SELECT COUNT(*) FROM reviews r JOIN categories c ON r.category_id = c.id";
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
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}

// Get total count
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Build the main query
$sql = "SELECT r.*, c.name AS category_name, c.id AS category_id 
        FROM reviews r 
        JOIN categories c ON r.category_id = c.id";

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

// Add pagination
$sql .= " LIMIT $itemsPerPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();

// Function to build URL with current parameters
function buildUrl($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return '?' . http_build_query($params);
}
?>

<?php include 'header.php'; ?>

<!-- Search Bar -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="GET" action="" class="search-form">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 search-input" name="search" 
                           placeholder="Search books by title, author, or category..." 
                           value="<?= htmlspecialchars($searchTerm) ?>"
                           onchange="this.form.submit()">
                </div>
                <!-- Hidden fields to preserve other filters -->
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                <input type="hidden" name="page" value="1">
            </form>
        </div>
    </div>
</div>

<!-- Page Title -->
<h1 class="page-title">Our Books</h1>

<!-- Filters Section -->
<div class="container">
    <div class="filters-container">
        <!-- Category Filter -->
        <div class="filter-group">
            <form method="GET" action="" class="d-flex align-items-center">
                <select class="form-select category-select" name="category" onchange="this.form.submit()">
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
                <input type="hidden" name="page" value="1">
            </form>
        </div>

        <!-- Sort Filter -->
        <div class="filter-group">
            <form method="GET" action="" class="d-flex align-items-center">
                <select class="form-select sort-select" name="sort" onchange="this.form.submit()">
                    <option value="date-new" <?= $sortBy === 'date-new' ? 'selected' : '' ?>>Date: Newest First</option>
                    <option value="date-old" <?= $sortBy === 'date-old' ? 'selected' : '' ?>>Date: Oldest First</option>
                    <option value="title-az" <?= $sortBy === 'title-az' ? 'selected' : '' ?>>Name: A to Z</option>
                    <option value="title-za" <?= $sortBy === 'title-za' ? 'selected' : '' ?>>Name: Z to A</option>     
                </select>
                <!-- Hidden fields to preserve other filters -->
                <input type="hidden" name="search" value="<?= htmlspecialchars($searchTerm) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <input type="hidden" name="page" value="1">
            </form>
        </div>
    </div>
</div>

<!-- Books Grid -->
<div class="container">
    <?php if (empty($reviews)): ?>
        <div class="no-results">
            <p>No books found matching your criteria.</p>
            <a href="?" class="btn btn-outline-secondary">Show All Books</a>
        </div>
    <?php else: ?>
        <div class="book-grid-4col">
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage > 1 ? buildUrl(['page' => $currentPage - 1]) : '#' ?>" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildUrl(['page' => 1]) ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif;
                        endif;

                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildUrl(['page' => $i]) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;

                        if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildUrl(['page' => $totalPages]) ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage < $totalPages ? buildUrl(['page' => $currentPage + 1]) : '#' ?>" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Page Info -->
                <div class="page-info text-center mt-3">
                    <span class="text-muted">
                        Showing <?= ($currentPage - 1) * $itemsPerPage + 1 ?> to <?= min($currentPage * $itemsPerPage, $totalItems) ?> of <?= $totalItems ?> results
                    </span>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>