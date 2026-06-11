<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query_customer = mysqli_query($koneksi, "SELECT id_customer FROM tb_customer WHERE id_user = '$id_user'");
$data_customer = mysqli_fetch_assoc($query_customer);

if ($data_customer) {
    $id_customer = $data_customer['id_customer'];
    $tanggal_sekarang = date('Y-m-d');

    mysqli_begin_transaction($koneksi);

    try {
        $update_langganan = mysqli_query($koneksi, "UPDATE tb_langganan 
                            SET status_langganan = 'berhenti', tanggal_selesai = '$tanggal_sekarang' 
                            WHERE id_customer = '$id_customer' AND status_langganan = 'aktif'");

        $update_status_cust = mysqli_query($koneksi, "UPDATE tb_customer 
                              SET status_customer = 'nonaktif' 
                              WHERE id_customer = '$id_customer'");

        mysqli_commit($koneksi);
        $_SESSION['sukses'] = "Anda telah berhasil berhenti berlangganan.";

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['gagal'] = "Gagal memproses permintaan berhenti berlangganan.";
    }
}

header("Location: index.php");
exit;
?>