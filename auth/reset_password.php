<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Validasi Token dari URL dengan Token di Session
$token_url = $_GET['token'] ?? '';
$token_session = $_SESSION['reset_token'] ?? '';
$email_session = $_SESSION['reset_email'] ?? '';

if (empty($token_url) || $token_url !== $token_session || empty($email_session)) {
    die("Akses ditolak. Token reset tidak valid atau sudah kedaluwarsa.");
}

$show_alert = false;
$icon = '';
$title = '';
$text = '';
$redirect = '';

if (isset($_POST["reset"])) {
    $password_baru = $_POST["password"];
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    // Ambil id_user terlebih dahulu dari tb_customer berdasarkan email session
    $cust = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_user FROM tb_customer WHERE email_customer = '$email_session' LIMIT 1"));
    
    if ($cust) {
        $id_user = $cust['id_user'];

        // Update password baru di tb_user
        $update = mysqli_query($koneksi, "UPDATE tb_user SET password = '$password_hash' WHERE id_user = '$id_user'");

        if ($update) {
            // Hapus session reset agar token hangus dan aman
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_email']);

            $show_alert = true;
            $icon = 'success';
            $title = 'Berhasil!';
            $text = 'Password Anda telah berhasil diperbarui. Silahkan login kembali.';
            $redirect = 'login.php';
        } else {
            $show_alert = true;
            $icon = 'error';
            $title = 'Gagal!';
            $text = 'Gagal memperbarui password database.';
            $redirect = 'reset_password.php?token=' . $token_url;
        }
    } else {
        $show_alert = true;
        $icon = 'error';
        $title = 'Gagal!';
        $text = 'Data user tidak ditemukan.';
        $redirect = 'login.php';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Anuwani.net</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png"> 
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="auth-container">
        <form action="" method="POST" class="auth-card">
            <h2>Password Baru</h2>
            <p>Silahkan masukkan password baru untuk akun Anda</p>
            
            <div class="password-wrapper" style="margin-bottom: 20px !important;">
                <input type="password" name="password" id="passInput" placeholder="Password Baru" required autofocus>
                <i class="bi bi-eye toggle-pass" id="togglePass"></i>
            </div>

            <button type="submit" name="reset">Perbarui Password</button>
        </form>
    </div>

    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            const inp = document.getElementById('passInput');
            if (inp.type === 'password') {
                inp.type = 'text';
                this.className = 'bi bi-eye-slash toggle-pass';
            } else {
                inp.type = 'password';
                this.className = 'bi bi-eye toggle-pass';
            }
        });
    </script>

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