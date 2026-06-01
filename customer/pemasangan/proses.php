<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

$queryCustomer = mysqli_query($koneksi, 
    "SELECT * FROM tb_customer WHERE id_user = '$id_user'"
);

$customer = mysqli_fetch_assoc($queryCustomer);
$id_customer = $customer['id_customer'];

if (isset($_GET['aksi']) && $_GET['aksi'] === 'upgrade') {
    $id_paket_tujuan = (int)($_GET['id_paket'] ?? 0);
    $id_langganan    = (int)($_GET['id_langganan'] ?? 0);

    if (!$id_paket_tujuan || !$id_langganan) {
        header("Location: ../paket/index.php"); exit;
    }

    $cek_transaksi = mysqli_query($koneksi, "
        SELECT id_transaksi FROM tb_transaksi 
        WHERE id_langganan = '$id_langganan' 
          AND jenis_transaksi = 'upgrade' 
          AND status_pembayaran IN ('belum_bayar','menunggu_verifikasi') 
        LIMIT 1
    ");

    if (mysqli_num_rows($cek_transaksi) > 0) {
        $transaksi_lama = mysqli_fetch_assoc($cek_transaksi);
        header("Location: ../tagihan/bayar.php?id=" . $transaksi_lama['id_transaksi']);
        exit;
    }

    $q_paket = mysqli_query($koneksi, 
        "SELECT harga FROM tb_paket WHERE id_paket = '$id_paket_tujuan'"
    );

    $paket_baru = mysqli_fetch_assoc($q_paket);
    if (!$paket_baru) {
        header("Location: ../paket/index.php"); exit;
    }

    $total_bayar = $paket_baru['harga'];
    $bulan = date('n');
    $tahun = date('Y');
    $kode_invoice = "INV-UPG-" . time();

    $sql_insert = "
        INSERT INTO tb_transaksi (
            id_langganan, id_paket_baru, kode_invoice, 
            bulan_tagihan, tahun_tagihan, jumlah_bayar, 
            status_pembayaran, jenis_transaksi
        ) VALUES (
            '$id_langganan', '$id_paket_tujuan', '$kode_invoice', 
            '$bulan', '$tahun', '$total_bayar', 
            'belum_bayar', 'upgrade'
        )
    ";

    if (mysqli_query($koneksi, $sql_insert)) {
        $id_transaksi_baru = mysqli_insert_id($koneksi);
        header("Location: ../tagihan/bayar.php?id=$id_transaksi_baru"); exit;
    } else {
        echo "<script>
            alert('Gagal memprocess upgrade paket');
            window.location='../paket/index.php';
        </script>";
        exit;
    }
}

$id_paket = $_POST['id_paket'];
$alamat   = $_POST['alamat'];
$catatan  = $_POST['catatan'];
$metode   = $_POST['metode_pembayaran'];

$bukti = $_FILES['bukti_pembayaran']['name'];
$tmp   = $_FILES['bukti_pembayaran']['tmp_name'];
$folder = "../../uploads/pembayaran/";

move_uploaded_file($tmp, $folder . $bukti);

mysqli_query($koneksi, "
    INSERT INTO tb_pemasangan (
        id_customer, id_paket, alamat_pasang, 
        tanggal_pasang, catatan, status_pemasangan, 
        metode_pembayaran, bukti_pembayaran, status_pembayaran
    ) VALUES (
        '$id_customer', '$id_paket', '$alamat', 
        NOW(), '$catatan', 'Menunggu Konfirmasi', 
        '$metode', '$bukti', 'Menunggu Konfirmasi'
    )
");

header("Location: berhasil.php");
?>