-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 11 Mar 2026 pada 01.17
-- Versi server: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Versi PHP: 8.4.16

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
(8, 'Zee', '1234567812345678', 'Laki-laki', 'WNI', 'ALAMAT', '51119', NULL, NULL, '087765111393', 'zop7sqnin@mozmail.com', 'GOOD', 'INSTITUSI NYA', 'PEJABAT', 'YOUNG 7GG', '68612', NULL, NULL, NULL);

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
(1, '123133', 'Agil Khusnawan', 'Laki-laki', 'Surabaya'),
(2, '200123', 'REDHATLINUX', 'Perempuan', 'london'),
(9, '000.008535.2015', 'KHUSNAWAN', 'Laki-laki', 'PERUM WIRABARU II WIRADESA');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_adm`
--

CREATE TABLE `tb_bukti_adm` (
  `id_ba` int(11) NOT NULL,
  `bukti_adm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_bukti_adm`
--

INSERT INTO `tb_bukti_adm` (`id_ba`, `bukti_adm`) VALUES
(1, 'RIyan ADM AMDliqhefu89 ');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_dasar`
--

CREATE TABLE `tb_bukti_dasar` (
  `id_bd` int(11) NOT NULL,
  `bukti_dasar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_bukti_dasar`
--

INSERT INTO `tb_bukti_dasar` (`id_bd`, `bukti_dasar`) VALUES
(1, 'RIYAN MENYONTEK');

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
(48, 56, '1', '2');

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
(1, 1, 'BAGUS', 1);

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
(1, 1, 'bagus', 1);

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
(41, 35, '1.1', 'Konsep data dan struktur data diidentifikasi sesuai\r\ndengan konteks permasalahan.'),
(42, 35, '1.2', 'Alternatif struktur data dibandingkan kelebihan dan\r\nkekurangannya untuk konteks permasalahan yang\r\ndiselesaikan.'),
(43, 36, '2.11', 'Struktur data diimplementasikan nsesuai dengan\r\nbahasa pemrograman yang akan dipergunakan'),
(44, 36, '2.2', 'Akses terhadap data dinyatakan dalam algoritma\r\nyang efisiensi sesuai bahasa pemrograman yang\r\nakan dipakai'),
(45, 37, '1.1', 'Metode pengembangan aplikasi (software\r\ndevelopment) didefinisikan'),
(46, 37, '2.11', 'Metode pengembangann aplikasi (software development) dipilih sesuai kebutuhan'),
(47, 38, '2.1', 'Diagram program dengan metodologi pengembangan\r\nsistem didefinisikan'),
(48, 38, '2.2', 'Metode pemodelan, diagram objek dan diagram\r\nkomponen digunakan pada implementasi program\r\nsesuai dengan spesifikasi.'),
(49, 39, '3.1', 'Hasil pemodelan yang mendukung kemampuan\r\nmetodologi dipilih sesuai spesifikasi.'),
(50, 39, '3.2', 'Hasil pemrograman ( Integrated Development\r\nEnvironment-IDE ) yang mendukung kemampuan\r\nmetodologi bahasa pemrograman dipilih sesuai\r\nspesifikasi.'),
(59, 48, '11', '11');

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
(23, '1', 'MANAJEMEN', 'Bagus', 2);

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
(56, 23, '1', 'MAKANan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Asesor','Asesi') NOT NULL,
  `id_asesor` int(11) DEFAULT NULL,
  `id_asesi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `id_asesor`, `id_asesi`) VALUES
(1, 'adminmudikal', 'Admin1234', 'Admin', NULL, NULL),
(2, 'asesor1', 'Admin1234', 'Asesor', 1, NULL),
(3, 'asesor2', 'Admin1234', 'Asesor', 2, NULL),
(4, 'assesi1', 'Admin1234', 'Asesi', NULL, 1),
(23, 'assesi2', 'Admin1234', 'Asesi', NULL, NULL),
(27, 'Zee', 'Admin1234', 'Admin', NULL, NULL),
(30, 'althaf', 'ojorapak', 'Asesi', NULL, 8),
(32, 'ACE', '123', 'Asesi', NULL, NULL);

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`id_ba`);

--
-- Indeks untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  ADD PRIMARY KEY (`id_bd`);

--
-- Indeks untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  ADD PRIMARY KEY (`id_elemen`),
  ADD KEY `fk_elemen_unit` (`id_unit`);

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
-- Indeks untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  ADD PRIMARY KEY (`id_unit`),
  ADD KEY `id_skema` (`id_skema`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nik` (`username`),
  ADD KEY `fk_users_asesor` (`id_asesor`),
  ADD KEY `fk_users_asesi` (`id_asesi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  MODIFY `id_asesi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  MODIFY `id_asesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  MODIFY `id_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  MODIFY `id_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  MODIFY `id_elemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  MODIFY `id_isi_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  MODIFY `id_isi_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  MODIFY `id_kuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  MODIFY `id_skema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  ADD CONSTRAINT `fk_elemen_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  ADD CONSTRAINT `fk_adm_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`),
  ADD CONSTRAINT `fk_isi_bukti_adm` FOREIGN KEY (`id_ba`) REFERENCES `tb_bukti_adm` (`id_ba`);

--
-- Ketidakleluasaan untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  ADD CONSTRAINT `fk_dasar_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`),
  ADD CONSTRAINT `fk_isi_bukti_dasar` FOREIGN KEY (`id_bd`) REFERENCES `tb_bukti_dasar` (`id_bd`);

--
-- Ketidakleluasaan untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  ADD CONSTRAINT `fk_kuk_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  ADD CONSTRAINT `fk_skema_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`);

--
-- Ketidakleluasaan untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  ADD CONSTRAINT `fk_unit_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_unit_kompetensi_ibfk_1` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
