-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 04, 2025 at 06:36 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `berkahsecond`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `icon` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'Elektronik', 'Perangkat elektronik dan gadget', 'fas fa-mobile-alt', '2025-07-03 09:56:14'),
(2, 'Fashion', 'Pakaian, sepatu, dan aksesoris', 'fas fa-tshirt', '2025-07-03 09:56:14'),
(3, 'Kendaraan', 'Mobil, motor, dan aksesoris kendaraan', 'fas fa-car', '2025-07-03 09:56:14'),
(4, 'Rumah Tangga', 'Peralatan dan furniture rumah', 'fas fa-home', '2025-07-03 09:56:14'),
(5, 'Hobi & Olahraga', 'Peralatan dan perlengkapan olahraga', 'fas fa-dumbbell', '2025-07-03 09:56:14'),
(6, 'Buku & Edukasi', 'Buku, alat tulis, dan materi edukasi', 'fas fa-book', '2025-07-03 09:56:14'),
(7, 'Mainan & Anak', 'Mainan dan perlengkapan anak', 'fas fa-baby', '2025-07-03 09:56:14'),
(8, 'Kesehatan & Kecantikan', 'Produk kesehatan dan kecantikan', 'fas fa-heart', '2025-07-03 09:56:14'),
(9, 'Hobi & Koleksi', 'Barang hobi dan koleksi', 'fas fa-puzzle-piece', '2025-07-03 09:56:14'),
(10, 'Lainnya', 'Kategori lainnya', 'fas fa-ellipsis-h', '2025-07-03 09:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('message','transaction','product','system') COLLATE utf8mb4_general_ci DEFAULT 'system',
  `related_id` int DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `original_price` decimal(15,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `additional_images` text COLLATE utf8mb4_general_ci,
  `location` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_info` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `condition_item` enum('Seperti Baru','Baik','Cukup Baik') COLLATE utf8mb4_general_ci DEFAULT 'Baik',
  `status` enum('available','sold','inactive','pending') COLLATE utf8mb4_general_ci DEFAULT 'available',
  `is_featured` tinyint(1) DEFAULT '0',
  `views` int DEFAULT '0',
  `likes` int DEFAULT '0',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warranty` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tags` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `category_id`, `name`, `description`, `price`, `original_price`, `image`, `additional_images`, `location`, `contact_info`, `condition_item`, `status`, `is_featured`, `views`, `likes`, `weight`, `dimensions`, `warranty`, `tags`, `created_at`, `updated_at`) VALUES
(8, 3, 3, 'Test Product', 'Test product description', 1000000.00, 1200000.00, '1751537432_nggak tau.png', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 7, 0, NULL, NULL, NULL, NULL, '2025-07-03 10:10:32', '2025-07-03 18:15:02'),
(9, 3, 1, 'iPhone 13 Pro 256GB - Graphite', 'iPhone 13 Pro 256GB warna Graphite dalam kondisi seperti baru. Kelengkapan fullset, baterai 95%, tidak ada goresan, garansi masih aktif 3 bulan.', 12500000.00, 15000000.00, '1751548285_iphone.jpeg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:11:25', '2025-07-03 18:15:02'),
(10, 3, 1, 'MacBook Air M2 - 8GB/256GB', 'MacBook Air M2 dengan RAM 8GB dan SSD 256GB. Kondisi masih bagus dengan sedikit goresan halus. Performa sangat baik untuk pekerjaan sehari-hari.', 14300000.00, 16000000.00, '1751548367_MacBoox.jpeg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:12:47', '2025-07-03 18:15:02'),
(11, 3, 1, 'Sony Alpha A7 III Kit 28-70mm', 'Kamera mirrorless Sony Alpha A7 III dengan lensa kit 28-70mm. Kondisi seperti baru, shutter count baru 5000, lengkap dengan box dan aksesoris.', 18900000.00, 22000000.00, 'sony.jpeg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 5, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 20:16:45'),
(12, 3, 3, 'Honda PCX 160 2023 - Hitam', 'Honda PCX 160 tahun 2023 warna hitam. Kilometer masih rendah (5.000 KM), kondisi mesin terawat baik, body mulus dengan sedikit goresan kecil.', 28500000.00, 32000000.00, 'Honda-pcx.jpeg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:22:25'),
(13, 3, 5, 'Hot Toys Iron Man Mark LXXXV', 'Action figure Hot Toys Iron Man Mark LXXXV edisi Avengers Endgame. Kondisi seperti baru, lengkap dengan box dan aksesoris.', 7200000.00, 8500000.00, 'IronMan.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 2, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:42:23'),
(14, 3, 5, 'Vinyl Pink Floyd - The Dark Side of the Moon', 'Vinyl Pink Floyd - The Dark Side of the Moon original pressing. Kondisi sleeve sedikit wear, namun piringan masih bagus dengan sedikit noise.', 950000.00, 1200000.00, 'pinkfloyddarkside.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 20:04:57'),
(15, 3, 1, 'iPad Air 2022 - 64GB WiFi', 'iPad Air 2022 dengan kapasitas 64GB versi WiFi. Kondisi seperti baru, masih ada sisa garansi resmi Apple 6 bulan. Layar masih mulus, baterai 99%.', 5800000.00, 6500000.00, 'ipad_air_2022.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 20:15:54'),
(16, 3, 4, 'Sofa 3 Seater Minimalis - Abu-abu', 'Sofa 3 seater bergaya minimalis dengan warna abu-abu. Bahan kain premium, nyaman, dan masih dalam kondisi bagus. Umur pakai baru 1 tahun.', 3500000.00, 4200000.00, 'Sofa 3 Seater Minimalis - Abu-abu.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(17, 3, 2, 'Tas Tote Coach Original - Black', 'Tas Tote Coach Original warna hitam. Kondisi seperti baru, hanya dipakai beberapa kali. Dilengkapi dengan dustbag dan kartu autentikasi.', 2300000.00, 2800000.00, 'Tas Tote Coach Original - Black.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(18, 3, 3, 'Toyota Fortuner 2.4 VRZ 2022 - Putih', 'Toyota Fortuner 2.4 VRZ tahun 2022 warna putih. Kilometer rendah (15.000 KM), kondisi seperti baru, service record lengkap di bengkel resmi.', 99999999.99, 99999999.99, 'Toyota Fortuner 2.4 VRZ 2022 - Putih.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-04 05:28:41'),
(19, 3, 5, 'Fender Stratocaster MIM 2019 - Sunburst', 'Gitar listrik Fender Stratocaster Made in Mexico tahun 2019 warna Sunburst. Kondisi bagus, sedikit wear pada fret, suara masih jernih.', 4750000.00, 5500000.00, 'Fender Stratocaster MIM 2019 - Sunburst.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(20, 3, 1, 'Samsung Galaxy S24 Ultra - Titanium Black', 'Samsung Galaxy S24 Ultra dengan memory 256GB. Kondisi seperti baru, baterai 98%, fitur AI terbaru, kamera 200MP sangat jernih.', 15800000.00, 18000000.00, 'Samsung Galaxy S24 Ultra - Titanium Black.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 10, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 19:15:17'),
(21, 3, 5, 'Sepeda Polygon Strattos S3 - Red', 'Sepeda balap Polygon Strattos S3 warna merah. Frame carbon, groupset Shimano 105, kondisi terawat dengan beberapa goresan ringan.', 7500000.00, 8500000.00, 'Sepeda Polygon Strattos S3 - Red.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 2, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:24:50'),
(22, 3, 1, 'Canon EOS R6 Kit 24-105mm', 'Kamera mirrorless Canon EOS R6 dengan lensa kit 24-105mm. Kondisi seperti baru, shutter count rendah, IBIS sangat stabil untuk video.', 24700000.00, 28000000.00, 'Canon EOS R6 Kit 24-105mm.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(23, 3, 4, 'Meja Kerja Industrial - Oak', 'Meja kerja bergaya industrial dengan top kayu oak solid dan kaki besi. Luas permukaan 140x70cm, dilengkapi dengan rak penyimpanan.', 2100000.00, 2800000.00, 'Meja Kerja Industrial - Oak.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(24, 3, 2, 'Jam Tangan Seiko Prospex SRPE93', 'Seiko Prospex Turtle SRPE93 dengan diameter 45mm. Kondisi seperti baru, water resistant 200m, bezel diver yang responsif.', 4350000.00, 5000000.00, 'Jam Tangan Seiko Prospex SRPE93.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(25, 3, 3, 'Honda Brio RS 2022 - Orange', 'Honda Brio RS tahun 2022 warna orange. Kilometer rendah (8.000 KM), fitur lengkap, sangat ekonomis dan lincah untuk mobilitas perkotaan.', 99999999.99, 99999999.99, 'Honda Brio RS 2022 - Orange.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 19:22:44'),
(26, 3, 5, 'Yamaha Keyboard PSR-SX900', 'Keyboard Yamaha PSR-SX900 dengan 61 keys. Fitur lengkap, suara berkualitas tinggi, dan interface yang intuitif untuk produksi musik.', 18900000.00, 22000000.00, 'Yamaha Keyboard PSR-SX900.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:27:24'),
(27, 3, 1, 'Asus ROG Zephyrus G14 2023', 'Laptop gaming Asus ROG Zephyrus G14 dengan Ryzen 9 dan RTX 4070. Kondisi seperti baru, performa tinggi dengan portabilitas yang baik.', 18500000.00, 22000000.00, 'Asus ROG Zephyrus G14 2023.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(28, 3, 2, 'Sepatu Running Nike Pegasus 39', 'Sepatu running Nike Pegasus 39 ukuran 42. Kondisi masih bagus, nyaman untuk berlari jarak jauh dengan bantalan yang responsif.', 1650000.00, 2000000.00, 'Sepatu Running Nike Pegasus 39.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 1, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(29, 3, 4, 'Lemari Pakaian Sliding Door', 'Lemari pakaian 3 pintu sliding dengan cermin. Dimensi 180x60x200cm, material particle board berkualitas, kondisi masih bagus dengan beberapa goresan.', 3900000.00, 4500000.00, 'Lemari Pakaian Sliding Door.jpg', NULL, 'Bali', '085604578971', 'Baik', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(30, 3, 1, 'Samsung Galaxy Tab S9 - 256GB', 'Tablet Samsung Galaxy Tab S9 dengan S-Pen, memori 256GB. Kondisi seperti baru, layar AMOLED yang jernih, dan performa yang tangguh.', 9200000.00, 11000000.00, 'Samsung Galaxy Tab S9 - 256GB.jpg', NULL, 'Bali', '085604578971', 'Seperti Baru', 'available', 0, 0, 0, NULL, NULL, NULL, NULL, '2025-07-03 13:54:23', '2025-07-03 18:15:02'),
(44, 4, 3, 'Koeenigseeg Agera RS', 'Hypercar performa tinggi yang diproduksi oleh Koenigsegg. Agera RS dikenal sebagai mobil yang sangat cepat dan menjadi pesaing Bugatti Veyron Super Sport. Mobil ini memiliki mesin V8 5.0 liter twin-turbocharged yang mampu menghasilkan tenaga hingga 1.160 hp dengan bahan bakar biasa, dan dapat ditingkatkan hingga 1.360 hp dengan bahan bakar E85.', 120000000000.00, NULL, '1751566617_Agera Rs.jpg', NULL, 'blitar, jawa timur', NULL, 'Seperti Baru', 'available', 0, 24, 0, NULL, NULL, NULL, NULL, '2025-07-03 18:16:57', '2025-07-04 05:56:38'),
(45, 4, 3, 'Redbull Formula 1', 'Max Verstappen', 500000000000.00, NULL, '1751571872_2025-japanese-grand-prix.jpg', NULL, 'surabaya, jawa timur', NULL, 'Seperti Baru', 'available', 0, 10, 0, NULL, NULL, NULL, NULL, '2025-07-03 19:44:32', '2025-07-04 05:56:20'),
(46, 5, 3, 'Koenigsegg One:1', 'hypercar yang sangat langka dan eksklusif yang diproduksi oleh Koenigsegg, sebuah perusahaan otomotif asal Swedia. Mobil ini terkenal karena rasio tenaga-terhadap-beratnya yang unik, yaitu 1:1, yang berarti menghasilkan 1 tenaga kuda (hp) untuk setiap kilogram berat mobil. Mobil ini juga dijuluki sebagai \"megacar\" karena output tenaganya yang mencapai satu megawatt (1.341 hp).', 840000000.00, NULL, '1751608221_One.jpg', NULL, 'surabaya, jawa timur', NULL, 'Baik', 'available', 0, 2, 0, NULL, NULL, NULL, NULL, '2025-07-04 05:50:21', '2025-07-04 06:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `reporter_id` int NOT NULL,
  `reported_user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `reason` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','reviewed','resolved','dismissed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `reviewed_user_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `transaction_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `seller_id` int NOT NULL,
  `product_id` int NOT NULL,
  `transaction_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `rating` decimal(3,2) DEFAULT '0.00',
  `total_reviews` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `profile_image`, `is_verified`, `rating`, `total_reviews`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@berkahsecond.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '081234567890', 'Jl. Admin No. 1, Jakarta', NULL, 1, 5.00, 0, '2025-07-03 09:56:14', '2025-07-03 09:56:14'),
(2, 'IbnuRizal', 'ibnurizal849@gmail.com', '$2y$10$vbblyfMj6Dh6nbbIf6BVVuPzYRrGOdK1AKx244/eYahRN0jeBvRra', 'Ibnu Rizal', 'Ibnu Rizal', NULL, NULL, 0, 0.00, 0, '2025-07-03 10:00:21', '2025-07-03 10:00:21'),
(3, 'INU', 'mail@gmail.com', '$2y$10$eY55fJz0rh7rsDFwm6XkD.z3uPgAWD5zxjtR8fXj91HTDvz7d5Yi6', 'Ibnu Rizal', '085604578971', 'Bali, Indonesia', NULL, 0, 0.00, 0, '2025-07-03 10:01:58', '2025-07-03 10:01:58'),
(4, 'anjay', 'haydarahadya@gmail.com', '$2y$10$LU0SFm0v69YoxLseWOUXD.5tJpWQeql8SaHMCZgU9Wdx1M4/HkeOy', 'haydarahadya', 'anjay', NULL, NULL, 0, 0.00, 0, '2025-07-03 18:15:40', '2025-07-03 18:15:40'),
(5, 'fandiora', 'haydarahadya123@gmail.com', '$2y$10$A7Y7WiF7ejYC2rdwXyzln.vDz09HV2MCU.41GLX9bkFckjTQRlUlC', 'haydarahadya', 'anjay', NULL, NULL, 0, 0.00, 0, '2025-07-04 05:32:45', '2025-07-04 05:32:45'),
(6, 'Jovan', 'Jovan@gmail.com', '$2y$10$e3YgVlTxbAG3lthdCX0bmeNsZYBqkpplDnsrh3Qqwt774iMn61e.u', 'JovandaKelvin', '081289745837', NULL, NULL, 0, 0.00, 0, '2025-07-04 05:52:10', '2025-07-04 05:52:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categories_name` (`name`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_messages_read` (`is_read`),
  ADD KEY `idx_messages_created_at` (`created_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_notifications_read` (`is_read`),
  ADD KEY `idx_notifications_type` (`type`),
  ADD KEY `idx_notifications_created_at` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_products_status` (`status`),
  ADD KEY `idx_products_created_at` (`created_at`),
  ADD KEY `idx_products_price` (`price`),
  ADD KEY `idx_products_views` (`views`),
  ADD KEY `idx_products_likes` (`likes`),
  ADD KEY `idx_products_location` (`location`),
  ADD KEY `idx_products_condition` (`condition_item`),
  ADD KEY `idx_products_featured` (`is_featured`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_product_images_primary` (`is_primary`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `reported_user_id` (`reported_user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_reports_status` (`status`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_reviews_rating` (`rating`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_transactions_status` (`status`),
  ADD KEY `idx_transactions_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_rating` (`rating`),
  ADD KEY `idx_users_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
