<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_POST['submit'])) {

    $nama     = htmlspecialchars($_POST['nama_admin']);
    $username = htmlspecialchars($_POST['username']);
    $email    = htmlspecialchars($_POST['email_admin']);
    $password = md5($_POST['password']);

    // cek username
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM tb_user 
         WHERE username='$username'"
    );

    if (mysqli_num_rows($cek) > 0) {

        echo "
        <script>
            alert('Username sudah digunakan');
        </script>
        ";

    } else {

        // insert tb_user
        mysqli_query(
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
                '$password',
                'admin'
            )"
        );

        // ambil id user terakhir
        $id_user = mysqli_insert_id($koneksi);

        // insert tb_admin
        mysqli_query(
            $koneksi,
            "INSERT INTO tb_admin
            (
                id_user,
                nama_admin,
                email_admin
            )
            VALUES
            (
                '$id_user',
                '$nama',
                '$email'
            )"
        );

        echo "
        <script>
            alert('Admin berhasil ditambahkan');
            window.location='index.php';
        </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Tambah Admin</title>

    <link rel="icon" type="image/png" href="../../assets/images/logo.png"> 
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">

        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i> Kelola Paket
                </a>
            </li>

            <li>
                <a href="../customer/index.php">
                    <i class="bi bi-people"></i> Data Pelanggan
                </a>
            </li>

            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> Data Transaksi
                </a>
            </li>

            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-person-plus"></i> Tambah Admin
                </a>
            </li>

            <li>
                <a href="../../auth/logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <h1>Tambah Admin</h1>
            <p>Tambahkan akun admin baru</p>
        </div>

        <!-- ================= FORM ================= -->
        <div class="form-wrapper-admin">

            <form method="POST" class="form-admin-card">
                <h2>Form Admin</h2>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input 
                        type="text"
                        name="nama_admin"
                        placeholder="Masukkan nama admin"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input 
                        type="text"
                        name="username"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email"
                        name="email_admin"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input 
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button 
                    type="submit"
                    name="submit"
                    class="btn-orange"
                >
                    Tambah Admin
                </button>
            </form>

        </div>

    </div>

</div>

</body>
</html>