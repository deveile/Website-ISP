<?php 
session_start(); 
require_once __DIR__ . '/../koneksi.php';

// Proteksi halaman: Jika tidak ada session OTP, tendang kembali ke register
if (!isset($_SESSION['otp']) || !isset($_SESSION['mail'])) {
    header("Location: register.php");
    exit();
}

$show_alert = false;
$icon = '';
$title = '';
$text = '';
$redirect = '';

if (isset($_POST["verify"])) {
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];
    $otp_code = $_POST['otp_code'];

    if ($otp != $otp_code) {
        $show_alert = true;
        $icon = 'error';
        $title = 'Oops...';
        $text = 'Kode OTP yang Anda masukkan salah!';
        $redirect = 'verification.php';
    } else {
        // Update status_customer menjadi 'aktif' (atau sesuaikan dengan enum database-mu, misal 'active' atau 'aktif')
        $query_update = "UPDATE tb_customer SET status_customer = 'aktif' WHERE email_customer = '$email'";
        
        if (mysqli_query($koneksi, $query_update)) {
            // Hapus session OTP setelah berhasil verifikasi agar tidak bisa digunakan lagi
            unset($_SESSION['otp']);
            unset($_SESSION['mail']);

            $show_alert = true;
            $icon = 'success';
            $title = 'Verifikasi Berhasil!';
            $text = 'Akun Anda telah aktif, silahkan login.';
            $redirect = 'login.php';
        } else {
            $show_alert = true;
            $icon = 'error';
            $title = 'Gagal!';
            $text = 'Terjadi kesalahan saat mengaktifkan akun.';
            $redirect = 'verification.php';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun | Anuwani.net</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png"> 
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="auth-container">
        <form action="" method="POST" class="auth-card">
            <h2>Verifikasi Akun</h2>
            <p>Masukkan kode OTP 6-digit yang dikirim ke email Anda</p>
            
            <input type="text" name="otp_code" placeholder="Kode OTP" maxlength="6" required autofocus 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '');">

            <button type="submit" name="verify">Verifikasi</button>

            <div class="auth-link">
                Salah email? <a href="register.php">Kembali ke Register</a>
            </div>
        </form>
    </div>

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