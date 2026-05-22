<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'customer') {
    header("Location: ../../auth/login.php");
    exit;
}

/* ================= AMBIL DATA PAKET ================= */
$id_paket = $_GET['id'];

$data_paket = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_paket 
     WHERE id_paket='$id_paket'"
);

$paket = mysqli_fetch_assoc($data_paket);

/* ================= AMBIL DATA CUSTOMER ================= */
$id_user = $_SESSION['id_user'];

$data_customer = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_customer 
     WHERE id_user='$id_user'"
);

$customer = mysqli_fetch_assoc($data_customer);

/* ================= SUBMIT ================= */
if (isset($_POST['submit'])) {

    $alamat  = htmlspecialchars($_POST['alamat']);
    $catatan = htmlspecialchars($_POST['catatan']);

    // Query diperbaiki: Menghapus kolom nama_customer, telepon_customer, email_customer, dan status_pembayaran
    // Nilai default status_pemasangan diset 'Pending' (Menunggu konfirmasi/pembayaran awal)
    $query_insert = mysqli_query(
        $koneksi,
        "INSERT INTO tb_pemasangan
        (
            id_customer,
            id_paket,
            tanggal_pengajuan,
            tanggal_pasang,
            alamat_pasang,
            status_pemasangan,
            catatan,
            created_at
        )
        VALUES
        (
            '".$customer['id_customer']."',
            '$id_paket',
            CURDATE(),
            NULL, 
            '$alamat',
            'Pending',
            '$catatan',
            NOW()
        )"
    );

    if (!$query_insert) {
        die("Gagal menyimpan data pengajuan pemasangan: " . mysqli_error($koneksi));
    }

    $id_pemasangan = mysqli_insert_id($koneksi);

    header("Location: pembayaran.php?id=$id_pemasangan");
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
                    <input 
                        type="text"
                        name="nama"
                        value="<?= $customer['nama_customer']; ?>"
                        disabled
                    >
                    <small style="color: #666;">* Data profil utama Anda</small>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input 
                        type="text"
                        name="telepon"
                        value="<?= $customer['telepon_customer']; ?>"
                        disabled
                    >
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email"
                        name="email"
                        value="<?= $customer['email_customer']; ?>"
                        disabled
                    >
                </div>

                <div class="form-group">
                    <label>Alamat Pemasangan</label>
                    <textarea 
                        name="alamat"
                        required
                    ><?= $customer['alamat_customer']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Catatan Tambahan</label>
                    <textarea 
                        name="catatan"
                        placeholder="Contoh: Pasang siang hari, warna kabel hitam, dll."
                    ></textarea>
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

                <button type="submit" name="submit">
                    Lanjut Pembayaran
                </button>
            </form>
        </div>

    </div>

</div>

</body>
</html>