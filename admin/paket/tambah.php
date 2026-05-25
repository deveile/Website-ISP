<?php
require_once __DIR__ . '/../../koneksi.php';

$nama       = $_POST['nama_paket'];
$harga      = $_POST['harga'];
$kecepatan  = $_POST['kecepatan'];
$deskripsi  = $_POST['deskripsi'];
$status     = 'aktif';

mysqli_query($koneksi, "
    INSERT INTO tb_paket (
        nama_paket, 
        harga, 
        kecepatan, 
        deskripsi, 
        status
    ) 
    VALUES (
        '$nama', 
        '$harga', 
        '$kecepatan', 
        '$deskripsi', 
        '$status'
    )
");

header("Location:index.php?success=tambah");