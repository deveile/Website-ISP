<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT 
        tb_customer.*, 
        tb_langganan.status_langganan, 
        tb_langganan.tanggal_selesai, 
        tb_paket.nama_paket 
    FROM tb_customer 
    LEFT JOIN tb_langganan ON tb_customer.id_customer = tb_langganan.id_customer 
    LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket 
    ORDER BY tb_customer.id_customer DESC
");

function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $bulan_array = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan_array[(int)$split[1]] . ' ' . $split[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="../../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="../transaksi/index.php"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> Tambah Admin</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Data Pelanggan</h1>
                <p>Kelola seluruh data customer Anuwani.net</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Data Pelanggan</h3>
                <a href="tambah.php" class="btn-orange">
                    <i class="bi bi-plus-circle"></i> Tambah Pelanggan
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Paket</th>
                        <th>Aktif Sampai</th> <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($query)) : 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $data['nama_customer']; ?></td>
                        <td><?= $data['email_customer']; ?></td>
                        <td><?= $data['telepon_customer']; ?></td>
                        <td><?= $data['nama_paket'] ?? '-'; ?></td>
                        
                        <td>
                            <span style="font-weight: 500; color: #333;">
                                <?= tgl_indo($data['tanggal_selesai']); ?>
                            </span>
                        </td>
                        
                        <td>
                            <?php if (isset($data['status_langganan']) && strtolower($data['status_langganan']) == 'aktif') : ?>
                                <span class="status-active">Aktif</span>
                            <?php else : ?>
                                <span class="status-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-action">
                                <a href="detail.php?id=<?= $data['id_customer']; ?>" class="btn-edit">Detail</a>
                                <a href="hapus.php?id=<?= $data['id_customer']; ?>" class="btn-delete" onclick="return confirm('Hapus customer ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>
</body>
</html>