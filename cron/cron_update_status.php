<?php
require_once __DIR__ . '/../koneksi.php';

$today = date('Y-m-d');

mysqli_query($koneksi, "UPDATE tb_langganan 
    SET status_langganan = 'suspend' 
    WHERE status_langganan = 'aktif' AND tanggal_selesai < '$today'");

$empat_hari_lalu = date('Y-m-d', strtotime('-4 days'));
mysqli_query($koneksi, "UPDATE tb_langganan 
    SET status_langganan = 'dicabut' 
    WHERE status_langganan = 'suspend' AND tanggal_selesai <= '$empat_hari_lalu'");

mysqli_query($koneksi, "UPDATE tb_transaksi t
    JOIN tb_langganan l ON t.id_langganan = l.id_langganan
    SET t.status_pembayaran = 'expired'
    WHERE l.status_langganan = 'dicabut' AND t.status_pembayaran = 'belum_bayar'");

echo "Sistem status berhasil diperbarui.";
?>