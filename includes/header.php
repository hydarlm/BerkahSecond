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

        * {
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0a7a7e 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(7, 91, 94, 0.95);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-brand:hover {
            color: var(--warning-color) !important;
            transform: translateY(-2px);
        }

        .navbar-brand i {
            background: linear-gradient(45deg, var(--success-color), var(--info-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .navbar-nav .nav-link:hover::before {
            left: 100%;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
        }

        .btn-register {
            background: linear-gradient(45deg, var(--success-color), var(--info-color));
            border: none;
            color: white !important;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .dropdown-menu {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { 
                opacity: 0; 
                transform: translateY(-10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, var(--light-color), rgba(7, 91, 94, 0.1));
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .dropdown-item.text-danger:hover {
            background: linear-gradient(90deg, var(--light-color), rgba(220, 53, 69, 0.1));
            color: var(--danger-color);
        }

        .dropdown-divider {
            margin: 0.5rem 1rem;
            border-top-color: rgba(108, 117, 125, 0.2);
        }

        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover {
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-icons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link i {
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        @media (max-width: 991px) {
            .navbar-nav {
                padding-top: 1rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
            }
            
            .btn-register {
                margin-top: 0.5rem;
                width: 100%;
            }
        }

        /* User avatar styling */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--warning-color), var(--info-color));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }

        /* Notification badge */
        .notification-badge {
            position: relative;
        }

        .notification-badge::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--danger-color);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?=$basepath;?>index.php">
                <i class="fas fa-recycle me-2"></i>BerkahSecond
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="mx-auto">
                    <ul class="navbar-nav nav-icons">
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$basepath;?>index.php">
                                <i class="fas fa-home"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$basepath;?>pages/katalog.php">
                                <i class="fas fa-th-large"></i>Katalog
                            </a>
                        </li>
                    </ul>
                </div>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="user-avatar">
                                    <?php echo strtoupper(substr($current_user['full_name'] ?? 'U', 0, 1)); ?>
                                </span>
                                <?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?=$basepath;?>pages/profile.php">
                                    <i class="fas fa-user-circle me-2"></i>Profil Saya
                                </a></li>
                                <li><a class="dropdown-item" href="<?=$basepath;?>pages/tambah_produk.php">
                                    <i class="fas fa-plus-circle me-2"></i>Jual Barang
                                </a></li>
                                <li><a class="dropdown-item notification-badge" href="<?=$basepath;?>pages/my_products.php">
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
                                <i class="fas fa-sign-in-alt"></i>Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-register ms-2" href="<?=$basepath;?>pages/register.php">
                                <i class="fas fa-user-plus me-1"></i>Daftar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Active nav link
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>