-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2026 at 02:36 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data_isp`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_admin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email_admin` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `id_user`, `nama_admin`, `email_admin`) VALUES
(1, 1, 'Super Admin Master', 'admin@ispnet.com');

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id_customer` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_customer` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_customer` text COLLATE utf8mb4_general_ci NOT NULL,
  `telepon_customer` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_customer` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sumber_customer` enum('online','offline') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_customer` enum('aktif','pending','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id_customer`, `id_user`, `nama_customer`, `alamat_customer`, `telepon_customer`, `email_customer`, `sumber_customer`, `status_customer`, `created_at`) VALUES
(1, 44, 'Rizky Permata', 'Jl. Flamboyan Gg. Kelinci 33', '081395997823', 'rizkypermata47@gmail.com', 'offline', 'aktif', '2025-11-22 01:00:00'),
(2, 45, 'Ahmad Prasetyo', 'Cluster Hijau Residence No. 85', '081335341536', 'ahmadprasetyo65@gmail.com', 'online', 'aktif', '2026-03-05 01:00:00'),
(3, 46, 'Yusuf Saputra', 'Jl. Gajah Mada Blok 89', '081312357021', 'yusufsaputra73@gmail.com', 'online', 'aktif', '2025-04-25 01:00:00'),
(4, 47, 'Agus Lestari', 'Jl. Flamboyan Gg. Kelinci 86', '081317182491', 'aguslestari84@gmail.com', 'online', 'aktif', '2025-07-13 01:00:00'),
(5, 48, 'Yusuf Kusuma', 'Jl. Merdeka No. 28', '081331490169', 'yusufkusuma48@gmail.com', 'offline', 'aktif', '2026-02-16 01:00:00'),
(6, 49, 'Rian Permata', 'Jl. Gajah Mada Blok 40', '081373853875', 'rianpermata88@gmail.com', 'offline', 'aktif', '2025-09-09 01:00:00'),
(7, 50, 'Fitri Wijaya', 'Perumahan Griya Asri Blok 57', '081381884364', 'fitriwijaya97@gmail.com', 'offline', 'aktif', '2026-03-23 01:00:00'),
(8, 51, 'Dian Marlina', 'Jl. Merdeka No. 9', '081322651512', 'dianmarlina17@gmail.com', 'online', 'aktif', '2026-02-11 01:00:00'),
(9, 52, 'Dedi Gunawan', 'Kampung Baru Raya No. 100', '081330985213', 'dedigunawan46@gmail.com', 'offline', 'aktif', '2026-02-16 01:00:00'),
(10, 53, 'Santi Prasetyo', 'Cluster Hijau Residence No. 38', '081336768432', 'santiprasetyo35@gmail.com', 'online', 'aktif', '2026-04-23 01:00:00'),
(11, 54, 'Eko Rahma', 'Jl. Flamboyan Gg. Kelinci 83', '081362437272', 'ekorahma27@gmail.com', 'offline', 'aktif', '2025-09-10 01:00:00'),
(12, 55, 'Rini Hidayat', 'Cluster Hijau Residence No. 40', '081383689309', 'rinihidayat31@gmail.com', 'offline', 'aktif', '2025-10-12 01:00:00'),
(13, 56, 'Ahmad Hidayat', 'Jl. Gajah Mada Blok 52', '081371693991', 'ahmadhidayat75@gmail.com', 'offline', 'aktif', '2025-10-14 01:00:00'),
(14, 57, 'Yusuf Fauzi', 'Jl. Merdeka No. 54', '081369544853', 'yusuffauzi52@gmail.com', 'offline', 'aktif', '2025-08-22 01:00:00'),
(15, 58, 'Ahmad Prasetyo', 'Kampung Baru Raya No. 28', '081381629762', 'ahmadprasetyo59@gmail.com', 'online', 'aktif', '2025-08-26 01:00:00'),
(16, 59, 'Siti Widodo', 'Kampung Baru Raya No. 77', '081321145505', 'sitiwidodo53@gmail.com', 'offline', 'aktif', '2025-03-10 01:00:00'),
(17, 60, 'Fitri Pratama', 'Jl. Merdeka No. 25', '081335896493', 'fitripratama95@gmail.com', 'online', 'aktif', '2025-09-15 01:00:00'),
(18, 61, 'Putri Sari', 'Perumahan Griya Asri Blok 80', '081345101627', 'putrisari90@gmail.com', 'online', 'aktif', '2026-04-17 01:00:00'),
(19, 62, 'Budi Saputra', 'Jl. Gajah Mada Blok 33', '081343713140', 'budisaputra62@gmail.com', 'online', 'aktif', '2025-07-24 01:00:00'),
(20, 63, 'Budi Gunawan', 'Perumahan Griya Asri Blok 39', '081373016626', 'budigunawan47@gmail.com', 'online', 'aktif', '2025-08-05 01:00:00'),
(21, 64, 'Putri Wijaya', 'Perumahan Griya Asri Blok 92', '081340767877', 'putriwijaya61@gmail.com', 'offline', 'aktif', '2026-01-28 01:00:00'),
(22, 65, 'Dian Marlina', 'Perumahan Griya Asri Blok 87', '081355972668', 'dianmarlina42@gmail.com', 'online', 'aktif', '2026-02-21 01:00:00'),
(23, 66, 'Indah Rahayu', 'Jl. Merdeka No. 46', '081322307378', 'indahrahayu20@gmail.com', 'online', 'aktif', '2026-04-11 01:00:00'),
(24, 67, 'Amalia Marlina', 'Jl. Gajah Mada Blok 42', '081384184477', 'amaliamarlina59@gmail.com', 'online', 'aktif', '2025-02-27 01:00:00'),
(25, 68, 'Rian Amelia', 'Jl. Merdeka No. 70', '081389189217', 'rianamelia23@gmail.com', 'online', 'aktif', '2026-03-28 01:00:00'),
(26, 69, 'Joko Saputra', 'Jl. Flamboyan Gg. Kelinci 21', '081383860617', 'jokosaputra11@gmail.com', 'offline', 'aktif', '2026-02-28 01:00:00'),
(27, 70, 'Siti Fauzi', 'Perumahan Griya Asri Blok 8', '081310400458', 'sitifauzi63@gmail.com', 'online', 'aktif', '2026-02-04 01:00:00'),
(28, 71, 'Agus Purnomo', 'Kampung Baru Raya No. 94', '081345574476', 'aguspurnomo82@gmail.com', 'online', 'aktif', '2025-09-16 01:00:00'),
(29, 72, 'Rini Prasetyo', 'Jl. Gajah Mada Blok 83', '081357364887', 'riniprasetyo24@gmail.com', 'online', 'aktif', '2026-03-10 01:00:00'),
(30, 73, 'Rian Santoso', 'Kampung Baru Raya No. 41', '081363582524', 'riansantoso12@gmail.com', 'online', 'aktif', '2025-06-17 01:00:00'),
(31, 74, 'Nina Rahma', 'Perumahan Griya Asri Blok 2', '081338791710', 'ninarahma51@gmail.com', 'online', 'aktif', '2025-06-15 01:00:00'),
(32, 75, 'Rini Permata', 'Jl. Merdeka No. 42', '081346080984', 'rinipermata58@gmail.com', 'online', 'aktif', '2025-03-28 01:00:00'),
(33, 76, 'Rizky Fauzi', 'Kampung Baru Raya No. 99', '081371671819', 'rizkyfauzi93@gmail.com', 'offline', 'aktif', '2025-06-02 01:00:00'),
(34, 77, 'Sri Rahayu', 'Kampung Baru Raya No. 41', '081385828744', 'srirahayu18@gmail.com', 'offline', 'aktif', '2025-09-24 01:00:00'),
(35, 78, 'Ahmad Fauzi', 'Cluster Hijau Residence No. 2', '081347756484', 'ahmadfauzi59@gmail.com', 'offline', 'aktif', '2025-08-21 01:00:00'),
(36, 79, 'Dewi Saputra', 'Jl. Flamboyan Gg. Kelinci 44', '081356144812', 'dewisaputra43@gmail.com', 'online', 'aktif', '2026-02-15 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_langganan`
--

CREATE TABLE `tb_langganan` (
  `id_langganan` int NOT NULL,
  `id_customer` int NOT NULL,
  `id_paket` int NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_langganan` enum('aktif','suspend','berhenti','menunggu_verifikasi','dicabut') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_langganan`
--

INSERT INTO `tb_langganan` (`id_langganan`, `id_customer`, `id_paket`, `tanggal_mulai`, `tanggal_selesai`, `status_langganan`) VALUES
(1, 1, 3, '2025-11-22', NULL, 'aktif'),
(2, 2, 2, '2026-03-05', NULL, 'aktif'),
(3, 3, 2, '2025-04-25', NULL, 'aktif'),
(4, 4, 3, '2025-07-13', NULL, 'aktif'),
(5, 5, 1, '2026-02-16', NULL, 'aktif'),
(6, 6, 3, '2025-09-09', NULL, 'aktif'),
(7, 7, 2, '2026-03-23', NULL, 'aktif'),
(8, 8, 3, '2026-02-11', NULL, 'aktif'),
(9, 9, 1, '2026-02-16', NULL, 'aktif'),
(10, 10, 1, '2026-04-23', NULL, 'aktif'),
(11, 11, 3, '2025-09-10', NULL, 'aktif'),
(12, 12, 2, '2025-10-12', NULL, 'aktif'),
(13, 13, 2, '2025-10-14', NULL, 'aktif'),
(14, 14, 1, '2025-08-22', NULL, 'aktif'),
(15, 15, 3, '2025-08-26', NULL, 'aktif'),
(16, 16, 2, '2025-03-10', NULL, 'aktif'),
(17, 17, 1, '2025-09-15', NULL, 'aktif'),
(18, 18, 2, '2026-04-17', NULL, 'aktif'),
(19, 19, 2, '2025-07-24', NULL, 'aktif'),
(20, 20, 1, '2025-08-05', NULL, 'aktif'),
(21, 21, 2, '2026-01-28', NULL, 'aktif'),
(22, 22, 1, '2026-02-21', NULL, 'aktif'),
(23, 23, 1, '2026-04-11', NULL, 'aktif'),
(24, 24, 1, '2025-02-27', NULL, 'aktif'),
(25, 25, 1, '2026-03-28', NULL, 'aktif'),
(26, 26, 2, '2026-02-28', NULL, 'aktif'),
(27, 27, 3, '2026-02-04', NULL, 'aktif'),
(28, 28, 1, '2025-09-16', NULL, 'aktif'),
(29, 29, 3, '2026-03-10', NULL, 'aktif'),
(30, 30, 1, '2025-06-17', NULL, 'aktif'),
(31, 31, 1, '2025-06-15', NULL, 'aktif'),
(32, 32, 3, '2025-03-28', NULL, 'aktif'),
(33, 33, 2, '2025-06-02', NULL, 'aktif'),
(34, 34, 3, '2025-09-24', NULL, 'aktif'),
(35, 35, 1, '2025-08-21', NULL, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tb_paket`
--

CREATE TABLE `tb_paket` (
  `id_paket` int NOT NULL,
  `nama_paket` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `kecepatan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_paket`
--

INSERT INTO `tb_paket` (`id_paket`, `nama_paket`, `harga`, `kecepatan`, `deskripsi`, `status`) VALUES
(1, 'Paket Hemat', 150000, '10 Mbps', 'Cocok untuk kebutuhan harian & browsing ringan', 'aktif'),
(2, 'Paket Populer', 250000, '20 Mbps', 'Paling pas untuk streaming keluarga & WFH', 'aktif'),
(3, 'Paket Premium', 400000, '50 Mbps', 'Super cepat, anti lag untuk gaming & download intensif', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemasangan`
--

CREATE TABLE `tb_pemasangan` (
  `id_pemasangan` int NOT NULL,
  `id_customer` int NOT NULL,
  `id_paket` int NOT NULL,
  `id_teknisi` int DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_pasang` date DEFAULT NULL,
  `alamat_pasang` text COLLATE utf8mb4_general_ci NOT NULL,
  `status_pemasangan` enum('menunggu','diproses','terpasang','dibatalkan') COLLATE utf8mb4_general_ci DEFAULT 'menunggu',
  `bukti_foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pemasangan`
--

INSERT INTO `tb_pemasangan` (`id_pemasangan`, `id_customer`, `id_paket`, `id_teknisi`, `tanggal_pengajuan`, `tanggal_pasang`, `alamat_pasang`, `status_pemasangan`, `bukti_foto`, `catatan`, `created_at`) VALUES
(1, 1, 3, 1, '2025-11-22', '2025-11-22', 'Jl. Flamboyan Gg. Kelinci 33', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(2, 2, 2, 1, '2026-03-05', '2026-03-05', 'Cluster Hijau Residence No. 85', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(3, 3, 2, 1, '2025-04-25', '2025-04-25', 'Jl. Gajah Mada Blok 89', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(4, 4, 3, 1, '2025-07-13', '2025-07-13', 'Jl. Flamboyan Gg. Kelinci 86', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(5, 5, 1, 1, '2026-02-16', '2026-02-16', 'Jl. Merdeka No. 28', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(6, 6, 3, 1, '2025-09-09', '2025-09-09', 'Jl. Gajah Mada Blok 40', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(7, 7, 2, 1, '2026-03-23', '2026-03-23', 'Perumahan Griya Asri Blok 57', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(8, 8, 3, 1, '2026-02-11', '2026-02-11', 'Jl. Merdeka No. 9', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(9, 9, 1, 1, '2026-02-16', '2026-02-16', 'Kampung Baru Raya No. 100', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(10, 10, 1, 1, '2026-04-23', '2026-04-23', 'Cluster Hijau Residence No. 38', 'terpasang', NULL, NULL, '2026-06-13 14:35:07'),
(11, 11, 3, 1, '2025-09-10', '2025-09-10', 'Jl. Flamboyan Gg. Kelinci 83', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(12, 12, 2, 1, '2025-10-12', '2025-10-12', 'Cluster Hijau Residence No. 40', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(13, 13, 2, 1, '2025-10-14', '2025-10-14', 'Jl. Gajah Mada Blok 52', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(14, 14, 1, 1, '2025-08-22', '2025-08-22', 'Jl. Merdeka No. 54', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(15, 15, 3, 1, '2025-08-26', '2025-08-26', 'Kampung Baru Raya No. 28', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(16, 16, 2, 1, '2025-03-10', '2025-03-10', 'Kampung Baru Raya No. 77', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(17, 17, 1, 1, '2025-09-15', '2025-09-15', 'Jl. Merdeka No. 25', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(18, 18, 2, 1, '2026-04-17', '2026-04-17', 'Perumahan Griya Asri Blok 80', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(19, 19, 2, 1, '2025-07-24', '2025-07-24', 'Jl. Gajah Mada Blok 33', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(20, 20, 1, 1, '2025-08-05', '2025-08-05', 'Perumahan Griya Asri Blok 39', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(21, 21, 2, 1, '2026-01-28', '2026-01-28', 'Perumahan Griya Asri Blok 92', 'terpasang', NULL, NULL, '2026-06-13 14:35:08'),
(22, 22, 1, 1, '2026-02-21', '2026-02-21', 'Perumahan Griya Asri Blok 87', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(23, 23, 1, 1, '2026-04-11', '2026-04-11', 'Jl. Merdeka No. 46', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(24, 24, 1, 1, '2025-02-27', '2025-02-27', 'Jl. Gajah Mada Blok 42', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(25, 25, 1, 1, '2026-03-28', '2026-03-28', 'Jl. Merdeka No. 70', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(26, 26, 2, 1, '2026-02-28', '2026-02-28', 'Jl. Flamboyan Gg. Kelinci 21', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(27, 27, 3, 1, '2026-02-04', '2026-02-04', 'Perumahan Griya Asri Blok 8', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(28, 28, 1, 1, '2025-09-16', '2025-09-16', 'Kampung Baru Raya No. 94', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(29, 29, 3, 1, '2026-03-10', '2026-03-10', 'Jl. Gajah Mada Blok 83', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(30, 30, 1, 1, '2025-06-17', '2025-06-17', 'Kampung Baru Raya No. 41', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(31, 31, 1, 1, '2025-06-15', '2025-06-15', 'Perumahan Griya Asri Blok 2', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(32, 32, 3, 1, '2025-03-28', '2025-03-28', 'Jl. Merdeka No. 42', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(33, 33, 2, 1, '2025-06-02', '2025-06-02', 'Kampung Baru Raya No. 99', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(34, 34, 3, 1, '2025-09-24', '2025-09-24', 'Kampung Baru Raya No. 41', 'terpasang', NULL, NULL, '2026-06-13 14:35:09'),
(35, 35, 1, 1, '2025-08-21', '2025-08-21', 'Cluster Hijau Residence No. 2', 'terpasang', NULL, NULL, '2026-06-13 14:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `tb_teknisi`
--

CREATE TABLE `tb_teknisi` (
  `id_teknisi` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_teknisi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telepon_teknisi` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_teknisi`
--

INSERT INTO `tb_teknisi` (`id_teknisi`, `id_user`, `nama_teknisi`, `telepon_teknisi`) VALUES
(1, 2, 'Teknisi Utama Lapangan', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int NOT NULL,
  `id_langganan` int NOT NULL,
  `id_paket_baru` int DEFAULT NULL,
  `kode_invoice` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bulan_tagihan` int DEFAULT NULL,
  `tahun_tagihan` int DEFAULT NULL,
  `jumlah_bayar` int NOT NULL,
  `metode_pembayaran` enum('transfer','qris','cash') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','menunggu_verifikasi','lunas','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'belum_bayar',
  `jenis_transaksi` enum('baru','perpanjang','upgrade','reaktivasi') COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_langganan`, `id_paket_baru`, `kode_invoice`, `bulan_tagihan`, `tahun_tagihan`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`, `jenis_transaksi`, `tanggal_bayar`, `created_at`) VALUES
(1, 1, NULL, 'INV-202511-0001', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-02', '2025-11-02 03:00:00'),
(2, 1, NULL, 'INV-202512-0001', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-01', '2025-12-01 03:00:00'),
(3, 1, NULL, 'INV-202601-0001', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-04', '2026-01-04 03:00:00'),
(4, 1, NULL, 'INV-202602-0001', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(5, 1, NULL, 'INV-202603-0001', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-01', '2026-03-01 03:00:00'),
(6, 1, NULL, 'INV-202604-0001', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(7, 1, NULL, 'INV-202605-0001', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-02', '2026-05-02 03:00:00'),
(8, 1, NULL, 'INV-202606-0001', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(9, 2, NULL, 'INV-202603-0002', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(10, 2, NULL, 'INV-202604-0002', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(11, 2, NULL, 'INV-202605-0002', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-05', '2026-05-05 03:00:00'),
(12, 2, NULL, 'INV-202606-0002', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(13, 3, NULL, 'INV-202504-0003', 4, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-04-04', '2025-04-04 03:00:00'),
(14, 3, NULL, 'INV-202505-0003', 5, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-05-02', '2025-05-02 03:00:00'),
(15, 3, NULL, 'INV-202506-0003', 6, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-05', '2025-06-05 03:00:00'),
(16, 3, NULL, 'INV-202507-0003', 7, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-05', '2025-07-05 03:00:00'),
(17, 3, NULL, 'INV-202508-0003', 8, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(18, 3, NULL, 'INV-202509-0003', 9, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-01', '2025-09-01 03:00:00'),
(19, 3, NULL, 'INV-202510-0003', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(20, 3, NULL, 'INV-202511-0003', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-05', '2025-11-05 03:00:00'),
(21, 3, NULL, 'INV-202512-0003', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-02', '2025-12-02 03:00:00'),
(22, 3, NULL, 'INV-202601-0003', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-03', '2026-01-03 03:00:00'),
(23, 3, NULL, 'INV-202602-0003', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(24, 3, NULL, 'INV-202603-0003', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-03', '2026-03-03 03:00:00'),
(25, 3, NULL, 'INV-202604-0003', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-01', '2026-04-01 03:00:00'),
(26, 3, NULL, 'INV-202605-0003', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(27, 3, NULL, 'INV-202606-0003', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(28, 4, NULL, 'INV-202507-0004', 7, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-05', '2025-07-05 03:00:00'),
(29, 4, NULL, 'INV-202508-0004', 8, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-01', '2025-08-01 03:00:00'),
(30, 4, NULL, 'INV-202509-0004', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-05', '2025-09-05 03:00:00'),
(31, 4, NULL, 'INV-202510-0004', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(32, 4, NULL, 'INV-202511-0004', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(33, 4, NULL, 'INV-202512-0004', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(34, 4, NULL, 'INV-202601-0004', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(35, 4, NULL, 'INV-202602-0004', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(36, 4, NULL, 'INV-202603-0004', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(37, 4, NULL, 'INV-202604-0004', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(38, 4, NULL, 'INV-202605-0004', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(39, 4, NULL, 'INV-202606-0004', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-04', '2026-06-04 03:00:00'),
(40, 5, NULL, 'INV-202602-0005', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(41, 5, NULL, 'INV-202603-0005', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-01', '2026-03-01 03:00:00'),
(42, 5, NULL, 'INV-202604-0005', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(43, 5, NULL, 'INV-202605-0005', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(44, 5, NULL, 'INV-202606-0005', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(45, 6, NULL, 'INV-202509-0006', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-04', '2025-09-04 03:00:00'),
(46, 6, NULL, 'INV-202510-0006', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-05', '2025-10-05 03:00:00'),
(47, 6, NULL, 'INV-202511-0006', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-03', '2025-11-03 03:00:00'),
(48, 6, NULL, 'INV-202512-0006', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-04', '2025-12-04 03:00:00'),
(49, 6, NULL, 'INV-202601-0006', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(50, 6, NULL, 'INV-202602-0006', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(51, 6, NULL, 'INV-202603-0006', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(52, 6, NULL, 'INV-202604-0006', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(53, 6, NULL, 'INV-202605-0006', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(54, 6, NULL, 'INV-202606-0006', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(55, 7, NULL, 'INV-202603-0007', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(56, 7, NULL, 'INV-202604-0007', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(57, 7, NULL, 'INV-202605-0007', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(58, 7, NULL, 'INV-202606-0007', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(59, 8, NULL, 'INV-202602-0008', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-01', '2026-02-01 03:00:00'),
(60, 8, NULL, 'INV-202603-0008', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-03', '2026-03-03 03:00:00'),
(61, 8, NULL, 'INV-202604-0008', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(62, 8, NULL, 'INV-202605-0008', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(63, 8, NULL, 'INV-202606-0008', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(64, 9, NULL, 'INV-202602-0009', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-03', '2026-02-03 03:00:00'),
(65, 9, NULL, 'INV-202603-0009', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(66, 9, NULL, 'INV-202604-0009', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(67, 9, NULL, 'INV-202605-0009', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(68, 9, NULL, 'INV-202606-0009', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(69, 10, NULL, 'INV-202604-0010', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(70, 10, NULL, 'INV-202605-0010', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-02', '2026-05-02 03:00:00'),
(71, 10, NULL, 'INV-202606-0010', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'belum_bayar', 'perpanjang', '2026-06-04', '2026-06-04 03:00:00'),
(72, 11, NULL, 'INV-202509-0011', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-04', '2025-09-04 03:00:00'),
(73, 11, NULL, 'INV-202510-0011', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-04', '2025-10-04 03:00:00'),
(74, 11, NULL, 'INV-202511-0011', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-01', '2025-11-01 03:00:00'),
(75, 11, NULL, 'INV-202512-0011', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-02', '2025-12-02 03:00:00'),
(76, 11, NULL, 'INV-202601-0011', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-05', '2026-01-05 03:00:00'),
(77, 11, NULL, 'INV-202602-0011', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-01', '2026-02-01 03:00:00'),
(78, 11, NULL, 'INV-202603-0011', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(79, 11, NULL, 'INV-202604-0011', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-01', '2026-04-01 03:00:00'),
(80, 11, NULL, 'INV-202605-0011', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(81, 11, NULL, 'INV-202606-0011', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-04', '2026-06-04 03:00:00'),
(82, 12, NULL, 'INV-202510-0012', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(83, 12, NULL, 'INV-202511-0012', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(84, 12, NULL, 'INV-202512-0012', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-02', '2025-12-02 03:00:00'),
(85, 12, NULL, 'INV-202601-0012', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(86, 12, NULL, 'INV-202602-0012', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(87, 12, NULL, 'INV-202603-0012', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(88, 12, NULL, 'INV-202604-0012', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(89, 12, NULL, 'INV-202605-0012', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-05', '2026-05-05 03:00:00'),
(90, 12, NULL, 'INV-202606-0012', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'belum_bayar', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(91, 13, NULL, 'INV-202510-0013', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-05', '2025-10-05 03:00:00'),
(92, 13, NULL, 'INV-202511-0013', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-05', '2025-11-05 03:00:00'),
(93, 13, NULL, 'INV-202512-0013', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(94, 13, NULL, 'INV-202601-0013', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-05', '2026-01-05 03:00:00'),
(95, 13, NULL, 'INV-202602-0013', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(96, 13, NULL, 'INV-202603-0013', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(97, 13, NULL, 'INV-202604-0013', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(98, 13, NULL, 'INV-202605-0013', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(99, 13, NULL, 'INV-202606-0013', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(100, 14, NULL, 'INV-202508-0014', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(101, 14, NULL, 'INV-202509-0014', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-01', '2025-09-01 03:00:00'),
(102, 14, NULL, 'INV-202510-0014', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-05', '2025-10-05 03:00:00'),
(103, 14, NULL, 'INV-202511-0014', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-01', '2025-11-01 03:00:00'),
(104, 14, NULL, 'INV-202512-0014', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-03', '2025-12-03 03:00:00'),
(105, 14, NULL, 'INV-202601-0014', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(106, 14, NULL, 'INV-202602-0014', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(107, 14, NULL, 'INV-202603-0014', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-01', '2026-03-01 03:00:00'),
(108, 14, NULL, 'INV-202604-0014', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(109, 14, NULL, 'INV-202605-0014', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-02', '2026-05-02 03:00:00'),
(110, 14, NULL, 'INV-202606-0014', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(111, 15, NULL, 'INV-202508-0015', 8, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-01', '2025-08-01 03:00:00'),
(112, 15, NULL, 'INV-202509-0015', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-04', '2025-09-04 03:00:00'),
(113, 15, NULL, 'INV-202510-0015', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(114, 15, NULL, 'INV-202511-0015', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-01', '2025-11-01 03:00:00'),
(115, 15, NULL, 'INV-202512-0015', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-04', '2025-12-04 03:00:00'),
(116, 15, NULL, 'INV-202601-0015', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(117, 15, NULL, 'INV-202602-0015', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(118, 15, NULL, 'INV-202603-0015', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-05', '2026-03-05 03:00:00'),
(119, 15, NULL, 'INV-202604-0015', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(120, 15, NULL, 'INV-202605-0015', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(121, 15, NULL, 'INV-202606-0015', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'belum_bayar', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(122, 16, NULL, 'INV-202503-0016', 3, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-03-04', '2025-03-04 03:00:00'),
(123, 16, NULL, 'INV-202504-0016', 4, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-04-04', '2025-04-04 03:00:00'),
(124, 16, NULL, 'INV-202505-0016', 5, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-05-02', '2025-05-02 03:00:00'),
(125, 16, NULL, 'INV-202506-0016', 6, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-04', '2025-06-04 03:00:00'),
(126, 16, NULL, 'INV-202507-0016', 7, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-02', '2025-07-02 03:00:00'),
(127, 16, NULL, 'INV-202508-0016', 8, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(128, 16, NULL, 'INV-202509-0016', 9, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-03', '2025-09-03 03:00:00'),
(129, 16, NULL, 'INV-202510-0016', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-04', '2025-10-04 03:00:00'),
(130, 16, NULL, 'INV-202511-0016', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-01', '2025-11-01 03:00:00'),
(131, 16, NULL, 'INV-202512-0016', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-01', '2025-12-01 03:00:00'),
(132, 16, NULL, 'INV-202601-0016', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-03', '2026-01-03 03:00:00'),
(133, 16, NULL, 'INV-202602-0016', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-03', '2026-02-03 03:00:00'),
(134, 16, NULL, 'INV-202603-0016', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(135, 16, NULL, 'INV-202604-0016', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(136, 16, NULL, 'INV-202605-0016', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(137, 16, NULL, 'INV-202606-0016', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(138, 17, NULL, 'INV-202509-0017', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-04', '2025-09-04 03:00:00'),
(139, 17, NULL, 'INV-202510-0017', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(140, 17, NULL, 'INV-202511-0017', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-01', '2025-11-01 03:00:00'),
(141, 17, NULL, 'INV-202512-0017', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-01', '2025-12-01 03:00:00'),
(142, 17, NULL, 'INV-202601-0017', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-05', '2026-01-05 03:00:00'),
(143, 17, NULL, 'INV-202602-0017', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(144, 17, NULL, 'INV-202603-0017', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-01', '2026-03-01 03:00:00'),
(145, 17, NULL, 'INV-202604-0017', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(146, 17, NULL, 'INV-202605-0017', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(147, 17, NULL, 'INV-202606-0017', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(148, 18, NULL, 'INV-202604-0018', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(149, 18, NULL, 'INV-202605-0018', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-05', '2026-05-05 03:00:00'),
(150, 18, NULL, 'INV-202606-0018', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(151, 19, NULL, 'INV-202507-0019', 7, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-03', '2025-07-03 03:00:00'),
(152, 19, NULL, 'INV-202508-0019', 8, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(153, 19, NULL, 'INV-202509-0019', 9, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-04', '2025-09-04 03:00:00'),
(154, 19, NULL, 'INV-202510-0019', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-03', '2025-10-03 03:00:00'),
(155, 19, NULL, 'INV-202511-0019', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-05', '2025-11-05 03:00:00'),
(156, 19, NULL, 'INV-202512-0019', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(157, 19, NULL, 'INV-202601-0019', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(158, 19, NULL, 'INV-202602-0019', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(159, 19, NULL, 'INV-202603-0019', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(160, 19, NULL, 'INV-202604-0019', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(161, 19, NULL, 'INV-202605-0019', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(162, 19, NULL, 'INV-202606-0019', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-04', '2026-06-04 03:00:00'),
(163, 20, NULL, 'INV-202508-0020', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(164, 20, NULL, 'INV-202509-0020', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-05', '2025-09-05 03:00:00'),
(165, 20, NULL, 'INV-202510-0020', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-04', '2025-10-04 03:00:00'),
(166, 20, NULL, 'INV-202511-0020', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-05', '2025-11-05 03:00:00'),
(167, 20, NULL, 'INV-202512-0020', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-03', '2025-12-03 03:00:00'),
(168, 20, NULL, 'INV-202601-0020', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-03', '2026-01-03 03:00:00'),
(169, 20, NULL, 'INV-202602-0020', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-03', '2026-02-03 03:00:00'),
(170, 20, NULL, 'INV-202603-0020', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(171, 20, NULL, 'INV-202604-0020', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(172, 20, NULL, 'INV-202605-0020', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(173, 20, NULL, 'INV-202606-0020', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(174, 21, NULL, 'INV-202601-0021', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-03', '2026-01-03 03:00:00'),
(175, 21, NULL, 'INV-202602-0021', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(176, 21, NULL, 'INV-202603-0021', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-03', '2026-03-03 03:00:00'),
(177, 21, NULL, 'INV-202604-0021', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(178, 21, NULL, 'INV-202605-0021', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(179, 21, NULL, 'INV-202606-0021', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(180, 22, NULL, 'INV-202602-0022', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-01', '2026-02-01 03:00:00'),
(181, 22, NULL, 'INV-202603-0022', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(182, 22, NULL, 'INV-202604-0022', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(183, 22, NULL, 'INV-202605-0022', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-02', '2026-05-02 03:00:00'),
(184, 22, NULL, 'INV-202606-0022', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(185, 23, NULL, 'INV-202604-0023', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(186, 23, NULL, 'INV-202605-0023', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(187, 23, NULL, 'INV-202606-0023', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(188, 24, NULL, 'INV-202502-0024', 2, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-02-01', '2025-02-01 03:00:00'),
(189, 24, NULL, 'INV-202503-0024', 3, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-03-04', '2025-03-04 03:00:00'),
(190, 24, NULL, 'INV-202504-0024', 4, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-04-02', '2025-04-02 03:00:00'),
(191, 24, NULL, 'INV-202505-0024', 5, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-05-05', '2025-05-05 03:00:00'),
(192, 24, NULL, 'INV-202506-0024', 6, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-01', '2025-06-01 03:00:00'),
(193, 24, NULL, 'INV-202507-0024', 7, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-02', '2025-07-02 03:00:00'),
(194, 24, NULL, 'INV-202508-0024', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-05', '2025-08-05 03:00:00'),
(195, 24, NULL, 'INV-202509-0024', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-01', '2025-09-01 03:00:00'),
(196, 24, NULL, 'INV-202510-0024', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-01', '2025-10-01 03:00:00'),
(197, 24, NULL, 'INV-202511-0024', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-03', '2025-11-03 03:00:00'),
(198, 24, NULL, 'INV-202512-0024', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-04', '2025-12-04 03:00:00'),
(199, 24, NULL, 'INV-202601-0024', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-04', '2026-01-04 03:00:00'),
(200, 24, NULL, 'INV-202602-0024', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(201, 24, NULL, 'INV-202603-0024', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(202, 24, NULL, 'INV-202604-0024', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(203, 24, NULL, 'INV-202605-0024', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(204, 24, NULL, 'INV-202606-0024', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(205, 25, NULL, 'INV-202603-0025', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(206, 25, NULL, 'INV-202604-0025', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-04', '2026-04-04 03:00:00'),
(207, 25, NULL, 'INV-202605-0025', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(208, 25, NULL, 'INV-202606-0025', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(209, 26, NULL, 'INV-202602-0026', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(210, 26, NULL, 'INV-202603-0026', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(211, 26, NULL, 'INV-202604-0026', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(212, 26, NULL, 'INV-202605-0026', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(213, 26, NULL, 'INV-202606-0026', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(214, 27, NULL, 'INV-202602-0027', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(215, 27, NULL, 'INV-202603-0027', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(216, 27, NULL, 'INV-202604-0027', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(217, 27, NULL, 'INV-202605-0027', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(218, 27, NULL, 'INV-202606-0027', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-04', '2026-06-04 03:00:00'),
(219, 28, NULL, 'INV-202509-0028', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-05', '2025-09-05 03:00:00'),
(220, 28, NULL, 'INV-202510-0028', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-01', '2025-10-01 03:00:00'),
(221, 28, NULL, 'INV-202511-0028', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(222, 28, NULL, 'INV-202512-0028', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-04', '2025-12-04 03:00:00'),
(223, 28, NULL, 'INV-202601-0028', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-01', '2026-01-01 03:00:00'),
(224, 28, NULL, 'INV-202602-0028', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(225, 28, NULL, 'INV-202603-0028', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-02', '2026-03-02 03:00:00'),
(226, 28, NULL, 'INV-202604-0028', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(227, 28, NULL, 'INV-202605-0028', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(228, 28, NULL, 'INV-202606-0028', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'belum_bayar', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(229, 29, NULL, 'INV-202603-0029', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-03', '2026-03-03 03:00:00'),
(230, 29, NULL, 'INV-202604-0029', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(231, 29, NULL, 'INV-202605-0029', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-04', '2026-05-04 03:00:00'),
(232, 29, NULL, 'INV-202606-0029', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00'),
(233, 30, NULL, 'INV-202506-0030', 6, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-02', '2025-06-02 03:00:00'),
(234, 30, NULL, 'INV-202507-0030', 7, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-01', '2025-07-01 03:00:00'),
(235, 30, NULL, 'INV-202508-0030', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-01', '2025-08-01 03:00:00'),
(236, 30, NULL, 'INV-202509-0030', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-02', '2025-09-02 03:00:00'),
(237, 30, NULL, 'INV-202510-0030', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-04', '2025-10-04 03:00:00'),
(238, 30, NULL, 'INV-202511-0030', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(239, 30, NULL, 'INV-202512-0030', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-03', '2025-12-03 03:00:00'),
(240, 30, NULL, 'INV-202601-0030', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-04', '2026-01-04 03:00:00'),
(241, 30, NULL, 'INV-202602-0030', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-04', '2026-02-04 03:00:00'),
(242, 30, NULL, 'INV-202603-0030', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-03', '2026-03-03 03:00:00'),
(243, 30, NULL, 'INV-202604-0030', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(244, 30, NULL, 'INV-202605-0030', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(245, 30, NULL, 'INV-202606-0030', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-02', '2026-06-02 03:00:00'),
(246, 31, NULL, 'INV-202506-0031', 6, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-02', '2025-06-02 03:00:00'),
(247, 31, NULL, 'INV-202507-0031', 7, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-01', '2025-07-01 03:00:00'),
(248, 31, NULL, 'INV-202508-0031', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(249, 31, NULL, 'INV-202509-0031', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-05', '2025-09-05 03:00:00'),
(250, 31, NULL, 'INV-202510-0031', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-04', '2025-10-04 03:00:00'),
(251, 31, NULL, 'INV-202511-0031', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-03', '2025-11-03 03:00:00'),
(252, 31, NULL, 'INV-202512-0031', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(253, 31, NULL, 'INV-202601-0031', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-04', '2026-01-04 03:00:00'),
(254, 31, NULL, 'INV-202602-0031', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(255, 31, NULL, 'INV-202603-0031', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-05', '2026-03-05 03:00:00'),
(256, 31, NULL, 'INV-202604-0031', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(257, 31, NULL, 'INV-202605-0031', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-05', '2026-05-05 03:00:00'),
(258, 31, NULL, 'INV-202606-0031', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(259, 32, NULL, 'INV-202503-0032', 3, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-03-02', '2025-03-02 03:00:00'),
(260, 32, NULL, 'INV-202504-0032', 4, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-04-03', '2025-04-03 03:00:00'),
(261, 32, NULL, 'INV-202505-0032', 5, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-05-02', '2025-05-02 03:00:00'),
(262, 32, NULL, 'INV-202506-0032', 6, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-02', '2025-06-02 03:00:00'),
(263, 32, NULL, 'INV-202507-0032', 7, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-04', '2025-07-04 03:00:00'),
(264, 32, NULL, 'INV-202508-0032', 8, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-02', '2025-08-02 03:00:00'),
(265, 32, NULL, 'INV-202509-0032', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-02', '2025-09-02 03:00:00'),
(266, 32, NULL, 'INV-202510-0032', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-02', '2025-10-02 03:00:00'),
(267, 32, NULL, 'INV-202511-0032', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-03', '2025-11-03 03:00:00'),
(268, 32, NULL, 'INV-202512-0032', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(269, 32, NULL, 'INV-202601-0032', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-04', '2026-01-04 03:00:00'),
(270, 32, NULL, 'INV-202602-0032', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-05', '2026-02-05 03:00:00'),
(271, 32, NULL, 'INV-202603-0032', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-05', '2026-03-05 03:00:00'),
(272, 32, NULL, 'INV-202604-0032', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-01', '2026-04-01 03:00:00'),
(273, 32, NULL, 'INV-202605-0032', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(274, 32, NULL, 'INV-202606-0032', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(275, 33, NULL, 'INV-202506-0033', 6, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-06-01', '2025-06-01 03:00:00'),
(276, 33, NULL, 'INV-202507-0033', 7, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-07-01', '2025-07-01 03:00:00'),
(277, 33, NULL, 'INV-202508-0033', 8, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-04', '2025-08-04 03:00:00'),
(278, 33, NULL, 'INV-202509-0033', 9, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-02', '2025-09-02 03:00:00'),
(279, 33, NULL, 'INV-202510-0033', 10, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-01', '2025-10-01 03:00:00'),
(280, 33, NULL, 'INV-202511-0033', 11, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(281, 33, NULL, 'INV-202512-0033', 12, 2025, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(282, 33, NULL, 'INV-202601-0033', 1, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-03', '2026-01-03 03:00:00'),
(283, 33, NULL, 'INV-202602-0033', 2, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(284, 33, NULL, 'INV-202603-0033', 3, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(285, 33, NULL, 'INV-202604-0033', 4, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-02', '2026-04-02 03:00:00'),
(286, 33, NULL, 'INV-202605-0033', 5, 2026, 250000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(287, 33, NULL, 'INV-202606-0033', 6, 2026, 250000, 'transfer', 'bukti.jpg', 'lunas', 'perpanjang', '2026-06-01', '2026-06-01 03:00:00'),
(288, 34, NULL, 'INV-202509-0034', 9, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-02', '2025-09-02 03:00:00'),
(289, 34, NULL, 'INV-202510-0034', 10, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-02', '2025-10-02 03:00:00'),
(290, 34, NULL, 'INV-202511-0034', 11, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-04', '2025-11-04 03:00:00'),
(291, 34, NULL, 'INV-202512-0034', 12, 2025, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-05', '2025-12-05 03:00:00'),
(292, 34, NULL, 'INV-202601-0034', 1, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-02', '2026-01-02 03:00:00'),
(293, 34, NULL, 'INV-202602-0034', 2, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-03', '2026-02-03 03:00:00'),
(294, 34, NULL, 'INV-202603-0034', 3, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-01', '2026-03-01 03:00:00'),
(295, 34, NULL, 'INV-202604-0034', 4, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-03', '2026-04-03 03:00:00'),
(296, 34, NULL, 'INV-202605-0034', 5, 2026, 400000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-03', '2026-05-03 03:00:00'),
(297, 34, NULL, 'INV-202606-0034', 6, 2026, 400000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-03', '2026-06-03 03:00:00'),
(298, 35, NULL, 'INV-202508-0035', 8, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-08-05', '2025-08-05 03:00:00'),
(299, 35, NULL, 'INV-202509-0035', 9, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-09-02', '2025-09-02 03:00:00'),
(300, 35, NULL, 'INV-202510-0035', 10, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-10-05', '2025-10-05 03:00:00'),
(301, 35, NULL, 'INV-202511-0035', 11, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-11-02', '2025-11-02 03:00:00'),
(302, 35, NULL, 'INV-202512-0035', 12, 2025, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2025-12-02', '2025-12-02 03:00:00'),
(303, 35, NULL, 'INV-202601-0035', 1, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-01-02', '2026-01-02 03:00:00'),
(304, 35, NULL, 'INV-202602-0035', 2, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-02-02', '2026-02-02 03:00:00'),
(305, 35, NULL, 'INV-202603-0035', 3, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-03-04', '2026-03-04 03:00:00'),
(306, 35, NULL, 'INV-202604-0035', 4, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-04-05', '2026-04-05 03:00:00'),
(307, 35, NULL, 'INV-202605-0035', 5, 2026, 150000, 'transfer', 'bukti.jpg', 'expired', 'perpanjang', '2026-05-01', '2026-05-01 03:00:00'),
(308, 35, NULL, 'INV-202606-0035', 6, 2026, 150000, 'transfer', 'bukti.jpg', 'menunggu_verifikasi', 'perpanjang', '2026-06-05', '2026-06-05 03:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','customer','teknisi') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'anuwani', '$2y$10$IDDwLdCaTlcyaWbONx05Cu4wvC92cod88AjJFhzwDgO3OlesgDemq', 'admin', '2026-06-13 14:08:23'),
(2, 'teknisi', '$2y$10$bwt025NoxWmjCdxIh3uPt.LO341YgHHBocfCnmhCW9cnTuNDkRAVu', 'teknisi', '2026-06-13 14:08:23'),
(44, 'cust_1', '$2y$10$YOXmTaMHSi4RE49ogfwGcOScJRCuGzgfEh87FywugUvpjWneXLP8y', 'customer', '2025-11-22 01:00:00'),
(45, 'cust_2', '$2y$10$BnuGI1FVesGOY4XZ7.wvJOpy82T39VBCSllkU1yN7LDbuMKK4jQJK', 'customer', '2026-03-05 01:00:00'),
(46, 'cust_3', '$2y$10$1PdS84wYJ8l6HtT3ulh.EunNHMEl18lZRiNOXtfo4O5TQfPJOrGDS', 'customer', '2025-04-25 01:00:00'),
(47, 'cust_4', '$2y$10$fZ7J3CR.hoaSqwNnWotDfeKLos21KDaYDt7vujtP9uOlrWW3yY8a.', 'customer', '2025-07-13 01:00:00'),
(48, 'cust_5', '$2y$10$IFGxwyAiqQm1JooGLaStT.zpgUoFDFhjWLPkD6HuRnI5Jfk2NEXQ6', 'customer', '2026-02-16 01:00:00'),
(49, 'cust_6', '$2y$10$SNvzjpGVMcRZMwrwaRFNJeW5R3AbK0P0rfxta/Dbp4.JWDkuXCoOa', 'customer', '2025-09-09 01:00:00'),
(50, 'cust_7', '$2y$10$qt5cwIPi/qFLhqRdGSKjAe3wUukjq/FC/qUYMqhKk3oRzXQbcCz4u', 'customer', '2026-03-23 01:00:00'),
(51, 'cust_8', '$2y$10$5bxoqQ2hGE9zZ31N.dEIAugY3lWkWHhtarns8o0du.bPT8eXTYGHS', 'customer', '2026-02-11 01:00:00'),
(52, 'cust_9', '$2y$10$7fSv30ay.2At8tGRW1fxIumGD7Ps31a3AL522gPMMr8y/TMdu69Pe', 'customer', '2026-02-16 01:00:00'),
(53, 'cust_10', '$2y$10$ws8dTUiaisTqG.p6SEP49eytT8CXY4W0Kuui3vDeHCuglHM4kNrnS', 'customer', '2026-04-23 01:00:00'),
(54, 'cust_11', '$2y$10$17AqLj1A88vPlbnoBG9NzOZlm1S2o.M10ozFxz4Hn5hanF9twWyN2', 'customer', '2025-09-10 01:00:00'),
(55, 'cust_12', '$2y$10$fJlZ5E3HmzTCFtOFIPGLU.nrA00cenOD9a2ofEzG7SlVWsqLoo1o2', 'customer', '2025-10-12 01:00:00'),
(56, 'cust_13', '$2y$10$bWCWZqQXHg/DWRv5J6Fn6.1AdFQOCIZ0R8507ZzOPxmOq3IoNHuty', 'customer', '2025-10-14 01:00:00'),
(57, 'cust_14', '$2y$10$F/QFT.dIAXU.5uXVmwTwDeYwZb1XplCe3i5vReWXOqwVyy8tHVAhq', 'customer', '2025-08-22 01:00:00'),
(58, 'cust_15', '$2y$10$VPgAXMqcG3ynepccCZ71n.iIcaTwa1aDSO/A6kXf807bf5kFGEMl.', 'customer', '2025-08-26 01:00:00'),
(59, 'cust_16', '$2y$10$c1M6b5qOpf1Ji7ary7yN2eBMZgsQd7oeVjur9Fei/qL5H83RSt.NS', 'customer', '2025-03-10 01:00:00'),
(60, 'cust_17', '$2y$10$kbVfbiWdz2bG3IE4LWwCbODpGbEAJ4wkgYyYPVS.LOj.AwqBwVxEe', 'customer', '2025-09-15 01:00:00'),
(61, 'cust_18', '$2y$10$MZ5P1IQmAcbjOA/1mHiVguAjUE8MwjqUj.wn5blZyQ/fDVKgj95ri', 'customer', '2026-04-17 01:00:00'),
(62, 'cust_19', '$2y$10$19WJRNduX03t5FNkvLGr8Os6jUMMBf3VetPYKqZRCl8/HaCIT3FhG', 'customer', '2025-07-24 01:00:00'),
(63, 'cust_20', '$2y$10$lGyZ40kV6ab1h.s/CljY0uPNUJxfmPIIOcaCvCxh6ujLPVzIkMXi2', 'customer', '2025-08-05 01:00:00'),
(64, 'cust_21', '$2y$10$y4ImJNHMCYj2y48AvFWVbeNAifQH3NAkv0GP.EcbHIPLBZDv9S/Xq', 'customer', '2026-01-28 01:00:00'),
(65, 'cust_22', '$2y$10$nQpCMv7NFlB7o8h4RgAngui7uu2OMGb.fyVRFRN1/t66i6naY8pfm', 'customer', '2026-02-21 01:00:00'),
(66, 'cust_23', '$2y$10$JZhsgZyJM.f0Q6mv9UuLUOmAE/8nb7tYhdLUn9fqgyt6tjITLjiCW', 'customer', '2026-04-11 01:00:00'),
(67, 'cust_24', '$2y$10$4Mmr0h4b3qSm9wBYFlsyY.djRiO7wYFLuqZiQ.uTnJmhJU7YVTaHO', 'customer', '2025-02-27 01:00:00'),
(68, 'cust_25', '$2y$10$c/Ldx9XpVSlVnVfiCgv9R.KMB74EKqnQbRCjkFWlgUh4uke2.Un46', 'customer', '2026-03-28 01:00:00'),
(69, 'cust_26', '$2y$10$w1Ba2h1qsPugj./cKxYImuAW51jpltw9kjm0KQe0w/XAgQBQQpY.m', 'customer', '2026-02-28 01:00:00'),
(70, 'cust_27', '$2y$10$R1J5XbZ7B4SOaDZ0P1a2j.b3OzdhbxCAI3XYh6bxa9kGxaDDuoHiG', 'customer', '2026-02-04 01:00:00'),
(71, 'cust_28', '$2y$10$18EbLJUm41fwi8qzr2RAAeTIaqIYOrZ0oVqgjy96u.W61xwdsL89i', 'customer', '2025-09-16 01:00:00'),
(72, 'cust_29', '$2y$10$zQql5wbMgCn5F6KKImlkQ.cWNwKx6BS.VeD5utp.kRW5aaPIPX9Tu', 'customer', '2026-03-10 01:00:00'),
(73, 'cust_30', '$2y$10$y/c/VAE2o2r/PPiXHzYoMuGPmqYipkY/u.Lh6LWatuc5zSrkAk/Ky', 'customer', '2025-06-17 01:00:00'),
(74, 'cust_31', '$2y$10$K.twniANkKYo5ZXHu6gxm.GXrbDMnT291Fw1um598C3ns4tfhttdy', 'customer', '2025-06-15 01:00:00'),
(75, 'cust_32', '$2y$10$tGQcvn2tyVynipj7WIVIvus/UXPtVGnUunHLeRqkHNAjQaPqGOReW', 'customer', '2025-03-28 01:00:00'),
(76, 'cust_33', '$2y$10$60tf25XCOkOr5v7B7tBoO.gMUwPumG9AisC76dP./wQVvLVOSyYeK', 'customer', '2025-06-02 01:00:00'),
(77, 'cust_34', '$2y$10$Ti7i/1mJJNXBLKlWmB4BGu.czmDF4V2AOSY.5cuqheKs7RYzkJHrS', 'customer', '2025-09-24 01:00:00'),
(78, 'cust_35', '$2y$10$v5Y8q9XxB7KKosAcjeKrIO7qBNgvXqVanGYQJbRis3twh7iIBROU.', 'customer', '2025-08-21 01:00:00'),
(79, 'cust_36', '$2y$10$ya61i.W8Jrh8eDoy5xsQe.d/OdISmLMLOIiCyuaosBXy9G2cuky9W', 'customer', '2026-02-15 01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD PRIMARY KEY (`id_customer`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_langganan`
--
ALTER TABLE `tb_langganan`
  ADD PRIMARY KEY (`id_langganan`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indexes for table `tb_paket`
--
ALTER TABLE `tb_paket`
  ADD PRIMARY KEY (`id_paket`);

--
-- Indexes for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  ADD PRIMARY KEY (`id_pemasangan`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `id_paket` (`id_paket`),
  ADD KEY `fk_pemasangan_teknisi` (`id_teknisi`);

--
-- Indexes for table `tb_teknisi`
--
ALTER TABLE `tb_teknisi`
  ADD PRIMARY KEY (`id_teknisi`),
  ADD KEY `fk_teknisi_user` (`id_user`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `kode_invoice` (`kode_invoice`),
  ADD KEY `id_langganan` (`id_langganan`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id_customer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tb_langganan`
--
ALTER TABLE `tb_langganan`
  MODIFY `id_langganan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id_paket` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  MODIFY `id_pemasangan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_teknisi`
--
ALTER TABLE `tb_teknisi`
  MODIFY `id_teknisi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD CONSTRAINT `tb_admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD CONSTRAINT `tb_customer_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tb_langganan`
--
ALTER TABLE `tb_langganan`
  ADD CONSTRAINT `tb_langganan_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`),
  ADD CONSTRAINT `tb_langganan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`);

--
-- Constraints for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  ADD CONSTRAINT `fk_pemasangan_teknisi` FOREIGN KEY (`id_teknisi`) REFERENCES `tb_teknisi` (`id_teknisi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_pemasangan_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_pemasangan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`);

--
-- Constraints for table `tb_teknisi`
--
ALTER TABLE `tb_teknisi`
  ADD CONSTRAINT `fk_teknisi_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_langganan`) REFERENCES `tb_langganan` (`id_langganan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
