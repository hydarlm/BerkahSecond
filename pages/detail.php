<?php
$page_title = "Detail Produk";
include '../includes/header.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: katalog.php');
    exit;
}

// Get product details
$stmt = $pdo->prepare("SELECT p.*, u.username, u.full_name, u.phone, u.email 
                      FROM products p 
                      LEFT JOIN users u ON p.user_id = u.id 
                      WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: katalog.php');
    exit;
}

// Get related products (same category, excluding current product)
$related_stmt = $pdo->prepare("SELECT * FROM products 
                              WHERE category_id = ? AND id != ? AND status = 'available' 
                              ORDER BY created_at DESC LIMIT 4");
$related_stmt->execute([$product['category_id'], $product_id]);
$related_products = $related_stmt->fetchAll();

// Update view count
$update_views = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
$update_views->execute([$product_id]);
?>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="katalog.php">Katalog</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-image-container">
                <?php if (!empty($product['image'])): ?>
                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     id="mainImage"
                     style="width: 100%; height: 400px; object-fit: cover;">
                <?php else: ?>
                <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" 
                     style="width: 100%; height: 400px;">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <!-- Category Badge -->
                <div class="mb-3">
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-tag me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($product['category_id'])); ?>
                    </span>
                    <span class="badge bg-success fs-6 ms-2">
                        <i class="fas fa-check-circle me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($product['status'])); ?>
                    </span>
                </div>

                <!-- Product Name -->
                <h1 class="display-6 fw-bold mb-3">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <!-- Price -->
                <div class="mb-4">
                    <h2 class="text-primary fw-bold mb-0">
                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                    </h2>
                    <small class="text-muted">Harga dapat dinegosiasi</small>
                </div>

                <!-- Product Info -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="info-item">
                            <i class="fas fa-cog text-muted me-2"></i>
                            <strong>Kondisi:</strong>
                            <span class="ms-1"><?php echo ucfirst(htmlspecialchars($product['condition_item'])); ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <strong>Lokasi:</strong>
                            <span class="ms-1">
                                <?php if(!empty($product["lokasi"])):?>
                                    <?php echo htmlspecialchars($product['lokasi']); ?> 
                                <?php else: ?>
                                    lokasi tidak ditemukan
                                <?php endif;?>
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mt-2">
                        <div class="info-item">
                            <i class="fas fa-eye text-muted me-2"></i>
                            <strong>Dilihat:</strong>
                            <span class="ms-1"><?php echo number_format($product['views']); ?> kali</span>
                        </div>
                    </div>
                    <div class="col-6 mt-2">
                        <div class="info-item">
                            <i class="fas fa-calendar text-muted me-2"></i>
                            <strong>Diposting:</strong>
                            <span class="ms-1"><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Contact Seller -->
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Hubungi Penjual
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="seller-info mb-3">
                            <h6 class="mb-1"><?php echo htmlspecialchars($product['full_name']); ?></h6>
                            <p class="text-muted mb-0">@<?php echo htmlspecialchars($product['username']); ?></p>
                        </div>
                        
                        <div class="contact-buttons">
                            <?php if ($product["phone"] && !empty($product['phone'])): ?>
                            <a href="https://wa.me/62<?php echo ltrim($product['phone'], '0'); ?>?text=Halo, saya tertarik dengan <?php echo urlencode($product['name']); ?>" 
                               class="btn btn-success btn-lg w-100 mb-2" 
                               target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>
                                Chat WhatsApp
                            </a>
                            
                            <a href="tel:<?php echo $product['phone']; ?>" 
                               class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-phone me-2"></i>
                                <?php echo $product['phone']; ?>
                            </a>
                            <?php endif; ?>
                            
                            <a href="mailto:<?php echo $product['email']; ?>?subject=Tanya produk: <?php echo urlencode($product['name']); ?>" 
                               class="btn btn-outline-secondary w-100">
                                <i class="fas fa-envelope me-2"></i>
                                Kirim Email
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-outline-danger btn-lg" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                        <i class="fas fa-heart me-2"></i>Simpan
                    </button>
                    <button class="btn btn-outline-primary btn-lg ms-2" onclick="shareProduct()">
                        <i class="fas fa-share-alt me-2"></i>Bagikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Deskripsi Produk
                    </h4>
                </div>
                <div class="card-body">
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-tags me-2"></i>Produk Serupa
            </h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if (!empty($related['gambar'])): ?>
                            <img src="../assets/images/products/<?php echo htmlspecialchars($related['gambar']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($related['nama']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <?php echo htmlspecialchars($related['name']); ?>
                            </h6>
                            <div class="mt-auto">
                                <h5 class="text-primary mb-2">
                                    Rp <?php echo number_format($related['price'], 0, ',', '.'); ?>
                                </h5>
                                <a href="detail.php?id=<?php echo $related['id']; ?>" 
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.product-image-container img {
    transition: transform 0.3s ease;
}

.product-image-container:hover img {
    transform: scale(1.02);
}

.info-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.seller-info {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.contact-buttons .btn {
    transition: all 0.3s ease;
}

.contact-buttons .btn:hover {
    transform: translateY(-2px);
}

.description-content {
    line-height: 1.8;
    font-size: 16px;
}

.action-buttons .btn {
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}
</style>

<script>
function addToWishlist(productId) {
    // Implement wishlist functionality
    alert('Produk berhasil disimpan ke wishlist!');
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo htmlspecialchars($product['nama']); ?>',
            text: 'Lihat produk ini di BerkahSecond',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}

// Image zoom effect
document.getElementById('mainImage')?.addEventListener('click', function() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img src="${this.src}" class="img-fluid w-100" alt="Product Image">
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
});
</script>

