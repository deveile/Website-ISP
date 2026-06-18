<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_user = $_SESSION['id_user'];

$queryCustomer = mysqli_query($koneksi, 
    "SELECT * FROM tb_customer WHERE id_user = '$id_user'"
);

$customer = mysqli_fetch_assoc($queryCustomer);
$id_customer = $customer['id_customer'];

function prosesReaktivasiPaket(mysqli $koneksi, int $id_paket_tujuan, int $id_langganan) {
    if (!$id_paket_tujuan || !$id_langganan) {
        header("Location: ../paket/index.php"); exit;
    }

    $cek_transaksi = mysqli_query($koneksi, "
        SELECT id_transaksi FROM tb_transaksi 
        WHERE id_langganan = '$id_langganan' 
          AND jenis_transaksi = 'reaktivasi' 
          AND status_pembayaran IN ('belum_bayar','menunggu_verifikasi') 
        LIMIT 1
    ");

    if (mysqli_num_rows($cek_transaksi) > 0) {
        $transaksi_lama = mysqli_fetch_assoc($cek_transaksi);
        header("Location: ../tagihan/bayar.php?id=" . $transaksi_lama['id_transaksi']);
        exit;
    }

    $q_paket = mysqli_query($koneksi, 
        "SELECT harga FROM tb_paket WHERE id_paket = '$id_paket_tujuan'"
    );

    $paket_baru = mysqli_fetch_assoc($q_paket);
    if (!$paket_baru) {
        header("Location: ../paket/index.php"); exit;
    }

    $total_bayar = $paket_baru['harga'];
    $bulan = date('n');
    $tahun = date('Y');
    $kode_invoice = "INV-RE-" . time();

    $sql_insert = "
        INSERT INTO tb_transaksi (
            id_langganan, id_paket_baru, kode_invoice, 
            bulan_tagihan, tahun_tagihan, jumlah_bayar, 
            status_pembayaran, jenis_transaksi
        ) VALUES (
            '$id_langganan', '$id_paket_tujuan', '$kode_invoice', 
            '$bulan', '$tahun', '$total_bayar', 
            'belum_bayar', 'reaktivasi'
        )
    ";

    if (mysqli_query($koneksi, $sql_insert)) {
        $id_transaksi_baru = mysqli_insert_id($koneksi);

        mysqli_query($koneksi, "
            UPDATE tb_langganan 
            SET id_paket = '$id_paket_tujuan' 
            WHERE id_langganan = '$id_langganan'
        ");
        
        header("Location: ../tagihan/bayar.php?id=$id_transaksi_baru"); exit;
    } else {
        echo "<script>
            alert('Gagal memproses reaktivasi paket');
            window.location='../paket/index.php';
        </script>";
        exit;
    }
}

if (isset($_GET['aksi'])) {
    
    if ($_GET['aksi'] === 'upgrade') {
        $id_paket_tujuan = (int)($_GET['id_paket'] ?? 0);
        $id_langganan    = (int)($_GET['id_langganan'] ?? 0);

        if (!$id_paket_tujuan || !$id_langganan) {
            header("Location: ../paket/index.php"); exit;
        }

        $cek_transaksi = mysqli_query($koneksi, "
            SELECT id_transaksi FROM tb_transaksi 
            WHERE id_langganan = '$id_langganan' 
              AND jenis_transaksi = 'upgrade' 
              AND status_pembayaran IN ('belum_bayar','menunggu_verifikasi') 
            LIMIT 1
        ");

        if (mysqli_num_rows($cek_transaksi) > 0) {
            $transaksi_lama = mysqli_fetch_assoc($cek_transaksi);
            header("Location: ../tagihan/bayar.php?id=" . $transaksi_lama['id_transaksi']);
            exit;
        }

        $q_paket = mysqli_query($koneksi, 
            "SELECT harga FROM tb_paket WHERE id_paket = '$id_paket_tujuan'"
        );

        $paket_baru = mysqli_fetch_assoc($q_paket);
        if (!$paket_baru) {
            header("Location: ../paket/index.php"); exit;
        }

        $total_bayar = $paket_baru['harga'];
        $bulan = date('n');
        $tahun = date('Y');
        $kode_invoice = "INV-UPG-" . time();

        $sql_insert = "
            INSERT INTO tb_transaksi (
                id_langganan, id_paket_baru, kode_invoice, 
                bulan_tagihan, tahun_tagihan, jumlah_bayar, 
                status_pembayaran, jenis_transaksi
            ) VALUES (
                '$id_langganan', '$id_paket_tujuan', '$kode_invoice', 
                '$bulan', '$tahun', '$total_bayar', 
                'belum_bayar', 'upgrade'
            )
        ";

        if (mysqli_query($koneksi, $sql_insert)) {
            $id_transaksi_baru = mysqli_insert_id($koneksi);
            header("Location: ../tagihan/bayar.php?id=$id_transaksi_baru"); exit;
        } else {
            echo "<script>
                alert('Gagal memproses upgrade paket');
                window.location='../paket/index.php';
            </script>";
            exit;
        }
    }

    if ($_GET['aksi'] === 'reaktivasi') {
        $id_paket_tujuan = (int)($_GET['id_paket'] ?? 0);
        $id_langganan    = (int)($_GET['id_langganan'] ?? 0);
        prosesReaktivasiPaket($koneksi, $id_paket_tujuan, $id_langganan);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenis_aksi = $_POST['aksi'] ?? '';
    $id_langganan = (int)($_POST['id_langganan'] ?? 0);

    if ($jenis_aksi === 'reaktivasi' || $id_langganan > 0) {
        $id_paket_tujuan = (int)($_POST['id_paket'] ?? 0);
        prosesReaktivasiPaket($koneksi, $id_paket_tujuan, $id_langganan);
    }

    // MURNI PASANG BARU
    $id_paket = (int)($_POST['id_paket'] ?? 0);
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']  ?? ''));
    $catatan  = mysqli_real_escape_string($koneksi, trim($_POST['catatan'] ?? ''));

    $insert_pasang = mysqli_query($koneksi, "
        INSERT INTO tb_pemasangan (
            id_customer, id_paket, tanggal_pengajuan, tanggal_pasang, alamat_pasang, status_pemasangan, catatan
        ) VALUES (
            '$id_customer', '$id_paket', NOW(), NULL, '$alamat', 'menunggu', '$catatan'
        )
    ");

    if (!$insert_pasang) {
        die('Gagal simpan pemasangan: ' . mysqli_error($koneksi));
    }

    header("Location: berhasil.php");
    exit;
}
?>