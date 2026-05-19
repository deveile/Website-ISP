<<<<<<< HEAD
<?php
include 'koneksi.php';

$paket = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_paket
    ORDER BY id_paket DESC
    LIMIT 3"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuwani.Net</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/template/bootslander/assets/vendor/bootstrap/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/template/bootslander/assets/vendor/bootstrap-icons/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- =========================================================
    NAVBAR
    ========================================================= -->
    <header class="navbar-custom">
        <div class="container">
            <div class="navbar-wrapper">
                <div class="logo-area">
                    <img src="assets/images/logo.png" class="main-logo">
                    <h2>Anuwani Network</h2>
                </div>

                <nav>
                    <ul>
                        <li><a href="#hero" class="active">Beranda</a></li>
                        <li><a href="#paket">Paket Internet</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                        <li><a href="auth/login.php">Login</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- =========================================================
    HERO
    ========================================================= -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-text">
                        <h1>Menghubungkan Subang Dengan Kecepatan dan Hati</h1>
                        <p>
                            Internet fiber optic cepat, stabil, dan terjangkau
                            untuk kebutuhan rumah dan bisnis di Kabupaten Subang.
                        </p>
                        <a href="auth/login.php" class="btn-orange">Pasang Sekarang</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image-area">
                        <div class="hero-bg"></div>
                        <img src="assets/images/logo.png" class="hero-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================
    PAKET
    ========================================================= -->
    <section id="paket" class="section-space">
        <div class="container">
            <div class="section-title">
                <h2>Paket Internet</h2>
                <p>Pilih paket terbaik untuk kebutuhan Anda</p>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($paket) > 0) { ?>
                    <?php while ($data = mysqli_fetch_assoc($paket)) { ?>
                        <div class="col-lg-4">
                            <div class="paket-card">
                                <h3><?php echo $data['nama_paket']; ?></h3>
                                <p><?php echo $data['kecepatan']; ?></p>
                                <h1>Rp <?php echo number_format($data['harga']); ?></h1>
                                <span><?php echo nl2br($data['deskripsi']); ?></span>
                                <br>
                                <a href="auth/login.php" class="btn-paket">Pesan Sekarang</a>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12">
                        <div class="empty-paket">
                            <i class="bi bi-wifi-off"></i>
                            <h3>Belum Ada Paket</h3>
                            <p>Admin belum menambahkan paket internet</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- =========================================================
    TENTANG
    ========================================================= -->
    <section id="tentang" class="section-space">
        <div class="container">
            <div class="section-title">
                <h2>Mengapa Pilih Kami?</h2>
                <p>Layanan internet cepat, stabil, dan terpercaya</p>
            </div>

            <div class="why-wrapper">
                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-wifi"></i></div>
                    <h3>Kecepatan Stabil</h3>
                    <p>Koneksi tanpa gangguan</p>
                </div>

                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-shield-check"></i></div>
                    <h3>Keamanan Jaringan</h3>
                    <p>Proteksi maksimal</p>
                </div>

                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-infinity"></i></div>
                    <h3>Kuota Tidak Terbatas</h3>
                    <p>Streaming sepuasnya</p>
                </div>

                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-cash-coin"></i></div>
                    <h3>Harga Terjangkau</h3>
                    <p>Sesuai budget Anda</p>
                </div>

                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-headset"></i></div>
                    <h3>Layanan 24 Jam</h3>
                    <p>Siap membantu kapanpun</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================
    KONTAK
    ========================================================= -->
    <section id="kontak" class="section-space">
        <div class="container">
            <div class="section-title">
                <h2>Hubungi Kami</h2>
                <p>Kami siap membantu kebutuhan internet Anda</p>
            </div>

            <div class="contact-wrapper">
                <div class="contact-card">
                    <div class="contact-icon"><i class="bi bi-headset"></i></div>
                    <h3>Customer Service</h3>
                    <p>0812-3456-7890</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                    <h3>Email</h3>
                    <p>info@anuwani.net</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                    <h3>Alamat</h3>
                    <p>Subang, Jawa Barat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================
    FOOTER
    ========================================================= -->
    <footer class="footer-custom">
        <div class="container">
            <p>© 2026 Anuwani Network</p>
        </div>
    </footer>

</body>

=======
<?php
include '../auth/cek_login.php';
include '../koneksi.php';

// Proteksi Halaman Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* ================= AMBIL DATA STATISTIK ================= */
$total_customer  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_customer"));
$total_paket     = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_paket"));
$total_transaksi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_pemasangan"));
$pending         = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tb_pemasangan WHERE status_pemasangan='Pending'"));

// Hitung Total Pendapatan (Join Table)
$query_income = "SELECT SUM(tb_paket.harga) AS total_pendapatan 
                 FROM tb_pemasangan 
                 JOIN tb_paket ON tb_pemasangan.id_paket = tb_paket.id_paket 
                 WHERE tb_pemasangan.status_pemasangan='Selesai'";
$pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, $query_income));
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link rel="icon" type="image/png" href="../assets/images/logo.png">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
  <div class="dashboard-layout">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">
      <div class="sidebar-logo">
        <img src="../assets/images/logo.png" alt="Logo">
        <h2>Anuwani</h2>
      </div>

      <ul>
        <li>
          <a href="index.php" class="active">
            <i class="bi bi-grid"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="paket/index.php">
            <i class="bi bi-wifi"></i> Kelola Paket
          </a>
        </li>
        <li>
          <a href="customer/index.php">
            <i class="bi bi-people"></i> Data Pelanggan
          </a>
        </li>
        <li>
          <a href="transaksi/index.php">
            <i class="bi bi-credit-card"></i> Data Transaksi
          </a>
        </li>
        <li>
          <a href="admin_user/index.php">
            <i class="bi bi-person-plus"></i> Tambah Admin
          </a>
        </li>
        <li>
          <a href="../auth/logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="dashboard-content">

      <!-- TOPBAR -->
      <div class="topbar">
        <div>
          <h1>Dashboard Admin</h1>
          <p>Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong></p>
        </div>
      </div>

      <!-- KARTU PENDAPATAN UTAMA -->
      <div class="income-card">
        <div>
          <h5>Total Pendapatan</h5>
          <h1>Rp <?php echo number_format($pendapatan['total_pendapatan'] ?? 0); ?></h1>
        </div>
        <i class="bi bi-cash-stack"></i>
      </div>

      <!-- GRID KARTU STATISTIK -->
      <div class="admin-card-grid">

        <!-- Total Pelanggan -->
        <div class="admin-card">
          <div>
            <h5>Total Pelanggan</h5>
            <h2><?php echo $total_customer; ?></h2>
          </div>
          <i class="bi bi-people-fill"></i>
        </div>

        <!-- Transaksi Pending -->
        <div class="admin-card">
          <div>
            <h5>Transaksi Pending</h5>
            <h2><?php echo $pending; ?></h2>
          </div>
          <i class="bi bi-hourglass-split"></i>
        </div>

        <!-- Total Paket -->
        <div class="admin-card">
          <div>
            <h5>Total Paket</h5>
            <h2><?php echo $total_paket; ?></h2>
          </div>
          <i class="bi bi-wifi"></i>
        </div>

        <!-- Total Transaksi -->
        <div class="admin-card">
          <div>
            <h5>Total Transaksi</h5>
            <h2><?php echo $total_transaksi; ?></h2>
          </div>
          <i class="bi bi-credit-card"></i>
        </div>

      </div> <!-- End Card Grid -->

    </div> <!-- End Dashboard Content -->
  </div> <!-- End Dashboard Layout -->
</body>

>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
</html>