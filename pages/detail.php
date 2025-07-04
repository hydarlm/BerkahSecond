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

<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

<div class="product-detail-container">
    <!-- Hero Section with Gradient Background -->
    <div class="hero-section">
        <div class="container">
            <!-- Enhanced Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-down">
                <ol class="breadcrumb glass-morphism">
                    <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="katalog.php"><i class="fas fa-th-large me-1"></i>Katalog</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-box me-1"></i><?php echo htmlspecialchars($product['name']); ?></li>
                </ol>
            </nav>

            <div class="row align-items-center">
                <!-- Enhanced Product Images -->
                <div class="col-lg-6 mb-5" data-aos="fade-right">
                    <div class="product-image-showcase">
                        <div class="main-image-container">
                            <?php if (!empty($product['image'])): ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="main-product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 id="mainImage">
                            <div class="image-overlay">
                                <div class="zoom-icon">
                                    <i class="fas fa-search-plus"></i>
                                    <span>Klik untuk memperbesar</span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image"></i>
                                <span>Tidak ada gambar</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Image Actions -->
                        <div class="image-actions mt-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleImageFullscreen()">
                                <i class="fas fa-expand me-1"></i>Fullscreen
                            </button>
                            <button class="btn btn-outline-secondary btn-sm ms-2" onclick="shareProduct()">
                                <i class="fas fa-share-alt me-1"></i>Bagikan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Product Details -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="product-info-card">
                        <!-- Status Badges -->
                        <div class="status-badges mb-4">
                            <span class="status-badge primary">
                                <i class="fas fa-tag me-1"></i>
                                <?php echo ucfirst(htmlspecialchars($product['category_id'])); ?>
                            </span>
                            <span class="status-badge success">
                                <i class="fas fa-check-circle me-1"></i>
                                <?php echo ucfirst(htmlspecialchars($product['status'])); ?>
                            </span>
                        </div>

                        <!-- Product Name with Animation -->
                        <h1 class="product-title animate__animated animate__fadeInUp">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h1>

                        <!-- Price Section -->
                        <div class="price-section mb-4">
                            <div class="price-main">
                                <span class="currency">Rp</span>
                                <span class="amount"><?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="price-note">
                                <i class="fas fa-handshake me-1"></i>
                                <span>Harga dapat dinegosiasi</span>
                            </div>
                        </div>

                        <!-- Enhanced Product Info Grid -->
                        <div class="product-info-grid">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="info-content">
                                    <label>Kondisi</label>
                                    <value><?php echo ucfirst(htmlspecialchars($product['condition_item'])); ?></value>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <label>Lokasi</label>
                                    <value>
                                        <?php if(!empty($product["location"])): ?>
                                            <?php echo htmlspecialchars($product['location']); ?>
                                        <?php else: ?>
                                            Lokasi tidak tersedia
                                        <?php endif; ?>
                                    </value>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="info-content">
                                    <label>Dilihat</label>
                                    <value><?php echo number_format($product['views']); ?> kali</value>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <label>Diposting</label>
                                    <value><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></value>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons-container mt-4">
                            <button class="btn btn-primary btn-lg pulse-animation" onclick="scrollToContact()" style="background: var(--primary-color); border-color: var(--primary-color);">
                                <i class="fas fa-phone me-2"></i>
                                Hubungi Penjual
                            </button>
                            <button class="btn btn-outline-danger btn-lg" onclick="addToWishlist(<?php echo $product['id']; ?>)" style="border-color: var(--danger-color);">
                                <i class="fas fa-heart me-2"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description Section -->
    <div class="section-container" data-aos="fade-up">
        <div class="container">
            <div class="description-card">
                <div class="section-header">
                    <h3><i class="fas fa-info-circle me-2"></i>Deskripsi Produk</h3>
                </div>
                <div class="description-content">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Contact Seller Section -->
    <div class="section-container" id="contactSection" data-aos="fade-up">
        <div class="container">
            <div class="contact-seller-card">
                <div class="contact-header">
                    <div class="seller-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="seller-info">
                        <h4><?php echo htmlspecialchars($product['full_name']); ?></h4>
                        <p>@<?php echo htmlspecialchars($product['username']); ?></p>
                        <div class="seller-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>(4.8)</span>
                        </div>
                    </div>
                </div>
                
                <div class="contact-actions">
                    <?php if ($product["phone"] && !empty($product['phone'])): ?>
                    <a href="https://wa.me/62<?php echo ltrim($product['phone'], '0'); ?>?text=Halo, saya tertarik dengan <?php echo urlencode($product['name']); ?>" 
                       class="contact-btn whatsapp" 
                       target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                        <div class="btn-glow"></div>
                    </a>
                    
                    <a href="tel:<?php echo $product['phone']; ?>" 
                       class="contact-btn phone">
                        <i class="fas fa-phone"></i>
                        <span><?php echo $product['phone']; ?></span>
                        <div class="btn-glow"></div>
                    </a>
                    <?php endif; ?>
                    
                    <a href="mailto:<?php echo $product['email']; ?>?subject=Tanya produk: <?php echo urlencode($product['name']); ?>" 
                       class="contact-btn email">
                        <i class="fas fa-envelope"></i>
                        <span>Email</span>
                        <div class="btn-glow"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="section-container" data-aos="fade-up">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h3><i class="fas fa-tags me-2"></i>Produk Serupa</h3>
                <p>Temukan produk lain yang mungkin Anda sukai</p>
            </div>
            
            <div class="related-products-grid">
                <?php foreach ($related_products as $index => $related): ?>
                <div class="product-card" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="product-image-container">
                        <?php if (!empty($related['image'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($related['image']); ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                        </div>
                        <?php endif; ?>
                        <div class="product-overlay">
                            <a href="detail.php?id=<?php echo $related['id']; ?>" class="view-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <h5><?php echo htmlspecialchars($related['name']); ?></h5>
                        <div class="price">
                            Rp <?php echo number_format($related['price'], 0, ',', '.'); ?>
                        </div>
                        <a href="detail.php?id=<?php echo $related['id']; ?>" 
                           class="btn btn-sm w-100 mt-2" 
                           style="background: var(--primary-color); border-color: var(--primary-color); color: white;">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Enhanced Styles -->
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
    
    --primary-gradient: linear-gradient(135deg, #075b5e 0%, #0a7c82 100%);
    --secondary-gradient: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    --success-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    --danger-gradient: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    --warning-gradient: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    --info-gradient: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
    --shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
    --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.15);
}

* {
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--dark-color);
}

.product-detail-container {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--light-color) 0%, #e9ecef 100%);
}

.hero-section {
    background: var(--primary-gradient);
    color: white;
    padding: 2rem 0 4rem;
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>');
    background-size: cover;
}

.glass-morphism {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 15px;
    padding: 1rem;
}

.breadcrumb {
    background: none;
    margin-bottom: 0;
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: all 0.3s ease;
}

.breadcrumb-item a:hover {
    color: white;
    transform: translateY(-2px);
}

.product-image-showcase {
    position: relative;
}

.main-image-container {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-heavy);
    background: white;
}

.main-product-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    transition: transform 0.6s ease;
    cursor: pointer;
}

.main-product-image:hover {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.main-image-container:hover .image-overlay {
    opacity: 1;
}

.zoom-icon {
    text-align: center;
    color: white;
}

.zoom-icon i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.no-image-placeholder {
    width: 100%;
    height: 500px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.2rem;
}

.no-image-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.product-info-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: var(--shadow-light);
    position: relative;
    overflow: hidden;
}

.product-info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.status-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
    display: inline-flex;
    align-items: center;
    animation: fadeInUp 0.6s ease;
}

.status-badge.primary {
    background: var(--primary-gradient);
}

.status-badge.success {
    background: var(--success-gradient);
}

.product-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark-color);
    line-height: 1.2;
}

.price-section {
    text-align: center;
    padding: 1.5rem;
    background: var(--primary-gradient);
    border-radius: 15px;
    color: white;
    margin-bottom: 2rem;
}

.price-main {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.currency {
    font-size: 1.5rem;
    vertical-align: top;
    margin-right: 0.5rem;
}

.price-note {
    font-size: 0.9rem;
    opacity: 0.9;
}

.product-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-card {
    background: var(--light-color);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.info-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-light);
    border-color: var(--primary-color);
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.info-content label {
    display: block;
    font-size: 0.8rem;
    color: var(--secondary-color);
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-content value {
    font-weight: 600;
    color: var(--dark-color);
}

.action-buttons-container {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-buttons-container .btn {
    flex: 1;
    min-width: 150px;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.section-container {
    padding: 3rem 0;
}

.description-card, .contact-seller-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-light);
    overflow: hidden;
}

.section-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.5rem 2rem;
    margin: 0;
}

.section-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.description-content {
    padding: 2rem;
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--dark-color);
}

.contact-seller-card {
    padding: 2rem;
}

.contact-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.seller-avatar {
    width: 80px;
    height: 80px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.seller-info h4 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.seller-info p {
    margin: 0 0 0.5rem 0;
    color: var(--secondary-color);
}

.seller-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.seller-rating i {
    color: var(--warning-color);
}

.contact-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.contact-btn {
    flex: 1;
    min-width: 200px;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    color: white;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.contact-btn.whatsapp {
    background: var(--success-color);
}

.contact-btn.phone {
    background: var(--info-color);
}

.contact-btn.email {
    background: var(--secondary-color);
}

.contact-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-light);
    color: white;
}

.contact-btn .btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s ease;
}

.contact-btn:hover .btn-glow {
    left: 100%;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-light);
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-heavy);
}

.product-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image-container img {
    transform: scale(1.1);
}

.no-image {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.view-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.view-btn:hover {
    transform: scale(1.2);
    color: #667eea;
}

.product-info {
    padding: 1.5rem;
}

.product-info h5 {
    margin-bottom: 0.5rem;
    color: #2d3748;
    font-weight: 600;
}

.product-info .price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 1rem;
}

.image-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-title {
        font-size: 2rem;
    }
    
    .price-main {
        font-size: 2rem;
    }
    
    .product-info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons-container {
        flex-direction: column;
    }
    
    .contact-actions {
        flex-direction: column;
    }
    
    .contact-btn {
        min-width: auto;
    }
}

/* Loading Animation */
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
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 50
});

function addToWishlist(productId) {
    // Add visual feedback
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Tersimpan!';
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-danger');
            btn.disabled = false;
        }, 2000);
    }, 1000);
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo htmlspecialchars($product['name']); ?>',
            text: 'Lihat produk ini di BerkahSecond',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard with better UX
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Link berhasil disalin ke clipboard!', 'success');
        });
    }
}

function scrollToContact() {
    document.getElementById('contactSection').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

function toggleImageFullscreen() {
    const img = document.getElementById('mainImage');
    if (!img) return;
    
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.style.background = 'rgba(0,0,0,0.9)';
    modal.innerHTML = `
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <img src="${img.src}" class="img-fluid" alt="Product Image" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
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
}

// Enhanced image click handler
document.getElementById('mainImage')?.addEventListener('click', function() {
    toggleImageFullscreen();
});

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 350px;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
    `;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Smooth scrolling for all internal links
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

// Add loading states to contact buttons
document.querySelectorAll('.contact-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Add subtle loading animation
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Image lazy loading effect
const observerOptions = {
    threshold: 0.1,
    rootMargin: '50px'
};

const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.style.opacity = '1';
            img.style.transform = 'translateY(0)';
            imageObserver.unobserve(img);
        }
    });
}, observerOptions);

// Apply lazy loading to product images
document.querySelectorAll('.product-card img').forEach(img => {
    img.style.opacity = '0';
    img.style.transform = 'translateY(20px)';
    img.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    imageObserver.observe(img);
});

// Enhanced hover effects for product cards
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// CSS Animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    .bounce-animation {
        animation: bounce 2s infinite;
    }
`;
document.head.appendChild(style);

// Add bounce animation to action buttons on page load
window.addEventListener('load', () => {
    setTimeout(() => {
        document.querySelectorAll('.action-buttons-container .btn').forEach((btn, index) => {
            setTimeout(() => {
                btn.classList.add('bounce-animation');
                setTimeout(() => {
                    btn.classList.remove('bounce-animation');
                }, 2000);
            }, index * 200);
        });
    }, 1000);
});

// Enhanced product info card animations
const infoCards = document.querySelectorAll('.info-card');
const cardObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
            }, index * 100);
            cardObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

infoCards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    cardObserver.observe(card);
});
</script>