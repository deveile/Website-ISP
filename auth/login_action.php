<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php"); exit;
}

$username = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

$query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' LIMIT 1");
$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['id_user']  = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role']     = $data['role'];

    $icon = 'success';
    $title = 'Login Berhasil';

    if ($data['role'] === 'admin') {
        $adm = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_admin WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']     = $adm['nama_admin'] ?? 'Admin';
        $_SESSION['id_admin'] = $adm['id_admin'] ?? null;

        // Otomatis generate tagihan saat admin login
        generate_tagihan_jatuh_tempo($koneksi);

        $text = 'Selamat datang Admin';
        $redirect = '/website-isp/admin/index.php';
        
    } elseif ($data['role'] === 'teknisi') {
        // AMBIL DATA DARI TB_TEKNISI JIKA ROLE ADALAH TEKNISI
        $tek = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_teknisi WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']       = $tek['nama_teknisi'] ?? 'Teknisi';
        $_SESSION['id_teknisi'] = $tek['id_teknisi'] ?? null;

        $text = 'Selamat datang Teknisi';
        $redirect = '/website-isp/teknisi/index.php'; // Sesuaikan dengan folder dashboard teknisimu
        
    } else {
        // JIKA BUKAN ADMIN/TEKNISI, MAKA ROLE ADALAH CUSTOMER
        $cust = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM tb_customer WHERE id_user = {$data['id_user']} LIMIT 1
        "));
        $_SESSION['nama']        = $cust['nama_customer'] ?? $data['username'];
        $_SESSION['id_customer'] = $cust['id_customer'] ?? null;

        $text = 'Selamat datang';
        $redirect = '/website-isp/customer/index.php';
    }
} else {
    $icon = 'error';
    $title = 'Login Gagal';
    $text = !$data ? 'Username tidak ditemukan' : 'Password salah';
    $redirect = 'login.php';
}

function generate_tagihan_jatuh_tempo($koneksi) {
    $hari_ini = date('Y-m-d');
    $q = mysqli_query($koneksi, "
        SELECT tl.id_langganan, tl.tanggal_selesai, tp.harga 
        FROM tb_langganan tl
        INNER JOIN tb_paket tp ON tl.id_paket = tp.id_paket
        INNER JOIN tb_customer tc ON tl.id_customer = tc.id_customer
        WHERE tl.status_langganan = 'aktif'
          AND tp.status = 'aktif'
          AND tl.tanggal_selesai <= '$hari_ini'
    ");

    if (!$q || mysqli_num_rows($q) === 0) return;

    while ($row = mysqli_fetch_assoc($q)) {
        $id_langganan  = (int)$row['id_langganan'];
        $harga         = (int)$row['harga'];
        $tgl_selesai   = $row['tanggal_selesai'];
        $bulan_tagihan = (int)date('m', strtotime($tgl_selesai));
        $tahun_tagihan = (int)date('Y', strtotime($tgl_selesai));

        $cek_ada = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT id_transaksi FROM tb_transaksi
            WHERE id_langganan = $id_langganan
              AND bulan_tagihan = $bulan_tagihan
              AND tahun_tagihan = $tahun_tagihan
            LIMIT 1
        "));

        if ($cek_ada) continue;

        $kode_invoice = 'INV-' . $tahun_tagihan . sprintf('%02d', $bulan_tagihan) . '-' . 
            strtoupper(substr(md5($id_langganan . microtime() . rand()), 0, 6));

        mysqli_query($koneksi, "
            INSERT INTO tb_transaksi (
                id_langganan, kode_invoice, bulan_tagihan, 
                tahun_tagihan, jumlah_bayar, status_pembayaran, created_at
            ) VALUES (
                $id_langganan, '$kode_invoice', $bulan_tagihan, 
                $tahun_tagihan, $harga, 'belum_bayar', NOW()
            )
        ");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: '<?= $icon; ?>',
    title: '<?= $title; ?>',
    text: '<?= $text; ?>',
    confirmButtonColor: '#ff7a00'
}).then(() => {
    window.location.href = '<?= $redirect; ?>';
});
</script>
</body>
</html>