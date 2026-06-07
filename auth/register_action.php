<?php
// Memulai session untuk menyimpan OTP dan Email secara sementara
session_start();

require_once __DIR__ . '/../koneksi.php';

$nama     = $_POST['nama_customer'] ?? '';
$username = $_POST['username'] ?? '';
$email    = $_POST['email_customer'] ?? '';
$telepon  = $_POST['telepon_customer'] ?? '';

if (!preg_match('/^[0-9]{10,12}$/', $telepon)) {
    die("Nomor telepon tidak valid. Harus terdiri dari 10-12 digit angka.");
}
$alamat   = $_POST['alamat_customer'] ?? '';
$password = $_POST['password'] ?? '';

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// 1. Cek apakah Username SUDAH DIGUNAKAN
$cek_username = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

// 2. Cek apakah Email SUDAH DIGUNAKAN (Tambahan validasi agar tidak dobel email)
$cek_email = mysqli_query($koneksi, "SELECT * FROM tb_customer WHERE email_customer = '$email'");

if (mysqli_num_rows($cek_username) > 0) {
    $icon = 'error';
    $title = 'Oops...';
    $text = 'Username sudah digunakan';
    $redirect = 'register.php';
} elseif (mysqli_num_rows($cek_email) > 0) {
    $icon = 'error';
    $title = 'Oops...';
    $text = 'Email sudah terdaftar';
    $redirect = 'register.php';
} else {
    // Generate kode OTP (6 digit angka acak)
    $otp = rand(100000, 999999);

    // Kirim Email OTP menggunakan PHPMailer terlebih dahulu sebelum insert database
    // Sesuaikan path ke file PHPMailerAutoload.php milikmu
    require "Mail/phpmailer/PHPMailerAutoload.php"; 
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';

 
    // ==========================================
    $mail->Username = 'devdevv1864@gmail.com'; // Akun Gmail pengirim
    $mail->Password = 'knbd cgxe ggmj ezir'; // Password Aplikasi Gmail (App Password)
    $mail->setFrom('devdevv1864@gmail.com', 'OTP Verification');
    // ==========================================

    $mail->addAddress($email); // Mengirim ke email inputan customer

    $mail->isHTML(true);
    $mail->Subject = "Kode Verifikasi OTP Anda";
    $mail->Body = "<p>Halo <b>$nama</b>,</p>
                   <h3>Kode OTP verifikasi Anda adalah: <span style='color:blue;'>$otp</span></h3>
                   <p>Jangan bagikan kode ini kepada siapapun.</p>
                   <br>
                   <p>Salam hangat,<br><b>Anuwani.net</b></p>";

    // Jika Email GAGAL dikirim
    if (!$mail->send()) {
        $icon = 'error';
        $title = 'Gagal!';
        $text = 'Gagal mengirimkan kode OTP ke email. Pastikan email valid.';
        $redirect = 'register.php';
    } else {
        // Jika Email BERHASIL dikirim, baru kita masukkan data ke database dengan status 'pending'
        
        // Simpan OTP dan Email ke session untuk divalidasi nanti di verification.php
        $_SESSION['otp']  = $otp;
        $_SESSION['mail'] = $email;

        // Insert ke tabel user
        $insert_user = mysqli_query($koneksi, "INSERT INTO tb_user (username, password, role) VALUES ('$username', '$password_hash', 'customer')");
        if (!$insert_user) die("Gagal insert user");

        $id_user = mysqli_insert_id($koneksi);
        $sumber_customer = "Online";

        // Insert ke tabel customer dengan status_customer 'pending' (menunggu verifikasi OTP)
        $insert_customer = mysqli_query($koneksi, "INSERT INTO tb_customer (id_user, nama_customer, alamat_customer, telepon_customer, email_customer, sumber_customer, status_customer) VALUES ('$id_user', '$nama', '$alamat', '$telepon', '$email', '$sumber_customer', 'pending')");
        if (!$insert_customer) die("Gagal insert customer");

        $icon = 'success';
        $title = 'Berhasil!';
        $text = 'Register berhasil, kode OTP telah dikirim ke ' . $email;
        $redirect = 'verification.php'; // Diarahkan ke halaman verifikasi OTP
    }
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