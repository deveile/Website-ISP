<?php
require_once __DIR__ . '/../../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM tb_customer WHERE id_customer='$id'");

header("Location: index.php");