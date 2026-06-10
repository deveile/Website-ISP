<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_customer");
$total_data = mysqli_fetch_assoc($query_total)['total'] ?? 0;
$total_pages = ceil($total_data / $limit);

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
    LIMIT $limit OFFSET $offset
");

$query_notif_pemasangan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_pemasangan
    WHERE status_pemasangan = 'menunggu'
");
$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'] ?? 0;

$query_notif_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'] ?? 0;

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
    .status-active {
        background: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-nonactive {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
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
    }

    .table-responsive-container {
        overflow-x: auto;
        width: 100%;
        border-radius: 8px;
    }

    .table-responsive-container table {
        width: 100% !important;
        min-width: 900px;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }

    .table-responsive-container table th {
        background: #ff6600 !important;
        color: #ffffff !important;
        text-transform: uppercase !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        letter-spacing: .5px;
        padding: 14px 12px !important;
        border-bottom: 1px solid #e4e4e7 !important;
        border-right: 1px solid rgba(255,255,255,.15) !important;
    }

    .table-responsive-container table th:last-child {
        border-right: none !important;
    }

    .table-responsive-container table td {
        padding: 12px 14px !important;
        border-bottom: 1px solid #e4e4e7 !important;
        border-right: 1px solid #e4e4e7 !important;
        color: #27272a !important;
        vertical-align: middle;
    }

    .table-responsive-container table td:last-child {
        border-right: none !important;
    }

    .table-responsive-container table tr:last-child td {
        border-bottom: none !important;
    }

    .table-responsive-container table tbody tr:hover td {
        background: #f8fafc !important;
    }
        
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        padding-left: 20px;
        padding-right: 20px;
        padding-bottom: 20px;
    }
    .pagination-info {
        font-size: 14px;
        color: #666;
    }
    .pagination-buttons {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pagination-buttons a, .pagination-buttons span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: #4a5568;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .pagination-buttons a:hover {
        background: #edf2f7;
        border-color: #cbd5e1;
    }
    .pagination-buttons .active-page {
        background: #ff6b00;
        color: #ffffff;
        border-color: #ff6b00;
        font-weight: 600;
    }
    .pagination-buttons .disabled-btn {
        color: #cbd5e1;
        background: #f8fafc;
        pointer-events: none;
        border-color: #e2e8f0;
    }
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
    @media (max-width: 575px) {
        .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 15px;
        }
        .table-header .btn-orange {
            width: 100%;
            justify-content: center;
        }
    }

    .btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    background: #0091ff;
    color: #ffffff !important;
    text-decoration: none;
    border: none;
    transition: all .2s;
}

    .btn-edit:hover {
        background: #0077dd;
        transform: translateY(-1px);
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
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li>
                <a href="../pemasangan/index.php">
                    <i class="bi bi-tools"></i>
                    <span>Kelola Pemasangan</span>
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

            <div class="table-responsive-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Paket</th>
                            <th>Aktif Sampai</th> 
                            <th style="text-align: center;">Status</th>
                            <th style="width: 160px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $offset + 1;
                        if (mysqli_num_rows($query) > 0) :
                            while ($data = mysqli_fetch_assoc($query)) : 
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($data['nama_customer'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($data['email_customer'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($data['telepon_customer'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($data['nama_paket'] ?? '-'); ?></td>
                            <td>
                                <span style="font-weight: 500; color: #333;">
                                    <?= tgl_indo($data['tanggal_selesai']); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <?php 
                                $status = strtolower($data['status_langganan'] ?? '');
                                if ($status == 'aktif') : 
                                ?>
                                    <span class="status-active">Aktif</span>
                                <?php elseif ($status == 'suspend') : ?>
                                    <span class="status-pending">Suspend</span>
                                <?php elseif ($status == 'berhenti') : ?>
                                    <span class="status-nonactive">Berhenti</span>
                                <?php else : ?>
                                    <span class="status-pending">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-action" style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="detail.php?id=<?= $data['id_customer']; ?>" class="btn-edit">Detail</a>
                                    <a href="hapus.php?id=<?= $data['id_customer']; ?>" class="btn-delete" onclick="return confirm('Hapus customer ini?')" style="background: #ef4444; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 600;">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else :
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px; color: #666;">Data customer belum ada.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) : ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan data <b><?= $offset + 1; ?></b> sampai <b><?= min($offset + $limit, $total_data); ?></b> dari <b><?= $total_data; ?></b> total customer.
                </div>
                <div class="pagination-buttons">
                    <?php if ($page > 1) : ?>
                        <a href="index.php?page=<?= $page - 1; ?>"><i class="bi bi-chevron-left"></i></a>
                    <?php else : ?>
                        <span class="disabled-btn"><i class="bi bi-chevron-left"></i></span>
                    <?php endif; ?>

                    <?php
                    $start_loop = max(1, $page - 2);
                    $end_loop = min($total_pages, $page + 2);
                    
                    if ($start_loop > 1) {
                        echo '<a href="index.php?page=1">1</a>';
                        if ($start_loop > 2) echo '<span>...</span>';
                    }

                    for ($i = $start_loop; $i <= $end_loop; $i++) {
                        if ($page == $i) {
                            echo '<span class="active-page">' . $i . '</span>';
                        } else {
                            echo '<a href="index.php?page=' . $i . '">' . $i . '</a>';
                        }
                    }

                    if ($end_loop < $total_pages) {
                        if ($end_loop < $total_pages - 1) echo '<span>...</span>';
                        echo '<a href="index.php?page=' . $total_pages . '">' . $total_pages . '</a>';
                    }
                    ?>

                    <?php if ($page < $total_pages) : ?>
                        <a href="index.php?page=<?= $page + 1; ?>"><i class="bi bi-chevron-right"></i></a>
                    <?php else : ?>
                        <span class="disabled-btn"><i class="bi bi-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

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