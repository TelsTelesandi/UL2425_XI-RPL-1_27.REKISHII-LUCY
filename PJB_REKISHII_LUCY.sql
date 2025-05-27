-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 23, 2025 at 02:14 AM
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
-- Database: `pjb_rekishii_lucy`
--

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `PEMINJAMAN_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `SARANA_ID` int NOT NULL,
  `TANGGAL_PINJAM` varchar(200) NOT NULL,
  `TANGGAL_KEMBALI` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `JUMLAH_PINJAM` int NOT NULL,
  `STATUS` varchar(10) NOT NULL,
  `CATATAN_ADMIN` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`PEMINJAMAN_ID`, `USER_ID`, `SARANA_ID`, `TANGGAL_PINJAM`, `TANGGAL_KEMBALI`, `JUMLAH_PINJAM`, `STATUS`, `CATATAN_ADMIN`) VALUES
(1, 1, 1, '2025-01-15', NULL, 1, 'menunggu', NULL),
(2, 2, 2, '2025-02-10', NULL, 2, 'disetujui', 'Disetujui untuk kegiatan presentasi.'),
(3, 3, 3, '2025-03-05', NULL, 1, 'ditolak', 'tidak perlu.'),
(4, 4, 4, '2025-04-01', '2025-04-02', 1, 'selesai', 'Sudah dikembalikan dalam kondisi baik.'),
(5, 5, 5, '2025-05-20', NULL, 3, 'disetujui', 'Disetujui untuk acara sekolah.'),
(6, 1, 4, '2025-05-23', NULL, 3, 'disetujui', 'kelas king');

-- --------------------------------------------------------

--
-- Table structure for table `sarana`
--

CREATE TABLE `sarana` (
  `SARANA_ID` int NOT NULL,
  `NAMA_SARANA` varchar(200) NOT NULL,
  `JUMLAH_TERSEDIA` int NOT NULL,
  `LOKASI` varchar(200) NOT NULL,
  `KETERANGAN` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sarana`
--

INSERT INTO `sarana` (`SARANA_ID`, `NAMA_SARANA`, `JUMLAH_TERSEDIA`, `LOKASI`, `KETERANGAN`) VALUES
(1, 'Proyektor Epson', 8, 'Ruang Multimedia', 'Digunakan untuk presentasi dan pembelajaran.'),
(2, 'Laptop Lenovo', 30, 'Lab Komputer', 'Laptop untuk kebutuhan siswa dan guru.'),
(3, 'Speaker Portable', 10, 'Gudang Sarpras', 'Untuk kegiatan di lapangan atau aula.'),
(4, 'Kabel Roll 10m', 47, 'Gudang', 'Kabel perpanjangan listrik 10 meter.'),
(5, 'Meja Lipat Plastik', 20, 'Ruang Serbaguna', 'Meja lipat untuk acara, seminar, atau pelatihan.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int NOT NULL,
  `ID_CARD` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ROLE` varchar(5) NOT NULL,
  `NAMA_LENGKAP` varchar(200) NOT NULL,
  `JENIS_PENGGUNA` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `ID_CARD`, `USERNAME`, `PASSWORD`, `ROLE`, `NAMA_LENGKAP`, `JENIS_PENGGUNA`) VALUES
(1, '10293847', 'agus123', '$2y$10$DsUf4aaFJ9OSEKgBoLXKR.7YodIzElkYb60Zds4nxySYlwy76fscm', 'user', 'Agus Wijaya', 'siswa'),
(2, '84736291', 'intan789', '$2y$10$x0G5v60Q5I.yGI3KsmAqZeD4aCwKUYa0L8TnYuTsltM/o5hijh/I6', 'user', 'Intan Permata', 'siswa'),
(3, '92018374', 'budiadmin', '$2y$10$inPSZpJ.htIAml8xtwgVs.Ig2PMSUScHgiX6z6sy2U4e2Aez0EAYa', 'admin', 'Budi Santosa', 'guru'),
(4, '18273645', 'linda_guru', '$2y$10$BEWEH03Xy1N/J5B4sGX79.MoQmdjFh6PcOtF1wOLBX.HCg/c1vjDC', 'user', 'Linda Kartika', 'guru'),
(5, '73628491', 'reza321', '$2y$10$oaRJeaUp69A2y3HgKLB5l./bow8HwGBEYgnO8ReNHA6p.hXyiQyrK', 'user', 'Reza Maulana', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`PEMINJAMAN_ID`),
  ADD KEY `rekasi_ke_user` (`USER_ID`),
  ADD KEY `relasi_ke_sarana` (`SARANA_ID`);

--
-- Indexes for table `sarana`
--
ALTER TABLE `sarana`
  ADD PRIMARY KEY (`SARANA_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `PEMINJAMAN_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sarana`
--
ALTER TABLE `sarana`
  MODIFY `SARANA_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `rekasi_ke_user` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relasi_ke_sarana` FOREIGN KEY (`SARANA_ID`) REFERENCES `sarana` (`SARANA_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
