-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 04:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email_admin` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `id_user`, `nama_admin`, `email_admin`) VALUES
(4, 10, 'Administrator', 'adminanuwani@gmail.com'),
(5, 11, 'anuwani admin', 'anuwanien@gmail.com'),
(6, 18, 'Admin anuwani', 'wanii@gmail.com'),
(7, 21, 'admin baru', 'baru@gmail.com'),
(10, 24, 'sallllll', 'salk@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id_customer` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `alamat_customer` text NOT NULL,
  `telepon_customer` varchar(20) DEFAULT NULL,
  `email_customer` varchar(100) DEFAULT NULL,
  `sumber_customer` enum('online','offline') DEFAULT NULL,
  `status_customer` enum('aktif','pending','nonaktif') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id_customer`, `id_user`, `nama_customer`, `alamat_customer`, `telepon_customer`, `email_customer`, `sumber_customer`, `status_customer`, `created_at`) VALUES
(4, 5, 'bagus dandi', 'Pamanukan, Subang', '081144567734', 'bagus@gmail.com', 'online', 'pending', '2026-05-21 23:57:51'),
(6, 7, 'Santi', 'Kupang', '087755663344', 'sans@gmail.com', 'online', 'pending', '2026-05-22 00:06:21'),
(8, 14, 'Gena brodi', 'subang', '22222335355', 'genss@gmail.com', 'offline', 'aktif', '2026-05-22 02:09:32'),
(9, 15, 'diax', 'Subang', '2345678', 'dd@gmail.com', 'online', 'pending', '2026-05-22 11:05:27'),
(10, 16, 'dryan', 'Perumnas, Subang', '098765432', 'dryan@gmail.com', 'online', 'pending', '2026-05-22 12:11:22'),
(11, 17, 'deva nur', 'Bandung', '765432', 'dev@gmail.com', 'offline', 'aktif', '2026-05-22 12:21:31'),
(12, 19, 'liaa', 'Pagaden, jabar', '2345678', 'laaa@gmail.com', 'online', 'pending', '2026-05-22 12:40:30'),
(13, 20, 'budiono siregar', 'Cigadung', '87654321', 'buds@gmail.com', 'offline', 'aktif', '2026-05-22 12:48:18'),
(14, 25, 'Raysal Gena', 'Subang, JawaBarat', '0987654', 'raysal@gmial.com', 'online', 'pending', '2026-05-25 04:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `tb_langganan`
--

CREATE TABLE `tb_langganan` (
  `id_langganan` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_langganan` enum('aktif','berhenti') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_langganan`
--

INSERT INTO `tb_langganan` (`id_langganan`, `id_customer`, `id_paket`, `tanggal_mulai`, `tanggal_selesai`, `status_langganan`) VALUES
(1, 8, 1, '2026-05-22', '2026-06-21', 'aktif'),
(2, 6, 1, '2026-05-22', '2026-06-21', ''),
(3, 4, 1, '2026-05-22', '2026-06-21', ''),
(4, 4, 1, '2026-05-22', '2026-06-21', ''),
(5, 4, 1, '2026-05-22', '2026-06-21', ''),
(6, 9, 2, '2026-05-22', '2026-06-21', ''),
(7, 9, 2, '2026-05-22', '2026-06-21', ''),
(8, 6, 2, '2026-05-22', '2026-06-21', ''),
(9, 10, 2, '2026-05-22', '2026-06-21', ''),
(10, 11, 3, '2026-05-22', '2026-06-21', 'aktif'),
(11, 12, 2, '2026-05-22', '2026-06-21', ''),
(12, 13, 2, '2026-05-22', '2026-06-21', 'aktif'),
(13, 14, 4, '2026-05-25', '2026-06-24', ''),
(14, 14, 4, '2026-05-25', '2026-06-24', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_paket`
--

CREATE TABLE `tb_paket` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `kecepatan` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
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
(11, 'Paket Booster', 215000, '80 Mbps', 'Gaming tanpa batas\r\nKoneksi terbaik\r\nLayanan bagus\r\n', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemasangan`
--

CREATE TABLE `tb_pemasangan` (
  `id_pemasangan` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_pasang` date DEFAULT NULL,
  `alamat_pasang` text NOT NULL,
  `status_pemasangan` enum('menunggu','diproses','terpasang','dibatalkan') DEFAULT 'menunggu',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pemasangan`
--

INSERT INTO `tb_pemasangan` (`id_pemasangan`, `id_customer`, `id_paket`, `tanggal_pengajuan`, `tanggal_pasang`, `alamat_pasang`, `status_pemasangan`, `catatan`, `created_at`) VALUES
(1, 6, 1, '2026-05-22', NULL, 'Subang', '', '', '2026-05-22 04:40:34'),
(2, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'pasangnya hari sabtu pagi jam 9', '2026-05-22 07:07:00'),
(3, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'pasangnya hari sabtu pagi jam 9', '2026-05-22 07:17:17'),
(4, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'pasangnya hari sabtu pagi jam 9', '2026-05-22 07:17:20'),
(5, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'hari minggu jam 11', '2026-05-22 07:25:30'),
(6, 6, 1, '2026-05-22', NULL, 'Bogor', '', '', '2026-05-22 07:26:42'),
(7, 6, 1, '2026-05-22', NULL, 'Bogor', '', '', '2026-05-22 07:26:45'),
(8, 6, 1, '2026-05-22', NULL, 'Bogor', '', '', '2026-05-22 07:26:46'),
(9, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'hari selasa ya pasangnya jam 3', '2026-05-22 07:29:56'),
(10, 6, 1, '2026-05-22', NULL, 'Bogor', '', 'hari selasa ya pasangnya jam 3', '2026-05-22 07:33:14'),
(11, 4, 1, '2026-05-22', NULL, 'Pamanukan, Subang', '', '', '2026-05-22 07:57:57'),
(12, 4, 1, '2026-05-22', NULL, 'Pamanukan, Subang', '', '', '2026-05-22 07:58:18'),
(13, 4, 1, '2026-05-22', NULL, 'Pamanukan, Subang', '', '', '2026-05-22 08:10:18'),
(14, 9, 2, '2026-05-22', NULL, 'Subang', '', '', '2026-05-22 11:06:54'),
(15, 9, 2, '2026-05-22', NULL, 'Subang', '', '', '2026-05-22 11:09:49'),
(16, 6, 2, '2026-05-22', NULL, 'Kupang', '', '', '2026-05-22 11:44:11'),
(17, 10, 2, '2026-05-22', NULL, 'Perumnas, Subang', '', 'pasang di hari minggu jam 9 pagi', '2026-05-22 12:16:02'),
(18, 11, 3, '2026-05-22', '2026-05-22', 'Bandung', '', 'Pendaftaran offline langsung diinput oleh Admin.', '2026-05-22 12:21:31'),
(19, 12, 2, '2026-05-22', NULL, 'Pagaden', '', 'pasang hari kamis sore', '2026-05-22 12:43:01'),
(20, 13, 2, '2026-05-22', '2026-05-22', 'Cigadung', '', 'Pendaftaran offline langsung diinput oleh Admin.', '2026-05-22 12:48:18'),
(21, 14, 4, '2026-05-25', NULL, 'Perumahan, Subang, JawaBarat', '', 'Pemasangan dilakukan pada hari minggu pukul 9 pagi', '2026-05-25 04:38:15'),
(22, 14, 4, '2026-05-25', NULL, 'Subang, JawaBarat', '', '', '2026-05-25 04:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_langganan` int(11) NOT NULL,
  `kode_invoice` varchar(50) DEFAULT NULL,
  `bulan_tagihan` int(11) DEFAULT NULL,
  `tahun_tagihan` int(11) DEFAULT NULL,
  `jumlah_bayar` int(11) NOT NULL,
  `metode_pembayaran` enum('transfer','qris','cash') DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','menunggu_verifikasi','lunas') DEFAULT 'belum_bayar',
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_langganan`, `kode_invoice`, `bulan_tagihan`, `tahun_tagihan`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`, `tanggal_bayar`, `created_at`) VALUES
(1, 1, 'INV-202605-8', 5, 2026, 125000, NULL, NULL, '', NULL, '2026-05-22 04:28:29'),
(2, 2, 'INV-202605-664', 5, 2026, 125000, 'transfer', 'BUKTI-INV-202605-664-1779697097.jpg', 'menunggu_verifikasi', '2026-05-25', '2026-05-22 07:33:14'),
(3, 3, 'INV-202605-869', 5, 2026, 125000, NULL, NULL, '', NULL, '2026-05-22 07:57:57'),
(4, 4, 'INV-202605-974', 5, 2026, 125000, NULL, NULL, '', NULL, '2026-05-22 07:58:18'),
(5, 5, 'INV-202605-742', 5, 2026, 125000, NULL, NULL, '', NULL, '2026-05-22 08:10:18'),
(6, 6, 'INV-202605-995', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 11:06:54'),
(7, 7, 'INV-202605-500', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 11:09:49'),
(8, 8, 'INV-202605-637', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 11:44:11'),
(9, 9, 'INV-202605-754', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 12:16:02'),
(10, 10, 'INV-202605-11', 5, 2026, 100000, NULL, NULL, '', NULL, '2026-05-22 12:21:54'),
(11, 11, 'INV-202605-709', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 12:43:02'),
(12, 12, 'INV-202605-13', 5, 2026, 220000, NULL, NULL, '', NULL, '2026-05-22 12:48:36'),
(13, 13, 'INV-202605-332', 5, 2026, 300000, NULL, NULL, '', NULL, '2026-05-25 04:38:15'),
(14, 14, 'INV-202605-584', 5, 2026, 300000, NULL, NULL, '', NULL, '2026-05-25 04:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'wow', '$2y$10$oqgHaWsk3Q9dLu/pR5g1PuCtdZSpky9MZsCD5LLIAysrZg5K8JzG6', 'customer', '2026-05-21 13:40:38'),
(3, 'budi', '$2y$10$iLMm6MTGhqsyqp07Tphc0OCRgHrMZ5CEi/baOwjac/9OHIfFKgzyK', 'customer', '2026-05-21 23:49:15'),
(4, 'joko', '$2y$10$ZgJ4btkyZHvDf9Gbz6erIeLG0We/eD3WW3auLrM12a5II5GTwVlK2', 'customer', '2026-05-21 23:52:42'),
(5, 'bagus', '$2y$10$ExfKs5SUhJ6SM.owaptdI.qiut5bmysdsEdiToNaf8S5Kjt9i7sEm', 'customer', '2026-05-21 23:57:51'),
(6, 'andi', '$2y$10$GYHl5UuPfC6g7itTgdqmTuzNJ86Xi.L7GgNqAyJZzi163o.LdO6n6', 'customer', '2026-05-22 00:03:54'),
(7, 'santi', '$2y$10$mHVESZBw.y57JHxisUvcFOt2YcheKFLV7vFRcaa3m/t6aO4mhi2Jq', 'customer', '2026-05-22 00:06:21'),
(10, 'admin', '$2y$10$QVmTYNzgy8bsme78HB4Tl.2.IbsEsfpV6iuVb37r.L4C/KDr1.l8K', 'admin', '2026-05-22 01:12:37'),
(11, 'anuwani', '$2y$10$ApslX/dRq0ZK9NZmmXyp1O86B5a69Wt6FzM9bpys4dtZ7FvpZX93q', 'admin', '2026-05-22 01:40:00'),
(14, 'gena', '$2y$10$BwI4soeRLn7dKAuimIxGH.2PbaZ1hZ8YiUz9rWeVlAnr9AziBFdAK', 'customer', '2026-05-22 02:09:31'),
(15, 'dd', '$2y$10$f4chkZPB3nV/RF.92/IBbeKnpZ6ganvERUkHGCy6rFSUJlObp7Uv2', 'customer', '2026-05-22 11:05:27'),
(16, 'dryan', '$2y$10$Qb5qfNsmftKZXYKdSSTr7ePry05RK8oMe7ZD7frq8I3N4MmVG4pnm', 'customer', '2026-05-22 12:11:22'),
(17, 'deva', '$2y$10$Plrdcq1eljBFvC6LDYsOsuCYfmrZMK8Ra66R/yAMF8OJ5CQr8Tm3m', 'customer', '2026-05-22 12:21:31'),
(18, 'wani', '$2y$10$L/SXIvX90j4FUVwc3cEkuOzJZwQSGtECb1TEpb5fbSojBPoL2i4UG', 'admin', '2026-05-22 12:23:27'),
(19, 'lia', '$2y$10$Enu.HjuC.DI2tO1GR0BInOf/YKAqDZDU56JUQPOkpChVb1Y48nUFG', 'customer', '2026-05-22 12:40:30'),
(20, 'bud', '$2y$10$qfQ.0G8QTbLR5Wy3GtsRrONahGdCuB2AqiglSmYzDwLLoLJTrMvKu', 'customer', '2026-05-22 12:48:18'),
(21, 'baru', '$2y$10$CmOpvosH303./7qpbK8lJORMIVE6wcZOmA/rHgpj5Z78lgjXV9jk6', 'admin', '2026-05-22 12:50:11'),
(24, 'sal', '$2y$10$8.DNT5oEft4fBVRAQp0ije8J/X.h3eMYblf7TNdIuAoY2YxsWaPge', 'admin', '2026-05-25 02:18:11'),
(25, 'raysal', '$2y$10$k6aJz1glZLCZb6NCEzobhe6FFDcgKEuxG/yIy0UvzyBHCkzBrA6am', 'customer', '2026-05-25 04:36:04');

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_langganan`
--
ALTER TABLE `tb_langganan`
  MODIFY `id_langganan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  MODIFY `id_pemasangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
