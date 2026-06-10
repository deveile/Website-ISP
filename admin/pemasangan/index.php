<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$limit = 10;
$halaman = (isset($_GET['halaman'])) ? max(1, (int)$_GET['halaman']) : 1;
$awalData = ($halaman - 1) * $limit;

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pemasangan");
$totalData = mysqli_fetch_assoc($query_total)['total'] ?? 0;
$jumlahHalaman = ceil($totalData / $limit);

$query = mysqli_query($koneksi, "
    SELECT p.id_pemasangan, c.nama_customer, pk.nama_paket, p.tanggal_pengajuan, p.tanggal_pasang, p.alamat_pasang, p.status_pemasangan, p.catatan
    FROM tb_pemasangan p
    LEFT JOIN tb_customer c ON p.id_customer = c.id_customer
    LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
    ORDER BY p.id_pemasangan DESC
    LIMIT $awalData, $limit
");

$query_notif_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_transaksi WHERE status_pembayaran = 'menunggu_verifikasi'");
$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'] ?? 0;

$query_notif_pemasangan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pemasangan WHERE status_pemasangan = 'menunggu'");
$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'] ?? 0;

function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '—';
    $bulan_array = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . '/' . sprintf("%02d", $split[1]) . '/' . $split[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemasangan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
    .notif-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ef4444;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        margin-left: auto;
        flex-shrink: 0;
        animation: pulse-badge 1.8s ease-in-out infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
        50%       { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
    }

    .table-card {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 24px !important; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02) !important;
    }
    .table-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 20px !important;
        flex-wrap: wrap !important;
        gap: 15px !important;
        padding: 0 !important;
    }
    .table-header h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    table th {
        background: #ff6600 !important;
        color: #ffffff !important;
        text-transform: uppercase !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        padding: 14px 12px !important;
        border-bottom: 1px solid #e4e4e7 !important;
        border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
    }
    table th:last-child {
        border-right: none !important;
    }
    table td {
        padding: 12px 14px !important;
        border-bottom: 1px solid #e4e4e7 !important;
        border-right: 1px solid #e4e4e7 !important;
        color: #27272a !important;
        font-weight: 400;
        vertical-align: middle;
    }
    table td:last-child {
        border-right: none !important;
    }
    table tr:last-child td {
        border-bottom: none !important;
    }

    .status-active    { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .status-pending   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .status-process   { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .status-nonactive { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }

    .btn-detail-tr {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;
        background: #0091ff; color: #ffffff !important;
        border: none; text-decoration: none; transition: all .2s; cursor: pointer;
    }
    .btn-detail-tr:hover { 
        background: #0077dd; 
        transform: translateY(-1px);
    }
    
    tr.row-menunggu td { background: #fffdf0; }
    tr.row-menunggu:hover td { background: #fef9c3 !important; }

    .dashboard-layout {
        display: flex !important;
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
    }
    .sidebar-toggle {
        display: flex !important;
        flex-direction: column;
        gap: 4px;
        background: #f0f0f0;
        border: none;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        margin-right: 15px;
        transition: background 0.2s;
    }
    .sidebar-toggle:hover {
        background: #e0e0e0;
    }
    .sidebar-toggle span {
        display: block;
        width: 20px;
        height: 2.5px;
        background-color: #333;
        border-radius: 2px;
    }

    .pagination-container { display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e4e4e7; flex-wrap: wrap; gap: 10px; }
    .pagination-info { font-size: 13px; color: #71717a; }
    .pagination-list { display: flex; gap: 5px; list-style: none; padding: 0; margin: 0; }
    .pagination-list a { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 8px; border: 1.5px solid #e4e4e7; background: #fff; color: #27272a; transition: all 0.2s; }
    .pagination-list a:hover { background: #f4f4f5; }
    .pagination-list .active-page a { background: #ff6600; color: #fff; border-color: #ff6600; pointer-events: none; }
    .pagination-list .disabled-page a { color: #a1a1aa; background: #fafafa; border-color: #e4e4e7; pointer-events: none; }

    @media (min-width: 992px) {
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            z-index: 1000 !important;
            overflow-y: auto !important;
            width: 260px !important;
            min-width: 260px !important;
            max-width: 260px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .dashboard-content {
            flex-grow: 1 !important;
            margin-left: 260px !important; 
            width: calc(100% - 260px) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .topbar {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }
        .sidebar.collapsed {
            width: 70px !important;
            min-width: 70px !important;
            max-width: 70px !important;
            padding: 24px 8px !important;
        }
        .sidebar.collapsed + .dashboard-content {
            margin-left: 70px !important;
            width: calc(100% - 70px) !important;
        }
        .sidebar.collapsed .sidebar-logo h2,
        .sidebar.collapsed ul li a span,
        .sidebar.collapsed .notif-badge {
            display: none !important; 
        }
        .sidebar.collapsed .sidebar-logo {
            justify-content: center !important;
            padding: 0 !important;
        }
        .sidebar.collapsed ul li a {
            justify-content: center !important;
            padding: 12px 0 !important;
        }
        .sidebar.collapsed ul li a i {
            margin: 0 !important;
            font-size: 20px !important; 
        }
    }
    @media (max-width: 991px) {
        .dashboard-layout {
            flex-direction: column !important;
        }
        .sidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 260px !important;
            min-width: 260px !important;
            height: 100vh !important;
            background: #ffffff !important;
            z-index: 9999 !important;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            padding: 24px !important;
            overflow-y: auto;
        }
        .sidebar.active {
            transform: translateX(0); 
        }
        .dashboard-content {
            width: 100% !important;
            padding: 20px !important;
        }
        .topbar {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            margin-bottom: 24px;
        }
    }
    .logout-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .logout-modal.show {
        display: flex;
    }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> <span>Dashboard</span></a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> <span>Kelola Paket</span></a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-tools"></i> <span>Kelola Pemasangan</span>
                    <?php if ($total_notif_pemasangan > 0): ?>
                        <span class="notif-badge"><?= $total_notif_pemasangan; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif_transaksi > 0): ?>
                        <span class="notif-badge"><?= $total_notif_transaksi; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> <span>Kelola Admin</span></a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Kelola Pemasangan</h1>
                <p>Kelola seluruh data pemasangan Anuwani.net</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Data Pemasangan</h3>
            </div>

            <div style="overflow-x:auto; width: 100%; border-radius: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Nama</th>
                            <th>Paket</th>
                            <th>Tgl Pengajuan</th>
                            <th>Tgl Pasang</th>
                            <th>Alamat Pasang</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $awalData + 1;
                        if (mysqli_num_rows($query) > 0) :
                            while ($data = mysqli_fetch_assoc($query)) : 
                                $status = strtolower(trim($data['status_pemasangan'] ?? ''));
                        ?>
                        <tr class="<?= $status === 'menunggu' ? 'row-menunggu' : '' ?>">
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($data['nama_customer'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($data['nama_paket'] ?? '—'); ?></td>
                            <td><?= tgl_indo($data['tanggal_pengajuan']); ?></td>
                            <td><?= tgl_indo($data['tanggal_pasang']); ?></td>
                            <td><?= htmlspecialchars($data['alamat_pasang'] ?? ''); ?></td>
                            <td>
                                <?php 
                                if ($status == 'terpasang') {
                                    echo '<span class="status-active"><i class="bi bi-check-circle-fill"></i> Terpasang</span>';
                                } elseif ($status == 'menunggu') {
                                    echo '<span class="status-pending"><i class="bi bi-hourglass-split"></i> Menunggu</span>';
                                } elseif ($status == 'diproses') {
                                    echo '<span class="status-process"><i class="bi bi-gear-fill"></i> Diproses</span>';
                                } elseif ($status == 'dibatalkan') {
                                    echo '<span class="status-nonactive"><i class="bi bi-x-circle-fill"></i> Dibatalkan</span>';
                                } else {
                                    echo '<span class="status-pending">' . htmlspecialchars($data['status_pemasangan']) . '</span>';
                                }
                                ?>
                            </td>
                            <td style="color:#a1a1aa; font-size:13px;"><?= !empty($data['catatan']) ? htmlspecialchars($data['catatan']) : '—'; ?></td>
                            <td>
                                <div style="display:flex; justify-content:center;">
                                    <a href="detail.php?id=<?= $data['id_pemasangan']; ?>" class="btn-detail-tr">Detail</a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else :
                        ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:40px; color:#a1a1aa;">
                                <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                                Belum ada data pemasangan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($jumlahHalaman > 1) : ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Menampilkan data ke-<?= $awalData + 1; ?> sampai <?= min($awalData + $limit, $totalData); ?> dari total <strong><?= $totalData; ?></strong> data
                </div>
                <ul class="pagination-list">
                    <li class="<?= ($halaman <= 1) ? 'disabled-page' : '' ?>">
                        <a href="index.php?halaman=<?= $halaman - 1; ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>

                    <?php
                    $start_loop = max(1, $halaman - 2);
                    $end_loop = min($jumlahHalaman, $halaman + 2);
                    
                    if ($start_loop > 1) {
                        echo '<li><a href="index.php?halaman=1">1</a></li>';
                        if ($start_loop > 2) echo '<li class="disabled-page"><a href="#">...</a></li>';
                    }

                    for ($i = $start_loop; $i <= $end_loop; $i++) {
                        if ($halaman == $i) {
                            echo '<li class="active-page"><a href="#">' . $i . '</a></li>';
                        } else {
                            echo '<li><a href="index.php?halaman=' . $i . '">' . $i . '</a></li>';
                        }
                    }

                    if ($end_loop < $jumlahHalaman) {
                        if ($end_loop < $jumlahHalaman - 1) echo '<li class="disabled-page"><a href="#">...</a></li>';
                        echo '<li><a href="index.php?halaman=' . $jumlahHalaman . '">' . $jumlahHalaman . '</a></li>';
                    }
                    ?>

                    <li class="<?= ($halaman >= $jumlahHalaman) ? 'disabled-page' : '' ?>">
                        <a href="index.php?halaman=<?= $halaman + 1; ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

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

<script>
    function openLogoutModal() {
        document.getElementById('logoutModal').classList.add('show');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                if (window.innerWidth >= 992) {
                    sidebar.classList.toggle('collapsed');
                } else {
                    sidebar.classList.toggle('active');
                }
                e.stopPropagation();
            });

            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    });
</script>
</body>
</html>