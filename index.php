<?php
$page_title = "Beranda";
include 'includes/header.php';

// Get latest products
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name, u.full_name as seller_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.status = 'available' 
    ORDER BY p.created_at DESC 
    LIMIT 8
");
$stmt->execute();
$latest_products = $stmt->fetchAll();

// Get categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_products FROM products WHERE status = 'available'");
$stmt->execute();
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
$stmt->execute();
$total_users = $stmt->fetch()['total_users'];
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

/* Hero Section Styles */
.hero-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7d82 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle cx="200" cy="200" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="400" cy="400" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="600" cy="600" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="800" cy="800" r="3" fill="rgba(255,255,255,0.1)"/></svg>');
    animation: float 20s infinite linear;
}

@keyframes float {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100px); }
}

.hero-content {
    position: relative;
    z-index: 2;
    color: white;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: slideInUp 1s ease-out;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    animation: slideInUp 1s ease-out 0.2s both;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-buttons {
    animation: slideInUp 1s ease-out 0.4s both;
}

.btn-hero {
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-hero:hover::before {
    left: 100%;
}

.btn-hero:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.hero-stats {
    animation: slideInUp 1s ease-out 0.6s both;
}

.stat-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.2);
}

.stat-card:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,0.15);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--warning-color);
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Categories Section */
.categories-section {
    padding: 5rem 0;
    background: var(--light-color);
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.section-subtitle {
    color: var(--secondary-color);
    font-size: 1.1rem;
}

.category-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    height: 100%;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.category-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
    font-size: 2rem;
    transition: all 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
    box-shadow: 0 10px 30px rgba(7, 91, 94, 0.3);
}

.category-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
}

/* Products Section */
.products-section {
    padding: 5rem 0;
    background: white;
}

.product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    height: 100%;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.product-image {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.condition-badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-body {
    padding: 1.5rem;
}

.product-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.product-description {
    color: var(--secondary-color);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.price-tag {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.product-meta {
    font-size: 0.85rem;
    color: var(--secondary-color);
}

.btn-product {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
    margin-top: 1rem;
    text-decoration: none;
}

.btn-product:hover {
    background: var(--info-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(7, 91, 94, 0.3);
}

/* Features Section */
.features-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, var(--light-color) 0%, #e9ecef 100%);
}

.feature-card {
    text-align: center;
    padding: 2rem;
    height: 100%;
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
    font-size: 2.5rem;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
    box-shadow: 0 15px 35px rgba(7, 91, 94, 0.3);
}

.feature-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 1rem;
}

.feature-description {
    color: var(--secondary-color);
    line-height: 1.6;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7d82 100%);
    color: white;
    padding: 5rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon points="0,0 1000,300 1000,1000 0,700" fill="rgba(255,255,255,0.05)"/></svg>');
}

.cta-content {
    position: relative;
    z-index: 2;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-subtitle {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
}

/* Animation for scroll reveal */
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

.animate-on-scroll {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out forwards;
}

/* Utility Classes */
.text-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-custom {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-custom:hover {
    background: var(--info-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(7, 91, 94, 0.3);
    color: white;
}

.btn-outline-custom {
    background: transparent;
    color: white;
    border: 2px solid white;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-outline-custom:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Temukan Barang <span class="text-warning">Berkualitas</span> dengan Harga Terjangkau
                    </h1>
                    <p class="hero-subtitle">
                        Platform jual beli barang bekas terpercaya. Berikan kesempatan kedua untuk barang-barang berkualitas dan dapatkan harga terbaik.
                    </p>
                    <div class="hero-buttons d-flex flex-wrap gap-3">
                        <a href="pages/katalog.php" class="btn-hero btn-custom">
                            <i class="fas fa-search me-2"></i>Jelajahi Katalog
                        </a>
                        <a href="./pages/tambah_produk.php" class="btn-hero btn-outline-custom">
                            <i class="fas fa-plus-circle me-2"></i>Jual Barang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-stats row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo number_format($total_products); ?>+</div>
                            <div class="stat-label">Produk Tersedia</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo number_format($total_users); ?>+</div>
                            <div class="stat-label">Pengguna Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-title">
            <h2>Kategori Populer</h2>
            <p class="section-subtitle">Temukan barang sesuai kategori yang Anda butuhkan</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="pages/katalog.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                    <div class="category-card">
                        <div class="category-icon">
                            <?php
                            $icons = [
                                'Elektronik' => 'fas fa-laptop',
                                'Fashion' => 'fas fa-tshirt',
                                'Otomotif' => 'fas fa-car',
                                'Rumah Tangga' => 'fas fa-home',
                                'Hobi & Olahraga' => 'fas fa-gamepad',
                                'Buku & Majalah' => 'fas fa-book'
                            ];
                            $icon = $icons[$category['name']] ?? 'fas fa-box';
                            ?>
                            <i class="<?php echo $icon; ?>"></i>
                        </div>
                        <h6 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h6>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="products-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="section-title text-start">
                <h2>Produk Terbaru</h2>
                <p class="section-subtitle">Barang-barang yang baru saja ditambahkan</p>
            </div>
            <a href="pages/katalog.php" class="btn-custom">
                Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($latest_products as $product): ?>
            <div class="col-lg-3 col-md-6">
                <div class="product-card">
                    <div class="position-relative overflow-hidden">
                        <img src="assets/images/<?php echo $product['image'] ?: 'no-image.jpg'; ?>" 
                            class="product-image w-100" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="position-absolute top-0 end-0 m-3">
                            <?php
                            $badge_class = [
                                'Seperti Baru' => 'bg-success',
                                'Baik' => 'bg-primary', 
                                'Cukup Baik' => 'bg-warning text-dark'
                            ];
                            $class = $badge_class[$product['condition_item']] ?? 'bg-secondary';
                            ?>
                            <span class="condition-badge <?php echo $class; ?>">
                                <?php echo htmlspecialchars($product['condition_item']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="product-body">
                        <h6 class="product-title">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h6>
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="price-tag"><?php echo formatRupiah($product['price']); ?></span>
                            <small class="product-meta">
                                <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($product['category_name']); ?>
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small class="product-meta">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($product['seller_name']); ?>
                            </small>
                            <small class="product-meta">
                                <i class="fas fa-clock me-1"></i><?php echo timeAgo($product['created_at']); ?>
                            </small>
                        </div>
                        <a href="pages/detail.php?id=<?php echo $product['id']; ?>" class="btn-product">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-title">
            <h2>Mengapa Memilih BerkahSecond?</h2>
            <p class="section-subtitle">Pengalaman jual beli yang aman, mudah, dan terpercaya</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="feature-title">Aman & Terpercaya</h5>
                    <p class="feature-description">
                        Sistem keamanan berlapis dan verifikasi pengguna untuk memastikan transaksi yang aman.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="feature-title">Mudah Dicari</h5>
                    <p class="feature-description">
                        Fitur pencarian dan filter canggih membantu Anda menemukan barang yang diinginkan dengan cepat.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5 class="feature-title">Komunitas Solid</h5>
                    <p class="feature-description">
                        Bergabung dengan komunitas jual beli yang saling membantu dan berbagi pengalaman.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Siap Memulai Jual Beli?</h2>
            <p class="cta-subtitle">
                Bergabunglah dengan ribuan pengguna yang sudah merasakan kemudahan bertransaksi di BerkahSecond
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <?php if (!isLoggedIn()): ?>
                <a href="pages/register.php" class="btn-hero btn-custom">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </a>
                <a href="pages/login.php" class="btn-hero btn-outline-custom">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </a>
                <?php else: ?>
                <a href="pages/tambah_produk.php" class="btn-hero btn-custom">
                    <i class="fas fa-plus-circle me-2"></i>Jual Barang Sekarang
                </a>
                <a href="pages/katalog.php" class="btn-hero btn-outline-custom">
                    <i class="fas fa-search me-2"></i>Cari Barang
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Smooth scroll reveal animation
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-on-scroll');
        }
    });
}, observerOptions);

// Observe all sections
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.categories-section, .products-section, .features-section, .cta-section');
    sections.forEach(section => {
        observer.observe(section);
    });
});

// Add interactive hover effects
document.addEventListener('DOMContentLoaded', () => {
    // Product cards parallax effect
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
        });
    });
});
</script>