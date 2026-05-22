<?php
require_once __DIR__ . '/../auth/cek_login.php';
require_once __DIR__ . '/../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= CUSTOMER ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_customer.*,
        tb_langganan.id_langganan,
        tb_langganan.status_langganan,
        tb_langganan.tanggal_mulai,
        tb_langganan.tanggal_selesai,
        tb_paket.nama_paket,
        tb_paket.harga,
        tb_paket.kecepatan,
        tb_paket.deskripsi
    FROM tb_customer
    LEFT JOIN tb_langganan
        ON tb_customer.id_customer = tb_langganan.id_customer
    LEFT JOIN tb_paket
        ON tb_langganan.id_paket = tb_paket.id_paket
    WHERE tb_customer.id_user = '$id_user'
    LIMIT 1"
);

$data = mysqli_fetch_assoc($query);

/* ================= VALIDASI CUSTOMER (UBAH KE POPUP) ================= */
$show_error_popup = false;
if (!$data) {
    $show_error_popup = true;
}

/* ================= AMANKAN VARIABEL DEFAULT (SOLUSI UTAMA ERROR) ================= */
$t = null;
$nominal_tagihan = 0; // Taruh di sini agar selalu terbaca oleh HTML meskipun data langganan kosong
$riwayat = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE 1=0");

/* ================= JIKA SUDAH BERLANGGANAN ================= */
if ($data && !empty($data['id_langganan'])) {

    $bulan = date('n');
    $tahun = date('Y');

    /* ================= TAGIHAN BULAN INI ================= */
    $tagihan = mysqli_query(
        $koneksi,
        "SELECT *
        FROM tb_transaksi
        WHERE id_langganan = '".$data['id_langganan']."'
        AND bulan_tagihan = '$bulan'
        AND tahun_tagihan = '$tahun'
        LIMIT 1"
    );

    $t = mysqli_fetch_assoc($tagihan);

    // LOGIKA NOMINAL: Hanya tampil jika tagihan ada DAN statusnya belum lunas
    if ($t && strtolower($t['status_pembayaran']) != 'lunas') {
        $nominal_tagihan = $t['jumlah_bayar'] ?? $data['harga'];
    }

    /* ================= RIWAYAT ================= */
    $riwayat = mysqli_query(
        $koneksi,
        "SELECT *
        FROM tb_transaksi
        WHERE id_langganan = '".$data['id_langganan']."'
        ORDER BY id_transaksi DESC
        LIMIT 5"
    );
}

/* ================= HELPER FORMAT TANGGAL INDONESIA ================= */
function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $bulan_array = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan_array[(int)$split[1]] . ' ' . $split[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Dashboard Customer</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="../assets/js/script.js" defer></script>
</head>
<body>

<?php if ($show_error_popup) : ?>
<div class="error-overlay">
    <div class="error-modal-box">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <h2>Data Tidak Dibuat!</h2>
        <p>Akun login Anda terdaftar, namun profil data diri pelanggan belum diinput oleh Admin Anuwani.net.</p>
        <a href="../auth/logout.php" class="btn-error-return">Kembali ke Login</a>
    </div>
</div>
<?php exit; ?>
<?php endif; ?>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="index.php" class="active"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="tagihan/index.php"><i class="bi bi-receipt"></i> Tagihan Saya</a></li>
            <li><a href="paket/index.php"><i class="bi bi-wifi"></i> Paket Internet</a></li>
            <li><a href="profile/index.php"><i class="bi bi-person"></i> Profile</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <h1>Halo, <?= $data['nama_customer'] ?? 'User'; ?></h1>
            <p>Selamat datang kembali</p>
        </div>

        <div class="customer-hero-card">
            <div class="hero-left">
                <span class="hero-label">Paket Aktif</span>
                <h1><?= $data['nama_paket'] ?? 'Belum Berlangganan'; ?></h1>
                <p class="hero-speed"><?= $data['kecepatan'] ?? '-'; ?></p>

                <div class="hero-info-wrapper">
                    <div class="hero-info-box">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <span>Jatuh Tempo</span>
                            <h3><?= isset($data['tanggal_selesai']) ? tgl_indo($data['tanggal_selesai']) : '-'; ?></h3>
                        </div>
                    </div>
                    
                    <div class="hero-info-box">
                        <i class="bi bi-wallet2"></i>
                        <div>
                            <span>Tagihan Bulan Ini</span>
                            <h3>Rp<?= number_format($nominal_tagihan); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <i class="bi bi-wifi hero-wifi-icon"></i>
                
                <?php if ($t && strtolower($t['status_pembayaran']) == 'belum') : ?>
                    <a href="tagihan/bayar.php?id=<?= $t['id_transaksi']; ?>" class="hero-button">
                        Bayar Tagihan
                    </a>
                <?php else : ?>
                    <button class="hero-button disabled-btn" disabled>
                        Belum Ada Tagihan
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Riwayat Tagihan Terakhir</h3>
                <a href="tagihan/index.php" class="btn-orange-outline">Lihat Semua</a>
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
                    <?php if (mysqli_num_rows($riwayat) > 0) : ?>
                        <?php while ($r = mysqli_fetch_assoc($riwayat)) : ?>
                            <tr>
                                <td><?= $r['kode_invoice']; ?></td>
                                <td><?= date('F Y', strtotime($r['tahun_tagihan'] . '-' . $r['bulan_tagihan'] . '-01')); ?></td>
                                <td>Rp<?= number_format($r['jumlah_bayar']); ?></td>
                                <td>
                                    <span class="status-<?= (strtolower($r['status_pembayaran']) == 'lunas') ? 'active' : ((strtolower($r['status_pembayaran']) == 'menunggu') ? 'pending' : 'belum'); ?>">
                                        <?= ucfirst($r['status_pembayaran']); ?>
                                    </span>
                                </td>
                                <td><?= !empty($r['tanggal_bayar']) ? tgl_indo($r['tanggal_bayar']) : '-'; ?></td>
                                <td><a href="tagihan/detail.php?id=<?= $r['id_transaksi']; ?>" class="btn-detail">Detail</a></td>
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

</body>
</html>