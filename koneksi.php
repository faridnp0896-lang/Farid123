<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir_farid";

$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Buat database jika belum ada
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");
mysqli_select_db($conn, $db);

// Buat ulang atau sesuaikan tabel produk agar strukturnya lengkap
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL
)");

// Validasi kolom-kolom pada tabel produk jika tabel sudah terlanjur dibuat dengan struktur lama
$cek_kolom_kode = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'kode_barang'");
if (mysqli_num_rows($cek_kolom_kode) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN kode_barang VARCHAR(50) NOT NULL UNIQUE FIRST");
}

$cek_kolom_nama = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'nama_barang'");
if (mysqli_num_rows($cek_kolom_nama) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN nama_barang VARCHAR(100) NOT NULL AFTER kode_barang");
}

$cek_kolom_harga = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'harga'");
if (mysqli_num_rows($cek_kolom_harga) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN harga DECIMAL(10,2) NOT NULL AFTER nama_barang");
}

$cek_kolom_stok = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'stok'");
if (mysqli_num_rows($cek_kolom_stok) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN stok INT NOT NULL AFTER harga");
}

// Buat tabel pendukung lainnya
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir'
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_belanja DECIMAL(10,2) NOT NULL,
    pajak DECIMAL(10,2) NOT NULL,
    grand_total DECIMAL(10,2) NOT NULL,
    uang_bayar DECIMAL(10,2) NOT NULL,
    kembalian DECIMAL(10,2) NOT NULL,
    kasir VARCHAR(100) NOT NULL
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(50) NOT NULL,
    kode_barang VARCHAR(50) NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL
)");

// Tambah user admin default jika kosong
$cek_user = mysqli_query($conn, "SELECT * FROM users");
if (mysqli_num_rows($cek_user) == 0) {
    mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('admin', MD5('password123'), 'Administrator', 'admin')");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    $_SESSION['login'] = true;
    $_SESSION['username'] = 'admin';
    $_SESSION['nama'] = 'Administrator';
    $_SESSION['role'] = 'admin';
}
?>