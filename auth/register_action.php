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
$cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

if(mysqli_num_rows($cek) > 0){
    echo "
    <script>
        alert('Username sudah digunakan');
        window.location='register.php';
    </script>
    ";
    exit;
}

/* ================= INSERT USER ================= */
mysqli_query($koneksi, "INSERT INTO tb_user(username, password, role) VALUES('$username', '$password_hash', 'customer')");

/* ================= AMBIL ID USER ================= */
$id_user = mysqli_insert_id($koneksi);

/* ================= SUMBER CUSTOMER ================= */
$sumber_customer = "Online";

/* ================= INSERT CUSTOMER ================= */
mysqli_query($koneksi, "INSERT INTO tb_customer(id_user, nama_customer, email_customer, telepon_customer, alamat_customer, id_paket, status_paket, sumber_customer) VALUES('$id_user', '$nama', '$email', '$telepon', '$alamat', NULL, 'Pending', '$sumber_customer')");

/* ================= SUCCESS ================= */
echo "
<script>
    alert('Register berhasil');
    window.location='login.php';
</script>
";
?>