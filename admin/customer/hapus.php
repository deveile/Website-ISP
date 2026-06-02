<?php
require_once __DIR__ . '/../../koneksi.php';

$id = $_GET['id'];

$customer = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT id_customer FROM tb_customer WHERE id_customer='$id'"
    )
);

mysqli_query(
    $koneksi,
    "DELETE t
     FROM tb_transaksi t
     JOIN tb_langganan l
     ON t.id_langganan = l.id_langganan
     WHERE l.id_customer='$id'"
);

mysqli_query(
    $koneksi,
    "DELETE FROM tb_langganan
     WHERE id_customer='$id'"
);

mysqli_query(
    $koneksi,
    "DELETE FROM tb_pemasangan
     WHERE id_customer='$id'"
);

mysqli_query(
    $koneksi,
    "DELETE FROM tb_customer
     WHERE id_customer='$id'"
);

header("Location: index.php");
exit;