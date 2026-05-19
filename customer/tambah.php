<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

$paket = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");

/* ================= SIMPAN ================= */
if(isset($_POST['simpan'])){
    $nama     = $_POST['nama_customer'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email    = $_POST['email_customer'];
    $telepon  = $_POST['telepon_customer'];
    $alamat   = $_POST['alamat_customer'];
    $id_paket = $_POST['id_paket'];
    $status   = $_POST['status_paket'];
    $sumber   = "Offline";

    /* ================= CEK USERNAME ================= */
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username'");

    if(mysqli_num_rows($cek) > 0){
        echo "
        <script>
            alert('Username sudah digunakan');
            window.location='tambah.php';
        </script>
        ";
        exit;
    }

    /* ================= INSERT USER ================= */
    mysqli_query($koneksi, "INSERT INTO tb_user(username, password, role) VALUES('$username', '$password', 'customer')");

    /* ================= AMBIL ID USER ================= */
    $id_user = mysqli_insert_id($koneksi);

    /* ================= INSERT CUSTOMER ================= */
    mysqli_query($koneksi, "INSERT INTO tb_customer(id_user, nama_customer, email_customer, telepon_customer, alamat_customer, id_paket, status_paket, sumber_customer) VALUES('$id_user', '$nama', '$email', '$telepon', '$alamat', '$id_paket', '$status', '$sumber')");

    echo "
    <script>
        alert('Pelanggan berhasil ditambahkan');
        window.location='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="dashboard-layout">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> Data Pelanggan</a></li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="dashboard-content">
        <div class="topbar">
            <h1>Tambah Pelanggan</h1>
            <p>Tambah customer offline + buat akun login</p>
        </div>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_customer" required>
                </div>

                <div class="form-group">
                    <label>Username Login</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password Login</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_customer" required>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="telepon_customer" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat_customer" required></textarea>
                </div>

                <div class="form-group">
                    <label>Pilih Paket</label>
                    <select name="id_paket" required>
                        <?php while($p = mysqli_fetch_assoc($paket)) : ?>
                            <option value="<?= $p['id_paket']; ?>"><?= $p['nama_paket']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Paket</label>
                    <select name="status_paket">
                        <option value="Pending">Pending</option>
                        <option value="Aktif">Aktif</option>
                    </select>
                </div>

                <button type="submit" name="simpan">Simpan Pelanggan</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>