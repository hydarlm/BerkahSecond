<?php
$page_title = "Edit Produk";
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    header('Location: my_products.php');
    exit;
}

// Get product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->execute([$product_id, $_SESSION['user_id']]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: my_products.php');
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
    $phone = trim($_POST['phone']);
    
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
    if (empty($phone)) {
        $errors[] = "Informasi kontak harus diisi";
    }
    
    // Handle image upload
    $image_name = $product['image']; // Keep existing image by default
    
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
            // Delete old image if exists
            if ($product['image'] && file_exists('../assets/images/' . $product['image'])) {
                unlink('../assets/images/' . $product['image']);
            }
            
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $upload_path = '../assets/images/' . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal mengupload gambar";
                $image_name = $product['image']; // Revert to old image
            }
        }
    }
    
    // Update database if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, category_id = ?, 
                    condition_item = ?, location = ?, image = ?, updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([
                $name, $description, $price, $category_id, $condition_item, 
                $location, $image_name, $product_id, $_SESSION['user_id']
            ]);
            
            $success_message = "Produk berhasil diperbarui!";
            
            // Refresh product data
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
            $stmt->execute([$product_id, $_SESSION['user_id']]);
            $product = $stmt->fetch();
            
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan saat memperbarui produk";
        }
    }
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

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-container {
    background: transparent;
    padding: 2rem 0;
}

.page-header {
    background: linear-gradient(135deg, var(--primary-color), #0a7479);
    color: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 15px 35px rgba(7, 91, 94, 0.3);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.page-header p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.form-card {
    background: white;
    border-radius: 25px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
}

.form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
}

.form-card-body {
    padding: 3rem;
}

.form-group {
    margin-bottom: 2rem;
    position: relative;
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    font-size: 1.1rem;
}

.form-label i {
    color: var(--primary-color);
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1rem 1.5rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
    background: white;
    transform: translateY(-2px);
}

.form-control:hover, .form-select:hover {
    border-color: var(--primary-color);
    background: white;
}

.input-group-text {
    background: var(--primary-color);
    color: white;
    border: 2px solid var(--primary-color);
    border-radius: 15px 0 0 15px;
    font-weight: 600;
    padding: 1rem 1.5rem;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 15px 15px 0;
}

.condition-options {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.condition-card {
    flex: 1;
    min-width: 150px;
    position: relative;
}

.condition-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.condition-label {
    display: block;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.condition-label::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: var(--primary-color);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
    z-index: 1;
}

.condition-input:checked + .condition-label::before {
    width: 100%;
    height: 100%;
    border-radius: 15px;
}

.condition-input:checked + .condition-label {
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(7, 91, 94, 0.3);
}

.condition-label .badge {
    position: relative;
    z-index: 2;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.condition-input:checked + .condition-label .badge {
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

.current-image {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}

.current-image img {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.current-image:hover img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 600;
}

.file-upload-area {
    border: 2px dashed #e9ecef;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fa;
    position: relative;
    overflow: hidden;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: #f0f8ff;
}

.file-upload-area.drag-over {
    border-color: var(--primary-color);
    background: #e6f3ff;
    transform: scale(1.02);
}

.file-upload-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #0a7479);
    border: none;
    border-radius: 15px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(7, 91, 94, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
}

.btn-primary:hover::before {
    width: 300px;
    height: 300px;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(7, 91, 94, 0.4);
}

.btn-outline-secondary {
    border: 2px solid var(--secondary-color);
    color: var(--secondary-color);
    background: transparent;
    border-radius: 15px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: var(--secondary-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
}

.alert {
    border: none;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    font-size: 1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #34ce57);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #e74c3c);
    color: white;
}

.alert i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.btn-close {
    filter: brightness(0) invert(1);
}

.form-text {
    margin-top: 0.5rem;
    color: var(--secondary-color);
    font-size: 0.9rem;
}

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.button-group .btn {
    flex: 1;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .page-header {
        padding: 2rem 1rem;
    }
    
    .page-header h1 {
        font-size: 2rem;
    }
    
    .form-card-body {
        padding: 2rem;
    }
    
    .condition-options {
        flex-direction: column;
    }
    
    .condition-card {
        min-width: 100%;
    }
    
    .button-group {
        flex-direction: column;
    }
}

/* Animation untuk loading state */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.loading-overlay.active {
    opacity: 1;
    visibility: visible;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<div class="main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <!-- Page Header -->
                <div class="page-header text-center">
                    <h1>Edit Produk</h1>
                    <p>Perbarui informasi produk Anda dengan mudah</p>
                </div>

                <!-- Success Message -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="form-card">
                    <div class="form-card-body">
                        <form method="POST" enctype="multipart/form-data" id="editProductForm">
                            <!-- Product Name -->
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-box"></i>Nama Produk
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>" 
                                       placeholder="Masukkan nama produk yang menarik" required>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i>Deskripsi Produk
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="5" 
                                          placeholder="Jelaskan kondisi dan detail produk Anda secara lengkap untuk menarik pembeli" required><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
                            </div>

                            <!-- Price and Category Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="form-label">
                                            <i class="fas fa-tag"></i>Harga
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>" 
                                                   placeholder="0" min="1" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id" class="form-label">
                                            <i class="fas fa-list"></i>Kategori
                                        </label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Pilih kategori produk</option>
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo (isset($_POST['category_id']) ? ($_POST['category_id'] == $category['id']) : ($product['category_id'] == $category['id'])) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Condition -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star"></i>Kondisi Barang
                                </label>
                                <div class="condition-options">
                                    <div class="condition-card">
                                        <input class="condition-input" type="radio" name="condition_item" 
                                               id="condition1" value="Seperti Baru" 
                                               <?php echo (isset($_POST['condition_item']) ? ($_POST['condition_item'] == 'Seperti Baru') : ($product['condition_item'] == 'Seperti Baru')) ? 'checked' : ''; ?>>
                                        <label class="condition-label" for="condition1">
                                            <i class="fas fa-gem" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                            <div class="badge bg-success">Seperti Baru</div>
                                        </label>
                                    </div>
                                    <div class="condition-card">
                                        <input class="condition-input" type="radio" name="condition_item" 
                                               id="condition2" value="Baik" 
                                               <?php echo (isset($_POST['condition_item']) ? ($_POST['condition_item'] == 'Baik') : ($product['condition_item'] == 'Baik')) ? 'checked' : ''; ?>>
                                        <label class="condition-label" for="condition2">
                                            <i class="fas fa-thumbs-up" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                            <div class="badge bg-primary">Baik</div>
                                        </label>
                                    </div>
                                    <div class="condition-card">
                                        <input class="condition-input" type="radio" name="condition_item" 
                                               id="condition3" value="Cukup Baik" 
                                               <?php echo (isset($_POST['condition_item']) ? ($_POST['condition_item'] == 'Cukup Baik') : ($product['condition_item'] == 'Cukup Baik')) ? 'checked' : ''; ?>>
                                        <label class="condition-label" for="condition3">
                                            <i class="fas fa-check-circle" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                            <div class="badge bg-warning text-dark">Cukup Baik</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Location and Contact Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location" class="form-label">
                                            <i class="fas fa-map-marker-alt"></i>Lokasi
                                        </label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?php echo htmlspecialchars($_POST['location'] ?? $product['location']); ?>" 
                                               placeholder="Contoh: Jakarta Selatan, DKI Jakarta" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone"></i>Kontak
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($_POST['contact_info'] ?? $product['contact_info']); ?>" 
                                               placeholder="WhatsApp: 0812-3456-7890" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Image Display -->
                            <?php if ($product['image']): ?>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-image"></i>Gambar Saat Ini
                                </label>
                                <div class="current-image">
                                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="Current product image" class="img-thumbnail" style="max-width: 250px; max-height: 200px; object-fit: cover;">
                                    <div class="image-overlay">
                                        <i class="fas fa-check"></i> Aktif
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Image Upload -->
                            <div class="form-group">
                                <label for="image" class="form-label">
                                    <i class="fas fa-camera"></i>Ganti Foto Produk
                                </label>
                                <div class="file-upload-area" id="fileUploadArea">
                                    <div class="file-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5>Klik atau drag & drop foto di sini</h5>
                                    <p class="text-muted">Format: JPEG, PNG, GIF. Maksimal 5MB</p>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" style="display: none;">
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengubah gambar
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="button-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <a href="my_products.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload area interactions
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('image');
    
    fileUploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.classList.add('drag-over');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('drag-over');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileUploadArea(files[0]);
        }
    });
    
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            updateFileUploadArea(e.target.files[0]);
        }
    });
    
    function updateFileUploadArea(file) {
        const fileUploadArea = document.getElementById('fileUploadArea');
        fileUploadArea.innerHTML = `
            <div class="file-upload-icon">
                <i class="fas fa-check-circle text-success"></i>
            </div>
            <h5 class="text-success">File dipilih: ${file.name}</h5>
            <p class="text-muted">Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
        `;
    }
    
    // Form submission with loading
    const form = document.getElementById('editProductForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    form.addEventListener('submit', function(e) {
        // Show loading overlay
        loadingOverlay.classList.add('active');
        
        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Validate form before submission
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Check if condition is selected
        const conditionInputs = form.querySelectorAll('input[name="condition_item"]');
        let conditionSelected = false;
        conditionInputs.forEach(input => {
            if (input.checked) {
                conditionSelected = true;
            }
        });
        
        if (!conditionSelected) {
            isValid = false;
            // Add visual feedback for condition selection
            document.querySelector('.condition-options').style.border = '2px solid var(--danger-color)';
            document.querySelector('.condition-options').style.borderRadius = '15px';
        } else {
            document.querySelector('.condition-options').style.border = 'none';
        }
        
        if (!isValid) {
            e.preventDefault();
            loadingOverlay.classList.remove('active');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Perubahan';
            
            // Show error message
            showNotification('Mohon lengkapi semua field yang wajib diisi', 'error');
        }
    });
    
    // Input validation on blur
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Price input formatting
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function() {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Format with thousands separator
        if (this.value) {
            const formattedValue = parseInt(this.value).toLocaleString('id-ID');
            // We don't actually change the display here to avoid cursor issues
            // But we could add a display element to show formatted price
        }
    });
    
    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const charCounter = document.createElement('div');
    charCounter.className = 'form-text text-end';
    charCounter.style.marginTop = '0.5rem';
    descriptionTextarea.parentNode.appendChild(charCounter);
    
    function updateCharCounter() {
        const currentLength = descriptionTextarea.value.length;
        const maxLength = 1000; // Set a reasonable max length
        charCounter.innerHTML = `${currentLength} karakter`;
        
        if (currentLength > maxLength * 0.8) {
            charCounter.style.color = 'var(--warning-color)';
        } else {
            charCounter.style.color = 'var(--secondary-color)';
        }
    }
    
    descriptionTextarea.addEventListener('input', updateCharCounter);
    updateCharCounter(); // Initial call
    
    // Auto-resize textarea
    function autoResize() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    }
    
    descriptionTextarea.addEventListener('input', autoResize);
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function() {
        // Basic phone number formatting for Indonesian numbers
        let value = this.value.replace(/\D/g, ''); // Remove non-digits
        
        if (value.startsWith('0')) {
            // Format: 0812-3456-7890
            value = value.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
        } else if (value.startsWith('62')) {
            // Format: +62 812-3456-7890
            value = value.replace(/(\d{2})(\d{3})(\d{4})(\d{4})/, '+$1 $2-$3-$4');
        }
        
        this.value = value;
    });
    
    // Location autocomplete suggestion (basic implementation)
    const locationInput = document.getElementById('location');
    const locationSuggestions = [
        'Jakarta Selatan, DKI Jakarta',
        'Jakarta Pusat, DKI Jakarta',
        'Jakarta Utara, DKI Jakarta',
        'Jakarta Barat, DKI Jakarta',
        'Jakarta Timur, DKI Jakarta',
        'Bandung, Jawa Barat',
        'Surabaya, Jawa Timur',
        'Medan, Sumatera Utara',
        'Yogyakarta, DI Yogyakarta',
        'Semarang, Jawa Tengah'
    ];
    
    locationInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        // This is a basic implementation - in a real app, you'd use a proper autocomplete library
        if (value.length > 2) {
            const suggestions = locationSuggestions.filter(location => 
                location.toLowerCase().includes(value)
            );
            // You could display these suggestions in a dropdown
        }
    });
    
    // Smooth scrolling to form errors
    function scrollToError() {
        const firstError = document.querySelector('.is-invalid, .alert-danger');
        if (firstError) {
            firstError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
    
    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Preview image before upload
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview
                const previewContainer = document.createElement('div');
                previewContainer.className = 'mt-3';
                previewContainer.innerHTML = `
                    <label class="form-label">
                        <i class="fas fa-eye"></i> Preview Gambar Baru
                    </label>
                    <div class="current-image">
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" 
                             style="max-width: 250px; max-height: 200px; object-fit: cover;">
                        <div class="image-overlay">
                            <i class="fas fa-clock"></i> Preview
                        </div>
                    </div>
                `;
                
                // Remove existing preview
                const existingPreview = form.querySelector('.image-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                previewContainer.className += ' image-preview';
                fileUploadArea.parentNode.insertBefore(previewContainer, fileUploadArea.nextSibling);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Add tooltips for better UX
    const tooltips = [
        { element: '#name', message: 'Gunakan nama yang menarik dan deskriptif' },
        { element: '#price', message: 'Berikan harga yang kompetitif' },
        { element: '#description', message: 'Jelaskan detail produk untuk menarik pembeli' }
    ];
    
    tooltips.forEach(tooltip => {
        const element = document.querySelector(tooltip.element);
        if (element) {
            element.setAttribute('title', tooltip.message);
            element.setAttribute('data-bs-toggle', 'tooltip');
        }
    });
    
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Add floating labels effect
    const floatingInputs = document.querySelectorAll('.form-control, .form-select');
    floatingInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (input.value) {
            input.parentNode.classList.add('focused');
        }
    });
});

// Add some CSS for the floating label effect
const style = document.createElement('style');
style.textContent = `
    .form-group.focused .form-label {
        transform: translateY(-5px);
        font-size: 0.9rem;
        color: var(--primary-color);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-valid, .form-select.is-valid {
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-valid:focus, .form-select.is-valid:focus {
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
`;
document.head.appendChild(style);
</script>