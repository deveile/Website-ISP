<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'customer') {
    header("Location: ../../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? '';

$queryCustomer = mysqli_query($koneksi, "SELECT * FROM tb_customer WHERE id_user = '$id_user'");
$customer = mysqli_fetch_assoc($queryCustomer);
$id_customer = $customer['id_customer'] ?? '';

$cek_langganan = mysqli_query($koneksi, "
    SELECT l.*, p.harga
    FROM tb_langganan l
    JOIN tb_paket p ON l.id_paket = p.id_paket
    WHERE l.id_customer = '$id_customer'
    AND LOWER(l.status_langganan) IN ('aktif','suspend')
    LIMIT 1
    ");
$langganan = mysqli_fetch_assoc($cek_langganan);

$cek_langganan_lama = mysqli_query($koneksi, "
    SELECT * FROM tb_langganan 
    WHERE id_customer = '$id_customer' 
    AND LOWER(status_langganan) IN ('nonaktif', 'dicabut', 'berhenti') 
    ORDER BY id_langganan DESC LIMIT 1
");
$langganan_lama = mysqli_fetch_assoc($cek_langganan_lama);

$is_pending = false;
if ($langganan) {
    $cek_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi 
                    WHERE id_langganan = '".$langganan['id_langganan']."' 
                    AND jenis_transaksi = 'upgrade' 
                    AND status_pembayaran != 'lunas'");
    $row_p = mysqli_fetch_assoc($cek_pending);
    $is_pending = ($row_p['total'] > 0);
}

$data = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE status='aktif' ORDER BY id_paket DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Internet</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../assets/js/script.js" defer></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .dashboard-layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            transition: all 0.3s ease;
            background: #fff;
            overflow: hidden;
        }

        .sidebar-toggle {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #f0f0f0;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .sidebar-toggle span {
            width: 20px;
            height: 2.5px;
            background: #333;
            border-radius: 2px;
        }

        @media (min-width: 992px) {
            .sidebar.collapsed {
                width: 70px !important;
                min-width: 70px !important;
                max-width: 70px !important;
                padding: 24px 8px !important;
            }

            .sidebar.collapsed ul li a span,
            .sidebar.collapsed .sidebar-logo h2,
            .sidebar.collapsed .notif-badge {
                display: none;
            }

            .sidebar.collapsed ul li a {
                justify-content: center;
            }

            .sidebar.collapsed ul li a i {
                margin: 0 !important;
                font-size: 20px !important;
            }

            .dashboard-content {
                flex: 1;
                transition: all 0.3s ease;
            }
        }

        @media (max-width: 991px) {
            .dashboard-layout {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                z-index: 9999;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .dashboard-content {
                width: 100%;
                padding: 20px;
            }
        }

        .notif-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ef4444;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            margin-left: auto;
        }

        .paket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            padding: 20px 0;
        }

        .customer-paket-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .customer-paket-card p {
            flex-grow: 1;
            margin-bottom: 20px;
        }

        .customer-paket-card .btn-orange,
        .btn-disabled {
            margin-top: auto;
            display: inline-block;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
        }

        .btn-disabled {
            background-color: #cbd5e1;
            color: #64748b;
            border: none;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../../assets/images/logo.png">
            <h2>Anuwani</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="../index.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../tagihan/index.php">
                    <i class="bi bi-receipt"></i>
                    <span>Tagihan Saya</span>
                </a>
            </li>
            <li>
                <a href="../paket/index.php" class="active">
                    <i class="bi bi-wifi"></i>
                    <span>Paket Internet</span>
                </a>
            </li>
            <li>
                <a href="../profile/index.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="openLogoutModal()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>   
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div>
                <h1>Paket Internet</h1>
                <p>Pilih paket terbaik untuk kebutuhan Anda</p>
            </div>
        </div>

        <div class="paket-grid">
            <?php while ($paket = mysqli_fetch_assoc($data)) : ?>
                <div class="customer-paket-card">
                    <h3><?= $paket['nama_paket']; ?></h3>
                    <h1><?= $paket['kecepatan']; ?> </h1>
                    <h2>Rp <?= number_format($paket['harga']); ?></h2>
                    <p><?= nl2br($paket['deskripsi']); ?></p>
                    
                    <?php if (!$langganan) : ?>
                        <?php if ($langganan_lama) : ?>
                            <a href="../pemasangan/proses.php?id_paket=<?= $paket['id_paket']; ?>&id_langganan=<?= $langganan_lama['id_langganan']; ?>&aksi=reaktivasi" class="btn-orange">Langganan Lagi</a>
                        <?php else : ?>
                            <a href="../pemasangan/index.php?id=<?= $paket['id_paket']; ?>" class="btn-orange">Pesan Sekarang</a>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ($langganan['id_paket'] == $paket['id_paket']) : ?>
                        <button class="btn-disabled" disabled>
                            <i class="bi bi-check-circle-fill"></i> Paket Aktif Anda
                        </button>

                        <?php elseif ($paket['harga'] <= $langganan['harga']) : ?>
                        <button class="btn-disabled" disabled>
                            Tidak Bisa Upgrade
                        </button>

                        <?php elseif ($is_pending) : ?>
                        <button class="btn-disabled" disabled>
                            Upgrade Menunggu Verifikasi
                        </button>

                        <?php else : ?>
                        <a href="../pemasangan/proses.php?id_paket=<?= $paket['id_paket']; ?>&id_langganan=<?= $langganan['id_langganan']; ?>&aksi=upgrade"
                        class="btn-orange">
                            Upgrade Paket
                        </a>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
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
                if (window.innerWidth < 991) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    });
</script>
</body>
</html>