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

$update_sukses = false;

if(isset($_POST['submit'])){
    $nama      = htmlspecialchars($_POST['nama_paket']);
    $kecepatan = htmlspecialchars($_POST['kecepatan']);
    $harga     = htmlspecialchars($_POST['harga']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    $update = mysqli_query($koneksi, "UPDATE tb_paket SET nama_paket='$nama', kecepatan='$kecepatan', harga='$harga', deskripsi='$deskripsi' WHERE id_paket='$id'");

    if($update) {
        $update_sukses = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Paket</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body>

<div class="auth-container">
    <div class="form-card">
        <h2>Edit Paket Internet</h2>
        <p>Ubah informasi paket internet.</p>

        <form method="POST">
            <div class="form-group">
                <label>Nama Paket</label>
                <input type="text" name="nama_paket" placeholder="Nama Paket" value="<?= $data['nama_paket']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kecepatan</label>
                <input type="text" name="kecepatan" placeholder="Kecepatan" value="<?= $data['kecepatan']; ?>" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" placeholder="Harga" value="<?= $data['harga']; ?>" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Paket</label>
                <textarea name="deskripsi" placeholder="Deskripsi Paket" required><?= $data['deskripsi']; ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn-orange">Update Paket</button>
        </form>
    </div>
</div>

<div id="updateSuccessModal" class="update-modal">
    <div class="update-modal-content">
        <div class="update-success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h2>Update Berhasil!</h2>
        <p>Data paket internet telah berhasil diperbarui di dalam sistem.</p>
        <div class="update-modal-action">
            <button onclick="closeUpdateModal()" class="btn-confirm-modal">Kembali</button>
        </div>
    </div>
</div>

<?php
if ($update_sukses) {
    echo "<script>showUpdateModal();</script>";
}
?>

</body>
</html>