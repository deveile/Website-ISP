<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

/* ================= AMBIL DATA ================= */
$username = $_POST['username'];
$password = $_POST['password'];

/* ================= CEK USER ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT *
    FROM tb_user
    WHERE username = '$username'"
);

$data = mysqli_fetch_assoc($query);

/* ================= JIKA USER DITEMUKAN ================= */
if ($data) {

    /* ================= CEK PASSWORD ================= */
    if (password_verify($password, $data['password'])) {

        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        /* ================= LOGIN ADMIN ================= */
        if ($data['role'] == 'admin') {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Berhasil</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'Login Berhasil',
    text: 'Selamat datang Admin',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = '/isp_projek/admin/index.php';
});
</script>

</body>
</html>
<?php
exit;
        }

        /* ================= LOGIN CUSTOMER ================= */
        else {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Berhasil</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'Login Berhasil',
    text: 'Selamat datang',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = '/isp_projek/customer/index.php';
});
</script>

</body>
</html>
<?php
exit;
        }

    }

    /* ================= PASSWORD SALAH ================= */
    else {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Password Salah</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: 'Password salah',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = 'login.php';
});
</script>

</body>
</html>
<?php
exit;
    }

}

/* ================= USERNAME TIDAK DITEMUKAN ================= */
else {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Username Tidak Ditemukan</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: 'Username tidak ditemukan',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = 'login.php';
});
</script>

</body>
</html>
<?php
exit;
}
?>