<?php
require_once __DIR__ . '/../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "
    UPDATE tb_paket
    SET status='nonaktif'
    WHERE id_paket='$id'

");

header("Location:index.php?success=nonaktif");
exit;