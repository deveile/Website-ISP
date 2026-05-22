<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

/* ================= AMBIL DATA CUSTOMER ================= */
$query_customer = mysqli_query(
    $koneksi,
    "SELECT *
    FROM tb_customer
    WHERE id_user = '$id_user'
    LIMIT 1"
);

$customer = mysqli_fetch_assoc($query_customer);

/* ================= CEK LANGGANAN ================= */
$langganan = null;
if ($customer) {
    $query_langganan = mysqli_query(
        $koneksi,
        "SELECT
            tb_langganan.*,
            tb_paket.nama_paket,
            tb_paket.kecepatan,
            tb_paket.harga
        FROM tb_langganan
        LEFT JOIN tb_paket
            ON tb_langganan.id_paket = tb_paket.id_paket
        WHERE tb_langganan.id_customer = '".$customer['id_customer']."'
        LIMIT 1"
    );
    $langganan = mysqli_fetch_assoc($query_langganan);
}

/* ================= FILTER ================= */
$periode = $_GET['periode'] ?? '';
$status  = $_GET['status'] ?? '';

/* ================= DEFAULT QUERY ================= */
$where = "WHERE 1=0";

/* ================= JIKA ADA LANGGANAN ================= */
if ($langganan) {
    $where = "
        WHERE id_langganan = '".$langganan['id_langganan']."'
    ";

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
}

/* ================= AMBIL TRANSAKSI ================= */
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

    <link rel="icon" type="image/png" href="../../assets/images/logo.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <script src="../../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
</head>
<body>

<div class="dashboard-layout">

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>

        <ul>
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="index.php" class="active">
                    <i class="bi bi-receipt"></i>
                    Tagihan Saya
                </a>
            </li>
            <li>
                <a href="../paket/index.php">
                    <i class="bi bi-wifi"></i>
                    Paket Internet
                </a>
            </li>
            <li>
                <a href="../profile/index.php">
                    <i class="bi bi-person"></i>
                    Profile
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="dashboard-content">

        <div class="topbar">
            <div>
                <h1>Tagihan Saya</h1>
                <p>Riwayat pembayaran internet</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Tagihan</h3>

                <form method="GET" class="filter-form">
                    <input
                        type="text"
                        id="periode"
                        name="periode"
                        value="<?= $periode; ?>"
                        placeholder="Pilih Periode"
                    >

                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="Belum Bayar" <?= ($status == 'Belum Bayar') ? 'selected' : ''; ?>>Belum Bayar</option>
                        <option value="Menunggu Konfirmasi" <?= ($status == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="Lunas" <?= ($status == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                    </select>

                    <button type="submit" class="btn-orange">Filter</button>
                </form>
            </div>

                            <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Periode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($query && mysqli_num_rows($query) > 0) : ?>
                            <?php while ($t = mysqli_fetch_assoc($query)) : ?>

                                <?php
                                $status_pay = strtolower($t['status_pembayaran']);
                                if ($status_pay == 'lunas') {
                                    $status_class = 'active';
                                } elseif (
                                    $status_pay == 'menunggu konfirmasi' ||
                                    $status_pay == 'menunggu'
                                ) {
                                    $status_class = 'pending';
                                } else {
                                    $status_class = 'belum';
                                }
                                $periode_tagihan = date(
                                    'F Y',
                                    strtotime(
                                        $t['tahun_tagihan'] . '-' .
                                        $t['bulan_tagihan'] . '-01'
                                    )
                                );

                                // FORMAT TANGGAL BAYAR
                                $tanggal_bayar = !empty($t['tanggal_bayar'])
                                    ? $t['tanggal_bayar']
                                    : '-';
                                ?>

                                <tr>
                                    <td><?= $t['kode_invoice']; ?></td>

                                    <td><?= $periode_tagihan; ?></td>

                                    <td>
                                        Rp<?= number_format($t['jumlah_bayar']); ?>
                                    </td>

                                    <td>
                                        <?php
                                        if (
                                            $status_pay == 'lunas'
                                        ) {
                                            $status_text = 'Lunas';
                                            $status_class = 'active';
                                        } elseif (
                                            $status_pay == 'menunggu konfirmasi' ||
                                            $status_pay == 'menunggu'
                                        ) {
                                            $status_text = 'Menunggu Verifikasi';
                                            $status_class = 'pending';
                                        } else {
                                            $status_text = 'Pending';
                                            $status_class = 'belum';
                                        }
                                        ?>
                                        <span class="status-<?= $status_class; ?>">
                                            <?= $status_text; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= $tanggal_bayar; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $status_pay == 'belum bayar' ||
                                            $status_pay == 'belum'
                                        ) : ?>

                                            <a href="bayar.php?id=<?= $t['id_transaksi']; ?>"
                                            class="btn-bayar">
                                                Bayar
                                            </a>

                                        <?php else : ?>

                                            <a href="detail.php?id=<?= $t['id_transaksi']; ?>"
                                            class="btn-detail">
                                                Detail
                                            </a>

                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else : ?>

                            <tr>
                                <td colspan="6" style="text-align:center;">
                                    Belum ada tagihan
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
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