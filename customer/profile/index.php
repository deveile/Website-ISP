<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id_user = $_SESSION['id_user'];

$query = mysqli_query(
    $koneksi,
    "SELECT 
        tb_customer.*,
        tb_paket.nama_paket,
        tb_paket.kecepatan,
        tb_user.username
     FROM tb_customer
     LEFT JOIN tb_paket ON tb_customer.id_paket = tb_paket.id_paket
     LEFT JOIN tb_user ON tb_customer.id_user = tb_user.id_user
     WHERE tb_customer.id_user = '$id_user'"
);

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Profil Customer</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
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
                    <a href="../tagihan/index.php">
                        <i class="bi bi-receipt"></i> Tagihan Saya
                    </a>
                </li>

                <li>
                    <a href="../paket/index.php">
                        <i class="bi bi-wifi"></i> Paket Internet
                    </a>
                </li>

                <li>
                    <a href="index.php" class="active">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>

                <li>
                    <a href="../../auth/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>

            </ul>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Profil Customer</h1>
                <p>Informasi akun customer</p>
            </div>
        </div>

        <!-- ================= PROFILE CARD ================= -->
        <div class="profile-customer-card">

            <div class="profile-customer-header">

                <div class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div>
                    <h2>
                        <?= $data['nama_customer']; ?>
                    </h2>
                    <p>
                        @<?= $data['username']; ?>
                    </p>
                </div>

            </div>

            <!-- ================= INFO GRID ================= -->
            <div class="profile-info-grid">

                <div class="profile-info-item">
                    <span>Paket Aktif</span>
                    <strong>
                        <?= $data['nama_paket'] ?? '-'; ?>
                    </strong>
                </div>

                <div class="profile-info-item">
                    <span>Kecepatan</span>
                    <strong>
                        <?= $data['kecepatan'] ?? '-'; ?>
                    </strong>
                </div>

            </div>

            <!-- ================= DETAIL ================= -->
            <div class="profile-detail-list">

                <div class="profile-detail-item">
                    <span>Email</span>
                    <strong>
                        <?= $data['email_customer']; ?>
                    </strong>
                </div>

                <div class="profile-detail-item">
                    <span>Telepon</span>
                    <strong>
                        <?= $data['telepon_customer']; ?>
                    </strong>
                </div>

                <div class="profile-detail-item">
                    <span>Alamat</span>
                    <strong>
                        <?= $data['alamat_customer']; ?>
                    </strong>
                </div>

            </div>

            <!-- ================= BUTTON ================= -->
            <div class="profile-action">
                <a 
                    href="edit.php"
                    class="btn-orange"
                >
                    Edit Profil
                </a>
            </div>

        </div>

    </div>

</div>

</body>
</html>