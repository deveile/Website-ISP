<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id_user = $_SESSION['id_user'];

$queryCustomer = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_customer
     WHERE id_user = '$id_user'"
);

$customer = mysqli_fetch_assoc($queryCustomer);

$id_customer = $customer['id_customer'];

$id_paket = $_POST['id_paket'];
$alamat   = $_POST['alamat'];
$catatan  = $_POST['catatan'];
$metode   = $_POST['metode_pembayaran'];

$bukti = $_FILES['bukti_pembayaran']['name'];
$tmp   = $_FILES['bukti_pembayaran']['tmp_name'];

$folder = "../../uploads/pembayaran/";

move_uploaded_file($tmp, $folder . $bukti);

mysqli_query(
    $koneksi,
    "INSERT INTO tb_pemasangan
    (
        id_customer,
        id_paket,
        alamat_pasang,
        tanggal_pasang,
        catatan,
        status_pemasangan,
        metode_pembayaran,
        bukti_pembayaran,
        status_pembayaran
    )
    VALUES
    (
        '$id_customer',
        '$id_paket',
        '$alamat',
        NOW(),
        '$catatan',
        'Menunggu Konfirmasi',
        '$metode',
        '$bukti',
        'Menunggu Konfirmasi'
    )"
);

header("Location: berhasil.php");