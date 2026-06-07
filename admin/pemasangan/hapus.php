<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: index.php");
    exit;
}

// Cek data pemasangan
$cek = mysqli_query(
    $koneksi,
    "SELECT id_pemasangan
     FROM tb_pemasangan
     WHERE id_pemasangan = '$id'"
);

if (mysqli_num_rows($cek) == 0) {
    header("Location: index.php");
    exit;
}

// Hapus pemasangan
mysqli_query(
    $koneksi,
    "DELETE FROM tb_pemasangan
     WHERE id_pemasangan = '$id'"
);

header("Location: index.php");
exit;