<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_transaksi = $_GET['id'] ?? '';

$sql = "SELECT t.*, p.nama_paket, p.kecepatan, p.harga 
        FROM tb_transaksi t
        LEFT JOIN tb_langganan l ON t.id_langganan = l.id_langganan
        LEFT JOIN tb_paket p ON l.id_paket = p.id_paket
        WHERE t.id_transaksi = '$id_transaksi'";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if ($data['status_pembayaran'] == 'lunas'  ||
    $data['status_pembayaran'] == 'menunggu_verifikasi') {
    echo "<script>alert('Tagihan sudah diproses!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['submit'])) {
    $metode = $_POST['metode_pembayaran'];

    if ($_FILES['bukti_pembayaran']['name'] == '') {
        echo "<script>alert('Upload bukti dulu!');</script>";
    } else {
        $bukti = $_FILES['bukti_pembayaran']['name'];
        $ext = pathinfo($bukti, PATHINFO_EXTENSION);
        $nama_file = "BUKTI-" . $data['kode_invoice'] . "-" . time() . "." . $ext;

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array(strtolower($ext), $allowed)) {
            echo "<script>alert('Format file harus JPG, PNG, atau WEBP!');</script>";
            exit;
        }
        if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], '../../assets/uploads/bukti/' . $nama_file)) {
            mysqli_begin_transaction($koneksi);
            try {
                $upd = "UPDATE tb_transaksi SET 
                        metode_pembayaran = '$metode', 
                        bukti_pembayaran = '$nama_file', 
                        status_pembayaran = 'menunggu_verifikasi', 
                        tanggal_bayar = CURDATE() 
                        WHERE id_transaksi = '$id_transaksi'";
                mysqli_query($koneksi, $upd);
                mysqli_commit($koneksi);
                echo "<script>alert('Berhasil!'); window.location='index.php';</script>";
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                echo "<script>alert('Gagal!');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="auth-container">
    <div class="payment-merge-card">
        <div class="payment-left">
            <h2>Ringkasan</h2>
            <div class="payment-summary-list">
                <div class="payment-summary-item">
                    <span>Invoice</span><strong><?= $data['kode_invoice']; ?></strong>
                </div>
                <div class="payment-summary-item">
                    <span>Paket</span><strong><?= $data['nama_paket']; ?></strong>
                </div>
                <div class="payment-summary-item">
                    <span>Total</span>
                    <strong class="payment-price">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></strong>
                </div>
            </div>
        </div>

        <div class="payment-right">
            <h2>Pembayaran</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Metode</label>
                    <select name="metode_pembayaran" id="metode" required>
                        <option value="">Pilih Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div id="bankBox" class="payment-box" style="display:none;">
                    <h4>Transfer Bank</h4>
                    <p>BCA: 1234567890<br>Mandiri: 9876543210</p>
                </div>

                <div id="qrisBox" class="payment-box" style="display:none;">
                    <img src="../../assets/images/qris.png" style="max-width:150px;">
                </div>

                <div class="form-group">
                    <label>Bukti Bayar</label>
                    <input type="file" name="bukti_pembayaran" accept="image/*" required>
                </div>

                <button type="submit" name="submit" class="btn-payment">Kirim</button>
            </form>
        </div>
    </div>
</div>

<script>
const m = document.getElementById('metode');
m.addEventListener('change', function() {
    document.getElementById('bankBox').style.display = (this.value == 'transfer') ? 'block' : 'none';
    document.getElementById('qrisBox').style.display = (this.value == 'qris') ? 'block' : 'none';
});
</script>
</body>
</html>