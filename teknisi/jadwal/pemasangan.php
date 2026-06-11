<?php
require_once __DIR__ . '/../../auth/cek_login.php'; 
require_once __DIR__ . '/../../koneksi.php';       

// Proteksi halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../../auth/login.php"); 
    exit;
}

$id_teknisi = $_SESSION['id_teknisi'];

// Ambil data profil teknisi untuk nama di topbar
$sql_tek = "SELECT nama_teknisi FROM tb_teknisi WHERE id_teknisi = '$id_teknisi' LIMIT 1";
$query_tek = mysqli_query($koneksi, $sql_tek);
$data_tek = mysqli_fetch_assoc($query_tek);

// QUERY UTAMA (SUDAH DIPERBAIKI): Menampilkan SEMUA tugas yang siap dikerjakan di lapangan oleh siapa saja
$sql_jadwal = "SELECT tp.*, tc.nama_customer, tc.telepon_customer, pk.nama_paket 
               FROM tb_pemasangan tp
               INNER JOIN tb_customer tc ON tp.id_customer = tc.id_customer
               INNER JOIN tb_paket pk ON tp.id_paket = pk.id_paket
               WHERE tp.status_pemasangan IN ('proses', 'diproses')
               ORDER BY tp.status_pemasangan ASC, tp.tanggal_pasang DESC";

// EKSEKUSI QUERY (Jangan sampai terhapus)
$semua_tugas = mysqli_query($koneksi, $sql_jadwal);

function tgl_indo($tgl) {
    if (empty($tgl) || $tgl == '0000-00-00') return '-';
    $b = [1=>'Januari','Februari','Maret','April','Mei','Juni',
          'Juli','Agustus','September','Oktober','November','Desember'];
    $s = explode('-', $tgl);
    return $s[2] . ' ' . $b[(int)$s[1]] . ' ' . $s[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pemasangan - Teknisi</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png"> <link rel="stylesheet" href="../../assets/css/style.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script> <style>
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
        .sidebar-toggle:hover { background: #e0e0e0; }
        .sidebar-toggle span {
            display: block;
            width: 20px;
            height: 2.5px;
            background-color: #333;
            border-radius: 2px;
        }

        .table-card {
            background: #ffffff !important;
            border-radius: 16px !important;
            padding: 24px !important; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02) !important;
            margin-top: 10px;
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
            background: #ea580c !important; /* Tema Oranye */
            color: #ffffff !important;
            text-transform: uppercase !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            padding: 14px 12px !important;
        }
        
        table td {
            padding: 12px 14px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            border-right: 1px solid #e4e4e7 !important;
        }
        table td:last-child { border-right: none !important; }
        table tr:last-child td { border-bottom: none !important; }

        /* Badge Status */
        .status-active { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }

        /* Modal Logout */
        .logout-modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 16px;
        }
        .logout-modal.show { opacity: 1; pointer-events: auto; }
        
        .logout-modal-content {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .logout-icon {
            width: 56px; height: 56px;
            background: #ffedd5; color: #ea580c;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin: 0 auto 16px auto;
            line-height: 56px;
        }
        .logout-modal-content h2 { margin: 0 0 8px 0; font-size: 20px; color: #1e293b; }
        .logout-modal-content p { margin: 0 0 24px 0; font-size: 14px; color: #64748b; line-height: 1.5; }
        .logout-modal-action { display: flex; gap: 12px; }
        .btn-cancel, .btn-confirm { flex: 1; padding: 12px; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; text-align: center; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        .btn-confirm { background: #ea580c; color: #fff; }

        /* Responsif Breakpoints */
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
                padding: 32px !important;
            }
            
            .topbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                margin-bottom: 25px;
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
            .sidebar.collapsed ul li a span {
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
            .dashboard-layout { flex-direction: column !important; }
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
            .sidebar.active { transform: translateX(0); }
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
            <img src="../../assets/images/logo.png" alt="Logo"> <h2>Anuwani</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="../index.php"> <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="pemasangan.php" class="active"> 
                    <i class="bi bi-calendar-event"></i>
                    <span>Jadwal Pasang</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>   
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
                <h1 style="margin:0; font-size: 20px; color:#1e293b;">Data Jadwal Pemasangan</h1>
                <p style="margin:0; font-size: 14px; color:#64748b;">Semua list tugas instalasi jaringan Anda</p>
            </div>
        </div>
        
        <div class="table-card">
            <div class="table-header">
                <h3 style="margin:0; color:#1e293b;">Daftar Seluruh Tugas</h3>
            </div>
            
            <div style="overflow-x:auto; width: 100%; border-radius: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal Pasang</th>
                            <th>Nama Pelanggan</th>
                            <th>Paket</th>
                            <th>Alamat Lokasi</th>
                            <th>Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($semua_tugas) > 0) : ?>
                        <?php while ($r = mysqli_fetch_assoc($semua_tugas)) : 
                            $status_p = strtolower(trim($r['status_pemasangan']));
                            $class = ($status_p == 'terpasang') ? 'active' : 'pending';
                            $text  = ($status_p == 'terpasang') ? 'Terpasang' : 'Diproses';
                        ?>
                        <tr>
                            <td style="font-weight: 600;"><?= tgl_indo($r['tanggal_pasang']); ?></td>
                            <td>
                                <strong><?= htmlspecialchars($r['nama_customer']); ?></strong><br>
                                <small style="color:#64748b;"><?= htmlspecialchars($r['telepon_customer']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['nama_paket']); ?></td>
                            <td><?= htmlspecialchars($r['alamat_pasang']); ?></td>
                            <td><span class="status-<?= $class; ?>"><?= $text; ?></span></td>
                            <td style="text-align:center;">
                                <a href="detail.php?id=<?= $r['id_pemasangan']; ?>" class="btn-detail" style="background:#ea580c; color:#fff; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; display: inline-block; white-space: nowrap;">
                                    <i class="bi bi-eye-fill"></i> Detail / Upload
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="6" style="text-align:center; color:#a1a1aa; padding:40px;">Belum ada tugas pemasangan jaringan yang dimasukkan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-icon"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar dari Panel Teknisi?</p>
        <div class="logout-modal-action">
            <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
            <a href="../../auth/logout.php" class="btn-confirm">Ya, Logout</a> </div>
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
                if (window.innerWidth < 991) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    });

    function openLogoutModal() { document.getElementById('logoutModal').classList.add('show'); }
    function closeLogoutModal() { document.getElementById('logoutModal').classList.remove('show'); }
</script>
</body>
</html>