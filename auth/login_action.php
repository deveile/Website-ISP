<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    if (password_verify($password, $data['password'])) {
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        $icon = 'success';
        $title = 'Login Berhasil';
        $text = ($data['role'] == 'admin') ? 'Selamat datang Admin' : 'Selamat datang';
        $redirect = ($data['role'] == 'admin') ? '/website-isp/admin/index.php' : '/website-isp/customer/index.php';
    } else {
        $icon = 'error';
        $title = 'Login Gagal';
        $text = 'Password salah';
        $redirect = 'login.php';
    }
} else {
    $icon = 'error';
    $title = 'Login Gagal';
    $text = 'Username tidak ditemukan';
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