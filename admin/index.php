<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$query_customer = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_customer
    FROM tb_customer
");
$total_customer = mysqli_fetch_assoc($query_customer)['total_customer'];

$query_paket = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_paket
    FROM tb_paket
");
$total_paket = mysqli_fetch_assoc($query_paket)['total_paket'];

$query_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_transaksi
    FROM tb_transaksi
");
$total_transaksi = mysqli_fetch_assoc($query_transaksi)['total_transaksi'];

$query_pending = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_pending
    FROM tb_transaksi
    WHERE 
        status_pembayaran = 'belum_bayar'
        OR status_pembayaran = 'menunggu_verifikasi'
");
$pending = mysqli_fetch_assoc($query_pending)['total_pending'];

$query_income = mysqli_query($koneksi, "
    SELECT SUM(jumlah_bayar) AS total_pendapatan
    FROM tb_transaksi
    WHERE status_pembayaran = 'lunas'
");
$pendapatan = mysqli_fetch_assoc($query_income);
$total_pendapatan = $pendapatan['total_pendapatan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link class="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="paket/index.php">
                    <i class="bi bi-wifi"></i> Kelola Paket
                </a>
            </li>
            <li>
                <a href="customer/index.php">
                    <i class="bi bi-people"></i> Data Pelanggan
                </a>
            </li>
            <li>
                <a href="transaksi/index.php">
                    <i class="bi bi-credit-card"></i> Data Transaksi
                </a>
            </li>
            <li>
                <a href="admin_user/index.php">
                    <i class="bi bi-person-plus"></i> Tambah Admin
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Dashboard Admin</h1>
                <p>Selamat datang, 
                    <strong><?php echo $_SESSION['username']; ?></strong>
                </p>
            </div>
        </div>

        <div class="income-card">
            <div>
                <h5>Total Pendapatan</h5>
                <h1>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h1>
            </div>
            <i class="bi bi-cash-stack"></i>
        </div>

        <div class="admin-card-grid">

            <div class="admin-card">
                <div>
                    <h5>Total Pelanggan</h5>
                    <h2><?php echo $total_customer; ?></h2>
                </div>
                <i class="bi bi-people-fill"></i>
            </div>

            <div class="admin-card">
                <div>
                    <h5>Transaksi Pending</h5>
                    <h2><?php echo $pending; ?></h2>
                </div>
                <i class="bi bi-hourglass-split"></i>
            </div>

            <div class="admin-card">
                <div>
                    <h5>Total Paket</h5>
                    <h2><?php echo $total_paket; ?></h2>
                </div>
                <i class="bi bi-wifi"></i>
            </div>

            <div class="admin-card">
                <div>
                    <h5>Total Transaksi</h5>
                    <h2><?php echo $total_transaksi; ?></h2>
                </div>
                <i class="bi bi-credit-card"></i>
            </div>

        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-box-arrow-right"></i>
        </div>
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar?</p>

        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
            <a href="../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

</body>
</html>