-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 01.09
-- Versi server: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- Versi PHP: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisuskom1`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak01`
--

CREATE TABLE `tb_ak01` (
  `id_ak01` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL COMMENT 'Nilai = id_apl1 (FK fk_ak01_apl1)',
  `id_skema` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `tuk_pelaksanaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.01 Persetujuan Asesmen dan Kerahasiaan';

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  ADD PRIMARY KEY (`id_ak01`),
  ADD KEY `idx_ak01_asesi` (`id_asesi`),
  ADD KEY `idx_ak01_skema` (`id_skema`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  MODIFY `id_ak01` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  ADD CONSTRAINT `fk_ak01_apl1` FOREIGN KEY (`id_asesi`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
