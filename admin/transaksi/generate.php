<?php
require_once __DIR__ . '/../../koneksi.php';

$bulan = date('n');
$tahun = date('Y');

/* ================= AMBIL CUSTOMER AKTIF ================= */
$customer = mysqli_query(
    $koneksi, 
    "SELECT * 
     FROM tb_customer 
     WHERE status_paket = 'Aktif'"
);

/* ================= LOOP CUSTOMER ================= */
while($c = mysqli_fetch_assoc($customer)){

    $id_customer = $c['id_customer'];

    /* ================= CEK TAGIHAN ================= */
    $cek = mysqli_query(
        $koneksi, 
        "SELECT * 
         FROM tb_transaksi 
         WHERE id_customer = '$id_customer' 
         AND bulan_tagihan = '$bulan' 
         AND tahun_tagihan = '$tahun'"
    );

    /* ================= JIKA BELUM ADA ================= */
    if(mysqli_num_rows($cek) == 0){

        /* ================= AMBIL DATA PAKET ================= */
        $paket = mysqli_query(
            $koneksi, 
            "SELECT * 
             FROM tb_paket 
             WHERE id_paket = '" . $c['id_paket'] . "'"
        );
        
        $p = mysqli_fetch_assoc($paket);
        $jumlah = $p['harga_paket'];

        /* ================= KODE INVOICE ================= */
        $invoice = "INV-" . $tahun . $bulan . "-" . $id_customer;

        /* ================= INSERT ================= */
        mysqli_query(
            $koneksi, 
            "INSERT INTO tb_transaksi (
                id_customer,
                kode_invoice,
                bulan_tagihan,
                tahun_tagihan,
                jumlah_bayar,
                status_pembayaran
            ) VALUES (
                '$id_customer',
                '$invoice',
                '$bulan',
                '$tahun',
                '$jumlah',
                'Belum Bayar'
            )"
        );
    }
}
