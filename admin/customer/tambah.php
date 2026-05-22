<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

// Mengambil data master paket untuk pilihan di dropdown select HTML
$paket = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");

if(isset($_POST['simpan'])){
    $nama     = $_POST['nama_customer'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email    = $_POST['email_customer'];
    $telepon  = $_POST['telepon_customer'];
    $alamat   = $_POST['alamat_customer'];
    $id_paket = $_POST['id_paket'];
    $status   = strtolower($_POST['status_paket']); // 'aktif' atau 'berhenti' sesuai ENUM database
    $sumber   = "offline"; // Menyesuaikan ENUM database ('online', 'offline')

    /* ================= CEK USERNAME DOUBLE ================= */
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username'");

    if(mysqli_num_rows($cek) > 0){
        echo "
        <script>
            alert('Username sudah digunakan! Silakan gunakan username lain.');
            window.location='tambah.php';
        </script>
        ";
        exit;
    }

    /* ================= 1. INSERT KE TABEL USER ================= */
    $insert_user = mysqli_query($koneksi, "INSERT INTO tb_user(username, password, role) VALUES('$username', '$password', 'customer')");
    
    if(!$insert_user) {
        die("Gagal menyimpan data akun user: " . mysqli_error($koneksi));
    }

    // Mengambil ID terakhir yang digenerate oleh tb_user
    $id_user = mysqli_insert_id($koneksi);

    /* ================= 2. INSERT KE TABEL CUSTOMER ================= */
    $insert_customer = mysqli_query($koneksi, "
        INSERT INTO tb_customer (
            id_user, 
            nama_customer, 
            email_customer, 
            telepon_customer, 
            alamat_customer, 
            sumber_customer,
            status_customer
        ) 
        VALUES (
            '$id_user', 
            '$nama', 
            '$email', 
            '$telepon', 
            '$alamat', 
            '$sumber',
            'aktif'
        )
    ");

    if(!$insert_customer) {
        die("Gagal menyimpan data profil customer: " . mysqli_error($koneksi));
    }

    // Mengambil ID terakhir yang digenerate oleh tb_customer
    $id_customer = mysqli_insert_id($koneksi);

    /* ================= 3. INSERT KE TABEL PEMASANGAN (FIX) ================= */
    $tanggal_sekarang = date('Y-m-d');
    $status_pemasangan = ($status == 'aktif') ? 'Selesai' : 'Pending';
    $catatan_pemasangan = "Pendaftaran offline langsung diinput oleh Admin.";

    $insert_pemasangan = mysqli_query($koneksi, "
        INSERT INTO tb_pemasangan (
            id_customer,
            id_paket,
            tanggal_pengajuan,
            tanggal_pasang,
            alamat_pasang,
            status_pemasangan,
            catatan
        ) VALUES (
            '$id_customer',
            '$id_paket',
            '$tanggal_sekarang',
            '$tanggal_sekarang',
            '$alamat',
            '$status_pemasangan',
            '$catatan_pemasangan'
        )
    ");

    if(!$insert_pemasangan) {
        die("Gagal menyimpan riwayat data pemasangan: " . mysqli_error($koneksi));
    }

    /* ================= 4. INSERT KE TABEL LANGGANAN ================= */
    $tanggal_mulai    = date('Y-m-d'); // Tanggal hari ini
    
    // LOGIKA OTOMATIS: Hitung tanggal selesai 30 hari ke depan dari tanggal mulai
    $tanggal_selesai  = date('Y-m-d', strtotime('+30 days', strtotime($tanggal_mulai)));

    $insert_langganan = mysqli_query($koneksi, "
        INSERT INTO tb_langganan (
            id_customer, 
            id_paket, 
            tanggal_mulai, 
            tanggal_selesai, 
            status_langganan
        ) 
        VALUES (
            '$id_customer', 
            '$id_paket', 
            '$tanggal_mulai', 
            '$tanggal_selesai', 
            '$status'
        )
    ");

    if(!$insert_langganan) {
        die("Gagal menyimpan data paket langganan: " . mysqli_error($koneksi));
    }

    /* ================= BERHASIL ================= */
    echo "
    <script>
        alert('Pelanggan offline, riwayat pemasangan, & paket langganan berhasil ditambahkan!');
        window.location='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> Data Pelanggan</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <h1>Tambah Pelanggan</h1>
            <p>Tambah customer offline + otomatis hitung masa aktif 30 hari</p>
        </div>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_customer" required>
                </div>

                <div class="form-group">
                    <label>Username Login</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password Login</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_customer" required>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="telepon_customer" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat_customer" required></textarea>
                </div>

                <div class="form-group">
                    <label>Pilih Paket</label>
                    <select name="id_paket" required>
                        <option value="" disabled selected>-- Pilih Paket Internet --</option>
                        <?php while($p = mysqli_fetch_assoc($paket)) : ?>
                            <option value="<?= $p['id_paket']; ?>"><?= $p['nama_paket']; ?> (<?= $p['kecepatan']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Paket</label>
                    <select name="status_paket" required>
                        <option value="aktif" selected>Aktif (Langsung Jalan)</option>
                        <option value="berhenti">Pending / Berhenti</option>
                    </select>
                </div>

                <button type="submit" name="simpan">Simpan Pelanggan</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>