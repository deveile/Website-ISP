<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';
$data = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Paket</title>
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
            <li><a href="index.php" class="active"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="../transaksi/index.php"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> Tambah Admin</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <?php if (isset($_GET['success'])) : ?>
            <div class="success-popup">
                <i class="bi bi-check-circle-fill"></i>
                <?php 
                    $msg = ['tambah' => 'Paket berhasil ditambahkan', 'aktif' => 'Paket berhasil diaktifkan', 'nonaktif' => 'Paket berhasil dinonaktifkan'];
                    echo $msg[$_GET['success']] ?? '';
                ?>
            </div>
        <?php endif; ?>

        <div class="topbar">
            <h1>Kelola Paket</h1>
            <p>Tambah dan kelola paket internet</p>
        </div>

        <div class="paket-admin-layout">
            <div class="paket-grid">
                <?php while ($p = mysqli_fetch_assoc($data)) : ?>
                    <div class="paket-admin-card">
                        <h3><?= $p['nama_paket']; ?></h3>
                        <h1><?= $p['kecepatan']; ?></h1>
                        <h2>Rp <?= number_format($p['harga']); ?></h2>
                        <p><?= nl2br($p['deskripsi']); ?></p>
                        <div class="paket-action">
                            <a href="edit.php?id=<?= $p['id_paket']; ?>" class="btn-edit">Edit</a>
                            <?php if ($p['status'] == 'aktif') : ?>
                                <a href="#" class="btn-delete" onclick="openNonaktifModal(<?= $p['id_paket']; ?>)">Nonaktifkan</a>
                            <?php else : ?>
                                <a href="#" class="btn-edit" onclick="openAktifModal(<?= $p['id_paket']; ?>)">Aktifkan</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="form-paket-card">
                <h3>Tambah Paket</h3>
                <form id="formPaket" action="tambah.php" method="POST">
                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="nama_paket" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" required>
                    </div>
                    <div class="form-group">
                        <label>Kecepatan</label>
                        <input type="text" name="kecepatan" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="4"></textarea>
                    </div>
                    <button type="button" class="btn-orange" onclick="openTambahModal()">Simpan Paket</button>
                </form>
            </div>
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

<div class="logout-modal" id="tambahModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-plus-circle"></i>
        </div>
        <h2>Tambah Paket</h2>
        <p>Apakah Anda yakin ingin menambahkan paket baru?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeTambahModal()">
                Batal
            </button>
            <button class="btn-confirm" onclick="submitTambahPaket()">
                Ya, Simpan
            </button>
        </div>
    </div>
</div>

<div class="logout-modal" id="nonaktifModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-exclamation-circle"></i></div>
        <h2>Nonaktifkan Paket</h2>
        <p>Apakah Anda yakin ingin menonaktifkan paket ini?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeNonaktifModal()">Batal</button>
            <a href="#" class="btn-confirm" id="btnNonaktif">Ya, Nonaktifkan</a>
        </div>
    </div>
</div>

<div class="logout-modal" id="aktifModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-check-circle"></i></div>
        <h2>Aktifkan Paket</h2>
        <p>Aktifkan kembali paket ini?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeAktifModal()">Batal</button>
            <a href="#" class="btn-confirm" id="btnAktif">Ya, Aktifkan</a>
        </div>
    </div>
</div>
</body>
</html>