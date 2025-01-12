-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 12:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apotek`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) NOT NULL,
  `alamat` varchar(20) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `id_user` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(20) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan','','') NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `id_user` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama`, `alamat`, `jenis_kelamin`, `nomor_telepon`, `id_user`) VALUES
(3, 'Aira Salsa K', 'jl.koya barat', 'Perempuan', '081223453', 15);

-- --------------------------------------------------------

--
-- Table structure for table `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id_konsultasi` int(100) NOT NULL,
  `id_pasien` int(10) NOT NULL,
  `tanggal_konsultasi` date NOT NULL,
  `id_dokter` int(10) NOT NULL,
  `status_nomor_antrian` tinyint(1) NOT NULL,
  `waktu_konsultasi` time NOT NULL,
  `nomor_antrian` varchar(10) DEFAULT NULL,
  `keluhan` varchar(50) NOT NULL,
  `riwayat_penyakit` varchar(100) NOT NULL,
  `status_konsultasi` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konsultasi`
--

INSERT INTO `konsultasi` (`id_konsultasi`, `id_pasien`, `tanggal_konsultasi`, `id_dokter`, `status_nomor_antrian`, `waktu_konsultasi`, `nomor_antrian`, `keluhan`, `riwayat_penyakit`, `status_konsultasi`) VALUES
(28, 19, '2024-12-13', 3, 1, '13:00:00', NULL, 'e', 'e', 'Dibatalkan'),
(29, 18, '2024-12-13', 3, 1, '08:00:00', 'P-2', 's', 's', 'Selesai'),
(30, 18, '2024-12-13', 3, 1, '13:00:00', NULL, 'e', 'e', 'Dibatalkan'),
(31, 20, '2024-12-13', 3, 1, '08:00:00', NULL, 'e', 'e', 'Dibatalkan'),
(32, 19, '2024-12-14', 3, 1, '08:00:00', 'P-1', 'er', 'e', 'Selesai'),
(33, 20, '2024-12-14', 3, 1, '08:00:00', 'P-2', 'e', 'e', 'Selesai'),
(34, 19, '2024-12-14', 3, 1, '19:00:00', 'M-1', 'sakit perut', 'mag', 'Selesai'),
(35, 19, '2024-12-14', 3, 1, '13:00:00', 'S-1', 'sakit gigi', 'malaria', 'Selesai'),
(36, 21, '2024-12-14', 3, 1, '08:00:00', 'P-3', 'sakit gigi', 'tidak ada', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(25) NOT NULL,
  `deskripsi` varchar(100) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `jenis_obat` varchar(50) NOT NULL,
  `tanggal_kadaluarsa` date NOT NULL,
  `harga` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `deskripsi`, `nama_obat`, `jenis_obat`, `tanggal_kadaluarsa`, `harga`) VALUES
(1, 'w', 'paracetamol', 'Umum', '2025-12-14', 0),
(2, 'meredakan mag', 'milanta', 'Umum', '2025-12-14', 0),
(3, 'meredekan sakit gigi', 'ambroxol', 'Umum', '2025-12-14', 0),
(4, 'meredakan panas', 'sanmol', 'Umum', '2025-12-14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(10) NOT NULL,
  `umur` int(3) NOT NULL,
  `id_dokter` int(10) DEFAULT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan','','') NOT NULL,
  `nama` varchar(30) NOT NULL,
  `id_user` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `umur`, `id_dokter`, `nomor_telepon`, `alamat`, `jenis_kelamin`, `nama`, `id_user`) VALUES
(5, 21, 3, '081129283', 'jl. Abe', 'Laki-laki', 'ida bagus', 16),
(9, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'fa', NULL),
(10, 19, NULL, '081111111', 'jalan kutilang sentani', 'Perempuan', 'salsa', 18),
(11, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'sa', NULL),
(12, 11, NULL, '08122222222', 'koya barat', 'Laki-laki', 'malik umar', 19),
(13, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'malik', NULL),
(14, 20, NULL, '08122222222', 'jln. jayawijaya koya barat', 'Laki-laki', 'malik rusyda ', 20),
(15, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'rusyda', NULL),
(16, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'rusyda', NULL),
(17, 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', 'rusyda', NULL),
(18, 212, NULL, '0821082023', 'jl. wijaya Arso 2', 'Laki-laki', 'Irfan ', 21),
(19, 11, NULL, '0892834323', 'abe', 'Laki-laki', 'ida', 22),
(20, 12, NULL, '0812923423', 'jl. garuda sentani', 'Perempuan', 'fira', 23),
(21, 12, NULL, '0912882324', 'jl. koya', 'Laki-laki', 'ary', 24);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(8) NOT NULL,
  `alamat` varchar(20) NOT NULL,
  `id_dokter` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `id_pasien` int(10) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan','','') NOT NULL,
  `id_user` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `alamat`, `id_dokter`, `nama`, `id_pasien`, `jenis_kelamin`, `id_user`) VALUES
(2, 'jl.koya timur', 3, 'Irfan Nurdah', 5, 'Laki-laki', 14);

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

CREATE TABLE `rekam_medis` (
  `id_rekam_medis` int(40) NOT NULL,
  `id_dokter` int(10) NOT NULL,
  `id_konsultasi` int(100) NOT NULL,
  `catatan_dokter` varchar(50) NOT NULL,
  `diagnosis` varchar(30) NOT NULL,
  `id_pasien` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekam_medis`
--

INSERT INTO `rekam_medis` (`id_rekam_medis`, `id_dokter`, `id_konsultasi`, `catatan_dokter`, `diagnosis`, `id_pasien`) VALUES
(1, 3, 32, 's', 's', 19),
(3, 3, 33, 'd', 'd', 20),
(4, 3, 34, 'makan teratur', 'kurang makan', 19),
(5, 3, 35, 'rajin sikat gigi setelah makan', 'gigi berlubang', 19),
(6, 3, 36, 'kurangi manis2', 'gigi lubang', 21);

-- --------------------------------------------------------

--
-- Table structure for table `resep`
--

CREATE TABLE `resep` (
  `id_resep` int(20) NOT NULL,
  `id_pasien` int(10) NOT NULL,
  `id_dokter` int(10) NOT NULL,
  `dosis` varchar(100) NOT NULL,
  `tanggal_resep` date NOT NULL,
  `id_konsultasi` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resep`
--

INSERT INTO `resep` (`id_resep`, `id_pasien`, `id_dokter`, `dosis`, `tanggal_resep`, `id_konsultasi`) VALUES
(1, 19, 3, 'Sesuai instruksi', '2024-12-14', 32),
(3, 20, 3, 'Sesuai aturan pakai', '2024-12-13', 31),
(4, 19, 3, 'Sesuai aturan pakai', '2024-12-13', 28),
(5, 19, 3, 'Sesuai aturan pakai', '2024-12-14', 32),
(6, 21, 3, 'Sesuai aturan pakai', '2024-12-14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resep_obat`
--

CREATE TABLE `resep_obat` (
  `id_resep_obat` int(30) NOT NULL,
  `id_resep` int(20) NOT NULL,
  `id_obat` int(25) NOT NULL,
  `instruksi_pengguna` varchar(100) NOT NULL,
  `jumlah_obat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resep_obat`
--

INSERT INTO `resep_obat` (`id_resep_obat`, `id_resep`, `id_obat`, `instruksi_pengguna`, `jumlah_obat`) VALUES
(1, 3, 1, 'w', 1),
(2, 4, 2, 'sebelum makan', 1),
(3, 5, 3, 'setelah makan', 1),
(4, 6, 4, 'setelah makan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `spesialis`
--

CREATE TABLE `spesialis` (
  `id_spesialis` int(15) NOT NULL,
  `nama_spesialis` varchar(30) NOT NULL,
  `nomor_lisensi` int(9) NOT NULL,
  `id_dokter` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spesialis`
--

INSERT INTO `spesialis` (`id_spesialis`, `nama_spesialis`, `nomor_lisensi`, `id_dokter`) VALUES
(2, 'gigi', 10203, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(5) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('Petugas','Pasien','Dokter','Admin','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(14, 'fan', 'a', 'Petugas'),
(15, 'ara', 'ara', 'Dokter'),
(16, 'da', 'da', 'Pasien'),
(17, 'fa', 'fa', 'Pasien'),
(18, 'sa', 'sa', 'Pasien'),
(19, 'malik', '1', 'Pasien'),
(20, 'ali', 'a', 'Pasien'),
(21, 'irfan', 'an', 'Pasien'),
(22, 'de', 'e', 'Pasien'),
(23, 'faa', 'aa', 'Pasien'),
(24, 'ary', 'a', 'Pasien');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `fk_user_admin` (`id_user`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD KEY `fk_user_dokter` (`id_user`);

--
-- Indexes for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id_konsultasi`),
  ADD KEY `id_pasien` (`id_pasien`,`id_dokter`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `fk_user` (`id_user`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD KEY `id_dokter` (`id_dokter`,`id_pasien`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `fk_user_petugas` (`id_user`);

--
-- Indexes for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD PRIMARY KEY (`id_rekam_medis`),
  ADD KEY `id_dokter` (`id_dokter`,`id_konsultasi`,`id_pasien`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_konsultasi` (`id_konsultasi`);

--
-- Indexes for table `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_pasien` (`id_pasien`,`id_dokter`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_konsultasi` (`id_konsultasi`);

--
-- Indexes for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD PRIMARY KEY (`id_resep_obat`),
  ADD KEY `id_resep` (`id_resep`,`id_obat`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `spesialis`
--
ALTER TABLE `spesialis`
  ADD PRIMARY KEY (`id_spesialis`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id_konsultasi` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id_rekam_medis` int(40) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `resep`
--
ALTER TABLE `resep`
  MODIFY `id_resep` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `resep_obat`
--
ALTER TABLE `resep_obat`
  MODIFY `id_resep_obat` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `spesialis`
--
ALTER TABLE `spesialis`
  MODIFY `id_spesialis` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_user_admin` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `fk_user_dokter` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD CONSTRAINT `konsultasi_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `konsultasi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);

--
-- Constraints for table `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pasien_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);

--
-- Constraints for table `petugas`
--
ALTER TABLE `petugas`
  ADD CONSTRAINT `fk_dokter_petugas` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pasien_petugas` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_petugas` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD CONSTRAINT `rekam_medis_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `rekam_medis_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`),
  ADD CONSTRAINT `rekam_medis_ibfk_3` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id_konsultasi`);

--
-- Constraints for table `resep`
--
ALTER TABLE `resep`
  ADD CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `resep_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`),
  ADD CONSTRAINT `resep_ibfk_3` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id_konsultasi`);

--
-- Constraints for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD CONSTRAINT `resep_obat_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`),
  ADD CONSTRAINT `resep_obat_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`);

--
-- Constraints for table `spesialis`
--
ALTER TABLE `spesialis`
  ADD CONSTRAINT `spesialis_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
