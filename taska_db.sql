-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 08:30 AM
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
-- Database: `taska_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ibubapa`
--

CREATE TABLE `ibubapa` (
  `id_ibubapa` int(11) NOT NULL,
  `ic_bapa` varchar(20) NOT NULL,
  `nama_bapa` varchar(100) NOT NULL,
  `pekerjaan_bapa` varchar(100) DEFAULT NULL,
  `pendapatan_bapa` float DEFAULT NULL,
  `email_bapa` varchar(100) DEFAULT NULL,
  `no_bapa` varchar(15) DEFAULT NULL,
  `ic_ibu` varchar(20) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `pekerjaan_ibu` varchar(100) DEFAULT NULL,
  `pendapatan_ibu` float DEFAULT NULL,
  `no_ibu` varchar(15) DEFAULT NULL,
  `EmailIbu` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(20) NOT NULL,
  `peranan_id` int(11) NOT NULL DEFAULT 2,
  `pengesahan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ibubapa`
--

INSERT INTO `ibubapa` (`id_ibubapa`, `ic_bapa`, `nama_bapa`, `pekerjaan_bapa`, `pendapatan_bapa`, `email_bapa`, `no_bapa`, `ic_ibu`, `nama_ibu`, `pekerjaan_ibu`, `pendapatan_ibu`, `no_ibu`, `EmailIbu`, `username`, `password`, `peranan_id`, `pengesahan`) VALUES
(10, '710116015070', 'azman bin abdollah', 'guru', 1100, 'azman@gmail.com', '01127285413', '730120015060', 'sarinah binti sulor', 'guru', 900, '0197302822', 'sarinah@gmail.com', 'azman@gmail.com', '710116015070', 2, 1),
(11, '020514017089', 'hamid bin aman', 'engineer', 122, 'hamid@gmail.com', '0137111175', '710525012045', 'siti binti ainul', 'guru', 122, '0193942216', 'siti@gmail.com', 'hamid@gmail.com', '020514017089', 2, 1),
(12, '780615014455', 'faris bin hafizuddin', 'guru', 1100, 'kiah.dino@gmail.com', '0193942216', '780514016677', 'rosshaleza binti sharep', 'suri rumah', 0, '0137111176', 'ross@gmail.com', 'kiah.dino@gmail.com', '780615014455', 2, 1),
(13, '730717013344', 'khairuddin bin khairul', 'guru', 1100, 'khairuddin@gmail.com', '0137111175', '730817010167', 'hanis bin abdollah', 'guru', 1111, '0193942216', 'hanis@gmail.com', 'khairuddin@gmail.com', '730717013344', 2, 1),
(14, '990516016677', 'zudin bin halim', 'guru', 1111, 'kiahdino@gmail.com', '0137111175', '990816016070', 'caca binti khai', 'guru', 1111, '0197302822', 'caca@gmail.com', 'kiahdino@gmail.com', '990516016677', 2, 0),
(15, '020718014545', 'yasin bin mohd', 'guru', 1100, 'iffahsyamilahradzi60@gmail.com', '0142972925', '010916019988', 'iffah binti radzi', 'guru', 1000, '0142964534', 'iffah@gmail.com', 'iffahsyamilahradzi60@gmail.com', '020718014545', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jadual`
--

CREATE TABLE `jadual` (
  `IdJadual` int(11) NOT NULL,
  `Masa` time NOT NULL,
  `Isnin` text DEFAULT NULL,
  `Selasa` text DEFAULT NULL,
  `Rabu` text DEFAULT NULL,
  `Khamis` text DEFAULT NULL,
  `Jumaat` text DEFAULT NULL,
  `id_pentadbir` int(11) NOT NULL,
  `minggu` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadual`
--

INSERT INTO `jadual` (`IdJadual`, `Masa`, `Isnin`, `Selasa`, `Rabu`, `Khamis`, `Jumaat`, `id_pentadbir`, `minggu`) VALUES
(1, '07:30:00', 'Menyambut ketibaan kanak-kanak', 'Menyambut ketibaan kanak-kanak', 'Menyambut ketibaan kanak-kanak', 'Menyambut ketibaan kanak-kanak', 'Menyambut ketibaan kanak-kanak', 1, 1),
(3, '08:00:00', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 1, 1),
(4, '08:30:00', 'Sarapan Pagi', 'Sarapan Pagi', 'Sarapan Pagi', 'Sarapan Pagi', 'Sarapan Pagi', 1, 1),
(5, '09:00:00', 'Perhimpunan pagi/circle time/senaman pagi /ikrar ', 'Perhimpunan pagi/circle time/senaman pagi /ikrar ', 'Perhimpunan pagi/circle time/senaman pagi /ikrar ', 'Perhimpunan pagi/circle time/senaman pagi /ikrar ', 'Perhimpunan pagi/circle time/senaman pagi /ikrar ', 1, 1),
(6, '09:30:00', 'Membuat istana pasir', 'Permainan bebas di Playground', 'Adab berkongsi mainan', 'Main Air (Menyiram Pokok)', 'Mengutip daun kering', 1, 1),
(7, '10:15:00', 'Makan buah/Snek', 'Makan buah/Snek', 'Makan buah/Snek', 'Makan buah/Snek', 'Makan buah/Snek', 1, 1),
(8, '10:30:00', 'Perkembangan Bahasa Komunikasi & Literasi Awal (Cita-cita saya) ', 'Perkembangan Kreativiti & Estetika (Kolaj Pokok)', 'Perkembangan Deria & Pemahaman Dunia Sekitar (Mengenal bunyi haiwan)', 'Perkembangan Awal Matematik & Pemikiran Logik (Memadankan bentuk', 'Perkembangan Fizikal (Menguntai Manik)', 1, 1),
(9, '11:20:00', 'Bercerita', 'Kisah Tauladan', 'Benda Maujud', 'Bercerita', 'Bercerita', 1, 1),
(10, '11:45:00', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 1, 1),
(11, '12:00:00', 'Makan tengahari', 'Makan tengahari', 'Makan tengahari', 'Makan tengahari', 'Makan tengahari', 1, 1),
(15, '13:00:00', 'Pengurusan Diri (Mandi / Gosok gigi)', 'Pengurusan Diri (Mandi / Gosok gigi)', 'Pengurusan Diri (Mandi / Gosok gigi)', 'Pengurusan Diri (Mandi / Gosok gigi)', 'Pengurusan Diri (Mandi / Gosok gigi)', 1, 1),
(16, '12:30:00', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 'Permainan bebas', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `fizikal` int(11) NOT NULL,
  `deria_persekitaran` int(11) NOT NULL,
  `sahsiah` int(11) NOT NULL,
  `kreativiti` int(11) NOT NULL,
  `komunikasi` int(11) NOT NULL,
  `matematik_logik` int(11) NOT NULL,
  `ulasan` text NOT NULL,
  `tarikh_laporan` date DEFAULT NULL,
  `bulan` varchar(20) NOT NULL DEFAULT '',
  `id_pendidik` int(11) NOT NULL,
  `ic_pelajar` varchar(20) NOT NULL,
  `fizikal_ulasan` text DEFAULT NULL,
  `deria_persekitaran_ulasan` text DEFAULT NULL,
  `sahsiah_ulasan` text DEFAULT NULL,
  `kreativiti_ulasan` text DEFAULT NULL,
  `komunikasi_ulasan` text DEFAULT NULL,
  `matematik_logik_ulasan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `fizikal`, `deria_persekitaran`, `sahsiah`, `kreativiti`, `komunikasi`, `matematik_logik`, `ulasan`, `tarikh_laporan`, `bulan`, `id_pendidik`, `ic_pelajar`, `fizikal_ulasan`, `deria_persekitaran_ulasan`, `sahsiah_ulasan`, `kreativiti_ulasan`, `komunikasi_ulasan`, `matematik_logik_ulasan`) VALUES
(2, 3, 2, 1, 3, 3, 3, 'hahaha', '2025-06-01', '2025-06', 7, '020616010150', 'baik', 'bagus', 'qqq', 'qqq', 'qq', 'test'),
(3, 3, 2, 3, 1, 2, 2, 'bagus', '2025-06-03', '2025-12', 7, '020616010150', 'bagus', 'bagus', 'bagus', 'bagus', 'bagus', 'bagus');

-- --------------------------------------------------------

--
-- Table structure for table `pelajar`
--

CREATE TABLE `pelajar` (
  `ic_pelajar` varchar(20) NOT NULL,
  `nama_pelajar` varchar(100) NOT NULL,
  `jantina` varchar(10) NOT NULL,
  `alamat_semasa` varchar(255) DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `Alahan` varchar(100) DEFAULT NULL,
  `gambar_pelajar` varchar(255) DEFAULT NULL,
  `ibubapa_id` int(11) NOT NULL,
  `tahun_pengajian` int(4) DEFAULT NULL,
  `id_pentadbir` int(11) DEFAULT NULL,
  `id_pendidik` int(11) DEFAULT NULL,
  `sijilLahir_pelajar` varchar(255) DEFAULT NULL,
  `pengesahan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelajar`
--

INSERT INTO `pelajar` (`ic_pelajar`, `nama_pelajar`, `jantina`, `alamat_semasa`, `umur`, `Alahan`, `gambar_pelajar`, `ibubapa_id`, `tahun_pengajian`, `id_pentadbir`, `id_pendidik`, `sijilLahir_pelajar`, `pengesahan`) VALUES
('020615017089', 'syafiqah binti khairuddin', 'Perempuan', '19, jalan nilam kota tinggi', 2, 'tiada', '683be0f18cc4c_ic.jpg', 13, 2026, NULL, NULL, '683be0f18d20a_sijilkelahiran.pdf', 1),
('020616010150', 'sakinah balqis binti nor azman', 'Perempuan', 'NO 10, LORONG GELIGA 1, BANDAR MAS 81900 KOTA TINGGI JOHOR', 2, 'tiada', '682a43af7aa16_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 10, 2026, NULL, 7, '682a43af7afea_sijilkelahiran.pdf', 1),
('020628141518', 'caca binti hamid', 'Perempuan', '10 jalan maju', 2, 'tiada', '682a4c740cf1c_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 11, 2026, NULL, 7, '682a4c740d4fc_sijilkelahiran.pdf', 1),
('030819015060', 'hariz bin nor azman', 'Lelaki', 'NO 10, LORONG GELIGA 1, BANDAR MAS 81900 KOTA TINGGI JOHOR', 3, 'tiada', '682a43af7bb37_IMG_7113.jpg', 10, 2026, NULL, NULL, '682a43af7bff1_sijilkelahiran.pdf', 1),
('040512014456', 'aisyah binti hamid', 'Perempuan', 'NO 10, LORONG GELIGA 1, BANDAR MAS 81900 KOTA TINGGI JOHOR', 2, 'tiada', '682a4b786f4f2_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 11, 2026, 1, 8, '682a4b786fd9f_sijilkelahiran.pdf', 1),
('050117014455', 'hajar binti zudin', 'Perempuan', '10 jalan murni', 2, 'tiada', '683be8f998a2f_ic.jpg', 14, 2026, NULL, NULL, '683be8f99bbb5_sijilkelahiran.pdf', 1),
('070819013456', 'fatihah binti faris', 'Perempuan', '10, jalan durian', 2, 'tiada', '6835363972bca_ic.jpg', 12, 2026, NULL, NULL, '6835363973184_sijilkelahiran.pdf', 1),
('2509160190', 'melur binti yasin', 'Perempuan', '5, JALAN FIRMA 3/1, KAWASAN PERINDUSTRIAN TEBRAU 4, 81100 JOHOR BAHRU', 2, 'tiada', '68468137dc6c5_ic.jpg', 15, 2026, NULL, NULL, '68468137dd072_Sakinah Balqis Resume.pdf', 1),
('980315017080', 'hafiz bin nor azman', 'Lelaki', '10 taman maju', 4, 'tiada', '682a474a2bdf5_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 10, 2026, NULL, NULL, '682a474a2c3a0_sijilkelahiran.pdf', 0),
('980512014455', 'hakim bin nor azman', 'Lelaki', '10 taman maju', 2, 'tiada', '682a474a2d05a_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 10, 2026, NULL, NULL, '682a474a2d53c_sijilkelahiran.pdf', 0),
('999816012030', 'juju bin hamid', 'Lelaki', '10 taman maju', 2, 'tiada', '682a4c740ecd6_WhatsApp Image 2025-01-15 at 10.35.30 PM.jpeg', 11, 2026, NULL, NULL, '682a4c740f12e_sijilkelahiran.pdf', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pendidik`
--

CREATE TABLE `pendidik` (
  `id_pendidik` int(11) NOT NULL,
  `ic_pendidik` varchar(20) NOT NULL,
  `nama_pendidik` varchar(100) NOT NULL,
  `email_pendidik` varchar(100) NOT NULL,
  `no_pendidik` varchar(15) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(20) NOT NULL,
  `peranan_id` int(11) NOT NULL DEFAULT 3,
  `umur` int(11) NOT NULL,
  `alamat_pendidik` varchar(255) NOT NULL,
  `sijil_pengajian` varchar(255) NOT NULL,
  `kursus_kanak_kanak` varchar(255) NOT NULL,
  `pengesahan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendidik`
--

INSERT INTO `pendidik` (`id_pendidik`, `ic_pendidik`, `nama_pendidik`, `email_pendidik`, `no_pendidik`, `username`, `password`, `peranan_id`, `umur`, `alamat_pendidik`, `sijil_pengajian`, `kursus_kanak_kanak`, `pengesahan`) VALUES
(7, '020716010155', 'hafizah binti hafiz', 'hafizah@gmail.com', '0137802623', 'hafizah@gmail.com', '020716010155', 3, 26, '10 Jalan Durian Batu Pahat', '', '', 1),
(8, '730115023450', 'nana bin azman', 'nana@gmail.com', '0178237676', 'nana@gmail.com', '730115023450', 3, 0, '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pentadbir`
--

CREATE TABLE `pentadbir` (
  `id_pentadbir` int(11) NOT NULL,
  `ic_pentadbir` varchar(20) NOT NULL,
  `nama_pentadbir` varchar(100) NOT NULL,
  `email_pentadbir` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(20) NOT NULL,
  `peranan_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pentadbir`
--

INSERT INTO `pentadbir` (`id_pentadbir`, `ic_pentadbir`, `nama_pentadbir`, `email_pentadbir`, `username`, `password`, `peranan_id`) VALUES
(1, '990615014033', 'hamidah binti hamid', 'sakinahbalqis16@gmail.com', 'sakinahbalqis16@gmail.com', '990615014033', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rpa`
--

CREATE TABLE `rpa` (
  `id_RPA` int(10) NOT NULL,
  `minggu` varchar(10) NOT NULL,
  `tarikh` date NOT NULL,
  `masa` varchar(10) NOT NULL,
  `hari` varchar(10) NOT NULL,
  `tajuk` varchar(255) NOT NULL,
  `pengetahuan` text NOT NULL,
  `objektif` text NOT NULL,
  `bidang` varchar(255) NOT NULL,
  `bahan` text NOT NULL,
  `tempat` varchar(255) NOT NULL,
  `rancangan` text NOT NULL,
  `hasil` text NOT NULL,
  `id_pendidik` int(11) NOT NULL,
  `id_pentadbir` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rpa`
--

INSERT INTO `rpa` (`id_RPA`, `minggu`, `tarikh`, `masa`, `hari`, `tajuk`, `pengetahuan`, `objektif`, `bidang`, `bahan`, `tempat`, `rancangan`, `hasil`, `id_pendidik`, `id_pentadbir`) VALUES
(1, '1', '2025-03-26', '10:30 AM', 'Selasa', 'Badan Saya - Menyebut nama anggota badan', 'Kanak-kanak boleh menyebut perkataan yang didengar\r\n', '1) Kanak-kanak dapat mengenali anggota badan \r\n2) Kanak-kanak boleh mengira bilangan anggota badan\r\n\r\n', 'Perkembangan Bahasa, Komunikasi & Literasi Awal', '1) Carta tubuh badan\r\n2) Flash card', 'Ruang tamu', '1) Pendidik membawa kanak-kanak ke ruang aktiviti\r\n2) Pendidik menjelaskan kepada kanak-kanak tentang tubuh badan saya yang akan dilakukan.\r\n3) Pendidik memperkenalkan bahan-bahan yang akan digunakan untuk aktiviti tubuh badan saya', '1) Pendidik memuji kanak-kanak yang berjaya melakukan aktiviti\r\n2) Pendidik memberikan galakan, membantu dan menunjukkan semula aktiviti sehingga kanak-kanak berjaya melakukan aktiviti tersebut', 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `yuran`
--

CREATE TABLE `yuran` (
  `id_yuran` int(11) NOT NULL,
  `id_ibubapa` int(11) NOT NULL,
  `ic_pelajar` varchar(20) NOT NULL,
  `tarikh` date NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `kaedah` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `jenis_yuran` varchar(100) DEFAULT NULL,
  `bulan` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yuran`
--

INSERT INTO `yuran` (`id_yuran`, `id_ibubapa`, `ic_pelajar`, `tarikh`, `jumlah`, `kaedah`, `status`, `jenis_yuran`, `bulan`) VALUES
(18, 10, '030819015060', '2025-05-26', 10.00, 'card', 'success', 'Pendaftaran', 3),
(19, 10, '030819015060', '2025-05-27', 10.00, 'card', 'success', 'Bulanan', 5),
(20, 10, '030819015060', '2025-06-01', 10.00, 'card', 'success', 'Bulanan', 3),
(21, 10, '020616010150', '2025-06-01', 10.00, 'card', 'success', 'Bulanan', 5),
(24, 10, '020616010150', '2025-06-03', 10.00, 'card', 'success', 'Pendaftaran', 3),
(25, 10, '020616010150', '2025-06-04', 10.00, 'card', 'success', 'Bulanan', 6),
(26, 11, '020628141518', '2025-06-04', 10.00, 'card', 'success', 'Pendaftaran', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ibubapa`
--
ALTER TABLE `ibubapa`
  ADD PRIMARY KEY (`id_ibubapa`),
  ADD UNIQUE KEY `ic_bapa` (`ic_bapa`),
  ADD UNIQUE KEY `email_bapa` (`email_bapa`);

--
-- Indexes for table `jadual`
--
ALTER TABLE `jadual`
  ADD PRIMARY KEY (`IdJadual`),
  ADD KEY `id_pentadbir` (`id_pentadbir`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_pendidik` (`id_pendidik`),
  ADD KEY `ic_pelajar` (`ic_pelajar`);

--
-- Indexes for table `pelajar`
--
ALTER TABLE `pelajar`
  ADD PRIMARY KEY (`ic_pelajar`),
  ADD KEY `ibubapa_id` (`ibubapa_id`),
  ADD KEY `fk_id_pentadbir` (`id_pentadbir`),
  ADD KEY `fk_id_pendidik` (`id_pendidik`);

--
-- Indexes for table `pendidik`
--
ALTER TABLE `pendidik`
  ADD PRIMARY KEY (`id_pendidik`),
  ADD UNIQUE KEY `ic_pendidik` (`ic_pendidik`),
  ADD UNIQUE KEY `email_pendidik` (`email_pendidik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pentadbir`
--
ALTER TABLE `pentadbir`
  ADD PRIMARY KEY (`id_pentadbir`),
  ADD UNIQUE KEY `ic_pentadbir` (`ic_pentadbir`),
  ADD UNIQUE KEY `email_pentadbir` (`email_pentadbir`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `rpa`
--
ALTER TABLE `rpa`
  ADD PRIMARY KEY (`id_RPA`),
  ADD KEY `id_pendidik` (`id_pendidik`),
  ADD KEY `id_pentadbir` (`id_pentadbir`);

--
-- Indexes for table `yuran`
--
ALTER TABLE `yuran`
  ADD PRIMARY KEY (`id_yuran`),
  ADD KEY `fk_ibubapa` (`id_ibubapa`),
  ADD KEY `fk_pelajar` (`ic_pelajar`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ibubapa`
--
ALTER TABLE `ibubapa`
  MODIFY `id_ibubapa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `jadual`
--
ALTER TABLE `jadual`
  MODIFY `IdJadual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pendidik`
--
ALTER TABLE `pendidik`
  MODIFY `id_pendidik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pentadbir`
--
ALTER TABLE `pentadbir`
  MODIFY `id_pentadbir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpa`
--
ALTER TABLE `rpa`
  MODIFY `id_RPA` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `yuran`
--
ALTER TABLE `yuran`
  MODIFY `id_yuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadual`
--
ALTER TABLE `jadual`
  ADD CONSTRAINT `jadual_ibfk_1` FOREIGN KEY (`id_pentadbir`) REFERENCES `pentadbir` (`id_pentadbir`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_pendidik`) REFERENCES `pendidik` (`id_pendidik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`ic_pelajar`) REFERENCES `pelajar` (`ic_pelajar`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pelajar`
--
ALTER TABLE `pelajar`
  ADD CONSTRAINT `fk_id_pendidik` FOREIGN KEY (`id_pendidik`) REFERENCES `pendidik` (`id_pendidik`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_id_pentadbir` FOREIGN KEY (`id_pentadbir`) REFERENCES `pentadbir` (`id_pentadbir`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pelajar_ibfk_1` FOREIGN KEY (`ibubapa_id`) REFERENCES `ibubapa` (`id_ibubapa`);

--
-- Constraints for table `rpa`
--
ALTER TABLE `rpa`
  ADD CONSTRAINT `rpa_ibfk_1` FOREIGN KEY (`id_pendidik`) REFERENCES `pendidik` (`id_pendidik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rpa_ibfk_2` FOREIGN KEY (`id_pentadbir`) REFERENCES `pentadbir` (`id_pentadbir`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `yuran`
--
ALTER TABLE `yuran`
  ADD CONSTRAINT `fk_ibubapa` FOREIGN KEY (`id_ibubapa`) REFERENCES `ibubapa` (`id_ibubapa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pelajar` FOREIGN KEY (`ic_pelajar`) REFERENCES `pelajar` (`ic_pelajar`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
