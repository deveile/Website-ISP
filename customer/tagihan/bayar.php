<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id = $_GET['id'];

/* ================= AMBIL TRANSAKSI ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_transaksi.*,
        tb_customer.nama_customer
     FROM tb_transaksi
     LEFT JOIN tb_customer ON tb_transaksi.id_customer = tb_customer.id_customer
     WHERE tb_transaksi.id_transaksi = '$id'"
);

$data = mysqli_fetch_assoc($query);

/* ================= UPLOAD ================= */
if (isset($_POST['upload'])) {
    $nama_file = $_FILES['bukti_pembayaran']['name'];
    $tmp       = $_FILES['bukti_pembayaran']['tmp_name'];

    $random = rand(1000, 9999);
    $file_baru = $random . '-' . $nama_file;

    move_uploaded_file(
        $tmp,
        "../../assets/uploads/" . $file_baru
    );

    mysqli_query(
        $koneksi,
        "UPDATE tb_transaksi 
         SET
            bukti_pembayaran = '$file_baru',
            status_pembayaran = 'Menunggu Konfirmasi',
            tanggal_transaksi = NOW()
         WHERE id_transaksi = '$id'"
    );

    echo "
    <script>
        alert('Bukti pembayaran berhasil dikirim');
        window.location='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Upload Pembayaran</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">

        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-receipt"></i> Tagihan Saya
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i> Paket Internet
                </a>
            </li>
            <li>
                <a href="../profil.php">
                    <i class="bi bi-person"></i> Profil
                </a>
            </li>
            <li>
                <a href="../../auth/logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Upload Pembayaran</h1>
                <p>Kirim bukti transfer pembayaran</p>
            </div>
        </div>

        <!-- ================= CARD ================= -->
        <div class="form-card">

            <h2>
                <?= $data['kode_invoice']; ?>
            </h2>

            <p>
                Total pembayaran:
                <strong>
                    Rp<?= number_format($data['jumlah_bayar']); ?>
                </strong>
            </p>

            <!-- ================= INFO BANK ================= -->
            <div
                style="
                    background: #fff7ed;
                    border: 1px solid #fed7aa;
                    padding: 20px;
                    border-radius: 20px;
                    margin-bottom: 25px;
                "
            >
                <h3
                    style="
                        margin-bottom: 15px;
                        color: #ea580c;
                    "
                >
                    Transfer Pembayaran
                </h3>

                <p style="margin-bottom: 8px;">
                    Bank BCA - 1234567890
                </p>

                <p style="margin-bottom: 8px;">
                    A/N Anuwani.net
                </p>

                <p style="color: #666;">
                    Upload bukti transfer setelah pembayaran berhasil dilakukan.
                </p>
            </div>

            <!-- ================= FORM ================= -->
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>
                        Upload Bukti Pembayaran
                    </label>

                    <input 
                        type="file"
                        name="bukti_pembayaran"
                        accept="image/*"
                        required
                    >
                </div>

                <button 
                    type="submit"
                    name="upload"
                >
                    Kirim Pembayaran
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>