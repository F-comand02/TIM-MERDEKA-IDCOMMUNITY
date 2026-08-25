-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 20, 2026 at 04:49 PM
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
-- Database: `semangat_kemerdekaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `aksi`
--

CREATE TABLE `aksi` (
  `id` int NOT NULL,
  `kategori_id` int NOT NULL,
  `nama_aksi` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `poin` int DEFAULT '10',
  `tingkat_kesulitan` enum('Mudah','Sedang','Sulit') DEFAULT 'Mudah',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aksi`
--

INSERT INTO `aksi` (`id`, `kategori_id`, `nama_aksi`, `deskripsi`, `poin`, `tingkat_kesulitan`, `created_at`) VALUES
(1, 1, 'Berbagi Buku', 'Berikan buku yang masih layak digunakan kepada anak atau masyarakat yang membutuhkan.', 20, 'Mudah', '2026-08-20 16:47:07'),
(2, 1, 'Mengajar Anak', 'Luangkan waktu untuk membantu anak-anak belajar di lingkungan sekitar.', 30, 'Sedang', '2026-08-20 16:47:07'),
(3, 1, 'Literasi Digital', 'Ajarkan masyarakat cara menggunakan teknologi dan internet secara aman.', 30, 'Sedang', '2026-08-20 16:47:07'),
(4, 2, 'Olahraga 30 Menit', 'Lakukan aktivitas fisik minimal 30 menit untuk menjaga kesehatan tubuh.', 10, 'Mudah', '2026-08-20 16:47:07'),
(5, 2, 'Edukasi Hidup Sehat', 'Bagikan informasi mengenai pola hidup sehat kepada masyarakat.', 20, 'Mudah', '2026-08-20 16:47:07'),
(6, 3, 'Bersih-Bersih Lingkungan', 'Ikut membersihkan lingkungan sekitar bersama masyarakat.', 20, 'Mudah', '2026-08-20 16:47:07'),
(7, 3, 'Tanam Pohon', 'Tanam dan rawat minimal satu pohon di lingkungan sekitar.', 30, 'Sedang', '2026-08-20 16:47:07'),
(8, 3, 'Kurangi Plastik', 'Kurangi penggunaan plastik sekali pakai dalam aktivitas sehari-hari.', 10, 'Mudah', '2026-08-20 16:47:07'),
(9, 4, 'Dukung UMKM Lokal', 'Membeli atau mempromosikan produk UMKM di sekitar tempat tinggal.', 20, 'Mudah', '2026-08-20 16:47:07'),
(10, 4, 'Promosi Produk Lokal', 'Bantu mempromosikan produk UMKM melalui media sosial.', 20, 'Mudah', '2026-08-20 16:47:07'),
(11, 5, 'Berbagi Ilmu', 'Bagikan pengetahuan atau keterampilan kepada orang lain tanpa membedakan latar belakang.', 20, 'Mudah', '2026-08-20 16:47:07'),
(12, 5, 'Bantu Akses Pendidikan', 'Bantu seseorang mendapatkan informasi atau akses terhadap pendidikan.', 30, 'Sedang', '2026-08-20 16:47:07');

INSERT IGNORE INTO `aksi` (`id`, `kategori_id`, `nama_aksi`, `deskripsi`, `poin`, `tingkat_kesulitan`) VALUES
(13, 1, 'Bangun Kelas Belajar Komunitas', 'Rancang dan jalankan kelas belajar rutin untuk minimal 10 peserta selama empat minggu.', 80, 'Sulit'),
(14, 2, 'Selenggarakan Pemeriksaan Kesehatan', 'Bantu mengorganisir kegiatan pemeriksaan kesehatan dasar bersama tenaga atau fasilitas kesehatan setempat.', 90, 'Sulit'),
(15, 3, 'Pimpin Program Pemulihan Lingkungan', 'Rancang dan koordinasikan program pemulihan lingkungan yang melibatkan warga serta memiliki hasil yang terukur.', 100, 'Sulit'),
(16, 4, 'Dampingi UMKM Naik Kelas', 'Dampingi satu UMKM lokal menyusun strategi pemasaran, pencatatan sederhana, dan rencana pengembangan usaha.', 90, 'Sulit'),
(17, 5, 'Bangun Program Inklusi Komunitas', 'Buat dan jalankan program yang memperluas akses atau kesempatan bagi kelompok yang kurang terwakili di lingkunganmu.', 100, 'Sulit');

-- --------------------------------------------------------

--
-- Table structure for table `aksi_user`
--

CREATE TABLE `aksi_user` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `aksi_id` int NOT NULL,
  `daerah` varchar(100) DEFAULT NULL,
  `wilayah` varchar(50) DEFAULT NULL,
  `bukti` text,
  `tanggal_aksi` date NOT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cerita`
--

CREATE TABLE `cerita` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nama_komunitas` varchar(150) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `cerita` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `daerah` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `sdg` varchar(100) DEFAULT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `icon`, `warna`, `sdg`, `deskripsi`) VALUES
(1, 'Pendidikan', '📚', '#DC2626', 'SDG 4', 'Aksi untuk meningkatkan akses pendidikan, literasi dan pengetahuan masyarakat.'),
(2, 'Kesehatan', '❤️', '#EAB308', 'SDG 3', 'Aksi untuk meningkatkan kesadaran dan kualitas kesehatan masyarakat.'),
(3, 'Lingkungan & Komunitas', '🌱', '#16A34A', 'SDG 11 & 13', 'Aksi menjaga lingkungan dan memperkuat kepedulian masyarakat.'),
(4, 'Ekonomi Masyarakat', '💼', '#2563EB', 'SDG 8', 'Aksi untuk mendukung UMKM, kewirausahaan dan ekonomi masyarakat.'),
(5, 'Kesetaraan', '🤝', '#9333EA', 'SDG 5 & 10', 'Aksi untuk menciptakan kesempatan dan akses yang lebih setara.');

-- --------------------------------------------------------

--
-- Table structure for table `tantangan`
--

CREATE TABLE `tantangan` (
  `id` int NOT NULL,
  `hari` int NOT NULL,
  `sdg_nomor` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tantangan`
--

INSERT INTO `tantangan` (`id`, `hari`, `sdg_nomor`, `judul`, `deskripsi`, `icon`) VALUES
(1, 1, 1, 'Berbagi dengan Sesama', 'Berikan makanan atau kebutuhan dasar kepada orang yang membutuhkan.', '🤲'),
(2, 2, 2, 'Kurangi Food Waste', 'Habiskan makanan secukupnya dan hindari membuang makanan.', '🍚'),
(3, 3, 3, 'Jaga Kesehatan', 'Lakukan olahraga minimal 30 menit hari ini.', '❤️'),
(4, 4, 4, 'Berbagi Ilmu', 'Ajarkan satu pengetahuan baru kepada orang lain.', '📚'),
(5, 5, 5, 'Dukung Kesetaraan', 'Berikan kesempatan yang sama kepada semua orang.', '⚖️'),
(6, 6, 6, 'Hemat Air', 'Kurangi penggunaan air yang tidak diperlukan.', '💧'),
(7, 7, 7, 'Hemat Energi', 'Matikan perangkat elektronik yang tidak digunakan.', '⚡'),
(8, 8, 8, 'Dukung UMKM', 'Beli atau promosikan produk UMKM lokal.', '💼'),
(9, 9, 9, 'Gunakan Teknologi Positif', 'Gunakan teknologi untuk membantu dan memberikan manfaat bagi orang lain.', '💻'),
(10, 10, 10, 'Lawan Diskriminasi', 'Perlakukan semua orang dengan adil tanpa membedakan latar belakang.', '🤝'),
(11, 11, 11, 'Bersihkan Lingkungan', 'Ikut membersihkan lingkungan sekitar.', '🏘️'),
(12, 12, 12, 'Kurangi Sampah', 'Gunakan kembali barang yang masih dapat digunakan.', '♻️'),
(13, 13, 13, 'Peduli Iklim', 'Kurangi aktivitas yang menghasilkan emisi berlebihan.', '🌍'),
(14, 14, 14, 'Jaga Lingkungan Air', 'Jangan membuang sampah ke sungai atau saluran air.', '🌊'),
(15, 15, 15, 'Tanam Pohon', 'Tanam dan rawat tanaman di lingkungan sekitar.', '🌳'),
(16, 16, 16, 'Jaga Kerukunan', 'Ciptakan lingkungan yang aman, damai dan saling menghargai.', '🕊️'),
(17, 17, 17, 'Ajak Orang Lain', 'Ajak minimal satu orang untuk ikut melakukan aksi positif.', '🇮🇩');

-- --------------------------------------------------------

--
-- Table structure for table `tantangan_user`
--

CREATE TABLE `tantangan_user` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tantangan_id` int NOT NULL,
  `status` enum('belum','selesai') DEFAULT 'belum',
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `daerah` varchar(100) DEFAULT NULL,
  `tanggal_daftar` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Default admin account for local development.
-- Email: admin@gmail.com
-- Password: admin12345
INSERT INTO `users`
  (`id`, `nama`, `email`, `password`, `role`, `daerah`)
VALUES
  (1, 'Administrator', 'admin@gmail.com', '$2y$12$nbHA388yrwNo.aKI.3Abb.ynHciV1oCT2cbDy.iwLVsP8O.qOjYDG', 'admin', 'Indonesia')
ON DUPLICATE KEY UPDATE
  `nama` = VALUES(`nama`),
  `email` = VALUES(`email`),
  `password` = VALUES(`password`),
  `role` = 'admin',
  `daerah` = VALUES(`daerah`);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aksi`
--
ALTER TABLE `aksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `aksi_user`
--
ALTER TABLE `aksi_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `aksi_id` (`aksi_id`);

--
-- Indexes for table `cerita`
--
ALTER TABLE `cerita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tantangan`
--
ALTER TABLE `tantangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hari` (`hari`);

--
-- Indexes for table `tantangan_user`
--
ALTER TABLE `tantangan_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tantangan_id` (`tantangan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aksi`
--
ALTER TABLE `aksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `aksi_user`
--
ALTER TABLE `aksi_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cerita`
--
ALTER TABLE `cerita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tantangan`
--
ALTER TABLE `tantangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tantangan_user`
--
ALTER TABLE `tantangan_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aksi`
--
ALTER TABLE `aksi`
  ADD CONSTRAINT `aksi_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `aksi_user`
--
ALTER TABLE `aksi_user`
  ADD CONSTRAINT `aksi_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aksi_user_ibfk_2` FOREIGN KEY (`aksi_id`) REFERENCES `aksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cerita`
--
ALTER TABLE `cerita`
  ADD CONSTRAINT `cerita_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tantangan_user`
--
ALTER TABLE `tantangan_user`
  ADD CONSTRAINT `tantangan_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tantangan_user_ibfk_2` FOREIGN KEY (`tantangan_id`) REFERENCES `tantangan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
