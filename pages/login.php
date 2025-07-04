<?php
session_start();
$page_title = "Masuk";
include_once '../includes/koneksi.php'; // jangan panggil header.php dulu!

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            
            $redirect = $_GET['redirect'] ?? '../index.php';
            header("Location: $redirect"); // HEADER DIJALANKAN SEBELUM ADA HTML
            exit();
        } else {
            $error = 'Username/email atau password salah!';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

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
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a7b7f 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    max-width: 450px;
    width: 100%;
    position: relative;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color), var(--success-color));
}

.login-header {
    text-align: center;
    padding: 2rem 2rem 1rem;
    position: relative;
}

.login-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 10px 30px rgba(7, 91, 94, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.login-icon i {
    color: white;
    font-size: 2rem;
}

.login-title {
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
}

.login-subtitle {
    color: var(--secondary-color);
    margin-bottom: 0;
    font-size: 1rem;
}

.login-form {
    padding: 0 2rem 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.input-group {
    position: relative;
}

.input-group-text {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    border: none;
    color: white;
    border-radius: 10px 0 0 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control {
    border: 2px solid #e9ecef;
    border-left: none;
    border-radius: 0 10px 10px 0;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(7, 91, 94, 0.25);
    background: white;
}

.form-control:focus + .input-group-text {
    background: var(--primary-color);
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--secondary-color);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.password-toggle:hover {
    background: var(--light-color);
    color: var(--primary-color);
}

.form-check {
    margin-bottom: 1rem;
}

.form-check-input {
    border: 2px solid #dee2e6;
    border-radius: 4px;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-label {
    color: var(--dark-color);
    font-size: 0.9rem;
}

.forgot-password {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.forgot-password:hover {
    color: var(--info-color);
    text-decoration: underline;
}

.btn-login {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(7, 91, 94, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

.register-link {
    text-align: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.register-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.register-link a:hover {
    color: var(--info-color);
    text-decoration: underline;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 1rem;
    margin-bottom: 1.5rem;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
    color: var(--danger-color);
    border-left: 4px solid var(--danger-color);
}

.alert-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.floating-elements {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
}

.floating-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s ease-in-out infinite;
}

.floating-circle:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.floating-circle:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 70%;
    right: 15%;
    animation-delay: 2s;
}

.floating-circle:nth-child(3) {
    width: 60px;
    height: 60px;
    bottom: 30%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@media (max-width: 576px) {
    .login-card {
        margin: 1rem;
        border-radius: 15px;
    }
    
    .login-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .login-form {
        padding: 0 1.5rem 1.5rem;
    }
    
    .login-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="floating-elements">
    <div class="floating-circle"></div>
    <div class="floating-circle"></div>
    <div class="floating-circle"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Masuk ke BerkahSecond - Tempat Jual Beli Barang Berkualitas</p>
        </div>
        
        <div class="login-form">
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Masukkan username atau email"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan password"
                               required>
                        <button class="password-toggle" 
                                type="button" 
                                onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn btn-login w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Akun
                </button>
                
                <div class="register-link">
                    <p class="mb-0">Belum punya akun? 
                        <a href="register.php">Daftar sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Add input focus animations
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'translateY(-2px)';
        this.parentElement.style.transition = 'transform 0.3s ease';
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'translateY(0)';
    });
});

// Add form validation feedback
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.btn-login');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    submitBtn.disabled = true;
    
    // Re-enable after 3 seconds if form doesn't redirect
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>