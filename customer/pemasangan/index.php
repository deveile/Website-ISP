<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'customer') {
    header("Location: ../../auth/login.php");
    exit;
}

$id_paket = $_GET['id'];
$data_paket = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE id_paket='$id_paket'");
$paket = mysqli_fetch_assoc($data_paket);

$id_user = $_SESSION['id_user'];
$data_customer = mysqli_query($koneksi, "SELECT * FROM tb_customer WHERE id_user='$id_user'");
$customer = mysqli_fetch_assoc($data_customer);

if (isset($_POST['submit'])) {
    $alamat  = htmlspecialchars($_POST['alamat']);
    $catatan = htmlspecialchars($_POST['catatan']);

    $query_insert = mysqli_query($koneksi, "
        INSERT INTO tb_pemasangan (
            id_customer, 
            id_paket, 
            tanggal_pengajuan, 
            tanggal_pasang, 
            alamat_pasang, 
            status_pemasangan, 
            catatan, 
            created_at
        ) VALUES (
            '".$customer['id_customer']."', 
            '$id_paket', 
            CURDATE(), 
            NULL, 
            '$alamat', 
            'menunggu', 
            '$catatan',
             NOW()
        )
    ");

    if (!$query_insert) {
        die("Gagal menyimpan data pengajuan pemasangan: " . mysqli_error($koneksi));
    }

    mysqli_query($koneksi, "
    INSERT INTO tb_langganan (
        id_customer, 
        id_paket, 
        tanggal_mulai, 
        tanggal_selesai, 
        status_langganan
    ) VALUES (
        '" . $customer['id_customer'] . "', 
        '$id_paket', 
        NULL, 
        NULL, 
        'Pending'
    )
");

    $id_langganan = mysqli_insert_id($koneksi);

    $bulan = date('n');
    $tahun = date('Y');
    $kode_invoice = 'INV-' . date('Ym') . '-' . rand(100, 999);

    mysqli_query($koneksi, "
        INSERT INTO tb_transaksi (
            id_langganan, 
            kode_invoice, 
            bulan_tagihan, 
            tahun_tagihan, 
            jumlah_bayar, 
            status_pembayaran, 
            created_at
        ) VALUES (
            '$id_langganan', 
            '$kode_invoice', 
            '$bulan', 
            '$tahun', 
            '" . $paket['harga'] . "', 
            'Belum', NOW()
        )
    ");

    $id_transaksi = mysqli_insert_id($koneksi);

    header("Location: ../tagihan/bayar.php?id=$id_transaksi");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Pemasangan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
</style>
</head>
<body>

<div class="auth-container">
    <div class="pemasangan-layout">
        <div class="form-card">
            <h2>Informasi Pemasangan</h2>
            <p>Lengkapi data berikut untuk proses pemasangan internet.</p>

            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= $customer['nama_customer']; ?>" disabled>
                    <small style="color: #666;">* Data profil utama Anda</small>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input type="text" name="telepon" value="<?= $customer['telepon_customer']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $customer['email_customer']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Alamat Pemasangan</label>
                    <textarea name="alamat" required><?= $customer['alamat_customer']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Catatan Tambahan</label>
                    <textarea name="catatan" placeholder="Contoh: Pasang siang hari, dll."></textarea>
                </div>

                <div class="ringkasan-card">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="ringkasan-item">
                        <span>Paket</span>
                        <strong><?= $paket['nama_paket']; ?></strong>
                    </div>
                    <div class="ringkasan-item">
                        <span>Kecepatan</span>
                        <strong><?= $paket['kecepatan']; ?></strong>
                    </div>
                    <div class="ringkasan-item">
                        <span>Harga</span>
                        <strong>Rp <?= number_format($paket['harga']); ?></strong>
                    </div>
                    <div class="ringkasan-item">
                        <span>Status</span>
                        <strong class="status-belum">Belum Bayar</strong>
                    </div>
                </div>

                <button type="submit" name="submit">Lanjut Pembayaran</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>