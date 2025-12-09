-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250914.f72491a1c0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 09, 2025 at 06:07 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gowisata_crud`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `id` bigint UNSIGNED NOT NULL,
  `pengguna_id` bigint UNSIGNED NOT NULL,
  `destinasi_id` bigint UNSIGNED NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`id`, `pengguna_id`, `destinasi_id`, `dibuat_pada`) VALUES
(4, 1, 1, '2025-12-09 08:12:47'),
(5, 3, 2, '2025-12-09 08:40:33');

-- --------------------------------------------------------

--
-- Table structure for table `destinasi`
--

CREATE TABLE `destinasi` (
  `id` bigint UNSIGNED NOT NULL,
  `kategori_id` bigint UNSIGNED DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `slug` varchar(170) NOT NULL,
  `deskripsi` text,
  `kota` varchar(100) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `harga_tiket` bigint UNSIGNED DEFAULT NULL,
  `jam_buka` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `rata_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `jumlah_rating` int UNSIGNED NOT NULL DEFAULT '0',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `destinasi`
--

INSERT INTO `destinasi` (`id`, `kategori_id`, `nama`, `slug`, `deskripsi`, `kota`, `provinsi`, `harga_tiket`, `jam_buka`, `latitude`, `longitude`, `rata_rating`, `jumlah_rating`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, NULL, 'Gunung Maras', 'gunung-maras', '', 'Berbura', 'Bangka-Belitung Islands', 0, '', -1.8833330, 105.8333330, 5.00, 3, '2025-12-09 07:50:19', '2025-12-09 08:46:07'),
(2, NULL, 'Pantai Pasir Padi', 'pantai-pasir-padi', '', 'Pangkalpinang', 'Bangka-Belitung Islands', 0, '08:00-17:00', -2.1047897, 106.1672570, 0.00, 0, '2025-12-09 08:26:38', '2025-12-09 08:26:38'),
(3, NULL, 'Wisata Desa Namang', 'wisata-desa-namang', '', 'Desa Namang', 'Bangka-Belitung Islands', 0, '08:00-17:00', -2.3218775, 106.1898294, 0.00, 0, '2025-12-09 09:17:11', '2025-12-09 09:17:11'),
(4, NULL, 'Alun Alun Toboali', 'alun-alun-toboali', '', 'Toboali', 'Bangka-Belitung Islands', 5000, '08:00-22:00', -3.0107089, 106.4549280, 0.00, 0, '2025-12-09 17:44:52', '2025-12-09 17:44:52'),
(5, NULL, 'Rumput Hijau/Vila Acun', 'rumput-hijau-vila-acun', '', 'Pangkalan Baru', 'Bangka-Belitung Islands', 5000, '08:00-17:00', -2.1326040, 106.0841257, 0.00, 0, '2025-12-09 17:53:58', '2025-12-09 17:53:58'),
(6, NULL, 'Bukit Pau', 'bukit-pau', '', 'Pangkalan Baru', 'Bangka-Belitung Islands', 0, '', -2.2188551, 106.1020090, 0.00, 0, '2025-12-09 18:06:24', '2025-12-09 18:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `gambar_destinasi`
--

CREATE TABLE `gambar_destinasi` (
  `id` bigint UNSIGNED NOT NULL,
  `destinasi_id` bigint UNSIGNED NOT NULL,
  `url_gambar` varchar(255) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `utama` tinyint(1) NOT NULL DEFAULT '0',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gambar_destinasi`
--

INSERT INTO `gambar_destinasi` (`id`, `destinasi_id`, `url_gambar`, `keterangan`, `utama`, `dibuat_pada`) VALUES
(1, 1, '1765266619_images.jpg', NULL, 1, '2025-12-09 07:50:19'),
(2, 2, '1765268798_MENYULAP-PASIR-PADI-JADI-ANCOL-DI-PANGKALPINANG.png', NULL, 1, '2025-12-09 08:26:38'),
(3, 2, '1765268798_624_Keunikan_Pantai_Pasir_Padi_yang_memiliki_garis_pantai_sepanjang_100_hingga_300_m_adalah_ombak_ya2.jpg', NULL, 0, '2025-12-09 08:26:38'),
(4, 3, '1765271831_Mendung_di_desa_namang.jpg', NULL, 1, '2025-12-09 09:17:11'),
(5, 3, '1765271831_Thumb_Yt_Fb.jpg', NULL, 0, '2025-12-09 09:17:11'),
(6, 4, '1765302292_WhatsApp-Image-2025-01-17-at-13.44.06-e1737096925816.jpeg', NULL, 1, '2025-12-09 17:44:52'),
(7, 5, '1765302838_Screenshot_2025-12-10_005309.png', NULL, 1, '2025-12-09 17:53:58'),
(8, 6, '1765303584_Screenshot_2025-12-10_010425.png', NULL, 1, '2025-12-09 18:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` bigint UNSIGNED NOT NULL,
  `destinasi_id` bigint UNSIGNED NOT NULL,
  `pengguna_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `komentar` text,
  `tanggal_kunjungan` date DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `destinasi_id`, `pengguna_id`, `rating`, `komentar`, `tanggal_kunjungan`, `dibuat_pada`) VALUES
(1, 1, 3, 5, 'tes', NULL, '2025-12-09 08:04:11'),
(2, 1, 1, 5, 'tesstt123', NULL, '2025-12-09 08:14:01'),
(7, 1, 4, 5, 'tes', NULL, '2025-12-09 08:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `peran` enum('pengguna','admin') DEFAULT 'pengguna',
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password_hash`, `peran`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'te', 'varisaaulya6@gmail.com', '1765268362_images.jpg', '$2y$10$yDPRDVocD5AWHHLdZoxUneHB0yz2f7Sp9.a7YVvFjJcAg3wNCgci2', 'pengguna', '2025-12-09 07:10:57', '2025-12-09 08:19:22'),
(3, 'Administrator', 'admin@gowisata.com', '1765268565_624_Keunikan_Pantai_Pasir_Padi_yang_memiliki_garis_pantai_sepanjang_100_hingga_300_m_adalah_ombak_ya2.jpg', '$2b$12$m3rTdl3cREHsGkf3pR5q1eCc656S9fBk0OPjJvu3BWSLo7FTzqCgS', 'admin', '2025-12-09 07:17:34', '2025-12-09 08:22:45'),
(4, 'user', 'user@gowisata.com', '1765269962_images.jpg', '$2y$10$G73LCawMbWyQePQ4pvz4OuKoWC38c0evQlFi704krlARMm/LgeqAq', 'pengguna', '2025-12-09 08:45:39', '2025-12-09 08:46:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookmark_pengguna_id_destinasi_id_unique` (`pengguna_id`,`destinasi_id`),
  ADD KEY `bookmark_pengguna_id_index` (`pengguna_id`),
  ADD KEY `bookmark_destinasi_id_index` (`destinasi_id`);

--
-- Indexes for table `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `destinasi_slug_unique` (`slug`),
  ADD KEY `destinasi_kategori_id_index` (`kategori_id`),
  ADD KEY `destinasi_kota_index` (`kota`),
  ADD KEY `destinasi_provinsi_index` (`provinsi`);

--
-- Indexes for table `gambar_destinasi`
--
ALTER TABLE `gambar_destinasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar_destinasi_destinasi_id_index` (`destinasi_id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ulasan_destinasi_id_pengguna_id_unique` (`destinasi_id`,`pengguna_id`),
  ADD KEY `ulasan_destinasi_id_index` (`destinasi_id`),
  ADD KEY `ulasan_pengguna_id_index` (`pengguna_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gambar_destinasi`
--
ALTER TABLE `gambar_destinasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_destinasi_id_foreign` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`),
  ADD CONSTRAINT `bookmark_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `gambar_destinasi`
--
ALTER TABLE `gambar_destinasi`
  ADD CONSTRAINT `gambar_destinasi_destinasi_id_foreign` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`);

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_destinasi_id_foreign` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`),
  ADD CONSTRAINT `ulasan_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
