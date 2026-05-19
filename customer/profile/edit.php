<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= DATA ================= */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tb_customer
     WHERE id_user = '$id_user'"
);

$data = mysqli_fetch_assoc($query);

/* ================= UPDATE ================= */

if (isset($_POST['simpan'])) {

    $nama    = $_POST['nama_customer'];
    $email   = $_POST['email_customer'];
    $telepon = $_POST['telepon_customer'];
    $alamat  = $_POST['alamat_customer'];

    mysqli_query(
        $koneksi,
        "UPDATE tb_customer SET
            nama_customer = '$nama',
            email_customer = '$email',
            telepon_customer = '$telepon',
            alamat_customer = '$alamat'
         WHERE id_user = '$id_user'"
    );

    echo "
    <script>
        alert('Profil berhasil diperbarui');
        window.location='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Edit Profil</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
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
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="../tagihan/index.php">
                    <i class="bi bi-receipt"></i>
                    Tagihan Saya
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i>
                    Paket Internet
                </a>
            </li>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-person"></i>
                    Profil
                </a>
            </li>
            <li>
                <a href="../../auth/logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Edit Profil</h1>
                <p>Perbarui informasi akun Anda</p>
            </div>
        </div>

        <!-- FORM -->
        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input 
                        type="text"
                        name="nama_customer"
                        value="<?= $data['nama_customer']; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email"
                        name="email_customer"
                        value="<?= $data['email_customer']; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input 
                        type="text"
                        name="telepon_customer"
                        value="<?= $data['telepon_customer']; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea 
                        name="alamat_customer"
                        required
                    ><?= $data['alamat_customer']; ?></textarea>
                </div>

                <div class="form-action">
                    <a 
                        href="index.php"
                        class="btn-cancel"
                    >
                        Batal
                    </a>
                    <button 
                        type="submit"
                        name="simpan"
                        class="btn-orange"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

</body>
</html>