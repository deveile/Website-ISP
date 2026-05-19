<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE id_paket='$id'");
$data = mysqli_fetch_assoc($query);

if(isset($_POST['submit'])){
    $nama      = htmlspecialchars($_POST['nama_paket']);
    $kecepatan = htmlspecialchars($_POST['kecepatan']);
    $harga     = htmlspecialchars($_POST['harga']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    mysqli_query($koneksi, "UPDATE tb_paket SET nama_paket='$nama', kecepatan='$kecepatan', harga='$harga', deskripsi='$deskripsi' WHERE id_paket='$id'");

    echo "<script>alert('Paket berhasil diupdate'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Paket</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-container">
    <div class="form-card">
        <h2>Edit Paket Internet</h2>
        <p>Ubah informasi paket internet.</p>

        <form method="POST">
            <input type="text" name="nama_paket" placeholder="Nama Paket" value="<?= $data['nama_paket']; ?>" required>
            <input type="text" name="kecepatan" placeholder="Kecepatan" value="<?= $data['kecepatan']; ?>" required>
            <input type="number" name="harga" placeholder="Harga" value="<?= $data['harga']; ?>" required>
            <textarea name="deskripsi" placeholder="Deskripsi Paket" required><?= $data['deskripsi']; ?></textarea>
            <button type="submit" name="submit">Update Paket</button>
        </form>
    </div>
</div>

</body>
</html>