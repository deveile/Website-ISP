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

$query_notif = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_notif
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif = mysqli_fetch_assoc($query_notif)['total_notif'];
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
    <style>
        .notif-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 50%;
            background: #ef4444; color: #fff;
            font-size: 11px; font-weight: 800;
            margin-left: auto; flex-shrink: 0;
            animation: pulse-badge 1.8s ease-in-out infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
            50%       { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
        }

        .dashboard-layout {
            display: flex !important;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Desain Tombol Hamburger */
        .sidebar-toggle {
            display: flex !important;
            flex-direction: column;
            gap: 4px;
            background: #f0f0f0;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 15px;
            transition: background 0.2s;
        }
        .sidebar-toggle:hover {
            background: #e0e0e0;
        }
        .sidebar-toggle span {
            display: block;
            width: 20px;
            height: 2.5px;
            background-color: #333;
            border-radius: 2px;
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 1000 !important;
                overflow-y: auto !important;
                width: 260px !important;
                min-width: 260px !important;
                max-width: 260px !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .dashboard-content {
                flex-grow: 1 !important;
                margin-left: 260px !important; 
                width: calc(100% - 260px) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .topbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
            }

            .sidebar.collapsed {
                width: 70px !important;
                min-width: 70px !important;
                max-width: 70px !important;
                padding: 24px 8px !important;
            }
            
            .sidebar.collapsed + .dashboard-content {
                margin-left: 70px !important;
                width: calc(100% - 70px) !important;
            }

            .sidebar.collapsed .sidebar-logo h2,
            .sidebar.collapsed ul li a span,
            .sidebar.collapsed .notif-badge {
                display: none !important; 
            }

            .sidebar.collapsed .sidebar-logo {
                justify-content: center !important;
                padding: 0 !important;
            }
            .sidebar.collapsed ul li a {
                justify-content: center !important;
                padding: 12px 0 !important;
            }
            .sidebar.collapsed ul li a i {
                margin: 0 !important;
                font-size: 20px !important; 
            }
        }

        @media (max-width: 991px) {
            .dashboard-layout {
                flex-direction: column !important;
            }
            .sidebar {
                position: fixed !important;
                top: 0; left: 0;
                width: 260px !important;
                min-width: 260px !important;
                height: 100vh !important;
                background: #ffffff !important;
                z-index: 9999 !important;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                padding: 24px !important;
                overflow-y: auto;
            }
            .sidebar.active {
                transform: translateX(0); 
            }
            .dashboard-content {
                width: 100% !important;
                padding: 20px !important;
            }
            .topbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                margin-bottom: 24px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> <span>Dashboard</span></a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> <span>Kelola Paket</span></a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li><a href="../pemasangan/index.php"><i class="bi bi-tools"></i><span>Kelola Pemasangan</span></a></li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="index.php" class="active"><i class="bi bi-person-plus"></i> <span>Kelola Admin</span></a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Tambah Admin</h1>
                <p>Tambahkan akun admin baru</p>
            </div>
        </div>

        <div class="form-wrapper-admin">
            <form method="POST" class="form-admin-card">
                
                <div class="form-admin-header">
                    <h2>Form Admin</h2>
                    <a href="list_admin.php" class="btn-ke-daftar">
                        <i class="bi bi-list-ul"></i> List Admin
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert-box alert-error">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        <?= htmlspecialchars($error) ?>
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

<div class="success-modal" id="successModal">
    <div class="success-modal-content">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h2>Berhasil!</h2>
        <p>Akun admin baru telah berhasil dibuat.</p>
        <div class="success-modal-action">
            <button class="btn-close-popup" onclick="closeSuccessModal()">Tetap di Sini</button>
            <a href="index.php" class="btn-view-list">Lihat Daftar</a>
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
    // --- JAVASCRIPT SYSTEM COMPATIBILITY LOGIC ---
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                if (window.innerWidth >= 992) {
                    sidebar.classList.toggle('collapsed');
                } else {
                    sidebar.classList.toggle('active');
                }
                e.stopPropagation();
            });

            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    });

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

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }

    <?php if (!empty($success)): ?>
        document.getElementById('successModal').style.display = 'flex';
    <?php endif; ?>
</script>
</body>
</html>