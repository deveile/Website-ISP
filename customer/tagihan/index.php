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

$batas = 10; 
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$periode = $_GET['periode'] ?? '';
$status  = $_GET['status'] ?? '';
$where   = "WHERE 1=0";

if ($langganan) {
    $where = "WHERE id_langganan = '" . $langganan['id_langganan'] . "'";
    
    if ($periode != '') {
        $split = explode('-', $periode);
        $tahun = (int)$split[0];
        $bulan = (int)$split[1];
        $where .= " AND bulan_tagihan = '$bulan' AND tahun_tagihan = '$tahun'";
    }
    if ($status != '') {
        $status_clean = mysqli_real_escape_string($koneksi, $status);
        $where .= " AND status_pembayaran = '$status_clean'";
    }
}

$sql_total = "SELECT COUNT(*) AS total FROM tb_transaksi $where";
$query_total = mysqli_query($koneksi, $sql_total);
$total_data = mysqli_fetch_assoc($query_total)['total'] ?? 0;
$total_halaman = ceil($total_data / $batas);

$sql_main = "SELECT * FROM tb_transaksi $where ORDER BY id_transaksi DESC LIMIT $halaman_awal, $batas";
$query = mysqli_query($koneksi, $sql_main);
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

    .badge-lunas { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .badge-menunggu { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .badge-belum { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }

    .invoice-code {
        font-family: 'Courier New', monospace;
        font-size: 13px; font-weight: 700;
        color: #4f46e5; letter-spacing: .3px;
    }

    .btn-bayar-cust {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 700;
        background: #ff6600; color: #ffffff !important;
        border: none; text-decoration: none; transition: all .2s; cursor: pointer;
        box-shadow: 0 2px 8px rgba(255,102,0,.2);
    }
    .btn-bayar-cust:hover { background: #e05500; transform: translateY(-1px); }

    .btn-detail-cust {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;
        background: #0091ff; color: #ffffff !important;
        border: none; text-decoration: none; transition: all .2s; cursor: pointer;
    }
    .btn-detail-cust:hover { background: #0077dd; transform: translateY(-1px); }

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
            <img src="../../assets/images/logo.png" alt="Logo">
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
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Tagihan Saya</h1>
                <p>Riwayat pembayaran internet</p>
            </div>
        </div>
        
        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Tagihan</h3>
                <form method="GET" class="filter-form">
                    <input type="text" id="periode" name="periode" value="<?= htmlspecialchars($periode); ?>" placeholder="Pilih Periode">
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" <?= ($status == 'belum_bayar') ? 'selected' : ''; ?>>Belum Bayar</option>
                        <option value="menunggu_verifikasi" <?= ($status == 'menunggu_verifikasi') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                        <option value="lunas" <?= ($status == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                    </select>
                    <button type="submit" class="btn-orange">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    
                    <?php if (!empty($status) || !empty($periode)): ?>
                    <a href="index.php" style="padding:9px 14px;border-radius:8px;border:1.5px solid #e4e4e7;
                        color:#52525b;font-size:13px;font-weight:600;text-decoration:none;
                        background:#fafafa;display:inline-flex;align-items:center;gap:5px;">
                        <i class="bi bi-x"></i> Reset
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div style="overflow-x:auto; width: 100%; border-radius: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Periode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $no = $halaman_awal + 1;
                    if ($query && mysqli_num_rows($query) > 0) : 
                        while ($t = mysqli_fetch_assoc($query)) : 
                            $status_pay = strtolower($t['status_pembayaran']);
                            $periode_tagihan = date('M Y', mktime(0, 0, 0, $t['bulan_tagihan'], 1, $t['tahun_tagihan']));
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="invoice-code"><?= htmlspecialchars($t['kode_invoice']); ?></span></td>
                            <td><?= $periode_tagihan; ?></td>
                            <td style="font-weight:600;">Rp <?= number_format($t['jumlah_bayar'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($status_pay == 'lunas') : ?>
                                    <span class="badge-lunas"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                                <?php elseif ($status_pay == 'menunggu_verifikasi') : ?>
                                    <span class="badge-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu Verifikasi</span>
                                <?php else : ?>
                                    <span class="badge-belum"><i class="bi bi-x-circle-fill"></i> Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:#a1a1aa;font-size:13px;">
                                <?= !empty($t['tanggal_bayar']) ? date('d/m/Y', strtotime($t['tanggal_bayar'])) : '—'; ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($status_pay == 'belum_bayar') : ?>
                                    <a href="bayar.php?id=<?= $t['id_transaksi']; ?>" class="btn-bayar-cust">Bayar</a>
                                <?php else : ?>
                                    <a href="detail.php?id=<?= $t['id_transaksi']; ?>" class="btn-detail-cust">Detail</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" style="text-align:center; color:#a1a1aa;">Belum ada tagihan</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_data > 0) : ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Menampilkan data ke-<?= ($halaman_awal + 1) ?> sampai <?= min($halaman_awal + $batas, $total_data) ?> dari total <?= $total_data ?> data
                </div>
                <ul class="pagination-list">
                    <?php if ($halaman > 1) : ?>
                        <li><a href="?halaman=<?= $halaman - 1 ?>&periode=<?= $periode ?>&status=<?= $status ?>"><i class="bi bi-chevron-left"></i></a></li>
                    <?php else : ?>
                        <li class="disabled-page"><a><i class="bi bi-chevron-left"></i></a></li>
                    <?php endif; ?>

                    <?php for ($x = 1; $x <= $total_halaman; $x++) : ?>
                        <li class="<?= ($x == $halaman) ? 'active-page' : '' ?>">
                            <a href="?halaman=<?= $x ?>&periode=<?= $periode ?>&status=<?= $status ?>"><?= $x ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($halaman < $total_halaman) : ?>
                        <li><a href="?halaman=<?= $halaman + 1 ?>&periode=<?= $periode ?>&status=<?= $status ?>"><i class="bi bi-chevron-right"></i></a></li>
                    <?php else : ?>
                        <li class="disabled-page"><a><i class="bi bi-chevron-right"></i></a></li>
                    <?php endif; ?>
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