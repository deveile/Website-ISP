<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') { header("Location: ../../auth/login.php"); exit; }

$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$filter_tipe  = isset($_GET['tipe']) && $_GET['tipe'] === 'tahunan' ? 'tahunan' : 'bulanan';

$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];

function rp($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }

if ($filter_tipe === 'bulanan') {
    $q = mysqli_query($koneksi, "
        SELECT bulan_tagihan, tahun_tagihan,
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
    $rows = [];
    $tot  = ['total_transaksi'=>0,'total_tagihan'=>0,'pendapatan'=>0,'belum_bayar'=>0,'jml_lunas'=>0,'jml_belum'=>0,'jml_menunggu'=>0];
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
        foreach ($tot as $k => $_) $tot[$k] += $r[$k];
    }
} else {
    $q = mysqli_query($koneksi, "
        SELECT tahun_tagihan,
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
    $rows = [];
    $tot  = ['total_transaksi'=>0,'total_tagihan'=>0,'pendapatan'=>0,'belum_bayar'=>0,'jml_lunas'=>0,'jml_belum'=>0];
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
        foreach ($tot as $k => $_) $tot[$k] += $r[$k];
    }
}

$judul    = $filter_tipe === 'bulanan'
    ? "Laporan Keuangan Bulanan – Tahun $filter_tahun"
    : "Laporan Keuangan Tahunan – Semua Periode";
$tanggal  = date('d F Y');
$jam      = date('H:i');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $judul ?></title>
<link rel="icon" type="image/png" href="../../assets/images/logo.png">
<style>
  @page { size: A4 landscape; margin: 15mm 16mm; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #222; background: #fff; }

  .pdf-header { display: flex; align-items: center; gap: 16px; margin-bottom: 10px; border-bottom: 3px solid #f4600c; padding-bottom: 10px; }
  .pdf-logo   { width: 44px; height: 44px; object-fit: contain; }
  .pdf-title  h1 { font-size: 18px; font-weight: 800; color: #18181b; }
  .pdf-title  p  { font-size: 11px; color: #71717a; margin-top: 2px; }
  .pdf-meta   { margin-left: auto; text-align: right; font-size: 11px; color: #71717a; line-height: 1.6; }

  .summary { display: flex; gap: 12px; margin: 14px 0; }
  .sum-box {
    flex: 1; border: 1px solid #e4e4e7; border-radius: 8px;
    padding: 10px 14px; background: #fafafa;
  }
  .sum-box small { font-size: 10px; color: #a1a1aa; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 3px; }
  .sum-box strong { font-size: 15px; font-weight: 800; }
  .sum-green { border-left: 3px solid #22c55e; }
  .sum-red   { border-left: 3px solid #ef4444; }
  .sum-blue  { border-left: 3px solid #3b82f6; }
  .sum-orange{ border-left: 3px solid #f4600c; }

  table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11.5px; }
  thead tr { background: #f4600c; color: #fff; }
  th { padding: 9px 12px; text-align: left; font-weight: 700; font-size: 11px; }
  td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
  tbody tr:nth-child(even) td { background: #fafafa; }
  tfoot td { font-weight: 800; background: #fff4ee; border-top: 2px solid #f4600c; padding: 10px 12px; }

  .chip { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; }
  .chip-g { background: #f0fdf4; color: #16a34a; }
  .chip-r { background: #fef2f2; color: #dc2626; }
  .chip-y { background: #fffbeb; color: #d97706; }

  .pct-bar { width: 70px; height: 6px; background: #f0f0f0; border-radius: 3px; display: inline-block; vertical-align: middle; overflow: hidden; }
  .pct-fill { height: 100%; border-radius: 3px; display: block; }

  .footer { margin-top: 18px; text-align: center; font-size: 10px; color: #a1a1aa; border-top: 1px solid #e4e4e7; padding-top: 8px; }

  @media print {
    .no-print { display: none !important; }
    body { background: #fff; }
  }

  /* Print button */
  .print-bar {
    position: fixed; bottom: 20px; right: 20px;
    display: flex; gap: 10px; z-index: 99;
  }

  .btn-print {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 22px; border-radius: 10px; border: none;
    background: #f4600c; color: #fff; font-size: 14px;
    font-weight: 700; cursor: pointer; box-shadow: 0 4px 16px rgba(244,96,12,.4);
  }

  .btn-close {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 22px; border-radius: 10px; border: none;
    background: #18181b; color: #fff; font-size: 14px;
    font-weight: 700; cursor: pointer; text-decoration: none;
  }
</style>
</head>
<body>

<!-- Print Bar -->
<div class="print-bar no-print">
    <a href="index.php?tipe=<?= $filter_tipe ?>&tahun=<?= $filter_tahun ?>" class="btn-close">
        ✕ Tutup
    </a>
    <button class="btn-print" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>
</div>

<!-- Header -->
<div class="pdf-header">
    <img class="pdf-logo" src="../../assets/images/logo.png"
         onerror="this.style.display='none'" alt="Logo">
    <div class="pdf-title">
        <h1><?= $judul ?></h1>
        <p>Anuwani Network</p>
    </div>
    <div class="pdf-meta">
        Dicetak: <?= $tanggal ?>, <?= $jam ?> WIB<br>
        Oleh: <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?>
    </div>
</div>

<!-- Summary -->
<div class="summary">
    <div class="sum-box sum-blue">
        <small>Total Transaksi</small>
        <strong><?= (int)$tot['total_transaksi'] ?></strong>
    </div>
    <div class="sum-box sum-orange">
        <small>Total Tagihan</small>
        <strong><?= rp($tot['total_tagihan']) ?></strong>
    </div>
    <div class="sum-box sum-green">
        <small>Pendapatan Masuk</small>
        <strong><?= rp($tot['pendapatan']) ?></strong>
    </div>
    <div class="sum-box sum-red">
        <small>Tunggakan / Belum Bayar</small>
        <strong><?= rp($tot['belum_bayar']) ?></strong>
    </div>
</div>

<!-- Table -->
<?php if ($filter_tipe === 'bulanan'): ?>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Bulan</th>
            <th>Total Transaksi</th>
            <th>Lunas</th>
            <th>Belum Bayar</th>
            <th>Menunggu</th>
            <th>Pendapatan Masuk</th>
            <th>Tunggakan</th>
            <th>Total Tagihan</th>
            <th>Realisasi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($rows)): ?>
        <tr><td colspan="10" style="text-align:center;color:#888;padding:20px;">Tidak ada data</td></tr>
    <?php else: ?>
        <?php $no=1; foreach ($rows as $r):
            $pct = $r['total_tagihan']>0 ? round($r['pendapatan']/$r['total_tagihan']*100) : 0;
            $col = $pct>=80?'#22c55e':($pct>=50?'#f59e0b':'#ef4444');
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= $nama_bulan[(int)$r['bulan_tagihan']] ?> <?= $r['tahun_tagihan'] ?></strong></td>
            <td><?= (int)$r['total_transaksi'] ?></td>
            <td><span class="chip chip-g"><?= (int)$r['jml_lunas'] ?></span></td>
            <td><span class="chip chip-r"><?= (int)$r['jml_belum'] ?></span></td>
            <td><span class="chip chip-y"><?= (int)$r['jml_menunggu'] ?></span></td>
            <td style="color:#16a34a;font-weight:700;"><?= rp($r['pendapatan']) ?></td>
            <td style="color:#dc2626;"><?= rp($r['belum_bayar']) ?></td>
            <td><?= rp($r['total_tagihan']) ?></td>
            <td>
                <span style="font-weight:700;color:<?= $col ?>"><?= $pct ?>%</span>
                <span class="pct-bar"><span class="pct-fill" style="width:<?= $pct ?>%;background:<?= $col ?>;"></span></span>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">TOTAL</td>
            <td><?= (int)$tot['total_transaksi'] ?></td>
            <td><?= (int)$tot['jml_lunas'] ?></td>
            <td><?= (int)$tot['jml_belum'] ?></td>
            <td><?= (int)$tot['jml_menunggu'] ?></td>
            <td style="color:#16a34a;"><?= rp($tot['pendapatan']) ?></td>
            <td style="color:#dc2626;"><?= rp($tot['belum_bayar']) ?></td>
            <td><?= rp($tot['total_tagihan']) ?></td>
            <td>—</td>
        </tr>
    </tfoot>
</table>

<?php else: ?>
<table>
    <thead>
        <tr>
            <th>No</th><th>Tahun</th><th>Total Transaksi</th>
            <th>Lunas</th><th>Belum Bayar</th>
            <th>Pendapatan Masuk</th><th>Tunggakan</th>
            <th>Total Tagihan</th><th>Realisasi</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=1; foreach ($rows as $r):
        $pct = $r['total_tagihan']>0 ? round($r['pendapatan']/$r['total_tagihan']*100) : 0;
        $col = $pct>=80?'#22c55e':($pct>=50?'#f59e0b':'#ef4444');
    ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= $r['tahun_tagihan'] ?></strong></td>
            <td><?= (int)$r['total_transaksi'] ?></td>
            <td><span class="chip chip-g"><?= (int)$r['jml_lunas'] ?></span></td>
            <td><span class="chip chip-r"><?= (int)$r['jml_belum'] ?></span></td>
            <td style="color:#16a34a;font-weight:700;"><?= rp($r['pendapatan']) ?></td>
            <td style="color:#dc2626;"><?= rp($r['belum_bayar']) ?></td>
            <td><?= rp($r['total_tagihan']) ?></td>
            <td>
                <span style="font-weight:700;color:<?= $col ?>"><?= $pct ?>%</span>
                <span class="pct-bar"><span class="pct-fill" style="width:<?= $pct ?>%;background:<?= $col ?>;"></span></span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">TOTAL</td>
            <td><?= (int)$tot['total_transaksi'] ?></td>
            <td><?= (int)$tot['jml_lunas'] ?></td>
            <td><?= (int)$tot['jml_belum'] ?></td>
            <td style="color:#16a34a;"><?= rp($tot['pendapatan']) ?></td>
            <td style="color:#dc2626;"><?= rp($tot['belum_bayar']) ?></td>
            <td><?= rp($tot['total_tagihan']) ?></td>
            <td>—</td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<div class="footer">
    Dokumen ini digenerate otomatis oleh sistem Anuwani Network •
    <?= $tanggal ?> • Halaman ini hanya untuk keperluan internal
</div>

<script>
// Auto print setelah halaman siap
window.addEventListener('load', function() {
    // Beri delay kecil agar logo sempat load
    setTimeout(function() {
        // Tidak auto print, biarkan user pilih
    }, 500);
});
</script>
</body>
</html>
