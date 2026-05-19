<<<<<<< HEAD
<?php
include '../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "

    DELETE FROM tb_paket
    WHERE id_paket='$id'

");

=======
<?php
include '../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "

    DELETE FROM tb_paket
    WHERE id_paket='$id'

");

>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
header("Location:index.php");