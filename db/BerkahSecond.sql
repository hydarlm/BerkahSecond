-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 04:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Elektronik', '2025-07-03 09:56:14'),
(2, 'Fashion', '2025-07-03 09:56:14'),
(3, 'Otomotif', '2025-07-03 09:56:14'),
(4, 'Rumah Tangga', '2025-07-03 09:56:14'),
(5, 'Hobi & Olahraga', '2025-07-03 09:56:14'),
(6, 'Buku & Majalah', '2025-07-03 09:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `condition_item` enum('Seperti Baru','Baik','Cukup Baik') DEFAULT 'Baik',
  `status` enum('available','sold') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `category_id`, `name`, `description`, `price`, `image`, `location`, `condition_item`, `status`, `created_at`, `views`) VALUES
(8, 3, 3, 'whateverrr', 'whateverr', 1000000.00, '1751537432_nggak tau.png', 'Bali', 'Seperti Baru', 'available', '2025-07-03 10:10:32', 7),
(9, 3, 1, 'iPhone 13 Pro 256GB - Graphite', 'iPhone 13 Pro 256GB warna Graphite dalam kondisi seperti baru. Kelengkapan fullset, baterai 95%, tidak ada goresan, garansi masih aktif 3 bulan.', 12500000.00, '1751548285_iphone.jpeg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:11:25', 0),
(10, 3, 1, 'MacBook Air M2 - 8GB/256GB', 'MacBook Air M2 dengan RAM 8GB dan SSD 256GB. Kondisi masih bagus dengan sedikit goresan halus. Performa sangat baik untuk pekerjaan sehari-hari.', 14300000.00, '1751548367_MacBoox.jpeg', 'Bali', 'Baik', 'available', '2025-07-03 13:12:47', 0),
(11, 3, 1, 'Sony Alpha A7 III Kit 28-70mm', 'Kamera mirrorless Sony Alpha A7 III dengan lensa kit 28-70mm. Kondisi seperti baru, shutter count baru 5000, lengkap dengan box dan aksesoris.', 18900000.00, 'sony.jpeg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(12, 3, 3, 'Honda PCX 160 2023 - Hitam', 'Honda PCX 160 tahun 2023 warna hitam. Kilometer masih rendah (5.000 KM), kondisi mesin terawat baik, body mulus dengan sedikit goresan kecil.', 28500000.00, 'Honda-pcx.jpeg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(13, 3, 5, 'Hot Toys Iron Man Mark LXXXV', 'Action figure Hot Toys Iron Man Mark LXXXV edisi Avengers Endgame. Kondisi seperti baru, lengkap dengan box dan aksesoris.', 7200000.00, 'IronMan.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(14, 3, 5, 'Vinyl Pink Floyd - The Dark Side of the Moon', 'Vinyl Pink Floyd - The Dark Side of the Moon original pressing. Kondisi sleeve sedikit wear, namun piringan masih bagus dengan sedikit noise.', 950000.00, 'pinkfloyddarkside.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(15, 3, 1, 'iPad Air 2022 - 64GB WiFi', 'iPad Air 2022 dengan kapasitas 64GB versi WiFi. Kondisi seperti baru, masih ada sisa garansi resmi Apple 6 bulan. Layar masih mulus, baterai 99%.', 5800000.00, 'ipad_air_2022.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(16, 3, 4, 'Sofa 3 Seater Minimalis - Abu-abu', 'Sofa 3 seater bergaya minimalis dengan warna abu-abu. Bahan kain premium, nyaman, dan masih dalam kondisi bagus. Umur pakai baru 1 tahun.', 3500000.00, 'Sofa 3 Seater Minimalis - Abu-abu.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(17, 3, 2, 'Tas Tote Coach Original - Black', 'Tas Tote Coach Original warna hitam. Kondisi seperti baru, hanya dipakai beberapa kali. Dilengkapi dengan dustbag dan kartu autentikasi.', 2300000.00, 'Tas Tote Coach Original - Black.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(18, 3, 3, 'Toyota Fortuner 2.4 VRZ 2022 - Putih', 'Toyota Fortuner 2.4 VRZ tahun 2022 warna putih. Kilometer rendah (15.000 KM), kondisi seperti baru, service record lengkap di bengkel resmi.', 99999999.99, 'Toyota Fortuner 2.4 VRZ 2022 - Putih.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(19, 3, 5, 'Fender Stratocaster MIM 2019 - Sunburst', 'Gitar listrik Fender Stratocaster Made in Mexico tahun 2019 warna Sunburst. Kondisi bagus, sedikit wear pada fret, suara masih jernih.', 4750000.00, 'Fender Stratocaster MIM 2019 - Sunburst.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(20, 3, 1, 'Samsung Galaxy S24 Ultra - Titanium Black', 'Samsung Galaxy S24 Ultra dengan memory 256GB. Kondisi seperti baru, baterai 98%, fitur AI terbaru, kamera 200MP sangat jernih.', 15800000.00, 'Samsung Galaxy S24 Ultra - Titanium Black.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(21, 3, 5, 'Sepeda Polygon Strattos S3 - Red', 'Sepeda balap Polygon Strattos S3 warna merah. Frame carbon, groupset Shimano 105, kondisi terawat dengan beberapa goresan ringan.', 7500000.00, 'Sepeda Polygon Strattos S3 - Red.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(22, 3, 1, 'Canon EOS R6 Kit 24-105mm', 'Kamera mirrorless Canon EOS R6 dengan lensa kit 24-105mm. Kondisi seperti baru, shutter count rendah, IBIS sangat stabil untuk video.', 24700000.00, 'Canon EOS R6 Kit 24-105mm.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(23, 3, 4, 'Meja Kerja Industrial - Oak', 'Meja kerja bergaya industrial dengan top kayu oak solid dan kaki besi. Luas permukaan 140x70cm, dilengkapi dengan rak penyimpanan.', 2100000.00, 'Meja Kerja Industrial - Oak.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(24, 3, 2, 'Jam Tangan Seiko Prospex SRPE93', 'Seiko Prospex \'Turtle\' SRPE93 dengan diameter 45mm. Kondisi seperti baru, water resistant 200m, bezel diver yang responsif.', 4350000.00, 'Jam Tangan Seiko Prospex SRPE93.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(25, 3, 3, 'Honda Brio RS 2022 - Orange', 'Honda Brio RS tahun 2022 warna orange. Kilometer rendah (8.000 KM), fitur lengkap, sangat ekonomis dan lincah untuk mobilitas perkotaan.', 99999999.99, 'Honda Brio RS 2022 - Orange.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(26, 3, 5, 'Yamaha Keyboard PSR-SX900', 'Keyboard Yamaha PSR-SX900 dengan 61 keys. Fitur lengkap, suara berkualitas tinggi, dan interface yang intuitif untuk produksi musik.', 18900000.00, 'Yamaha Keyboard PSR-SX900.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(27, 3, 1, 'Asus ROG Zephyrus G14 2023', 'Laptop gaming Asus ROG Zephyrus G14 dengan Ryzen 9 dan RTX 4070. Kondisi seperti baru, performa tinggi dengan portabilitas yang baik.', 18500000.00, 'Asus ROG Zephyrus G14 2023.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(28, 3, 2, 'Sepatu Running Nike Pegasus 39', 'Sepatu running Nike Pegasus 39 ukuran 42. Kondisi masih bagus, nyaman untuk berlari jarak jauh dengan bantalan yang responsif.', 1650000.00, 'Sepatu Running Nike Pegasus 39.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 1),
(29, 3, 4, 'Lemari Pakaian Sliding Door', 'Lemari pakaian 3 pintu sliding dengan cermin. Dimensi 180x60x200cm, material particle board berkualitas, kondisi masih bagus dengan beberapa goresan.', 3900000.00, 'Lemari Pakaian Sliding Door.jpg', 'Bali', '', 'available', '2025-07-03 13:54:23', 0),
(30, 3, 1, 'Samsung Galaxy Tab S9 - 256GB', 'Tablet Samsung Galaxy Tab S9 dengan S-Pen, memori 256GB. Kondisi seperti baru, layar AMOLED yang jernih, dan performa yang tangguh.', 9200000.00, 'Samsung Galaxy Tab S9 - 256GB.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(31, 3, 3, 'Vespa Primavera 150 - 2022', 'Vespa Primavera 150 tahun 2022. Kilometer rendah, kondisi seperti baru, warna putih klasik yang elegan dan timeless.', 45000000.00, 'Vespa Primavera 150 - 2022.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(32, 3, 1, 'Kamera GoPro Hero 11 Black', 'Action camera GoPro Hero 11 Black dengan stabilisasi HyperSmooth 5.0. Kondisi bagus dengan beberapa goresan kecil pada layar.', 5300000.00, 'Kamera GoPro Hero 11 Black.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(33, 3, 2, 'Jaket Kulit Schott Perfecto', 'Jaket kulit asli Schott Perfecto model 618. Kulit sapi tebal berkualitas, warna hitam klasik yang sudah developing patina yang indah.', 3700000.00, 'Jaket Kulit Schott Perfecto.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(34, 3, 1, 'Monitor Dell Ultrasharp 27\" 4K', 'Monitor Dell Ultrasharp 27 inch resolusi 4K dengan color accuracy 99% Adobe RGB. Kondisi seperti baru, ideal untuk designer dan content creator.', 5800000.00, 'Monitor Dell Ultrasharp 27 4K.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(35, 3, 4, 'Kursi Gaming Secretlab Titan Evo', 'Kursi gaming Secretlab Titan Evo 2022 dengan material SoftWeave Plus. Ergonomis dengan lumbar support yang dapat disesuaikan.', 6500000.00, 'Kursi Gaming Secretlab Titan Evo.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(36, 3, 3, 'Datsun Go+ Panca 2020', 'Datsun Go+ Panca tahun 2020 tipe tertinggi. 7 seater yang ekonomis, perawatan rutin, AC dingin dan mesin responsif.', 95000000.00, 'Datsun Go+ Panca 2020.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(37, 3, 1, 'Nintendo Switch OLED - White', 'Nintendo Switch versi OLED warna putih. Kondisi seperti baru, screen protector terpasang, dockable dengan layar TV.', 4200000.00, 'Nintendo Switch OLED - White.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(38, 3, 4, 'Karpet Persia Handmade 2x3m', 'Karpet Persia handmade ukuran 2x3m dengan motif klasik. Material wool premium dengan ketebalan yang nyaman untuk kaki.', 8500000.00, 'Karpet Persia Handmade 2x3m.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(39, 3, 1, 'iPhone 14 Pro Max 512GB - Gold', 'iPhone 14 Pro Max 512GB warna Gold. Kondisi seperti baru, Dynamic Island yang interaktif, kamera 48MP yang mengesankan.', 13900000.00, 'iPhone 14 Pro Max 512GB - Gold.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(40, 3, 3, 'Yamaha R15 V4 2023 - Racing Blue', 'Motor sport Yamaha R15 V4 tahun 2023 warna Racing Blue. Kondisi seperti baru dengan Quick Shifter dan fitur Traction Control.', 35000000.00, 'Yamaha R15 V4 2023 - Racing Blue.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(41, 3, 1, 'DJI Mini 3 Pro Combo', 'Drone DJI Mini 3 Pro dengan Fly More Combo. Kondisi bagus, baterai masih sehat, dapat merekam video 4K dengan sensor 1/1.3\".', 8900000.00, 'DJI Mini 3 Pro Combo.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0),
(42, 3, 2, 'Jam Tangan Rolex Datejust Replika', 'Jam tangan Rolex Datejust replika kualitas premium. Mesin automatic, water resistant, dan finish yang rapi menyerupai aslinya.', 2700000.00, 'Jam Tangan Rolex Datejust Replika.jpg', 'Bali', 'Seperti Baru', 'available', '2025-07-03 13:54:23', 0),
(43, 3, 1, 'Kulkas 2 Pintu Samsung Digital Inverter', 'Kulkas 2 pintu Samsung dengan teknologi Digital Inverter yang hemat energi. Kapasitas 300L, kondisi terawat dan bersih.', 7800000.00, 'Kulkas 2 Pintu Samsung Digital Inverter.jpg', 'Bali', 'Baik', 'available', '2025-07-03 13:54:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `created_at`) VALUES
(1, 'admin', 'admin@berkahsecond.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '081234567890', '2025-07-03 09:56:14'),
(2, 'IbnuRizal', 'ibnurizal849@gmail.com', '$2y$10$vbblyfMj6Dh6nbbIf6BVVuPzYRrGOdK1AKx244/eYahRN0jeBvRra', 'ibnu rizal', 'Ibnu Rizal', '2025-07-03 10:00:21'),
(3, 'INU', 'mail@gmail.com', '$2y$10$eY55fJz0rh7rsDFwm6XkD.z3uPgAWD5zxjtR8fXj91HTDvz7d5Yi6', 'ibnu rizal', '085604578971', '2025-07-03 10:01:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
