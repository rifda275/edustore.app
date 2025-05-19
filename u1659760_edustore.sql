-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 18, 2025 at 05:17 AM
-- Server version: 10.11.11-MariaDB-cll-lve
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u1659760_edustore`
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
  `kode_barang` varchar(255) DEFAULT NULL,
  `keuntungan` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `id_kategori`, `harga`, `tanggal_ditambahkan`, `stok`, `gambar`, `kode_barang`, `keuntungan`) VALUES
(41, 'kuas lukis 1 set', 14, 20000.00, '2025-05-17 16:06:06', 9, '1747311013_kuas lukis set.png', '5012345678900', NULL),
(42, 'buku', 11, 3000.00, '2025-05-16 06:35:23', 20, '1747311414_buku 58.png', '76543210', NULL),
(43, 'kaca pembesar', 11, 5000.00, '2025-05-16 06:34:56', 20, '1747312708_bulpoin.png', '21123450', NULL),
(44, 'juhghfg', 12, 9876.00, '2025-05-16 06:32:05', 90876, '1747377125_busur.png', '76543210', NULL),
(45, '12346trked', 16, 9876.00, '2025-05-16 06:33:30', 10, '1747377210_bulpoin.png', '21123450', NULL);

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
(114, 93, 41, 1, 20000.00);

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
(14, 'alat lukis'),
(15, 'alat praktik'),
(16, '987tyrfygujhasnmd');

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

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `id_transaksi`, `tanggal`, `total_pendapatan`, `jumlah_barang_terjual`) VALUES
(3, 87, '2025-05-02', 7000.00, 2),
(4, 88, '2025-05-04', 7000.00, 2),
(5, 89, '2025-05-04', 7000.00, 2),
(6, 90, '2025-05-08', 1500.00, 1),
(7, 91, '2025-05-13', 1500.00, 1),
(8, 92, '2025-05-14', 30000.00, 2),
(9, 93, '2025-05-17', 20000.00, 1);

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
(10, 'yanti', 'yanti', '$2y$10$D.BNkgmcjD6Z4IhuozmeSegQyfaMyAA80wIyacae7QhrQrCDW7b6K', 'Admin', '2025-05-08 04:37:10'),
(14, 'lia', 'lia', '$2y$10$QbuIul368nsZvRe3qc2Y0.Wlq1MANf6h/lzdhGfZpU9NpuMk8U6rG', 'Kasir', '2025-05-07 19:33:13'),
(17, 'rifda', 'rifda', '$2y$10$E7RY25wEWGMMu1LSiQNAtunaQ7vQqBTKOLBYrlmNThSIQPsNJg9Gi', 'Admin', '2025-05-07 15:49:25'),
(18, 'fitri', 'fitri', '$2y$10$GPWlL189EuIpuW1H.EKiTu8D/5mTmrLmyw1Xe7Bs0uTzaF1x4V8US', 'Kasir', '2025-05-14 07:16:09'),
(19, 'bima', 'bima', '$2y$10$auWV17LHCqlUykvKVRtPpex70.1aXSRQQQAnJZ9hq.EkdwPZqrUj6', 'Manager', '2025-05-14 07:29:44');

-- --------------------------------------------------------

--
-- Table structure for table `restokbarang`
--

CREATE TABLE `restokbarang` (
  `id_restok` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_restok` date DEFAULT current_timestamp(),
  `harga_beli` decimal(15,2) DEFAULT NULL,
  `laba` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restokbarang`
--

INSERT INTO `restokbarang` (`id_restok`, `id_pengguna`, `id_barang`, `jumlah`, `tanggal_restok`, `harga_beli`, `laba`) VALUES
(67, 17, 43, 10, '2025-05-16', 20000.00, 0.00),
(68, 17, 42, 10, '2025-05-16', 20000.00, 0.00);

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
(87, 10, '2025-05-02 08:08:43', 7000.00, 'Tunai', 10000),
(88, 10, '2025-05-04 15:35:22', 7000.00, 'Tunai', 10000),
(89, 10, '2025-05-04 15:47:56', 7000.00, 'Tunai', 10000),
(90, 10, '2025-05-08 16:11:54', 1500.00, 'Tunai', 2000),
(91, 17, '2025-05-13 18:39:22', 1500.00, 'Tunai', 2000),
(92, 17, '2025-05-14 14:23:11', 30000.00, 'Tunai', 50000),
(93, 17, '2025-05-17 23:06:06', 20000.00, 'Tunai', 20000);

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
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `restokbarang`
--
ALTER TABLE `restokbarang`
  MODIFY `id_restok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

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
