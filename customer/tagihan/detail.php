<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_transaksi.*,
        tb_customer.nama_customer,
        tb_customer.email_customer,
        tb_customer.telepon_customer,
        tb_paket.nama_paket,
        tb_paket.kecepatan
     FROM tb_transaksi
     LEFT JOIN tb_customer ON tb_transaksi.id_customer = tb_customer.id_customer
     LEFT JOIN tb_paket ON tb_customer.id_paket = tb_paket.id_paket
     WHERE tb_transaksi.id_transaksi = '$id'"
);

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Detail Tagihan</title>

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
                <a href="../../auth/logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Detail Tagihan</h1>
                <p>Informasi pembayaran customer</p>
            </div>
        </div>

        <!-- ================= DETAIL CARD ================= -->
        <div class="detail-customer-card">

            <div class="detail-header">
                
                <div class="detail-avatar">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <h2>
                        <?= $data['kode_invoice']; ?>
                    </h2>
                    <p>
                        <?= $data['nama_customer']; ?>
                    </p>
                </div>

            </div>

            <!-- ================= GRID ================= -->
            <div class="detail-grid">

                <div class="detail-item">
                    <span>Paket Internet</span>
                    <strong>
                        <?= $data['nama_paket']; ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Kecepatan</span>
                    <strong>
                        <?= $data['kecepatan']; ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Periode Tagihan</span>
                    <strong>
                        <?= $data['bulan_tagihan']; ?>/<?= $data['tahun_tagihan']; ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Total Tagihan</span>
                    <strong>
                        Rp<?= number_format($data['jumlah_bayar']); ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Status Pembayaran</span>
                    <strong>
                        <?php if ($data['status_pembayaran'] == 'Lunas') : ?>
                            <span class="status-active">
                                Lunas
                            </span>
                        <?php elseif ($data['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
                            <span class="status-pending">
                                Menunggu Konfirmasi
                            </span>
                        <?php else : ?>
                            <span class="status-belum">
                                Belum Bayar
                            </span>
                        <?php endif; ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Tanggal</span>
                    <strong>
                        <?= $data['tanggal_transaksi']; ?>
                    </strong>
                </div>

              <!-- ================= BUKTI ================= -->
                <div class="detail-item full">
                    <span>Bukti Pembayaran</span>

                    <?php if ($data['bukti_pembayaran'] != '') : ?>

                        <img 
                            src="../../assets/uploads/bukti/<?= $data['bukti_pembayaran']; ?>" 
                            class="img-bukti"
                        >

                    <?php else : ?>

                        <p style="margin-top: 15px; color: #777;">
                            Belum upload bukti pembayaran
                        </p>

                    <?php endif; ?>
                </div>

            </div>

            <!-- ================= BUTTON ================= -->
            <div class="detail-action">

                <?php if ($data['status_pembayaran'] == 'Belum Bayar') : ?>
                    <a 
                        href="bayar.php?id=<?= $data['id_transaksi']; ?>" 
                        class="btn-orange"
                    >
                        Bayar Sekarang
                    </a>
                <?php endif; ?>

                <a 
                    href="index.php" 
                    class="btn-delete"
                >
                    Kembali
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>