<?php
include 'koneksi.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') { 
    header("Location: index.php"); 
    exit; 
}

// Tambah / Edit User
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    $password_input = $_POST['password'];

    if ($id == "") {
        // Tambah user baru (password wajib)
        $password = md5($password_input);
        mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')");
    } else {
        // Edit user (jika password diisi, update password juga. Jika kosong, biarkan password lama)
        if (!empty($password_input)) {
            $password = md5($password_input);
            mysqli_query($conn, "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama_lengkap', role='$role' WHERE id='$id'");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE id='$id'");
        }
    }
    header("Location: user.php");
    exit;
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: user.php");
    exit;
}

// Ambil data untuk Edit
$edit_data = ['id' => '', 'username' => '', 'nama_lengkap' => '', 'role' => 'kasir'];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
    if (mysqli_num_rows($q) > 0) {
        $edit_data = mysqli_fetch_assoc($q);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
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
        input, select { padding: 10px; margin: 5px 0 15px 0; width: 100%; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #219653; }
        .btn-danger { background: #c0392b; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn-warning { background: #f39c12; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        .note { font-size: 12px; color: #7f8c8d; margin-top: -10px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>POS KASIR</h2>
        <a href="index.php">Dashboard</a>
        <a href="kasir.php">Halaman Kasir</a>
        <a href="produk.php">Manajemen Produk</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?><a href="user.php" class="active">Manajemen User</a><?php endif; ?>
        <a href="riwayat.php">Riwayat Transaksi</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;">Logout</a>
    </div>
    <div class="main-content">
        <h2>Manajemen User</h2>
        <div class="form-card">
            <h3><?= $edit_data['id'] ? 'Edit User' : 'Tambah User Baru'; ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                
                <label>Username</label>
                <input type="text" name="username" value="<?= $edit_data['username']; ?>" required>
                
                <label>Password</label>
                <input type="password" name="password" placeholder="<?= $edit_data['id'] ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password'; ?>" <?= $edit_data['id'] ? '' : 'required'; ?>>
                <?php if($edit_data['id']): ?>
                    <span class="note">*Kosongkan password jika tidak ingin menggantinya.</span>
                <?php endif; ?>

                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?= $edit_data['nama_lengkap']; ?>" required>
                
                <label>Role</label>
                <select name="role" required>
                    <option value="admin" <?= $edit_data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="kasir" <?= $edit_data['role'] == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
                </select>

                <button type="submit" name="simpan">Simpan User</button>
                <?php if ($edit_data['id']): ?>
                    <a href="user.php" style="margin-left: 10px; color: #7f8c8d; text-decoration: none;">Batal</a>
                <?php endif; ?>
            </form>
        </div>
        <table>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            $query = mysqli_query($conn, "SELECT * FROM users");
            while ($row = mysqli_fetch_assoc($query)):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['username']; ?></td>
                <td><?= $row['nama_lengkap']; ?></td>
                <td><?= ucfirst($row['role']); ?></td>
                <td>
                    <a href="user.php?edit=<?= $row['id']; ?>" class="btn-warning">Edit</a>
                    <?php if ($row['username'] != 'admin'): // Mencegah admin utama terhapus sembarangan ?>
                        <a href="user.php?hapus=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>