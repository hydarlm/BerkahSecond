# BerkahSecond

![BerkahSecond Homepage](assets/images/homepage.png)
*Tampilan halaman utama BerkahSecond*

Aplikasi marketplace second hand (barang bekas) yang memungkinkan pengguna untuk menjual dan membeli produk bekas dengan mudah.

## 📋 Deskripsi

BerkahSecond adalah platform jual beli barang bekas yang dibuat dengan PHP dan MySQL. Aplikasi ini menyediakan fitur untuk pengguna mendaftarkan akun, menambahkan produk, melihat katalog produk, dan mengelola profil mereka.

## 🚀 Fitur Utama

- **Registrasi & Login**: Sistem autentikasi pengguna
- **Katalog Produk**: Menampilkan semua produk yang tersedia
- **Detail Produk**: Informasi lengkap tentang produk
- **Tambah Produk**: Pengguna dapat menambahkan produk untuk dijual
- **Kelola Produk**: Edit dan hapus produk milik sendiri
- **Profil Pengguna**: Manajemen informasi akun
- **Upload Gambar**: Fitur upload gambar produk

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Web Server**: Apache (XAMPP/WAMP/LAMP)

## 📦 Kategori Produk

Berdasarkan gambar yang tersedia, aplikasi ini mendukung berbagai kategori produk bekas seperti:

- **Elektronik**: iPhone, iPad, MacBook, Nintendo Switch, Monitor, dll.
- **Kendaraan**: Mobil (Honda, Toyota, Datsun), Motor (Yamaha, Honda, Vespa)
- **Musik**: Gitar Fender, Keyboard Yamaha, Vinyl Records
- **Gaming**: Gaming Chair, Konsol Game
- **Fashion**: Jaket, Sepatu, Tas
- **Rumah Tangga**: Sofa, Lemari, Kulkas, Karpet
- **Olahraga**: Sepeda, Raket Badminton
- **Fotografi**: Kamera Canon, GoPro, DJI Drone

## 🌐 Demo

Aplikasi ini dapat diakses secara online di:
**[https://berkahsecond.great-site.net/](https://berkahsecond.great-site.net/)**

## 🔧 Instalasi

1. **Persiapkan Environment**
   ```bash
   # Pastikan XAMPP/MAMP/Laragon sudah terinstall
   # Aktifkan Apache dan MySQL
   ```

2. **Clone/Download Project**
   ```bash
   git clone [https://github.com/hydarlm/BerkahSecond.git]
   # atau download dan extract ke folder htdocs
   ```

3. **Setup Database**
   ```sql
   -- Import file BerkahSecond.sql ke phpMyAdmin
   -- atau melalui command line:
   mysql -u root -p < db/BerkahSecond.sql
   ```

4. **Konfigurasi Database**
   ```php
   // Edit file includes/koneksi.php
   // Sesuaikan dengan konfigurasi database Anda
   ```

5. **Jalankan Aplikasi**
   ```
   http://localhost/BerkahSecond/
   ```

## 🗃️ Database

Database `BerkahSecond.sql` berisi:
- Tabel pengguna (users)
- Tabel produk (products)
- Tabel kategori (categories)
- Data sample untuk testing

## 📱 Penggunaan

1. **Registrasi**: Buat akun baru melalui halaman register
2. **Login**: Masuk dengan kredensial yang telah dibuat
3. **Browse Produk**: Lihat katalog produk yang tersedia
4. **Tambah Produk**: Upload produk yang ingin dijual
5. **Kelola Produk**: Edit atau hapus produk milik Anda
6. **Profil**: Update informasi profil pengguna

## 🔒 Keamanan

- Validasi input pada semua form
- Sanitasi data sebelum query database
- Session management untuk autentikasi
- Upload file validation untuk gambar

## 📞 Kontribusi

Jika ingin berkontribusi pada project ini:

1. Fork repository
2. Buat branch untuk fitur baru
3. Commit perubahan Anda
4. Push ke branch
5. Buat Pull Request
