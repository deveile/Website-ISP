<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$query_notif = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_notif
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif = mysqli_fetch_assoc($query_notif)['total_notif'];

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
        .btn-hapus {
            cursor: pointer !important;
        }
        .btn-hapus {
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
            cursor: pointer !important;
        }

        .btn-kembali {
            cursor: pointer;
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

        /* ==========================================================================
           CSS UPDATE: SINKRONISASI LAYOUT LIST ADMIN & HAMBURGER
           ========================================================================== */
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

        /* TAMPILAN LAPTOP / DESKTOP (Min 992px) */
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

            /* EFEK COLLAPSED (Sidebar Mengecil Menjadi 70px) */
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

        /* TAMPILAN SMARTPHONE / TABLET (Max 991px) */
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
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="list_admin.php" class="active"><i class="bi bi-person-gear"></i> <span>Kelola Admin</span></a></li>
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

        <div class="table-container-admin">
            <div class="table-header-action">
                <h2>Data Administrator</h2>
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

            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th width="140" class="text-center">Aksi</th>
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
                        <td class="text-center"><?= $no++ ?></td>
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
                        <td class="text-center">
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
                        <td colspan="5" class="text-center text-muted" style="padding:30px;">
                            Belum ada data admin yang terdaftar.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

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

    // Modal click backdrop logic handling
    document.querySelectorAll('.logout-modal').forEach(function(m) {
        m.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });
</script>
</body>
</html>