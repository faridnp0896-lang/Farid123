<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; display: flex; flex-direction: column; }
        .sidebar h2 { padding: 20px; text-align: center; background: #1a252f; font-size: 20px; }
        .sidebar a { padding: 15px 20px; color: #bdc3c7; text-decoration: none; display: block; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .main-content { flex: 1; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #34495e; color: white; }
        .btn-struk { background: #3498db; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 14px; }
        .btn-struk:hover { background: #2980b9; }
        .detail-box { background: #fdfdfd; padding: 10px 15px; margin-top: 5px; margin-bottom: 15px; border-left: 3px solid #3498db; font-size: 13px; color: #555; }
        .detail-box ul { padding-left: 20px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>POS KASIR</h2>
        <a href="index.php">Dashboard</a>
        <a href="kasir.php">Halaman Kasir</a>
        <a href="produk.php">Manajemen Produk</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?><a href="user.php">Manajemen User</a><?php endif; ?>
        <a href="riwayat.php" class="active">Riwayat Transaksi</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;">Logout</a>
    </div>
    <div class="main-content">
        <h2>Riwayat Transaksi</h2>
        <table>
            <tr>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Detail Barang Belanjaan</th>
                <th>Grand Total</th>
                <th>Kasir</th>
                <th>Aksi</th>
            </tr>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY id DESC");
            while ($row = mysqli_fetch_assoc($query)):
                $no_transaksi = $row['no_transaksi'];
            ?>
            <tr>
                <td><strong><?= $row['no_transaksi']; ?></strong></td>
                <td><?= $row['tanggal']; ?></td>
                <td>
                    <div class="detail-box">
                        <ul>
                            <?php
                            $q_detail = mysqli_query($conn, "SELECT * FROM detail_transaksi WHERE no_transaksi = '$no_transaksi'");
                            while ($det = mysqli_fetch_assoc($q_detail)):
                            ?>
                                <li>
                                    <?= $det['nama_barang']; ?> 
                                    (<?= $det['qty']; ?> x Rp <?= number_format($det['harga'], 0, ',', '.'); ?>) 
                                    = <b>Rp <?= number_format($det['subtotal'], 0, ',', '.'); ?></b>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </td>
                <td><strong>Rp <?= number_format($row['grand_total'], 0, ',', '.'); ?></strong></td>
                <td><?= $row['kasir']; ?></td>
                <td>
                    <a href="struk.php?no=<?= $row['no_transaksi']; ?>" target="_blank" class="btn-struk">Cetak Struk</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>