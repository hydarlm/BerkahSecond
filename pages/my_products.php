<?php
$page_title = "Produk Saya";
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Get product info to delete image
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$product_id, $_SESSION['user_id']]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Delete image file if exists
        if ($product['image'] && file_exists('../assets/images/' . $product['image'])) {
            unlink('../assets/images/' . $product['image']);
        }
        
        // Delete product from database
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
        $stmt->execute([$product_id, $_SESSION['user_id']]);
        
        $success_message = "Produk berhasil dihapus!";
    }
}

// Handle status change
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $new_status = $_GET['toggle_status'] === 'sold' ? 'sold' : 'available';
    
    $stmt = $pdo->prepare("UPDATE products SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $product_id, $_SESSION['user_id']]);
    
    $success_message = $new_status === 'sold' ? "Produk ditandai sebagai terjual!" : "Produk ditandai sebagai tersedia!";
}

// Get user's products
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Count products by status
$total_products = count($products);
$available_products = count(array_filter($products, function($p) { return $p['status'] === 'available'; }));
$sold_products = count(array_filter($products, function($p) { return $p['status'] === 'sold'; }));
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

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
}

/* Header Styling */
.page-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 10px 30px rgba(7, 91, 94, 0.3);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    opacity: 0.3;
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Stats Cards */
.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.6), rgba(255,255,255,0.3));
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.stats-card.bg-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
}

.stats-card.bg-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #34ce57 100%);
}

.stats-card.bg-info {
    background: linear-gradient(135deg, var(--info-color) 0%, #2dc4d6 100%);
}

.stats-card .card-body {
    padding: 1.5rem;
    text-align: center;
    position: relative;
}

.stats-card .fa-2x {
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-card h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

.stats-card small {
    font-size: 0.9rem;
    font-weight: 500;
    opacity: 0.9;
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 4px solid var(--success-color);
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Action Header */
.action-header {
    background: white;
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.action-header h3 {
    color: var(--primary-color);
    font-weight: 700;
    margin: 0;
}

.btn-add-product {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(7, 91, 94, 0.3);
}

.btn-add-product:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(7, 91, 94, 0.4);
    color: white;
}

/* Product Cards */
.product-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    background: white;
    position: relative;
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

.product-image .no-image {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: var(--secondary-color);
}

.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.status-badge.bg-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #34ce57 100%);
}

.status-badge.bg-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
}

.product-card .card-body {
    padding: 1.5rem;
}

.product-card .card-title {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.product-meta {
    color: var(--secondary-color);
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.product-meta i {
    color: var(--primary-color);
    margin-right: 0.3rem;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.condition-badge {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #7a8288 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

.product-info {
    color: var(--secondary-color);
    font-size: 0.8rem;
    margin-bottom: 1rem;
}

.product-info div {
    margin-bottom: 0.25rem;
}

.product-info i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.action-btn {
    flex: 1;
    border: none;
    border-radius: 10px;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}

.action-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.btn-edit {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(7, 91, 94, 0.3);
}

.btn-edit:hover {
    color: white;
    box-shadow: 0 6px 20px rgba(7, 91, 94, 0.4);
}

.btn-sold {
    background: linear-gradient(135deg, var(--success-color) 0%, #34ce57 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-sold:hover {
    color: white;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-available {
    background: linear-gradient(135deg, var(--warning-color) 0%, #ffd43b 100%);
    color: var(--dark-color);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.btn-available:hover {
    color: var(--dark-color);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, var(--danger-color) 0%, #e55870 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.btn-delete:hover {
    color: white;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    margin: 2rem 0;
}

.empty-state .fa-box-open {
    color: var(--secondary-color);
    opacity: 0.5;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--secondary-color);
    margin-bottom: 2rem;
}

.empty-state .btn {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7c82 100%);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(7, 91, 94, 0.3);
}

.empty-state .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(7, 91, 94, 0.4);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .action-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}

/* Animation for cards */
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

.product-card {
    animation: fadeInUp 0.6s ease-out;
}

.product-card:nth-child(2) { animation-delay: 0.1s; }
.product-card:nth-child(3) { animation-delay: 0.2s; }
.product-card:nth-child(4) { animation-delay: 0.3s; }
</style>

<div class="page-header">
    <div class="container">
        <div class="text-center">
            <h1><i class="fas fa-store me-3"></i>Produk Saya</h1>
            <p>Kelola semua produk yang Anda jual dengan mudah dan efisien</p>
        </div>
    </div>
</div>

<div class="container mt-4 mb-5">
    <!-- Success Message -->
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <i class="fas fa-box fa-2x"></i>
                    <h3><?php echo $total_products; ?></h3>
                    <small>Total Produk</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <i class="fas fa-store fa-2x"></i>
                    <h3><?php echo $available_products; ?></h3>
                    <small>Tersedia</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <i class="fas fa-handshake fa-2x"></i>
                    <h3><?php echo $sold_products; ?></h3>
                    <small>Terjual</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Header -->
    <div class="action-header">
        <h3><i class="fas fa-list me-2"></i>Daftar Produk</h3>
        <a href="tambah_produk.php" class="btn btn-add-product">
            <i class="fas fa-plus me-2"></i>Tambah Produk Baru
        </a>
    </div>

    <!-- Products List -->
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <i class="fas fa-box-open fa-4x"></i>
        <h4>Belum ada produk</h4>
        <p>Mulai jual barang Anda sekarang dan raih keuntungan!</p>
        <a href="add_product.php" class="btn">
            <i class="fas fa-plus me-2"></i>Tambah Produk Pertama
        </a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card product-card h-100">
                <!-- Product Image -->
                <div class="product-image">
                    <?php if ($product['image']): ?>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Status Badge -->
                    <div class="status-badge <?php echo $product['status'] === 'sold' ? 'bg-success' : 'bg-primary'; ?>">
                        <?php echo $product['status'] === 'sold' ? 'Terjual' : 'Tersedia'; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    
                    <div class="product-meta">
                        <i class="fas fa-tag"></i>
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Tidak berkategori'); ?>
                    </div>
                    
                    <p class="card-text text-muted">
                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...
                    </p>
                    
                    <!-- Price and Condition -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                        <span class="condition-badge"><?php echo htmlspecialchars($product['condition_item']); ?></span>
                    </div>

                    <!-- Location and Date -->
                    <div class="product-info">
                        <div><i class="fas fa-map-marker-alt"></i><?php echo htmlspecialchars($product['location']); ?></div>
                        <div><i class="fas fa-calendar"></i><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <!-- Edit Button -->
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                           class="action-btn btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <!-- Toggle Status Button -->
                        <?php if ($product['status'] === 'available'): ?>
                        <a href="?toggle_status=sold&id=<?php echo $product['id']; ?>" 
                           class="action-btn btn-sold"
                           onclick="return confirm('Tandai produk sebagai terjual?')">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php else: ?>
                        <a href="?toggle_status=available&id=<?php echo $product['id']; ?>" 
                           class="action-btn btn-available"
                           onclick="return confirm('Tandai produk sebagai tersedia?')">
                            <i class="fas fa-undo"></i>
                        </a>
                        <?php endif; ?>
                        
                        <!-- Delete Button -->
                        <a href="?delete=1&id=<?php echo $product['id']; ?>" 
                           class="action-btn btn-delete"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Add smooth scrolling and interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            }
        });
    }, observerOptions);
    
    // Observe all product cards
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });
    
    // Add loading state to action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.href.includes('delete') || this.href.includes('toggle_status')) {
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
                
                setTimeout(() => {
                    icon.className = originalClass;
                }, 1000);
            }
        });
    });
});
</script>