<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

$sql = "
SELECT
    tb_transaksi.*,
    tb_customer.nama_customer,
    tb_customer.telepon_customer,
    tb_customer.email_customer,
    tb_customer.alamat_customer,

    CASE
        WHEN tb_transaksi.jenis_transaksi = 'upgrade'
        THEN paket_baru.nama_paket
        ELSE paket_lama.nama_paket
    END AS nama_paket,

    CASE
        WHEN tb_transaksi.jenis_transaksi = 'upgrade'
        THEN paket_baru.kecepatan
        ELSE paket_lama.kecepatan
    END AS kecepatan,

    tb_langganan.id_customer

FROM tb_transaksi

INNER JOIN tb_langganan
    ON tb_transaksi.id_langganan = tb_langganan.id_langganan

INNER JOIN tb_customer
    ON tb_langganan.id_customer = tb_customer.id_customer

LEFT JOIN tb_paket paket_lama
    ON tb_langganan.id_paket = paket_lama.id_paket

LEFT JOIN tb_paket paket_baru
    ON tb_transaksi.id_paket_baru = paket_baru.id_paket

WHERE tb_transaksi.id_transaksi = $id
LIMIT 1
";

$data = mysqli_fetch_assoc(mysqli_query($koneksi, $sql));
if (!$data) { header("Location: index.php"); exit; }

$status   = strtolower($data['status_pembayaran']);
$nama_bln = ['','Januari','Februari','Maret','April','Mei','Juni',
             'Juli','Agustus','September','Oktober','November','Desember'];

$pesan    = $_GET['pesan']   ?? '';
$tipe_msg = $_GET['tipe']    ?? '';

$query_notif_pemasangan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_pemasangan
    WHERE status_pemasangan = 'menunggu'
");
$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'];

$query_notif_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi <?= htmlspecialchars($data['kode_invoice']) ?></title>
    <link rel="icon"        type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet"  href="../../assets/css/style.css">
    <link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
        .notif-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 50%;
            background: #ef4444; color: #fff;
            font-size: 11px; font-weight: 800;
            margin-left: auto; flex-shrink: 0;
            animation: pulse-badge 1.8s ease-in-out infinite;
        }

@keyframes pulseNotif {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
        .det-card {
            background: #fff; border-radius: 16px;
            border: 1px solid #e4e4e7; margin-bottom: 20px;
            overflow: hidden;
        }
        .det-card-head {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 22px; border-bottom: 1px solid #f4f4f5;
            background: #fafafa;
        }
        .det-card-head .head-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
        }
        .det-card-head h4 {
            font-size: 13px; font-weight: 800; letter-spacing: .6px;
            text-transform: uppercase; color: #52525b; margin: 0;
        }
        .det-card-body { padding: 22px; }

        .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field-grid.cols-1 { grid-template-columns: 1fr; }
        .field-item label {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            color: #a1a1aa; margin-bottom: 6px;
        }
        .field-value {
            background: #f9f9f9; border: 1px solid #e4e4e7;
            border-radius: 8px; padding: 11px 14px;
            font-size: 14px; color: #18181b; font-weight: 500;
            min-height: 42px; display: flex; align-items: center;
        }
        .field-value.highlight {
            background: #fff4ee; border-color: rgba(244,96,12,.25);
            color: #f4600c; font-size: 18px; font-weight: 800;
        }

        .status-pill {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: 20px;
            font-size: 13px; font-weight: 700;
        }
        .sp-lunas    { background: #f0fdf4; color: #16a34a; border: 1.5px solid #bbf7d0; }
        .sp-menunggu { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }
        .sp-belum    { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; }
        .sp-expired {
            background-color: #fee2e2; 
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .sp-dicabut {
            background-color: #f1f5f9; 
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .sp-reaktivasi { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .sp-upgrade    { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .sp-perpanjang { background: #e8f0fe; color: #1a73e8; border: 1px solid #d2e3fc; }
        .sp-baru       { background: #e6f4ea; color: #137333; border: 1px solid #ceead6; }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
        }

        .bukti-wrap {
            background: #f9f9f9; border: 2px dashed #e4e4e7;
            border-radius: 12px; padding: 24px; text-align: center;
        }
        .bukti-wrap img {
            max-width: 100%; max-height: 420px;
            border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.1);
            cursor: zoom-in; transition: transform .2s;
        }
        .bukti-wrap img:hover { transform: scale(1.01); }
        .bukti-hint { font-size: 12px; color: #a1a1aa; margin-top: 10px; }
        .no-bukti {
            padding: 40px 20px; color: #a1a1aa;
        }
        .no-bukti i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .4; }

        .action-bar {
            background: #fff; border: 1px solid #e4e4e7;
            border-radius: 16px; padding: 20px 24px;
            display: flex; align-items: center;
            gap: 12px; flex-wrap: wrap; margin-bottom: 20px;
        }
        .action-bar .action-info { flex: 1; min-width: 180px; }
        .action-bar .action-info h4 { font-size: 15px; font-weight: 800; color: #18181b; margin: 0 0 3px; }
        .action-bar .action-info p  { font-size: 13px; color: #71717a; margin: 0; }

        .btn-terima {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 26px; border-radius: 10px; border: none;
            background: #22c55e; color: #fff;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(34,197,94,.35);
        }
        .btn-terima:hover { background: #16a34a; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(34,197,94,.4); }

        .btn-tolak {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 26px; border-radius: 10px; border: none;
            background: #fff; color: #dc2626;
            border: 2px solid #fecaca;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all .2s;
        }
        .btn-tolak:hover { background: #ef4444; color: #fff; border-color: #ef4444; transform: translateY(-1px); }

        .msg-box {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
            font-size: 14px; font-weight: 600;
        }
        .msg-success { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #16a34a; }
        .msg-danger  { background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; }

        .inv-header {
            background: linear-gradient(135deg, #f4600c, #ff8a3d);
            border-radius: 16px; padding: 24px 28px;
            display: flex; justify-content: space-between; align-items: center;
            color: #fff; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;
        }
        .inv-header h2 { font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: -.3px; }
        .inv-header p  { font-size: 13px; opacity: .85; margin: 0; }
        .inv-header .inv-number {
            background: rgba(255,255,255,.2); border-radius: 10px;
            padding: 10px 18px; font-size: 15px; font-weight: 700;
            font-family: 'Courier New', monospace; letter-spacing: .5px;
            backdrop-filter: blur(8px);
        }

        .sidebar-toggle {
    background: #fff;
    border: 1.5px solid #e4e4e7;
    border-radius: 8px;
    padding: 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 4px;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.sidebar-toggle:hover {
    background: #f4f4f5;
}

.sidebar-toggle span {
    display: block;
    width: 18px;
    height: 2px;
    background-color: #18181b;
    border-radius: 1px;
    transition: all 0.3s;
}

@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}

@media (min-width: 992px) {
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
        font-size: 0 !important;
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
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="../pemasangan/index.php">
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
                <a href="../transaksi/index.php" class="active">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif_transaksi > 0): ?>
                        <span class="notif-badge"><?= $total_notif_transaksi; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
            <li><a href="../admin_user/list_admin.php"><i class="bi bi-person-gear"></i> Kelola Admin</a></li>
            <li><a href="../teknisi_user/index.php"><i class="bi bi-person-plus"></i> <span>Kelola Teknisi</span></a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar" style="display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 15px; margin-bottom: 24px;">
            
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <a href="index.php" style="display:inline-flex;align-items:center;gap:6px;
                    padding:9px 16px;border-radius:8px;border:1.5px solid #e4e4e7;
                    background:#fff;color:#52525b;font-size:13px;font-weight:600;text-decoration:none;
                    transition:all .2s;" onmouseover="this.style.background='#f4f4f5'"
                    onmouseout="this.style.background='#fff'">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
                <div>
                    <h1 style="font-size:22px;font-weight:800;margin:0;color:#18181b;">Detail Transaksi</h1>
                    <p style="color:#a1a1aa;font-size:13px;margin:0;">Invoice <?= htmlspecialchars($data['kode_invoice']) ?></p>
                </div>
            </div>

        </div>
        <?php if ($pesan): ?>
        <div class="msg-box <?= $tipe_msg === 'sukses' ? 'msg-success' : 'msg-danger' ?>">
            <i class="bi bi-<?= $tipe_msg === 'sukses' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
            <?= htmlspecialchars($pesan) ?>
        </div>
        <?php endif; ?>

        <div class="inv-header">
            <div>
                <h2><i class="bi bi-receipt me-2"></i>Rincian Invoice</h2>
                <p>Nomor Invoice: <strong><?= htmlspecialchars($data['kode_invoice']) ?></strong></p>
            </div>
            <div class="inv-number"><?= htmlspecialchars($data['kode_invoice']) ?></div>
        </div>

        <?php if ($status === 'menunggu_verifikasi'): ?>
        <div class="action-bar" style="border-color:#fde68a;background:linear-gradient(135deg,#fffdf7,#fff);">
            <div style="width:44px;height:44px;border-radius:12px;background:#f59e0b;color:#fff;
                        display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="action-info">
                <h4>Menunggu Verifikasi Admin</h4>
                <p>Periksa foto bukti pembayaran di bawah sebelum memverifikasi.</p>
            </div>

            <button class="btn-terima" onclick="bukaModalTerima()">
                <i class="bi bi-shield-check"></i> Terima Pembayaran
            </button>
  
            <button class="btn-tolak" onclick="bukaModalTolak()">
                <i class="bi bi-x-circle"></i> Tolak Pembayaran
            </button>
        </div>
        <?php endif; ?>

        <div class="det-card">
            <div class="det-card-head">
                <div class="head-icon" style="background:#eff6ff;color:#3b82f6;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h4>Data Pelanggan</h4>
            </div>
            <div class="det-card-body">
                <div class="field-grid">
                    <div class="field-item">
                        <label>Nama Pelanggan</label>
                        <div class="field-value"><?= htmlspecialchars($data['nama_customer']) ?></div>
                    </div>
                    <div class="field-item">
                        <label>Nomor Telepon / WhatsApp</label>
                        <div class="field-value">
                            <?= $data['telepon_customer'] ? htmlspecialchars($data['telepon_customer']) : '—' ?>
                        </div>
                    </div>
                    <div class="field-item">
                        <label>Alamat Email</label>
                        <div class="field-value">
                            <?= $data['email_customer'] ? htmlspecialchars($data['email_customer']) : '—' ?>
                        </div>
                    </div>
                    <div class="field-item">
                        <label>Alamat Pemasangan</label>
                        <div class="field-value">
                            <?= $data['alamat_customer'] ? htmlspecialchars($data['alamat_customer']) : '—' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <div class="det-card">
    <div class="det-card-head">
        <div class="head-icon" style="background:#fff4ee;color:#f4600c;">
            <i class="bi bi-wifi"></i>
        </div>
        <h4>Paket & Status Tagihan</h4>
    </div>
    <div class="det-card-body">
        <div class="field-grid">
            <div class="field-item">
                <label>Jenis Transaksi</label>
                <div class="field-value">
                    <?php 
                    $jenis = $data['jenis_transaksi'] ?? 'perpanjang'; 
                    $badge = ['reaktivasi' => ['l' => 'Re-Aktivasi', 'c' => 'sp-reaktivasi', 'i' => 'bi-telephone-plus-fill'], 'upgrade' => ['l' => 'Upgrade Paket', 'c' => 'sp-upgrade', 'i' => 'bi-arrow-up-circle-fill'], 'baru' => ['l' => 'Pasang Baru', 'c' => 'sp-baru', 'i' => 'bi-person-plus-fill'], 'perpanjang' => ['l' => 'Perpanjang', 'c' => 'sp-perpanjang', 'i' => 'bi-arrow-repeat']];
                    $b = $badge[$jenis] ?? $badge['perpanjang'];
                    ?>
                    <span class="status-pill <?= $b['c'] ?>"><i class="bi <?= $b['i'] ?>"></i> <?= $b['l'] ?></span>
                </div>
            </div>
            <div class="field-item">
                <label>Produk / Paket Internet</label>
                <div class="field-value">
                    <?= htmlspecialchars($data['nama_paket']) ?>
                    <span style="font-size:12px;color:#a1a1aa;margin-left:6px;">
                        (<?= htmlspecialchars($data['kecepatan']) ?>)
                    </span>
                </div>
            </div>
            <div class="field-item">
                <label>Periode Tagihan</label>
                <div class="field-value"><?= $nama_bln[(int)$data['bulan_tagihan']] ?> <?= $data['tahun_tagihan'] ?></div>
            </div>
            <div class="field-item">
                <label>Total Nominal Tagihan</label>
                <div class="field-value highlight">Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?></div>
            </div>
            
            <?php 
            $status_raw = $data['status'] ?? $data['status_pembayaran'] ?? $data['status_invoice'] ?? 'belum_bayar';
            $status_db = strtolower(trim($status_raw)); 
            $jatuh_tempo = $data['tanggal_jatuh_tempo'] ?? $data['tgl_jatuh_tempo'] ?? $data['jatuh_tempo'] ?? '';
            $hari_ini = date('Y-m-d');

            if ($status_db !== 'lunas' && $status_db !== 'menunggu_verifikasi' && !empty($jatuh_tempo) && $jatuh_tempo < $hari_ini) {
                $status = 'expired';
                $metode_pembayaran = '-';
            } else {
                $status = $status_db;
                $metode_raw = $data['metode_pembayaran'] ?? $data['metode'] ?? '';
                $metode_pembayaran = !empty($metode_raw) ? htmlspecialchars(ucfirst($metode_raw)) : '-';
            }

            switch ($status) {
                case 'lunas':
                    $status_class = 'sp-lunas';
                    $status_icon  = 'bi-check-circle-fill';
                    $status_label = 'Lunas';
                    break;
                case 'menunggu_verifikasi':
                case 'menunggu':
                    $status_class = 'sp-menunggu';
                    $status_icon  = 'bi-hourglass-split';
                    $status_label = 'Menunggu Verifikasi';
                    break;
                case 'expired':
                    $status_class = 'sp-expired';
                    $status_icon  = 'bi-x-circle-fill';
                    $status_label = 'Expired';
                    break;
                case 'belum_bayar':
                case 'belum':
                default:
                    $status_class = 'sp-belum';
                    $status_icon  = 'bi-clock-fill';
                    $status_label = 'Belum Bayar';
                    break;
            }
            ?>

            <div class="field-item">
                <label>Status Pembayaran</label>
                <div class="field-value">
                    <span class="status-pill <?= $status_class ?>">
                        <i class="bi <?= $status_icon ?>"></i> <?= $status_label ?>
                    </span>
                </div>
            </div>

            <div class="field-item">
                <label>Metode Pembayaran</label>
                <div class="field-value">
                    <?= $metode_pembayaran ?>
                </div>
            </div>
                    </div>
                </div>
            </div>

<div class="det-card">
    <div class="det-card-head">
        <div class="head-icon" style="background:#fef9c3;color:#ca8a04;">
            <i class="bi bi-image"></i>
        </div>
        <h4>Lampiran Bukti Pembayaran</h4>
    </div>
    <div class="det-card-body">
        <?php
        $bukti_path = $data['bukti_pembayaran'] ?? '';
        $bukti_url  = '../../assets/uploads/bukti/' . $bukti_path;
        $has_bukti  = !empty($bukti_path) && file_exists(__DIR__ . '/../../assets/uploads/bukti/' . $bukti_path);
        ?>
        
        <?php if ($has_bukti): ?>
            <div class="bukti-wrap" style="text-align: center;">
                <a href="<?= $bukti_url ?>" target="_blank">
                    <img src="<?= $bukti_url ?>" alt="Bukti" style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #ddd;">
                </a>
            </div>
        <?php else: ?>
            <div class="no-bukti" style="padding: 20px; text-align: center; color: #666;">
                <i class="bi bi-image-fill" style="font-size: 2rem;"></i>
                <p>Belum ada bukti pembayaran dikirim</p>
            </div>
        <?php endif; ?>
    </div>
</div>

    </div>
</div>

<div class="logout-modal" id="modalTerima">
    <div class="logout-modal-content">
        <div class="logout-icon" style="background:#f0fdf4;color:#22c55e;">
            <i class="bi bi-shield-check" style="font-size:30px;"></i>
        </div>
        <h2 style="color:#16a34a;">Terima Pembayaran?</h2>
        <p>
            Status transaksi <strong><?= htmlspecialchars($data['kode_invoice']) ?></strong>
            akan diubah menjadi <strong>Lunas</strong>.<br>
            Pelanggan akan mendapat notifikasi pembayaran diterima.
        </p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="tutupModal('modalTerima')">Batal</button>
            <form action="aksi_verifikasi.php" method="POST" style="flex:1;">
                <input type="hidden" name="id_transaksi" value="<?= $data['id_transaksi'] ?>">
                <input type="hidden" name="aksi" value="terima">
                <button type="submit" style="
                    width:100%;padding:13px;border-radius:10px;border:none;
                    background:#22c55e;color:#fff;font-size:14px;font-weight:700;
                    cursor:pointer;box-shadow:0 4px 14px rgba(34,197,94,.35);">
                    <i class="bi bi-check-lg"></i> Ya, Terima
                </button>
            </form>
        </div>
    </div>
</div>

<div class="logout-modal" id="modalTolak">
    <div class="logout-modal-content">
        <div class="logout-icon" style="background:#fef2f2;color:#ef4444;">
            <i class="bi bi-x-circle" style="font-size:30px;"></i>
        </div>
        <h2 style="color:#dc2626;">Tolak Pembayaran?</h2>
        <p>
            Bukti pembayaran <strong><?= htmlspecialchars($data['kode_invoice']) ?></strong>
            dianggap tidak valid.<br>
            Status akan kembali ke <strong>Belum Bayar</strong> dan
            pelanggan harus mengirim ulang bukti.
        </p>
        <div class="mb-3" style="margin-top:14px;text-align:left;">
            <label style="font-size:13px;font-weight:700;color:#52525b;display:block;margin-bottom:6px;">
                Alasan penolakan (opsional):
            </label>
            <textarea id="alasanTolak" rows="3" style="
                width:100%;padding:10px 14px;border-radius:8px;
                border:1.5px solid #e4e4e7;font-size:13px;resize:none;outline:none;
                font-family:inherit;" placeholder="Contoh: Foto buram, nominal tidak sesuai..."></textarea>
        </div>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="tutupModal('modalTolak')">Batal</button>
            <form action="aksi_verifikasi.php" method="POST" style="flex:1;" id="formTolak">
                <input type="hidden" name="id_transaksi" value="<?= $data['id_transaksi'] ?>">
                <input type="hidden" name="aksi" value="tolak">
                <input type="hidden" name="alasan" id="inputAlasan">
                <button type="submit" style="
                    width:100%;padding:13px;border-radius:10px;border:none;
                    background:#ef4444;color:#fff;font-size:14px;font-weight:700;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Ya, Tolak
                </button>
            </form>
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
function bukaModalTerima() {
    document.getElementById('modalTerima').classList.add('show');
}
function bukaModalTolak() {
    document.getElementById('modalTolak').classList.add('show');
}
function tutupModal(id) {
    document.getElementById(id).classList.remove('show');
}

document.getElementById('formTolak')?.addEventListener('submit', function() {
    document.getElementById('inputAlasan').value =
        document.getElementById('alasanTolak').value;
});

document.querySelectorAll('.logout-modal').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const layout = document.querySelector('.dashboard-layout');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
      
            sidebar.classList.toggle('active');
            sidebar.classList.toggle('collapsed');

            if (layout) {
                layout.classList.toggle('sidebar-toggled');
                layout.classList.toggle('collapsed');
            }
        
            document.body.classList.toggle('sidebar-toggled');
            
            console.log('Hamburger berhasil diklik! Class sidebar saat ini:', sidebar.className);
        });

        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
                if (layout) layout.classList.remove('sidebar-toggled');
                document.body.classList.remove('sidebar-toggled');
            }
        });
    } else {
        console.error('PENTING: Tombol hamburger (#sidebarToggle) atau elemen (.sidebar) tidak ditemukan di halaman ini!');
    }
});
</script>
</body>
</html>