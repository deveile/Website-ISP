<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= CUSTOMER ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_customer.*,
        tb_paket.nama_paket,
        tb_paket.harga AS harga_paket,
        tb_paket.kecepatan,
        tb_paket.deskripsi
     FROM tb_customer
     LEFT JOIN tb_paket ON tb_customer.id_paket = tb_paket.id_paket
     WHERE tb_customer.id_user = '$id_user'"
);

$data = mysqli_fetch_assoc($query);

/* ================= TAGIHAN BULAN INI ================= */
$bulan = date('n');
$tahun = date('Y');

$tagihan = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_transaksi
     WHERE id_customer = '".$data['id_customer']."'
     AND bulan_tagihan = '$bulan'
     AND tahun_tagihan = '$tahun'
     ORDER BY id_transaksi DESC
     LIMIT 1"
);

$t = mysqli_fetch_assoc($tagihan);

/* ================= RIWAYAT TAGIHAN ================= */
$riwayat = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_transaksi
     WHERE id_customer = '".$data['id_customer']."'
     ORDER BY id_transaksi DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Dashboard Customer</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">

        <div class="sidebar-logo">
            <img src="../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="tagihan/index.php">
                    <i class="bi bi-receipt"></i> Tagihan Saya
                </a>
            </li>
            <li>
                <a href="paket/index.php">
                    <i class="bi bi-wifi"></i> Paket Internet
                </a>
            </li>
            <li>
                <a href="profile/index.php">
                    <i class="bi bi-person"></i> Profile    
                </a>
            </li>
            <li>
                <a href="../auth/logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <!-- ================= TOPBAR ================= -->
        <div class="topbar">
            <h1>Halo, <?= $data['nama_customer']; ?></h1>
            <p>Selamat datang kembali</p>
        </div>

        <!-- ================= HERO CARD ================= -->
        <div class="customer-hero-card">

            <div class="hero-left">

                <span class="hero-label">
                    Paket Aktif
                </span>

                <h1>
                    <?= $data['nama_paket']; ?>
                </h1>

                <p class="hero-speed">
                    <?= $data['kecepatan']; ?>
                </p>

                <div class="hero-info-wrapper">

                    <div class="hero-info-box">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <span>Jatuh Tempo</span>
                            <h3>20 April 2026</h3>
                        </div>
                    </div>

                    <div class="hero-info-box">
                        <i class="bi bi-wallet2"></i>
                        <div>
                            <span>Tagihan Bulan Ini</span>
                            <h3>
                                Rp<?= number_format($t['jumlah_bayar'] ?? 0); ?>
                            </h3>
                        </div>
                    </div>

                </div>

            </div>

            <div class="hero-right">

                <i class="bi bi-wifi hero-wifi-icon"></i>

                <?php if ($t) : ?>
                    <?php if ($t['status_pembayaran'] != 'Lunas') : ?>
                        <a 
                            href="tagihan/bayar.php?id=<?= $t['id_transaksi']; ?>" 
                            class="hero-button"
                        >
                            Bayar Tagihan
                        </a>
                    <?php else : ?>
                        <a 
                            href="tagihan/index.php" 
                            class="hero-button"
                        >
                            Lihat Tagihan
                        </a>
                    <?php endif; ?>
                <?php else : ?>
                    <button class="hero-button disabled-btn">
                        Belum Ada Tagihan
                    </button>
                <?php endif; ?>

            </div>

        </div>

        <!-- ================= RIWAYAT ================= -->
        <div class="table-card">

            <div class="table-header">
                <h3>
                    Riwayat Tagihan Terakhir
                </h3>
                <a 
                    href="tagihan/index.php" 
                    class="btn-orange-outline"
                >
                    Lihat Semua
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = mysqli_fetch_assoc($riwayat)) : ?>
                        <tr>
                            <td>
                                <?= $r['kode_invoice']; ?>
                            </td>

                            <td>
                                <?= date(
                                    'F Y',
                                    strtotime($r['tahun_tagihan'] . '-' . $r['bulan_tagihan'] . '-01')
                                ); ?>
                            </td>

                            <td>
                                Rp<?= number_format($r['jumlah_bayar']); ?>
                            </td>

                            <td>
                                <?php if ($r['status_pembayaran'] == 'Lunas') : ?>
                                    <span class="status-active">
                                        Lunas
                                    </span>
                                <?php elseif ($r['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
                                    <span class="status-pending">
                                        Menunggu
                                    </span>
                                <?php else : ?>
                                    <span class="status-belum">
                                        Belum Bayar
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= $r['tanggal_transaksi']; ?>
                            </td>

                            <td>
                                <?php if ($r['status_pembayaran'] == 'Lunas') : ?>
                                    <a 
                                        href="tagihan/detail.php?id=<?= $r['id_transaksi']; ?>" 
                                        class="btn-detail"
                                    >
                                        Detail
                                    </a>
                                <?php elseif ($r['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
                                    <a 
                                        href="tagihan/detail.php?id=<?= $r['id_transaksi']; ?>" 
                                        class="btn-upload"
                                    >
                                        Menunggu
                                    </a>
                                <?php else : ?>
                                    <a 
                                        href="tagihan/bayar.php?id=<?= $r['id_transaksi']; ?>" 
                                        class="btn-bayar"
                                    >
                                        Bayar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>