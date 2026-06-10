<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

$id_user = $_SESSION['id_user'];

// Kolom jatuh tempo disesuaikan dengan database kamu: tanggal_selesai
$sql = "SELECT c.*, l.id_langganan, l.status_langganan, 
        l.tanggal_mulai, l.tanggal_selesai, 
        p.nama_paket, p.harga, p.kecepatan 
        FROM tb_customer c 
        LEFT JOIN tb_langganan l ON c.id_customer = l.id_customer 
        LEFT JOIN tb_paket p ON l.id_paket = p.id_paket 
        WHERE c.id_user = '$id_user' LIMIT 1";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

$show_error = (!$data);
$t = null;
$nominal = 0;
$riwayat = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE 1=0");

if ($data && !empty($data['id_langganan'])) {
    // FIX: Ambil transaksi paling terakhir agar tombol bayar tidak hilang saat lewat bulan
    $sql_t = "SELECT * FROM tb_transaksi 
              WHERE id_langganan = '" . $data['id_langganan'] . "' 
              ORDER BY id_transaksi DESC LIMIT 1";
    
    $tagihan = mysqli_query($koneksi, $sql_t);
    $t = mysqli_fetch_assoc($tagihan);
    $nominal = ($t && $t['status_pembayaran'] != 'lunas') 
               ? $t['jumlah_bayar'] : 0;

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
    <link class="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../assets/js/script.js" defer></script>

    <style>
html, body {
    height: 100%;
    margin: 0;
}

.dashboard-layout {
    display: flex;
    width: 100%;
    min-height: 100vh;
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
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: #f0f0f0;
    border: none;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
}

.sidebar-toggle span {
    width: 20px;
    height: 2.5px;
    background: #333;
    border-radius: 2px;
}

@media (min-width: 992px) {
    .sidebar.collapsed {
        width: 70px !important;
        min-width: 70px !important;
        max-width: 70px !important;
        padding: 24px 8px !important;
    }

    .sidebar.collapsed ul li a span,
    .sidebar.collapsed .sidebar-logo h2,
    .sidebar.collapsed .notif-badge {
        display: none;
    }

    .sidebar.collapsed ul li a {
        justify-content: center;
    }

    .sidebar.collapsed ul li a i {
         margin: 0 !important;
        font-size: 20px !important;
    }

    .dashboard-content {
        flex: 1;
        transition: all 0.3s ease;
    }
}

@media (max-width: 991px) {
    .dashboard-layout {
        flex-direction: column;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        z-index: 9999;
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .dashboard-content {
        width: 100%;
        padding: 20px;
    }
}

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
}

.customer-hero-card {
    display: flex !important;
    justify-content: space-between;
    align-items: stretch; 
    flex-wrap: wrap;
    gap: 20px;
}

.hero-left {
    flex: 1;
    min-width: 250px;
}

.hero-right {
    display: flex !important;
    flex-direction: column;
    justify-content: space-between; 
    align-items: flex-end; 
    text-align: right;
    min-width: 150px;
}

@media (max-width: 576px) {
    .customer-hero-card {
        flex-direction: column;
        align-items: stretch;
    }
    .hero-right {
        align-items: flex-end; 
        min-height: 120px;
    }
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
            <img src="../assets/images/logo.png">
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
            <button class="sidebar-toggle" id="sidebarToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Halo, <?= $data['nama_customer']; ?></h1>
                <p>Selamat datang kembali</p>
            </div>
        </div>
        
        <div class="customer-hero-card">
            <div class="hero-left">
                <?php if (strtolower($data['status_langganan'] ?? '') == 'suspend') : ?>
                    <span class="hero-label" style="background-color: #ef4444; color: white;">Paket Suspend </span>
                <?php else : ?>
                    <span class="hero-label" style="background-color: #b29786; color: white;">Paket Aktif</span>
                <?php endif; ?>

                <h1 class="hero-package">
                    <?= $data['nama_paket'] ?? 'Belum Berlangganan'; ?>
                </h1>
                <h2 class="hero-speed">
                    <?= $data['kecepatan'] ?? '-'; ?>
                </h2>

                <div class="hero-info-wrapper">
                    <div class="hero-info-box">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <span>Jatuh Tempo</span>
                            <h3><?= tgl_indo($data['tanggal_selesai']); ?></h3>
                        </div>
                    </div>

                    <div class="hero-info-box">
                        <i class="bi bi-wallet2"></i>
                        <div>
                            <span>Tagihan Bulan Ini</span>
                            <h3>
                                <?php if($nominal > 0) : ?>
                                    Rp<?= number_format($nominal, 0, ',', '.'); ?>
                                <?php else : ?>
                                    Tidak Ada
                                <?php endif; ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <i class="bi bi-wifi hero-wifi-icon"></i>
                <?php $status = strtolower(trim($t['status_pembayaran'] ?? '')); ?>
                
                <?php if($status == 'belum_bayar') : ?>
                    <a href="tagihan/bayar.php?id=<?= $t['id_transaksi']; ?>" class="hero-button">Bayar Tagihan</a>
                <?php elseif($status == 'menunggu_verifikasi') : ?>
                    <button class="hero-button waiting-btn" disabled>Menunggu Verifikasi</button>
                <?php else : ?>
                    <button class="hero-button disabled-btn" disabled>Belum Ada Tagihan</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Riwayat Tagihan</h3>
                <a href="tagihan/index.php" class="btn-orange-outline">Lihat Semua</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th><th>Periode</th><th>Jumlah</th>
                        <th>Status</th><th>Tanggal Bayar</th><th>Aksi</th>
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
                        <td>
                            <?php if ($s_pay == 'belum') : ?>
                            <a href="tagihan/bayar.php?id=<?= $r['id_transaksi']; ?>" class="btn-bayar">Bayar</a>
                            <?php else : ?>
                                <a href="tagihan/detail.php?id=<?= $r['id_transaksi']; ?>" class="btn-detail">Detail</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr><td colspan="6" style="text-align:center;">Belum ada tagihan</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
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
</script>
</body>
</html>