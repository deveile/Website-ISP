<?php
require_once __DIR__ . '/../koneksi.php';

$nama     = $_POST['nama_customer'] ?? '';
$username = $_POST['username'] ?? '';
$email    = $_POST['email_customer'] ?? '';
$telepon  = $_POST['telepon_customer'] ?? '';
$alamat   = $_POST['alamat_customer'] ?? '';
$password = $_POST['password'] ?? '';

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

if (mysqli_num_rows($cek) > 0) {
    $icon = 'error';
    $title = 'Oops...';
    $text = 'Username sudah digunakan';
    $redirect = 'register.php';
} else {
    $insert_user = mysqli_query($koneksi, "INSERT INTO tb_user (username, password, role) VALUES ('$username', '$password_hash', 'customer')");
    if (!$insert_user) die("Gagal insert user");

    $id_user = mysqli_insert_id($koneksi);
    $sumber_customer = "Online";

    $insert_customer = mysqli_query($koneksi, "INSERT INTO tb_customer (id_user, nama_customer, alamat_customer, telepon_customer, email_customer, sumber_customer, status_customer) VALUES ('$id_user', '$nama', '$alamat', '$telepon', '$email', '$sumber_customer', 'pending')");
    if (!$insert_customer) die("Gagal insert customer");

    $icon = 'success';
    $title = 'Berhasil!';
    $text = 'Register berhasil';
    $redirect = 'login.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: '<?= $icon; ?>',
    title: '<?= $title; ?>',
    text: '<?= $text; ?>',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = '<?= $redirect; ?>';
});
</script>
</body>
</html>