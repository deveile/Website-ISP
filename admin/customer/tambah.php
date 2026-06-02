<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit;
}

$query_notif = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_notif
    FROM tb_transaksi
    WHERE status_pembayaran = 'menunggu_verifikasi'
");

$total_notif = mysqli_fetch_assoc($query_notif)['total_notif'];

$paket = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");

if(isset($_POST['simpan'])){
    $nama     = $_POST['nama_customer'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email    = $_POST['email_customer'];
    $telepon  = $_POST['telepon_customer'];
    $alamat   = $_POST['alamat_customer'];
    $id_paket = $_POST['id_paket'];
    $status   = strtolower($_POST['status_paket']); 
    $sumber   = "offline"; 

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

    $insert_user = mysqli_query($koneksi, "INSERT INTO tb_user(username, password, role) VALUES('$username', '$password', 'customer')");
    
    if(!$insert_user) {
        die("Gagal menyimpan data akun user: " . mysqli_error($koneksi));
    }

    $id_user = mysqli_insert_id($koneksi);

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

    $id_customer = mysqli_insert_id($koneksi);

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

    $tanggal_mulai    = date('Y-m-d'); 

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

$id_langganan = mysqli_insert_id($koneksi);

$get_paket = mysqli_query(
    $koneksi,
    "SELECT harga FROM tb_paket WHERE id_paket='$id_paket'"
);

$paket_data = mysqli_fetch_assoc($get_paket);

$harga_paket = $paket_data['harga'];

$kode_invoice = 'INV-' . date('YmdHis');

$metode_pembayaran = $_POST['metode_pembayaran'] ?? 'cash';

$bukti_pembayaran = NULL;

if (
    $metode_pembayaran != 'cash'
    && empty($_FILES['bukti_pembayaran']['name'])
) {
    die('Bukti pembayaran wajib diupload untuk Transfer atau QRIS');
}

if (!empty($_FILES['bukti_pembayaran']['name'])) {

    $ext = strtolower(
        pathinfo(
            $_FILES['bukti_pembayaran']['name'],
            PATHINFO_EXTENSION
        )
    );

    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) {
        die('Format bukti pembayaran harus JPG, JPEG, PNG atau WEBP');
    }

    $bukti_pembayaran =
        'BUKTI-' .
        $kode_invoice .
        '-' .
        time() .
        '.' .
        $ext;

    if (!move_uploaded_file(
        $_FILES['bukti_pembayaran']['tmp_name'],
        '../../assets/uploads/bukti/' . $bukti_pembayaran
    )) {
        die('Gagal upload bukti pembayaran');
    }
}

$insert_transaksi = mysqli_query($koneksi, "
    INSERT INTO tb_transaksi (
       id_langganan,
        kode_invoice,
        bulan_tagihan,
        tahun_tagihan,
        jumlah_bayar,
        metode_pembayaran,
        bukti_pembayaran,
        status_pembayaran,
        jenis_transaksi,
        tanggal_bayar
    )
    VALUES (
        '$id_langganan',
        '$kode_invoice',
        '".date('n')."',
        '".date('Y')."',
        '$harga_paket',
        '$metode_pembayaran',
        '$bukti_pembayaran',
        'lunas',
        'baru',
        CURDATE()
    )
");

if(!$insert_transaksi){
    die(
        'Gagal menyimpan data transaksi: '
        . mysqli_error($koneksi)
    );
}
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

        .dashboard-layout {
            display: flex !important;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar-toggle {
            display: flex !important;
            flex-direction: column;
            gap: 4px;
            background: #f0f0f0;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 15px;
            transition: background 0.2s;
        }
        .sidebar-toggle:hover {
            background: #e0e0e0;
        }
        .sidebar-toggle span {
            display: block;
            width: 20px;
            height: 2.5px;
            background-color: #333;
            border-radius: 2px;
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 1000 !important;
                overflow-y: auto !important;
                width: 260px !important;
                min-width: 260px !important;
                max-width: 260px !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .dashboard-content {
                flex-grow: 1 !important;
                margin-left: 260px !important; 
                width: calc(100% - 260px) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .topbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
            }

            .sidebar.collapsed {
                width: 70px !important;
                min-width: 70px !important;
                max-width: 70px !important;
                padding: 24px 8px !important;
            }
            
            .sidebar.collapsed + .dashboard-content {
                margin-left: 70px !important;
                width: calc(100% - 70px) !important;
            }

            .sidebar.collapsed .sidebar-logo h2,
            .sidebar.collapsed ul li a span,
            .sidebar.collapsed .notif-badge {
                display: none !important; 
            }

            .sidebar.collapsed .sidebar-logo {
                justify-content: center !important;
                padding: 0 !important;
            }
            .sidebar.collapsed ul li a {
                justify-content: center !important;
                padding: 12px 0 !important;
            }
            .sidebar.collapsed ul li a i {
                margin: 0 !important;
                font-size: 20px !important; 
            }
        }

        @media (max-width: 991px) {
            .dashboard-layout {
                flex-direction: column !important;
            }
            .sidebar {
                position: fixed !important;
                top: 0; left: 0;
                width: 260px !important;
                min-width: 260px !important;
                height: 100vh !important;
                background: #ffffff !important;
                z-index: 9999 !important;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                padding: 24px !important;
                overflow-y: auto;
            }
            .sidebar.active {
                transform: translateX(0); 
            }
            .dashboard-content {
                width: 100% !important;
                padding: 20px !important;
            }
            .topbar {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                margin-bottom: 24px;
            }
        }

        .form-card-wrapper {
            width: 100%;
            max-width: 650px;
            margin: 30px auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group input[type="email"],
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 10px;
            font-size: 14px;
            color: #334155;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease-in-out;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff6b00;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 10px;
            font-size: 14px;
            color: #334155;
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 15px;
            box-sizing: border-box;
            transition: all 0.2s ease-in-out;
        }
        .form-group select:focus {
            border-color: #ff6b00;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        .payment-section{
    margin-top:30px;
    padding-top:25px;
    border-top:1px solid #e2e8f0;
}

.payment-section h2{
    font-size:22px;
    margin-bottom:8px;
    color:#1e293b;
}

.payment-desc{
    color:#64748b;
    font-size:14px;
    margin-bottom:20px;
}

.payment-box{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:15px;
    margin-bottom:20px;
}

.payment-box h4{
    margin-bottom:10px;
    color:#ff6b00;
}

.payment-box p{
    margin:5px 0;
}

.qris-image{
    max-width:250px;
    width:100%;
    display:block;
    margin:auto;
    border-radius:10px;
}

#buktiPembayaran{
    width:100%;
    padding:12px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#f8fafc;
}
        .status-badge-info {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #bbf7d0;
            width: 100%;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #ff6b00;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.2);
            transition: all 0.2s ease;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background: #e66000;
            box-shadow: 0 6px 16px rgba(255, 107, 0, 0.3);
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
            <li><a href="index.php" class="active"><i class="bi bi-people"></i> <span>Data Pelanggan</span></a></li>
            <li>
                <a href="../transaksi/index.php">
                    <i class="bi bi-credit-card"></i> <span>Data Transaksi</span>
                    <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif; ?></span>
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
                <h1>Tambah Pelanggan</h1>
                <p>Tambah customer offline + otomatis hitung masa aktif 30 hari</p>
            </div>
        </div>

        <div class="form-card-wrapper">
            <form method="POST" enctype="multipart/form-data">
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
                    <div class="status-badge-info">
                        <i class="bi bi-check-circle-fill"></i> Aktif (Langsung Berjalan 30 Hari)
                    </div>
                    <input type="hidden" name="status_paket" value="aktif">
                </div>

             <div class="payment-section">
    <h2>Pembayaran</h2>
    <p class="payment-desc">
        Pilih metode pembayaran pelanggan.
    </p>

   <div class="form-group">
    <label>Metode Pembayaran</label>
    <select name="metode_pembayaran" required>
        <option value="cash">Cash</option>
        <option value="transfer">Transfer Bank</option>
        <option value="qris">QRIS</option>
    </select>
</div>

    <div class="payment-box" id="bankBox" style="display:none;">
        <h4>Transfer Bank</h4>
        <p>BCA : 1234567890</p>
        <p>Mandiri : 9876543210</p>
        <p>A/N PT Anuwani Network</p>
    </div>

    <div class="payment-box" id="qrisBox" style="display:none;">
        <h4>QRIS Payment</h4>
        <img src="../../assets/images/qris.png" class="qris-image" alt="QRIS">
    </div>

    <div class="form-group" id="buktiGroup">
        <label>Upload Bukti Pembayaran</label>
        <input
            type="file"
            name="bukti_pembayaran"
            id="buktiPembayaran"
            accept="image/*">
    </div>
</div>
                    
                <button type="submit" name="simpan">Simpan Pelanggan</button>
            </form>
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

    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }
    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const metode = document.getElementById('metodePembayaran');
    const bankBox = document.getElementById('bankBox');
    const qrisBox = document.getElementById('qrisBox');
    const bukti = document.getElementById('buktiPembayaran');
    const buktiGroup = document.getElementById('buktiGroup');

    function updateMetode() {

        bankBox.style.display = 'none';
        qrisBox.style.display = 'none';

        if (metode.value === 'Transfer Bank') {

            bankBox.style.display = 'block';

            bukti.required = true;
            buktiGroup.style.display = 'block';

        } else if (metode.value === 'QRIS') {

            qrisBox.style.display = 'block';

            bukti.required = true;
            buktiGroup.style.display = 'block';

        } else if (metode.value === 'Cash') {

            bukti.required = false;
            bukti.value = '';

            buktiGroup.style.display = 'none';
        }
    }

    metode.addEventListener('change', updateMetode);

    updateMetode();
});
</script>
</body>
</html>