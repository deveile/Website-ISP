<?php
include '../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "

    DELETE FROM tb_paket
    WHERE id_paket='$id'

");

header("Location:index.php");