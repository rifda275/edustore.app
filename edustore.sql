-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 06:37 AM
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
-- Database: `edustore`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `tanggal_ditambahkan` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) NOT NULL,
  `kode_barang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `id_kategori`, `harga`, `tanggal_ditambahkan`, `stok`, `gambar`, `kode_barang`) VALUES
(39, 'buku', 13, 3000.00, '2025-04-26 03:58:56', 93, '1745411599_buku 58.png', '21123450'),
(40, 'busur', 11, 3000.00, '2025-04-23 12:34:04', 100, '1745411644_busur.png', '76543210'),
(41, 'kuas lukis 1 set', 15, 30000.00, '2025-04-24 14:35:26', 99, '1745411731_kuas lukis set.png', '5012345678900');

-- --------------------------------------------------------

--
-- Table structure for table `detailtransaksi`
--

CREATE TABLE `detailtransaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailtransaksi`
--

INSERT INTO `detailtransaksi` (`id_detail`, `id_transaksi`, `id_barang`, `jumlah`, `subtotal`) VALUES
(107, 93, 39, 1, 3000.00),
(108, 94, 39, 1, 3000.00),
(109, 95, 40, 1, 3000.00),
(110, 96, 39, 1, 3000.00),
(111, 97, 39, 1, 3000.00),
(112, 98, 39, 1, 3000.00),
(113, 99, 40, 1, 3000.00),
(114, 100, 41, 1, 30000.00),
(115, 101, 41, 1, 30000.00),
(116, 102, 39, 1, 3000.00),
(117, 103, 41, 1, 30000.00),
(118, 104, 39, 1, 3000.00),
(119, 105, 39, 1, 3000.00),
(120, 106, 39, 1, 3000.00),
(121, 107, 39, 2, 6000.00),
(122, 108, 39, 1, 3000.00),
(123, 109, 39, 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `kategoribarang`
--

CREATE TABLE `kategoribarang` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoribarang`
--

INSERT INTO `kategoribarang` (`id_kategori`, `nama_kategori`) VALUES
(11, 'alat sekolah'),
(12, 'fiksi'),
(13, 'alat tulis'),
(14, 'buku'),
(15, 'alat lukis');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `total_pendapatan` decimal(10,2) NOT NULL,
  `jumlah_barang_terjual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Manager','Kasir') NOT NULL,
  `tanggal_ditambahkan` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `password`, `role`, `tanggal_ditambahkan`) VALUES
(10, 'yanto', 'yanto', '$2y$10$s9uiM/dllZYBIV6bGi.wrO8jdcwIyZtzXpUxlBns63LLpbyuYZ36a', 'Admin', '2025-04-12 13:39:48'),
(12, 'siti', 'siti', '$2y$10$HviGGy7pTz8tt/GIh0Cu../g/22NTr0xRS3Fw9z4rQvIExfZzn/kq', 'Manager', '2025-04-08 05:26:26'),
(13, 'bima', 'bima', '$2y$10$wsZFcQ0smYG1.UsKlDveF.fnmkkUDw7VrNlY.48mAwimgknphvL3G', 'Admin', '2025-04-09 05:34:25'),
(14, 'lia', 'lia', '$2y$10$LwpcIBvloXC4tLchdcAmXeYlt6Gtwp/tVckC7NDUO4Ktg3xKGbXP2', 'Kasir', '2025-04-08 02:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `restokbarang`
--

CREATE TABLE `restokbarang` (
  `id_restok` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_restok` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restokbarang`
--

INSERT INTO `restokbarang` (`id_restok`, `id_pengguna`, `id_barang`, `jumlah`, `tanggal_restok`) VALUES
(24, 10, 39, 100, '2025-04-23');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) NOT NULL,
  `metode_pembayaran` varchar(255) NOT NULL,
  `bayar` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `tanggal_transaksi`, `total_harga`, `metode_pembayaran`, `bayar`) VALUES
(72, 14, '2025-04-07 19:41:11', 8000.00, 'Tunai', 10000),
(73, 14, '2025-04-07 19:52:50', 3000.00, 'Tunai', 3000),
(74, 14, '2025-04-07 20:47:43', 16000.00, 'Tunai', 20000),
(75, 14, '2025-04-08 07:17:21', 6000.00, 'Tunai', 10000),
(76, 14, '2025-04-08 07:20:04', 3000.00, 'Tunai', 3000),
(77, 14, '2025-04-14 08:06:21', 5000.00, 'Tunai', 6000),
(78, 14, '2025-04-14 08:07:56', 5000.00, 'Tunai', 6000),
(79, 14, '2025-04-14 12:10:36', 3000.00, 'Tunai', 4000),
(80, 14, '2025-04-14 20:15:08', 5000.00, 'Tunai', 5000),
(81, 14, '2025-04-15 15:01:11', 3000.00, 'Tunai', 4000),
(82, 14, '2025-04-15 15:20:39', 10000.00, 'Tunai', 20000),
(83, 14, '2025-04-16 21:52:16', 8000.00, 'Tunai', 10000),
(84, 14, '2025-04-16 21:57:20', 5000.00, 'Tunai', 5000),
(85, 14, '2025-04-16 21:58:53', 3000.00, 'Tunai', 3000),
(86, 14, '2025-04-16 22:02:46', 3000.00, 'Tunai', 3000),
(87, 10, '2025-04-18 12:27:17', 33000.00, 'Tunai', 35000),
(88, 10, '2025-04-18 12:31:03', 33000.00, 'Tunai', 35000),
(89, 10, '2025-04-18 12:36:28', 25000.00, 'Tunai', 30000),
(90, 14, '2025-04-18 18:18:56', 0.00, '', 0),
(91, 14, '2025-04-18 18:21:12', 0.00, '', 0),
(92, 14, '2025-04-20 18:49:01', 3000.00, 'Tunai', 5000),
(93, 14, '2025-04-23 21:05:50', 3000.00, 'Tunai', 3000),
(94, 10, '2025-04-24 19:27:42', 3000.00, 'Tunai', 5000),
(95, 10, '2025-04-24 20:55:08', 3000.00, 'Tunai', 3000),
(96, 14, '2025-04-24 20:57:22', 3000.00, 'Tunai', 3000),
(97, 14, '2025-04-24 21:02:09', 3000.00, 'Tunai', 3000),
(98, 10, '2025-04-24 21:02:36', 3000.00, 'Tunai', 5000),
(99, 10, '2025-04-24 21:03:00', 3000.00, 'Tunai', 3000),
(100, 10, '2025-04-24 21:12:17', 30000.00, 'Tunai', 30000),
(101, 10, '2025-04-24 21:13:29', 30000.00, 'Tunai', 30000),
(102, 10, '2025-04-24 21:26:42', 3000.00, 'Tunai', 3000),
(103, 10, '2025-04-24 21:35:26', 30000.00, 'Tunai', 30000),
(104, 10, '2025-04-26 09:39:04', 3000.00, 'Tunai', 3000),
(105, 10, '2025-04-26 09:39:43', 3000.00, 'Tunai', 3000),
(106, 14, '2025-04-26 10:39:10', 3000.00, 'Tunai', 3000),
(107, 10, '2025-04-26 10:54:56', 6000.00, 'Tunai', 10000),
(108, 10, '2025-04-26 10:58:34', 3000.00, 'Tunai', 3000),
(109, 10, '2025-04-26 10:58:56', 3000.00, 'Tunai', 3000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `detailtransaksi_ibfk_1` (`id_transaksi`);

--
-- Indexes for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `restokbarang`
--
ALTER TABLE `restokbarang`
  ADD PRIMARY KEY (`id_restok`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `restokbarang`
--
ALTER TABLE `restokbarang`
  MODIFY `id_restok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategoribarang` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD CONSTRAINT `detailtransaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `detailtransaksi_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `restokbarang`
--
ALTER TABLE `restokbarang`
  ADD CONSTRAINT `restokbarang_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL,
  ADD CONSTRAINT `restokbarang_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
