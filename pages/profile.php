<?php
$page_title = 'Profil';

// Coba include koneksi.php dari berbagai lokasi
$koneksi_files = [
    '../koneksi.php',
    'koneksi.php',
    '../includes/koneksi.php',
    '../../koneksi.php'
];

$koneksi_loaded = false;
foreach ($koneksi_files as $file) {
    if (file_exists($file)) {
        include_once $file;
        $koneksi_loaded = true;
        break;
    }
}

if (!$koneksi_loaded) {
    die('File koneksi.php tidak ditemukan. Pastikan file koneksi.php ada di folder yang benar.');
}

// Pastikan koneksi database tersedia (PDO)
if (!isset($pdo) || !$pdo) {
    die('Koneksi database gagal. Periksa konfigurasi database di koneksi.php');
}

// Redirect jika belum login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user = getCurrentUser();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (empty($full_name) || empty($email)) {
        $error = 'Nama lengkap dan email harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Cek apakah email sudah digunakan user lain
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email sudah digunakan oleh user lain!';
        } else {
            // Update profil
            $update_fields = "full_name = ?, email = ?, phone = ?, address = ?";
            $params = [$full_name, $email, $phone, $address];
            $param_types = "ssss";
            
            // Jika ada password baru
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Password saat ini harus diisi untuk mengubah password!';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'Konfirmasi password tidak cocok!';
                } elseif (strlen($new_password) < 6) {
                    $error = 'Password baru minimal 6 karakter!';
                } else {
                    // Verifikasi password saat ini
                    if (!password_verify($current_password, $current_user['password'])) {
                        $error = 'Password saat ini salah!';
                    } else {
                        // Tambahkan password ke update
                        $update_fields .= ", password = ?";
                        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
                        $param_types .= "s";
                    }
                }
            }
            
            if (empty($error)) {
                $params[] = $_SESSION['user_id'];
                $param_types .= "i";
                
            if (empty($error)) {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET {$update_fields} WHERE id = ?");
                    $stmt->execute([...$params]);
                    
                    $message = 'Profil berhasil diperbarui!';
                    // Refresh current user data
                    $current_user = getCurrentUser();
                } catch (PDOException $e) {
                    $error = 'Gagal memperbarui profil: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Gagal mempersiapkan query untuk validasi email!';
        }
        }
    }
}

include_once '../includes/header.php';
?>

<style>
/* Smooth scrolling fix */
html {
    scroll-behavior: smooth;
    overflow-x: hidden;
}

body {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

/* Prevent scroll issues from animations */
* {
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    -ms-backface-visibility: hidden;
    backface-visibility: hidden;
}

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

.profile-container {
    background: linear-gradient(135deg, var(--primary-color), #0a7075);
    min-height: 100vh;
    padding: 2rem 0;
    /* Prevent layout shift during animations */
    will-change: auto;
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
    color: white;
}

.profile-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.profile-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    /* Smooth transition without affecting scroll */
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    margin-bottom: 2rem;
    /* Prevent transform from affecting scroll */
    will-change: transform;
}

.profile-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.card-header-custom {
    background: linear-gradient(135deg, var(--primary-color), #0a7075);
    color: white;
    padding: 1.5rem;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header-custom::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.card-header-custom h4 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    position: relative;
    z-index: 1;
}

.card-body-custom {
    padding: 2rem;
}

.form-group-animated {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-control-custom {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    /* Lighter transition for better scrolling */
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background-color 0.15s ease-in-out;
    background: var(--light-color);
    will-change: border-color, box-shadow;
}

.form-control-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
    background: white;
    /* Remove transform from focus state */
    outline: none;
}

.form-label-custom {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label-custom i {
    color: var(--primary-color);
    font-size: 1.1rem;
}

.btn-custom {
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    /* Lighter transition */
    transition: all 0.2s ease-out;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    will-change: transform;
}

.btn-primary-custom {
    background: linear-gradient(135deg, var(--primary-color), #0a7075);
    color: white;
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(7, 91, 94, 0.25);
}

.btn-secondary-custom {
    background: linear-gradient(135deg, var(--secondary-color), #5a6268);
    color: white;
}

.btn-secondary-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(108, 117, 125, 0.25);
}

.alert-custom {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    /* Smoother animation */
    animation: slideInDown 0.3s ease-out;
}

.alert-success-custom {
    background: linear-gradient(135deg, var(--success-color), #34ce57);
    color: white;
}

.alert-danger-custom {
    background: linear-gradient(135deg, var(--danger-color), #e55a5a);
    color: white;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    /* Lighter transition */
    transition: transform 0.2s ease-out, border-color 0.2s ease-out, box-shadow 0.2s ease-out;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
    will-change: transform;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
}

.stats-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary-color);
    box-shadow: 0 12px 25px rgba(7, 91, 94, 0.15);
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.stats-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--secondary-color);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.password-section {
    background: linear-gradient(135deg, var(--light-color), #ffffff);
    border-radius: 15px;
    padding: 2rem;
    margin-top: 2rem;
    border: 2px solid #e9ecef;
    /* Lighter transition */
    transition: border-color 0.2s ease-out, box-shadow 0.2s ease-out;
}

.password-section:hover {
    border-color: var(--primary-color);
    box-shadow: 0 8px 20px rgba(7, 91, 94, 0.08);
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-card {
    background: linear-gradient(135deg, var(--info-color), #20c3d8);
    color: white;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(23, 162, 184, 0.2);
}

.info-card .card-header-custom {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.info-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    backdrop-filter: blur(5px);
}

.info-item strong {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Smoother animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animated-card {
    animation: fadeInUp 0.4s ease-out;
}

.animated-card:nth-child(2) {
    animation-delay: 0.1s;
}

.animated-card:nth-child(3) {
    animation-delay: 0.2s;
}

.floating-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    opacity: 0.1;
    font-size: 3rem;
    pointer-events: none;
    /* Lighter transition */
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
}

.form-group-animated:hover .floating-icon {
    opacity: 0.3;
    transform: translateY(-50%) scale(1.05);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .profile-header h1 {
        font-size: 2rem;
    }
    
    .card-body-custom {
        padding: 1.5rem;
    }
    
    .stats-number {
        font-size: 2rem;
    }
    
    /* Reduce hover effects on mobile */
    .profile-card:hover,
    .stats-card:hover,
    .password-section:hover {
        transform: none;
    }
    
    .btn-custom:hover {
        transform: none;
    }
}

/* Prevent iOS bounce scrolling issues */
@media (max-width: 768px) {
    body {
        -webkit-overflow-scrolling: touch;
        overflow-x: hidden;
    }
}

/* Add focus styles for better accessibility */
.form-control-custom:focus,
.btn-custom:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>

<div class="profile-container">
    <div class="container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle me-3"></i>Profil Saya</h1>
            <p>Kelola informasi akun dan pengaturan profil Anda</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Main Profile Card -->
                <div class="profile-card animated-card">
                    <div class="card-header-custom">
                        <h4><i class="fas fa-edit me-2"></i>Edit Profil</h4>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($message): ?>
                            <div class="alert-custom alert-success-custom" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert-custom alert-danger-custom" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-animated">
                                        <label for="full_name" class="form-label-custom">
                                            <i class="fas fa-user"></i>Nama Lengkap *
                                        </label>
                                        <input type="text" class="form-control form-control-custom" id="full_name" name="full_name" 
                                               value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required>
                                        <i class="fas fa-user floating-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-animated">
                                        <label for="email" class="form-label-custom">
                                            <i class="fas fa-envelope"></i>Email *
                                        </label>
                                        <input type="email" class="form-control form-control-custom" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                                        <i class="fas fa-envelope floating-icon"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-animated">
                                        <label for="phone" class="form-label-custom">
                                            <i class="fas fa-phone"></i>Nomor Telepon
                                        </label>
                                        <input type="tel" class="form-control form-control-custom" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>" 
                                               placeholder="08xxxxxxxxxx">
                                        <i class="fas fa-phone floating-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-animated">
                                        <label for="username" class="form-label-custom">
                                            <i class="fas fa-at"></i>Username
                                        </label>
                                        <input type="text" class="form-control form-control-custom" id="username" 
                                               value="<?php echo htmlspecialchars($current_user['username']); ?>" 
                                               readonly disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <small class="text-muted">Username tidak dapat diubah</small>
                                        <i class="fas fa-lock floating-icon"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group-animated">
                                <label for="address" class="form-label-custom">
                                    <i class="fas fa-map-marker-alt"></i>Alamat
                                </label>
                                <textarea class="form-control form-control-custom" id="address" name="address" rows="3" 
                                          placeholder="Masukkan alamat lengkap Anda"><?php echo htmlspecialchars($current_user['address'] ?? ''); ?></textarea>
                                <i class="fas fa-map-marker-alt floating-icon"></i>
                            </div>
                            
                            <div class="password-section">
                                <div class="section-title">
                                    <i class="fas fa-lock"></i>Ubah Password
                                </div>
                                <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group-animated">
                                            <label for="current_password" class="form-label-custom">
                                                <i class="fas fa-key"></i>Password Saat Ini
                                            </label>
                                            <input type="password" class="form-control form-control-custom" id="current_password" name="current_password">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group-animated">
                                            <label for="new_password" class="form-label-custom">
                                                <i class="fas fa-lock"></i>Password Baru
                                            </label>
                                            <input type="password" class="form-control form-control-custom" id="new_password" name="new_password" 
                                                   minlength="6" placeholder="Minimal 6 karakter">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group-animated">
                                            <label for="confirm_password" class="form-label-custom">
                                                <i class="fas fa-check-circle"></i>Konfirmasi Password
                                            </label>
                                            <input type="password" class="form-control form-control-custom" id="confirm_password" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="../index.php" class="btn btn-custom btn-secondary-custom">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-custom btn-primary-custom">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Information Card -->
                <div class="profile-card info-card animated-card">
                    <div class="card-header-custom">
                        <h4><i class="fas fa-info-circle me-2"></i>Informasi Akun</h4>
                    </div>
                    <div class="card-body-custom">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <strong>Bergabung sejak:</strong>
                                    <span><?php echo date('d F Y', strtotime($current_user['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <strong>Terakhir login:</strong>
                                    <span><?php echo isset($current_user['last_login']) ? date('d F Y H:i', strtotime($current_user['last_login'])) : 'Belum pernah'; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        // Hitung jumlah produk user
                        $product_count = 0;
                        try {
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            $result = $stmt->fetch();
                            $product_count = $result['total'];
                        } catch (PDOException $e) {
                            $product_count = 0;
                        }
                        ?>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo $product_count; ?></div>
                                    <div class="stats-label">Produk Dijual</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="stats-number">
                                        <?php 
                                            if (isset($current_user['status'])) {
                                                echo $current_user['status'] === 'active' ? 'Aktif' : 'Non-aktif';
                                            } else {
                                                echo 'Aktif';
                                            }
                                        ?>
                                    </div>
                                    <div class="stats-label">Status Akun</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scrolling and performance optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Optimize animations for better performance
    const cards = document.querySelectorAll('.animated-card');
    
    // Use requestAnimationFrame for better performance
    function animateCards() {
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                requestAnimationFrame(() => {
                    card.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, index * 100);
        });
    }
    
    animateCards();
    
    // Debounce scroll events for better performance
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(function() {
            // Any scroll-related code here
        }, 10);
    });
});

// Optimized password validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    // Use lighter visual feedback
    if (newPassword !== confirmPassword && confirmPassword !== '') {
        this.style.borderColor = 'var(--danger-color)';
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.style.borderColor = 'var(--success-color)';
        this.setCustomValidity('');
    }
});

// Optimized form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const currentPassword = document.getElementById('current_password').value;
    
    if (newPassword && !currentPassword) {
        e.preventDefault();
        showCustomAlert('Password saat ini harus diisi untuk mengubah password', 'error');
        return;
    }
    
    if (newPassword && newPassword !== confirmPassword) {
        e.preventDefault();
        showCustomAlert('Konfirmasi password tidak cocok', 'error');
        return;
    }
    
    // Lighter loading animation
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Optimized alert function
function showCustomAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-custom alert-${type === 'error' ? 'danger' : 'success'}-custom`;
    alertDiv.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>${message}`;
    
    const form = document.querySelector('form');
    form.insertBefore(alertDiv, form.firstChild);
    
    // Smooth scroll to top to show alert
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 5000);
}

// Optimized hover effects (use passive event listeners)
document.addEventListener('DOMContentLoaded', function() {
    // Passive event listeners for better performance
    const hoverElements = document.querySelectorAll('.profile-card, .stats-card, .btn-custom');
    
    hoverElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.willChange = 'transform';
        }, { passive: true });
        
        element.addEventListener('mouseleave', function() {
            this.style.willChange = 'auto';
        }, { passive: true });
    });
});

// Enhanced form validation with real-time feedback
function initFormValidation() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('input', function() {
            validateField(this);
        });
        
        // Blur validation
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let message = '';
        
        // Remove existing validation styling
        field.classList.remove('is-valid', 'is-invalid');
        
        switch (fieldName) {
            case 'full_name':
                if (value.length < 2) {
                    isValid = false;
                    message = 'Nama lengkap minimal 2 karakter';
                }
                break;
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = 'Format email tidak valid';
                }
                break;
                
            case 'phone':
                if (value && !/^[0-9+\-\s()]+$/.test(value)) {
                    isValid = false;
                    message = 'Nomor telepon hanya boleh berisi angka';
                }
                break;
                
            case 'new_password':
                if (value && value.length < 6) {
                    isValid = false;
                    message = 'Password minimal 6 karakter';
                }
                break;
                
            case 'confirm_password':
                const newPassword = document.getElementById('new_password').value;
                if (value && value !== newPassword) {
                    isValid = false;
                    message = 'Konfirmasi password tidak cocok';
                }
                break;
        }
        
        // Apply validation styling
        if (value && field.hasAttribute('required') || value) {
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
            
            // Show/hide validation message
            showValidationMessage(field, message, isValid);
        }
    }
    
    function showValidationMessage(field, message, isValid) {
        // Remove existing message
        const existingMessage = field.parentNode.querySelector('.validation-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Add new message if invalid
        if (!isValid && message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'validation-message text-danger small mt-1';
            messageDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;
            field.parentNode.appendChild(messageDiv);
        }
    }
}

// Initialize form validation
initFormValidation();

// Advanced password strength indicator
function initPasswordStrengthIndicator() {
    const passwordInput = document.getElementById('new_password');
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength-indicator mt-2';
    strengthIndicator.innerHTML = `
        <div class="strength-bar">
            <div class="strength-fill"></div>
        </div>
        <div class="strength-text small text-muted"></div>
    `;
    
    passwordInput.parentNode.appendChild(strengthIndicator);
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updateStrengthIndicator(strength);
    });
    
    function calculatePasswordStrength(password) {
        if (!password) return { score: 0, text: '' };
        
        let score = 0;
        let feedback = [];
        
        // Length check
        if (password.length >= 8) score += 2;
        else if (password.length >= 6) score += 1;
        else feedback.push('minimal 6 karakter');
        
        // Character variety
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        
        // Determine strength level
        let level, text, color;
        if (score < 2) {
            level = 'weak';
            text = 'Lemah';
            color = '#dc3545';
        } else if (score < 4) {
            level = 'medium';
            text = 'Sedang';
            color = '#ffc107';
        } else if (score < 6) {
            level = 'strong';
            text = 'Kuat';
            color = '#28a745';
        } else {
            level = 'very-strong';
            text = 'Sangat Kuat';
            color = '#20c997';
        }
        
        return { score, level, text, color, feedback };
    }
    
    function updateStrengthIndicator(strength) {
        const fill = strengthIndicator.querySelector('.strength-fill');
        const text = strengthIndicator.querySelector('.strength-text');
        
        if (strength.score === 0) {
            fill.style.width = '0%';
            text.textContent = '';
            return;
        }
        
        const percentage = (strength.score / 6) * 100;
        fill.style.width = percentage + '%';
        fill.style.backgroundColor = strength.color;
        text.textContent = `Kekuatan password: ${strength.text}`;
        text.style.color = strength.color;
    }
}

// Initialize password strength indicator
initPasswordStrengthIndicator();

// Auto-save functionality (draft)
function initAutoSave() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input:not([type="password"]), textarea');
    const autoSaveKey = 'profile_draft_' + (window.currentUserId || 'user');
    
    // Load saved draft
    loadDraft();
    
    // Save draft on input
    inputs.forEach(input => {
        input.addEventListener('input', debounce(saveDraft, 1000));
    });
    
    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        clearDraft();
    });
    
    function saveDraft() {
        const draftData = {};
        inputs.forEach(input => {
            draftData[input.name] = input.value;
        });
        
        try {
            localStorage.setItem(autoSaveKey, JSON.stringify(draftData));
            showAutoSaveIndicator();
        } catch (e) {
            console.warn('Could not save draft:', e);
        }
    }
    
    function loadDraft() {
        try {
            const savedDraft = localStorage.getItem(autoSaveKey);
            if (savedDraft) {
                const draftData = JSON.parse(savedDraft);
                inputs.forEach(input => {
                    if (draftData[input.name] && !input.value) {
                        input.value = draftData[input.name];
                    }
                });
            }
        } catch (e) {
            console.warn('Could not load draft:', e);
        }
    }
    
    function clearDraft() {
        try {
            localStorage.removeItem(autoSaveKey);
        } catch (e) {
            console.warn('Could not clear draft:', e);
        }
    }
    
    function showAutoSaveIndicator() {
        // Remove existing indicator
        const existingIndicator = document.querySelector('.autosave-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        // Show new indicator
        const indicator = document.createElement('div');
        indicator.className = 'autosave-indicator';
        indicator.innerHTML = '<i class="fas fa-cloud-upload-alt me-1"></i>Draft tersimpan';
        indicator.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(indicator);
        
        // Animate in
        setTimeout(() => {
            indicator.style.opacity = '1';
            indicator.style.transform = 'translateY(0)';
        }, 10);
        
        // Animate out
        setTimeout(() => {
            indicator.style.opacity = '0';
            indicator.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                indicator.remove();
            }, 300);
        }, 2000);
    }
}

// Initialize auto-save
initAutoSave();

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Enhanced image upload preview (if avatar upload is added later)
function initImageUpload() {
    const imageUpload = document.getElementById('avatar_upload');
    if (!imageUpload) return;
    
    const preview = document.getElementById('avatar_preview');
    const uploadArea = document.querySelector('.upload-area');
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageUpload(files[0]);
        }
    });
    
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleImageUpload(file);
        }
    });
    
    function handleImageUpload(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showCustomAlert('File harus berupa gambar', 'error');
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showCustomAlert('Ukuran file maksimal 5MB', 'error');
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').dispatchEvent(new Event('submit'));
    }
    
    // Escape to clear focus
    if (e.key === 'Escape') {
        document.activeElement.blur();
    }
});

// Smooth scroll to validation errors
function scrollToFirstError() {
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        firstError.focus();
    }
}

// Enhanced form submission with better UX
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Check for validation errors
    const errors = this.querySelectorAll('.is-invalid');
    if (errors.length > 0) {
        e.preventDefault();
        scrollToFirstError();
        showCustomAlert('Mohon perbaiki kesalahan pada form', 'error');
        return;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Add loading overlay
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    overlay.innerHTML = `
        <div class="loading-spinner" style="
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        ">
            <i class="fas fa-spinner fa-spin fa-2x mb-3" style="color: var(--primary-color);"></i>
            <div>Menyimpan perubahan...</div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Remove loading state if form submission fails
    setTimeout(() => {
        if (document.body.contains(overlay)) {
            overlay.remove();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 10000); // 10 second timeout
});

// Add CSS for validation styles
const validationStyles = document.createElement('style');
validationStyles.textContent = `
    .form-control.is-valid {
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-invalid {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .validation-message {
        animation: slideInDown 0.3s ease;
    }
    
    .password-strength-indicator {
        margin-top: 0.5rem;
    }
    
    .strength-bar {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }
    
    .strength-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background-color 0.3s ease;
        border-radius: 2px;
    }
    
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .upload-area:hover,
    .upload-area.drag-over {
        border-color: var(--primary-color);
        background: rgba(7, 91, 94, 0.05);
    }
    
    .loading-overlay {
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;

document.head.appendChild(validationStyles);

// Initialize tooltips (if Bootstrap tooltips are available)
if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Performance monitoring and optimization
function initPerformanceMonitoring() {
    // Monitor long tasks
    if ('PerformanceObserver' in window) {
        const observer = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            entries.forEach((entry) => {
                if (entry.duration > 50) {
                    console.warn('Long task detected:', entry);
                }
            });
        });
        
        observer.observe({ entryTypes: ['longtask'] });
    }
    
    // Monitor layout shifts
    if ('PerformanceObserver' in window && 'LayoutShift' in window) {
        const observer = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            entries.forEach((entry) => {
                if (entry.value > 0.1) {
                    console.warn('Layout shift detected:', entry);
                }
            });
        });
        
        observer.observe({ entryTypes: ['layout-shift'] });
    }
}

// Initialize performance monitoring in development
if (location.hostname === 'localhost' || location.hostname === '127.0.0.1') {
    initPerformanceMonitoring();
}

// Cleanup function for better memory management
window.addEventListener('beforeunload', function() {
    // Clear any timers or intervals
    clearTimeout(window.scrollTimeout);
    
    // Remove event listeners if needed
    document.removeEventListener('keydown', arguments.callee);
});

console.log('Profile page JavaScript loaded successfully');
</script>
