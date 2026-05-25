<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

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
        /* ✅ Fix 1: cursor pointer pada tombol hapus */
        .btn-hapus {
            cursor: pointer !important;
        }

        /* ✅ Fix 2: tombol hapus warna merah solid */
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

        /* ✅ Fix 3: tombol kembali — icon panah kiri */
        .btn-kembali {
            cursor: pointer;
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
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="../transaksi/index.php"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="list_admin.php" class="active"><i class="bi bi-person-gear"></i> Kelola Admin</a></li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <h1>Kelola Admin</h1>
            <p>Daftar semua akun administrator sistem</p>
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

<!-- Modal Konfirmasi Hapus -->
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

<!-- Modal Logout -->
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

document.querySelectorAll('.logout-modal').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
</script>

</body>
</html>