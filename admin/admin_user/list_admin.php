<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$query_notif_pemasangan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_pemasangan
    WHERE status_pemasangan = 'menunggu'
");
$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'];

$query_notif_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'] ?? 0;

$pesan_error  = '';
$pesan_sukses = '';

if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];

    if ($id_hapus == (int)$_SESSION['id_user']) {
        $pesan_error = 'Tidak bisa menghapus akun Anda sendiri.';
    } else {
        $total = (int)mysqli_fetch_assoc(
            mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM tb_admin")
        )['c'];

        if ($total <= 1) {
            $pesan_error = 'Harus ada minimal 1 admin aktif di sistem.';
        } else {
            $ok1 = mysqli_query($koneksi, "DELETE FROM tb_admin WHERE id_user = $id_hapus");
            $ok2 = $ok1 ? mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user = $id_hapus AND role = 'admin'") : false;

            if ($ok1 && $ok2 && mysqli_affected_rows($koneksi) > 0) {
                $pesan_sukses = 'Akun admin berhasil dihapus.';
            } else {
                $pesan_error = 'Gagal: ' . mysqli_error($koneksi);
            }
        }
    }
}

$result = mysqli_query($koneksi,
    "SELECT u.id_user, a.nama_admin, u.username, a.email_admin
     FROM tb_admin a
     JOIN tb_user u ON a.id_user = u.id_user
     ORDER BY a.nama_admin ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin | Anuwani</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .dashboard-layout {
            display: flex !important;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            transition: all 0.3s ease;
            background: #fff;
            overflow: hidden;
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

        /* --- TABLE STYLES MATCHED TO REFERENCED LAYOUT --- */
        .table-card {
            background: #ffffff !important;
            border-radius: 16px !important;
            padding: 24px !important; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02) !important;
        }

        .table-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 20px !important;
            flex-wrap: wrap !important;
            gap: 15px !important;
        }

        table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            overflow: hidden !important;
        }

        table th {
            background: #ff6600 !important;
            color: #ffffff !important;
            text-transform: uppercase !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            padding: 14px 12px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
        }
        table th:last-child {
            border-right: none !important;
        }
        
        table td {
            padding: 12px 14px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            border-right: 1px solid #e4e4e7 !important;
            color: #27272a !important;
            font-weight: 400;
            vertical-align: middle;
        }
        table td:last-child {
            border-right: none !important;
        }
        table tr:last-child td {
            border-bottom: none !important;
        }

        .btn-hapus {
            cursor: pointer !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: #e53935 !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        .btn-hapus:hover {
            background-color: #b71c1c !important;
            color: #ffffff !important;
        }

        .btn-kembali {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1.5px solid #e4e4e7;
            color: #52525b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: #fafafa;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-kembali:hover {
            background: #f4f4f5;
        }

        .table-footer-action {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e4e4e7;
        }

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

        .logout-modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .logout-modal.show {
            opacity: 1; pointer-events: auto;
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed !important;
                top: 0 !important; left: 0 !important;
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
            <img src="../../assets/images/logo.png" alt="Logo">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> <span>Dashboard</span></a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> <span>Kelola Paket</span></a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li><a href="../pemasangan/index.php">
                <i class="bi bi-tools"></i>
                <span>Kelola Pemasangan</span>
                <?php if ($total_notif_pemasangan > 0): ?>
                    <span class="notif-badge">
                        <?= $total_notif_pemasangan; ?>
                    </span>
                <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif_transaksi > 0): ?>
                        <span class="notif-badge"><?= $total_notif_transaksi; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="list_admin.php" class="active"><i class="bi bi-person-gear"></i> <span>Kelola Admin</span></a></li>
            <li><a href="../teknisi_user/index.php"><i class="bi bi-person-plus"></i> <span>Kelola Teknisi</span></a></li>
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
                <h1>Kelola Admin</h1>
                <p>Daftar semua akun administrator sistem</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Data Administrator</h3>
            </div>

            <?php if ($pesan_error): ?>
                <div class="alert-mini alert-mini-gagal">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($pesan_error) ?>
                </div>
            <?php endif; ?>

            <?php if ($pesan_sukses): ?>
                <div class="alert-mini alert-mini-sukses">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= htmlspecialchars($pesan_sukses) ?>
                </div>
            <?php endif; ?>

            <div style="overflow-x:auto; width: 100%; border-radius: 8px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="60" style="text-align:center;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th width="140" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                            $is_me = ((int)$row['id_user'] === (int)$_SESSION['id_user']);
                    ?>
                        <tr>
                            <td style="text-align:center;"><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_admin']) ?></strong>
                                <?php if ($is_me): ?>
                                    <span style="background:#e3f2fd;color:#1565c0;font-size:11px;
                                                 padding:2px 8px;border-radius:20px;
                                                 margin-left:6px;font-weight:600;">
                                        Anda
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <?= $row['email_admin']
                                    ? htmlspecialchars($row['email_admin'])
                                    : '<span class="text-muted">- Tidak ada -</span>' ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if (!$is_me): ?>
                                    <button class="btn-hapus"
                                        data-id="<?= (int)$row['id_user'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_admin'], ENT_QUOTES) ?>"
                                        onclick="openDeleteConfirm(this)">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                <?php else: ?>
                                    <span style="color:#aaa;font-size:13px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#a1a1aa; padding:30px;">
                                Belum ada data admin yang terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-footer-action">
                <a href="index.php" class="btn-kembali">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Form
                </a>
            </div>
        </div>
    </div>
</div>

<div class="logout-modal" id="deleteModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-exclamation-triangle-fill" style="color:#eb4d4b;font-size:36px;"></i>
        </div>
        <h2>Konfirmasi Hapus</h2>
        <p>Hapus akun admin <strong id="namaAdmin"></strong>?<br>
           <small style="color:#888;">Tindakan ini tidak bisa dibatalkan.</small>
        </p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="tutupDeleteModal()">Batal</button>
            <button class="btn-danger-confirm" onclick="jalankanHapus()" style="cursor:pointer;">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon">
            <i class="bi bi-box-arrow-right" style="font-size:36px;"></i>
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

    var idHapusAdmin = 0;

    function openDeleteConfirm(btn) {
        idHapusAdmin = btn.getAttribute('data-id');
        document.getElementById('namaAdmin').textContent = btn.getAttribute('data-nama');
        document.getElementById('deleteModal').classList.add('show');
    }

    function tutupDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
        idHapusAdmin = 0;
    }

    function jalankanHapus() {
        if (idHapusAdmin) {
            window.location.href = 'list_admin.php?hapus=' + idHapusAdmin;
        }
    }

    function openLogoutModal() {
        document.getElementById('logoutModal').classList.add('show');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.remove('show');
    }

    document.querySelectorAll('.logout-modal').forEach(function(m) {
        m.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });
</script>
</body>
</html>