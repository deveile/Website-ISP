<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

$id_user = $_SESSION['id_user'];

$sql = "SELECT c.*, l.id_langganan, l.status_langganan, 
        l.tanggal_mulai, l.tanggal_selesai, 
        p.nama_paket, p.harga, p.kecepatan 
        FROM tb_customer c 
        LEFT JOIN tb_langganan l ON c.id_customer = l.id_customer 
        LEFT JOIN tb_paket p ON l.id_paket = p.id_paket 
        WHERE c.id_user = '$id_user' 
        ORDER BY l.id_langganan DESC LIMIT 1";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

$show_error = (!$data);
$t = null;
$nominal = 0;
$riwayat = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE 1=0");

if ($data && !empty($data['id_langganan'])) {
    $sql_t = "SELECT * FROM tb_transaksi 
              WHERE id_langganan = '" . $data['id_langganan'] . "' 
              ORDER BY id_transaksi DESC LIMIT 1";
    
    $tagihan = mysqli_query($koneksi, $sql_t);
    $t = mysqli_fetch_assoc($tagihan);
    $nominal = ($t && $t['status_pembayaran'] != 'lunas') ? $t['jumlah_bayar'] : 0;

    $sql_r = "SELECT * FROM tb_transaksi 
              WHERE id_langganan = '" . $data['id_langganan'] . "' 
              ORDER BY id_transaksi DESC LIMIT 3";
              
    $riwayat = mysqli_query($koneksi, $sql_r);
}

function tgl_indo($tgl) {
    if (empty($tgl) || $tgl == '0000-00-00') return '-';
    $b = [1=>'Januari','Februari','Maret','April','Mei','Juni',
          'Juli','Agustus','September','Oktober','November','Desember'];
    $s = explode('-', $tgl);
    return $s[2] . ' ' . $b[(int)$s[1]] . ' ' . $s[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../assets/js/script.js" defer></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .dashboard-layout {
            display: flex !important;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            transition: all 0.3s ease;
            background: #fff;
            overflow: hidden;
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

        .customer-hero-card {
            position: relative !important; 
            display: flex !important;
            justify-content: space-between;
            align-items: stretch; 
            flex-wrap: wrap;
            gap: 24px;
            padding: 24px !important;
            background: linear-gradient(135deg, #ff6600, #ff8c42);
            border-radius: 20px;
            color: white;
            overflow: hidden;
        }

        .hero-wifi-icon {
            position: absolute !important;
            top: 24px !important;
            right: 24px !important;
            font-size: 38px !important;
            opacity: 0.25 !important;
            line-height: 1 !important;
            z-index: 1;
        }

        .hero-left {
            flex: 1;
            min-width: 280px;
            z-index: 2;
        }

        .hero-package {
            font-size: 32px !important;
            margin: 12px 0 4px 0 !important;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero-info-wrapper {
            display: flex !important;
            gap: 16px !important;
            margin-top: 20px !important;
            flex-wrap: wrap !important;
        }

        .hero-info-box {
            flex: 1;
            min-width: 140px;
            background: rgba(255, 255, 255, 0.15);
            padding: 14px !important;
            border-radius: 14px;
            backdrop-filter: blur(4px);
        }

        .hero-right {
            display: flex !important;
            flex-direction: column;
            justify-content: flex-end; 
            align-items: flex-end; 
            text-align: right;
            min-width: 200px;
            z-index: 2;
        }

        .hero-button, .disabled-btn, .waiting-btn, .btn-outline-danger {
            width: 100%;
            max-width: 240px;
            text-align: center;
        }

        .btn-outline-danger {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            margin-top: 12px;
        }
        
        .btn-outline-danger:not(:disabled):hover {
            background: #ef4444;
            border-color: #ef4444;
        }

        .btn-outline-danger:disabled {
            opacity: 0.35 !important;
            cursor: not-allowed !important;
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            pointer-events: none !important;
        }

        .table-card {
            background: #ffffff !important;
            border-radius: 16px !important;
            padding: 24px !important; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02) !important;
            margin-top: 25px;
        }

        .table-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 20px !important;
            flex-wrap: wrap !important;
            gap: 15px !important;
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
            padding: 14px 12px !important;
        }
        
        table td {
            padding: 12px 14px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            border-right: 1px solid #e4e4e7 !important;
        }
        table td:last-child { border-right: none !important; }
        table tr:last-child td { border-bottom: none !important; }

        .logout-modal, .berhenti-modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 16px;
        }
        .logout-modal.show, .berhenti-modal.show { opacity: 1; pointer-events: auto; }
        
        .logout-modal-content {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .logout-icon {
            width: 56px; height: 56px;
            background: #ffedd5; color: #ff6600;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin: 0 auto 16px auto;
        }
        .logout-modal-content h2 { margin: 0 0 8px 0; font-size: 20px; color: #1e293b; }
        .logout-modal-content p { margin: 0 0 24px 0; font-size: 14px; color: #64748b; line-height: 1.5; }
        .logout-modal-action { display: flex; gap: 12px; }
        .btn-cancel, .btn-confirm { flex: 1; padding: 12px; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; text-align: center; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        .btn-confirm { background: #ff6600; color: #fff; }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed !important;
                top: 0 !important; left: 0 !important;
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
                padding: 32px !important;
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
            .sidebar.collapsed .notif-badge,
            .sidebar.collapsed ul li a span {
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

        @media (max-width: 576px) {
            .customer-hero-card {
                flex-direction: column !important;
                padding-top: 50px !important; 
            }
            .hero-left, .hero-right {
                width: 100% !important;
                min-width: 100% !important;
            }
            .hero-right {
                align-items: stretch !important;
                text-align: center !important;
                margin-top: 8px;
            }
            .hero-button, .disabled-btn, .waiting-btn, .btn-outline-danger {
                max-width: 100% !important;
            }
            .hero-info-wrapper { flex-direction: column !important; }
            .hero-package { font-size: 26px !important; }
        }
    </style>
</head>
<body>

<?php if ($show_error) : ?>
<div class="error-overlay">
    <div class="error-modal-box">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <h2>Data Tidak Ditemukan</h2>
        <p>Data customer belum dibuat admin.</p>
        <a href="../auth/logout.php" class="btn-error-return">Kembali</a>
    </div>
</div>
<?php exit; endif; ?>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="tagihan/index.php">
                    <i class="bi bi-receipt"></i>
                    <span>Tagihan Saya</span>
                </a>
            </li>
            <li>
                <a href="paket/index.php">
                    <i class="bi bi-wifi"></i>
                    <span>Paket Internet</span>
                </a>
            </li>
            <li>
                <a href="profile/index.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
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
                <h1 style="margin:0; font-size: 20px; color:#1e293b;">Halo, <?= $data['nama_customer']; ?></h1>
                <p style="margin:0; font-size: 14px; color:#64748b;">Selamat datang kembali</p>
            </div>
        </div>
        
        <div class="customer-hero-card">
            <i class="bi bi-wifi hero-wifi-icon"></i>

            <div class="hero-left">
                <?php 
                $status_l = strtolower(trim($data['status_langganan'] ?? ''));
                if ($status_l == 'suspend') : ?>
                    <span class="hero-label" style="background-color: #ef4444; color: white;">Paket Suspend</span>
                <?php elseif ($status_l == 'berhenti') : ?>
                    <span class="hero-label" style="background-color: #475569; color: white;">Akan Berhenti</span>
                <?php elseif ($status_l == 'menunggu_verifikasi') : ?>
                    <span class="hero-label" style="background-color: #f59e0b; color: white;">Menunggu Verifikasi</span>
                <?php elseif ($status_l == 'aktif') : ?>
                    <span class="hero-label" style="background-color: #10b981; color: white;">Paket Aktif</span>
                <?php else : ?>
                    <span class="hero-label" style="background-color: rgba(255,255,255,0.25); color: white;">Belum Berlangganan</span>
                <?php endif; ?>

                <h1 class="hero-package"><?= $data['nama_paket'] ?? 'Belum Berlangganan'; ?></h1>
                <h2 class="hero-speed" style="opacity: 0.9; font-weight: 500; font-size: 18px; margin: 0;"><?= $data['kecepatan'] ?? '-'; ?></h2>

                <div class="hero-info-wrapper">
                    <div class="hero-info-box">
                        <span style="font-size: 12px; opacity: 0.8; display:block; margin-bottom:4px;">Jatuh Tempo</span>
                        <h3 style="margin:0; font-size:15px; font-weight:700;"><?= tgl_indo($data['tanggal_selesai']); ?></h3>
                    </div>
                    <div class="hero-info-box">
                        <span style="font-size: 12px; opacity: 0.8; display:block; margin-bottom:4px;">Tagihan Bulan Ini</span>
                        <h3 style="margin:0; font-size:15px; font-weight:700;"><?= ($nominal > 0) ? 'Rp '.number_format($nominal, 0, ',', '.') : 'Tidak Ada'; ?></h3>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div style="width: 100%;">
                    <?php $status = strtolower(trim($t['status_pembayaran'] ?? '')); ?>
                    
                    <?php if($status == 'belum_bayar') : ?>
                        <a href="tagihan/bayar.php?id=<?= $t['id_transaksi']; ?>" class="hero-button">Bayar Tagihan</a>
                    <?php elseif($status == 'menunggu_verifikasi') : ?>
                        <button class="hero-button waiting-btn" disabled>Menunggu Verifikasi</button>
                    <?php else : ?>
                        <button class="hero-button disabled-btn" disabled>Belum Ada Tagihan</button>
                    <?php endif; ?>

                    <?php if (empty($data['id_langganan']) || $status_l == 'menunggu_verifikasi' || $status_l == 'berhenti') : ?>
                        <button type="button" class="btn-outline-danger" disabled>Berhenti Langganan</button>
                    <?php elseif ($status_l == 'aktif' || $status_l == 'suspend') : ?>
                        <button type="button" class="btn-outline-danger" onclick="openBerhentiModal()">Berhenti Langganan</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3 style="margin:0; color:#1e293b;">Riwayat Tagihan</h3>
                <a href="tagihan/index.php" class="btn-orange-outline">Lihat Semua</a>
            </div>
            <div style="overflow-x:auto; width: 100%; border-radius: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th><th>Periode</th><th>Jumlah</th>
                            <th>Status</th><th>Tanggal Bayar</th><th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($riwayat) > 0) : ?>
                        <?php while ($r = mysqli_fetch_assoc($riwayat)) : 
                            $s_pay = strtolower($r['status_pembayaran']);
                            $class = ($s_pay == 'lunas') ? 'active' : (($s_pay == 'menunggu_verifikasi') ? 'pending' : 'belum');
                            $text  = ($s_pay == 'lunas') ? 'Lunas' : (($s_pay == 'menunggu_verifikasi') ? 'Menunggu Verifikasi' : 'Belum Bayar');
                            
                            $format_bulan = sprintf('%02d', $r['bulan_tagihan']);
                            $string_tanggal = $r['tahun_tagihan'] . '-' . $format_bulan . '-01';
                            $periode_tgl = date('F Y', strtotime($string_tanggal));
                        ?>
                        <tr>
                            <td><?= $r['kode_invoice'] ?? '-'; ?></td>
                            <td><?= $periode_tgl; ?></td>
                            <td>Rp<?= number_format($r['jumlah_bayar'], 0, ',', '.'); ?></td>
                            <td><span class="status-<?= $class; ?>"><?= $text; ?></span></td>
                            <td><?= !empty($r['tanggal_bayar']) ? tgl_indo($r['tanggal_bayar']) : '-'; ?></td>
                            <td style="text-align:center;">
                                <?php if ($s_pay == 'belum') : ?>
                                <a href="tagihan/bayar.php?id=<?= $r['id_transaksi']; ?>" class="btn-bayar">Bayar</a>
                                <?php else : ?>
                                    <a href="tagihan/detail.php?id=<?= $r['id_transaksi']; ?>" class="btn-detail">Detail</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="6" style="text-align:center; color:#a1a1aa; padding:30px;">Belum ada tagihan</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="berhenti-modal" id="berhentiModal">
    <div class="logout-modal-content">
        <div class="logout-icon" style="background: #fee2e2; color: #ef4444;"><i class="bi bi-exclamation-octagon"></i></div>
        <h2>Berhenti Berlangganan?</h2>
        <p>Layanan Anda tetap dapat digunakan sampai akhir periode masa aktif. Tagihan baru tidak akan diterbitkan kembali.</p>
        <form action="proses_berhentiLangganan.php" method="POST">
            <input type="hidden" name="id_langganan" value="<?= $data['id_langganan']; ?>">
            <div class="logout-modal-action">
                <button type="button" class="btn-cancel" onclick="closeBerhentiModal()">Batal</button>
                <button type="submit" class="btn-confirm" style="background: #ef4444;">Ya, Berhenti</button>
            </div>
        </form>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-box-arrow-right"></i></div>
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
                if (window.innerWidth < 991) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    });

    function openLogoutModal() { document.getElementById('logoutModal').classList.add('show'); }
    function closeLogoutModal() { document.getElementById('logoutModal').classList.remove('show'); }
    
    function openBerhentiModal() { document.getElementById('berhentiModal').classList.add('show'); }
    function closeBerhentiModal() { document.getElementById('berhentiModal').classList.remove('show'); }
</script>
</body>
</html>