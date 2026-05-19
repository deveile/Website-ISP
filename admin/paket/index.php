<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

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
</head>

<body>
    <div class="dashboard-layout">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <img src="../../assets/images/logo.png">
                <h2>Anuwani</h2>
            </div>
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="bi bi-grid"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="index.php" class="active">
                        <i class="bi bi-wifi"></i>
                        Kelola Paket
                    </a>
                </li>
                <li>
                    <a href="../customer/index.php">
                        <i class="bi bi-people"></i>
                        Data Pelanggan
                    </a>
                </li>
                <li>
                    <a href="../transaksi/index.php">
                        <i class="bi bi-credit-card"></i>
                        Data Transaksi
                    </a>
                </li>
                <li>
                    <a href="../admin_user/index.php">
                        <i class="bi bi-person-plus"></i>
                        Tambah Admin
                    </a>
                </li>
                <li>
                    <a href="../../auth/logout.php"
                    onclick="return confirm('Apakah Anda yakin ingin logout?')">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- CONTENT -->
        <div class="dashboard-content">
            <div class="topbar">
                <div>
                    <h1>Kelola Paket</h1>
                    <p>Tambah dan kelola paket internet</p>
                </div>
            </div>

            <div class="paket-admin-layout">
                <!-- LIST PAKET -->
                <div class="paket-grid">
                    <?php while ($paket = mysqli_fetch_assoc($data)) : ?>
                        <div class="paket-admin-card">
                            <h3><?= $paket['nama_paket']; ?></h3>
                            <h2>Rp <?= number_format($paket['harga']); ?></h2>
                            <p><?= nl2br($paket['deskripsi']); ?></p>

                            <div class="paket-action">
                                <a href="edit.php?id=<?= $paket['id_paket']; ?>" class="btn-edit">
                                    Edit
                                </a>
                                <a href="hapus.php?id=<?= $paket['id_paket']; ?>" class="btn-delete" onclick="return confirm('Hapus paket?')">
                                    Hapus
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- FORM -->
                <div class="form-paket-card">
                    <h3>Tambah Paket</h3>
                    <form action="tambah.php" method="POST">
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

                        <button type="submit" class="btn-orange">
                            Simpan Paket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>