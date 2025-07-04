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
}
?>

<style>
:root {
    --primary-color: #075b5e;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><path d="M0,20 C150,60 350,0 500,20 C650,40 850,0 1000,20 L1000,100 L0,100 Z"/></svg>');
    background-size: cover;
    background-position: bottom;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3.5rem;
    color: var(--warning-color);
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    margin-bottom: 2rem;
}

.stats-container {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    min-width: 120px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-top: -50px;
    position: relative;
    z-index: 3;
    padding: 2rem;
}

.filter-header {
    text-align: center;
    margin-bottom: 2rem;
}

.filter-title {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.custom-form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.custom-form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
}

.filter-btn {
    background: var(--primary-color);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.filter-btn:hover {
    background: var(--info-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(7, 91, 94, 0.3);
}

.reset-btn {
    background: var(--secondary-color);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.reset-btn:hover {
    background: var(--dark-color);
    color: white;
    transform: translateY(-1px);
}

/* Results Info */
.results-info {
    background: var(--light-color);
    border-radius: 15px;
    padding: 1.5rem;
    margin: 2rem 0;
    border-left: 4px solid var(--primary-color);
}

.sort-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    background: white;
    color: var(--dark-color);
    font-weight: 500;
    transition: all 0.3s ease;
}

.sort-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
}

/* Product Cards */
.product-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    height: 100%;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(7, 91, 94, 0.8), rgba(23, 162, 184, 0.8));
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-card:hover .image-overlay {
    opacity: 1;
}

.quick-view-btn {
    background: white;
    color: var(--primary-color);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.product-card:hover .quick-view-btn {
    transform: translateY(0);
}

.product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--success-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.condition-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--warning-color);
    color: var(--dark-color);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.card-body {
    padding: 1.5rem;
}

.product-title {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.product-description {
    color: var(--secondary-color);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.price-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.product-price {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.3rem;
    margin: 0;
}

.location-info {
    color: var(--secondary-color);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-btn:hover {
    background: var(--info-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(7, 91, 94, 0.3);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    color: var(--secondary-color);
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 2rem;
    color: var(--light-color);
}

.empty-state-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--dark-color);
}

.empty-state-text {
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

.pagination-btn {
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    padding: 0.75rem 1.5rem;
    margin: 0 0.25rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(7, 91, 94, 0.3);
}

.pagination-btn.active {
    background: var(--primary-color);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .stats-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stat-item {
        margin: 0 auto;
        max-width: 200px;
    }
    
    .filter-section {
        margin-top: -30px;
        padding: 1.5rem;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title">
                <i class="fas fa-recycle me-2"></i>
                Katalog Barang Second
            </h1>
            <p class="hero-subtitle">
                Temukan Harta Karun Berkualitas dengan Harga Terjangkau
            </p>
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($total_products); ?></span>
                    <span class="stat-label">Produk Tersedia</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4.8</span>
                    <span class="stat-label">Rating Seller</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Customer Support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-header">
            <h3 class="filter-title">
                <i class="fas fa-filter me-2"></i>
                Cari Produk Impian Anda
            </h3>
            <p class="text-muted">Gunakan filter untuk menemukan produk yang sesuai dengan kebutuhan Anda</p>
        </div>
        
        <form method="GET" class="row g-3">
            <!-- Search -->
            <div class="col-md-4">
                <label for="search" class="form-label fw-bold">
                    <i class="fas fa-search me-1"></i>Pencarian
                </label>
                <div class="position-relative">
                    <input type="text" 
                           class="form-control custom-form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Cari produk yang Anda inginkan..."
                           value="<?php echo htmlspecialchars($search); ?>">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
            </div>

            <!-- Category -->
            <div class="col-md-3">
                <label for="category" class="form-label fw-bold">
                    <i class="fas fa-tags me-1"></i>Kategori
                </label>
                <select class="form-select custom-form-control" id="category" name="category">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                            <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Price Range -->
            <div class="col-md-2">
                <label for="min_price" class="form-label fw-bold">
                    <i class="fas fa-money-bill-wave me-1"></i>Harga Min
                </label>
                <input type="number" 
                       class="form-control custom-form-control" 
                       id="min_price" 
                       name="min_price" 
                       placeholder="Rp 0"
                       value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
            </div>

            <div class="col-md-2">
                <label for="max_price" class="form-label fw-bold">Harga Max</label>
                <input type="number" 
                       class="form-control custom-form-control" 
                       id="max_price" 
                       name="max_price" 
                       placeholder="Rp 999.999"
                       value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
            </div>

            <!-- Submit -->
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn filter-btn w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">
                    <i class="fas fa-shopping-bag me-2 text-primary"></i>
                    Hasil Pencarian
                </h5>
                <p class="text-muted mb-0">
                    Menampilkan <strong><?php echo count($products); ?></strong> dari <strong><?php echo $total_products; ?></strong> produk
                    <?php if (!empty($search)): ?>
                        untuk "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <form method="GET" class="d-inline">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key !== 'sort'): ?>
                            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <label class="form-label fw-bold">
                        <i class="fas fa-sort me-1"></i>Urutkan:
                    </label>
                    <select name="sort" class="form-select sort-select d-inline-block" style="width: auto;" onchange="this.form.submit()">
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

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
    <div class="empty-state fade-in-up">
        <div class="empty-state-icon">
            <i class="fas fa-search"></i>
        </div>
        <h3 class="empty-state-title">Oops! Tidak Ada Produk Ditemukan</h3>
        <p class="empty-state-text">Coba ubah filter pencarian atau kata kunci Anda</p>
        <a href="katalog.php" class="btn filter-btn">
            <i class="fas fa-refresh me-2"></i>Reset Semua Filter
        </a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($products as $index => $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card product-card fade-in-up stagger-<?php echo ($index % 4) + 1; ?>">
                <div class="product-image-container">
                    <?php if (!empty($product['image'])): ?>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="product-image" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-badge">
                        <i class="fas fa-tag me-1"></i>Second
                    </div>
                    
                    <div class="condition-badge">
                        <?php echo ucfirst(htmlspecialchars($product['condition_item'])); ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <h5 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h5>
                    <p class="product-description">
                        <?php echo substr(htmlspecialchars($product['description']), 0, 100) . '...'; ?>
                    </p>
                    
                    <div class="price-container">
                        <h4 class="product-price">
                            Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                        </h4>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="location-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Jakarta</span>
                        </div>
                        <a href="detail.php?id=<?php echo $product['id']; ?>" class="detail-btn">
                            <i class="fas fa-shopping-cart"></i>
                            Beli Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-container">
        <!-- Previous -->
        <?php if ($page > 1): ?>
        <a class="pagination-btn" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
            <i class="fas fa-chevron-left"></i>
            Previous
        </a>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
        <a class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>" 
           href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($page < $total_pages): ?>
        <a class="pagination-btn" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
            Next
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Add smooth scrolling and interactive animations
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for pagination - FIXED VERSION
    document.querySelectorAll('.pagination-btn').forEach(link => {
        link.addEventListener('click', function(e) {
            // Only prevent default if we're on the same page
            const currentPage = new URL(window.location.href).searchParams.get('page') || '1';
            const targetPage = new URL(this.href).searchParams.get('page') || '1';
            
            if (currentPage !== targetPage) {
                // Different page - let it navigate normally but scroll to top first
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                // Small delay to allow smooth scroll to start
                setTimeout(() => {
                    window.location.href = this.href;
                }, 200);
                e.preventDefault();
            }
        });
    });

    // Alternative approach - Simple scroll to top after page load
    if (window.location.search.includes('page=')) {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Interactive product cards
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Auto-submit form on sort change with loading animation
    const sortSelect = document.querySelector('.sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const form = this.closest('form');
            const submitBtn = document.createElement('div');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
            submitBtn.className = 'text-muted small mt-1';
            this.parentNode.appendChild(submitBtn);
            
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    }

    // Smooth scroll for any internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading state for filter form
    const filterForm = document.querySelector('.filter-section form');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.filter-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;
            
            // Re-enable after a timeout in case of errors
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
});

// Alternative simpler approach for pagination scroll
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>