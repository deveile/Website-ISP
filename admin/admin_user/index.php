<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$error   = '';
$success = '';

if (isset($_POST['submit'])) {

    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama_admin']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email_admin']));
    $password = $_POST['password'];

    if (empty($nama) || empty($username) || empty($password)) {
        $error = 'Semua field wajib diisi.';

    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';

    } else {

        $cek = mysqli_query($koneksi, "SELECT id_user FROM tb_user WHERE username = '$username' LIMIT 1");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Username '$username' sudah digunakan, pilih username lain.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            mysqli_begin_transaction($koneksi);

            $ok1 = mysqli_query($koneksi,
                "INSERT INTO tb_user (username, password, role)
                 VALUES ('$username', '$hash', 'admin')"
            );
            $id_user = mysqli_insert_id($koneksi);

            $ok2 = mysqli_query($koneksi,
                "INSERT INTO tb_admin (id_user, nama_admin, email_admin)
                 VALUES ('$id_user', '$nama', '$email')"
            );

            if ($ok1 && $ok2) {
                mysqli_commit($koneksi);
                $success = "Admin '$nama' berhasil ditambahkan.";
            } else {
                mysqli_rollback($koneksi);
                $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin</title>
    <link class="form-group" rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="../transaksi/index.php"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-person-plus"></i> Tambah Admin</a></li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar">
            <h1>Tambah Admin</h1>
            <p>Tambahkan akun admin baru</p>
        </div>

        <div class="form-wrapper-admin">
            <form method="POST" class="form-admin-card">
                <h2>Form Admin</h2>

                <?php if ($error): ?>
                    <div class="alert-box alert-error">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-box alert-success">
                        <i class="bi bi-check-circle me-1"></i>
                        <?= htmlspecialchars($success) ?>
                        &nbsp;·&nbsp; <a href="index.php">Lihat daftar admin</a>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_admin"
                           placeholder="Masukkan nama admin"
                           value="<?= htmlspecialchars($_POST['nama_admin'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           placeholder="Masukkan username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_admin"
                           placeholder="Masukkan email"
                           value="<?= htmlspecialchars($_POST['email_admin'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="passInput" placeholder="Masukkan password" required minlength="6">
                        <i class="bi bi-eye toggle-pass" id="togglePass"></i>
                    </div>
                    <div class="password-hint">* Minimal 6 karakter</div>
                </div>

                <button type="submit" name="submit" class="btn-orange">
                    <i class="bi bi-person-plus"></i> Tambah Admin
                </button>
            </form>
        </div>

    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-box-arrow-right"></i>
        </div>

        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar?</p>

        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

<script>
    // Toggle tampilkan/sembunyikan password (Sudah disesuaikan untuk tag ikon langsung)
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

</body>
</html>