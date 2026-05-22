<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id = $_GET['id'] ?? '';

$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_transaksi.*,
        tb_customer.nama_customer,
        tb_customer.email_customer,
        tb_customer.telepon_customer,
        tb_customer.alamat_customer,
        tb_paket.nama_paket,
        tb_paket.kecepatan,
        tb_paket.harga
     FROM tb_transaksi
     LEFT JOIN tb_langganan ON tb_transaksi.id_langganan = tb_langganan.id_langganan
     LEFT JOIN tb_customer ON tb_langganan.id_customer = tb_customer.id_customer
     LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket
     WHERE tb_transaksi.id_transaksi = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data transaksi tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

$status_pay = strtolower($data['status_pembayaran']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian | <?= $data['kode_invoice']; ?></title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- JS -->
    <script src="../../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-receipt"></i> Tagihan Saya</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Paket Internet</a></li>
            <li><a href="../profile/index.php"><i class="bi bi-person"></i> Profile</a></li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">
        
        <div class="topbar">
            <div>
                <h1>Rincian Invoice</h1>
                <p>Nomor Invoice: <strong><?= $data['kode_invoice']; ?></strong></p>
            </div>
        </div>

        <div class="detail-form-container">
            
            <div class="detail-section-title">
                <i class="bi bi-person-circle" style="color: #ff6b00;"></i> Data Transaksi
            </div>
            <div class="form-grid-2">
                <div class="form-field">
                    <label>Nama Pelanggan</label>
                    <div class="value-box"><?= $data['nama_customer'] ?? '-'; ?></div>
                </div>
                <div class="form-field">
                    <label>Nomor Telepon / WhatsApp</label>
                    <div class="value-box"><?= $data['telepon_customer'] ?? '-'; ?></div>
                </div>
                <div class="form-field">
                    <label>Alamat Email</label>
                    <div class="value-box"><?= $data['email_customer'] ?? '-'; ?></div>
                </div>
                <div class="form-field">
                    <label>Alamat Pemasangan</label>
                    <div class="value-box"><?= $data['alamat_customer'] ?? '-'; ?></div>
                </div>
            </div>

            <div class="detail-section-title">
                <i class="bi bi-wifi" style="color: #ff6b00;"></i> Paket & Status Tagihan
            </div>
            <div class="form-grid-2">
                <div class="form-field">
                    <label>Produk / Paket Internet</label>
                    <div class="value-box"><?= $data['nama_paket'] ?? '-'; ?> (<?= $data['kecepatan'] ?? '-'; ?>)</div>
                </div>
                <div class="form-field">
                    <label>Periode Penggunaan</label>
                    <div class="value-box">Bulan <?= $data['bulan_tagihan']; ?> / Tahun <?= $data['tahun_tagihan']; ?></div>
                </div>
                <div class="form-field">
                    <label>Total Nominal Tagihan</label>
                    <div class="value-box" style="color: #ff6b00; font-weight: 700;">Rp <?= number_format($data['jumlah_bayar']); ?></div>
                </div>
                <div class="form-field">
                    <label>Status Pembayaran Saat Ini</label>
                    <div style="margin-top: 5px;">
                        <?php if ($status_pay == 'lunas') : ?>
                            <span class="badge-status badge-lunas"><i class="bi bi-check-circle-fill"></i> Terverifikasi Lunas</span>
                        <?php elseif ($status_pay == 'menunggu konfirmasi' || $status_pay == 'menunggu') : ?>
                            <span class="badge-status badge-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Admin</span>
                        <?php else : ?>
                            <span class="badge-status badge-belum"><i class="bi bi-exclamation-triangle-fill"></i> Belum Dibayar</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="detail-section-title">
                <i class="bi bi-image" style="color: #ff6b00;"></i> Lampiran Bukti Pembayaran
            </div>
            <div class="form-field">
                <div class="bukti-container">
                    <?php if (!empty($data['bukti_pembayaran'])) : ?>
                        <p style="margin-bottom: 12px; color: #555;"><i class="bi bi-info-circle"></i> Berikut adalah foto struk/bukti transfer yang dikirimkan:</p>
                        <a href="../../assets/uploads/bukti/<?= $data['bukti_pembayaran']; ?>" target="_blank" title="Klik untuk memperbesar">
                            <img src="../../assets/uploads/bukti/<?= $data['bukti_pembayaran']; ?>" class="img-bukti-view">
                        </a>
                        <p style="margin-top: 10px; font-size: 0.85rem; color: #888;">* Klik gambar untuk membuka di tab baru (Resolusi Penuh)</p>
                    <?php else : ?>
                        <div style="padding: 20px; color: #888;">
                            <i class="bi bi-camera-video-off" style="font-size: 2.5rem; display: block; margin-bottom: 10px;"></i>
                            <p>Tidak ada berkas bukti pembayaran yang dilampirkan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="action-flex">
                <a href="index.php" class="btn-back-gray">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
                
                <?php if ($status_pay == 'belum bayar' || $status_pay == 'belum') : ?>
                    <a href="bayar.php?id=<?= $data['id_transaksi']; ?>" class="btn-orange btn-orange-flex">
                        Upload Bukti Sekarang <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ================= LOGOUT MODAL ================= -->
<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-box-arrow-right"></i>
        </div>
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