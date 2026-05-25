<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'customer') {
    header("Location: ../../auth/login.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE status='aktif' ORDER BY id_paket DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Internet</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../tagihan/index.php"><i class="bi bi-receipt"></i> Tagihan Saya</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-wifi"></i> Paket Internet</a></li>
            <li><a href="../profile/index.php"><i class="bi bi-person"></i> Profile</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Paket Internet</h1>
                <p>Pilih paket terbaik untuk kebutuhan Anda</p>
            </div>
        </div>

        <div class="paket-grid">
            <?php while ($paket = mysqli_fetch_assoc($data)) : ?>
                <div class="customer-paket-card">
                    <h3><?= $paket['nama_paket']; ?></h3>
                    <h1><?= $paket['kecepatan']; ?></h1>
                    <h2>Rp <?= number_format($paket['harga']); ?></h2>
                    <p><?= nl2br($paket['deskripsi']); ?></p>
                    <a href="../pemasangan/index.php?id=<?= $paket['id_paket']; ?>" class="btn-orange">Pesan Sekarang</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

</body>
</html>