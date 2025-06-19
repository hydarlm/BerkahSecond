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

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Temukan Barang <span class="text-warning">Berkualitas</span> dengan Harga Terjangkau
                </h1>
                <p class="lead mb-4">
                    Platform jual beli barang bekas terpercaya. Berikan kesempatan kedua untuk barang-barang berkualitas dan dapatkan harga terbaik.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="pages/katalog.php" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-search me-2"></i>Jelajahi Katalog
                    </a>
                    <a href="pages/tambah_produk.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-plus-circle me-2"></i>Jual Barang
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-stats row g-3 mt-4">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <h3 class="fw-bold mb-1"><?php echo number_format($total_products); ?>+</h3>
                            <p class="mb-0 opacity-75">Produk Tersedia</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <h3 class="fw-bold mb-1"><?php echo number_format($total_users); ?>+</h3>
                            <p class="mb-0 opacity-75">Pengguna Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="container mb-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-3">Kategori Populer</h2>
        <p class="text-muted">Temukan barang sesuai kategori yang Anda butuhkan</p>
    </div>
    
    <div class="row g-3">
        <?php foreach ($categories as $category): ?>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <a href="pages/katalog.php?category=<?php echo $category['id']; ?>" 
            class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm category-card">
                    <div class="card-body py-4">
                        <div class="category-icon mb-3">
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
                            <i class="<?php echo $icon; ?> fa-2x text-success"></i>
                        </div>
                        <h6 class="fw-semibold mb-0"><?php echo htmlspecialchars($category['name']); ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Latest Products Section -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2">Produk Terbaru</h2>
            <p class="text-muted mb-0">Barang-barang yang baru saja ditambahkan</p>
        </div>
        <a href="pages/katalog.php" class="btn btn-outline-primary">
            Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
    
    <div class="row g-4">
        <?php foreach ($latest_products as $product): ?>
        <div class="col-lg-3 col-md-6">
            <div class="card product-card h-100">
                <div class="position-relative">
                    <img src="assets/images/<?php echo $product['image'] ?: 'no-image.jpg'; ?>" 
                        class="card-img-top" 
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-2">
                        <?php
                        $badge_class = [
                            'Seperti Baru' => 'bg-success',
                            'Baik' => 'bg-primary', 
                            'Cukup Baik' => 'bg-warning text-dark'
                        ];
                        $class = $badge_class[$product['condition_item']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?php echo $class; ?> condition-badge">
                            <?php echo htmlspecialchars($product['condition_item']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title mb-2">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h6>
                    <p class="card-text text-muted small mb-2">
                        <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                    </p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="price-tag"><?php echo formatRupiah($product['price']); ?></span>
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($product['category_name']); ?>
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($product['seller_name']); ?>
                            </small>
                            <small class="text-muted">
                                <?php echo timeAgo($product['created_at']); ?>
                            </small>
                        </div>
                        <a href="pages/detail.php?id=<?php echo $product['id']; ?>" 
                        class="btn btn-primary btn-sm w-100 mt-2">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Features Section -->
<section class="bg-white py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Mengapa Memilih BerkahSecond?</h2>
            <p class="text-muted">Pengalaman jual beli yang aman, mudah, dan terpercaya</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-3">Aman & Terpercaya</h5>
                    <p class="text-muted">
                        Sistem keamanan berlapis dan verifikasi pengguna untuk memastikan transaksi yang aman.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-search fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-semibold mb-3">Mudah Dicari</h5>
                    <p class="text-muted">
                        Fitur pencarian dan filter canggih membantu Anda menemukan barang yang diinginkan dengan cepat.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-handshake fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-semibold mb-3">Komunitas Solid</h5>
                    <p class="text-muted">
                        Bergabung dengan komunitas jual beli yang saling membantu dan berbagi pengalaman.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Siap Memulai Jual Beli?</h2>
        <p class="lead mb-4">
            Bergabunglah dengan ribuan pengguna yang sudah merasakan kemudahan bertransaksi di BerkahSecond
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php if (!isLoggedIn()): ?>
            <a href="pages/register.php" class="btn btn-light btn-lg px-4">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </a>
            <a href="pages/login.php" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </a>
            <?php else: ?>
            <a href="pages/tambah_produk.php" class="btn btn-light btn-lg px-4">
                <i class="fas fa-plus-circle me-2"></i>Jual Barang Sekarang
            </a>
            <a href="pages/katalog.php" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-search me-2"></i>Cari Barang
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>