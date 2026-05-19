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

</html>