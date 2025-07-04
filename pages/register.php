<?php
$page_title = "Daftar";
include '../includes/header.php';

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    // Validasi
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Semua field yang bertanda * harus diisi!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak sama!';
    } else {
        // Cek username sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username sudah digunakan!';
        } else {
            // Cek email sudah ada
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email sudah digunakan!';
            } else {
                // Insert user baru
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                    $success = 'Pendaftaran berhasil! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan saat mendaftar!';
                }
            }
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
    background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 0;
}

.register-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(7, 91, 94, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    border: 1px solid rgba(7, 91, 94, 0.1);
}

.register-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(7, 91, 94, 0.15);
}

.register-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), #0a7075);
    color: white;
    padding: 40px 30px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(180deg); }
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.header-icon:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 0.3);
}

.header-icon i {
    font-size: 2.5rem;
    color: white;
}

.card-body {
    padding: 40px 30px;
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    position: relative;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(7, 91, 94, 0.1);
    background: white;
    outline: none;
}

.input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary-color);
    font-size: 18px;
    z-index: 10;
    transition: color 0.3s ease;
}

.input-group .form-control {
    padding-left: 55px;
}

.input-group:focus-within .input-icon {
    color: var(--primary-color);
}

.password-toggle {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--secondary-color);
    cursor: pointer;
    font-size: 18px;
    z-index: 10;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.form-help {
    font-size: 12px;
    color: var(--secondary-color);
    margin-top: 5px;
}

.alert {
    border: none;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 25px;
    font-weight: 500;
    position: relative;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-danger {
    background: linear-gradient(135deg, #ffeaea, #ffebee);
    color: var(--danger-color);
    border-left: 4px solid var(--danger-color);
}

.alert-success {
    background: linear-gradient(135deg, #e8f5e8, #f1f8f1);
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #0a7075);
    border: none;
    border-radius: 12px;
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(7, 91, 94, 0.3);
    background: linear-gradient(135deg, #0a7075, var(--primary-color));
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-success {
    background: var(--success-color);
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-1px);
}

.form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}

.form-check-input {
    width: 20px;
    height: 20px;
    border: 2px solid var(--primary-color);
    border-radius: 4px;
    cursor: pointer;
}

.form-check-input:checked {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-label {
    cursor: pointer;
    font-size: 14px;
    color: var(--dark-color);
}

.form-check-label a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.form-check-label a:hover {
    text-decoration: underline;
}

.login-link {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.login-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.login-link a:hover {
    color: #0a7075;
    text-decoration: underline;
}

.is-invalid {
    border-color: var(--danger-color) !important;
    background: #fff5f5;
}

.is-valid {
    border-color: var(--success-color) !important;
    background: #f0fff0;
}

.floating-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
}

.shape {
    position: absolute;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.shape:nth-child(1) {
    top: 10%;
    left: 10%;
    width: 80px;
    height: 80px;
    background: var(--primary-color);
    border-radius: 50%;
    animation-delay: 0s;
}

.shape:nth-child(2) {
    top: 60%;
    right: 10%;
    width: 60px;
    height: 60px;
    background: var(--info-color);
    border-radius: 50%;
    animation-delay: 2s;
}

.shape:nth-child(3) {
    bottom: 20%;
    left: 20%;
    width: 100px;
    height: 100px;
    background: var(--success-color);
    border-radius: 50%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@media (max-width: 768px) {
    .register-container {
        padding: 10px;
    }
    
    .card-header {
        padding: 30px 20px 20px;
    }
    
    .card-body {
        padding: 30px 20px;
    }
    
    .form-control {
        padding: 12px 15px;
    }
    
    .input-group .form-control {
        padding-left: 45px;
    }
    
    .input-icon {
        left: 15px;
        font-size: 16px;
    }
    
    .password-toggle {
        right: 15px;
        font-size: 16px;
    }
}
</style>

<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="register-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h2 class="mb-2">Bergabung dengan BerkahSecond</h2>
                        <p class="mb-0 opacity-75">Marketplace terpercaya untuk barang bekas berkualitas</p>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success" onclick="window.location.href='login.php'">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login Sekarang
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="registerForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username *</label>
                                        <div class="input-group">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="username" 
                                                   name="username" 
                                                   placeholder="Username unik Anda"
                                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                                   required>
                                        </div>
                                        <div class="form-help">Minimal 3 karakter, tanpa spasi</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email *</label>
                                        <div class="input-group">
                                            <i class="fas fa-envelope input-icon"></i>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="email" 
                                                   name="email" 
                                                   placeholder="email@example.com"
                                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="full_name" class="form-label">Nama Lengkap *</label>
                                <div class="input-group">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" 
                                           class="form-control" 
                                           id="full_name" 
                                           name="full_name" 
                                           placeholder="Masukkan nama lengkap Anda"
                                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <div class="input-group">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="08xxxxxxxxx"
                                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                <div class="form-help">Opsional - untuk kemudahan komunikasi</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password *</label>
                                        <div class="input-group">
                                            <i class="fas fa-lock input-icon"></i>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Buat password kuat"
                                                   required>
                                            <button type="button" 
                                                    class="password-toggle" 
                                                    onclick="togglePassword('password', 'toggleIcon1')">
                                                <i class="fas fa-eye" id="toggleIcon1"></i>
                                            </button>
                                        </div>
                                        <div class="form-help">Minimal 6 karakter</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                        <div class="input-group">
                                            <i class="fas fa-lock input-icon"></i>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="confirm_password" 
                                                   name="confirm_password" 
                                                   placeholder="Ulangi password"
                                                   required>
                                            <button type="button" 
                                                    class="password-toggle" 
                                                    onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                                <i class="fas fa-eye" id="toggleIcon2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    Saya setuju dengan <a href="#">Syarat & Ketentuan</a> 
                                    dan <a href="#">Kebijakan Privasi</a>
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                </button>
                            </div>
                        </form>
                        
                        <div class="login-link">
                            <p class="mb-0">
                                Sudah punya akun? 
                                <a href="login.php">Login di sini</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validasi password secara real-time
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else if (confirmPassword && password === confirmPassword) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else {
        this.classList.remove('is-invalid', 'is-valid');
    }
});

// Validasi username tanpa spasi
document.getElementById('username').addEventListener('input', function() {
    this.value = this.value.replace(/\s/g, '');
});

// Validasi strength password
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length >= 6) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else if (password.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-invalid', 'is-valid');
    }
    
    // Re-check confirm password
    if (confirmPassword) {
        const confirmField = document.getElementById('confirm_password');
        if (password === confirmPassword) {
            confirmField.classList.add('is-valid');
            confirmField.classList.remove('is-invalid');
        } else {
            confirmField.classList.add('is-invalid');
            confirmField.classList.remove('is-valid');
        }
    }
});

// Validasi email
document.getElementById('email').addEventListener('input', function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (emailRegex.test(email)) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else if (email.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-invalid', 'is-valid');
    }
});

// Validasi username
document.getElementById('username').addEventListener('input', function() {
    const username = this.value;
    
    if (username.length >= 3) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else if (username.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-invalid', 'is-valid');
    }
});

// Animasi loading pada submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
    submitBtn.disabled = true;
    
    // Simulasi loading (hapus ini jika tidak diperlukan)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});
</script>