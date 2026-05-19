<<<<<<< HEAD
<?php
include '../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM tb_customer WHERE id_customer='$id'");

header("Location: index.php");
=======
<?php
include '../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM tb_customer WHERE id_customer='$id'");

header("Location: index.php");
>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
?>