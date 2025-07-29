<?php
require 'includes/db.php';

$keyword = $_GET['q'] ?? '';
$category_id = $_GET['category'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12; // Match index.php pagination
$offset = ($page - 1) * $perPage;

$params = [];
$where = "WHERE 1=1";

if ($keyword) {
    $where .= " AND (r.title LIKE ? OR r.content LIKE ?)";
    $params[] = "%" . $keyword . "%";
    $params[] = "%" . $keyword . "%";
}
if ($category_id) {
    $where .= " AND r.category_id = ?";
    $params[] = $category_id;
}

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews r $where");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("SELECT r.id, r.title, r.content, r.image_path, r.created_at, c.name AS category_name 
                       FROM reviews r 
                       JOIN categories c ON r.category_id = c.id 
                       $where 
                       ORDER BY r.created_at DESC 
                       LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$results = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Function to build URL with current parameters
function buildSearchUrl($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return 'search.php?' . http_build_query($params);
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <!-- Search Header -->
    <div class="text-center mb-4">
        <h1 class="page-title">Search Reviews</h1>
        <?php if ($keyword || $category_id): ?>
            <p class="text-muted">
                <?php if ($keyword): ?>
                    Results for "<strong><?= htmlspecialchars($keyword) ?></strong>"
                <?php endif; ?>
                <?php if ($keyword && $category_id): ?> in <?php endif; ?>
                <?php if ($category_id): ?>
                    <?php 
                    $selectedCat = array_filter($cats, function($cat) use ($category_id) {
                        return $cat['id'] == $category_id;
                    });
                    if ($selectedCat) {
                        $selectedCat = reset($selectedCat);
                        echo '<strong>' . htmlspecialchars($selectedCat['name']) . '</strong>';
                    }
                    ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Search Form Card -->
    <div class="dashboard-card mb-4">
        <div class="dashboard-card-body">
            <form method="get" action="search.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="q" class="form-label">
                            <i class="bi bi-search me-2"></i>Search Keywords
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="q" 
                               name="q" 
                               value="<?= htmlspecialchars($keyword) ?>" 
                               placeholder="Enter book title, author, or keywords...">
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label">
                            <i class="bi bi-tags me-2"></i>Category
                        </label>
                        <select class="form-select" name="category" id="category">
                            <option value="">-- All Categories --</option>
                            <?php foreach ($cats as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>Search Results
        </h3>
        <span class="badge bg-primary fs-6"><?= $total ?> found</span>
    </div>

    <?php if (empty($results)): ?>
        <div class="dashboard-card">
            <div class="dashboard-card-body text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">No Results Found</h4>
                <p class="text-muted">Try adjusting your search terms or browse all categories.</p>
                <div class="mt-3">
                    <a href="search.php" class="btn btn-outline-primary me-2">Clear Search</a>
                    <a href="index.php" class="btn btn-primary">Browse All Books</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Results Grid -->
        <div class="book-grid-4col">
            <?php foreach ($results as $r): ?>
                <div class="book-card">
                    <div class="book-image" 
                         style="background-image: url('<?= $r['image_path'] ? htmlspecialchars($r['image_path']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop' ?>')">
                        <div class="book-category">
                            <?= htmlspecialchars($r['category_name']) ?>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($r['title']) ?></h3>
                        <div class="book-date">
                            Published: <?= date('M j, Y', strtotime($r['created_at'])) ?>
                        </div>
                        <p class="book-description">
                            <?= htmlspecialchars(substr(strip_tags($r['content']), 0, 150)) ?>...
                        </p>
                        <a href="review.php?id=<?= $r['id'] ?>" class="view-details-btn">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <nav aria-label="Search results pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $page > 1 ? buildSearchUrl(['page' => $page - 1]) : '#' ?>" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildSearchUrl(['page' => 1]) ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif;
                        endif;

                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildSearchUrl(['page' => $i]) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;

                        if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildSearchUrl(['page' => $totalPages]) ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $page < $totalPages ? buildSearchUrl(['page' => $page + 1]) : '#' ?>" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Page Info -->
                <div class="page-info text-center mt-3">
                    <span class="text-muted">
                        Showing <?= ($page - 1) * $perPage + 1 ?> to <?= min($page * $perPage, $total) ?> of <?= $total ?> results
                    </span>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Search Tips Card -->
    <div class="dashboard-card mt-4">
        <div class="dashboard-card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-lightbulb me-2"></i>Search Tips
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Search Examples</h6>
                    <ul class="small text-muted">
                        <li>"Harry Potter" - Find specific titles</li>
                        <li>"mystery thriller" - Find by genre keywords</li>
                        <li>"Stephen King" - Search by author name</li>
                        <li>"science fiction" - Discover sci-fi books</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Pro Tips</h6>
                    <ul class="small text-muted">
                        <li>Use specific keywords for better results</li>
                        <li>Combine category filter with keywords</li>
                        <li>Try different variations of book titles</li>
                        <li>Browse categories for inspiration</li>
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

.badge {
    background-color: var(--coral) !important;
}

/* 4 Column Grid */
.book-grid-4col {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    padding: 1rem 0;
}

@media (max-width: 1200px) {
    .book-grid-4col {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .book-grid-4col {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .book-grid-4col {
        grid-template-columns: 1fr;
    }
}

.book-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.book-image {
    width: 100%;
    height: 250px;
    background-size: cover;
    background-position: center;
    position: relative;
    background-color: #f0f0f0;
}

.book-category {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--coral);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.book-info {
    padding: 1.5rem;
}

.book-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--dark-blue);
    margin-bottom: 0.75rem;
    line-height: 1.3;
    height: 2.6rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.book-description {
    color: #666;
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 1rem;
    height: 4.2rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.book-date {
    color: #999;
    font-size: 0.75rem;
    margin-bottom: 1rem;
}

.view-details-btn {
    background: var(--coral);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: bold;
    width: 100%;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    font-size: 0.9rem;
}

.view-details-btn:hover {
    background: #E66B6B;
    transform: translateY(-2px);
    color: white;
}

/* Pagination Styles */
.pagination-container {
    margin-top: 3rem;
    margin-bottom: 2rem;
}

.pagination .page-link {
    color: var(--dark-blue);
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    margin: 0 0.125rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background-color: var(--coral-light);
    border-color: var(--coral);
    color: var(--dark-blue);
}

.pagination .page-item.active .page-link {
    background-color: var(--coral);
    border-color: var(--coral);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-info {
    color: #666;
    font-size: 0.9rem;
}
</style>

<?php include 'footer.php'; ?>