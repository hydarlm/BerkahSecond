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

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Buat Akun Baru</h2>
                        <p class="text-muted">Bergabunglah dengan BerkahSecond</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <div class="mt-2">
                            <a href="login.php" class="btn btn-success btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i>Login Sekarang
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Username unik"
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                           required>
                                </div>
                                <small class="text-muted">Minimal 3 karakter, tanpa spasi</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
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
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="full_name" 
                                       name="full_name" 
                                       placeholder="Nama lengkap Anda"
                                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="08xxxxxxxxx"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Password"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('password', 'toggleIcon1')">
                                        <i class="fas fa-eye" id="toggleIcon1"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Konfirmasi Password"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                        <i class="fas fa-eye" id="toggleIcon2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    Saya setuju dengan <a href="#" class="text-primary">Syarat & Ketentuan</a> 
                                    dan <a href="#" class="text-primary">Kebijakan Privasi</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Sudah punya akun? 
                            <a href="login.php" class="text-primary fw-bold">
                                Login di sini
                            </a>
                        </p>
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
    } else if (confirmPassword) {
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
</script>
