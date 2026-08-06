<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Tambah / Edit Produk
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($id == "") {
        mysqli_query($conn, "INSERT INTO produk (kode_barang, nama_barang, harga, stok) VALUES ('$kode', '$nama', '$harga', '$stok')");
    } else {
        mysqli_query($conn, "UPDATE produk SET kode_barang='$kode', nama_barang='$nama', harga='$harga', stok='$stok' WHERE id='$id'");
    }
    header("Location: produk.php");
    exit;
}

// Hapus Produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM produk WHERE id='$id'");
    header("Location: produk.php");
    exit;
}

// Ambil data untuk Edit
$edit_data = ['id' => '', 'kode_barang' => '', 'nama_barang' => '', 'harga' => '', 'stok' => ''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
    if (mysqli_num_rows($q) > 0) {
        $edit_data = mysqli_fetch_assoc($q);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk</title>
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
        .form-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        input { padding: 10px; margin: 5px 0 15px 0; width: 100%; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #219653; }
        .btn-danger { background: #c0392b; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn-warning { background: #f39c12; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>POS KASIR</h2>
        <a href="index.php">Dashboard</a>
        <a href="kasir.php">Halaman Kasir</a>
        <a href="produk.php" class="active">Manajemen Produk</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?><a href="user.php">Manajemen User</a><?php endif; ?>
        <a href="riwayat.php">Riwayat Transaksi</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;">Logout</a>
    </div>
    <div class="main-content">
        <h2>Manajemen Produk</h2>
        <div class="form-card">
            <h3><?= $edit_data['id'] ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" value="<?= $edit_data['kode_barang']; ?>" required>
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" value="<?= $edit_data['nama_barang']; ?>" required>
                <label>Harga Satuan (Rp)</label>
                <input type="number" name="harga" value="<?= $edit_data['harga']; ?>" required>
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $edit_data['stok']; ?>" required>
                <button type="submit" name="simpan">Simpan Produk</button>
                <?php if ($edit_data['id']): ?>
                    <a href="produk.php" style="margin-left: 10px; color: #7f8c8d; text-decoration: none;">Batal</a>
                <?php endif; ?>
            </form>
        </div>
        <table>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            $query = mysqli_query($conn, "SELECT * FROM produk");
            while ($row = mysqli_fetch_assoc($query)):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['kode_barang']; ?></td>
                <td><?= $row['nama_barang']; ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?= $row['stok']; ?></td>
                <td>
                    <a href="produk.php?edit=<?= $row['id']; ?>" class="btn-warning">Edit</a>
                    <a href="produk.php?hapus=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>