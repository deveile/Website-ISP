<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';
$data = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");

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
    <title>Kelola Paket</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                padding-left: 30px !important;
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
                border-radius: 8px !important;
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
                top: 0;
                left: 0;
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
            <img src="../../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> <span>Dashboard</span></a></li>
            <li><a href="index.php" class="active"><i class="bi bi-wifi"></i> <span>Kelola Paket</span></a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> <span>Kelola Admin</span></a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <?php if (isset($_GET['success'])) : ?>
            <div class="success-popup">
                <i class="bi bi-check-circle-fill"></i>
                <?php 
                    $msg = ['tambah' => 'Paket berhasil ditambahkan', 'aktif' => 'Paket berhasil diaktifkan', 'nonaktif' => 'Paket berhasil dinonaktifkan'];
                    echo $msg[$_GET['success']] ?? '';
                ?>
            </div>
        <?php endif; ?>

        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Kelola Paket</h1>
                <p>Tambah dan kelola paket internet</p>
            </div>
        </div>

        <div class="paket-admin-layout">
            <div class="paket-grid">
                <?php while ($p = mysqli_fetch_assoc($data)) : ?>
                    <div class="paket-admin-card">
                        <h3><?= $p['nama_paket']; ?></h3>
                        <h1><?= $p['kecepatan']; ?></h1>
                        <h2>Rp <?= number_format($p['harga']); ?></h2>
                        <p><?= nl2br($p['deskripsi']); ?></p>
                        <div class="paket-action">
                            <a href="edit.php?id=<?= $p['id_paket']; ?>" class="btn-edit">Edit</a>
                            <?php if ($p['status'] == 'aktif') : ?>
                                <a href="#" class="btn-delete" onclick="openNonaktifModal(<?= $p['id_paket']; ?>)">Nonaktifkan</a>
                            <?php else : ?>
                                <a href="#" class="btn-edit" onclick="openAktifModal(<?= $p['id_paket']; ?>)">Aktifkan</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="form-paket-card">
                <h3>Tambah Paket</h3>
                <form id="formPaket" action="tambah.php" method="POST">
                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="nama_paket" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" required>
                    </div>
                    <div class="form-group">
                        <label>Kecepatan</label>
                        <input type="text" name="kecepatan" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="4"></textarea>
                    </div>
                    <button type="button" class="btn-orange" onclick="openTambahModal()">Simpan Paket</button>
                </form>
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

<div class="logout-modal" id="tambahModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-plus-circle"></i></div>
        <h2>Tambah Paket</h2>
        <p>Apakah Anda yakin ingin menambahkan paket baru?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeTambahModal()">Batal</button>
            <button class="btn-confirm" onclick="submitTambahPaket()">Ya, Simpan</button>
        </div>
    </div>
</div>

<div class="logout-modal" id="nonaktifModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-exclamation-circle"></i></div>
        <h2>Nonaktifkan Paket</h2>
        <p>Apakah Anda yakin ingin menonaktifkan paket ini?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeNonaktifModal()">Batal</button>
            <a href="#" class="btn-confirm" id="btnNonaktif">Ya, Nonaktifkan</a>
        </div>
    </div>
</div>

<div class="logout-modal" id="aktifModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-check-circle"></i></div>
        <h2>Aktifkan Paket</h2>
        <p>Aktifkan kembali paket ini?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeAktifModal()">Batal</button>
            <a href="#" class="btn-confirm" id="btnAktif">Ya, Aktifkan</a>
        </div>
    </div>
</div>

<script>
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
</script>
</body>
</html>