<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id_pemasangan = mysqli_real_escape_string($koneksi, $_GET['id']);

$success_alert = false;
$error_alert = false;
$msg = "";

if (isset($_POST['ubah_status'])) {
    $status_baru = mysqli_real_escape_string($koneksi, $_POST['status_pemasangan']);
    $tanggal_sekarang = date('Y-m-d');

    if ($status_baru == 'terpasang') {
        $query_update = "UPDATE tb_pemasangan SET 
                            status_pemasangan = '$status_baru', 
                            tanggal_pasang = '$tanggal_sekarang' 
                         WHERE id_pemasangan = '$id_pemasangan'";
    } else {
        $query_update = "UPDATE tb_pemasangan SET 
                            status_pemasangan = '$status_baru' 
                         WHERE id_pemasangan = '$id_pemasangan'";
    }

    if (mysqli_query($koneksi, $query_update)) {
        $success_alert = true;
        $msg = ucfirst($status_baru);
    } else {
        $error_alert = true;
        $msg = mysqli_error($koneksi);
    }
}

$query_detail = mysqli_query($koneksi, "
    SELECT 
        p.id_pemasangan,
        p.id_customer,
        p.id_paket,
        p.tanggal_pengajuan,
        p.tanggal_pasang,
        p.alamat_pasang,
        p.status_pemasangan,
        p.catatan,
        c.nama_customer,
        c.telepon_customer,
        c.email_customer,
        pk.nama_paket,
        pk.kecepatan
    FROM tb_pemasangan p
    LEFT JOIN tb_customer c ON p.id_customer = c.id_customer
    LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
    WHERE p.id_pemasangan = '$id_pemasangan'
");
$data = mysqli_fetch_assoc($query_detail);

if (!$data) {
    header("Location: index.php");
    exit;
}

$query_notif_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");
$total_notif_transaksi = mysqli_fetch_assoc($query_notif_transaksi)['total'];

$query_notif_pemasangan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tb_pemasangan
    WHERE status_pemasangan = 'menunggu' OR status_pemasangan = 'Pending'
");
$total_notif_pemasangan = mysqli_fetch_assoc($query_notif_pemasangan)['total'];

function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $bulan_array = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan_array[(int)$split[1]] . ' ' . $split[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemasangan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .status-active { background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-nonactive { background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

        .dashboard-layout { display: flex !important; width: 100%; min-height: 100vh; overflow-x: hidden; }
        .sidebar-toggle {
            display: flex !important; flex-direction: column; gap: 4px; background: #f0f0f0; border: none;
            padding: 10px; border-radius: 8px; cursor: pointer; margin-right: 15px; transition: background 0.2s;
        }
        .sidebar-toggle:hover { background: #e0e0e0; }
        .sidebar-toggle span { display: block; width: 20px; height: 2.5px; background-color: #333; border-radius: 2px; }

        /* Detail Layout Grid System */
        .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px; width: 100%; }
        .detail-info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .detail-info-table td { padding: 14px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: top; }
        .detail-info-table td.label { font-weight: 600; color: #64748b; width: 30%; }
        
        /* Tombol Kembali Model Custom Red dari image_d8105f.png */
        .card-footer-action {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }
        .btn-kembali-red {
            background-color: #e11d48;
            color: #ffffff !important;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none !important;
            display: inline-block;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-kembali-red:hover {
            background-color: #be123c;
        }

        .action-panel { display: flex; flex-direction: column; gap: 12px; }
        .btn-action { 
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px; border: none; border-radius: 8px; 
            font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-action-process { background-color: #3b82f6; color: white; }
        .btn-action-success { background-color: #22c55e; color: white; }
        .btn-action-cancel { background-color: #ef4444; color: white; }
        .btn-action:hover { opacity: 0.9; transform: translateY(-1px); }
        .panel-locked { text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: #64748b; font-size: 13px; }

        @media (min-width: 992px) {
            .sidebar { position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; overflow-y: auto !important; width: 260px !important; min-width: 260px !important; max-width: 260px !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; }
            .dashboard-content { flex-grow: 1 !important; margin-left: 260px !important; width: calc(100% - 260px) !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; }
            .topbar { display: flex !important; align-items: center !important; justify-content: flex-start !important; }
            .sidebar.collapsed { width: 70px !important; min-width: 70px !important; max-width: 70px !important; padding: 24px 8px !important; }
            .sidebar.collapsed + .dashboard-content { margin-left: 70px !important; width: calc(100% - 70px) !important; }
            .sidebar.collapsed .sidebar-logo h2, .sidebar.collapsed ul li a span, .sidebar.collapsed .notif-badge { display: none !important; }
            .sidebar.collapsed .sidebar-logo { justify-content: center !important; padding: 0 !important; }
            .sidebar.collapsed ul li a { justify-content: center !important; padding: 12px 0 !important; }
            .sidebar.collapsed ul li a i { margin: 0 !important; font-size: 20px !important; }
        }

        /* Responsive Mobile Breakpoint */
        @media (max-width: 991px) {
            .dashboard-layout { flex-direction: column !important; }
            .sidebar { position: fixed !important; top: 0; left: 0; width: 260px !important; min-width: 260px !important; height: 100vh !important; background: #ffffff !important; z-index: 9999 !important; box-shadow: 4px 0 15px rgba(0,0,0,0.1); transform: translateX(-100%); transition: transform 0.3s ease-in-out; padding: 24px !important; overflow-y: auto; }
            .sidebar.active { transform: translateX(0); }
            .dashboard-content { width: 100% !important; padding: 20px !important; }
            .topbar { display: flex !important; align-items: center !important; justify-content: flex-start !important; margin-bottom: 24px; }
            .detail-grid { grid-template-columns: 1fr; } /* Layout berubah jadi 1 kolom bertumpuk di HP */
            .detail-info-table td.label { width: 40%; }
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
            <li><a href="../customer/index.php"><i class="bi bi-people"></i><span>Data Pelanggan</span></a></li>
            <li class="active"><a href="../pemasangan/index.php" class="active">
                <i class="bi bi-tools"></i>
                <span>Kelola Pemasangan</span>
                <?php if ($total_notif_pemasangan > 0): ?>
                    <span class="notif-badge"><?= $total_notif_pemasangan; ?></span>
                <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i>
                    <span>Data Transaksi</span>
                    <?php if ($total_notif_transaksi > 0): ?>
                        <span class="notif-badge"><?= $total_notif_transaksi; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../laporan_keuangan/index.php"><i class="bi bi-bar-chart-line"></i> <span>Laporan Keuangan</span></a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> <span>Kelola Admin</span></a></li>
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
                <h1>Detail Pemasangan</h1>
                <p>Kelola berkas manajemen instalasi teknis pelanggan.</p>
            </div>
        </div>

        <div class="detail-grid">
            <div class="table-card" style="margin-top: 0;">
                <div class="table-header">
                    <h3>Data Pemasangan</h3>
                </div>

                <table class="detail-info-table">
                    <tr>
                        <td class="label">Nama Customer</td>
                        <td>: <?= htmlspecialchars($data['nama_customer']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Kontak & Email</td>
                        <td>: <?= htmlspecialchars($data['telepon_customer'] . ' / ' . $data['email_customer']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Paket Internet</td>
                        <td>: <strong><?= htmlspecialchars($data['nama_paket'] . ' (' . $data['kecepatan'] . ')'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Pengajuan</td>
                        <td>: <?= tgl_indo($data['tanggal_pengajuan']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Pasang</td>
                        <td>: <?= tgl_indo($data['tanggal_pasang']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Pasang</td>
                        <td>: <?= htmlspecialchars($data['alamat_pasang']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>: 
                            <?php
                            $status = strtolower(trim($data['status_pemasangan']));
                            if ($status == 'selesai' || $status == 'terpasang') {
                                echo '<span class="status-active">Selesai / Terpasang</span>';
                            } elseif ($status == 'pending' || $status == 'menunggu') {
                                echo '<span class="status-pending">Menunggu</span>';
                            } elseif ($status == 'proses' || $status == 'diproses') {
                                echo '<span class="status-pending" style="background:#dbeafe; color:#1e40af;">Diproses</span>';
                            } else {
                                echo '<span class="status-nonactive">' . htmlspecialchars($data['status_pemasangan']) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Catatan teknis</td>
                        <td>: <em><?= !empty($data['catatan']) ? htmlspecialchars($data['catatan']) : '-'; ?></em></td>
                    </tr>
                </table>

                <div class="card-footer-action">
                    <a href="index.php" class="btn-kembali-red">Kembali</a>
                </div>
            </div>

            <div class="table-card" style="margin-top: 0; height: fit-content;">
                <div class="table-header">
                    <h3>Panel Informasi Pemasangan</h3>
                </div>
                
                <div style="padding: 10px 0;">
                    <form id="form_update_status" action="" method="POST">
                        <input type="hidden" name="status_pemasangan" id="input_status_hidden" value="">
                        <input type="hidden" name="ubah_status" value="1">

                        <div class="action-panel">
                            <?php if ($status == 'menunggu' || $status == 'pending') : ?>
                                <button type="button" class="btn-action btn-action-process" onclick="mintaKonfirmasi('diproses', 'Proses Pemasangan?', 'Ubah status ke proses pengerjaan lapangan.', 'info')">
                                    <i class="bi bi-gear-fill"></i> Mulai Proses Lapangan
                                </button>
                            <?php endif; ?>

                            <?php if ($status == 'menunggu' || $status == 'pending' || $status == 'proses' || $status == 'diproses') : ?>
                                <button type="button" class="btn-action btn-action-success" onclick="mintaKonfirmasi('terpasang', 'Pemasangan Selesai?', 'Nyatakan berkas selesai dan kunci data sistem.', 'success')">
                                    <i class="bi bi-check-circle-fill"></i> Selesai / Terpasang
                                </button>

                                <button type="button" class="btn-action btn-action-cancel" onclick="mintaKonfirmasi('dibatalkan', 'Batalkan Pengajuan?', 'Batalkan permohonan pemasangan pelanggan.', 'error')">
                                    <i class="bi bi-x-circle-fill"></i> Batalkan Pengajuan
                                </button>
                            <?php endif; ?>

                            <?php if ($status == 'terpasang' || $status == 'selesai' || $status == 'dibatalkan') : ?>
                                <div class="panel-locked">
                                    <i class="bi bi-lock-fill"></i> <br>
                                    Berkas pengajuan ini telah selesai diproses dan dikunci oleh sistem utama.
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
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

    function openLogoutModal() { document.getElementById('logoutModal').style.display = 'flex'; }
    function closeLogoutModal() { document.getElementById('logoutModal').style.display = 'none'; }

    function mintaKonfirmasi(status, judul, deskripsi, tipeIcon) {
        Swal.fire({
            title: judul,
            text: deskripsi,
            icon: tipeIcon,
            showCancelButton: true,
            confirmButtonColor: status === 'dibatalkan' ? '#ef4444' : (status === 'terpasang' ? '#22c55e' : '#3b82f6'),
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Eksekusi!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('input_status_hidden').value = status;
                document.getElementById('form_update_status').submit();
            }
        });
    }

    <?php if ($success_alert): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Status pengerjaan berhasil diupdate menjadi: <?= $msg; ?>',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
        });
    <?php endif; ?>

    <?php if ($error_alert): ?>
        Swal.fire({
            title: 'Sistem Error!',
            text: 'Pembaruan gagal dilakukan: <?= $msg; ?>',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>
</script>
</body>
</html>