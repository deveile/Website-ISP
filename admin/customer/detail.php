<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'];

/* ================= QUERY UPDATE: MENAMBAHKAN TANGGAL SELESAI ================= */
$query = mysqli_query($koneksi, "
    SELECT 
        tb_customer.*, 
        tb_langganan.status_langganan, 
        tb_langganan.tanggal_selesai, 
        tb_paket.nama_paket 
    FROM tb_customer 
    LEFT JOIN tb_langganan ON tb_customer.id_customer = tb_langganan.id_customer 
    LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket 
    WHERE tb_customer.id_customer = '$id'
");

$data = mysqli_fetch_assoc($query);

if(!$data){
    header("Location: index.php");
    exit;
}

/* ================= HELPER FORMAT TANGGAL INDONESIA ================= */
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
    <title>Detail Pelanggan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li>
                <a href="../../auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Detail Pelanggan</h1>
                <p>Informasi lengkap customer</p>
            </div>
        </div>

        <div class="detail-customer-card">
            <div class="detail-header">
                <div class="detail-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h2><?= $data['nama_customer']; ?></h2>
                    <p>Customer Anuwani.net</p>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span>Email</span>
                    <strong><?= $data['email_customer']; ?></strong>
                </div>

                <div class="detail-item">
                    <span>Telepon</span>
                    <strong><?= $data['telepon_customer']; ?></strong>
                </div>

                <div class="detail-item">
                    <span>Paket Internet</span>
                    <strong><?= $data['nama_paket'] ?? '-'; ?></strong>
                </div>

                <div class="detail-item">
                    <span>Status Paket</span>
                    <strong>
                        <?php if(isset($data['status_langganan']) && strtolower($data['status_langganan']) == 'aktif') : ?>
                            <span class="status-active">Aktif</span>
                        <?php else : ?>
                            <span class="status-pending">Pending</span>
                        <?php endif; ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Aktif Sampai</span>
                    <strong style="color: #333;">
                        <?= tgl_indo($data['tanggal_selesai']); ?>
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Sumber Customer</span>
                    <strong>
                        <?= $data['sumber_customer']; ?>
                    </strong>
                </div>

                <div class="detail-item full">
                    <span>Alamat</span>
                    <strong><?= $data['alamat_customer']; ?></strong>
                </div>
            </div>

            <div class="detail-action">
                <a href="edit.php?id=<?= $data['id_customer']; ?>" class="btn-orange">
                    <i class="bi bi-pencil-square"></i> Edit Customer
                </a>
                <a href="index.php" class="btn-delete">Kembali</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>