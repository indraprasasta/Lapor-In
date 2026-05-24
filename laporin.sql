-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 03:20 PM
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
-- Database: `laporin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`) VALUES
(1, 'admin', 'admin', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `foto`, `kategori`, `tanggal`) VALUES
(3, 'Jalan baru dua arah di KOTA MATARM ', 'Pemerintah Kota Mataram fokus meningkatkan infrastruktur dengan alokasi APBD di atas 40%, menekankan perbaikan jalan, jembatan, dan fasilitas publik. Pembangunan menargetkan status kota maju dengan mempercantik ikon kota dan pariwisata, meskipun menghadapi tantangan DAK fisik yang terbatas dan kebutuhan aksesibilitas disabilitas.', '1778028019_infrastruktur.jpg', 'Infrastruktur', '2026-05-06 00:40:19'),
(4, 'Empat Pohon di Mataram Kembali Tumbang, Tak Ada Korban dan Kerugian Warga', 'Hujan lebat disertai angin kencang terjadi di wilayah Mataram, Minggu (8/12). Peristiwa itu mengakibatkan pohon tumbang di beberapa tempat.\r\n”Total ada empat pohon yang tumbang,” kata Kepala Dinas Lingkungan Hidup  (DLH) Kota Mataram H Nizar Denny Cahyadi.\r\nPohon tumbang terjadi di Jalan Udayana, di depan rumah dinas Kajati NTB, dan di depan SDN 2 Karang Jangkong. yang mengakibatkan kerusakan\r\n”Kalau yang di Jalan Udayana ada dua pohon yang tumbang,” terangnya.\r\nUntuk memaksimalkan pembersihan puing-puing, semua jalan harus ditutup sementara. Selain itu, juga menurunkan puluhan armada.\r\n“Kita berhasil membersihkan dan normal dilewati kendaraan hanya sekitar satu jam,” kata dia.\r\nPada peristiwa tersebut tidak ada korban jiwa. Tidak ada kerugian juga yang dialami warga. ”Tidak ada kendaraan yang sedang parkir tertimpa,” bebernya.\r\nSaat ini, masyarakat harus lebih waspada. Belum seluruhnya angin barat itu memasuki wilayah Mataram.\r\n”Angin barat belum sepenuhnya menjangkau Mataram,” terangnya.\r\nJika masyarakat menemukan adanya hujan yang disertai angin kencang diminta untuk tidak melanjutkan perjalanan. Mengantisipasi hal-hal yang tidak diinginkan.\r\n”Lebih baik cari yang aman dulu,” kata dia.', '1778483387_pohontumbang1.jpg', 'Lingkungan', '2026-05-11 07:09:47'),
(8, 'Banjir bandang yang menerjang sejumlah kendaraan ', 'Pada hari Minggu,06 Juli 2025 mulai pukul 14.00 WITA, wilayah Kota Mataram dan sekitarnya dilanda hujan deras mengguyur Kota Mataram dan sekitarnya selama beberapa jam. Akibat curah hujan yang tinggi, debit air Sungai meningkat hingga meluap ke permukiman warga di Kecamatan Sandubaya, Selaparang, Mataram dan Sekarbela. Kejadian tersebut mengakibatkan beberapa rumah warga terendam, akses jalan terputus akibat terendam air di beberapa titik, Pohon tumbang, dan tembok TPST Sandubaya roboh. Kondisi saat ini hujan sudah reda dan air masih merendam permukiman warga.\r\n\r\n\r\n\r\nSaat ini Tim Reaksi Cepat BPBD Provinsi NTB, BPBD Kota Mataram, Basarnas, TNI/Polri sedang melakukan evakuasi terhadap warga yang terjebak banjir.\r\n\r\n\r\n\r\nKami Menghimbau kepada Masyarakat terdampak bencana untuk sementara sembari menunggu bantuan datang, silahkan lakukan evakuasi mandiri, hindari daerah dengan arus air tinggi dan mencari tempat yg lebih tinggi dari permukaan air, matikan listrik dan jangan lupa membawa barang-barang berharga dan surat penting, senter, persediaan makan minum dan obat2an dalam tas siaga bencana.\r\n\r\n', '1779254672_banjir.jpg', 'Lingkungan', '2026-05-20 05:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `dinas`
--

CREATE TABLE `dinas` (
  `id` int(11) NOT NULL,
  `nama_dinas` varchar(100) NOT NULL,
  `kode_dinas` varchar(20) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dinas`
--

INSERT INTO `dinas` (`id`, `nama_dinas`, `kode_dinas`, `deskripsi`) VALUES
(1, 'Dinas Pekerjaan Umum', 'PU', NULL),
(2, 'Dinas Pertamanan', 'PERTAMANAN', NULL),
(3, 'Dinas Kebersihan', 'KEBERSIHAN', NULL),
(4, 'Dinas Penerangan Jalan', 'PENERANGAN', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dinas_kategori`
--

CREATE TABLE `dinas_kategori` (
  `id` int(11) NOT NULL,
  `dinas_id` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dinas_kategori`
--

INSERT INTO `dinas_kategori` (`id`, `dinas_id`, `kategori`) VALUES
(1, 1, 'Jalan Rusak'),
(2, 1, 'Jembatan'),
(3, 1, 'Trotoar'),
(4, 2, 'Pohon Tumbang'),
(5, 2, 'Fasilitas Umum'),
(6, 3, 'Saluran Air'),
(7, 3, 'Sampah Menumpuk'),
(8, 4, 'Lampu Jalan Mati');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_laporan`
--

CREATE TABLE `kategori_laporan` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'tag',
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_laporan`
--

INSERT INTO `kategori_laporan` (`id`, `nama_kategori`, `icon`, `deskripsi`, `aktif`, `tanggal_dibuat`) VALUES
(10, 'Jalan rusak', 'road', 'Laporkan jalan berlubang, aspal terkelupas, atau trotoar yang membahayakan pengguna jalan dan pejalan kaki.', 1, '2026-05-20 02:38:44'),
(12, 'Pohon Tumbang', 'tree-pine', 'Bantu cegah kecelakaan dengan melaporkan pohon rawan tumbang, dahan patah, atau pohon tumbang yang menutupi jalan.', 1, '2026-05-20 02:42:08'),
(13, 'Lampu Jalan Mati', 'lightbulb', 'Beri tahu kami jika ada Penerangan Jalan Umum (PJU) yang padam untuk mengembalikan rasa aman di malam hari.', 1, '2026-05-20 02:49:00'),
(14, 'Fasilitas Umun', 'building', 'Laporkan kerusakan pada fasilitas publik seperti taman kota, halte bus, atau sarana umum lainnya agar segera diperbaiki.', 1, '2026-05-20 02:52:51'),
(18, 'Jembatan', 'route', 'Informasikan jika ada kerusakan struktur jembatan, aspal berlubang di jembatan, atau pembatas yang membahayakan.', 1, '2026-05-20 03:45:18'),
(22, 'Sampah Menumpuk', 'trash-2', 'Laporkan jika ada penumpukan sampah yang mengakibatkan masalah.', 1, '2026-05-21 17:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `ulasan` text DEFAULT NULL,
  `alamat` text NOT NULL,
  `kecamatan` varchar(50) NOT NULL,
  `kelurahan` varchar(50) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Ditolak') DEFAULT 'Menunggu',
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `user_id`, `judul`, `kategori`, `deskripsi`, `foto`, `rating`, `ulasan`, `alamat`, `kecamatan`, `kelurahan`, `latitude`, `longitude`, `status`, `tanggal`) VALUES
(3, 7, 'Lampu di arena buah mati', 'Fasilitas Umum', 'lampu di arena buah mati menyebabkan kesulitan mencari buah yang sodop ', '1778057187_foto kopi.jpg', NULL, NULL, 'JL cendrawasih no 11 cakra barat', 'Cakranegara', 'Cakranegara Barat', 0.00000000, 0.00000000, 'Selesai', '2026-05-06 08:46:27'),
(4, 5, 'Empat Pohon di Mataram Kembali Tumbang, Tak Ada Korban dan Kerugian Warga', 'Pohon Tumbang', 'Hujan lebat disertai angin kencang terjadi di wilayah Mataram, Minggu. Peristiwa itu mengakibatkan pohon tumbang di beberapa tempat.\r\n\r\n\r\n”Total ada empat pohon yang tumbang,” kata Kepala Dinas Lingkungan Hidup  (DLH) Kota Mataram H Nizar Denny Cahyadi.\r\nPohon tumbang terjadi di Jalan Udayana, di depan rumah dinas Kajati NTB, dan di depan SDN 2 Karang Jangkong.\r\n”Kalau yang di Jalan Udayana ada dua pohon yang tumbang,” terangnya.\r\nSaat ini, masyarakat harus lebih waspada. Belum seluruhnya angin barat itu memasuki wilayah Mataram.\r\n”Angin barat belum sepenuhnya menjangkau Mataram,” terangnya.\r\nJika masyarakat menemukan adanya hujan yang disertai angin kencang diminta untuk tidak melanjutkan perjalanan. Mengantisipasi hal-hal yang tidak diinginkan.\r\n”Lebih baik cari yang aman dulu,” kata dia.', '1778482891_pohontumbang1.jpg', NULL, NULL, 'Jalan Udayana, di depan rumah dinas Kajati NTB, dan di depan SDN 2 Karang Jangkong.', 'Mataram', 'Mataram Barat', 0.00000000, 0.00000000, 'Selesai', '2026-05-11 07:01:31'),
(5, 6, 'Lampu merah di perempatan rembiga rusak', 'Lampu Jalan Mati', 'lampu merah di simpang 4 rembiga mati sehingga menimbulkan kemacetan yang dahsyat', '1779027166_lampu rusak.jpg', NULL, NULL, 'JL. rembiga sukarno hatta', 'Selaparang', 'Rembiga', 0.00000000, 0.00000000, 'Selesai', '2026-05-17 14:12:46'),
(7, 5, 'jalanan rusak di daerah pagesangan', 'Jalan Rusak', 'jalanan rusak yang menyebabkan kecelakaan yang serius di jalan pagesangan', '1779191577_foto jalan berlubang.jpg', NULL, NULL, 'jl pagesngan timur gajah mada', 'Mataram', 'Pagesangan', 0.00000000, 0.00000000, 'Selesai', '2026-05-19 11:52:57'),
(17, 5, 'Sampah menumpuk', 'Sampah Menumpuk', 'sampah menumpuk sehingga mengakibatkan kemacetan karena sampah hingga masuk ke area jalan', '1779383415_sampah.jpg', NULL, NULL, 'Jl karang jasi cakra', 'Cakranegara', 'Cilinaya', 0.00000000, 0.00000000, 'Diproses', '2026-05-21 17:10:15'),
(18, 6, 'Jalan hancur ', 'Jalan rusak', 'jalan rusak di sekitar gomong yang menyebabkan kemacetan dikarenakan tidak ada jalan lain', '1779412839_rusakni.jpg', NULL, NULL, 'JL pendidikan raya no 234 gomong', 'Selaparang', 'Gomong', 0.00000000, 0.00000000, 'Menunggu', '2026-05-22 01:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT 'Petugas Lapangan',
  `dinas_id` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id`, `nama`, `nip`, `username`, `password`, `jabatan`, `dinas_id`, `foto`, `tanggal_dibuat`) VALUES
(1, 'gazagaza', '1029384756242', 'gazasiap', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', '', 3, NULL, '2026-05-16 04:16:49'),
(2, 'indra', '1234567890', 'indrokasino', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', '', 2, NULL, '2026-05-16 09:36:28'),
(3, 'ajria danuarta', '0982947295249', 'ajriaped', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', '', 4, NULL, '2026-05-17 13:27:59'),
(4, 'habib azahri', '2847294249249', 'habibdud', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', '', 1, NULL, '2026-05-17 13:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nik` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `nik`, `username`, `password`, `gender`, `alamat`, `foto`) VALUES
(5, 'i wayan girindra', '1234567890123456', 'prasasta', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', 'Laki-laki', 'JL beaq ganggas no 46', '1777726261_foto indra.jpeg'),
(6, 'gazamuhammad', '1029384785763943', 'gazambrl', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', 'Laki-laki', 'pagutan', ''),
(7, 'ni nengah ayu mirah cupes', '0987654321123456', 'cemplon', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', 'Perempuan', 'kr sampalan', ''),
(9, 'habib', '1928192918122222', 'duda', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', 'Laki-laki', 'lotim anjay', ''),
(10, 'ajria', '1010192929383832', 'ped', '$2y$10$yvJblwiNhbybc0ZEmVmaZefpWS69Ok4Rp0L7KwEIe3C9AP2nXWGUW', 'Perempuan', 'kekalik jaya superr', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dinas`
--
ALTER TABLE `dinas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_dinas` (`kode_dinas`);

--
-- Indexes for table `dinas_kategori`
--
ALTER TABLE `dinas_kategori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dinas_id` (`dinas_id`);

--
-- Indexes for table `kategori_laporan`
--
ALTER TABLE `kategori_laporan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `dinas_id` (`dinas_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dinas`
--
ALTER TABLE `dinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dinas_kategori`
--
ALTER TABLE `dinas_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kategori_laporan`
--
ALTER TABLE `kategori_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dinas_kategori`
--
ALTER TABLE `dinas_kategori`
  ADD CONSTRAINT `dinas_kategori_ibfk_1` FOREIGN KEY (`dinas_id`) REFERENCES `dinas` (`id`);

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `petugas`
--
ALTER TABLE `petugas`
  ADD CONSTRAINT `petugas_ibfk_1` FOREIGN KEY (`dinas_id`) REFERENCES `dinas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
