-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2026 at 06:18 AM
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
(5, 11, 'anuwani admin', 'anuwanien@gmail.com'),
(11, 32, 'admin baru', 'admin22@gmail.com');

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
(30, 42, 'Rian Wibowo', 'No. 73 Buana Subang Raya', '08136959564', 'test_2771@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(31, 43, 'Budi Aminah', 'No. 96 Cibogo', '08132077054', 'test_5402@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(32, 44, 'Dewi Wibowo', 'No. 88 Permata Hijau', '08135021999', 'test_9343@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(33, 45, 'Budi Hidayat', 'No. 80 Sukamaju', '08134438902', 'test_5614@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(34, 46, 'Fitri Aminah', 'No. 50 Permata Hijau', '08139156435', 'test_1585@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(35, 47, 'Rian Wibowo', 'No. 61 Permata Hijau', '08139821625', 'test_7436@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(36, 48, 'Guntur Prasetyo', 'No. 19 Cibogo', '08132933336', 'test_5167@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(37, 49, 'Fani Wibowo', 'No. 14 Cibogo', '08131269805', 'test_6698@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(38, 50, 'Dewi Aminah', 'No. 21 Permata Hijau', '08131848554', 'test_2269@gmail.com', 'online', 'aktif', '2026-06-10 13:14:27'),
(39, 51, 'Fani Hidayat', 'No. 50 Cibogo', '08131832938', 'test_67310@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(40, 52, 'Budi Prasetyo', 'No. 43 Cibogo', '08136493090', 'test_37811@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(41, 53, 'Rian Darmawan', 'No. 96 Cibogo', '08132205170', 'test_69212@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(42, 54, 'Guntur Lestari', 'No. 42 Cibogo', '08138427476', 'test_80413@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(43, 55, 'Eko Darmawan', 'No. 55 Permata Hijau', '08138745442', 'test_58914@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(44, 56, 'Guntur Aminah', 'No. 6 Sukamaju', '08136323851', 'test_21015@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(45, 57, 'Guntur Prasetyo', 'No. 35 Permata Hijau', '08136910176', 'test_93316@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(46, 58, 'Eko Aminah', 'No. 65 Sukamaju', '08133840447', 'test_57017@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(47, 59, 'Fitri Prasetyo', 'No. 73 Cibogo', '08136594751', 'test_52618@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(48, 60, 'Rian Lestari', 'No. 90 Cibogo', '08137294437', 'test_44219@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(49, 61, 'Fitri Wibowo', 'No. 88 Cibogo', '08134613983', 'test_55820@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(50, 62, 'Siti Aminah', 'No. 10 Sukamaju', '08131319363', 'test_20821@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(51, 63, 'Siti Wibowo', 'No. 24 Sukamaju', '08139473000', 'test_34622@gmail.com', 'online', 'aktif', '2026-06-10 13:14:28'),
(52, 64, 'Rian Hidayat', 'No. 26 Cibogo', '08139394548', 'test_13323@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(53, 65, 'Dewi Hidayat', 'No. 16 Permata Hijau', '08134111718', 'test_74724@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(54, 66, 'Eko Aminah', 'No. 97 Buana Subang Raya', '08138516220', 'test_35225@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(55, 67, 'Guntur Wibowo', 'No. 78 Permata Hijau', '08131428893', 'test_19026@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(56, 68, 'Siti Prasetyo', 'No. 77 Cibogo', '08137501292', 'test_96827@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(57, 69, 'Eko Darmawan', 'No. 19 Cibogo', '08132747921', 'test_29828@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(58, 70, 'Eko Prasetyo', 'No. 39 Cibogo', '08133195827', 'test_29229@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(59, 71, 'Budi Darmawan', 'No. 75 Permata Hijau', '08138472429', 'test_49830@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(60, 72, 'Fitri Prasetyo', 'No. 14 Permata Hijau', '08136547599', 'test_52531@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(61, 73, 'Dewi Darmawan', 'No. 54 Buana Subang Raya', '08137587057', 'test_10132@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(62, 74, 'Eko Aminah', 'No. 97 Cibogo', '08138186611', 'test_14533@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(63, 75, 'Siti Aminah', 'No. 93 Buana Subang Raya', '08136206965', 'test_95234@gmail.com', 'online', 'aktif', '2026-06-10 13:14:29'),
(64, 76, 'Eko Darmawan', 'No. 50 Sukamaju', '08133967471', 'test_11035@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(65, 77, 'Guntur Wibowo', 'No. 59 Cibogo', '08134250519', 'test_55336@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(66, 78, 'Rian Darmawan', 'No. 90 Sukamaju', '08131224888', 'test_23937@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(67, 79, 'Budi Aminah', 'No. 37 Sukamaju', '08132069333', 'test_50638@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(68, 80, 'Eko Lestari', 'No. 58 Buana Subang Raya', '08132801786', 'test_89239@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(69, 81, 'Budi Hidayat', 'No. 88 Cibogo', '08137766872', 'test_42440@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(70, 82, 'Budi Prasetyo', 'No. 80 Cibogo', '08131049985', 'test_87641@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(71, 83, 'Fani Lestari', 'No. 82 Cibogo', '08136052470', 'test_32342@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(72, 84, 'Budi Darmawan', 'No. 29 Permata Hijau', '08139708357', 'test_41843@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(73, 85, 'Fani Aminah', 'No. 2 Buana Subang Raya', '08137513309', 'test_42344@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(74, 86, 'Eko Lestari', 'No. 28 Buana Subang Raya', '08135792395', 'test_60745@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(75, 87, 'Budi Aminah', 'No. 10 Buana Subang Raya', '08137997486', 'test_31046@gmail.com', 'online', 'aktif', '2026-06-10 13:14:30'),
(76, 88, 'Dewi Aminah', 'No. 86 Cibogo', '08136577075', 'test_26347@gmail.com', 'online', 'aktif', '2026-06-10 13:14:31'),
(77, 89, 'Dewi Prasetyo', 'No. 45 Cibogo', '08132630581', 'test_13948@gmail.com', 'online', 'aktif', '2026-06-10 13:14:31'),
(78, 90, 'Guntur Lestari', 'No. 35 Cibogo', '08131792714', 'test_71549@gmail.com', 'online', 'aktif', '2026-06-10 13:14:31'),
(79, 91, 'Rian Aminah', 'No. 17 Cibogo', '08135842459', 'test_32450@gmail.com', 'online', 'aktif', '2026-06-10 13:14:31'),
(80, 92, 'raysal gena', 'Subang, Jawabarat', '085312347864', 'limitgaming586@gmail.com', 'online', 'nonaktif', '2026-06-10 13:22:49'),
(81, 93, 'gena saputra', 'Subang', '085312123457', 'raysalraysal28@gmail.com', 'online', 'aktif', '2026-06-11 05:18:16');

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
  `status_langganan` enum('aktif','suspend','berhenti','menunggu_verifikasi') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_langganan`
--

INSERT INTO `tb_langganan` (`id_langganan`, `id_customer`, `id_paket`, `tanggal_mulai`, `tanggal_selesai`, `status_langganan`) VALUES
(32, 30, 5, '2026-06-02', '2026-07-02', 'aktif'),
(33, 31, 1, '2026-06-02', '2026-07-02', 'aktif'),
(34, 32, 5, '2026-06-02', '2026-07-02', 'aktif'),
(35, 33, 6, '2026-06-02', '2026-07-02', 'aktif'),
(36, 34, 2, '2026-06-02', '2026-07-02', 'aktif'),
(37, 35, 12, '2026-06-02', '2026-07-02', 'aktif'),
(38, 36, 13, '2026-06-02', '2026-07-02', 'aktif'),
(39, 37, 11, '2026-06-02', '2026-07-02', 'aktif'),
(40, 38, 12, '2026-06-02', '2026-07-02', 'aktif'),
(41, 39, 13, '2026-06-02', '2026-07-02', 'aktif'),
(42, 40, 3, '2026-06-02', '2026-07-02', 'aktif'),
(43, 41, 11, '2026-06-02', '2026-07-02', 'aktif'),
(44, 42, 5, '2026-06-02', '2026-07-02', 'aktif'),
(45, 43, 5, '2026-06-02', '2026-07-02', 'aktif'),
(46, 44, 13, '2026-06-02', '2026-07-02', 'aktif'),
(47, 45, 13, '2026-06-02', '2026-07-02', 'aktif'),
(48, 46, 13, '2026-06-02', '2026-07-02', 'aktif'),
(49, 47, 3, '2026-06-02', '2026-07-02', 'aktif'),
(50, 48, 4, '2026-06-02', '2026-07-02', 'aktif'),
(51, 49, 5, '2026-06-02', '2026-07-02', 'aktif'),
(52, 50, 11, '2026-06-02', '2026-07-02', 'aktif'),
(53, 51, 6, '2026-06-02', '2026-07-02', 'aktif'),
(54, 52, 12, '2026-06-02', '2026-07-02', 'aktif'),
(55, 53, 4, '2026-06-02', '2026-07-02', 'aktif'),
(56, 54, 4, '2026-06-02', '2026-07-02', 'aktif'),
(57, 55, 6, '2026-06-02', '2026-07-02', 'aktif'),
(58, 56, 6, '2026-06-02', '2026-07-02', 'aktif'),
(59, 57, 3, '2026-06-02', '2026-07-02', 'aktif'),
(60, 58, 1, '2026-06-02', '2026-07-02', 'aktif'),
(61, 59, 2, '2026-06-02', '2026-07-02', 'aktif'),
(62, 60, 12, '2026-06-02', '2026-07-02', 'aktif'),
(63, 61, 13, '2026-06-02', '2026-07-02', 'aktif'),
(64, 62, 13, '2026-06-02', '2026-07-02', 'aktif'),
(65, 63, 6, '2026-06-02', '2026-07-02', 'aktif'),
(66, 64, 1, '2026-06-02', '2026-07-02', 'aktif'),
(67, 65, 3, '2026-06-02', '2026-07-02', 'aktif'),
(68, 66, 1, '2026-06-02', '2026-07-02', 'aktif'),
(69, 67, 5, '2026-06-02', '2026-07-02', 'aktif'),
(70, 68, 2, '2026-06-02', '2026-07-02', 'aktif'),
(71, 69, 1, '2026-06-02', '2026-07-02', 'aktif'),
(72, 70, 4, '2026-06-02', '2026-07-02', 'aktif'),
(73, 71, 2, '2026-06-02', '2026-07-02', 'aktif'),
(74, 72, 13, '2026-06-02', '2026-07-02', 'aktif'),
(75, 73, 2, '2026-06-02', '2026-07-02', 'aktif'),
(76, 74, 4, '2026-06-02', '2026-07-02', 'aktif'),
(77, 75, 3, '2026-06-02', '2026-07-02', 'aktif'),
(78, 76, 12, '2026-06-02', '2026-07-02', 'aktif'),
(79, 77, 13, '2026-06-02', '2026-07-02', 'aktif'),
(80, 78, 6, '2026-06-02', '2026-07-02', 'aktif'),
(81, 79, 2, '2026-06-02', '2026-07-02', 'aktif'),
(82, 80, 3, NULL, NULL, 'menunggu_verifikasi'),
(83, 80, 3, NULL, NULL, 'menunggu_verifikasi'),
(84, 80, 3, '2026-06-11', '2026-06-11', 'berhenti'),
(85, 81, 2, '2026-06-11', '2026-07-11', 'aktif');

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
(1, 'Paket Keluarga', 125000, '40 Mbps', 'Fleksibel\r\nMultitasking\r\nMemudahkan pekerjaan\r\nKecepatan stabil', 'aktif'),
(2, 'Paket Jitu', 220000, '50 Mbps', 'Kecepatan tinggi\r\nMultitasking hebat\r\nAnti lag', 'aktif'),
(3, 'Paket Hemat', 100000, '20 Mbps', 'Hemat\r\nMurah\r\nKecepatan stabil', 'aktif'),
(4, 'Paket Ngebut', 300000, '120 Mbps', 'Anti lag seharian', 'aktif'),
(5, 'Paket Jawara', 250000, '100 Mbps', 'Kecepatan maksimal\r\nInternet tanpa batas\r\nSupport gaming dan pekerjaan berat', 'aktif'),
(6, 'Paket Bombardir', 160000, '35 Mbps', 'Internet rekomdenasi keluarga\r\nHarga ramah\r\nKonektivitas stabil', 'aktif'),
(11, 'Paket Booster', 215000, '80 Mbps', 'Gaming tanpa batas\r\nKoneksi terbaik\r\nLayanan bagus\r\n', 'aktif'),
(12, 'Paket Rakyat', 115000, '30 Mbps', 'Kebutuhan harian\r\nMudah internetan\r\nAktivitas online', 'aktif'),
(13, 'Paket Bahagia', 240000, '110 Mbps', 'Koneksi stabil\r\ncocok untuk aktivitas online\r\nmemudahkan multitasking', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemasangan`
--

CREATE TABLE `tb_pemasangan` (
  `id_pemasangan` int NOT NULL,
  `id_customer` int NOT NULL,
  `id_paket` int NOT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_pasang` date DEFAULT NULL,
  `alamat_pasang` text COLLATE utf8mb4_general_ci NOT NULL,
  `status_pemasangan` enum('menunggu','diproses','terpasang','dibatalkan') COLLATE utf8mb4_general_ci DEFAULT 'menunggu',
  `catatan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pemasangan`
--

INSERT INTO `tb_pemasangan` (`id_pemasangan`, `id_customer`, `id_paket`, `tanggal_pengajuan`, `tanggal_pasang`, `alamat_pasang`, `status_pemasangan`, `catatan`, `created_at`) VALUES
(1, 30, 5, '2026-06-01', '2026-06-02', 'No. 73 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(2, 31, 1, '2026-06-01', '2026-06-02', 'No. 96 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(3, 32, 5, '2026-06-01', '2026-06-02', 'No. 88 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(4, 33, 6, '2026-06-01', '2026-06-02', 'No. 80 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(5, 34, 2, '2026-06-01', '2026-06-02', 'No. 50 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(6, 35, 12, '2026-06-01', '2026-06-02', 'No. 61 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(7, 36, 13, '2026-06-01', '2026-06-02', 'No. 19 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(8, 37, 11, '2026-06-01', '2026-06-02', 'No. 14 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(9, 38, 12, '2026-06-01', '2026-06-02', 'No. 21 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:27'),
(10, 39, 13, '2026-06-01', '2026-06-02', 'No. 50 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(11, 40, 3, '2026-06-01', '2026-06-02', 'No. 43 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(12, 41, 11, '2026-06-01', '2026-06-02', 'No. 96 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(13, 42, 5, '2026-06-01', '2026-06-02', 'No. 42 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(14, 43, 5, '2026-06-01', '2026-06-02', 'No. 55 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(15, 44, 13, '2026-06-01', '2026-06-02', 'No. 6 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(16, 45, 13, '2026-06-01', '2026-06-02', 'No. 35 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(17, 46, 13, '2026-06-01', '2026-06-02', 'No. 65 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(18, 47, 3, '2026-06-01', '2026-06-02', 'No. 73 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(19, 48, 4, '2026-06-01', '2026-06-02', 'No. 90 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(20, 49, 5, '2026-06-01', '2026-06-02', 'No. 88 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(21, 50, 11, '2026-06-01', '2026-06-02', 'No. 10 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(22, 51, 6, '2026-06-01', '2026-06-02', 'No. 24 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:28'),
(23, 52, 12, '2026-06-01', '2026-06-02', 'No. 26 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(24, 53, 4, '2026-06-01', '2026-06-02', 'No. 16 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(25, 54, 4, '2026-06-01', '2026-06-02', 'No. 97 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(26, 55, 6, '2026-06-01', '2026-06-02', 'No. 78 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(27, 56, 6, '2026-06-01', '2026-06-02', 'No. 77 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(28, 57, 3, '2026-06-01', '2026-06-02', 'No. 19 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(29, 58, 1, '2026-06-01', '2026-06-02', 'No. 39 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(30, 59, 2, '2026-06-01', '2026-06-02', 'No. 75 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(31, 60, 12, '2026-06-01', '2026-06-02', 'No. 14 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(32, 61, 13, '2026-06-01', '2026-06-02', 'No. 54 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(33, 62, 13, '2026-06-01', '2026-06-02', 'No. 97 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(34, 63, 6, '2026-06-01', '2026-06-02', 'No. 93 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:29'),
(35, 64, 1, '2026-06-01', '2026-06-02', 'No. 50 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(36, 65, 3, '2026-06-01', '2026-06-02', 'No. 59 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(37, 66, 1, '2026-06-01', '2026-06-02', 'No. 90 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(38, 67, 5, '2026-06-01', '2026-06-02', 'No. 37 Sukamaju', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(39, 68, 2, '2026-06-01', '2026-06-02', 'No. 58 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(40, 69, 1, '2026-06-01', '2026-06-02', 'No. 88 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(41, 70, 4, '2026-06-01', '2026-06-02', 'No. 80 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(42, 71, 2, '2026-06-01', '2026-06-02', 'No. 82 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(43, 72, 13, '2026-06-01', '2026-06-02', 'No. 29 Permata Hijau', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(44, 73, 2, '2026-06-01', '2026-06-02', 'No. 2 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(45, 74, 4, '2026-06-01', '2026-06-02', 'No. 28 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(46, 75, 3, '2026-06-01', '2026-06-02', 'No. 10 Buana Subang Raya', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:30'),
(47, 76, 12, '2026-06-01', '2026-06-02', 'No. 86 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:31'),
(48, 77, 13, '2026-06-01', '2026-06-02', 'No. 45 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:31'),
(49, 78, 6, '2026-06-01', '2026-06-02', 'No. 35 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:31'),
(50, 79, 2, '2026-06-01', '2026-06-02', 'No. 17 Cibogo', 'terpasang', 'Pemasangan Dummy', '2026-06-10 13:14:31'),
(51, 80, 3, '2026-06-11', NULL, 'Subang, Jawabarat', 'menunggu', 'hari senin ya dipasangnya jam 3 sore, untuk informasi lebih lanjut chat whatsapp', '2026-06-11 03:02:54'),
(52, 80, 3, '2026-06-11', NULL, 'Subang, Jawabarat', 'menunggu', 'hari senin ya dipasangnya jam 3 sore, untuk informasi lebih lanjut chat whatsapp', '2026-06-11 03:03:01'),
(53, 80, 3, '2026-06-11', NULL, 'Subang, Jawabarat', 'menunggu', 'hari senin ya dipasangnya jam 3 sore, untuk informasi lebih lanjut chat whatsapp', '2026-06-11 03:09:59'),
(54, 80, 3, '2026-06-11', NULL, 'Subang, Jawabarat', 'menunggu', 'hari senin ya dipasangnya jam 3 sore, untuk informasi lebih lanjut chat whatsapp', '2026-06-11 03:10:07'),
(55, 80, 3, '2026-06-11', NULL, 'Subang, Jawabarat', 'menunggu', 'hari senin ya dipasangnya jam 3 sore, untuk informasi lebih lanjut chat whatsapp', '2026-06-11 03:12:40'),
(56, 81, 12, '2026-06-11', '2026-06-11', 'Subang', 'terpasang', 'hari minggu ya pasangnya, untukinfo lebih lanjut japri wa', '2026-06-11 05:19:29');

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
  `status_pembayaran` enum('belum_bayar','menunggu_verifikasi','lunas') COLLATE utf8mb4_general_ci DEFAULT 'belum_bayar',
  `jenis_transaksi` enum('baru','perpanjang','upgrade') COLLATE utf8mb4_general_ci DEFAULT 'perpanjang',
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_langganan`, `id_paket_baru`, `kode_invoice`, `bulan_tagihan`, `tahun_tagihan`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`, `jenis_transaksi`, `tanggal_bayar`, `created_at`) VALUES
(1, 32, NULL, 'INV-202606-80032', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(2, 33, NULL, 'INV-202606-10766', 6, 2026, 125000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(3, 34, NULL, 'INV-202606-40949', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(4, 35, NULL, 'INV-202606-90085', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(5, 36, NULL, 'INV-202606-98070', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(6, 37, NULL, 'INV-202606-86999', 6, 2026, 115000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(7, 38, NULL, 'INV-202606-79136', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(8, 39, NULL, 'INV-202606-49179', 6, 2026, 215000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(9, 40, NULL, 'INV-202606-27410', 6, 2026, 115000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:27'),
(10, 41, NULL, 'INV-202606-85125', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(11, 42, NULL, 'INV-202606-39450', 6, 2026, 100000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(12, 43, NULL, 'INV-202606-32050', 6, 2026, 215000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(13, 44, NULL, 'INV-202606-54323', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(14, 45, NULL, 'INV-202606-25160', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(15, 46, NULL, 'INV-202606-76449', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(16, 47, NULL, 'INV-202606-35403', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(17, 48, NULL, 'INV-202606-18656', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(18, 49, NULL, 'INV-202606-83698', 6, 2026, 100000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(19, 50, NULL, 'INV-202606-61593', 6, 2026, 300000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(20, 51, NULL, 'INV-202606-55332', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(21, 52, NULL, 'INV-202606-97082', 6, 2026, 215000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(22, 53, NULL, 'INV-202606-86948', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:28'),
(23, 54, NULL, 'INV-202606-37149', 6, 2026, 115000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(24, 55, NULL, 'INV-202606-75899', 6, 2026, 300000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(25, 56, NULL, 'INV-202606-86465', 6, 2026, 300000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(26, 57, NULL, 'INV-202606-91880', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(27, 58, NULL, 'INV-202606-79321', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(28, 59, NULL, 'INV-202606-20511', 6, 2026, 100000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(29, 60, NULL, 'INV-202606-89622', 6, 2026, 125000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(30, 61, NULL, 'INV-202606-11459', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(31, 62, NULL, 'INV-202606-71712', 6, 2026, 115000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(32, 63, NULL, 'INV-202606-79836', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(33, 64, NULL, 'INV-202606-23238', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(34, 65, NULL, 'INV-202606-34583', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:29'),
(35, 66, NULL, 'INV-202606-23057', 6, 2026, 125000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(36, 67, NULL, 'INV-202606-26394', 6, 2026, 100000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(37, 68, NULL, 'INV-202606-43956', 6, 2026, 125000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(38, 69, NULL, 'INV-202606-34161', 6, 2026, 250000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(39, 70, NULL, 'INV-202606-59096', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(40, 71, NULL, 'INV-202606-27491', 6, 2026, 125000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(41, 72, NULL, 'INV-202606-96289', 6, 2026, 300000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(42, 73, NULL, 'INV-202606-38221', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(43, 74, NULL, 'INV-202606-76241', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(44, 75, NULL, 'INV-202606-76022', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(45, 76, NULL, 'INV-202606-43928', 6, 2026, 300000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(46, 77, NULL, 'INV-202606-73818', 6, 2026, 100000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:30'),
(47, 78, NULL, 'INV-202606-62849', 6, 2026, 115000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:31'),
(48, 79, NULL, 'INV-202606-69042', 6, 2026, 240000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:31'),
(49, 80, NULL, 'INV-202606-77374', 6, 2026, 160000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:31'),
(50, 81, NULL, 'INV-202606-93923', 6, 2026, 220000, 'qris', 'bukti.png', 'lunas', 'baru', '2026-06-02', '2026-06-10 13:14:31'),
(51, 84, NULL, 'INV-202606-195', 6, 2026, 100000, 'qris', 'BUKTI-INV-202606-195-1781147575.png', 'lunas', 'perpanjang', '2026-06-11', '2026-06-11 03:12:40'),
(52, 85, NULL, 'INV-202606-765', 6, 2026, 115000, 'transfer', 'BUKTI-INV-202606-765-1781155176.png', 'lunas', 'perpanjang', '2026-06-11', '2026-06-11 05:19:29'),
(53, 85, 2, 'INV-UPG-1781155845', 6, 2026, 220000, 'transfer', 'BUKTI-INV-UPG-1781155845-1781155857.png', 'lunas', 'upgrade', '2026-06-11', '2026-06-11 05:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `role`, `created_at`) VALUES
(11, 'anuwani', '$2y$10$ApslX/dRq0ZK9NZmmXyp1O86B5a69Wt6FzM9bpys4dtZ7FvpZX93q', 'admin', '2026-05-22 01:40:00'),
(32, 'admin', '$2y$10$oz06YarHFgURLVmgDQVAyO9oT3u.roknu6rAEzryVcAy7VEiVWQUa', 'admin', '2026-06-02 03:07:00'),
(42, 'user_test_697_1', '$2y$10$xy1z8XSNhQXRXPrtuLELlO5hgqPpd1zm/ZwU9d3lDZcDA4vs/iEdS', 'customer', '2026-06-10 13:14:27'),
(43, 'user_test_271_2', '$2y$10$7TQi1OwlGx4cxeXWyBbdqeAZJZ5sqOR1UDfad8qWUz7ox2bZwhR4O', 'customer', '2026-06-10 13:14:27'),
(44, 'user_test_963_3', '$2y$10$Fc5sjlcQijuX.O3avis/2uQMUAnhIDYHIrCfaILGRUBBjcs/oHWuC', 'customer', '2026-06-10 13:14:27'),
(45, 'user_test_524_4', '$2y$10$LwYCIsZ7bwHqTEO4RVtIV.rpD5IwW9sOKAjfy5Z8cPxrFwUDK.Wdy', 'customer', '2026-06-10 13:14:27'),
(46, 'user_test_640_5', '$2y$10$XbSRsKYRIzP68ND3QG2sVu1oleZ6KQaDthBVSd0Ffwld6Lo0ipO7.', 'customer', '2026-06-10 13:14:27'),
(47, 'user_test_509_6', '$2y$10$0g6U9/7/deGrj4tBESUik.1hFoQmzMD8ENEAjzuzJ.GpP5Ot9mTba', 'customer', '2026-06-10 13:14:27'),
(48, 'user_test_792_7', '$2y$10$uO3h/mey9GzqAlAggBPk4.QgRg1uweI9tKVxx0iksfvxl/X6NtEGS', 'customer', '2026-06-10 13:14:27'),
(49, 'user_test_170_8', '$2y$10$hT0nAQ2cIw9EkhaqrrgDUu4jeEuXrp/I0K6o5xOPx2YWQMhbyQmHW', 'customer', '2026-06-10 13:14:27'),
(50, 'user_test_350_9', '$2y$10$yDXa/LM3FqJQ4igJvzSutuaHUZcg0nj4dfOcsevqEGKSw5X3JpZn.', 'customer', '2026-06-10 13:14:27'),
(51, 'user_test_346_10', '$2y$10$Xg2OJEAfla68ND6j46R9C.4AAY8YDO5geUW1yY6Sgt/hezFaAMhli', 'customer', '2026-06-10 13:14:28'),
(52, 'user_test_282_11', '$2y$10$BZevhrga19.7FM87sR83cuZVqaMQvH6O3Cl6GnT.wOvOP26aZj3le', 'customer', '2026-06-10 13:14:28'),
(53, 'user_test_588_12', '$2y$10$svNzl4uKWmS3ylN2XxcwZueisVjAq3Npc6WIUIWeuskVtRV1sei.K', 'customer', '2026-06-10 13:14:28'),
(54, 'user_test_371_13', '$2y$10$nVfqggMJDWVZyb8x/c7gq.9cG.xIg8jLgvynBDmGK0hTZZ5rXfPd2', 'customer', '2026-06-10 13:14:28'),
(55, 'user_test_102_14', '$2y$10$EDX1kgYjBQ3bFThWOBr86.s6Hh7mipxFJvDbVTXSMaAdHOaGy2KP.', 'customer', '2026-06-10 13:14:28'),
(56, 'user_test_787_15', '$2y$10$Bm3zNDV8uEj9X0jdPFQeDeilBemYxpQI5lK7L2paXW/A.0EtTglwi', 'customer', '2026-06-10 13:14:28'),
(57, 'user_test_223_16', '$2y$10$zniysjUP.N/7Ht3g5hLaee2nspZjGO.zZZcC0Hp.fNBxcVBNLzkQ.', 'customer', '2026-06-10 13:14:28'),
(58, 'user_test_340_17', '$2y$10$vE7DhJXPDuXJZG3R4XsDeO7djYRvyfoI0c7wKSvxalXlMbJ5Y435m', 'customer', '2026-06-10 13:14:28'),
(59, 'user_test_252_18', '$2y$10$k88oedxPVv3ygUKlh5VR8exhvfaSd80B4Xpza9mfs3QrxsCZV4PGy', 'customer', '2026-06-10 13:14:28'),
(60, 'user_test_631_19', '$2y$10$fV5aU.y8C/0ePqIv93MucudNs3w9xDQy3NZot9yV9xIOZAio7yBuC', 'customer', '2026-06-10 13:14:28'),
(61, 'user_test_561_20', '$2y$10$1924sihGjqKxJMuo/OGIQe.rIQcw3IxvlWNqXQFDdtbsNEn0yxrBq', 'customer', '2026-06-10 13:14:28'),
(62, 'user_test_566_21', '$2y$10$k1W772PTyOGINHXCCoUjGuJH.zf/qHcL3PYZ32Q/aVC7okLwSuCnm', 'customer', '2026-06-10 13:14:28'),
(63, 'user_test_104_22', '$2y$10$zgPYHkx1YJO.rSvAXoNMEeEVKOqS1u.fca.P6YeFFdlfbjSqwegNi', 'customer', '2026-06-10 13:14:28'),
(64, 'user_test_651_23', '$2y$10$U.bt407PbmYG76KmpdsL..hkh1XNF5kA2dMVBX8LFyEoVuSs.XvH.', 'customer', '2026-06-10 13:14:29'),
(65, 'user_test_644_24', '$2y$10$bHoQolrydtCLxTwklKxbmeEe6h7FxjD6xov.53lQhG6MRgjkgzmVG', 'customer', '2026-06-10 13:14:29'),
(66, 'user_test_890_25', '$2y$10$xPQQclsbe75D.u5.cpd3wOAwNdoNeS0gSBphPZ94tbOg8nfOrGEAO', 'customer', '2026-06-10 13:14:29'),
(67, 'user_test_788_26', '$2y$10$TIGu1mgmHjYpXksIYi6H8OQgEP/vqmjA8gURE/pfmstIMM5umej7m', 'customer', '2026-06-10 13:14:29'),
(68, 'user_test_613_27', '$2y$10$iw6tOf5vigwPYNu3o83wYuWJ1s8buzGePZRn0gyCCThzFvqH4xw8O', 'customer', '2026-06-10 13:14:29'),
(69, 'user_test_345_28', '$2y$10$SwJKaC0EamKDKT0m/zI2ne9r4EGWB7RrNsUGKfqAlOUR.za2oEzHW', 'customer', '2026-06-10 13:14:29'),
(70, 'user_test_508_29', '$2y$10$jJOiJHWy0GqekPdqudDNFOTn6V6DBwo5prCc1XnKHqipTVR18uJkK', 'customer', '2026-06-10 13:14:29'),
(71, 'user_test_394_30', '$2y$10$qEGUhFZJrBanGCTFMfo8fOec4vCLwAahone/H8HHPAGwc4LeDnhay', 'customer', '2026-06-10 13:14:29'),
(72, 'user_test_361_31', '$2y$10$gr8jikDuCfoGeqypnR2VJuA1D9e2Ay31jvd/FqTQ1KRc3zAv4cXgy', 'customer', '2026-06-10 13:14:29'),
(73, 'user_test_671_32', '$2y$10$YLhzt79oK5zvTPCk5k8BU.2kUc39TKlft//Tq4xg83BlPqVaLYeBO', 'customer', '2026-06-10 13:14:29'),
(74, 'user_test_255_33', '$2y$10$maQukKx2wwkCxDV4vI7Tj.EzUxW7g1w7sh6TsQTpBd9RKGAUtBaJW', 'customer', '2026-06-10 13:14:29'),
(75, 'user_test_722_34', '$2y$10$N9w7i0Wv/EY.q0fidAVVW.pWNeZ/JlebbX9fKXlOjeidP.blK0uC.', 'customer', '2026-06-10 13:14:29'),
(76, 'user_test_355_35', '$2y$10$LXT5thj0DR5xRbrZr.qz7eMvmVT8os2v6kWfXtu29khP.Yr.xl68O', 'customer', '2026-06-10 13:14:30'),
(77, 'user_test_629_36', '$2y$10$hHDMVlIv6XwpvxLytADZmezf1ec29LccFpjOilSKsKxUCs2NZ/TUe', 'customer', '2026-06-10 13:14:30'),
(78, 'user_test_163_37', '$2y$10$TRxUyhb5QIZgKHy45FmS6O9awihgo5Cz7ufIAtpxA66wfHA2qH9pu', 'customer', '2026-06-10 13:14:30'),
(79, 'user_test_340_38', '$2y$10$gMUR/PYl869bAcHUyukM9.b2sqPsg1gW9A7LrX0vIhevhfyuBgkZ2', 'customer', '2026-06-10 13:14:30'),
(80, 'user_test_671_39', '$2y$10$.D9SlIq8jEOiveW3e3I9TeJOzyR16Ki0hGg8IbNoiQ6CLojwAFQi6', 'customer', '2026-06-10 13:14:30'),
(81, 'user_test_724_40', '$2y$10$2Uuh3z.MhqWqF2DCOPqc9uvlLKWyEoxBLJxrqP0CpYtZ2EVYZBHZi', 'customer', '2026-06-10 13:14:30'),
(82, 'user_test_383_41', '$2y$10$YetPJAKeIAkTTC8ObtCsueAFLTM/6AlaV9hmKUmFuqV6WJRSzmxva', 'customer', '2026-06-10 13:14:30'),
(83, 'user_test_212_42', '$2y$10$cg5fFDkbhRGkYCOc49oyLuqKnEpENwhK39DszH4EQQ2nw8eDtjGz.', 'customer', '2026-06-10 13:14:30'),
(84, 'user_test_525_43', '$2y$10$0jaIiQIiapDyrjgm/cROwOV2WCb.hYhAouiIdRuiQ8KVYzWHdR0Ve', 'customer', '2026-06-10 13:14:30'),
(85, 'user_test_810_44', '$2y$10$2.tmEijOQUfYTS/YSoHedeQ7HAm3gJlV9ugYjbW/GM1GYQOcyVKoi', 'customer', '2026-06-10 13:14:30'),
(86, 'user_test_986_45', '$2y$10$ji9gkrvrvT6SPJuJXbkyMO2cvdoISU4AiaFgY.osJ0Ui10EgU6xPC', 'customer', '2026-06-10 13:14:30'),
(87, 'user_test_407_46', '$2y$10$s2RRmkADPAkJECyBgtW5weBowGRc5aQo9W6/w9wLNaHynXvIUcyz6', 'customer', '2026-06-10 13:14:30'),
(88, 'user_test_499_47', '$2y$10$i8Co1cRIr1F8//MNi6jM/uVWyWi0p2AIqJTcuZPdJDFWblLVFag7a', 'customer', '2026-06-10 13:14:31'),
(89, 'user_test_521_48', '$2y$10$HDj01HxwRTnYo0mOZWqQVOZ8P2tDH.0IwVvuv2mQjNVNB19s0u2wK', 'customer', '2026-06-10 13:14:31'),
(90, 'user_test_936_49', '$2y$10$7btLSUqG.AoNqGE7QBVqvucKA0jwacOimsXUUqE7y7pMYAWOCuqT.', 'customer', '2026-06-10 13:14:31'),
(91, 'user_test_868_50', '$2y$10$u4/Dtd2iBeZXdlELFbNNneKwG/d8co/7OnsYILphY5hPRO0D/Vtti', 'customer', '2026-06-10 13:14:31'),
(92, 'raysal', '$2y$10$G3iThvbfPp7kRaGZsybdUureTrGPcrTtEiQeOfgS1VQ6dlv09cOQO', 'customer', '2026-06-10 13:22:49'),
(93, 'gena', '$2y$10$ufcBy0r3Ki7A3fjkW8zSX.iA6TXzIenNPMepKKYqyj8dJ9wDvWqIS', 'customer', '2026-06-11 05:18:16');

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
  ADD KEY `id_paket` (`id_paket`);

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
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id_customer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tb_langganan`
--
ALTER TABLE `tb_langganan`
  MODIFY `id_langganan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id_paket` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  MODIFY `id_pemasangan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

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
  ADD CONSTRAINT `tb_pemasangan_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_pemasangan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`);

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_langganan`) REFERENCES `tb_langganan` (`id_langganan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
