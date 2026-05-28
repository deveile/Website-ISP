<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "
    SELECT 
        tb_customer.*,
        tb_user.username,
        tb_langganan.id_langganan,
        tb_langganan.status_langganan,
        tb_paket.nama_paket,
        tb_paket.kecepatan
    FROM tb_customer
    LEFT JOIN tb_user ON tb_customer.id_user = tb_user.id_user
    LEFT JOIN tb_langganan ON tb_customer.id_customer = tb_langganan.id_customer
    LEFT JOIN tb_paket ON tb_langganan.id_paket = tb_paket.id_paket
    WHERE tb_customer.id_user = '$id_user'
    ORDER BY tb_langganan.id_langganan DESC
    LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data customer tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
        .status-active {
    background: #dcfce7;
    color: #15803d;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-block;
}

.status-suspend {
    background: #fef3c7;
    color: #b45309;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-block;
}

.status-stop {
    background: #fee2e2;
    color: #b91c1c;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-block;
}

.status-pending {
    background: #e5e7eb;
    color: #374151;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-block;
}
    </style>
</head>
<body>
    <?php if (isset($_GET['update']) && $_GET['update'] == 'sukses') : ?>
    <div class="success-popup" id="successPopup">
        <i class="bi bi-check-circle-fill"></i>
        <span>Profile berhasil diperbarui</span>
    </div>

    <style>
        .success-popup {
            position: fixed;
            top: 25px;
            right: 25px;
            background: #22c55e;
            color: white;
            padding: 14px 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            z-index: 9999;
            box-shadow: 0 8px 20px rgba(34, 197, 94, .25);
            animation: slideIn .3s ease;
        }

        .success-popup i {
            font-size: 20px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

    <script>
        setTimeout(() => {
            const popup = document.getElementById('successPopup');
            if (popup) {
                popup.style.display = 'none';
            }
        }, 3000);
    </script>
<?php endif; ?>
<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../tagihan/index.php"><i class="bi bi-receipt"></i> Tagihan Saya</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Paket Internet</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-person"></i> Profile</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Profil Customer</h1>
                <p>Informasi akun customer</p>
            </div>
        </div>

        <div class="profile-customer-card">
            <div class="profile-customer-header">
                <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
                <div>
                    <h2><?= $data['nama_customer']; ?></h2>
                    <p>@<?= $data['username']; ?></p>
                </div>
            </div>

            <div class="profile-info-grid">
                <div class="profile-info-item">
                    <span>Paket Aktif</span>
                    <strong><?= $data['nama_paket'] ? $data['nama_paket'] : 'Belum Berlangganan'; ?></strong>
                </div>
                <div class="profile-info-item">
                    <span>Kecepatan</span>
                    <strong><?= $data['kecepatan'] ? $data['kecepatan'] : '-'; ?></strong>
                </div>
            </div>

            <div class="profile-detail-list">
                <div class="profile-detail-item">
                    <span>Email</span>
                    <strong><?= $data['email_customer']; ?></strong>
                </div>
                <div class="profile-detail-item">
                    <span>Telepon</span>
                    <strong><?= $data['telepon_customer']; ?></strong>
                </div>
                <div class="profile-detail-item">
                    <span>Alamat</span>
                    <strong><?= $data['alamat_customer']; ?></strong>
                </div>
                <div class="profile-detail-item">
                    <span>Status Langganan</span>
                    <?php
                    $status = strtolower($data['status_langganan'] ?? '');
                    if ($status == 'aktif') {
                        echo '<span class="status-active">Aktif</span>';
                    } elseif ($status == 'suspend') {
                        echo '<span class="status-suspend">Suspend</span>';
                    } elseif ($status == 'berhenti') {
                        echo '<span class="status-stop">Berhenti</span>';
                    } else {
                        echo '<span class="status-pending">Belum Aktif</span>';
                    }
                    ?>
                </div>
            </div>

            <div class="profile-action">
                <a href="edit.php" class="btn-orange">Edit Profil</a>
            </div>
        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
    </div>
</div>

</body>
</html>