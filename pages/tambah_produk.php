<?php
$page_title = "Jual Barang";
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get categories for dropdown
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $condition_item = $_POST['condition_item'];
    $location = trim($_POST['location']);
    $contact_info = trim($_POST['contact_info']);
    $user_id = $_SESSION['user_id'];
    
    $errors = [];
    
    // Validation
    if (empty($name)) {
        $errors[] = "Nama produk harus diisi";
    }
    if (empty($description)) {
        $errors[] = "Deskripsi produk harus diisi";
    }
    if ($price <= 0) {
        $errors[] = "Harga harus lebih dari 0";
    }
    if (empty($category_id)) {
        $errors[] = "Kategori harus dipilih";
    }
    if (empty($condition_item)) {
        $errors[] = "Kondisi barang harus dipilih";
    }
    if (empty($location)) {
        $errors[] = "Lokasi harus diisi";
    }
    if (empty($contact_info)) {
        $errors[] = "Informasi kontak harus diisi";
    }
    
    // Handle image upload
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Format gambar tidak didukung. Gunakan JPEG, PNG, atau GIF";
        }
        
        if ($_FILES['image']['size'] > $max_size) {
            $errors[] = "Ukuran gambar maksimal 5MB";
        }
        
        if (empty($errors)) {
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $upload_path = '../assets/images/' . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal mengupload gambar";
            }
        }
    }
    
    // Insert to database if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, category_id, condition_item, location, image, user_id, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available', NOW())
            ");
            
            $stmt->execute([
                $name, $description, $price, $category_id, $condition_item, 
                $location, $image_name, $user_id
            ]);
            
            $success_message = "Produk berhasil ditambahkan!";
            
            // Clear form data
            $_POST = [];
            
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan saat menyimpan produk";
        }
    }
}
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3">Jual Barang Anda</h1>
                <p class="text-muted">Berikan kesempatan kedua untuk barang berkualitas Anda</p>
            </div>

            <!-- Success Message -->
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-box me-2 text-primary"></i>Nama Produk
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                   placeholder="Masukkan nama produk" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Deskripsi
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Jelaskan kondisi dan detail produk Anda" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Price and Category Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-2 text-primary"></i>Harga
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
                                           placeholder="0" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">
                                    <i class="fas fa-list me-2 text-primary"></i>Kategori
                                </label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-star me-2 text-primary"></i>Kondisi Barang
                            </label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="condition_item" 
                                               id="condition1" value="Seperti Baru" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Seperti Baru') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="condition1">
                                            <span class="badge bg-success">Seperti Baru</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="condition_item" 
                                               id="condition2" value="Baik" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Baik') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="condition2">
                                            <span class="badge bg-primary">Baik</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="condition_item" 
                                               id="condition3" value="Cukup Baik" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Cukup Baik') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="condition3">
                                            <span class="badge bg-warning text-dark">Cukup Baik</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location and Contact Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Lokasi
                                </label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" 
                                       placeholder="Kota, Provinsi" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_info" class="form-label fw-semibold">
                                    <i class="fas fa-phone me-2 text-primary"></i>Kontak
                                </label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                       value="<?php echo htmlspecialchars($_POST['contact_info'] ?? ''); ?>" 
                                       placeholder="WhatsApp/Telepon" required>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold">
                                <i class="fas fa-camera me-2 text-primary"></i>Foto Produk
                            </label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">
                                <small class="text-muted">
                                    Format: JPEG, PNG, GIF. Maksimal 5MB. Foto yang baik akan menarik lebih banyak pembeli.
                                </small>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Produk
                            </button>
                            <a href="../index.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="card mt-4 border-0 bg-light">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips Jual Barang
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <small>Gunakan foto yang jelas dan menarik</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <small>Tulis deskripsi yang detail dan jujur</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <small>Tentukan harga yang kompetitif</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <small>Berikan kontak yang bisa dihubungi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>