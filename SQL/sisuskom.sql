

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `Tester`
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak1`
--

CREATE TABLE `detail_ak1` (
  `id_detail_ak1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `bukti` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ak5`
--

CREATE TABLE `detail_ak5` (
  `id_detail_ak5` int(11) NOT NULL,
  `id_ak5` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `rekomend` enum('K','BK') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` varchar(50) DEFAULT NULL,
  `aspek` varchar(255) DEFAULT NULL,
  `pencatatan` varchar(255) DEFAULT NULL,
  `saran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(1, '1928984182218738', 'Toyota Silvina');

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak05`
--

CREATE TABLE `tb_ak05` (
  `id_ak5` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_apl1`
--

CREATE TABLE `tb_apl1` (
  `id_apl1` int(11) NOT NULL,
  `id_det_periode` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `tujuan_asesmen` varchar(100) NOT NULL,
  `tujuan_lainnya` text DEFAULT NULL,
  `nama_pemohon` varchar(100) NOT NULL,
  `tanggal_pemohon` date NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `rekomendasi` enum('Diterima','Tidak Diterima') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(1, 'YANG BACA INI FIKS TRAKTIR', '1111111111111111', 'Perempuan', 'WNI', 'Java Center', '122132', '023984923743434', '023845297389729', '093284234234234', 'wtetweterrytiu@gmail.com', 'uiwqerutwuertweweritwerrtwueirt', 'qrutiutiurwrtiutwiuerrtuiweyri', 'iwtwrutuiqwtruiqt', 'qwutiuqwrirtuqiruiqtqruiqrqrqrwrqwrq', '123123', '023974873289478', '021612341626916', 'wtetweterryweqtiu@gmail.com');

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
(1, '127388172983798172397198239873', 'Dr.Konanne', 'Laki-laki', 'Jalan Anime23');

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
(1, 1, 'YANG BACA INI FIKS TRAKTIR'),
(2, 1, 'YANG BACA INI FIKS TRAKTIR1');

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
(1, 1, 'BUKTI APA'),
(2, 1, 'BUKTI APA1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_det_periode`
--

CREATE TABLE `tb_det_periode` (
  `id_det_periode` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_periode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(1, 1, '1', 'ESENCRTAL'),
(2, 1, '2', 'YANG BACA INI FIKS TRAKTIR');

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
  `belum_kompeten` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.01 Ceklis Observasi Aktivitas Tempat Kerja - Header';

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
(1, 1, 1, 1);

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
(1, 1, 'Tidak Memenuhi Syarat', 1),
(2, 2, 'Memenuhi Syarat', 1);

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
(1, 1, 'Tidak Memenuhi Syarat', 1),
(2, 2, 'Memenuhi Syarat', 1);

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
(1, 1, '1', 'GUBakuk'),
(2, 1, '2', 'BULUKUK'),
(3, 1, '3', 'GUGYEA'),
(4, 1, '4', 'ES TEH'),
(5, 2, '1', 'YANG BACA INI FIKS TRAKTIR'),
(6, 2, '2', 'YANG BACA INI FIKS TRAKTIR'),
(7, 2, '3', 'YANG BACA INI FIKS TRAKTIR'),
(8, 2, '4', 'YANG BACA INI FIKS TRAKTIR');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_periode`
--

CREATE TABLE `tb_periode` (
  `id_periode` int(11) NOT NULL,
  `tahun_ajaran` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_periode`
--

INSERT INTO `tb_periode` (`id_periode`, `tahun_ajaran`) VALUES
(1, '2025 / 2026 (genap)'),
(2, '2024 / 2025 (dansal)'),
(3, '2023 / 2024 (Genap)'),
(4, '2026 / 2027 (Gasal)');

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
(1, '1', 'Standar Digital', 'OKLUPUASI X HunterXHunter', 1);

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
(1, 1, 'YANG BACA INI FIKS TRAKTIR', 'YANG BACA INI FIKS TRAKTIR');

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
(1, 1, '1', 'UNIKAL');

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
(1, 'Baginda', '019230128038720973977293710723');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin_lsp','Asesor','Asesi') NOT NULL,
  `id_periode` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_asesor` int(11) DEFAULT NULL,
  `id_asesi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `id_periode`, `id_admin`, `id_asesor`, `id_asesi`) VALUES
(1, 'Althaf', 'c4ca4238a0b923820dcc509a6f75849b', 'Asesi', NULL, NULL, NULL, 1),
(3, 'Skyline lsp', '0ce9e2a07ebe3678ed75c8bbb1f63d04', 'Admin_lsp', NULL, 1, NULL, NULL),
(4, 'Sifa', '700933e83f12b73e3f23a8d6fde3fa2c', 'Asesor', NULL, NULL, NULL, NULL),
(7, 'Bunga', 'bcb48dddff8c14b5f452ee573b4db770', 'Asesi', NULL, NULL, NULL, NULL);

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
(1, 'Admin Mudikal', '751cb3f4aa17c36186f4856c8982bf27', 'Admin_utm');

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
  ADD KEY `id_apl1_dp` (`id_det_periode`);

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
  MODIFY `id_detail_ak02` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  MODIFY `id_detail_ak1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `detail_ak5`
--
ALTER TABLE `detail_ak5`
  MODIFY `id_detail_ak5` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  MODIFY `id_detail_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  MODIFY `id_detail_ia1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `hasil_ak03`
--
ALTER TABLE `hasil_ak03`
  MODIFY `id_detail_ak03` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  MODIFY `id_ak01` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  MODIFY `id_ak02` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  MODIFY `id_ak03` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ak05`
--
ALTER TABLE `tb_ak05`
  MODIFY `id_ak5` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  MODIFY `id_apl1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  MODIFY `id_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  MODIFY `id_asesi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  MODIFY `id_asesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  MODIFY `id_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  MODIFY `id_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_det_periode`
--
ALTER TABLE `tb_det_periode`
  MODIFY `id_det_periode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  MODIFY `id_elemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  MODIFY `id_ia01` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  MODIFY `id_ia06` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06a`
--
ALTER TABLE `tb_ia06a`
  MODIFY `id_ia06a` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  MODIFY `id_isi_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  MODIFY `id_isi_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_kuk`
--
ALTER TABLE `tb_kuk`
  MODIFY `id_kuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_periode`
--
ALTER TABLE `tb_periode`
  MODIFY `id_periode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_skema`
--
ALTER TABLE `tb_skema`
  MODIFY `id_skema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_soal`
--
ALTER TABLE `tb_soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_unit_kompetensi`
--
ALTER TABLE `tb_unit_kompetensi`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_validator`
--
ALTER TABLE `tb_validator`
  MODIFY `id_validator` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `fk_apl1_dp` FOREIGN KEY (`id_det_periode`) REFERENCES `tb_det_periode` (`id_det_periode`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_user_periode` FOREIGN KEY (`id_periode`) REFERENCES `tb_periode` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`id_admin`) REFERENCES `tb_admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;