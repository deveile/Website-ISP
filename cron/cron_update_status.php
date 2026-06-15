<?php
// cron_update_status.php
require_once __DIR__ . '/../koneksi.php';

$today = date('Y-m-d');

// 1. Update ke 'suspend' untuk yang lewat jatuh tempo
mysqli_query($koneksi, "UPDATE tb_langganan 
    SET status_langganan = 'suspend' 
    WHERE status_langganan = 'aktif' AND tanggal_selesai < '$today'");

// 2. Update ke 'dicabut' untuk yang lewat 4 hari dari jatuh tempo
$empat_hari_lalu = date('Y-m-d', strtotime('-4 days'));
mysqli_query($koneksi, "UPDATE tb_langganan 
    SET status_langganan = 'dicabut' 
    WHERE status_langganan = 'suspend' AND tanggal_selesai <= '$empat_hari_lalu'");

// 3. Update transaksi ke 'expired'
mysqli_query($koneksi, "UPDATE tb_transaksi t
    JOIN tb_langganan l ON t.id_langganan = l.id_langganan
    SET t.status_pembayaran = 'expired'
    WHERE l.status_langganan = 'dicabut' AND t.status_pembayaran = 'belum_bayar'");

echo "Sistem status berhasil diperbarui.";
?>