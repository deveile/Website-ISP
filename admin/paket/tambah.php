<?php
require_once __DIR__ . '/../../koneksi.php';

$nama       = $_POST['nama_paket'];
$harga      = $_POST['harga'];
$kecepatan  = $_POST['kecepatan'];
$deskripsi = $_POST['deskripsi'];

mysqli_query($koneksi, "
    INSERT INTO tb_paket 
    VALUES(
        NULL, 
        '$nama', 
        '$harga', 
        '$kecepatan', 
        '$deskripsi'
    )
");
header("Location:index.php");