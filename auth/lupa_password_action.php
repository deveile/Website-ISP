<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

$show_alert = false;
$icon = '';
$title = '';
$text = '';
$redirect = '';

if (isset($_POST["recover"])) {
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));

    // Cari data customer berdasarkan email
    $sql = mysqli_query($koneksi, "SELECT tc.*, tu.username FROM tb_customer tc JOIN tb_user tu ON tc.id_user = tu.id_user WHERE tc.email_customer = '$email' LIMIT 1");
    $count = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if ($count <= 0) {
        $show_alert = true;
        $icon = 'error';
        $title = 'Oops...';
        $text = 'Email tidak ditemukan di sistem kami!';
        $redirect = 'lupa_password.php';
    } elseif ($fetch["status_customer"] == 'pending') {
        $show_alert = true;
        $icon = 'warning';
        $title = 'Verifikasi Dulu';
        $text = 'Akun Anda belum diverifikasi sejak pendaftaran. Silahkan cek email registrasi awal.';
        $redirect = 'login.php';
    } else {
        // Generate Token unik 50 karakter
        $token = bin2hex(random_bytes(25));

        // Simpan email dan token di session untuk divalidasi di halaman reset
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;

        // Load PHPMailer
        require "Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';


        // ==========================================
        $mail->Username = 'devdevv1864@gmail.com'; 
        $mail->Password = 'knbd cgxe ggmj ezir'; // 16 digit Sandi Aplikasi Google
        $mail->setFrom('devdevv1864@gmail.com', 'Reset Password - Anuwani');
        // ==========================================

        $mail->addAddress($email);
        $mail->isHTML(true);
        
        $mail->Subject = "Permintaan Reset Password Akun Anda";
        
        // Link mengarah ke reset_password.php dengan membawa parameter token
        $link_reset = "http://localhost/website-isp/auth/reset_password.php?token=" . $token;

        $mail->Body = "<p>Yth. Customer <b>" . $fetch['nama_customer'] . "</b>,</p>
                       <p>Kami menerima permintaan untuk mereset password akun Anda (Username: <b>" . $fetch['username'] . "</b>).</p>
                       <p>Silahkan klik tombol atau link di bawah ini untuk mengganti password Anda:</p>
                       <p><a href='$link_reset' style='background-color:#ff7a00; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset Password Saya</a></p>
                       <br>
                       <p>Atau copy link berikut ke browser Anda jika tombol tidak berfungsi:</p>
                       <p>$link_reset</p>
                       <br>
                       <p>Salam hangat,<br><b>Anuwani.net</b></p>";

        if (!$mail->send()) {
            $show_alert = true;
            $icon = 'error';
            $title = 'Gagal!';
            $text = 'Gagal mengirim email pemulihan. Coba lagi nanti.';
            $redirect = 'lupa_password.php';
        } else {
            $show_alert = true;
            $icon = 'success';
            $title = 'Email Terkirim!';
            $text = 'Link pemulihan password telah dikirim ke ' . $email;
            $redirect = 'login.php';
        }
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
<?php if ($show_alert): ?>
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
<?php endif; ?>
</body>
</html>