<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id_pemasangan = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_pemasangan.*,
        tb_paket.nama_paket,
        tb_paket.kecepatan,
        tb_paket.harga
     FROM tb_pemasangan
     LEFT JOIN tb_paket
     ON tb_pemasangan.id_paket = tb_paket.id_paket
     WHERE tb_pemasangan.id_pemasangan = '$id_pemasangan'"
);

$data = mysqli_fetch_assoc($query);

/* ================= SUBMIT PEMBAYARAN ================= */
if (isset($_POST['submit'])) {

    $metode = $_POST['metode_pembayaran'];

    /* ================= UPLOAD FOTO ================= */
    $bukti = $_FILES['bukti_pembayaran']['name'];
    $tmp   = $_FILES['bukti_pembayaran']['tmp_name'];

    $nama_bukti = time() . '-' . $bukti;

    move_uploaded_file(
        $tmp,
        '../../assets/uploads/bukti/' . $nama_bukti
    );

    /* ================= UPDATE DATABASE ================= */
    mysqli_query(
        $koneksi,
        "UPDATE tb_pemasangan SET
            metode_pembayaran = '$metode',
            bukti_pembayaran = '$nama_bukti',
            status_pembayaran = 'Menunggu Konfirmasi'
         WHERE id_pemasangan = '$id_pemasangan'"
    );

    echo "
    <script>
        alert('Pembayaran berhasil dikirim');
        window.location='../index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Pembayaran</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-container">

  <div class="auth-container">

    <div class="payment-merge-card">

        <div class="payment-left">
            <h2>Ringkasan Pesanan</h2>

            <div class="payment-summary-list">
                <div class="payment-summary-item">
                    <span>Paket</span>
                    <strong><?= $data['nama_paket']; ?></strong>
                </div>

                <div class="payment-summary-item">
                    <span>Kecepatan</span>
                    <strong><?= $data['kecepatan']; ?></strong>
                </div>

                <div class="payment-summary-item">
                    <span>Total Pembayaran</span>
                    <strong class="payment-price">
                        Rp <?= number_format($data['harga']); ?>
                    </strong>
                </div>

                <div class="payment-summary-item">
                    <span>Status</span>
                    <strong class="status-belum">Belum Bayar</strong>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT ================= -->
        <div class="payment-right">
            <h2>Pembayaran</h2>
            <p>Upload bukti pembayaran untuk melanjutkan pemasangan.</p>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select 
                        name="metode_pembayaran"
                        id="metodePembayaran"
                        required
                    >
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>

                <!-- BANK -->
                <div class="payment-box" id="bankBox" style="display: none;">
                    <h4>Transfer Bank</h4>
                    <p>BCA : 1234567890</p>
                    <p>Mandiri : 9876543210</p>
                    <p>A/N PT Anuwani Network</p>
                </div>

                <!-- QRIS -->
                <div class="payment-box" id="qrisBox" style="display: none;">
                    <h4>QRIS Payment</h4>
                    <img src="../../assets/images/qris.png" class="qris-image">
                </div>

                <div class="form-group">
                    <label>Upload Bukti Pembayaran</label>
                    <input 
                        type="file"
                        name="bukti_pembayaran"
                        accept="image/*"
                        required
                    >
                </div>

                <button 
                    type="submit"
                    name="submit"
                    class="btn-payment"
                >
                    <i class="bi bi-check-circle-fill"></i>
                    Kirim Pembayaran
                </button>
            </form>
        </div>

    </div>
</div>

</div>

<script>
const metode = document.getElementById('metodePembayaran');
const bankBox = document.getElementById('bankBox');
const qrisBox = document.getElementById('qrisBox');

metode.addEventListener('change', function(){
    if (this.value == 'Transfer Bank') {
        bankBox.style.display = 'block';
        qrisBox.style.display = 'none';
    } else if (this.value == 'QRIS') {
        bankBox.style.display = 'none';
        qrisBox.style.display = 'block';
    } else {
        bankBox.style.display = 'none';
        qrisBox.style.display = 'none';
    }
});
</script>

</body>
</html>