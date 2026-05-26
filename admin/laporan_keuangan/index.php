<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$filter_tahun = isset($_GET['tahun']) && $_GET['tahun'] != '' ? (int)$_GET['tahun'] : date('Y');
$filter_tipe  = isset($_GET['tipe'])  && in_array($_GET['tipe'], ['bulanan','tahunan']) ? $_GET['tipe'] : 'bulanan';

$q_tahun = mysqli_query($koneksi, "SELECT DISTINCT tahun_tagihan FROM tb_transaksi ORDER BY tahun_tagihan DESC");
$list_tahun = [];
while ($r = mysqli_fetch_assoc($q_tahun)) $list_tahun[] = $r['tahun_tagihan'];
if (empty($list_tahun)) $list_tahun = [date('Y')];

$q_ringkasan = mysqli_query($koneksi, "
    SELECT
        COUNT(*) AS total_transaksi,
        SUM(jumlah_bayar) AS total_tagihan,
        SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS total_masuk,
        SUM(CASE WHEN status_pembayaran='belum_bayar' THEN jumlah_bayar ELSE 0 END) AS total_belum,
        SUM(CASE WHEN status_pembayaran='menunggu' THEN jumlah_bayar ELSE 0 END) AS total_menunggu,
        COUNT(CASE WHEN status_pembayaran='lunas' THEN 1 END) AS jml_lunas,
        COUNT(CASE WHEN status_pembayaran='belum_bayar' THEN 1 END) AS jml_belum,
        COUNT(CASE WHEN status_pembayaran='menunggu' THEN 1 END) AS jml_menunggu
    FROM tb_transaksi
    WHERE tahun_tagihan = $filter_tahun
");
$ringkasan = mysqli_fetch_assoc($q_ringkasan);

$q_bulanan = mysqli_query($koneksi, "
    SELECT
        bulan_tagihan,
        tahun_tagihan,
        COUNT(*) AS total_transaksi,
        SUM(jumlah_bayar) AS total_tagihan,
        SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS pendapatan,
        SUM(CASE WHEN status_pembayaran='belum_bayar' THEN jumlah_bayar ELSE 0 END) AS belum_bayar,
        COUNT(CASE WHEN status_pembayaran='lunas' THEN 1 END) AS jml_lunas,
        COUNT(CASE WHEN status_pembayaran='belum_bayar' THEN 1 END) AS jml_belum,
        COUNT(CASE WHEN status_pembayaran='menunggu' THEN 1 END) AS jml_menunggu
    FROM tb_transaksi
    WHERE tahun_tagihan = $filter_tahun
    GROUP BY tahun_tagihan, bulan_tagihan
    ORDER BY bulan_tagihan ASC
");

$data_bulanan = [];
while ($r = mysqli_fetch_assoc($q_bulanan)) $data_bulanan[] = $r;

$q_tahunan = mysqli_query($koneksi, "
    SELECT
        tahun_tagihan,
        COUNT(*) AS total_transaksi,
        SUM(jumlah_bayar) AS total_tagihan,
        SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS pendapatan,
        SUM(CASE WHEN status_pembayaran='belum_bayar' THEN jumlah_bayar ELSE 0 END) AS belum_bayar,
        COUNT(CASE WHEN status_pembayaran='lunas' THEN 1 END) AS jml_lunas,
        COUNT(CASE WHEN status_pembayaran='belum_bayar' THEN 1 END) AS jml_belum
    FROM tb_transaksi
    GROUP BY tahun_tagihan
    ORDER BY tahun_tagihan DESC
");

$data_tahunan = [];
while ($r = mysqli_fetch_assoc($q_tahunan)) $data_tahunan[] = $r;

$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];

function rp($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }

$chart_labels    = [];
$chart_masuk     = [];
$chart_belum     = [];
for ($i = 1; $i <= 12; $i++) {
    $chart_labels[] = substr(['','Jan','Feb','Mar','Apr','Mei','Jun',
                              'Jul','Agu','Sep','Okt','Nov','Des'][$i], 0, 3);
    $found = false;
    foreach ($data_bulanan as $b) {
        if ((int)$b['bulan_tagihan'] === $i) {
            $chart_masuk[]  = (int)$b['pendapatan'];
            $chart_belum[]  = (int)$b['belum_bayar'];
            $found = true; break;
        }
    }
    if (!$found) { $chart_masuk[] = 0; $chart_belum[] = 0; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Keuangan – Anuwani</title>
<link rel="icon" type="image/png" href="../../assets/images/logo.png">
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../../assets/js/script.js" defer></script>
<style>

.lap-stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; margin-bottom: 28px; }
.lap-stat {
    background: #fff; border-radius: 16px; padding: 22px 24px;
    border: 1px solid #e4e4e7; transition: all .2s;
    display: flex; align-items: center; gap: 16px;
}
.lap-stat:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); transform: translateY(-2px); }
.lap-stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.lap-stat-body small { font-size: 12px; color: #a1a1aa; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 4px; }
.lap-stat-body strong { font-size: 20px; font-weight: 800; color: #18181b; display: block; line-height: 1.2; }
.lap-stat-body span   { font-size: 12px; color: #a1a1aa; }

.icon-green  { background: #f0fdf4; color: #22c55e; }
.icon-red    { background: #fef2f2; color: #ef4444; }
.icon-orange { background: #fff4ee; color: #f4600c; }
.icon-blue   { background: #eff6ff; color: #3b82f6; }
.icon-yellow { background: #fffbeb; color: #f59e0b; }

.lap-section {
    background: #fff; border-radius: 16px; border: 1px solid #e4e4e7;
    overflow: hidden; margin-bottom: 24px;
}
.lap-section-header {
    padding: 18px 24px; border-bottom: 1px solid #e4e4e7;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.lap-section-header h3 { font-size: 16px; font-weight: 800; color: #18181b; margin: 0; }
.lap-section-body { padding: 24px; overflow-x: auto; }

.lap-table { width: 100%; border-collapse: collapse; min-width: 600px; }
.lap-table thead tr { background: #fafafa; }
.lap-table th { padding: 11px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #a1a1aa; border-bottom: 1px solid #e4e4e7; text-align: left; }
.lap-table td { padding: 13px 16px; font-size: 14px; color: #18181b; border-bottom: 1px solid #f4f4f5; }
.lap-table tbody tr:last-child td { border-bottom: none; }
.lap-table tbody tr:hover td { background: #fafafa; }
.lap-table tfoot td { padding: 13px 16px; font-size: 14px; font-weight: 700; color: #18181b; border-top: 2px solid #e4e4e7; background: #fafafa; }

.chip { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.chip-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.chip-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.chip-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

.chart-wrapper { height: 300px; position: relative; }

.filter-bar {
    background: #fff; border-radius: 14px; border: 1px solid #e4e4e7;
    padding: 18px 22px; margin-bottom: 24px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.filter-bar label { font-size: 13px; font-weight: 700; color: #52525b; }
.filter-bar select, .filter-bar input {
    padding: 9px 14px; border-radius: 8px;
    border: 1.5px solid #e4e4e7; font-size: 14px;
    outline: none; background: #fafafa; color: #18181b;
    transition: all .2s; cursor: pointer;
}
.filter-bar select:focus { border-color: #f4600c; box-shadow: 0 0 0 3px rgba(244,96,12,.12); }

.export-group { display: flex; gap: 8px; margin-left: auto; }

.btn-export-pdf {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700;
    border: none; cursor: pointer; transition: all .2s; text-decoration: none;
    background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca;
}
.btn-export-pdf:hover { background: #dc2626; color: #fff; border-color: #dc2626; }

.btn-export-excel {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700;
    border: none; cursor: pointer; transition: all .2s; text-decoration: none;
    background: #f0fdf4; color: #16a34a; border: 1.5px solid #bbf7d0;
}
.btn-export-excel:hover { background: #16a34a; color: #fff; border-color: #16a34a; }

.tab-group { display: flex; gap: 6px; }
.tab-btn {
    padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 700;
    border: 1.5px solid #e4e4e7; background: #fafafa; color: #52525b;
    cursor: pointer; transition: all .2s; text-decoration: none; display: inline-block;
}
.tab-btn.active { background: #f4600c; color: #fff; border-color: #f4600c; }
.tab-btn:hover:not(.active) { background: #f4f4f5; color: #18181b; }

.progress-bar-wrap { background: #f4f4f5; border-radius: 4px; height: 6px; margin-top: 6px; overflow: hidden; }
.progress-bar-fill { height: 100%; border-radius: 4px; background: #22c55e; }

@media (max-width: 900px) { .lap-stat-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px) { .lap-stat-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="dashboard-layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-logo">
        <img src="../../assets/images/logo.png" alt="Logo">
        <h2>Anuwani</h2>
    </div>
    <ul>
        <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
        <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
        <li><a href="../transaksi/index.php"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
        <li><a href="index.php" class="active"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
        <li><a href="../admin_user/list_admin.php"><i class="bi bi-person-gear"></i> Kelola Admin</a></li>
        <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="dashboard-content">
    <div class="topbar">
        <h1>Laporan Keuangan</h1>
        <p>Rekap pendapatan dan tagihan Anuwani Network</p>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;width:100%;">

            <label>Tampilkan:</label>
            <div class="tab-group">
                <a href="?tipe=bulanan&tahun=<?= $filter_tahun ?>"
                   class="tab-btn <?= $filter_tipe=='bulanan'?'active':'' ?>">
                    <i class="bi bi-calendar-month"></i> Bulanan
                </a>
                <a href="?tipe=tahunan&tahun=<?= $filter_tahun ?>"
                   class="tab-btn <?= $filter_tipe=='tahunan'?'active':'' ?>">
                    <i class="bi bi-calendar-range"></i> Tahunan
                </a>
            </div>

            <?php if ($filter_tipe === 'bulanan'): ?>
            <label>Tahun:</label>
            <select name="tahun" onchange="this.form.submit()">
                <?php foreach ($list_tahun as $t): ?>
                    <option value="<?= $t ?>" <?= $t==$filter_tahun?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="tipe" value="bulanan">
            <?php endif; ?>


            <div class="export-group">
                <a href="export_pdf.php?tipe=<?= $filter_tipe ?>&tahun=<?= $filter_tahun ?>"
                   target="_blank" class="btn-export-pdf">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="export_excel.php?tipe=<?= $filter_tipe ?>&tahun=<?= $filter_tahun ?>"
                   class="btn-export-excel">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    <?php if ($filter_tipe === 'bulanan'): ?>


    <div class="lap-stat-grid">
        <div class="lap-stat">
            <div class="lap-stat-icon icon-orange"><i class="bi bi-cash-stack"></i></div>
            <div class="lap-stat-body">
                <small>Total Tagihan <?= $filter_tahun ?></small>
                <strong><?= rp($ringkasan['total_tagihan']) ?></strong>
                <span><?= (int)$ringkasan['total_transaksi'] ?> transaksi</span>
            </div>
        </div>
        <div class="lap-stat">
            <div class="lap-stat-icon icon-green"><i class="bi bi-check-circle"></i></div>
            <div class="lap-stat-body">
                <small>Pendapatan Masuk</small>
                <strong><?= rp($ringkasan['total_masuk']) ?></strong>
                <span><?= (int)$ringkasan['jml_lunas'] ?> transaksi lunas</span>
            </div>
        </div>
        <div class="lap-stat">
            <div class="lap-stat-icon icon-red"><i class="bi bi-x-circle"></i></div>
            <div class="lap-stat-body">
                <small>Belum Dibayar</small>
                <strong><?= rp($ringkasan['total_belum']) ?></strong>
                <span><?= (int)$ringkasan['jml_belum'] ?> transaksi</span>
            </div>
        </div>
        <div class="lap-stat">
            <div class="lap-stat-icon icon-yellow"><i class="bi bi-hourglass-split"></i></div>
            <div class="lap-stat-body">
                <small>Menunggu Verifikasi</small>
                <strong><?= rp($ringkasan['total_menunggu']) ?></strong>
                <span><?= (int)$ringkasan['jml_menunggu'] ?> transaksi</span>
            </div>
        </div>
    </div>

    <div class="lap-section">
        <div class="lap-section-header">
            <h3><i class="bi bi-bar-chart" style="color:#f4600c;margin-right:8px;"></i>Grafik Pendapatan <?= $filter_tahun ?></h3>
        </div>
        <div class="lap-section-body">
            <div class="chart-wrapper">
                <canvas id="chartBulanan"></canvas>
            </div>
        </div>
    </div>

    <div class="lap-section">
        <div class="lap-section-header">
            <h3><i class="bi bi-table" style="color:#f4600c;margin-right:8px;"></i>Rincian Per Bulan — <?= $filter_tahun ?></h3>
            <span style="font-size:13px;color:#a1a1aa;"><?= count($data_bulanan) ?> bulan tercatat</span>
        </div>
        <div class="lap-section-body" style="padding:0;">
            <table class="lap-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Total Transaksi</th>
                        <th>Lunas</th>
                        <th>Belum Bayar</th>
                        <th>Menunggu</th>
                        <th>Pendapatan Masuk</th>
                        <th>Tunggakan</th>
                        <th>Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data_bulanan)): ?>
                    <tr><td colspan="8" style="text-align:center;color:#a1a1aa;padding:32px;">Tidak ada data untuk tahun <?= $filter_tahun ?></td></tr>
                <?php else: ?>
                    <?php
                    $tot_transaksi = $tot_lunas = $tot_belum = $tot_menunggu = $tot_masuk = $tot_tunggakan = 0;
                    foreach ($data_bulanan as $b):
                        $pct = $b['total_tagihan'] > 0 ? round($b['pendapatan'] / $b['total_tagihan'] * 100) : 0;
                        $tot_transaksi += $b['total_transaksi'];
                        $tot_lunas     += $b['jml_lunas'];
                        $tot_belum     += $b['jml_belum'];
                        $tot_menunggu  += $b['jml_menunggu'];
                        $tot_masuk     += $b['pendapatan'];
                        $tot_tunggakan += $b['belum_bayar'];
                    ?>
                    <tr>
                        <td><strong><?= $nama_bulan[(int)$b['bulan_tagihan']] ?></strong></td>
                        <td><?= (int)$b['total_transaksi'] ?></td>
                        <td><span class="chip chip-green"><?= (int)$b['jml_lunas'] ?></span></td>
                        <td><span class="chip chip-red"><?= (int)$b['jml_belum'] ?></span></td>
                        <td><span class="chip chip-yellow"><?= (int)$b['jml_menunggu'] ?></span></td>
                        <td style="color:#16a34a;font-weight:700;"><?= rp($b['pendapatan']) ?></td>
                        <td style="color:#dc2626;"><?= rp($b['belum_bayar']) ?></td>
                        <td>
                            <div style="min-width:80px;">
                                <span style="font-size:12px;font-weight:700;color:<?= $pct>=80?'#16a34a':($pct>=50?'#d97706':'#dc2626') ?>">
                                    <?= $pct ?>%
                                </span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill"
                                         style="width:<?= $pct ?>%;background:<?= $pct>=80?'#22c55e':($pct>=50?'#f59e0b':'#ef4444') ?>;">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <?php if (!empty($data_bulanan)): ?>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td><?= $tot_transaksi ?></td>
                        <td><?= $tot_lunas ?></td>
                        <td><?= $tot_belum ?></td>
                        <td><?= $tot_menunggu ?></td>
                        <td style="color:#16a34a;"><?= rp($tot_masuk) ?></td>
                        <td style="color:#dc2626;"><?= rp($tot_tunggakan) ?></td>
                        <td>—</td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <?php else: ?>

    <?php
    $q_all = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) AS tot, SUM(jumlah_bayar) AS total,
                SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS masuk
         FROM tb_transaksi"));
    ?>
    <div class="lap-stat-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="lap-stat">
            <div class="lap-stat-icon icon-blue"><i class="bi bi-receipt"></i></div>
            <div class="lap-stat-body">
                <small>Total Semua Transaksi</small>
                <strong><?= (int)$q_all['tot'] ?></strong>
                <span>Sejak pertama beroperasi</span>
            </div>
        </div>
        <div class="lap-stat">
            <div class="lap-stat-icon icon-orange"><i class="bi bi-cash-stack"></i></div>
            <div class="lap-stat-body">
                <small>Total Tagihan Keseluruhan</small>
                <strong><?= rp($q_all['total']) ?></strong>
            </div>
        </div>
        <div class="lap-stat">
            <div class="lap-stat-icon icon-green"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="lap-stat-body">
                <small>Total Pendapatan Masuk</small>
                <strong><?= rp($q_all['masuk']) ?></strong>
            </div>
        </div>
    </div>

    <div class="lap-section">
        <div class="lap-section-header">
            <h3><i class="bi bi-bar-chart" style="color:#f4600c;margin-right:8px;"></i>Grafik Pendapatan Per Tahun</h3>
        </div>
        <div class="lap-section-body">
            <div class="chart-wrapper">
                <canvas id="chartTahunan"></canvas>
            </div>
        </div>
    </div>


    <div class="lap-section">
        <div class="lap-section-header">
            <h3><i class="bi bi-table" style="color:#f4600c;margin-right:8px;"></i>Rekap Per Tahun</h3>
        </div>
        <div class="lap-section-body" style="padding:0;">
            <table class="lap-table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Total Transaksi</th>
                        <th>Lunas</th>
                        <th>Belum Bayar</th>
                        <th>Pendapatan Masuk</th>
                        <th>Tunggakan</th>
                        <th>Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data_tahunan)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#a1a1aa;padding:32px;">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($data_tahunan as $t):
                        $pct = $t['total_tagihan'] > 0 ? round($t['pendapatan'] / $t['total_tagihan'] * 100) : 0;
                    ?>
                    <tr>
                        <td><strong><?= $t['tahun_tagihan'] ?></strong></td>
                        <td><?= (int)$t['total_transaksi'] ?></td>
                        <td><span class="chip chip-green"><?= (int)$t['jml_lunas'] ?></span></td>
                        <td><span class="chip chip-red"><?= (int)$t['jml_belum'] ?></span></td>
                        <td style="color:#16a34a;font-weight:700;"><?= rp($t['pendapatan']) ?></td>
                        <td style="color:#dc2626;"><?= rp($t['belum_bayar']) ?></td>
                        <td>
                            <span style="font-size:12px;font-weight:700;color:<?= $pct>=80?'#16a34a':($pct>=50?'#d97706':'#dc2626') ?>">
                                <?= $pct ?>%
                            </span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill"
                                     style="width:<?= $pct ?>%;background:<?= $pct>=80?'#22c55e':($pct>=50?'#f59e0b':'#ef4444') ?>;">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>
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
<?php if ($filter_tipe === 'bulanan'): ?>
// Grafik Bulanan
new Chart(document.getElementById('chartBulanan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [
            {
                label: 'Pendapatan Masuk',
                data: <?= json_encode($chart_masuk) ?>,
                backgroundColor: 'rgba(34,197,94,.75)',
                borderRadius: 8, borderSkipped: false,
            },
            {
                label: 'Belum Dibayar',
                data: <?= json_encode($chart_belum) ?>,
                backgroundColor: 'rgba(239,68,68,.65)',
                borderRadius: 8, borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { size: 13, weight: '600' } } },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt'
                },
                grid: { color: '#f4f4f5' }
            },
            x: { grid: { display: false } }
        }
    }
});
<?php else: ?>
const tahunLabels = <?= json_encode(array_column($data_tahunan, 'tahun_tagihan')) ?>;
const tahunMasuk  = <?= json_encode(array_map(fn($r)=>(int)$r['pendapatan'], $data_tahunan)) ?>;
const tahunBelum  = <?= json_encode(array_map(fn($r)=>(int)$r['belum_bayar'], $data_tahunan)) ?>;

new Chart(document.getElementById('chartTahunan'), {
    type: 'bar',
    data: {
        labels: tahunLabels,
        datasets: [
            { label: 'Pendapatan Masuk', data: tahunMasuk, backgroundColor: 'rgba(34,197,94,.75)', borderRadius: 10, borderSkipped: false },
            { label: 'Belum Dibayar',    data: tahunBelum, backgroundColor: 'rgba(239,68,68,.65)',  borderRadius: 10, borderSkipped: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { size: 13, weight: '600' } } },
            tooltip: { callbacks: { label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID') } }
        },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }, grid: { color: '#f4f4f5' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>
