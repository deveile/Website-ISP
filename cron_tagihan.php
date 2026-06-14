<?php
// 1. KONEKSI DATABASE
require_once __DIR__ . '/koneksi.php'; // Menyesuaikan path karena file ada di dalam folder 'admin'

// 2. SET TIMEZONE INDONESIA
date_default_timezone_set('Asia/Jakarta');

$bulan = (int)date('n'); // Bulan berjalan (1-12)
$tahun = (int)date('Y'); // Tahun berjalan (Format 4 digit)

echo "<h3>⏳ Memulai Proses Generate Tagihan Otomatis Bulanan...</h3>";

// 3. SINGLE QUERY SAKTI (Mendukung Aktif & Suspend, Aman dari Error ENUM)
$query_generate = "
    INSERT INTO tb_transaksi (
        id_langganan, kode_invoice, bulan_tagihan, tahun_tagihan, 
        jumlah_bayar, status_pembayaran, metode_pembayaran, jenis_transaksi, created_at
    )
    SELECT 
        tl.id_langganan, 
        CONCAT('INV-', '$tahun', LPAD('$bulan', 2, '0'), '-', tc.id_customer) AS kode_invoice,
        $bulan AS bulan_tagihan,
        $tahun AS tahun_tagihan,
        tp.harga AS jumlah_bayar,
        'belum_bayar' AS status_pembayaran,
        'transfer' AS metode_pembayaran, -- Nilai awal default (placeholder)
        'perpanjang' AS jenis_transaksi,
        NOW() AS created_at
    FROM tb_langganan tl
    INNER JOIN tb_paket tp ON tl.id_paket = tp.id_paket
    INNER JOIN tb_customer tc ON tl.id_customer = tc.id_customer
    LEFT JOIN tb_transaksi tt ON tl.id_langganan = tt.id_langganan 
        AND tt.bulan_tagihan = $bulan 
        AND tt.tahun_tagihan = $tahun
    WHERE LOWER(tl.status_langganan) IN ('aktif', 'suspend') -- FIX: Mengcover status aktif dan suspend
      AND tt.id_transaksi IS NULL
";

$eksekusi = mysqli_query($koneksi, $query_generate);

if ($eksekusi) {
    $jumlah_terbuat = mysqli_affected_rows($koneksi);
    echo "<p style='color: green; font-weight: bold;'>🎉 Sukses! Berhasil menghasilkan {$jumlah_terbuat} tagihan baru (Aktif & Suspend) untuk bulan ini.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Gagal mengeksekusi sistem otomatis: " . mysqli_error($koneksi) . "</p>";
}
?>