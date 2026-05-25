<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';
require_once __DIR__ . '/generate.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$where = "WHERE 1=1";

if (isset($_GET['periode']) && $_GET['periode'] != '') {
    $explode = explode('-', $_GET['periode']);
    $tahun = $explode[0];
    $bulan = $explode[1];

    $where .= "
        AND tb_transaksi.bulan_tagihan='$bulan'
        AND tb_transaksi.tahun_tagihan='$tahun'
    ";
}

if (isset($_GET['status']) && $_GET['status'] != '') {
    $status = $_GET['status'];
    $where .= "
        AND tb_transaksi.status_pembayaran='$status'
    ";
}

$sql = "SELECT tb_transaksi.*, tb_customer.nama_customer
        FROM tb_transaksi
        INNER JOIN tb_langganan ON tb_transaksi.id_langganan = tb_langganan.id_langganan
        INNER JOIN tb_customer ON tb_langganan.id_customer = tb_customer.id_customer
        $where
        ORDER BY tb_transaksi.id_transaksi DESC";

$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul>
            <li><a href="../index.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="../paket/index.php"><i class="bi bi-wifi"></i> Kelola Paket</a></li>
            <li><a href="../customer/index.php"><i class="bi bi-people"></i> Data Pelanggan</a></li>
            <li><a href="index.php" class="active"><i class="bi bi-credit-card"></i> Data Transaksi</a></li>
            <li><a href="../admin_user/index.php"><i class="bi bi-person-plus"></i> Tambah Admin</a></li>
            <li><a href="#" onclick="openLogoutModal()"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <div>
                <h1>Data Transaksi</h1>
                <p>Kelola pembayaran pelanggan</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Transaksi</h3>
                
                <form method="GET" class="filter-form">
                    <input type="text" id="periode" name="periode" value="<?= isset($_GET['periode']) ? htmlspecialchars($_GET['periode']) : ''; ?>" placeholder="Pilih Periode">
                    
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" <?= (isset($_GET['status']) && $_GET['status'] == 'belum_bayar') ? 'selected' : ''; ?>>Belum Bayar</option>
                        <option value="menunggu_verifikasi" <?= (isset($_GET['status']) && $_GET['status'] == 'menunggu_verifikasi') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                        <option value="lunas" <?= (isset($_GET['status']) && $_GET['status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                    </select>

                    <button type="submit" class="btn-orange">Filter</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Periode</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0) :
                        while ($data = mysqli_fetch_assoc($query)) :
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $data['kode_invoice']; ?></strong></td>
                        <td><?= $data['nama_customer']; ?></td>
                        <td><?= date('F Y', strtotime($data['tahun_tagihan'] . '-' . $data['bulan_tagihan'] . '-01')); ?></td>
                        <td>Rp <?= number_format($data['jumlah_bayar']); ?></td>
                        <td>
                            <?php 
                            $status = strtolower($data['status_pembayaran']);
                            if ($status == 'lunas') : 
                            ?>
                                <span class="status-active">Lunas</span>
                            <?php elseif ($status == 'menunggu_verifikasi') : ?>
                                <span class="status-pending">Menunggu Verifikasi</span>
                            <?php else : ?>
                                <span class="status-belum" style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">
                                    Belum Bayar
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($data['tanggal_bayar']) ? $data['tanggal_bayar'] : '-'; ?></td>
                    </tr>
                    <?php 
                        endwhile; 
                    else : 
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888;">Tidak ada data transaksi ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #fff; border-left: 4px solid #ff6b00; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <details>
                <summary style="cursor: pointer; font-weight: 600; color: #333; user-select: none;">
                    <i class="bi bi-bug-fill" style="color: #ff6b00; margin-right: 5px;"></i> Debug Mode: Lihat Query SQL Halaman Ini
                </summary>
                <div style="margin-top: 15px; background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; line-height: 1.6; border: 1px solid #e9ecef; white-space: pre-wrap;">
                    <strong>Query Transaksi Berjalan:</strong><br>
                    <span style="color: #d63384;"><?= htmlspecialchars($sql); ?></span>
                </div>
            </details>
        </div>

    </div>
</div>

<script>
    flatpickr("#periode", {
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y"
            })
        ]
    });
</script>

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