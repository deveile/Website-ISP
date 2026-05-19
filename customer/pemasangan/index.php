<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

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

    $nama    = htmlspecialchars($_POST['nama']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $email   = htmlspecialchars($_POST['email']);
    $alamat  = htmlspecialchars($_POST['alamat']);
    $catatan = htmlspecialchars($_POST['catatan']);

    mysqli_query(
        $koneksi,
        "INSERT INTO tb_pemasangan
        (
            id_customer,
            id_paket,
            nama_customer,
            telepon_customer,
            email_customer,
            alamat_pasang,
            catatan,
            status_pemasangan,
            status_pembayaran,
            tanggal_pasang,
            created_at
        )
        VALUES
        (
            '$customer[id_customer]',
            '$id_paket',
            '$nama',
            '$telepon',
            '$email',
            '$alamat',
            '$catatan',
            'Pending',
            'Belum Bayar',
            CURDATE(),
            NOW()
        )"
    );

    $id_pemasangan = mysqli_insert_id($koneksi);

    header("Location: pembayaran.php?id=$id_pemasangan");
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

        <!-- FORM -->
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
                        required
                    >
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input 
                        type="text"
                        name="telepon"
                        value="<?= $customer['telepon_customer']; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email"
                        name="email"
                        value="<?= $customer['email_customer']; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Alamat Pemasangan</label>
                    <textarea 
                        name="alamat"
                        required
                    ></textarea>
                </div>

                <div class="form-group">
                    <label>Catatan Tambahan</label>
                    <textarea 
                        name="catatan"
                    ></textarea>
                </div>

                   <!-- RINGKASAN -->
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