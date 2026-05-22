<?php
require_once __DIR__ . '/../../koneksi.php';

$bulan = date('n');
$tahun = date('Y');

/* ================= AMBIL CUSTOMER YANG LANGGANANNYA AKTIF (FIXED JOIN) ================= */
// Kita hubungkan tb_customer dengan tb_langganan untuk mengecek status_langganan, 
// serta tb_paket untuk mengambil nominal harga paketnya secara real-time.
$customer = mysqli_query(
    $koneksi, 
    "SELECT 
        tb_customer.id_customer,
        tb_langganan.id_langganan,
        tb_paket.harga
     FROM tb_customer 
     INNER JOIN tb_langganan ON tb_customer.id_customer = tb_langganan.id_customer
     INNER JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket
     WHERE LOWER(tb_langganan.status_langganan) = 'aktif'"
);

/* ================= LOOP CUSTOMER ================= */
while($c = mysqli_fetch_assoc($customer)){

    $id_customer = $c['id_customer'];
    $id_langganan = $c['id_langganan'];
    $jumlah = $c['harga']; // Mengambil kolom 'harga' dari tb_paket yang sudah di-join di atas

    /* ================= CEK APAKAH TAGIHAN BULAN INI SUDAH ADA ================= */
    $cek = mysqli_query(
        $koneksi, 
        "SELECT * FROM tb_transaksi 
         WHERE id_langganan = '$id_langganan' 
         AND bulan_tagihan = '$bulan' 
         AND tahun_tagihan = '$tahun'"
    );

    /* ================= JIKA BELUM ADA, GENERATE TAGIHAN BARU ================= */
    if(mysqli_num_rows($cek) == 0){

        /* ================= FORMAT KODE INVOICE ================= */
        $invoice = "INV-" . $tahun . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-" . $id_customer;

        /* ================= INSERT KE DATA TRANSAKSI ================= */
        mysqli_query(
            $koneksi, 
            "INSERT INTO tb_transaksi (
                id_langganan,
                kode_invoice,
                bulan_tagihan,
                tahun_tagihan,
                jumlah_bayar,
                status_pembayaran
            ) VALUES (
                '$id_langganan',
                '$invoice',
                '$bulan',
                '$tahun',
                '$jumlah',
                'belum'
            )"
        );
    }
}