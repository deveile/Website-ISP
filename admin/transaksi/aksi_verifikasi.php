<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php"); exit;
}

$id_transaksi = (int)($_POST['id_transaksi'] ?? 0);
$aksi         = $_POST['aksi'] ?? '';
$alasan       = mysqli_real_escape_string($koneksi, trim($_POST['alasan'] ?? ''));

if (!$id_transaksi || !in_array($aksi, ['terima', 'tolak'])) {
    header("Location: index.php"); exit;
}

$trx = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM tb_transaksi WHERE id_transaksi = $id_transaksi LIMIT 1"));

if (!$trx) {
    header("Location: index.php"); exit;
}

if (strtolower($trx['status_pembayaran']) !== 'menunggu') {
    $pesan = urlencode('Transaksi ini sudah diproses sebelumnya.');
    header("Location: detail.php?id=$id_transaksi&pesan=$pesan&tipe=error");
    exit;
}

if ($aksi === 'terima') {
    $tanggal_bayar = date('Y-m-d');
    $ok = mysqli_query($koneksi, "
        UPDATE tb_transaksi SET
            status_pembayaran = 'lunas',
            tanggal_bayar     = '$tanggal_bayar'
        WHERE id_transaksi = $id_transaksi
    ");

    if ($ok) {
        $pesan = urlencode('Pembayaran berhasil diverifikasi. Status transaksi sekarang Lunas.');
        header("Location: detail.php?id=$id_transaksi&pesan=$pesan&tipe=sukses");
    } else {
        $pesan = urlencode('Gagal memverifikasi: ' . mysqli_error($koneksi));
        header("Location: detail.php?id=$id_transaksi&pesan=$pesan&tipe=error");
    }
    exit;

} elseif ($aksi === 'tolak') {
    $bukti_lama = $trx['bukti_pembayaran'] ?? '';

    $ok = mysqli_query($koneksi, "
        UPDATE tb_transaksi SET
            status_pembayaran  = 'belum_bayar',
            bukti_pembayaran   = NULL,
            metode_pembayaran  = NULL,
            tanggal_bayar      = NULL
        WHERE id_transaksi = $id_transaksi
    ");

    if ($ok) {
        if (!empty($bukti_lama)) {
            $file_path = __DIR__ . '/../../uploads/bukti/' . $bukti_lama;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $pesan = urlencode('Pembayaran ditolak. Status kembali ke Belum Bayar. Pelanggan perlu mengirim ulang bukti.');
        header("Location: detail.php?id=$id_transaksi&pesan=$pesan&tipe=sukses");
    } else {
        $pesan = urlencode('Gagal menolak pembayaran: ' . mysqli_error($koneksi));
        header("Location: detail.php?id=$id_transaksi&pesan=$pesan&tipe=error");
    }
    exit;
}

header("Location: index.php");
exit;