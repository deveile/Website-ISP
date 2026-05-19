<<<<<<< HEAD
<?php
include '../../koneksi.php';

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

=======
<?php
include '../../koneksi.php';

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

>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
header("Location:index.php");