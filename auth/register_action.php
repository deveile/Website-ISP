<?php
require_once __DIR__ . '/../koneksi.php';

/* ================= AMBIL DATA ================= */
$nama     = $_POST['nama_customer'];
$username = $_POST['username'];
$email    = $_POST['email_customer'];
$telepon  = $_POST['telepon_customer'];
$alamat   = $_POST['alamat_customer'];
$password = $_POST['password'];

/* ================= HASH PASSWORD ================= */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* ================= CEK USERNAME ================= */
$cek = mysqli_query(
    $koneksi,
    "SELECT *
    FROM tb_user
    WHERE username = '$username'"
);

/* ================= JIKA USERNAME SUDAH ADA ================= */
if (mysqli_num_rows($cek) > 0) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Gagal</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: 'Username sudah digunakan',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = 'register.php';
});
</script>

</body>
</html>
<?php
exit;
}

/* ================= INSERT USER ================= */
$insert_user = mysqli_query(
    $koneksi,
    "INSERT INTO tb_user
    (
        username,
        password,
        role
    )
    VALUES
    (
        '$username',
        '$password_hash',
        'customer'
    )"
);

/* ================= CEK INSERT USER ================= */
if (!$insert_user) {
    die("Gagal insert user");
}

/* ================= AMBIL ID USER ================= */
$id_user = mysqli_insert_id($koneksi);

/* ================= SUMBER CUSTOMER ================= */
$sumber_customer = "Online";

/* ================= INSERT CUSTOMER ================= */
$insert_customer = mysqli_query(
    $koneksi,
    "INSERT INTO tb_customer
    (
        id_user,
        nama_customer,
        alamat_customer,
        telepon_customer,
        email_customer,
        sumber_customer,
        status_customer
    )
    VALUES
    (
        '$id_user',
        '$nama',
        '$alamat',
        '$telepon',
        '$email',
        '$sumber_customer',
        'pending'
    )"
);

/* ================= CEK INSERT CUSTOMER ================= */
if (!$insert_customer) {
    die("Gagal insert customer");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Berhasil</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Register berhasil',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = 'login.php';
});
</script>

</body>
</html>