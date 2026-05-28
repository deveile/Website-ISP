<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';
require_once __DIR__ . '/generate.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$where = "WHERE 1=1";

if (isset($_GET['periode']) && $_GET['periode'] != '') {
    $explode = explode('-', $_GET['periode']);
    $tahun   = (int)$explode[0];
    $bulan   = (int)$explode[1];
    $where  .= " AND tb_transaksi.bulan_tagihan='$bulan' AND tb_transaksi.tahun_tagihan='$tahun'";
}

if (isset($_GET['status']) && $_GET['status'] != '') {
    $status  = mysqli_real_escape_string($koneksi, $_GET['status']);
    $where  .= " AND tb_transaksi.status_pembayaran='$status'";
}

$sql = "SELECT tb_transaksi.*, tb_customer.nama_customer
        FROM tb_transaksi
        INNER JOIN tb_langganan ON tb_transaksi.id_langganan = tb_langganan.id_langganan
        INNER JOIN tb_customer  ON tb_langganan.id_customer  = tb_customer.id_customer
        $where
        ORDER BY tb_transaksi.id_transaksi DESC";

$query = mysqli_query($koneksi, $sql);

$q_menunggu  = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS total FROM tb_transaksi WHERE status_pembayaran = 'menunggu_verifikasi'"));
$jml_menunggu = (int)$q_menunggu['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
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
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
            50%       { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
        }

        .alert-menunggu {
            display: flex; align-items: center; gap: 14px;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1.5px solid #fde68a;
            border-radius: 14px; padding: 14px 20px;
            margin-bottom: 22px;
        }
        .alert-menunggu .icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: #f59e0b; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .alert-menunggu h4 { font-size: 14px; font-weight: 700; color: #92400e; margin: 0 0 2px; }
        .alert-menunggu p  { font-size: 13px; color: #b45309; margin: 0; }
        .alert-menunggu a  { margin-left: auto; white-space: nowrap; }

        .badge-lunas    { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-menunggu { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-belum    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }

        .btn-detail-tr {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 600;
            border: 1.5px solid #e4e4e7; background: #fafafa; color: #52525b;
            text-decoration: none; transition: all .2s; cursor: pointer;
        }
        .btn-detail-tr:hover { background: #6366f1; color: #fff; border-color: #6366f1; }

        .btn-verify-tr {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 700;
            border: none; background: #22c55e; color: #fff;
            text-decoration: none; transition: all .2s; cursor: pointer;
            box-shadow: 0 2px 8px rgba(34,197,94,.3);
        }
        .btn-verify-tr:hover { background: #16a34a; transform: translateY(-1px); }

        tr.row-menunggu td { background: #fffdf0; }
        tr.row-menunggu:hover td { background: #fef9c3 !important; }

        .invoice-code {
            font-family: 'Courier New', monospace;
            font-size: 13px; font-weight: 700;
            color: #4f46e5; letter-spacing: .3px;
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
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-credit-card"></i> Data Transaksi
                    <?php if ($jml_menunggu > 0): ?>
                        <span class="notif-badge"><?= $jml_menunggu ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-gear"></i> Kelola Admin</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar">
            <h1>Data Transaksi</h1>
            <p>Kelola dan verifikasi pembayaran pelanggan</p>
        </div>

        <?php if ($jml_menunggu > 0): ?>
        <div class="alert-menunggu">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <h4><?= $jml_menunggu ?> transaksi menunggu verifikasi pembayaran</h4>
                <p>Periksa bukti transfer pelanggan dan segera verifikasi atau tolak.</p>
            </div>
            <a href="?status=menunggu_verifikasi" class="btn-orange" style="padding:9px 18px;font-size:13px;">
                <i class="bi bi-funnel"></i> Tampilkan
            </a>
        </div>
        <?php endif; ?>

        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Transaksi</h3>
                <form method="GET" class="filter-form">
                    <input type="text" id="periode" name="periode"
                           value="<?= isset($_GET['periode']) ? htmlspecialchars($_GET['periode']) : '' ?>"
                           placeholder="Pilih Periode">

                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar"
                            <?= (isset($_GET['status']) && $_GET['status']=='belum_bayar') ? 'selected' : '' ?>>
                            Belum Bayar
                        </option>
                        <option value="menunggu_verifikasi"
                            <?= (isset($_GET['status']) && $_GET['status']=='menunggu_verifikasi') ? 'selected' : '' ?>>
                            Menunggu Verifikasi
                        </option>
                        <option value="lunas"
                            <?= (isset($_GET['status']) && $_GET['status']=='lunas') ? 'selected' : '' ?>>
                            Lunas
                        </option>
                    </select>

                    <button type="submit" class="btn-orange">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    <?php if (!empty($_GET['status']) || !empty($_GET['periode'])): ?>
                    <a href="index.php" style="padding:9px 14px;border-radius:8px;border:1.5px solid #e4e4e7;
                        color:#52525b;font-size:13px;font-weight:600;text-decoration:none;
                        background:#fafafa;display:inline-flex;align-items:center;gap:5px;">
                        <i class="bi bi-x"></i> Reset
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Periode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0):
                        while ($data = mysqli_fetch_assoc($query)):
                            $st = strtolower($data['status_pembayaran']);
                    ?>
                    <tr class="<?= $st === 'menunggu_verifikasi' ? 'row-menunggu' : '' ?>">
                        <td><?= $no++ ?></td>

                        <td>
                            <span class="invoice-code"><?= htmlspecialchars($data['kode_invoice']) ?></span>
                        </td>

                        <td><?= htmlspecialchars($data['nama_customer']) ?></td>

                        <td>
                            <?= date('M Y', mktime(0,0,0, $data['bulan_tagihan'], 1, $data['tahun_tagihan'])) ?>
                        </td>

                        <td style="font-weight:600;">
                            Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <?php if ($st === 'lunas'): ?>
                                <span class="badge-lunas">
                                    <i class="bi bi-check-circle-fill"></i> Lunas
                                </span>
                            <?php elseif ($st === 'menunggu_verifikasi'): ?>
                                <span class="badge-menunggu">
                                    <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                </span>
                            <?php else: ?>
                                <span class="badge-belum">
                                    <i class="bi bi-x-circle-fill"></i> Belum Bayar
                                </span>
                            <?php endif; ?>
                        </td>

                        <td style="color:#a1a1aa;font-size:13px;">
                            <?= !empty($data['tanggal_bayar']) ? date('d/m/Y', strtotime($data['tanggal_bayar'])) : '—' ?>
                        </td>

                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                <a href="detail.php?id=<?= $data['id_transaksi'] ?>"
                                   class="btn-detail-tr" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>

                                <?php if ($st === 'menunggu_verifikasi'): ?>
                                <a href="detail.php?id=<?= $data['id_transaksi'] ?>"
                                   class="btn-verify-tr" title="Verifikasi Pembayaran">
                                    <i class="bi bi-shield-check"></i> Verifikasi
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#a1a1aa;">
                            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                            Tidak ada data transaksi ditemukan.
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
flatpickr("#periode", {
    plugins: [
        new monthSelectPlugin({ shorthand: true, dateFormat: "Y-m", altFormat: "F Y" })
    ]
});
</script>

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
</body>
</html>