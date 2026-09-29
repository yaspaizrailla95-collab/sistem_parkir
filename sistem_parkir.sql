-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 15, 2026 at 04:26 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

-- Buat database otomatis jika belum ada
CREATE DATABASE IF NOT EXISTS `sistem_parkir` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistem_parkir`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_parkir`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int NOT NULL,
  `terisi` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `id_kategori`, `nama_area`, `kapasitas`, `terisi`) VALUES
(1, NULL, 'Gedung A - Lantai 1', 50, 20),
(2, NULL, 'Gedung A - Lantai 2', 50, 10),
(3, NULL, 'Basement Motor', 100, 45),
(4, NULL, 'Outdoor Mobil', 30, 5),
(5, NULL, 'VIP Area', 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Mobil Reguler'),
(2, 'Mobil VIP'),
(3, 'Mobil Listrik / EV'),
(4, 'Motor Reguler'),
(5, 'Motor Besar / Moge'),
(6, 'Motor Listrik'),
(7, 'Sepeda / BICYCLE'),
(8, 'Khusus Disabilitas (Mobil/Motor)'),
(9, 'Valet Mobil'),
(10, 'Area Inap (Mobil/Motor)');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int NOT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `jenis_kendaraan` varchar(20) NOT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user`) VALUES
(1, 'B 1234 ABC', 'mobil', 'Hitam', 'Rian', 1),
(2, 'D 5678 EFG', 'motor', 'Merah', 'Doni', 2),
(3, 'B 9999 XYZ', 'mobil', 'Putih', 'Siska', 1),
(4, 'D 3333 LMN', 'motor', 'Biru', 'Eko', 3),
(5, 'B 4321 JKL', 'lainnya', 'Kuning', 'Tono', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `waktu_aktivitas` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'Menambahkan data tarif baru', '2026-08-29 07:30:00'),
(2, 2, 'Input kendaraan masuk B 1234 ABC', '2026-08-29 08:00:00'),
(3, 2, 'Proses pembayaran parkir B 1234 ABC', '2026-08-29 10:00:00'),
(4, 3, 'Input kendaraan masuk B 9999 XYZ', '2026-08-29 10:00:00'),
(5, 4, 'Melihat rekap laporan bulanan', '2026-08-29 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') NOT NULL,
  `tarif_per_jam` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(1, 'motor', 2000.00),
(2, 'mobil', 5000.00),
(3, 'lainnya', 10000.00),
(4, 'motor', 3000.00),
(5, 'mobil', 7000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_parkir` int NOT NULL,
  `id_kendaraan` int NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int NOT NULL,
  `durasi_jam` int DEFAULT NULL,
  `biaya_total` decimal(10,2) DEFAULT '0.00',
  `status` enum('masuk','keluar') NOT NULL DEFAULT 'masuk',
  `id_user` int NOT NULL,
  `id_area` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_parkir`, `id_kendaraan`, `waktu_masuk`, `waktu_keluar`, `id_tarif`, `durasi_jam`, `biaya_total`, `status`, `id_user`, `id_area`) VALUES
(1, 1, '2026-08-29 08:00:00', '2026-08-29 10:00:00', 2, 2, 10000.00, 'keluar', 2, 1),
(2, 2, '2026-08-29 09:15:00', '2026-08-29 12:15:00', 1, 3, 6000.00, 'keluar', 2, 3),
(3, 3, '2026-08-29 10:00:00', NULL, 2, NULL, NULL, 'masuk', 3, 4),
(4, 4, '2026-08-29 11:30:00', NULL, 1, NULL, NULL, 'masuk', 3, 3),
(5, 5, '2026-08-29 07:00:00', '2026-08-29 11:00:00', 3, 4, 40000.00, 'keluar', 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'Admin Utama', 'admin', '$2y$12$eXmzyN8cIgnEib0HILPzf.bBf/vZIjJXttsfJA5Ou8wioS5tjZ/A.', 'admin', 1),
(2, 'Budi Petugas', 'budi_p', '$2y$12$qlfU6C8wU0imYhxOXl3ErOFvV7A9ZIpwFfvzfeWNkWu6M6FRdIneW', 'petugas', 1),
(3, 'Siti Petugas', 'siti_p', '$2y$12$qlfU6C8wU0imYhxOXl3ErOFvV7A9ZIpwFfvzfeWNkWu6M6FRdIneW', 'petugas', 1),
(4, 'Pak Boss', 'owner1', '$2y$12$6Ufm/Hs8tr7o2K7ThvsFeO2uMssgvZ2SFfuUq7a.Gz8lbJS5/KyiW', 'owner', 1),
(5, 'Andi Petugas', 'andi_p', '$2y$12$qlfU6C8wU0imYhxOXl3ErOFvV7A9ZIpwFfvzfeWNkWu6M6FRdIneW', 'petugas', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`),
  ADD KEY `fk_area_kategori` (`id_kategori`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `fk_kendaraan_user` (`id_user`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_log_user` (`id_user`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_parkir`),
  ADD KEY `fk_transaksi_kendaraan` (`id_kendaraan`),
  ADD KEY `fk_transaksi_tarif` (`id_tarif`),
  ADD KEY `fk_transaksi_user` (`id_user`),
  ADD KEY `fk_transaksi_area` (`id_area`);

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
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_parkir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD CONSTRAINT `fk_area_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD CONSTRAINT `fk_kendaraan_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_area` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_tarif` FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif` (`id_tarif`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- ===== SiParkir functional settings =====
CREATE TABLE IF NOT EXISTS `tb_pengaturan` (
  `id_pengaturan` int NOT NULL,
  `nama_sistem` varchar(100) NOT NULL DEFAULT 'SiParkir Management',
  `nama_admin` varchar(100) NOT NULL DEFAULT 'Admin Utama',
  `email_admin` varchar(120) NOT NULL DEFAULT 'admin@siparkir.com',
  `telepon` varchar(30) NOT NULL DEFAULT '',
  `alamat` varchar(255) NOT NULL DEFAULT '',
  `toleransi_menit` int NOT NULL DEFAULT 10,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengaturan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_pengaturan` (`id_pengaturan`, `nama_sistem`, `nama_admin`, `email_admin`, `telepon`, `alamat`, `toleransi_menit`)
VALUES (1, 'SiParkir Management', 'Admin Utama', 'admin@siparkir.com', '', '', 10)
ON DUPLICATE KEY UPDATE `id_pengaturan`=`id_pengaturan`;

-- =========================================
-- KONFIGURASI LOGIN ADMIN (sudah terintegrasi)
-- Username: admin
-- Password: admin123
-- =========================================
UPDATE `tb_user`
SET `password` = '$2y$12$eXmzyN8cIgnEib0HILPzf.bBf/vZIjJXttsfJA5Ou8wioS5tjZ/A.',
    `status_aktif` = 1,
    `role` = 'admin'
WHERE `username` = 'admin';
