<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

// Ambil ID Transaksi dari parameter URL (Menggantikan id_pemasangan)
$id_transaksi = $_GET['id'] ?? '';

// 1. Ambil data gabungan melalui tb_langganan untuk ringkasan pesanan di sebelah kiri
$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_transaksi.*,
        tb_paket.nama_paket,
        tb_paket.kecepatan,
        tb_paket.harga
     FROM tb_transaksi
     LEFT JOIN tb_langganan ON tb_transaksi.id_langganan = tb_langganan.id_langganan
     LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket
     WHERE tb_transaksi.id_transaksi = '$id_transaksi'"
);

$data = mysqli_fetch_assoc($query);

// Validasi jika data tagihan tidak ditemukan
if (!$data) {
    echo "<script>alert('Data tagihan tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

/* ================= SUBMIT PEMBAYARAN ================= */
if (isset($_POST['submit'])) {

    $metode = $_POST['metode_pembayaran'];
    $total_bayar = $data['harga']; // Mengambil nominal harga paket

    /* ================= UPLOAD FOTO ================= */
    $bukti = $_FILES['bukti_pembayaran']['name'];
    $tmp   = $_FILES['bukti_pembayaran']['tmp_name'];

    // Membuat nama file unik berdasarkan kode invoice agar rapi dan tidak saling tertimpa
    $ekstensi_file = pathinfo($bukti, PATHINFO_EXTENSION);
    $nama_bukti = "BUKTI-" . $data['kode_invoice'] . "-" . time() . "." . $ekstensi_file;

    // Proses pemindahan file ke folder tujuan
    move_uploaded_file(
        $tmp,
        '../../assets/uploads/bukti/' . $nama_bukti
    );

    /* ================= UPDATE DATA TAGIHAN ================= */
    
    // Mulai transaksi database agar proses aman
    mysqli_begin_transaction($koneksi);

    try {
        // QUERY FIX: Melakukan UPDATE pada record transaksi yang sudah ada, bukan INSERT BARU
        mysqli_query(
            $koneksi,
            "UPDATE tb_transaksi SET 
                metode_pembayaran = '$metode', 
                bukti_pembayaran = '$nama_bukti', 
                jumlah_bayar = '$total_bayar', 
                status_pembayaran = 'Menunggu Konfirmasi',
                tanggal_transaksi = NOW()
             WHERE id_transaksi = '$id_transaksi'"
        );

        // Komit perubahan jika tidak ada error SQL
        mysqli_commit($koneksi);

        echo "
        <script>
            alert('Pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
            window.location='index.php';
        </script>
        ";

    } catch (mysqli_sql_exception $e) {
        // Batalkan perubahan jika query mengalami kegagalan sistem
        mysqli_rollback($koneksi);
        
        echo "<div style='color: red; padding: 20px; background: #ffdddd; border: 1px solid red; margin: 20px auto; max-width: 600px; border-radius: 8px;'>";
        echo "<strong>Gagal menyimpan pembayaran!</strong><br>";
        echo "Pesan Error: " . $e->getMessage();
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Pembayaran - Anuwani Network</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-container">
    <div class="payment-merge-card">

        <div class="payment-left">
            <h2>Ringkasan Pesanan</h2>

            <div class="payment-summary-list">
                <div class="payment-summary-item">
                    <span>Invoice</span>
                    <strong><?= $data['kode_invoice']; ?></strong>
                </div>

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
                        Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                    </strong>
                </div>

                <div class="payment-summary-item">
                    <span>Status</span>
                    <strong class="status-belum" style="color: #c5221f; background: #fce8e6; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                        <?php 
                            // Jika status_pembayaran di DB ada isinya, tampilkan. Jika kosong, tampilkan 'Belum Bayar'
                            echo !empty($data['status_pembayaran']) ? $data['status_pembayaran'] : 'Belum Bayar'; 
                        ?>
                    </strong>
                </div>
            </div>
        </div>

        <div class="payment-right">
            <h2>Pembayaran</h2>
            <p>Upload bukti pembayaran untuk melanjutkan konfirmasi layanan internet Anda.</p>

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

                <div class="payment-box" id="bankBox" style="display: none;">
                    <h4>Transfer Bank</h4>
                    <p><strong>BCA :</strong> 1234567890</p>
                    <p><strong>Mandiri :</strong> 9876543210</p>
                    <p><strong>A/N :</strong> PT Anuwani Network</p>
                </div>

                <div class="payment-box" id="qrisBox" style="display: none; text-align: center;">
                    <h4>QRIS Payment</h4>
                    <img src="../../assets/images/qris.png" class="qris-image" style="max-width: 180px; margin-top: 10px; border: 1px solid #ddd; padding: 5px; background: #fff;">
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

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <a href="index.php" style="background: #6c757d; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-align: center; flex: 1;">
                        Batal
                    </a>
                    <button 
                        type="submit"
                        name="submit"
                        class="btn-payment"
                        style="flex: 2; margin-top: 0;"
                    >
                        <i class="bi bi-check-circle-fill"></i>
                        Kirim Pembayaran
                    </button>
                </div>
            </form>
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