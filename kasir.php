<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Proses simpan transaksi
if (isset($_POST['proses_transaksi'])) {
    $no_transaksi = "TRX-" . date('YmdHis');
    $total_belanja = $_POST['val_total'];
    $pajak = $_POST['val_pajak'];
    $grand_total = $_POST['val_grand'];
    $uang_bayar = $_POST['val_bayar'];
    $kembalian = $_POST['val_kembali'];
    $kasir = $_SESSION['nama'];
    
    $items = json_decode($_POST['cart_data'], true);

    if (!empty($items)) {
        mysqli_query($conn, "INSERT INTO transaksi (no_transaksi, total_belanja, pajak, grand_total, uang_bayar, kembalian, kasir) VALUES ('$no_transaksi', '$total_belanja', '$pajak', '$grand_total', '$uang_bayar', '$kembalian', '$kasir')");
        
        foreach ($items as $item) {
            $kode = $item['kode'];
            $nama = $item['nama'];
            $harga = $item['harga'];
            $qty = $item['qty'];
            $subtotal = $item['subtotal'];

            mysqli_query($conn, "INSERT INTO detail_transaksi (no_transaksi, kode_barang, nama_barang, harga, qty, subtotal) VALUES ('$no_transaksi', '$kode', '$nama', '$harga', '$qty', '$subtotal')");
            
            // Kurangi stok produk
            mysqli_query($conn, "UPDATE produk SET stok = stok - $qty WHERE kode_barang = '$kode'");
        }
        
        echo "<script>alert('Transaksi Berhasil!'); window.open('cetak_struk.php?trx=$no_transaksi', '_blank'); window.location='kasir.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Kasir</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; display: flex; flex-direction: column; }
        .sidebar h2 { padding: 20px; text-align: center; background: #1a252f; font-size: 20px; }
        .sidebar a { padding: 15px 20px; color: #bdc3c7; text-decoration: none; display: block; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .main-content { flex: 1; padding: 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .input-group { display: flex; gap: 10px; margin-bottom: 15px; }
        input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8f9fa; }
        .summary-box { margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 6px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 15px; }
        .summary-row.grand { font-size: 18px; font-weight: bold; color: #2c3e50; border-top: 2px solid #ddd; padding-top: 8px; }
        .btn-checkout { background: #27ae60; width: 100%; padding: 12px; font-size: 16px; margin-top: 15px; font-weight: bold; }
        .btn-checkout:hover { background: #219653; }
        /* Animasi Loading Bergerak */
        .loading { display: none; text-align: center; color: #3498db; font-style: italic; margin-top: 10px; }
        .loading::after { content: ' .'; animation: dots 1s steps(5, end) infinite; }
        @keyframes dots { 0%, 20% { content: ' .'; } 40% { content: ' ..'; } 60% { content: ' ...'; } 80%, 100% { content: ' ....'; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>POS KASIR</h2>
        <a href="index.php">Dashboard</a>
        <a href="kasir.php" class="active">Halaman Kasir</a>
        <a href="produk.php">Manajemen Produk</a>
        <?php if($_SESSION['role'] == 'admin'): ?><a href="user.php">Manajemen User</a><?php endif; ?>
        <a href="riwayat.php">Riwayat Transaksi</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;">Logout</a>
    </div>
    
    <div class="main-content">
        <!-- Area Input & Keranjang -->
        <div class="card">
            <h3>Pencatatan Barang Belanjaan</h3>
            <div class="input-group" style="margin-top: 15px;">
                <input type="text" id="kode_barang" placeholder="Masukkan Kode Barang atau ID..." autofocus>
                <button type="button" onclick="cariProduk()">Cari</button>
            </div>
            <div class="loading" id="loadingText">Mencari data produk</div>

            <table id="tabelKeranjang">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    <!-- Item keranjang muncul disini via JS -->
                </tbody>
            </table>
        </div>

        <!-- Area Kalkulasi & Pembayaran -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3>Ringkasan Pembayaran</h3>
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Total Belanja</span>
                        <span id="txtTotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak (11%)</span>
                        <span id="txtPajak">Rp 0</span>
                    </div>
                    <div class="summary-row grand">
                        <span>Grand Total</span>
                        <span id="txtGrandTotal">Rp 0</span>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <label>Uang Bayar (Rp)</label>
                    <input type="number" id="uang_bayar" onkeyup="hitungKembalian()" placeholder="0">
                </div>
                <div class="summary-row" style="margin-top: 15px; font-size: 16px; font-weight: bold;">
                    <span>Kembalian:</span>
                    <span id="txtKembalian" style="color: #27ae60;">Rp 0</span>
                </div>
            </div>

            <form method="POST" id="formTransaksi">
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="val_total" id="val_total">
                <input type="hidden" name="val_pajak" id="val_pajak">
                <input type="hidden" name="val_grand" id="val_grand">
                <input type="hidden" name="val_bayar" id="val_bayar">
                <input type="hidden" name="val_kembali" id="val_kembali">
                <button type="submit" name="proses_transaksi" class="btn-checkout" onclick="return validasiCheckout()">Proses & Cetak Struk</button>
            </form>
        </div>
    </div>

    <script>
        let cart = [];

        function cariProduk() {
            let kode = document.getElementById('kode_barang').value;
            if(!kode) return;

            document.getElementById('loadingText').style.display = 'block';

            fetch('get_produk.php?kode=' + encodeURIComponent(kode))
                .then(response => response.json())
                .then(result => {
                    document.getElementById('loadingText').style.display = 'none';
                    if(result.status === 'success') {
                        tambahKeKeranjang(result.data);
                        document.getElementById('kode_barang').value = '';
                        document.getElementById('kode_barang').focus();
                    } else {
                        alert(result.message);
                    }
                });
        }

        document.getElementById('kode_barang').addEventListener('keypress', function(e){
            if(e.key === 'Enter') {
                e.preventDefault();
                cariProduk();
            }
        });

        function tambahKeKeranjang(produk) {
            let existing = cart.find(item => item.kode === produk.kode_barang);
            if(existing) {
                if(existing.qty < produk.stok) {
                    existing.qty++;
                    existing.subtotal = existing.qty * existing.harga;
                } else {
                    alert('Stok produk tidak mencukupi!');
                }
            } else {
                cart.push({
                    kode: produk.kode_barang,
                    nama: produk.nama_barang,
                    harga: parseFloat(produk.harga),
                    qty: 1,
                    subtotal: parseFloat(produk.harga)
                });
            }
            renderCart();
        }

        function updateQty(kode, qty) {
            let item = cart.find(i => i.kode === kode);
            if(item) {
                let val = parseInt(qty);
                if(val > 0) {
                    item.qty = val;
                    item.subtotal = item.qty * item.harga;
                } else {
                    cart = cart.filter(i => i.kode !== kode);
                }
            }
            renderCart();
        }

        function hapusItem(kode) {
            cart = cart.filter(i => i.kode !== kode);
            renderCart();
        }

        function renderCart() {
            let tbody = document.getElementById('cartBody');
            tbody.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                total += item.subtotal;
                tbody.innerHTML += `
                    <tr>
                        <td>${item.nama}</td>
                        <td>Rp ${item.harga.toLocaleString()}</td>
                        <td><input type="number" value="${item.qty}" style="width: 60px; padding: 4px;" onchange="updateQty('${item.kode}', this.value)"></td>
                        <td>Rp ${item.subtotal.toLocaleString()}</td>
                        <td><button type="button" onclick="hapusItem('${item.kode}')" style="background: #e74c3c; padding: 4px 8px;">X</button></td>
                    </tr>
                `;
            });

            let pajak = total * 0.11;
            let grandTotal = total + pajak;

            document.getElementById('txtTotal').innerText = 'Rp ' + total.toLocaleString();
            document.getElementById('txtPajak').innerText = 'Rp ' + pajak.toLocaleString();
            document.getElementById('txtGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString();

            document.getElementById('val_total').value = total;
            document.getElementById('val_pajak').value = pajak;
            document.getElementById('val_grand').value = grandTotal;

            hitungKembalian();
        }

        function hitungKembalian() {
            let grandTotal = parseFloat(document.getElementById('val_grand').value) || 0;
            let bayar = parseFloat(document.getElementById('uang_bayar').value) || 0;
            let kembalian = bayar - grandTotal;

            document.getElementById('txtKembalian').innerText = 'Rp ' + (kembalian >= 0 ? kembalian.toLocaleString() : 0);
            
            document.getElementById('val_bayar').value = bayar;
            document.getElementById('val_kembali').value = kembalian >= 0 ? kembalian : 0;
        }

        function validasiCheckout() {
            if(cart.length === 0) {
                alert('Keranjang belanja masih kosong!');
                return false;
            }
            let bayar = parseFloat(document.getElementById('uang_bayar').value) || 0;
            let grandTotal = parseFloat(document.getElementById('val_grand').value) || 0;
            if(bayar < grandTotal) {
                alert('Uang bayar kurang dari Grand Total!');
                return false;
            }
            document.getElementById('cart_data').value = JSON.stringify(cart);
            return true;
        }
    </script>
</body>
</html>