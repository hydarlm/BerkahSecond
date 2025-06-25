<?php
$page_title = "Katalog Produk";
include '../includes/header.php';

// Pagination
$limit = 12; // Produk per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter dan pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$where_conditions = ["status = 'available'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where_conditions[] = "category_id = ?";
    $params[] = $category;
}

if ($min_price > 0) {
    $where_conditions[] = "price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $where_conditions[] = "price <= ?";
    $params[] = $max_price;
}

$where_clause = implode(' AND ', $where_conditions);

// Sorting
$order_by = "created_at DESC"; // default
switch ($sort) {
    case 'price_low':
        $order_by = "price ASC";
        break;
    case 'price_high':
        $order_by = "price DESC";
        break;
    case 'name':
        $order_by = "name ASC";
        break;
    case 'oldest':
        $order_by = "created_at ASC";
        break;
}

// Get total products for pagination
$count_sql = "SELECT COUNT(*) FROM products WHERE $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Get products
$sql = "SELECT * FROM products WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for filter
try {
    $cat_stmt = $pdo->prepare("SELECT DISTINCT c.id, c.name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.status = ?");
    $cat_stmt->execute(['available']);
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error database: " . $e->getMessage();
}$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-center mb-3">
                <i class="fas fa-store text-primary"></i> Katalog Produk
            </h1>
            <p class="text-center text-muted">Temukan barang bekas berkualitas dengan harga terjangkau</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Cari produk..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- Category -->
                <div class="col-md-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($cat)); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="col-md-2">
                    <label for="min_price" class="form-label">Harga Min</label>
                    <input type="number" 
                           class="form-control" 
                           id="min_price" 
                           name="min_price" 
                           placeholder="0"
                           value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                </div>

                <div class="col-md-2">
                    <label for="max_price" class="form-label">Harga Max</label>
                    <input type="number" 
                           class="form-control" 
                           id="max_price" 
                           name="max_price" 
                           placeholder="0"
                           value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
                </div>

                <!-- Submit -->
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
            
            <!-- Sort & Results Info -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Menampilkan <?php echo count($products); ?> dari <?php echo $total_products; ?> produk
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <form method="GET" class="d-inline">
                        <?php foreach ($_GET as $key => $value): ?>
                            <?php if ($key !== 'sort'): ?>
                                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <select name="sort" class="form-select d-inline-block" style="width: auto;" onchange="this.form.submit()">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Terlama</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Harga Terendah</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                            <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Nama A-Z</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
    <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h3 class="text-muted">Tidak ada produk ditemukan</h3>
        <p class="text-muted">Coba ubah filter pencarian Anda</p>
        <a href="katalog.php" class="btn btn-primary">
            <i class="fas fa-refresh me-2"></i>Reset Filter
        </a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm product-card">
                <div class="position-relative">
                    <?php if (!empty($product['imag'])): ?>
                    <img src="../assets/images/products/<?php echo htmlspecialchars($product['imag']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="position-absolute top-0 start-0 p-2">
                        <span class="badge bg-primary">
                            <?php echo ucfirst(htmlspecialchars($product['category_id'])); ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-2">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h5>
                    <p class="card-text text-muted small mb-2">
                        <?php echo substr(htmlspecialchars($product['description']), 0, 100) . '...'; ?>
                    </p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="text-primary mb-0">
                                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                            </h4>
                            <small class="text-muted">
                                <?php echo ucfirst($product['condition_item']); ?>
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i>
                                <!-- <?php echo htmlspecialchars($product['location']); ?> -->
                            </small>
                            <a href="detail.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Product pagination" class="mt-5">
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            </li>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next -->
            <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.card-img-top {
    transition: transform 0.3s;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}
</style>