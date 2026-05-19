-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 06:25 AM
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
-- Database: `isp_projek`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_admin` varchar(255) DEFAULT NULL,
  `email_admin` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id_customer` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_customer` varchar(100) DEFAULT NULL,
  `alamat_customer` text DEFAULT NULL,
  `telepon_customer` varchar(20) DEFAULT NULL,
  `email_customer` varchar(100) DEFAULT NULL,
  `id_paket` int(11) DEFAULT NULL,
  `status_paket` varchar(30) DEFAULT 'Pending',
  `sumber_customer` varchar(20) DEFAULT NULL,
  `status_customer` varchar(30) DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id_customer`, `id_user`, `nama_customer`, `alamat_customer`, `telepon_customer`, `email_customer`, `id_paket`, `status_paket`, `sumber_customer`, `status_customer`) VALUES
(3, 4, 'bro', 'Kuningan', '12345678', 'bro@gmail.com', NULL, 'Pending', NULL, 'Aktif'),
(6, 9, 'Santi putri', 'Bogor', '62888888888', 'santi@gmail.com', NULL, 'Pending', 'Online', 'Aktif'),
(7, 10, 'agus kusuma', 'Balikpapan', '0812344678824', 'zagus@gmail.com', 5, 'Pending', 'Offline', 'Aktif'),
(8, 11, 'tegar', 'Subang', '00081111111', 'sampah@gmail.cpm', NULL, 'Pending', 'Online', 'Aktif'),
(9, 12, 'dryan', 'perumnas', '0896782227707', 'dryanpasha@gmail.com', NULL, 'Pending', 'Online', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tb_paket`
--

CREATE TABLE `tb_paket` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `kecepatan` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_paket`
--

INSERT INTO `tb_paket` (`id_paket`, `nama_paket`, `harga`, `kecepatan`, `deskripsi`) VALUES
(5, 'Paket Cemara', 125000, '30 Mbps', 'Fleksibel\r\nKecepatan konsisten\r\nAkses 24 jam\r\nMemudahkan pekerjaan'),
(6, 'Paket Jitu', 350000, '60 Mbps', 'Fleksibel\r\n Kecepatan Puting Beliung\r\n Memudahkan Gawe');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemasangan`
--

CREATE TABLE `tb_pemasangan` (
  `id_pemasangan` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `alamat_pasang` text NOT NULL,
  `tanggal_pasang` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `status_pemasangan` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_customer` varchar(100) DEFAULT NULL,
  `telepon_customer` varchar(20) DEFAULT NULL,
  `email_customer` varchar(100) DEFAULT NULL,
  `metode_pembayaran` varchar(30) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pemasangan`
--

INSERT INTO `tb_pemasangan` (`id_pemasangan`, `id_customer`, `id_paket`, `alamat_pasang`, `tanggal_pasang`, `catatan`, `status_pemasangan`, `created_at`, `nama_customer`, `telepon_customer`, `email_customer`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`) VALUES
(2, 6, 5, 'Pesona permata hijau 2', '2026-05-18', 'pemasangannya di siang hari', 'Pending', '2026-05-18 13:19:55', 'Santi putri', '62888888888', 'santi@gmail.com', NULL, NULL, NULL),
(3, 6, 5, 'subang, jawabarat', '2026-05-18', '', 'Pending', '2026-05-18 13:46:42', 'Santi putri', '62888888888', 'santi@gmail.com', NULL, NULL, 'Belum Bayar'),
(4, 6, 5, 'www', '2026-05-18', '', 'Pending', '2026-05-18 13:59:03', 'Santi putri', '62888888888', 'santi@gmail.com', NULL, NULL, 'Belum Bayar'),
(5, 9, 6, 'perumnas blok 5', '2026-05-19', 'buruan kadie min!!!!!!!', 'Pending', '2026-05-19 02:59:52', 'dryan', '0896782227707', 'dryanpasha@gmail.com', 'Transfer Bank', '1779159624-dfdlvl0.jpeg', 'Menunggu Konfirmasi');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `kode_invoice` varchar(50) DEFAULT NULL,
  `bulan_tagihan` int(11) NOT NULL,
  `tahun_tagihan` int(11) NOT NULL,
  `jumlah_bayar` int(11) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('Belum Bayar','Menunggu Konfirmasi','Lunas') DEFAULT 'Belum Bayar',
  `tanggal_transaksi` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `role`) VALUES
(4, 'bro', '$2y$10$7SD3Cdnb8y/4N7R6EBHSeeYJkKuoH7RyjASzxEp9l9dKs46zKOENK', 'customer'),
(6, 'admin', '$2y$10$u3ch3z8WScCac9bVlAdBEufrdDusf/5oavx36tqUcKp3wTbrOsuka', 'admin'),
(9, 'santi', '$2y$10$AevfdWCraadYTpqj05OELOKSmxT4anleWNCmVnClWKnTx6kQAguru', 'customer'),
(10, 'agus', '$2y$10$V0ERbwjtmhyNzg.HS9l0UeyA4/Cr7scFWWomb63ip7aVy4Dw6O1hq', 'customer'),
(11, 'niga', '$2y$10$4ra9mAFfk3HkfeMrOQLwlOl3TKoDndkIjjVDyr4W9r5u.Jearhf7C', 'customer'),
(12, 'jepang', '$2y$10$UWKo0g9iWcmGIHBmCdB8quY405GegrHcYm.VHuzoREiHldYQmceru', 'customer'),
(13, 'liacantik01', '$2y$10$niHix5jyoBXrCC8.L5JfquN8VKBLA3KGxXdr0OqXsSgudYiLjGH1q', 'customer'),
(14, 'gena', '38fe428bec60ffd062789586d33b02d0', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD PRIMARY KEY (`id_customer`),
  ADD KEY `fk_customer_paket` (`id_paket`),
  ADD KEY `fk_customer_user` (`id_user`);

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
  ADD KEY `fk_pemasangan_customer` (`id_customer`),
  ADD KEY `fk_pemasangan_paket` (`id_paket`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_customer` (`id_customer`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  MODIFY `id_pemasangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD CONSTRAINT `tb_admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD CONSTRAINT `fk_customer_paket` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`),
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`),
  ADD CONSTRAINT `tb_customer_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_pemasangan`
--
ALTER TABLE `tb_pemasangan`
  ADD CONSTRAINT `fk_pemasangan_customer` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`),
  ADD CONSTRAINT `fk_pemasangan_paket` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`);

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_customer` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`),
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
