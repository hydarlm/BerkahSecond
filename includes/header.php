<?php
include_once 'koneksi.php';
$current_user = getCurrentUser();

if (strpos($_SERVER['SCRIPT_NAME'], 'index.php')) {
    $basepath = dirname($_SERVER["SCRIPT_NAME"],1).'/';
}else {
    $basepath = dirname($_SERVER["SCRIPT_NAME"],2).'/'; 
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>BerkahSecond</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $basepath;?>css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-" href="<?=$basepath;?>index.php">
                <i class="fas fa-recycle me-2"></i>BerkahSecond
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?=$basepath;?>index.php">
                        <i class="fas fa-home me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?=$basepath;?>pages/katalog.php">
                        <i class="fas fa-th-large me-1"></i>Katalog
                    </a>
                </li>
            </ul>
</div>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$basepath;?>pages/tambah_produk.php">
                                <i class="fas fa-plus-circle me-1"></i>Jual Barang
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($current_user['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?=$basepath;?>pages/profile.php">
                                    <i class="fas fa-user-circle me-2"></i>Profil
                                </a></li>
                                <li><a class="dropdown-item" href="<?=$basepath;?>pages/my_products.php">
                                    <i class="fas fa-box me-2"></i>Barang Saya
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?=$basepath;?>pages/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$basepath;?>pages/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link2 btn btn-primary text-dark ms-2 px-3" href="<?=$basepath;?>pages/register.php">
                                <i class="fas fa-user-plus me-1 "></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>