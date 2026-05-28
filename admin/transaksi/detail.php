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
        tb_paket.nama_paket,
        tb_paket.kecepatan,
        tb_langganan.id_customer
    FROM tb_transaksi
    INNER JOIN tb_langganan ON tb_transaksi.id_langganan = tb_langganan.id_langganan
    INNER JOIN tb_customer  ON tb_langganan.id_customer  = tb_customer.id_customer
    INNER JOIN tb_paket     ON tb_langganan.id_paket     = tb_paket.id_paket
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi <?= htmlspecialchars($data['kode_invoice']) ?> – Anuwani</title>
    <link rel="icon"        type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet"  href="../../assets/css/style.css">
    <link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
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

        @media (max-width: 640px) {
            .field-grid { grid-template-columns: 1fr; }
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
            <li><a href="index.php" class="active"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
            <li><a href="../admin_user/list_admin.php"><i class="bi bi-person-gear"></i> Kelola Admin</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;flex-wrap:wrap;">
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
                        <label>Produk / Paket Internet</label>
                        <div class="field-value">
                            <?= htmlspecialchars($data['nama_paket']) ?>
                            <span style="font-size:12px;color:#a1a1aa;margin-left:8px;">
                                (<?= htmlspecialchars($data['kecepatan']) ?>)
                            </span>
                        </div>
                    </div>
                    <div class="field-item">
                        <label>Periode Tagihan</label>
                        <div class="field-value">
                            Bulan <?= (int)$data['bulan_tagihan'] ?> /
                            Tahun <?= (int)$data['tahun_tagihan'] ?>
                            &nbsp;·&nbsp;
                            <strong><?= $nama_bln[(int)$data['bulan_tagihan']] ?> <?= $data['tahun_tagihan'] ?></strong>
                        </div>
                    </div>
                    <div class="field-item">
                        <label>Total Nominal Tagihan</label>
                        <div class="field-value highlight">
                            Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="field-item">
                        <label>Status Pembayaran</label>
                        <div class="field-value" style="background:transparent;border:none;padding:0;">
                            <?php if ($status === 'lunas'): ?>
                                <span class="status-pill sp-lunas">
                                    <i class="bi bi-check-circle-fill"></i> Lunas
                                </span>
                            <?php elseif ($status === 'menunggu_verifikasi'): ?>
                                <span class="status-pill sp-menunggu">
                                    <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                </span>
                            <?php else: ?>
                                <span class="status-pill sp-belum">
                                    <i class="bi bi-x-circle-fill"></i> Belum Dibayar
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($data['metode_pembayaran']): ?>
                    <div class="field-item">
                        <label>Metode Pembayaran</label>
                        <div class="field-value"><?= htmlspecialchars(ucfirst($data['metode_pembayaran'])) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($data['tanggal_bayar']): ?>
                    <div class="field-item">
                        <label>Tanggal Pembayaran</label>
                        <div class="field-value">
                            <?= date('d F Y', strtotime($data['tanggal_bayar'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
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
                $bukti_full = __DIR__ . '/../../assets/uploads/bukti/' . $bukti_path;
                $bukti_url  = '../../assets/uploads/bukti/' . $bukti_path;
                $has_bukti  = !empty($bukti_path) && file_exists($bukti_full);
                ?>
                <?php if ($has_bukti): ?>
                    <div class="bukti-wrap">
                        <a href="<?= $bukti_url ?>" target="_blank" title="Buka resolusi penuh">
                            <img src="<?= $bukti_url ?>" alt="Bukti Pembayaran">
                        </a>
                        <p class="bukti-hint">
                            <i class="bi bi-info-circle"></i>
                            Klik gambar untuk membuka di tab baru (Resolusi Penuh)
                        </p>
                    </div>
                <?php elseif (!empty($bukti_path)): ?>
                    <div class="bukti-wrap">
                        <div class="no-bukti">
                            <i class="bi bi-exclamation-triangle"></i>
                            <p style="font-weight:600;color:#d97706;">File bukti tidak ditemukan di server</p>
                            <p style="font-size:13px;">Path: <?= htmlspecialchars($bukti_path) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bukti-wrap">
                        <div class="no-bukti">
                            <i class="bi bi-image-fill"></i>
                            <p style="font-weight:600;">Belum ada bukti pembayaran dikirim</p>
                            <p style="font-size:13px;">Pelanggan belum mengunggah foto bukti transfer.</p>
                        </div>
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
</body>
</html>