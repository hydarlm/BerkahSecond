<?php
ob_start(); // ⬅️ TAMBAHKAN BARIS INI
$page_title = "Jual Barang";
include '../includes/header.php';

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

.main-container {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7b7f 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(7, 91, 94, 0.1);
    overflow: hidden;
    position: relative;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color), var(--success-color));
}

.header-section {
    text-align: center;
    margin-bottom: 3rem;
    color: white;
}

.header-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.header-section p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
}

.input-group-text {
    background: var(--primary-color);
    color: white;
    border: 2px solid var(--primary-color);
    border-radius: 12px 0 0 12px;
    font-weight: 600;
}

.condition-options {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.condition-card {
    flex: 1;
    min-width: 150px;
    background: white;
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.condition-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.condition-card input[type="radio"] {
    display: none;
}

.condition-card.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color), #0a7b7f);
    color: white;
}

.condition-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.condition-badge.excellent {
    background: var(--success-color);
    color: white;
}

.condition-badge.good {
    background: var(--primary-color);
    color: white;
}

.condition-badge.fair {
    background: var(--warning-color);
    color: var(--dark-color);
}

.file-upload-area {
    border: 3px dashed #e9ecef;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(7, 91, 94, 0.05);
}

.file-upload-area.drag-over {
    border-color: var(--primary-color);
    background: rgba(7, 91, 94, 0.1);
}

.file-upload-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #0a7b7f);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(7, 91, 94, 0.3);
}

.btn-outline-secondary {
    border: 2px solid var(--secondary-color);
    color: var(--secondary-color);
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: var(--secondary-color);
    color: white;
    transform: translateY(-2px);
}

.tips-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 20px;
    padding: 2rem;
    margin-top: 2rem;
    position: relative;
    overflow: hidden;
}

.tips-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--warning-color), var(--success-color));
}

.tip-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.tip-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.tip-icon {
    color: var(--success-color);
    font-size: 1.2rem;
    margin-right: 1rem;
}

.alert {
    border-radius: 15px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    border: none;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #34ce57);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #e74c3c);
    color: white;
}

.progress-bar {
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
    border-radius: 2px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .condition-options {
        flex-direction: column;
    }
    
    .condition-card {
        min-width: 100%;
    }
    
    .header-section h1 {
        font-size: 2rem;
    }
}
</style>

<div class="main-container">
    <div class="progress-bar" id="progressBar"></div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Page Header -->
                <div class="header-section">
                    <h1>🛒 Jual Barang Anda</h1>
                    <p>Berikan kesempatan kedua untuk barang berkualitas Anda</p>
                </div>

                <!-- Success Message -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Form -->
                <div class="form-card">
                    <div class="p-4">
                        <form method="POST" enctype="multipart/form-data" id="sellForm">
                            <!-- Product Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-box"></i>Nama Produk
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                       placeholder="Masukkan nama produk yang menarik" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i>Deskripsi
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Jelaskan kondisi dan detail produk Anda dengan lengkap" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>

                            <!-- Price and Category Row -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-tag"></i>Harga
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
                                               placeholder="0" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-list"></i>Kategori
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
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-star"></i>Kondisi Barang
                                </label>
                                <div class="condition-options">
                                    <div class="condition-card" data-condition="Seperti Baru">
                                        <input type="radio" name="condition_item" value="Seperti Baru" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Seperti Baru') ? 'checked' : ''; ?>>
                                        <div class="condition-badge excellent">⭐ Seperti Baru</div>
                                        <small>Hampir tidak ada tanda penggunaan</small>
                                    </div>
                                    <div class="condition-card" data-condition="Baik">
                                        <input type="radio" name="condition_item" value="Baik" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Baik') ? 'checked' : ''; ?>>
                                        <div class="condition-badge good">👍 Baik</div>
                                        <small>Sedikit tanda penggunaan normal</small>
                                    </div>
                                    <div class="condition-card" data-condition="Cukup Baik">
                                        <input type="radio" name="condition_item" value="Cukup Baik" 
                                               <?php echo (isset($_POST['condition_item']) && $_POST['condition_item'] == 'Cukup Baik') ? 'checked' : ''; ?>>
                                        <div class="condition-badge fair">⚡ Cukup Baik</div>
                                        <small>Tanda penggunaan yang terlihat</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Location and Contact Row -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="location" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i>Lokasi
                                    </label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" 
                                           placeholder="Kota, Provinsi" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_info" class="form-label">
                                        <i class="fas fa-phone"></i>Kontak
                                    </label>
                                    <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                           value="<?php echo htmlspecialchars($_POST['contact_info'] ?? ''); ?>" 
                                           placeholder="WhatsApp/Telepon" required>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="image" class="form-label">
                                    <i class="fas fa-camera"></i>Foto Produk
                                </label>
                                <div class="file-upload-area" id="fileUploadArea">
                                    <div class="file-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="mb-2"><strong>Klik untuk pilih foto atau drag & drop</strong></p>
                                    <p class="text-muted mb-0">Format: JPEG, PNG, GIF. Maksimal 5MB</p>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" style="display: none;">
                                </div>
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 200px; border-radius: 10px;">
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
                <div class="tips-section">
                    <h5 class="fw-bold mb-4 text-center">
                        <i class="fas fa-lightbulb me-2" style="color: var(--warning-color);"></i>
                        Tips Sukses Jual Barang
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="tip-item">
                                <i class="fas fa-camera tip-icon"></i>
                                <span>Gunakan foto yang jelas dan menarik dari berbagai sudut</span>
                            </div>
                            <div class="tip-item">
                                <i class="fas fa-edit tip-icon"></i>
                                <span>Tulis deskripsi yang detail dan jujur tentang kondisi barang</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tip-item">
                                <i class="fas fa-dollar-sign tip-icon"></i>
                                <span>Tentukan harga yang kompetitif dengan riset pasar</span>
                            </div>
                            <div class="tip-item">
                                <i class="fas fa-phone tip-icon"></i>
                                <span>Berikan kontak yang aktif dan mudah dihubungi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Condition card selection
    const conditionCards = document.querySelectorAll('.condition-card');
    conditionCards.forEach(card => {
        card.addEventListener('click', function() {
            conditionCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
        
        // Check if already selected
        const radio = card.querySelector('input[type="radio"]');
        if (radio.checked) {
            card.classList.add('selected');
        }
    });

    // File upload handling
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('drag-over');
    });
    
    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('drag-over');
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Form progress tracking
    const form = document.getElementById('sellForm');
    const progressBar = document.getElementById('progressBar');
    const formFields = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    function updateProgress() {
        let filledFields = 0;
        formFields.forEach(field => {
            if (field.type === 'radio') {
                if (form.querySelector(`input[name="${field.name}"]:checked`)) {
                    filledFields++;
                }
            } else if (field.value.trim() !== '') {
                filledFields++;
            }
        });
        
        const progress = (filledFields / formFields.length) * 100;
        progressBar.style.width = progress + '%';
    }

    formFields.forEach(field => {
        field.addEventListener('input', updateProgress);
        field.addEventListener('change', updateProgress);
    });

    // Initial progress update
    updateProgress();

    // Form animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'slideUp 0.6s ease-out';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.form-card, .tips-section').forEach(el => {
        observer.observe(el);
    });

    // Real-time price formatting
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value) {
            // Format with thousand separators
            value = parseInt(value).toLocaleString('id-ID');
            // Remove the formatting for the actual input value
            e.target.value = value.replace(/\./g, '');
        }
    });
});
</script>