<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT
        p.id_pemasangan,
        c.nama_customer,
        pk.nama_paket,
        p.tanggal_pengajuan,
        p.tanggal_pasang,
        p.alamat_pasang,
        p.status_pemasangan,
        p.catatan
    FROM tb_pemasangan p
    LEFT JOIN tb_customer c
        ON p.id_customer = c.id_customer
    LEFT JOIN tb_paket pk
        ON p.id_paket = pk.id_paket
    ORDER BY p.id_pemasangan DESC
");

$query_notif_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");

$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'];

$query_notif_pemasangan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_pemasangan
    WHERE status_pemasangan = 'menunggu'
");

$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'];
$total_notif = $total_notif_transaksi + $total_notif_pemasangan;

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
    <title>Kelola Pemasangan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
        .table-responsive{
            width:100%;
            overflow-x:auto;
        }

        .table-responsive table{
            min-width:1200px;
        }
        .notif-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 50%;
            background: #ef4444; color: #fff;
            font-size: 11px; font-weight: 800;
            margin-left: auto; flex-shrink: 0;
            animation: pulse-badge 1.8s ease-in-out infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
            50%       { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
        }

        .status-active {
            background: #dcfce7; color: #166534;
            padding: 6px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            display: inline-block;
        }
        .status-pending {
            background: #fef3c7; color: #92400e;
            padding: 6px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            display: inline-block;
        }
        .status-process {
            background: #e0f2fe; color: #0369a1;
            padding: 6px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            display: inline-block;
        }
        .status-nonactive {
            background: #fee2e2; color: #991b1b;
            padding: 6px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            display: inline-block;
        }

        .table-action {
            display: flex !important;
            gap: 8px !important;
            align-items: center;
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
                top: 0; left: 0;
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
            <li><a href="../customer/index.php"><i class="bi bi-people"></i><span>Data Pelanggan</span></a></li>
            <li><a href="../pemasangan/index.php" class="active">
                <i class="bi bi-tools"></i>
                <span>Kelola Pemasangan</span>

                <?php if ($total_notif_pemasangan > 0): ?>
                    <span class="notif-badge">
                        <?= $total_notif_pemasangan; ?>
                    </span>
                <?php endif; ?>
                </a>
            </li>

            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i>
                    <span>Data Transaksi</span>

                    <?php if ($total_notif_transaksi > 0): ?>
                        <span class="notif-badge">
                            <?= $total_notif_transaksi; ?>
                        </span>
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

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Customer</th>
                            <th>Paket</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Tanggal Pasang</th>
                            <th>Alamat Pasang</th>
                            <th>Status</th>
                            <th>Catatan</th>
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
                                <td><?= htmlspecialchars($data['nama_customer']); ?></td>
                                <td><?= htmlspecialchars($data['nama_paket']); ?></td>
                                <td><?= tgl_indo($data['tanggal_pengajuan']); ?></td>
                                <td><?= tgl_indo($data['tanggal_pasang']); ?></td>
                                <td><?= htmlspecialchars($data['alamat_pasang']); ?></td>
                                <td>
                                    <?php
                                    // Menyesuaikan string dengan data asli enum MySQL
                                    $status = strtolower(trim($data['status_pemasangan']));

                                    if ($status == 'terpasang') {
                                        echo '<span class="status-active">Terpasang</span>';
                                    } elseif ($status == 'menunggu') {
                                        echo '<span class="status-pending">Menunggu</span>';
                                    } elseif ($status == 'diproses') {
                                        echo '<span class="status-process">Diproses</span>';
                                    } elseif ($status == 'dibatalkan') {
                                        echo '<span class="status-nonactive">Dibatalkan</span>';
                                    } else {
                                        echo '<span class="status-pending">' . htmlspecialchars($data['status_pemasangan']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($data['catatan']); ?></td>
                                <td>
                                    <div class="table-action">
                                        <a href="detail.php?id=<?= $data['id_pemasangan']; ?>" class="btn-edit">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">
                                    Belum ada data pemasangan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

<script>
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