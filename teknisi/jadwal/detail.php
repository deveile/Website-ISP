<?php
require_once __DIR__ . '/../../auth/cek_login.php'; // Memastikan session_start() aktif
require_once __DIR__ . '/../../koneksi.php';

// Proteksi Halaman: Hanya boleh diakses oleh Teknisi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pemasangan.php");
    exit;
}

$id_pemasangan = mysqli_real_escape_string($koneksi, $_GET['id']);
$id_teknisi_session = $_SESSION['id_teknisi'];

$success_alert = false;
$error_alert = false;
$msg = "";

// Ambil data detail pemasangan & HANYA IZINKAN AKSES jika ID teknisi COCOK dengan yang ditunjuk Admin
$query_detail = mysqli_query($koneksi, "
    SELECT 
        p.*,
        c.nama_customer, c.telepon_customer, c.email_customer,
        pk.nama_paket, pk.kecepatan
    FROM tb_pemasangan p
    LEFT JOIN tb_customer c ON p.id_customer = c.id_customer
    LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
    WHERE p.id_pemasangan = '$id_pemasangan' 
      AND p.id_teknisi = '$id_teknisi_session' -- Mengunci akses hanya untuk teknisi pilihan Admin
");
$data = mysqli_fetch_assoc($query_detail);

// Jika data tidak ditemukan atau tugas itu milik teknisi lain, tendang balik ke jadwal
if (!$data) {
    header("Location: ../jadwal_pasang.php");
    exit;
}

$status = strtolower(trim($data['status_pemasangan']));

// 2. Proses Update Status & Upload Bukti oleh Teknisi
if (isset($_POST['ubah_status'])) {
    $status_baru = mysqli_real_escape_string($koneksi, $_POST['status_pemasangan']);
    $tanggal_sekarang = date('Y-m-d');
    $catatan_teknis = mysqli_real_escape_string($koneksi, $_POST['catatan_teknis'] ?? '');

    if ($status_baru === 'dibatalkan') {
        $error_alert = true;
        $msg = "Teknisi tidak memiliki hak untuk membatalkan berkas pengajuan.";
    } else {
        
        // JIKA STATUS DIUBAH MENJADI TERPASANG (SELESAI) -> WAJIB UPLOAD FOTO
        if ($status_baru == 'terpasang' || $status_baru == 'selesai') {
            if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['bukti_foto']['tmp_name'];
                $file_name = $_FILES['bukti_foto']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
                
                if (in_array($file_ext, $ekstensi_diizinkan)) {
                    $nama_file_baru = "bukti_" . $id_pemasangan . "_" . time() . "." . $file_ext;
                    $folder_tujuan = "../../assets/uploads/bukti_pasang/";
                    
                    if (!is_dir($folder_tujuan)) { mkdir($folder_tujuan, 0755, true); }

                   if (move_uploaded_file($file_tmp, $folder_tujuan . $nama_file_baru)) {
                        // PERBAIKAN 1: Izinkan update jika tugas milik dia, ATAU jika statusnya rebutan (id_teknisi masih kosong/0)
                        $query_update = "UPDATE tb_pemasangan SET 
                                            status_pemasangan = '$status_baru', 
                                            id_teknisi = '$id_teknisi_session', -- Amankan penguncian ID Teknisi di sini
                                            tanggal_pasang = '$tanggal_sekarang',
                                            catatan = '$catatan_teknis',
                                            bukti_foto = '$nama_file_baru'
                                         WHERE id_pemasangan = '$id_pemasangan' 
                                           AND (id_teknisi = '$id_teknisi_session' OR id_teknisi IS NULL OR id_teknisi = '' OR id_teknisi = 0)";
                    } else {
                        $error_alert = true;
                        $msg = "Gagal memindahkan file foto ke server data.";
                    }
                } else {
                    $error_alert = true;
                    $msg = "Format file tidak didukung. Harap upload gambar berformat JPG, JPEG, atau PNG.";
                }
            } else {
                $error_alert = true;
                $msg = "Gagal menyelesaikan tugas! Teknisi wajib menyertakan foto bukti pemasangan.";
            }
        } else {
            // JIKA STATUS DIUBAH MENJADI DIPROSES (Sistem Rebutan dimulai dari sini)
            // PERBAIKAN 2: Set id_teknisi saat tombol "Mulai Proses Lapangan" diklik
            $query_update = "UPDATE tb_pemasangan SET 
                                status_pemasangan = '$status_baru',
                                id_teknisi = '$id_teknisi_session', -- Kunci teknisinya di sini agar tugas tidak direbut orang lain
                                catatan = '$catatan_teknis'
                             WHERE id_pemasangan = '$id_pemasangan' 
                               AND (id_teknisi = '$id_teknisi_session' OR id_teknisi IS NULL OR id_teknisi = '' OR id_teknisi = 0)";
        }

        // Eksekusi Query
        if (!$error_alert) {
            if (mysqli_query($koneksi, $query_update)) {
                $success_alert = true;
                $msg = ucfirst($status_baru);
                
                // Fetch ulang data terbaru
                $query_detail = mysqli_query($koneksi, "
                    SELECT p.*, c.nama_customer, c.telepon_customer, c.email_customer, pk.nama_paket, pk.kecepatan 
                    FROM tb_pemasangan p 
                    LEFT JOIN tb_customer c ON p.id_customer = c.id_customer 
                    LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket 
                    WHERE p.id_pemasangan = '$id_pemasangan'
                ");
                $data = mysqli_fetch_assoc($query_detail);
                $status = strtolower(trim($data['status_pemasangan']));
            } else {
                $error_alert = true;
                $msg = mysqli_error($koneksi);
            }
        }
    }
}

function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $bulan_array = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan_array[(int)$split[1]] . ' ' . $split[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kerja Pemasangan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px; width: 100%; }
        .detail-info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .detail-info-table td { padding: 14px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: top; }
        .detail-info-table td.label { font-weight: 600; color: #64748b; width: 30%; }

        .card-footer-action { display: flex; justify-content: flex-end; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9; }
        .btn-kembali-red { background-color: #ea580c; color: #ffffff !important; padding: 8px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none !important; display: inline-block; border: none; cursor: pointer; }
        .btn-kembali-red:hover { background-color: #c2410c; }

        .action-panel { display: flex; flex-direction: column; gap: 12px; }
        .btn-action { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; color: white; }
        .btn-action-process { background: linear-gradient(135deg,#3b82f6,#2563eb); }
        .btn-action-success { background: linear-gradient(135deg,#22c55e,#16a34a); }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.12); opacity: 0.9; }
        
        .table-card { background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .table-header { background: #ea580c; padding: 18px 24px; }
        .table-header h3 { color: #fff; margin: 0; font-size: 18px; font-weight: 700; }
        .detail-info-table tr:hover { background: #f8fafc; }
        .detail-info-table td.label { width: 220px; font-weight: 600; color: #475569; background: #f8fafc; }
        .detail-info-table td:last-child { color: #0f172a; }

        .panel-locked { padding: 20px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; text-align: center; color: #64748b; }
        .panel-locked i { font-size: 28px; color: #ea580c; margin-bottom: 8px; display: block; }

        .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; margin-top: 10px;}
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box; }

        @media (max-width: 991px) {
            .dashboard-layout { flex-direction: column !important; }
            .sidebar { position: fixed !important; top: 0; left: 0; width: 260px !important; height: 100vh !important; background: #ffffff !important; z-index: 9999 !important; box-shadow: 4px 0 15px rgba(0,0,0,0.1); transform: translateX(-100%); transition: transform 0.3s ease-in-out; padding: 24px !important; }
            .sidebar.active { transform: translateX(0); }
            .dashboard-content { width: 100% !important; padding: 20px !important; }
            .detail-grid { grid-template-columns: 1fr; }
            .detail-info-table td.label { width: 35%; }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar" style="position: fixed; top: 0; left: 0; height: 100vh; width: 260px; background: #fff; z-index: 1000; padding: 24px; border-right: 1px solid #e5e7eb;">
        <div class="sidebar-logo" style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px;">
            <img src="../../assets/images/logo.png" alt="Logo" style="width: 40px;">
            <h2 style="font-size: 20px; margin: 0; color: #ea580c;">Anuwani</h2>
        </div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 8px;"><a href="../index.php" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #475569; text-decoration: none; font-weight: 500; border-radius: 8px;"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li style="margin-bottom: 8px;"><a href="../jadwal_pasang.php" class="active" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #ea580c; background: #fff7ed; text-decoration: none; font-weight: 600; border-radius: 8px;"><i class="bi bi-calendar-event"></i> Jadwal Pasang</a></li>
            <li><a href="#" onclick="openLogoutModal()" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #ef4444; text-decoration: none; font-weight: 500; border-radius: 8px;"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content" style="flex-grow: 1; margin-left: 260px; padding: 32px; width: calc(100% - 260px);">
        <div class="topbar" style="display: flex; align-items: center; margin-bottom: 25px;">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span><span></span><span></span>
            </button>
            <div>
                <h1 style="margin:0; font-size: 22px; color:#1e293b;">Manajemen Tugas Lapangan</h1>
                <p style="margin:0; font-size: 14px; color:#64748b;">Harap lakukan instalasi secara teliti dan upload berkas bukti fisik.</p>
            </div>
        </div>

        <div class="detail-grid">
            <div class="table-card">
                <div class="table-header">
                    <h3>Detail Lembar Tugas Instalatir</h3>
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
                        <td class="label">Alamat Pelanggan</td>
                        <td>: <strong><?= htmlspecialchars($data['alamat_pasang']); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="label">Status Pekerjaan</td>
                        <td>: 
                            <?php
                            if ($status == 'selesai' || $status == 'terpasang') {
                                echo '<span class="status-active">Selesai / Terpasang</span>';
                            } elseif ($status == 'pending' || $status == 'menunggu') {
                                echo '<span class="status-pending">Menunggu Jadwal</span>';
                            } elseif ($status == 'proses' || $status == 'diproses') {
                                echo '<span class="status-pending" style="background:#dbeafe; color:#1e40af;">Sedang Diproses</span>';
                            } else {
                                echo '<span class="status-nonactive">' . htmlspecialchars($data['status_pemasangan']) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Catatan Teknis Admin</td>
                        <td>: <em><?= !empty($data['catatan']) ? htmlspecialchars($data['catatan']) : '-'; ?></em></td>
                    </tr>
                    <?php if (!empty($data['bukti_foto'])) : ?>
                    <tr>
                        <td class="label">Foto Bukti Terupload</td>
                        <td>: <a href="../../assets/uploads/bukti_pasang/<?= $data['bukti_foto']; ?>" target="_blank" style="color: #ea580c; font-weight: 600; text-decoration: underline;"><i class="bi bi-image"></i> Lihat Foto Lapangan</a></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <div class="card-footer-action">
                    <a href="pemasangan.php" class="btn-kembali-red">Kembali ke Daftar</a>
                </div>
            </div>

            <div class="table-card" style="height: fit-content;">
                <div class="table-header">
                    <h3>Aksi Lapangan Teknisi</h3>
                </div>
                
                <div style="padding: 20px;">
                    <form id="form_update_teknisi" action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="status_pemasangan" id="input_status_hidden" value="">
                        <input type="hidden" name="ubah_status" value="1">

                        <div class="action-panel">
                            <?php if ($status == 'menunggu' || $status == 'pending') : ?>
                                <label class="form-label">Catatan Lapangan (Opsional)</label>
                                <textarea name="catatan_teknis" class="form-control" rows="3" placeholder="Tulis catatan penunjang..."></textarea>
                                
                                <button type="button" class="btn-action btn-action-process" onclick="eksekusiTeknisi('diproses')">
                                    <i class="bi bi-gear-fill"></i> Mulai Proses Lapangan
                                </button>
                            <?php endif; ?>

                            <?php if ($status == 'proses' || $status == 'diproses') : ?>
                                <div style="background: #fff7ed; padding: 12px; border-radius: 8px; border: 1px solid #ffedd5; margin-bottom: 8px;">
                                    <label class="form-label" style="margin-top:0; color:#c2410c;"><i class="bi bi-camera-fill"></i> Upload Bukti Gambar (Wajib)</label>
                                    <input type="file" name="bukti_foto" id="file_bukti" class="form-control" accept="image/png, image/jpeg, image/jpg">
                                    
                                    <label class="form-label">Catatan Teknis Hasil Pasang</label>
                                    <textarea name="catatan_teknis" id="catatan_hasil" class="form-control" rows="3" placeholder="Contoh: Redaman -18dB, modem terpasang di ruang tamu."></textarea>
                                </div>

                                <button type="button" class="btn-action btn-action-success" onclick="eksekusiTeknisi('terpasang')">
                                    <i class="bi bi-check-circle-fill"></i> Selesai & Kirim Laporan
                                </button>
                            <?php endif; ?>

                            <?php if ($status == 'terpasang' || $status == 'selesai' || $status == 'dibatalkan') : ?>
                                <div class="panel-locked">
                                    <i class="bi bi-lock-fill"></i>
                                    Tugas ini telah diselesaikan/dikunci. Data laporan sudah terkirim ke pusat data Admin.
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="logout-modal" id="logoutModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); display: none; align-items: center; justify-content: center; z-index: 10000;">
    <div class="logout-modal-content" style="background: #fff; padding: 24px; border-radius: 16px; max-width: 400px; width: 100%; text-align: center;">
        <div class="logout-icon" style="font-size: 30px; color: #ea580c;"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Konfirmasi Keluar</h2>
        <p>Apakah Anda yakin ingin keluar dari halaman teknisi?</p>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button onclick="closeLogoutModal()" style="flex: 1; padding: 10px; border: none; background: #e2e8f0; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
            <a href="../../auth/logout.php" style="flex: 1; padding: 10px; background: #ea580c; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;">Ya, Keluar</a>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() { document.getElementById('logoutModal').style.display = 'flex'; }
    function closeLogoutModal() { document.getElementById('logoutModal').style.display = 'none'; }

    function eksekusiTeknisi(status_target) {
        let judul = "Mulai Pengerjaan?";
        let teks = "Status tugas akan diubah menjadi proses pengerjaan lapangan.";
        let icon = "info";
        let confirmColor = "#3b82f6";

        if (status_target === 'terpasang') {
            judul = "Selesaikan Pemasangan?";
            teks = "Pastikan konfigurasi internet lancar dan berkas foto bukti sudah diisi.";
            icon = "success";
            confirmColor = "#22c55e";

            // Validasi file input wajib terisi di sisi Client JavaScript
            const fileInput = document.getElementById('file_bukti');
            if (!fileInput || fileInput.files.length === 0) {
                Swal.fire({
                    title: 'Gagal Kirim!',
                    text: 'Anda harus mengambil/memilih foto bukti pemasangan terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }

        Swal.fire({
            title: judul,
            text: teks,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('input_status_hidden').value = status_target;
                document.getElementById('form_update_teknisi').submit();
            }
        });
    }

    <?php if ($success_alert): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Laporan pengerjaan berhasil diperbarui!',
            icon: 'success',
            confirmButtonColor: '#ea580c'
        });
    <?php endif; ?>

    <?php if ($error_alert): ?>
        Swal.fire({
            title: 'Gagal Terproses!',
            text: '<?= $msg; ?>',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>
</script>
</body>
</html>