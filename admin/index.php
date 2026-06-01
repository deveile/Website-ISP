<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$today = date('Y-m-d');
$log_file = __DIR__ . '/cron_last_run.txt';
$last_run = file_exists($log_file) ? file_get_contents($log_file) : '';

if ($last_run !== $today) {
    $sql_cek = "SELECT l.*, p.harga 
                FROM tb_langganan l
                JOIN tb_paket p ON l.id_paket = p.id_paket
                WHERE l.tanggal_selesai <= '$today' 
                AND l.status_langganan IN ('aktif', 'suspend')
                AND NOT EXISTS (
                    SELECT 1 FROM tb_transaksi t 
                    WHERE t.id_langganan = l.id_langganan 
                    AND t.status_pembayaran = 'belum_bayar'
                )";
                
    $query_cek = mysqli_query($koneksi, $sql_cek);

    while ($langganan = mysqli_fetch_assoc($query_cek)) {
        $id_langganan = $langganan['id_langganan'];
        $harga = $langganan['harga'];

        $kode_invoice = "INV-" . date('Ym') . "-" . rand(100, 999);
        $bulan_sekarang = date('n'); 
        $tahun_sekarang = date('Y');

        $sql_invoice = "INSERT INTO tb_transaksi (kode_invoice, id_langganan, jumlah_bayar, status_pembayaran, bulan_tagihan, tahun_tagihan) 
                        VALUES ('$kode_invoice', $id_langganan, '$harga', 'belum_bayar', '$bulan_sekarang', '$tahun_sekarang')";
        $insert_trx = mysqli_query($koneksi, $sql_invoice);

        if ($insert_trx) {
            $sql_update_langganan = "UPDATE tb_langganan SET 
                                        status_langganan = 'suspend' 
                                     WHERE id_langganan = $id_langganan";
            mysqli_query($koneksi, $sql_update_langganan);
        }
    }

    file_put_contents($log_file, $today);
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

$query_notif = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_notif
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");

$total_notif = mysqli_fetch_assoc($query_notif)['total_notif'];

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

    <style>
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
                width: 260px !important;
                min-width: 260px !important;
                max-width: 260px !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .dashboard-content {
                flex-grow: 1 !important;
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
                width: calc(100% - 70px) !important;
                margin-left: 0 !important; 
                padding-left: 30px !important; 
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
                border-radius: 8px !important;
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
            .admin-card-grid, .income-card {
                grid-template-columns: 1fr !important;
                width: 100% !important;
            }
        }
    </style>
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
                    <i class="bi bi-grid"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="paket/index.php">
                    <i class="bi bi-wifi"></i> <span>Kelola Paket</span>
                </a>
            </li>
            <li>
                <a href="customer/index.php">
                    <i class="bi bi-people"></i> <span>Data Pelanggan</span>
                </a>
            </li>
            <li>
                <a href="transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li>
                <a href="admin_user/index.php">
                    <i class="bi bi-person-plus"></i> <span>Kelola Admin</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </a>
            </li>
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