<<<<<<< HEAD
<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "isp_projek";

$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if(!$koneksi){
    die(
        "Koneksi database gagal : "
        . mysqli_connect_error()
    );

}
=======
<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "isp_projek";

$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if(!$koneksi){
    die(
        "Koneksi database gagal : "
        . mysqli_connect_error()
    );

}
>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
?>