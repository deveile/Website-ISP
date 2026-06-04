<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

$query_customer = mysqli_query($koneksi, "SELECT * FROM tb_customer WHERE id_user = '$id_user' LIMIT 1");
$customer = mysqli_fetch_assoc($query_customer);

$langganan = null;
if ($customer) {
    $sql_langganan = "SELECT tb_langganan.*, tb_paket.nama_paket, tb_paket.kecepatan, tb_paket.harga 
                      FROM tb_langganan 
                      LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket 
                      WHERE tb_langganan.id_customer = '" . $customer['id_customer'] . "' LIMIT 1";
    
    $query_langganan = mysqli_query($koneksi, $sql_langganan);
    $langganan = mysqli_fetch_assoc($query_langganan);
}

$periode = $_GET['periode'] ?? '';
$status  = $_GET['status'] ?? '';
$where   = "WHERE 1=0";

if ($langganan) {
    $where = "WHERE id_langganan = '" . $langganan['id_langganan'] . "'";
    
    if ($periode != '') {
        $split = explode('-', $periode);
        $where .= " AND bulan_tagihan = '" . $split[1] . "' AND tahun_tagihan = '" . $split[0] . "'";
    }
    if ($status != '') {
        $where .= " AND status_pembayaran = '$status'";
    }
}

$query = mysqli_query($koneksi, "SELECT * FROM tb_transaksi $where ORDER BY id_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Saya</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="../../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<style>
    /* =========================
   BASE LAYOUT FIX
========================= */
html, body {
    height: 100%;
    margin: 0;
}

.dashboard-layout {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

/* =========================
   SIDEBAR 
========================= */
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





/* =========================
   TOGGLE BUTTON
========================= */
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

/* =========================
   DESKTOP COLLAPSE
========================= */
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

/* =========================
   MOBILE SIDEBAR
========================= */
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

/* =========================
   NOTIF BADGE
========================= */
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
</style>
</head>
<body>
<div class="dashboard-layout">
    <div class="sidebar">
    <div class="sidebar-logo">
        <img src="../../assets/images/logo.png">
        <h2>Anuwani</h2>
    </div>
           <ul class="sidebar-menu">
    <li>
        <a href="../index.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li>
        <a href="index.php" class="active">
            <i class="bi bi-receipt"></i>
            <span>Tagihan Saya</span>
        </a>
    </li>

    <li>
        <a href="../paket/index.php">
            <i class="bi bi-wifi"></i>
            <span>Paket Internet</span>
        </a>
    </li>

    <li>
        <a href="../profile/index.php">
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
            <div>
                <h1>Tagihan Saya</h1>
                <p>Riwayat pembayaran internet</p>
            </div>
        </div>
        
        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Tagihan</h3>
                <form method="GET" class="filter-form">
                    <input type="text" id="periode" name="periode" value="<?= $periode; ?>" placeholder="Pilih Periode">
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" <?= ($status == 'belum_bayar') ? 'selected' : ''; ?>>Belum Bayar</option>
                        <option value="menunggu_verifikasi" <?= ($status == 'menunggu_verifikasi') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                        <option value="lunas" <?= ($status == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                    </select>
                    <button type="submit" class="btn-orange">Filter</button>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($query && mysqli_num_rows($query) > 0) : ?>
                    <?php while ($t = mysqli_fetch_assoc($query)) : 
                        $status_pay = strtolower($t['status_pembayaran']);
                        $periode_tagihan = date('F Y', strtotime($t['tahun_tagihan'].'-'.$t['bulan_tagihan'].'-01'));
                        
                        if ($status_pay == 'lunas') {
                            $status_class = 'active';
                            $status_text  = 'Lunas';
                        } elseif ($status_pay == 'menunggu_verifikasi') {
                            $status_class = 'pending';
                            $status_text  = 'Menunggu Verifikasi';
                        } else {
                            $status_class = 'belum';
                            $status_text  = 'Belum Bayar';
                        }
                    ?>
                    <tr>
                        <td><?= $t['kode_invoice']; ?></td>
                        <td><?= $periode_tagihan; ?></td>
                        <td>Rp<?= number_format($t['jumlah_bayar']); ?></td>
                        <td><span class="status-<?= $status_class; ?>"><?= $status_text; ?></span></td>
                        <td><?= !empty($t['tanggal_bayar']) ? $t['tanggal_bayar'] : '-'; ?></td>
                        <td>
                            <?php if ($status_pay == 'belum_bayar') : ?>
                                <a href="bayar.php?id=<?= $t['id_transaksi']; ?>" class="btn-bayar">Bayar</a>
                            <?php else : ?>
                                <a href="detail.php?id=<?= $t['id_transaksi']; ?>" class="btn-detail">Detail</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">Belum ada tagihan</td>
                    </tr>
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
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

<script>
flatpickr("#periode", {
    plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: "Y-m", altFormat: "F Y" })]
});
</script>

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