<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';
require_once __DIR__ . '/generate.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

/* ================= FILTER ================= */
$where = "WHERE 1=1";

/* ================= FILTER PERIODE ================= */
if(isset($_GET['periode']) && $_GET['periode'] != ''){

    $explode = explode('-', $_GET['periode']);
    $tahun = $explode[0];
    $bulan = $explode[1];

    $where .= "
        AND bulan_tagihan='$bulan'
        AND tahun_tagihan='$tahun'
    ";
}

/* ================= FILTER STATUS ================= */
if(isset($_GET['status']) && $_GET['status'] != ''){

    $status = $_GET['status'];

    $where .= "
        AND status_pembayaran='$status'
    ";
}

/* ================= QUERY ================= */
$query = mysqli_query(
    $koneksi, 
    "SELECT
        tb_transaksi.*,
        tb_customer.nama_customer
     FROM tb_transaksi
     JOIN tb_customer ON tb_transaksi.id_customer = tb_customer.id_customer
     $where
     ORDER BY id_transaksi DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>

    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <!-- FLATPICKR -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i> Kelola Paket
                </a>
            </li>
            <li>
                <a href="../customer/index.php">
                    <i class="bi bi-people"></i> Data Pelanggan
                </a>
            </li>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-credit-card"></i> Data Transaksi
                </a>
            </li>
            <li>
                <a href="../admin_user/index.php">
                    <i class="bi bi-person-plus"></i> Tambah Admin
                </a>
            </li>
            <li>
                <a href="../../auth/logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Data Transaksi</h1>
                <p>Kelola pembayaran pelanggan</p>
            </div>
        </div>

        <div class="table-card">
        <!-- TABLE HEADER -->
            <div class="table-header">
                <h3>Daftar Transaksi</h3>
                <!-- FILTER -->
                <form method="GET" class="filter-form">
                    <input
                        type="text"
                        id="periode"
                        name="periode"
                        value="<?= isset($_GET['periode']) ? $_GET['periode'] : ''; ?>"
                        placeholder="Pilih Periode"
                    >

                    <select name="status">
                        <option value="">
                            Semua Status
                        </option>
                        <option value="Belum Bayar">
                            Belum Bayar
                        </option>
                        <option value="Menunggu Konfirmasi">
                            Menunggu Konfirmasi
                        </option>
                        <option value="Lunas">
                            Lunas
                        </option>
                    </select>

                    <button type="submit" class="btn-orange">
                        Filter
                    </button>
                </form>
            </div>

            <!-- TABLE -->
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
                    while($data = mysqli_fetch_assoc($query)) :
                    ?>
                    <tr>
                        <td>
                            <?= $no++; ?>
                        </td>
                        
                        <td>
                            <?= $data['kode_invoice']; ?>
                        </td>
                        
                        <td>
                            <?= $data['nama_customer']; ?>
                        </td>
                        
                        <td>
                            <?= date(
                                'F Y',
                                strtotime($data['tahun_tagihan'].'-'.$data['bulan_tagihan'].'-01')
                            ); ?>
                        </td>
                        
                        <td>
                            Rp <?= number_format($data['jumlah_bayar']); ?>
                        </td>
                        
                        <td>
                            <?php if($data['status_pembayaran'] == 'Lunas') : ?>
                                <span class="status-active">
                                    Lunas
                                </span>
                            <?php elseif($data['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
                                <span class="status-pending">
                                    Menunggu
                                </span>
                            <?php else : ?>
                                <span class="status-belum">
                                    Belum Bayar
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <?= $data['tanggal_transaksi']; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

<!-- FLATPICKR INITIALIZATION -->
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

</body>
</html>