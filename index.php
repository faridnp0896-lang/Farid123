<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$jml_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM produk"));
$jml_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
$pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(grand_total) as total FROM transaksi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Aplikasi Kasir</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; display: flex; flex-direction: column; }
        .sidebar h2 { padding: 20px; text-align: center; background: #1a252f; font-size: 20px; }
        .sidebar a { padding: 15px 20px; color: #bdc3c7; text-decoration: none; display: block; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .content { padding: 30px; animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: relative; overflow: hidden; }
        .card h3 { color: #7f8c8d; font-size: 14px; text-transform: uppercase; margin-bottom: 10px; }
        .card p { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: #3498db; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>POS KASIR</h2>
        <a href="index.php" class="active">Dashboard</a>
        <a href="kasir.php">Halaman Kasir</a>
        <a href="produk.php">Manajemen Produk</a>
        <?php if($_SESSION['role'] == 'admin'): ?>
        <a href="user.php">Manajemen User</a>
        <?php endif; ?>
        <a href="riwayat.php">Riwayat Transaksi</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;">Logout</a>
    </div>
    <div class="main-content">
        <div class="header">
            <h3>Selamat Datang, <b><?= $_SESSION['nama']; ?></b></h3>
            <span><?= date('d M Y'); ?></span>
        </div>
        <div class="content">
            <h2>Dashboard Overview</h2>
            <div class="cards">
                <div class="card">
                    <h3>Total Produk</h3>
                    <p><?= $jml_produk; ?></p>
                </div>
                <div class="card">
                    <h3>Total Transaksi</h3>
                    <p><?= $jml_transaksi; ?></p>
                </div>
                <div class="card">
                    <h3>Pendapatan Bersih</h3>
                    <p>Rp <?= number_format($pendapatan, 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>