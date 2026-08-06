<?php
include 'koneksi.php';
if (isset($_GET['kode'])) {
    $kode = mysqli_real_escape_string($conn, $_GET['kode']);
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE kode_barang = '$kode' OR id = '$kode'");
    if (mysqli_num_rows($q) > 0) {
        $data = mysqli_fetch_assoc($q);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan!']);
    }
}
?>