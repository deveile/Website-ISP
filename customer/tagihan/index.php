<<<<<<< HEAD
<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= CUSTOMER ================= */
$customer = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_customer
     WHERE id_user = '$id_user'"
);

$c = mysqli_fetch_assoc($customer);

/* ================= FILTER ================= */
$periode = $_GET['periode'] ?? '';
$status  = $_GET['status'] ?? '';

$where = "WHERE id_customer = '" . $c['id_customer'] . "'";

if ($periode != '') {
    $split = explode('-', $periode);
    $tahun = $split[0];
    $bulan = $split[1];

    $where .= " 
        AND bulan_tagihan = '$bulan'
        AND tahun_tagihan = '$tahun'
    ";
}

if ($status != '') {
    $where .= " 
        AND status_pembayaran = '$status'
    ";
}

/* ================= TRANSAKSI ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_transaksi
     $where
     ORDER BY id_transaksi DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Tagihan Saya</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link class="favicon" rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
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
                <a href="index.php" class="active">
                    <i class="bi bi-receipt"></i> Tagihan Saya
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i> Paket Internet
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="bi bi-person"></i> Profile
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

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Tagihan Saya</h1>
                <p>Riwayat pembayaran dan tagihan internet</p>
            </div>
        </div>

        <!-- ================= TABLE ================= -->
        <div class="table-card">

            <div class="table-header">
                <h3>Daftar Tagihan</h3>

                <form method="GET" class="filter-form">
                    
                    <input 
                        type="month"
                        name="periode"
                        value="<?= $periode; ?>"
                    >

                    <select name="status">
                        
                        <option value="">
                            Semua Status
                        </option>

                        <option 
                            value="Belum Bayar"
                            <?= ($status == 'Belum Bayar') ? 'selected' : ''; ?>
                        >
                            Belum Bayar
                        </option>

                        <option 
                            value="Menunggu Konfirmasi"
                            <?= ($status == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>
                        >
                            Menunggu
                        </option>

                        <option 
                            value="Lunas"
                            <?= ($status == 'Lunas') ? 'selected' : ''; ?>
                        >
                            Lunas
                        </option>

                    </select>

                    <button type="submit" class="btn-orange">
                        Filter
                    </button>

                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($query)) : ?>
                    <tr>
                        <td>
                            <?= $t['kode_invoice']; ?>
                        </td>

                        <td>
                            <?= $t['bulan_tagihan']; ?>/<?= $t['tahun_tagihan']; ?>
                        </td>

                        <td>
                            Rp<?= number_format($t['jumlah_bayar']); ?>
                        </td>

                        <td>
                            <?php if ($t['status_pembayaran'] == 'Lunas') : ?>
                                <span class="status-active">
                                    Lunas
                                </span>
                            <?php elseif ($t['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
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
                            <?= $t['tanggal_transaksi']; ?>
                        </td>

                        <td>
                            <?php if ($t['status_pembayaran'] == 'Belum Bayar') : ?>
                                <a 
                                    href="bayar.php?id=<?= $t['id_transaksi']; ?>"
                                    class="btn-bayar"
                                >
                                    Bayar
                                </a>
                            <?php else : ?>
                                <a 
                                    href="detail.php?id=<?= $t['id_transaksi']; ?>"
                                    class="btn-detail"
                                >
                                    Detail
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
=======
<?php
include '../../auth/cek_login.php';
include '../../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= CUSTOMER ================= */
$customer = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_customer
     WHERE id_user = '$id_user'"
);

$c = mysqli_fetch_assoc($customer);

/* ================= FILTER ================= */
$periode = $_GET['periode'] ?? '';
$status  = $_GET['status'] ?? '';

$where = "WHERE id_customer = '" . $c['id_customer'] . "'";

if ($periode != '') {
    $split = explode('-', $periode);
    $tahun = $split[0];
    $bulan = $split[1];

    $where .= " 
        AND bulan_tagihan = '$bulan'
        AND tahun_tagihan = '$tahun'
    ";
}

if ($status != '') {
    $where .= " 
        AND status_pembayaran = '$status'
    ";
}

/* ================= TRANSAKSI ================= */
$query = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM tb_transaksi
     $where
     ORDER BY id_transaksi DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Tagihan Saya</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link class="favicon" rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
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
                <a href="index.php" class="active">
                    <i class="bi bi-receipt"></i> Tagihan Saya
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i> Paket Internet
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li>
                <a href="../../auth/logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Tagihan Saya</h1>
                <p>Riwayat pembayaran dan tagihan internet</p>
            </div>
        </div>

        <!-- ================= TABLE ================= -->
        <div class="table-card">

            <div class="table-header">
                <h3>Daftar Tagihan</h3>

                <form method="GET" class="filter-form">
                    
                    <input 
                        type="month"
                        name="periode"
                        value="<?= $periode; ?>"
                    >

                    <select name="status">
                        
                        <option value="">
                            Semua Status
                        </option>

                        <option 
                            value="Belum Bayar"
                            <?= ($status == 'Belum Bayar') ? 'selected' : ''; ?>
                        >
                            Belum Bayar
                        </option>

                        <option 
                            value="Menunggu Konfirmasi"
                            <?= ($status == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>
                        >
                            Menunggu
                        </option>

                        <option 
                            value="Lunas"
                            <?= ($status == 'Lunas') ? 'selected' : ''; ?>
                        >
                            Lunas
                        </option>

                    </select>

                    <button type="submit" class="btn-orange">
                        Filter
                    </button>

                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($query)) : ?>
                    <tr>
                        <td>
                            <?= $t['kode_invoice']; ?>
                        </td>

                        <td>
                            <?= $t['bulan_tagihan']; ?>/<?= $t['tahun_tagihan']; ?>
                        </td>

                        <td>
                            Rp<?= number_format($t['jumlah_bayar']); ?>
                        </td>

                        <td>
                            <?php if ($t['status_pembayaran'] == 'Lunas') : ?>
                                <span class="status-active">
                                    Lunas
                                </span>
                            <?php elseif ($t['status_pembayaran'] == 'Menunggu Konfirmasi') : ?>
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
                            <?= $t['tanggal_transaksi']; ?>
                        </td>

                        <td>
                            <?php if ($t['status_pembayaran'] == 'Belum Bayar') : ?>
                                <a 
                                    href="bayar.php?id=<?= $t['id_transaksi']; ?>"
                                    class="btn-bayar"
                                >
                                    Bayar
                                </a>
                            <?php else : ?>
                                <a 
                                    href="detail.php?id=<?= $t['id_transaksi']; ?>"
                                    class="btn-detail"
                                >
                                    Detail
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
</html>