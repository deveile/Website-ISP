<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php"); exit;
}

$username = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

$query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' LIMIT 1");
$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['id_user']  = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role']     = $data['role'];

    $icon = 'success';
    $title = 'Login Berhasil';

    if ($data['role'] === 'admin') {
        $adm = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_admin WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']     = $adm['nama_admin'] ?? 'Admin';
        $_SESSION['id_admin'] = $adm['id_admin'] ?? null;

        // Keterangan: Fungsi otomatisasi tagihan sudah dihapus dari sini 
        // agar proses login admin menjadi sangat cepat.

        $text = 'Selamat datang Admin';
        $redirect = '/website-isp/admin/index.php';
        
    } elseif ($data['role'] === 'teknisi') {
        $tek = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_teknisi WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']       = $tek['nama_teknisi'] ?? 'Teknisi';
        $_SESSION['id_teknisi'] = $tek['id_teknisi'] ?? null;

        $text = 'Selamat datang Teknisi';
        $redirect = '/website-isp/teknisi/index.php'; 
        
    } else {
        $cust = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_customer WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']        = $cust['nama_customer'] ?? $data['username'];
        $_SESSION['id_customer'] = $cust['id_customer'] ?? null;

        $text = 'Selamat datang';
        $redirect = '/website-isp/customer/index.php';
    }
} else {
    $icon = 'error';
    $title = 'Login Gagal';
    $text = !$data ? 'Username tidak ditemukan' : 'Password salah';
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