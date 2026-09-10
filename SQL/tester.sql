-- phpMyAdmin SQL Dump
-- version 5.2.3deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 29 Agu 2026 pada 05.21
-- Versi server: 8.4.10-0ubuntu0.26.04.1
-- Versi PHP: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `tester`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak02`
--

CREATE TABLE `detail_ak02` (
  `id_detail_ak02` int NOT NULL,
  `id_ak02` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_unit` int NOT NULL,
  `obs_demonstrasi` varchar(200) DEFAULT NULL,
  `portofolio` varchar(200) DEFAULT NULL,
  `pyt_pihak_ketiga` varchar(200) DEFAULT NULL,
  `pyt_wawancara` varchar(200) DEFAULT NULL,
  `pyt_lisan` varchar(200) DEFAULT NULL,
  `pyt_pertulis` varchar(200) DEFAULT NULL,
  `proyek_kerja` varchar(200) DEFAULT NULL,
  `lainnya` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak1`
--

CREATE TABLE `detail_ak1` (
  `id_detail_ak1` int NOT NULL,
  `id_ak01` int NOT NULL,
  `bukti` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak5`
--

CREATE TABLE `detail_ak5` (
  `id_detail_ak5` int NOT NULL,
  `id_ak5` int NOT NULL,
  `id_asesi` int NOT NULL,
  `rekomend` enum('K','BK') NOT NULL,
  `keterangan` text,
  `tanggal` varchar(50) DEFAULT NULL,
  `aspek` varchar(255) DEFAULT NULL,
  `pencatatan` varchar(255) DEFAULT NULL,
  `saran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_apl2`
--

CREATE TABLE `detail_apl2` (
  `id_detail_apl2` int NOT NULL,
  `id_apl2` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_unit` int NOT NULL,
  `id_elemen` int NOT NULL,
  `id_kuk` int NOT NULL,
  `nilai` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ia01`
--

CREATE TABLE `detail_ia01` (
  `id_detail_ia1` int NOT NULL,
  `id_ia01` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_unit` int NOT NULL,
  `id_elemen` int NOT NULL,
  `id_kuk` int NOT NULL,
  `pencapaian` varchar(100) DEFAULT NULL,
  `Penilaian Lanjut` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_ak03`
--

CREATE TABLE `hasil_ak03` (
  `id_detail_ak03` int NOT NULL,
  `id_ak03` int NOT NULL,
  `hasil` varchar(100) NOT NULL,
  `komentar_asesi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak01`
--

CREATE TABLE `tb_ak01` (
  `id_ak01` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_asesor` int NOT NULL,
  `id_asesi` int NOT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `waktu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuk_pelaksanaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.01 Persetujuan Asesmen dan Kerahasiaan';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak02`
--

CREATE TABLE `tb_ak02` (
  `id_ak02` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_ak01` int NOT NULL,
  `id_asesi` int NOT NULL,
  `id_asesor` int NOT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `komentar_asesor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.02 Header Rekaman Asesmen Kompetensi';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak03`
--

CREATE TABLE `tb_ak03` (
  `id_ak03` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_ak01` int NOT NULL,
  `id_asesi` int NOT NULL,
  `id_asesor` int NOT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `catatan_lainnya` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.03 Header Umpan Balik dan Catatan Asesmen';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak05`
--

CREATE TABLE `tb_ak05` (
  `id_ak5` int NOT NULL,
  `id_asesor` int NOT NULL,
  `id_apl1` int NOT NULL,
  `catatan` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_apl1`
--

CREATE TABLE `tb_apl1` (
  `id_apl1` int NOT NULL,
  `id_jadwal` int NOT NULL,
  `id_det_periode` int NOT NULL,
  `id_asesi` int NOT NULL,
  `tujuan_asesmen` varchar(100) NOT NULL,
  `tujuan_lainnya` text,
  `nama_pemohon` varchar(100) NOT NULL,
  `tanggal_pemohon` date NOT NULL,
  `catatan_admin` text,
  `rekomendasi` enum('Diterima','Tidak Diterima') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_apl2`
--

CREATE TABLE `tb_apl2` (
  `id_apl2` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_asesi` int NOT NULL,
  `id_asesor` int NOT NULL,
  `rekomendasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tertanda` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Header FR APL-02 Asesmen Mandiri';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_asesi`
--

CREATE TABLE `tb_asesi` (
  `id_asesi` int NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_asesor`
--

CREATE TABLE `tb_asesor` (
  `id_asesor` int NOT NULL,
  `no_reg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_asesor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kelamin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_adm`
--

CREATE TABLE `tb_bukti_adm` (
  `id_ba` int NOT NULL,
  `id_skema` int NOT NULL,
  `bukti_adm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bukti_dasar`
--

CREATE TABLE `tb_bukti_dasar` (
  `id_bd` int NOT NULL,
  `id_skema` int NOT NULL,
  `bukti_dasar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_detail_user`
--

CREATE TABLE `tb_detail_user` (
  `id_detail_user` int NOT NULL,
  `id_user` int NOT NULL,
  `id_periode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_det_periode`
--

CREATE TABLE `tb_det_periode` (
  `id_det_periode` int NOT NULL,
  `id_asesor` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_periode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_elemen`
--

CREATE TABLE `tb_elemen` (
  `id_elemen` int NOT NULL,
  `id_unit` int NOT NULL,
  `no_elemen` varchar(50) NOT NULL,
  `nama_elemen` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia01`
--

CREATE TABLE `tb_ia01` (
  `id_ia01` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_ak01` int NOT NULL,
  `id_asesi` int NOT NULL,
  `id_asesor` int NOT NULL,
  `tanggal` date DEFAULT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `umpan_balik` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `belum_kompeten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.01 Ceklis Observasi Aktivitas Tempat Kerja - Header';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06`
--

CREATE TABLE `tb_ia06` (
  `id_ia06` int NOT NULL,
  `id_apl1` int NOT NULL,
  `id_ak01` int NOT NULL,
  `id_ia06a` int NOT NULL,
  `id_asesor` int NOT NULL,
  `id_asesi` int NOT NULL,
  `aspek` enum('tercapai','belum_tercapai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `umpan_balik` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Header sesi jawaban asesi';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06a`
--

CREATE TABLE `tb_ia06a` (
  `id_ia06a` int NOT NULL,
  `id_asesor` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_validator` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06_jawaban`
--

CREATE TABLE `tb_ia06_jawaban` (
  `id_jawaban` int NOT NULL,
  `id_asesi` int NOT NULL,
  `id_ia06` int NOT NULL,
  `id_soal` int NOT NULL,
  `jawaban_asesi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil` enum('Benar','Salah') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Jawaban asesi per soal';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_isi_bukti_adm`
--

CREATE TABLE `tb_isi_bukti_adm` (
  `id_isi_ba` int NOT NULL,
  `id_ba` int NOT NULL,
  `kondisi` varchar(1000) NOT NULL,
  `id_asesi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_isi_bukti_dasar`
--

CREATE TABLE `tb_isi_bukti_dasar` (
  `id_isi_bd` int NOT NULL,
  `id_bd` int NOT NULL,
  `kondisi` varchar(1000) NOT NULL,
  `id_asesi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_jadwal`
--

CREATE TABLE `tb_jadwal` (
  `id_jadwal` int NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_periode` int NOT NULL,
  `id_skema` int NOT NULL,
  `id_asesor` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kuk`
--

CREATE TABLE `tb_kuk` (
  `id_kuk` int NOT NULL,
  `id_elemen` int NOT NULL,
  `no_kuk` text NOT NULL,
  `kuk` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_periode`
--

CREATE TABLE `tb_periode` (
  `id_periode` int NOT NULL,
  `tahun_ajaran` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_skema`
--

CREATE TABLE `tb_skema` (
  `id_skema` int NOT NULL,
  `nomor_skema` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `judul_skema` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `standar_kompetensi_kerja` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_periode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_soal`
--

CREATE TABLE `tb_soal` (
  `id_soal` int NOT NULL,
  `id_ia06a` int NOT NULL,
  `soal` varchar(500) DEFAULT NULL,
  `kunci_jawaban` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_unit_kompetensi`
--

CREATE TABLE `tb_unit_kompetensi` (
  `id_unit` int NOT NULL,
  `id_skema` int NOT NULL,
  `kode_unit` varchar(100) NOT NULL,
  `judul_unit` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_validator`
--

CREATE TABLE `tb_validator` (
  `id_validator` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `noreg` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Admin_lsp','Asesor','Asesi') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_periode` int DEFAULT NULL,
  `id_admin` int DEFAULT NULL,
  `id_asesor` int DEFAULT NULL,
  `id_asesi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users_admin`
--

CREATE TABLE `users_admin` (
  `id_user_admin` int NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin_utm') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `users_admin`
--

INSERT INTO `users_admin` (`id_user_admin`, `username`, `password`, `role`) VALUES
(1, 'Admin Mudikal', '751cb3f4aa17c36186f4856c8982bf27', 'Admin_utm');

--
-- Indeks untuk tabel yang dibuang
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
-- Indeks untuk tabel `detail_ak5`
--
ALTER TABLE `detail_ak5`
  ADD PRIMARY KEY (`id_detail_ak5`),
  ADD KEY `fk_detail5_ak5` (`id_ak5`),
  ADD KEY `fk_detail5_asesi` (`id_asesi`);

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
-- Indeks untuk tabel `tb_ak05`
--
ALTER TABLE `tb_ak05`
  ADD PRIMARY KEY (`id_ak5`),
  ADD KEY `fk_ak5_asesor` (`id_asesor`),
  ADD KEY `fk_ak5_apl1` (`id_apl1`);

--
-- Indeks untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  ADD PRIMARY KEY (`id_apl1`),
  ADD KEY `fk_apl1_asesi` (`id_asesi`),
  ADD KEY `id_apl1_dp` (`id_det_periode`),
  ADD KEY `fk_apl1_jadwal` (`id_jadwal`);

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
-- Indeks untuk tabel `tb_detail_user`
--
ALTER TABLE `tb_detail_user`
  ADD PRIMARY KEY (`id_detail_user`),
  ADD KEY `fk_du_user` (`id_user`),
  ADD KEY `fk_du_periode` (`id_periode`);

--
-- Indeks untuk tabel `tb_det_periode`
--
ALTER TABLE `tb_det_periode`
  ADD PRIMARY KEY (`id_det_periode`),
  ADD KEY `fk_det_asesor` (`id_asesor`),
  ADD KEY `fk_det_skema` (`id_skema`),
  ADD KEY `fk_det_periode` (`id_periode`);

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
-- Indeks untuk tabel `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `fk_jadwal_periode` (`id_periode`),
  ADD KEY `fk_jadwal_skema` (`id_skema`),
  ADD KEY `fk_jadwal_asesor` (`id_asesor`);

--
-- Indeks untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  ADD PRIMARY KEY (`id_kuk`),
  ADD KEY `fk_kuk_elemen` (`id_elemen`);

--
-- Indeks untuk tabel `tb_periode`
--
ALTER TABLE `tb_periode`
  ADD PRIMARY KEY (`id_periode`);

--
-- Indeks untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  ADD PRIMARY KEY (`id_skema`),
  ADD KEY `fk_periode` (`id_periode`);

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
  ADD KEY `fk_users_admin` (`id_admin`),
  ADD KEY `fk_user_periode` (`id_periode`);

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
  MODIFY `id_detail_ak02` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  MODIFY `id_detail_ak1` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `detail_ak5`
--
ALTER TABLE `detail_ak5`
  MODIFY `id_detail_ak5` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  MODIFY `id_detail_apl2` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  MODIFY `id_detail_ia1` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `hasil_ak03`
--
ALTER TABLE `hasil_ak03`
  MODIFY `id_detail_ak03` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT untuk tabel `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  MODIFY `id_ak01` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  MODIFY `id_ak02` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  MODIFY `id_ak03` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_ak05`
--
ALTER TABLE `tb_ak05`
  MODIFY `id_ak5` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  MODIFY `id_apl1` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  MODIFY `id_apl2` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  MODIFY `id_asesi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  MODIFY `id_asesor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  MODIFY `id_ba` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  MODIFY `id_bd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_det_periode`
--
ALTER TABLE `tb_det_periode`
  MODIFY `id_det_periode` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  MODIFY `id_elemen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  MODIFY `id_ia01` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  MODIFY `id_ia06` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06a`
--
ALTER TABLE `tb_ia06a`
  MODIFY `id_ia06a` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  MODIFY `id_jawaban` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  MODIFY `id_isi_ba` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  MODIFY `id_isi_bd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  MODIFY `id_kuk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `tb_periode`
--
ALTER TABLE `tb_periode`
  MODIFY `id_periode` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  MODIFY `id_skema` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_soal`
--
ALTER TABLE `tb_soal`
  MODIFY `id_soal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  MODIFY `id_unit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tb_validator`
--
ALTER TABLE `tb_validator`
  MODIFY `id_validator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
-- Ketidakleluasaan untuk tabel `detail_ak5`
--
ALTER TABLE `detail_ak5`
  ADD CONSTRAINT `fk_detail5_ak5` FOREIGN KEY (`id_ak5`) REFERENCES `tb_ak05` (`id_ak5`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail5_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_detailia01_ia01` FOREIGN KEY (`id_ia01`) REFERENCES `tb_ia01` (`id_ia01`) ON DELETE CASCADE ON UPDATE CASCADE,
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
-- Ketidakleluasaan untuk tabel `tb_ak05`
--
ALTER TABLE `tb_ak05`
  ADD CONSTRAINT `fk_ak5_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak5_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  ADD CONSTRAINT `fk_apl1_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apl1_dp` FOREIGN KEY (`id_det_periode`) REFERENCES `tb_det_periode` (`id_det_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apl1_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `tb_jadwal` (`id_jadwal`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Ketidakleluasaan untuk tabel `tb_detail_user`
--
ALTER TABLE `tb_detail_user`
  ADD CONSTRAINT `fk_du_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_du_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_det_periode`
--
ALTER TABLE `tb_det_periode`
  ADD CONSTRAINT `fk_det_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_det_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_det_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Ketidakleluasaan untuk tabel `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD CONSTRAINT `fk_jadwal_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jadwal_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jadwal_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  ADD CONSTRAINT `fk_kuk_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  ADD CONSTRAINT `fk_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_user_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`id_admin`) REFERENCES `tb_admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
