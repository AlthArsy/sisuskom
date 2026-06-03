-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 03 Jun 2026 pada 12.24
-- Versi server: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- Versi PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisuskom`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak02`
--

CREATE TABLE `detail_ak02` (
  `id_detail_ak02` int(11) NOT NULL,
  `id_ak02` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `obs_demonstrasi` varchar(200) DEFAULT NULL,
  `portofolio` varchar(200) DEFAULT NULL,
  `pyt_pihak_ketiga` varchar(200) DEFAULT NULL,
  `pyt_wawancara` varchar(200) DEFAULT NULL,
  `pyt_lisan` varchar(200) DEFAULT NULL,
  `pyt_pertulis` varchar(200) DEFAULT NULL,
  `proyek_kerja` varchar(200) DEFAULT NULL,
  `lainnya` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `detail_ak02`
--

INSERT INTO `detail_ak02` (`id_detail_ak02`, `id_ak02`, `id_skema`, `id_unit`, `obs_demonstrasi`, `portofolio`, `pyt_pihak_ketiga`, `pyt_wawancara`, `pyt_lisan`, `pyt_pertulis`, `proyek_kerja`, `lainnya`) VALUES
(25, 35, 21, 46, '0', '0', '0', '0', '0', '0', '0', NULL),
(26, 35, 21, 47, '0', '0', '0', '0', '0', '0', '0', NULL),
(27, 35, 21, 58, '0', '0', '0', '0', '0', '0', '0', NULL),
(34, 41, 21, 46, '1', '1', '1', '1', '1', '1', '1', '1'),
(35, 41, 21, 47, '1', '1', '1', '1', '1', '1', '1', '1'),
(36, 41, 21, 58, '1', '1', '1', '1', '1', '1', '1', '1'),
(37, 45, 21, 46, '1', '1', '1', '1', '1', '1', '1', '1'),
(38, 45, 21, 47, '1', '1', '1', '1', '1', '1', '1', '1'),
(39, 45, 21, 58, '1', '1', '1', '1', '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak1`
--

CREATE TABLE `detail_ak1` (
  `id_detail_ak1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `bukti` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `detail_ak1`
--

INSERT INTO `detail_ak1` (`id_detail_ak1`, `id_ak01`, `bukti`) VALUES
(7, 6, 'Hasil Verifikasi Portofolio, Hasil Observasi Langsung'),
(8, 7, 'Hasil Verifikasi Portofolio, Hasil Reviu Produk, Hasil Observasi Langsung, Hasil Kegiatan Terstruktur, Hasil Tanya Jawab, Hasil Pertanyaan Tulis, Hasil Pertanyaan Lisan, Hasil Pertanyaan Wawancara, Bukti Lainnya, oke'),
(10, 9, 'Hasil Observasi Langsung, Hasil Tanya Jawab'),
(11, 10, 'Bukti Lainnya, APAYA');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_apl2`
--

CREATE TABLE `detail_apl2` (
  `id_detail_apl2` int(11) NOT NULL,
  `id_apl2` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `id_elemen` int(11) NOT NULL,
  `id_kuk` int(11) NOT NULL,
  `nilai` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `detail_apl2`
--

INSERT INTO `detail_apl2` (`id_detail_apl2`, `id_apl2`, `id_skema`, `id_unit`, `id_elemen`, `id_kuk`, `nilai`) VALUES
(6, 2, 21, 46, 35, 41, 'K'),
(7, 2, 21, 46, 35, 42, 'K'),
(8, 2, 21, 46, 36, 43, 'K'),
(9, 2, 21, 46, 36, 44, 'K'),
(10, 2, 21, 47, 37, 45, 'K'),
(11, 2, 21, 47, 37, 46, 'K'),
(12, 2, 21, 47, 38, 47, 'K'),
(13, 2, 21, 47, 38, 48, 'K'),
(14, 2, 21, 47, 39, 49, 'BK'),
(15, 2, 21, 47, 39, 50, 'BK'),
(16, 3, 21, 46, 35, 41, 'K'),
(17, 3, 21, 46, 35, 42, 'K'),
(18, 3, 21, 46, 36, 43, 'K'),
(19, 3, 21, 46, 36, 44, 'K'),
(20, 3, 21, 47, 37, 45, 'K'),
(21, 3, 21, 47, 37, 46, 'K'),
(22, 3, 21, 47, 38, 47, 'K'),
(23, 3, 21, 47, 38, 48, 'K'),
(24, 3, 21, 47, 39, 49, 'K'),
(25, 3, 21, 47, 39, 50, 'K'),
(54, 7, 21, 46, 35, 41, 'K'),
(55, 7, 21, 46, 35, 42, 'K'),
(56, 7, 21, 46, 36, 43, 'K'),
(57, 7, 21, 46, 36, 44, 'K'),
(58, 7, 21, 47, 37, 45, 'K'),
(59, 7, 21, 47, 37, 46, 'K'),
(60, 7, 21, 47, 38, 47, 'K'),
(61, 7, 21, 47, 38, 48, 'K'),
(62, 7, 21, 47, 39, 49, 'K'),
(63, 7, 21, 47, 39, 50, 'K'),
(64, 7, 21, 58, 50, 60, 'K'),
(65, 7, 21, 58, 50, 61, 'K'),
(66, 7, 21, 58, 50, 62, 'K'),
(67, 7, 21, 58, 51, 63, 'K'),
(68, 7, 21, 58, 51, 64, 'K'),
(69, 7, 21, 58, 52, 65, 'K'),
(70, 7, 21, 58, 52, 66, 'K'),
(108, 2, 21, 58, 50, 60, 'BK'),
(109, 2, 21, 58, 51, 63, 'BK'),
(110, 2, 21, 58, 52, 65, 'BK'),
(111, 10, 21, 46, 35, 41, 'BK'),
(112, 10, 21, 46, 35, 42, 'BK'),
(113, 10, 21, 46, 36, 43, 'BK'),
(114, 10, 21, 46, 36, 44, 'BK'),
(115, 10, 21, 47, 37, 45, 'BK'),
(116, 10, 21, 47, 37, 46, 'BK'),
(117, 10, 21, 47, 38, 47, 'BK'),
(118, 10, 21, 47, 38, 48, 'BK'),
(119, 10, 21, 47, 39, 49, 'BK'),
(120, 10, 21, 47, 39, 50, 'BK'),
(121, 10, 21, 58, 50, 60, 'BK'),
(122, 10, 21, 58, 50, 61, 'BK'),
(123, 10, 21, 58, 50, 62, 'BK'),
(124, 10, 21, 58, 51, 63, 'BK'),
(125, 10, 21, 58, 51, 64, 'BK'),
(126, 10, 21, 58, 52, 65, 'BK'),
(127, 10, 21, 58, 52, 66, 'BK'),
(128, 12, 21, 46, 35, 41, ''),
(129, 12, 21, 46, 35, 42, ''),
(130, 12, 21, 46, 36, 43, ''),
(131, 12, 21, 46, 36, 44, ''),
(132, 12, 21, 47, 37, 45, ''),
(133, 12, 21, 47, 37, 46, ''),
(134, 12, 21, 47, 38, 47, ''),
(135, 12, 21, 47, 38, 48, ''),
(136, 12, 21, 47, 39, 49, ''),
(137, 12, 21, 47, 39, 50, ''),
(138, 12, 21, 58, 50, 60, ''),
(139, 12, 21, 58, 50, 61, ''),
(140, 12, 21, 58, 50, 62, ''),
(141, 12, 21, 58, 51, 63, ''),
(142, 12, 21, 58, 51, 64, ''),
(143, 12, 21, 58, 52, 65, ''),
(144, 12, 21, 58, 52, 66, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ia01`
--

CREATE TABLE `detail_ia01` (
  `id_detail_ia1` int(11) NOT NULL,
  `id_ia01` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `id_elemen` int(11) NOT NULL,
  `id_kuk` int(11) NOT NULL,
  `pencapaian` varchar(100) DEFAULT NULL,
  `Penilaian Lanjut` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `detail_ia01`
--

INSERT INTO `detail_ia01` (`id_detail_ia1`, `id_ia01`, `id_skema`, `id_unit`, `id_elemen`, `id_kuk`, `pencapaian`, `Penilaian Lanjut`) VALUES
(103, 72, 21, 46, 35, 41, 'Ya', 'BASUSS'),
(104, 72, 21, 46, 35, 42, 'Ya', 'BASUSS'),
(105, 72, 21, 46, 36, 43, 'Ya', 'BASUSS'),
(106, 72, 21, 46, 36, 44, 'Tidak', 'BASUSS'),
(107, 72, 21, 47, 37, 45, 'Ya', 'BASUSS'),
(108, 72, 21, 47, 37, 46, 'Ya', 'BASUSS'),
(109, 72, 21, 47, 38, 47, 'Ya', 'BASUSS'),
(110, 72, 21, 47, 38, 48, 'Tidak', 'BASUSS'),
(111, 72, 21, 47, 39, 49, 'Tidak', 'BASUSS'),
(112, 72, 21, 47, 39, 50, 'Ya', 'BASUSS'),
(113, 72, 21, 58, 50, 60, 'Ya', 'BASUSS'),
(114, 72, 21, 58, 50, 61, 'Tidak', 'BASUSS'),
(115, 72, 21, 58, 50, 62, 'Tidak', 'BASUSS'),
(116, 72, 21, 58, 51, 63, 'Tidak', 'BASUSS'),
(117, 72, 21, 58, 51, 64, 'Ya', 'BASUSS'),
(118, 72, 21, 58, 52, 65, 'Tidak', 'BASUSS'),
(119, 72, 21, 58, 52, 66, 'Tidak', 'OKEE'),
(239, 80, 21, 46, 35, 41, 'Ya', ''),
(240, 80, 21, 46, 35, 42, 'Ya', ''),
(241, 80, 21, 46, 36, 43, 'Ya', ''),
(242, 80, 21, 46, 36, 44, 'Ya', ''),
(243, 80, 21, 47, 37, 45, 'Ya', ''),
(244, 80, 21, 47, 37, 46, 'Ya', ''),
(245, 80, 21, 47, 38, 47, 'Ya', ''),
(246, 80, 21, 47, 38, 48, 'Ya', ''),
(247, 80, 21, 47, 39, 49, 'Ya', ''),
(248, 80, 21, 47, 39, 50, 'Ya', ''),
(249, 80, 21, 58, 50, 60, 'Ya', ''),
(250, 80, 21, 58, 50, 61, 'Ya', ''),
(251, 80, 21, 58, 50, 62, 'Ya', ''),
(252, 80, 21, 58, 51, 63, 'Tidak', ''),
(253, 80, 21, 58, 51, 64, 'Ya', ''),
(254, 80, 21, 58, 52, 65, 'Ya', ''),
(255, 80, 21, 58, 52, 66, 'Ya', ''),
(256, 81, 21, 46, 35, 41, '', ''),
(257, 81, 21, 46, 35, 42, '', ''),
(258, 81, 21, 46, 36, 43, '', ''),
(259, 81, 21, 46, 36, 44, '', ''),
(260, 81, 21, 47, 37, 45, '', ''),
(261, 81, 21, 47, 37, 46, '', ''),
(262, 81, 21, 47, 38, 47, '', ''),
(263, 81, 21, 47, 38, 48, '', ''),
(264, 81, 21, 47, 39, 49, '', ''),
(265, 81, 21, 47, 39, 50, '', ''),
(266, 81, 21, 58, 50, 60, '', ''),
(267, 81, 21, 58, 50, 61, '', ''),
(268, 81, 21, 58, 50, 62, '', ''),
(269, 81, 21, 58, 51, 63, '', ''),
(270, 81, 21, 58, 51, 64, '', ''),
(271, 81, 21, 58, 52, 65, '', ''),
(272, 81, 21, 58, 52, 66, '', ''),
(273, 82, 21, 46, 35, 41, 'Ya', ''),
(274, 82, 21, 46, 35, 42, 'Ya', ''),
(275, 82, 21, 46, 36, 43, 'Tidak', ''),
(276, 82, 21, 46, 36, 44, 'Ya', ''),
(277, 82, 21, 47, 37, 45, 'Ya', ''),
(278, 82, 21, 47, 37, 46, 'Ya', ''),
(279, 82, 21, 47, 38, 47, 'Ya', ''),
(280, 82, 21, 47, 38, 48, 'Ya', ''),
(281, 82, 21, 47, 39, 49, 'Ya', ''),
(282, 82, 21, 47, 39, 50, 'Tidak', ''),
(283, 82, 21, 58, 50, 60, 'Ya', ''),
(284, 82, 21, 58, 50, 61, 'Ya', ''),
(285, 82, 21, 58, 50, 62, 'Ya', ''),
(286, 82, 21, 58, 51, 63, 'Ya', ''),
(287, 82, 21, 58, 51, 64, 'Ya', ''),
(288, 82, 21, 58, 52, 65, 'Ya', ''),
(289, 82, 21, 58, 52, 66, 'Ya', ''),
(307, 84, 21, 46, 35, 41, 'Tidak', ''),
(308, 84, 21, 46, 35, 42, 'Tidak', ''),
(309, 84, 21, 46, 36, 43, 'Tidak', ''),
(310, 84, 21, 46, 36, 44, 'Tidak', ''),
(311, 84, 21, 47, 37, 45, 'Tidak', ''),
(312, 84, 21, 47, 37, 46, 'Tidak', ''),
(313, 84, 21, 47, 38, 47, 'Tidak', ''),
(314, 84, 21, 47, 38, 48, 'Tidak', ''),
(315, 84, 21, 47, 39, 49, 'Tidak', ''),
(316, 84, 21, 47, 39, 50, 'Tidak', ''),
(317, 84, 21, 58, 50, 60, 'Tidak', ''),
(318, 84, 21, 58, 50, 61, 'Tidak', ''),
(319, 84, 21, 58, 50, 62, 'Tidak', ''),
(320, 84, 21, 58, 51, 63, 'Tidak', ''),
(321, 84, 21, 58, 51, 64, 'Tidak', ''),
(322, 84, 21, 58, 52, 65, 'Tidak', ''),
(323, 84, 21, 58, 52, 66, 'Tidak', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_ak03`
--

CREATE TABLE `hasil_ak03` (
  `id_detail_ak03` int(11) NOT NULL,
  `id_ak03` int(11) NOT NULL,
  `hasil` varchar(100) NOT NULL,
  `komentar_asesi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hasil_ak03`
--

INSERT INTO `hasil_ak03` (`id_detail_ak03`, `id_ak03`, `hasil`, `komentar_asesi`) VALUES
(21, 13, 'Ya', ''),
(22, 13, 'Ya', ''),
(23, 13, 'Ya', ''),
(24, 13, 'Ya', ''),
(25, 13, 'Ya', ''),
(26, 13, 'Ya', ''),
(27, 13, 'Ya', ''),
(28, 13, 'Ya', ''),
(29, 13, 'Ya', ''),
(30, 13, 'Ya', ''),
(31, 14, 'Ya', ''),
(32, 14, 'Ya', ''),
(33, 14, 'Ya', ''),
(34, 14, 'Ya', ''),
(35, 14, 'Ya', ''),
(36, 14, 'Ya', ''),
(37, 14, 'Ya', ''),
(38, 14, 'Ya', ''),
(39, 14, 'Ya', ''),
(40, 14, 'Ya', ''),
(51, 16, 'Ya', 'WWWWWW'),
(52, 16, 'Ya', ''),
(53, 16, 'Ya', ''),
(54, 16, 'Ya', ''),
(55, 16, 'Ya', 'WWWWW'),
(56, 16, 'Ya', ''),
(57, 16, 'Ya', ''),
(58, 16, 'Ya', 'WWWWW'),
(59, 16, 'Ya', ''),
(60, 16, 'Ya', ''),
(71, 18, 'Ya', '1'),
(72, 18, 'Ya', '1'),
(73, 18, 'Ya', '1'),
(74, 18, 'Ya', '1'),
(75, 18, 'Ya', '1'),
(76, 18, 'Ya', '1'),
(77, 18, 'Ya', '1'),
(78, 18, 'Ya', '1'),
(79, 18, 'Ya', '1'),
(80, 18, 'Ya', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `nik`, `nama_admin`) VALUES
(1, '1234567812345678', 'Ryan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak01`
--

CREATE TABLE `tb_ak01` (
  `id_ak01` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `tuk_pelaksanaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.01 Persetujuan Asesmen dan Kerahasiaan';

--
-- Dumping data untuk tabel `tb_ak01`
--

INSERT INTO `tb_ak01` (`id_ak01`, `id_apl1`, `id_asesor`, `id_asesi`, `tuk`, `hari_tanggal`, `waktu`, `tuk_pelaksanaan`) VALUES
(6, 8, 1, 1, 'Sewaktu', '2026-05-18', '08.00-12.00', 'SMK'),
(7, 2, 1, 12, 'Mandiri', '2026-05-18', 'SITU SAMPAI SINI', 'SMK'),
(9, 3, 1, 17, 'Mandiri', '2026-05-22', '1221121221', 'SMK'),
(10, 9, 1, 19, 'Sewaktu', '2026-05-23', '00.00 SAMPAI JUMAT', 'STUDENT');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak02`
--

CREATE TABLE `tb_ak02` (
  `id_ak02` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `komentar_asesor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.02 Header Rekaman Asesmen Kompetensi';

--
-- Dumping data untuk tabel `tb_ak02`
--

INSERT INTO `tb_ak02` (`id_ak02`, `id_apl1`, `id_ak01`, `id_asesi`, `id_asesor`, `rekomendasi`, `tindak_lanjut`, `komentar_asesor`) VALUES
(21, 3, 9, 17, 1, NULL, NULL, NULL),
(35, 2, 7, 12, 1, NULL, NULL, NULL),
(41, 8, 6, 1, 1, 'Belum Kompeten', '1', '1'),
(45, 9, 10, 19, 1, 'Belum Kompeten', '1', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak03`
--

CREATE TABLE `tb_ak03` (
  `id_ak03` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `catatan_lainnya` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.03 Header Umpan Balik dan Catatan Asesmen';

--
-- Dumping data untuk tabel `tb_ak03`
--

INSERT INTO `tb_ak03` (`id_ak03`, `id_apl1`, `id_ak01`, `id_asesi`, `id_asesor`, `tgl_selesai`, `catatan_lainnya`) VALUES
(13, 8, 6, 1, 1, '2026-05-18', NULL),
(14, 2, 7, 12, 1, '2026-05-18', 'oke'),
(16, 3, 9, 17, 1, NULL, 'OKEEEEE'),
(18, 9, 10, 19, 1, '5000-12-31', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_apl1`
--

CREATE TABLE `tb_apl1` (
  `id_apl1` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `judul_skema` varchar(255) NOT NULL,
  `nomor_skema` varchar(100) NOT NULL,
  `tujuan_asesmen` varchar(100) NOT NULL,
  `tujuan_lainnya` text DEFAULT NULL,
  `nama_pemohon` varchar(100) NOT NULL,
  `tanggal_pemohon` date NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `rekomendasi` enum('Diterima','Tidak Diterima') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_apl1`
--

INSERT INTO `tb_apl1` (`id_apl1`, `id_skema`, `id_asesi`, `judul_skema`, `nomor_skema`, `tujuan_asesmen`, `tujuan_lainnya`, `nama_pemohon`, `tanggal_pemohon`, `catatan_admin`, `rekomendasi`) VALUES
(2, 21, 12, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Nadya', '2026-04-27', 'KERJA BaGUS', 'Diterima'),
(3, 21, 17, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Zee', '2026-04-08', 'bgau', 'Tidak Diterima'),
(5, 21, 13, 'Pemrogram Junior', '1', 'Pengakuan Kompetensi Terkini (PKT)', '', 'ABC', '2026-05-08', '', 'Diterima'),
(8, 21, 1, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Zee', '2026-05-18', '', 'Diterima'),
(9, 21, 19, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Abduh Qodir Salmin', '2026-05-23', 'NOPE', 'Tidak Diterima'),
(11, 21, 20, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'yannz', '2026-05-30', '', 'Diterima'),
(14, 29, 22, 'junior web developer', '123', 'Sertifikasi', '', 'rian', '2000-02-01', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_apl2`
--

CREATE TABLE `tb_apl2` (
  `id_apl2` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `rekomendasi` varchar(255) DEFAULT NULL,
  `tertanda` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Header FR APL-02 Asesmen Mandiri';

--
-- Dumping data untuk tabel `tb_apl2`
--

INSERT INTO `tb_apl2` (`id_apl2`, `id_apl1`, `id_asesi`, `id_asesor`, `rekomendasi`, `tertanda`) VALUES
(2, 3, 17, 1, 'Tidak Dapat', 'xammmpee'),
(3, 2, 12, 1, 'Dapat', 'Nadya'),
(7, 8, 1, 1, 'Dapat', 'Azeleone'),
(10, 9, 19, 1, 'Tidak Dapat', ''),
(12, 11, 20, 1, NULL, ''),
(15, 14, 22, 11, NULL, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_asesi`
--

CREATE TABLE `tb_asesi` (
  `id_asesi` int(11) NOT NULL,
  `nama_asesi` varchar(100) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `jenis_kelamin` varchar(50) NOT NULL,
  `kebangsaan` varchar(20) NOT NULL,
  `alamat_rumah` varchar(100) NOT NULL,
  `kode_pos` varchar(6) NOT NULL,
  `phone_rumah` varchar(15) DEFAULT NULL,
  `phone_kantor` varchar(15) DEFAULT NULL,
  `hp` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pendidikan` varchar(50) DEFAULT NULL,
  `nama_institusi` varchar(30) NOT NULL,
  `jabatan` varchar(17) NOT NULL,
  `alamat_institusi` varchar(100) NOT NULL,
  `kode_pos_institusi` varchar(6) NOT NULL,
  `telp_institusi` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `email_institusi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_asesi`
--

INSERT INTO `tb_asesi` (`id_asesi`, `nama_asesi`, `nik`, `jenis_kelamin`, `kebangsaan`, `alamat_rumah`, `kode_pos`, `phone_rumah`, `phone_kantor`, `hp`, `email`, `pendidikan`, `nama_institusi`, `jabatan`, `alamat_institusi`, `kode_pos_institusi`, `telp_institusi`, `fax`, `email_institusi`) VALUES
(1, 'Azeleone', '1234343414341111', 'Laki-laki', 'malaysoiaa', 'qiueroiuqioruqiwrwwwwww', '21313', '08111111111111', '0811111111111', '0811111111', 'rb4k0r1sk@mozmail.com', 'jmpp', 'asasap', 'gmp', 'asasap', '11234', '081111111111111', 'ak', 'sukacita@gmail.com'),
(8, 'Zee', '1234567812345678', 'Laki-laki', 'WNI', 'ALAMAT', '51119', NULL, NULL, '087765111393', 'zop7sqnin@mozmail.com', 'GOOD', 'INSTITUSI NYA', 'PEJABAT', 'YOUNG 7GG', '68612', NULL, NULL, NULL),
(12, 'Nadya', '123456789', 'Perempuan', 'Kebangsaan', 'Pekalongan', '51117', NULL, NULL, '082345678', 'nadya@smk.com', 'SMK', 'SMK Mudikal', 'Siswa', 'Pekalongan', '51117', NULL, NULL, NULL),
(13, 'NAZE', '29374017341124', 'Laki-laki', 'WNI', 'laoahiuiaiucbyebafbuibchus', '51134', NULL, NULL, '08231238479234', 'zop7sqnin@mozmail.com', 'SMK', 'KOMUNITAS', 'SWA', 'lheriuigbierbubr', '57812', NULL, NULL, NULL),
(15, 'amin', '123411241', 'Laki-laki', 'WNI', 'rgrgreergerergerrrgr3214', '14222', NULL, NULL, '0397823443', '8ftnrl49j@mozmail.com', 'ererhehereree', 'eeerhrherherherh', 'rherhererererer', 'erherhherhherherh', '34235', NULL, NULL, NULL),
(16, 'ZEE', '6117834518654', 'Laki-laki', 'WNI', 'qrqwrqrqrqwrqrqrq', '12312', NULL, NULL, '8908667', 'zop7sqnin@mozmail.com', 'qeewewewetwetewwetew', 'wetwetweetwetewtwww', 'etwetwetewtwee', 'tweewetewtwetwe', '21243', NULL, NULL, NULL),
(17, 'aXEEE', '1234567812345678', 'Perempuan', 'WNA', 'WEGWRGERTHTTHH', '23543', NULL, NULL, '05856865', 'rb4k0r1sk@mozmail.com', 'eewwetwetwt', 'wtwetwetwetwetw', 'twetwetwetw', 'twtwetwtwt', '33522', NULL, NULL, NULL),
(19, 'Abduh Qodir Salminn', '1234343414341', 'Laki-laki', 'WNI', 'JALAN KURINCI', '50012', NULL, NULL, '0838478923483', 'rb4k0r1sk@mozmail.com', 'SMK', 'MOJANG', 'SISWA', 'JALAN NEWYOURK CITY', '239733', NULL, NULL, NULL),
(20, 'yazz', '2`21213213213213', 'Laki-laki', 'WNI', 'kalimantan', '123', NULL, NULL, '1838723823', 'Q@gmail.com', 'smk', 'smk muhammadiyah', 'siswa', 'katon', '3234', NULL, NULL, NULL),
(21, 'Ashe', '2342342342342342', 'Laki-laki', 'WNI', 'erwererwerrwer', '234232', NULL, NULL, '068967557856756', 'iqwiuqwiyeiuyqiuwy@gmial.cod', 'SD', 'sCHOOL', 'SKM', 'uywiuyiyqtqwteqwe', '123123', NULL, NULL, NULL),
(22, 'asdaf', '324234', 'Laki-laki', 'WNI', 'safsadfa', '242342', '23423', NULL, '234234', 'asfs@gmail.com', 'SMK', 'SMK Muhammadiyah Pekalongan', 'Siswa', 'asdasf', '1231', '123', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_asesor`
--

CREATE TABLE `tb_asesor` (
  `id_asesor` int(11) NOT NULL,
  `no_reg` varchar(30) NOT NULL,
  `nama_asesor` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(50) NOT NULL,
  `alamat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_asesor`
--

INSERT INTO `tb_asesor` (`id_asesor`, `no_reg`, `nama_asesor`, `jenis_kelamin`, `alamat`) VALUES
(1, '123133', 'Agil ..', 'Laki-laki', 'Surabaya'),
(9, '000.008535.2015', 'KHUSNAWAN', 'Laki-laki', 'PERUM WIRABARU II WIRADESA'),
(11, '000.008535.2018', 'Agil Tri Anggoro', 'Laki-laki', 'Jl Tembaga IIc/12 Perum Podosugih Kota Pekalongan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_adm`
--

CREATE TABLE `tb_bukti_adm` (
  `id_ba` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `bukti_adm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_bukti_adm`
--

INSERT INTO `tb_bukti_adm` (`id_ba`, `id_skema`, `bukti_adm`) VALUES
(1, 21, 'Foto Kopi KTP/Kartu Pelajar'),
(2, 21, 'Pas foto 3x4 2 lembar dengan backgroud merah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_dasar`
--

CREATE TABLE `tb_bukti_dasar` (
  `id_bd` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `bukti_dasar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_bukti_dasar`
--

INSERT INTO `tb_bukti_dasar` (`id_bd`, `id_skema`, `bukti_dasar`) VALUES
(1, 21, 'Copy Raport SMK pada Konsentrasi Keahlian Rekayasa\nPerangkat Lunak semester 1 s.d 5 yang telah\nmenyelesaikan mata pelajaran berisi unit kompetensi\nyang diajukan'),
(2, 21, 'Copy sertifikat/surat keterangan Praktik Kerja Lapangan\n(PKL) pada rekayasa perangkat lunak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_elemen`
--

CREATE TABLE `tb_elemen` (
  `id_elemen` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `no_elemen` varchar(50) NOT NULL,
  `nama_elemen` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_elemen`
--

INSERT INTO `tb_elemen` (`id_elemen`, `id_unit`, `no_elemen`, `nama_elemen`) VALUES
(35, 46, '1', 'Mengidentifikasi konsep data dan struktur data'),
(36, 46, '2', 'Menerapkan struktur data dan akses terhadap struktur data tersebut'),
(37, 47, '1', 'Menggunakan metode pengembangan program'),
(38, 47, '2', 'Menggunakan diagram program dan deskripsi program'),
(39, 47, '3', 'Menerapkan hasil pemodelan ke dalam pengembangan program'),
(50, 58, '1', 'Mengidentifikasi mekanisme running atau eksekusi source code'),
(51, 58, '2', 'Mengeksekusi source code'),
(52, 58, '3', 'Mengidentifikasi hasil eksekusi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia01`
--

CREATE TABLE `tb_ia01` (
  `id_ia01` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') DEFAULT NULL,
  `umpan_balik` varchar(1000) DEFAULT NULL,
  `belum_kompeten` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.01 Ceklis Observasi Aktivitas Tempat Kerja - Header';

--
-- Dumping data untuk tabel `tb_ia01`
--

INSERT INTO `tb_ia01` (`id_ia01`, `id_apl1`, `id_ak01`, `id_asesi`, `id_asesor`, `tanggal`, `rekomendasi`, `umpan_balik`, `belum_kompeten`) VALUES
(80, 3, 9, 17, 1, NULL, 'Kompeten', NULL, NULL),
(82, 2, 7, 12, 1, NULL, 'Kompeten', 'OKE', NULL),
(84, 9, 10, 19, 1, NULL, 'Belum Kompeten', '1', 'ALL');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06`
--

CREATE TABLE `tb_ia06` (
  `id_ia06` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `id_ia06a` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `aspek` enum('tercapai','belum_tercapai') DEFAULT NULL,
  `umpan_balik` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Header sesi jawaban asesi';

--
-- Dumping data untuk tabel `tb_ia06`
--

INSERT INTO `tb_ia06` (`id_ia06`, `id_apl1`, `id_ak01`, `id_ia06a`, `id_asesor`, `id_asesi`, `aspek`, `umpan_balik`) VALUES
(8, 2, 7, 1, 1, 12, 'belum_tercapai', 'OKE'),
(9, 9, 10, 1, 1, 19, 'belum_tercapai', 'NGawiur cik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06a`
--

CREATE TABLE `tb_ia06a` (
  `id_ia06a` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_validator` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_ia06a`
--

INSERT INTO `tb_ia06a` (`id_ia06a`, `id_asesor`, `id_skema`, `id_validator`) VALUES
(1, 1, 21, 1),
(2, 1, 24, 1),
(3, 1, 25, 1),
(9, 1, 21, 4),
(12, 11, 29, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06_jawaban`
--

CREATE TABLE `tb_ia06_jawaban` (
  `id_jawaban` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_ia06` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `jawaban_asesi` text NOT NULL,
  `hasil` enum('Benar','Salah') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Jawaban asesi per soal';

--
-- Dumping data untuk tabel `tb_ia06_jawaban`
--

INSERT INTO `tb_ia06_jawaban` (`id_jawaban`, `id_asesi`, `id_ia06`, `id_soal`, `jawaban_asesi`, `hasil`) VALUES
(63, 12, 8, 7, '12', 'Salah'),
(64, 12, 8, 8, '1', 'Benar'),
(65, 12, 8, 9, '12', 'Salah'),
(66, 12, 8, 10, '12', 'Benar'),
(67, 12, 8, 11, '12', 'Salah'),
(68, 12, 8, 12, '12', 'Benar'),
(69, 12, 8, 13, '12', 'Salah'),
(70, 12, 8, 14, '12', 'Benar'),
(71, 12, 8, 15, '12', 'Salah'),
(72, 12, 8, 16, '12', 'Salah'),
(73, 19, 9, 7, '12', 'Salah'),
(74, 19, 9, 8, '12', 'Salah'),
(75, 19, 9, 9, '21', 'Salah'),
(76, 19, 9, 10, '12', 'Salah'),
(77, 19, 9, 11, '12', 'Salah'),
(78, 19, 9, 12, '12', 'Salah'),
(79, 19, 9, 13, '12', 'Salah'),
(80, 19, 9, 14, '12', 'Salah'),
(81, 19, 9, 15, '12', 'Salah'),
(82, 19, 9, 16, '12', 'Salah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_isi_bukti_adm`
--

CREATE TABLE `tb_isi_bukti_adm` (
  `id_isi_ba` int(11) NOT NULL,
  `id_ba` int(11) NOT NULL,
  `kondisi` varchar(1000) NOT NULL,
  `id_asesi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_isi_bukti_adm`
--

INSERT INTO `tb_isi_bukti_adm` (`id_isi_ba`, `id_ba`, `kondisi`, `id_asesi`) VALUES
(1, 1, 'BAGUS', 1),
(2, 1, 'Memenuhi Syarat', 15),
(5, 1, 'Memenuhi Syarat', 8),
(6, 2, 'Memenuhi Syarat', 8),
(7, 1, 'Memenuhi Syarat', 12),
(8, 2, 'Memenuhi Syarat', 12),
(9, 1, 'Tidak Memenuhi Syarat', 17),
(10, 2, 'Tidak Memenuhi Syarat', 17),
(13, 1, 'Memenuhi Syarat', 13),
(14, 2, 'Memenuhi Syarat', 13),
(17, 1, 'Memenuhi Syarat', 1),
(18, 2, 'Memenuhi Syarat', 1),
(19, 1, 'Tidak Memenuhi Syarat', 19),
(20, 2, 'Tidak Memenuhi Syarat', 19),
(21, 1, 'Memenuhi Syarat', 20),
(22, 2, 'Memenuhi Syarat', 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_isi_bukti_dasar`
--

CREATE TABLE `tb_isi_bukti_dasar` (
  `id_isi_bd` int(11) NOT NULL,
  `id_bd` int(11) NOT NULL,
  `kondisi` varchar(1000) NOT NULL,
  `id_asesi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_isi_bukti_dasar`
--

INSERT INTO `tb_isi_bukti_dasar` (`id_isi_bd`, `id_bd`, `kondisi`, `id_asesi`) VALUES
(1, 1, 'bagus', 1),
(2, 1, 'Memenuhi Syarat', 15),
(5, 1, 'Memenuhi Syarat', 8),
(6, 2, 'Memenuhi Syarat', 8),
(7, 1, 'Memenuhi Syarat', 12),
(8, 2, 'Memenuhi Syarat', 12),
(9, 1, 'Memenuhi Syarat', 17),
(10, 2, 'Memenuhi Syarat', 17),
(13, 1, 'Tidak Memenuhi Syarat', 13),
(14, 2, 'Tidak Memenuhi Syarat', 13),
(17, 1, 'Memenuhi Syarat', 1),
(18, 2, 'Memenuhi Syarat', 1),
(19, 1, 'Tidak Memenuhi Syarat', 19),
(20, 2, 'Tidak Memenuhi Syarat', 19),
(21, 1, 'Memenuhi Syarat', 20),
(22, 2, 'Memenuhi Syarat', 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kuk`
--

CREATE TABLE `tb_kuk` (
  `id_kuk` int(11) NOT NULL,
  `id_elemen` int(11) NOT NULL,
  `no_kuk` text NOT NULL,
  `kuk` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_kuk`
--

INSERT INTO `tb_kuk` (`id_kuk`, `id_elemen`, `no_kuk`, `kuk`) VALUES
(41, 35, '1.1', 'Konsep data dan struktur data diidentifikasi sesuai\\r\\ndengan konteks permasalahan.'),
(42, 35, '1.2', 'Alternatif struktur data dibandingkan kelebihan dan\r\nkekurangannya untuk konteks permasalahan yang\r\ndiselesaikan.'),
(43, 36, '2.1', 'Struktur data diimplementasikan nsesuai dengan\r\nbahasa pemrograman yang akan dipergunakan'),
(44, 36, '2.2', 'Akses terhadap data dinyatakan dalam algoritma\r\nyang efisiensi sesuai bahasa pemrograman yang\r\nakan dipakai'),
(45, 37, '1.1', 'Metode pengembangan aplikasi (software\r\ndevelopment) didefinisikan'),
(46, 37, '1.2', 'Metode pengembangann aplikasi (software development) dipilih sesuai kebutuhan'),
(47, 38, '2.1', 'Diagram program dengan metodologi pengembangan\r\nsistem didefinisikan'),
(48, 38, '2.2', 'Metode pemodelan, diagram objek dan diagram\r\nkomponen digunakan pada implementasi program\r\nsesuai dengan spesifikasi.'),
(49, 39, '3.1', 'Hasil pemodelan yang mendukung kemampuan\r\nmetodologi dipilih sesuai spesifikasi.'),
(50, 39, '3.2', 'Hasil pemrograman ( Integrated Development\r\nEnvironment-IDE ) yang mendukung kemampuan\r\nmetodologi bahasa pemrograman dipilih sesuai\r\nspesifikasi.'),
(60, 50, '1.1', 'Cara dan tools untuk mengeksekusi source code\r\ndiidentifikasi'),
(61, 50, '1.2', 'Parameter untuk mengeksekusi source code diidentifikasi.'),
(62, 50, '1.3', 'Peletakan source code sehingga bisa dieksekusi dengan\r\nbenar diidentifikasi.'),
(63, 51, '2.1', 'Source code dieksekusi sesuai\r\ndengan mekanisme eksekusi source code dari tools\r\npemrograman yang digunakan.'),
(64, 51, '2.2', 'Perbedaan antara running, debugging, atau\r\nmembuat executable file diidentifikasi.'),
(65, 52, '3.1', 'Source code berhasil dieksekusi sesuai skenario\r\nyang direncanakan.'),
(66, 52, '3.2', 'Jika eksekusi source code gagal/tidak berhasil,\r\nsumber permasalahan diidentifikasi.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_skema`
--

CREATE TABLE `tb_skema` (
  `id_skema` int(11) NOT NULL,
  `nomor_skema` varchar(100) NOT NULL,
  `judul_skema` varchar(100) NOT NULL,
  `standar_kompetensi_kerja` varchar(100) NOT NULL,
  `id_asesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_skema`
--

INSERT INTO `tb_skema` (`id_skema`, `nomor_skema`, `judul_skema`, `standar_kompetensi_kerja`, `id_asesor`) VALUES
(21, '1', 'Pemrogram Junior', 'Okupasi', 1),
(24, '2', 'Pemrogram Pemula', 'aaa', 1),
(25, '3', 'Junior Web Programming', 'SKKNI', 1),
(29, '123', 'junior web developer', 'skkni no II', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_soal`
--

CREATE TABLE `tb_soal` (
  `id_soal` int(11) NOT NULL,
  `id_ia06a` int(11) NOT NULL,
  `soal` varchar(500) DEFAULT NULL,
  `kunci_jawaban` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_soal`
--

INSERT INTO `tb_soal` (`id_soal`, `id_ia06a`, `soal`, `kunci_jawaban`) VALUES
(2, 2, 'MBG BERMANFAAT TIDAKK', 'TIDAKKKKKKKKKKKKKKKKKK'),
(7, 1, 'Jelaskan perbedaan antara HTML, CSS, dan JavaScript dalam pengembangan web.', 'HTML (HyperText Markup Language) digunakan untuk menentukan struktur konten web, CSS (Cascading Style\r\nSheets) digunakan untuk mengatur tampilan dan gaya dari elemen HTML, sementara JavaScript digunakan\r\nuntuk menambahkan interaktivitas dan logika ke situs web.'),
(8, 1, 'Apa itu framework dalam pemrograman web? Sebutkan beberapa framework populer untuk pengembangan\r\nfront-end dan back-end.', 'Framework adalah kerangka kerja yang menyediakan sekumpulan alat dan pustaka untuk memudahkan\r\npengembangan aplikasi web. Contoh front-end frameworks: React, Angular, Vue.js. Contoh back-end\r\nframeworks: Express.js, Laravel, Django.'),
(9, 1, 'Apa peran responsivitas dalam desain web, dan bagaimana cara menerapkannya menggunakan CSS?', 'Responsivitas memastikan situs web dapat diakses dengan baik di berbagai perangkat (desktop, tablet,\r\nsmartphone). Ini diterapkan dengan CSS menggunakan media queries untuk menyesuaikan layout sesuai\r\nukuran layar.'),
(10, 1, 'Apa itu REST API, dan bagaimana cara mengintegrasikannya ke dalam aplikasi web?', 'REST (Representational State Transfer) API adalah arsitektur untuk berkomunikasi antara klien dan server\r\nmenggunakan HTTP. Integrasi REST API dilakukan dengan mengirim permintaan HTTP (GET, POST, PUT, DELETE)\r\ndari aplikasi web ke server.'),
(11, 1, 'Bagaimana cara mengoptimalkan performa website agar lebih cepat diakses oleh pengguna?', 'Optimasi performa website dapat dilakukan dengan mengurangi ukuran file, meminifikasi CSS dan JavaScript,\r\nmenggunakan caching, mempercepat waktu respons server, dan memanfaatkan Content Delivery Network\r\n(CDN).'),
(12, 1, 'Jelaskan perbedaan antara database relasional dan non-relasional. Berikan contoh masing-masing.', 'Database relasional menggunakan tabel-tabel yang saling berhubungan (contoh: MySQL, PostgreSQL),\r\nsedangkan database non-relasional menyimpan data dalam format non-tabular, seperti dokumen, key-value,\r\ngraf (contoh: MongoDB, Redis).'),
(13, 1, 'Apa itu normalisasi dalam database, dan mengapa penting dilakukan?', 'Normalisasi adalah proses pengorganisasian data dalam tabel untuk mengurangi duplikasi dan meningkatkan\r\nintegritas data. Ini penting untuk menjaga konsistensi data dan efisiensi penyimpanan.'),
(14, 1, 'Jelaskan konsep primary key dan foreign key dalam relasi antar tabel pada database.', 'Primary key adalah kolom unik di setiap tabel yang mengidentifikasi setiap baris. Foreign key adalah kolom yang\r\nmengacu pada primary key di tabel lain, digunakan untuk membangun relasi antar tabel.'),
(15, 1, 'Apa perbedaan antara DDL (Data Definition Language) dan DML (Data Manipulation Language) dalam SQL?\r\nBerikan contoh masing-masing.', 'DDL digunakan untuk mendefinisikan struktur database (misal: CREATE TABLE, ALTER TABLE), sedangkan DML\r\ndigunakan untuk memanipulasi data dalam tabel (misal: SELECT, INSERT, UPDATE, DELETE).'),
(16, 1, 'Jelaskan pengertian dan kepanjangan dari SQL!', 'SQL merupakan singkatan dari \"Structured Query Language\", yang diterjemahkan sebagai Bahasa Permintaan\r\nTerstruktur. Ini adalah bahasa standar yang digunakan untuk mengelola dan berinteraksi dengan database\r\nrelasional. SQL memungkinkan pengguna untuk membuat, mengelola, dan memanipulasi data dalam database\r\nsecara efektif.'),
(17, 3, 'Test', 'ok'),
(30, 12, 'asdfadfasd', 'sdsdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_unit_kompetensi`
--

CREATE TABLE `tb_unit_kompetensi` (
  `id_unit` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `kode_unit` varchar(100) NOT NULL,
  `judul_unit` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_unit_kompetensi`
--

INSERT INTO `tb_unit_kompetensi` (`id_unit`, `id_skema`, `kode_unit`, `judul_unit`) VALUES
(46, 21, 'J.620100.004.02', 'Menggunakan Struktur Data'),
(47, 21, 'J.620100.009.01', 'Menggunakan Spesifikasi Program'),
(58, 21, 'J.620100.010.01', 'Menerapkan Perintah Eksekusi Bahasa Pemrograman Berbasis Teks, Grafik, dan Multimedia'),
(70, 29, 'DD12000', 'Menggunakan Struktur Data'),
(71, 29, '22123', 'sfasdfa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_validator`
--

CREATE TABLE `tb_validator` (
  `id_validator` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `noreg` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_validator`
--

INSERT INTO `tb_validator` (`id_validator`, `username`, `noreg`) VALUES
(1, 'BAGINDA', 'MET - 1781948 - MERDEKA'),
(4, 'Althaf', 'MET-0192129083-DISK'),
(5, 'GANJAR', 'MET-893UHF872Y38H-IHSG');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin_lsp','Asesor','Asesi') NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_asesor` int(11) DEFAULT NULL,
  `id_asesi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `id_admin`, `id_asesor`, `id_asesi`) VALUES
(1, 'adminmudikal', 'Admin1234', 'Admin_lsp', 1, NULL, NULL),
(2, 'asesor1', 'Admin1234', 'Asesor', NULL, 1, NULL),
(4, 'assesi1', 'Admin1234', 'Asesi', NULL, NULL, 1),
(23, 'assesi2', 'Admin1234', 'Asesi', NULL, NULL, NULL),
(30, 'althaf', 'ojorapak', 'Asesi', NULL, NULL, 8),
(32, 'ACE', '123', 'Asesi', NULL, NULL, 13),
(34, 'anda', '1', 'Asesi', NULL, NULL, 16),
(35, 'Ash', 'Admin1234', 'Asesi', NULL, NULL, 17),
(38, 'yanzz', '12345', 'Asesi', NULL, NULL, 20),
(41, 'Ashe', 'Admin1234', 'Asesi', NULL, NULL, 21),
(42, 'Rian', 'asesi', 'Asesi', NULL, NULL, 22),
(43, 'Agil Tri Anggoro', 'asesor', 'Asesor', NULL, 11, NULL),
(44, 'Urip Taufiq Hidayat', 'asesor', 'Asesor', NULL, NULL, NULL),
(45, 'yan', 'yan', 'Asesi', NULL, NULL, NULL),
(46, 'yaw', 'yaw', 'Asesi', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users_admin`
--

CREATE TABLE `users_admin` (
  `id_user_admin` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin_utm') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users_admin`
--

INSERT INTO `users_admin` (`id_user_admin`, `username`, `password`, `role`) VALUES
(1, 'Zee', 'Utama1234', 'Admin_utm');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_ak02`
--
ALTER TABLE `detail_ak02`
  ADD PRIMARY KEY (`id_detail_ak02`),
  ADD KEY `fk_detailak02_ak02` (`id_ak02`),
  ADD KEY `fk_detailak02_skema` (`id_skema`),
  ADD KEY `fk_detailak02_unit` (`id_unit`);

--
-- Indeks untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD PRIMARY KEY (`id_detail_ak1`),
  ADD KEY `fk_detailak1_ak01` (`id_ak01`);

--
-- Indeks untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  ADD PRIMARY KEY (`id_detail_apl2`),
  ADD KEY `fk_detail-apl2_apl2` (`id_apl2`),
  ADD KEY `fk_detail-apl2_skema` (`id_skema`),
  ADD KEY `fk_detail-apl2_unit` (`id_unit`),
  ADD KEY `fk_detail-apl2_elemen` (`id_elemen`),
  ADD KEY `fk_detail-apl2_kuk` (`id_kuk`);

--
-- Indeks untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  ADD PRIMARY KEY (`id_detail_ia1`),
  ADD KEY `fx_detailia01_ia01` (`id_ia01`),
  ADD KEY `fk_detailia01_skema` (`id_skema`),
  ADD KEY `fk_detailia01_unit` (`id_unit`),
  ADD KEY `fk_detailia01_elemen` (`id_elemen`),
  ADD KEY `fk_detailia01_kuk` (`id_kuk`);

--
-- Indeks untuk tabel `hasil_ak03`
--
ALTER TABLE `hasil_ak03`
  ADD PRIMARY KEY (`id_detail_ak03`),
  ADD KEY `fk_hasil_ak03` (`id_ak03`);

--
-- Indeks untuk tabel `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  ADD PRIMARY KEY (`id_ak01`),
  ADD KEY `fk_ak01_asesor` (`id_asesor`),
  ADD KEY `fk_ak01_apl1` (`id_apl1`),
  ADD KEY `fk_ak01_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  ADD PRIMARY KEY (`id_ak02`),
  ADD KEY `fk_ak03_apl1` (`id_apl1`),
  ADD KEY `fk_ak03_ak01` (`id_ak01`),
  ADD KEY `fk_ak02_asesor` (`id_asesor`),
  ADD KEY `fk_ak02_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  ADD PRIMARY KEY (`id_ak03`),
  ADD KEY `fk_ak03_asesor` (`id_asesor`),
  ADD KEY `fk_ak03_apl1` (`id_apl1`),
  ADD KEY `fk_ak03_ak01` (`id_ak01`),
  ADD KEY `fk_ak03_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  ADD PRIMARY KEY (`id_apl1`),
  ADD KEY `fk_apl1_skema` (`id_skema`),
  ADD KEY `fk_apl1_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  ADD PRIMARY KEY (`id_apl2`),
  ADD KEY `idx_apl2_asesi` (`id_asesi`),
  ADD KEY `idx_apl2_asesor` (`id_asesor`),
  ADD KEY `idx_apl2_apl1` (`id_apl1`);

--
-- Indeks untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  ADD PRIMARY KEY (`id_asesi`);

--
-- Indeks untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  ADD PRIMARY KEY (`id_asesor`),
  ADD UNIQUE KEY `nama_asesor` (`no_reg`),
  ADD UNIQUE KEY `idx_nama_asesor` (`nama_asesor`);

--
-- Indeks untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  ADD PRIMARY KEY (`id_ba`),
  ADD KEY `fk_bukti_adm_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  ADD PRIMARY KEY (`id_bd`),
  ADD KEY `fk_bukti_dasar_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  ADD PRIMARY KEY (`id_elemen`),
  ADD KEY `fk_elemen_unit` (`id_unit`);

--
-- Indeks untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  ADD PRIMARY KEY (`id_ia01`),
  ADD KEY `fx_ia01_asesor` (`id_asesor`),
  ADD KEY `fk_ia01_apl1` (`id_apl1`),
  ADD KEY `fk_ia01_ak01` (`id_ak01`),
  ADD KEY `fk_ia01_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  ADD PRIMARY KEY (`id_ia06`),
  ADD KEY `idx_ia06_asesi` (`id_asesi`),
  ADD KEY `fk_ia06_apl1` (`id_apl1`),
  ADD KEY `fk_ia06_ia06a` (`id_ia06a`),
  ADD KEY `fk_ia06_asesor` (`id_asesor`),
  ADD KEY `fk_ia06_ak01` (`id_ak01`);

--
-- Indeks untuk tabel `tb_ia06a`
--
ALTER TABLE `tb_ia06a`
  ADD PRIMARY KEY (`id_ia06a`),
  ADD KEY `fk_ia06a_asesor` (`id_asesor`),
  ADD KEY `fk_ia06a_skema` (`id_skema`),
  ADD KEY `fk_ia06a_val` (`id_validator`);

--
-- Indeks untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD KEY `idx_jawaban_ia06` (`id_ia06`),
  ADD KEY `idx_jawaban_soal` (`id_soal`),
  ADD KEY `fk_ia06_jawaban_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  ADD PRIMARY KEY (`id_isi_ba`),
  ADD KEY `fk_isi_bukti_adm` (`id_ba`),
  ADD KEY `fk_adm_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  ADD PRIMARY KEY (`id_isi_bd`),
  ADD KEY `fk_isi_bukti_dasar` (`id_bd`),
  ADD KEY `fk_dasar_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  ADD PRIMARY KEY (`id_kuk`),
  ADD KEY `fk_kuk_elemen` (`id_elemen`);

--
-- Indeks untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  ADD PRIMARY KEY (`id_skema`),
  ADD KEY `fk_skema_asesor` (`id_asesor`);

--
-- Indeks untuk tabel `tb_soal`
--
ALTER TABLE `tb_soal`
  ADD PRIMARY KEY (`id_soal`),
  ADD KEY `fk_soal_ia06` (`id_ia06a`);

--
-- Indeks untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  ADD PRIMARY KEY (`id_unit`),
  ADD KEY `id_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_validator`
--
ALTER TABLE `tb_validator`
  ADD PRIMARY KEY (`id_validator`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nik` (`username`),
  ADD KEY `fk_users_asesor` (`id_asesor`),
  ADD KEY `fk_users_asesi` (`id_asesi`),
  ADD KEY `fk_users_admin` (`id_admin`);

--
-- Indeks untuk tabel `users_admin`
--
ALTER TABLE `users_admin`
  ADD PRIMARY KEY (`id_user_admin`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_ak02`
--
ALTER TABLE `detail_ak02`
  MODIFY `id_detail_ak02` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  MODIFY `id_detail_ak1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  MODIFY `id_detail_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  MODIFY `id_detail_ia1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=462;

--
-- AUTO_INCREMENT untuk tabel `hasil_ak03`
--
ALTER TABLE `hasil_ak03`
  MODIFY `id_detail_ak03` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  MODIFY `id_ak01` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  MODIFY `id_ak02` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  MODIFY `id_ak03` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  MODIFY `id_apl1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  MODIFY `id_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  MODIFY `id_asesi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  MODIFY `id_asesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  MODIFY `id_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  MODIFY `id_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  MODIFY `id_elemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  MODIFY `id_ia01` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  MODIFY `id_ia06` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06a`
--
ALTER TABLE `tb_ia06a`
  MODIFY `id_ia06a` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  MODIFY `id_isi_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  MODIFY `id_isi_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  MODIFY `id_kuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  MODIFY `id_skema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `tb_soal`
--
ALTER TABLE `tb_soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `tb_validator`
--
ALTER TABLE `tb_validator`
  MODIFY `id_validator` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_ak02`
--
ALTER TABLE `detail_ak02`
  ADD CONSTRAINT `fk_detailak02_ak02` FOREIGN KEY (`id_ak02`) REFERENCES `tb_ak02` (`id_ak02`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detailak02_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detailak02_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD CONSTRAINT `fk_detailak1_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  ADD CONSTRAINT `fk_detail-apl2_apl2` FOREIGN KEY (`id_apl2`) REFERENCES `tb_apl2` (`id_apl2`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail-apl2_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail-apl2_kuk` FOREIGN KEY (`id_kuk`) REFERENCES `tb_kuk` (`id_kuk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail-apl2_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail-apl2_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  ADD CONSTRAINT `fk_detailia01_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detailia01_kuk` FOREIGN KEY (`id_kuk`) REFERENCES `tb_kuk` (`id_kuk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detailia01_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detailia01_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hasil_ak03`
--
ALTER TABLE `hasil_ak03`
  ADD CONSTRAINT `fk_hasil_ak03` FOREIGN KEY (`id_ak03`) REFERENCES `tb_ak03` (`id_ak03`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  ADD CONSTRAINT `fk_ak01_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  ADD CONSTRAINT `fk_ak02_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak02_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak02_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak02_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  ADD CONSTRAINT `fk_ak03_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak03_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak03_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak03_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  ADD CONSTRAINT `fk_apl1_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apl1_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  ADD CONSTRAINT `fk_apl2_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `idx_apl2_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `idx_apl2_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  ADD CONSTRAINT `fk_bukti_adm_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  ADD CONSTRAINT `fk_bukti_dasar_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  ADD CONSTRAINT `fk_elemen_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  ADD CONSTRAINT `fk_ia01_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia01_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia01_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `idx_ia01_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  ADD CONSTRAINT `fk_ia06_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06_ia06a` FOREIGN KEY (`id_ia06a`) REFERENCES `tb_ia06a` (`id_ia06a`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06a`
--
ALTER TABLE `tb_ia06a`
  ADD CONSTRAINT `fk_ia06a_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06a_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06a_val` FOREIGN KEY (`id_validator`) REFERENCES `tb_validator` (`id_validator`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  ADD CONSTRAINT `fk_ia06_jawaban_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jawaban_ia06` FOREIGN KEY (`id_ia06`) REFERENCES `tb_ia06` (`id_ia06`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jawaban_soal` FOREIGN KEY (`id_soal`) REFERENCES `tb_soal` (`id_soal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  ADD CONSTRAINT `fk_adm_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_isi_bukti_adm` FOREIGN KEY (`id_ba`) REFERENCES `tb_bukti_adm` (`id_ba`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  ADD CONSTRAINT `fk_dasar_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_isi_bukti_dasar` FOREIGN KEY (`id_bd`) REFERENCES `tb_bukti_dasar` (`id_bd`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  ADD CONSTRAINT `fk_kuk_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  ADD CONSTRAINT `fk_skema_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_soal`
--
ALTER TABLE `tb_soal`
  ADD CONSTRAINT `fk_soal_ia06a` FOREIGN KEY (`id_ia06a`) REFERENCES `tb_ia06a` (`id_ia06a`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  ADD CONSTRAINT `fk_unit_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_unit_kompetensi_ibfk_1` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`id_admin`) REFERENCES `tb_admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
