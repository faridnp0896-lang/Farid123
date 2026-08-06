<?php
include 'koneksi.php';

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Cek apakah username terdaftar di database
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        
        // Set session login
        $_SESSION['login'] = true;
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama'] = $row['nama_lengkap'];
        $_SESSION['role'] = $row['role'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: linear-gradient(135deg, #667eea, #764ba2); }
        .login-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        label { display: block; margin-bottom: 5px; color: #666; font-weight: 600; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #4338ca; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login Kasir</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username (cth: admin)" required autofocus>
            <button type="submit" name="login">Masuk</button>
        </form>
    </div>
</body>
</html>