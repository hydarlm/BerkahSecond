-- Database: BerkahSecond
CREATE DATABASE IF NOT EXISTS BerkahSecond;
USE Berkahsecond;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    condition_item ENUM('Seperti Baru', 'Baik', 'Cukup Baik') DEFAULT 'Baik',
    status ENUM('available', 'sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ALTER TABLE products ADD views INT DEFAULT 0;
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Elektronik'),
('Fashion'),
('Otomotif'),
('Rumah Tangga'),
('Hobi & Olahraga'),
('Buku & Majalah');

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, phone) VALUES 
('admin', 'admin@berkahsecond.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '081234567890');

-- Insert sample products
INSERT INTO products (user_id, category_id, name, description, price, image, condition_item) VALUES 
(1, 1, 'iPhone 12 Pro Max', 'iPhone 12 Pro Max 256GB warna biru, kondisi mulus 95%, fullset dengan charger original', 8500000.00, 'iphone12.jpg', 'Seperti Baru'),
(1, 1, 'Samsung Galaxy S21', 'Samsung Galaxy S21 128GB warna phantom gray, kondisi baik, ada sedikit lecet di bagian belakang', 5200000.00, 'samsung_s21.jpg', 'Baik'),
(1, 2, 'Jaket Denim Vintage', 'Jaket denim vintage ukuran L, warna biru tua, kondisi masih bagus dan nyaman dipakai', 150000.00, 'jaket_denim.jpg', 'Baik'),
(1, 3, 'Helm KYT R10', 'Helm KYT R10 ukuran L, warna hitam merah, sudah SNI, kondisi bagus tidak pernah jatuh', 450000.00, 'helm_kyt.jpg', 'Baik'),
(1, 4, 'Rice Cooker Cosmos', 'Rice cooker Cosmos 1.8L, masih berfungsi dengan baik, sudah dipakai 2 tahun', 200000.00, 'rice_cooker.jpg', 'Cukup Baik'),
(1, 5, 'Sepeda Lipat Polygon', 'Sepeda lipat Polygon Urbano 3.0, warna putih, kondisi terawat, jarang dipakai', 1800000.00, 'sepeda_lipat.jpg', 'Seperti Baru');