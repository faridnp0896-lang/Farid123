<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

if (!isset($_GET['no'])) {
    header("Location: riwayat.php");
    exit;
}

$no_transaksi = $_GET['no'];

$q_transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE no_transaksi = '$no_transaksi'");
$transaksi = mysqli_fetch_assoc($q_transaksi);

if (!$transaksi) {
    echo "Transaksi tidak ditemukan!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - <?= $no_transaksi; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #eee; margin: 0; padding: 20px; }
        .struk { background: #fff; width: 300px; margin: 0 auto; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .center { text-align: center; }
        .right { text-align: right; }
        hr { border: dashed 1px #888; margin: 10px 0; }
        table { width: 100%; font-size: 12px; border-collapse: collapse; }
        th, td { padding: 4px 0; vertical-align: top; }
        .btn-print { display: block; width: 100%; padding: 10px; background: #27ae60; color: white; text-align: center; text-decoration: none; border-radius: 5px; margin-top: 15px; font-family: 'Segoe UI', Tahoma, sans-serif; cursor: pointer; }
        .btn-back { display: block; width: 100%; padding: 8px; background: #7f8c8d; color: white; text-align: center; text-decoration: none; border-radius: 5px; margin-top: 5px; font-family: 'Segoe UI', Tahoma, sans-serif; }
        @media print {
            .btn-print, .btn-back { display: none; }
            body { background: none; padding: 0; }
            .struk { box-shadow: none; width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="struk">
        <div class="center">
            <h3>TOKO KASIR FARID</h3>
            <p style="font-size: 12px; margin-top: -5px;">Jl. Raya Kasir No. 1</p>
            <p style="font-size: 11px; color: #555;">-----------------------------------</p>
        </div>

        <div style="font-size: 12px; margin-bottom: 10px;">
            <p>No Trans : <b><?= $transaksi['no_transaksi']; ?></b></p>
            <p>Tanggal  : <?= $transaksi['tanggal']; ?></p>
            <p>Kasir    : <?= $transaksi['kasir']; ?></p>
        </div>

        <hr>

        <table>
            <?php
            $q_detail = mysqli_query($conn, "SELECT * FROM detail_transaksi WHERE no_transaksi = '$no_transaksi'");
            while ($item = mysqli_fetch_assoc($q_detail)):
            ?>
            <tr>
                <td colspan="2"><b><?= $item['nama_barang']; ?></b></td>
            </tr>
            <tr>
                <td><?= $item['qty']; ?> x <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                <td class="right">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <hr>

        <table style="font-size: 12px;">
            <tr>
                <td>Total Belanja</td>
                <td class="right">Rp <?= number_format($transaksi['total_belanja'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Pajak</td>
                <td class="right">Rp <?= number_format($transaksi['pajak'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td><b>Grand Total</b></td>
                <td class="right"><b>Rp <?= number_format($transaksi['grand_total'], 0, ',', '.'); ?></b></td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="right">Rp <?= number_format($transaksi['uang_bayar'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="right">Rp <?= number_format($transaksi['kembalian'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <hr>

        <div class="center" style="font-size: 12px; margin-top: 10px;">
            <p>TERIMA KASIH</p>
        </div>

        <button class="btn-print" onclick="window.print()">Cetak Ulang</button>
        <a href="riwayat.php" class="btn-back">Kembali ke Riwayat</a>
    </div>

</body>
</html>