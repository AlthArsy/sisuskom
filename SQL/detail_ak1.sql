-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 01.08
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
-- Struktur dari tabel `detail_ak1`
--

CREATE TABLE `detail_ak1` (
  `id_detail_ak1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `bukti` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD PRIMARY KEY (`id_detail_ak1`),
  ADD KEY `idx_detail_ak1_ak01` (`id_ak01`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  MODIFY `id_detail_ak1` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD CONSTRAINT `fk_detail_ak1_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
