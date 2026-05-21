
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `detail_ak1` (
  `id_detail_ak1` int(11) NOT NULL,
  `id_ak01` int(11) NOT NULL,
  `bukti` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `detail_ak1`
--

INSERT INTO `detail_ak1` (`id_detail_ak1`, `id_ak01`, `bukti`) VALUES
(1, 2, 'Hasil Verifikasi Portofolio, Hasil Observasi Langsung, Hasil Tanya Jawab');

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
(1, 1, 21, 46, 35, 41, 'K'),
(2, 1, 21, 46, 36, 43, 'K'),
(3, 1, 21, 47, 37, 45, 'BK'),
(4, 1, 21, 47, 38, 47, 'BK'),
(5, 1, 21, 47, 39, 49, 'BK'),
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
(26, 4, 23, 56, 48, 59, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_ia01`
--

CREATE TABLE `detail_ia01` (
  `id_detail_ia1` int(11) NOT NULL,
  `id_ia01` int(11) NOT NULL,
  `standar_industri` varchar(100) NOT NULL,
  `pencapaian` varchar(100) NOT NULL,
  `Penilaian Lanjut` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) NOT NULL,
  `nik` varchar(50) NOT NULL,
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
  `id_asesi` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `tuk_pelaksanaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.01 Persetujuan Asesmen dan Kerahasiaan';

--
-- Dumping data untuk tabel `tb_ak01`
--

INSERT INTO `tb_ak01` (`id_ak01`, `id_asesi`, `id_skema`, `id_asesor`, `tuk`, `hari_tanggal`, `waktu`, `tuk_pelaksanaan`) VALUES
(2, 1, 21, 1, 'Sewaktu', '2026-05-12', '11:02', 'SMK');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak02`
--

CREATE TABLE `tb_ak02` (
  `id_ak02` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `komentar_asesor` text DEFAULT NULL,
  `nama_asesi` varchar(255) DEFAULT NULL,
  `tanggal_asesi` date DEFAULT NULL,
  `ttd_asesi_qr` text DEFAULT NULL,
  `nama_asesor` varchar(255) DEFAULT NULL,
  `no_reg_asesor` varchar(100) DEFAULT NULL,
  `tanggal_asesor` date DEFAULT NULL,
  `ttd_asesor_qr` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.02 Header Rekaman Asesmen Kompetensi';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ak03`
--

CREATE TABLE `tb_ak03` (
  `id_ak03` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `catatan_lainnya` text DEFAULT NULL,
  `nama_asesi` varchar(255) DEFAULT NULL,
  `tanggal_asesi` date DEFAULT NULL,
  `ttd_asesi_qr` text DEFAULT NULL,
  `nama_asesor` varchar(255) DEFAULT NULL,
  `no_reg_asesor` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.AK.03 Header Umpan Balik dan Catatan Asesmen';

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
  `qr_data` text DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `rekomendasi` enum('Diterima','Tidak Diterima') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_apl1`
--

INSERT INTO `tb_apl1` (`id_apl1`, `id_skema`, `id_asesi`, `judul_skema`, `nomor_skema`, `tujuan_asesmen`, `tujuan_lainnya`, `nama_pemohon`, `tanggal_pemohon`, `qr_data`, `catatan_admin`, `rekomendasi`) VALUES
(1, 21, 8, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Zee', '2026-04-30', 'LSP MUDIKAL | APL1B2 | ID_ASESI:- | ID_SKEMA:21 | NAMA:Zee | SKEMA:Pemrogram Junior | ADMIN_LSP:Ryan | TGL:2026-04-30 | GEN:2026-04-22 01:36:31', 'semangga', 'Diterima'),
(2, 21, 12, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Nadya', '2026-04-27', 'LSP MUDIKAL | APL1B2 | ID_ASESI:- | ID_SKEMA:21 | NAMA:Nadya | SKEMA:Pemrogram Junior | ADMIN_LSP:Ryan | TGL:2026-04-27 | GEN:2026-04-27 03:08:29', 'KERJA BaGUS', 'Diterima'),
(3, 21, 17, 'Pemrogram Junior', '1', 'Sertifikasi', '', 'Zee', '2026-04-08', 'LSP MUDIKAL | APL1B2 | ID_ASESI:- | ID_SKEMA:21 | NAMA:Zee | SKEMA:Pemrogram Junior | ADMIN_LSP:Ryan | TGL:2026-04-08 | GEN:2026-04-28 04:25:30', 'bgau', 'Tidak Diterima'),
(4, 23, 16, 'MANAJEMEN', '1', 'Rekognisi Pembelajaran Lampau (RPL)', '', 'Zee', '2026-05-13', 'LSP MUDIKAL | APL1B2 | ID_ASESI:- | ID_SKEMA:23 | NAMA:Zee | SKEMA:MANAJEMEN | ADMIN_LSP:Ryan | TGL:2026-05-13 | GEN:2026-05-08 09:52:45', NULL, 'Tidak Diterima'),
(5, 21, 13, 'Pemrogram Junior', '1', 'Pengakuan Kompetensi Terkini (PKT)', '', 'ABC', '2026-05-08', 'LSP MUDIKAL | APL1B2 | ID_ASESI:- | ID_SKEMA:21 | NAMA:ABC | SKEMA:Pemrogram Junior | ADMIN_LSP:Ryan | TGL:2026-05-08 | GEN:2026-05-08 12:14:50', NULL, NULL);

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
(1, 1, 8, 1, 'Dapat', 'Zee'),
(2, 3, 17, 1, 'Tidak Dapat', 'xammmpee'),
(3, 2, 12, 1, 'Tidak Dapat', 'Nadya'),
(4, 4, 16, 2, NULL, 'ZEE');

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
(18, 'yah', '123', 'Laki-laki', 'WNI', 'wwwww', '123', '123134341331', '32132132121', '32132132121', 'cika@gmail.com', 'pancasila', 'mika', 'mc', 'manado', '3221', '21323123213', 'feks', 'cika@gmail.com');

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
  `id_skema` int(11) NOT NULL,
  `bukti_adm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_bukti_adm`
--

INSERT INTO `tb_bukti_adm` (`id_ba`, `id_skema`, `bukti_adm`) VALUES
(1, 21, 'Foto Kopi KTP/Kartu Pelajar'),
(2, 21, 'Pas foto 3x4 2 lembar dengan backgroud merah'),
(3, 23, '123Foto Kopi KTP/Kartu Pelajar'),
(4, 23, '123Pas foto 3x4 2 lembar dengan backgroud merah');

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
(2, 21, 'Copy sertifikat/surat keterangan Praktik Kerja Lapangan\n(PKL) pada rekayasa perangkat lunak'),
(3, 23, '123Copy Raport SMK pada Konsentrasi Keahlian Rekayasa\nPerangkat Lunak semester 1 s.d 5 yang telah\nmenyelesaikan mata pelajaran berisi unit kompetensi\nyang diajukan'),
(4, 23, '123Copy sertifikat/surat keterangan Praktik Kerja Lapangan\n(PKL) pada rekayasa perangkat lunak');

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
(48, 56, '1', '2'),
(49, 57, '1', 'mengidentifikasi kenapa orang bisa bodoh');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia01`
--

CREATE TABLE `tb_ia01` (
  `id_ia01` int(11) NOT NULL,
  `id_apl1` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `id_elemen` int(11) NOT NULL,
  `id_kuk` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `rekomendasi` enum('Kompeten','Belum Kompeten') DEFAULT NULL,
  `umpan_balik` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.01 Ceklis Observasi Aktivitas Tempat Kerja - Header';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06`
--

CREATE TABLE `tb_ia06` (
  `id_ia06` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `tuk` enum('Sewaktu','Tempat Kerja','Mandiri') NOT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` varchar(20) DEFAULT NULL,
  `umpan_balik` text DEFAULT NULL,
  `rekomendasi` enum('Tercapai','Belum Tercapai') DEFAULT NULL,
  `rek_detail` text DEFAULT NULL COMMENT 'Unit/elemen/KUK jika belum tercapai',
  `nama_asesi` varchar(255) DEFAULT NULL,
  `tanggal_asesi` date DEFAULT NULL,
  `ttd_asesi_qr` text DEFAULT NULL,
  `nama_asesor` varchar(255) DEFAULT NULL,
  `no_reg_asesor` varchar(100) DEFAULT NULL,
  `tanggal_asesor` date DEFAULT NULL,
  `ttd_asesor_qr` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Header sesi jawaban asesi';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06_jawaban`
--

CREATE TABLE `tb_ia06_jawaban` (
  `id_jawaban` int(11) NOT NULL,
  `id_asesi` int(11) NOT NULL,
  `id_ia06` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `jawaban_asesi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06C - Jawaban asesi per soal';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06_penyusun`
--

CREATE TABLE `tb_ia06_penyusun` (
  `id_penyusun` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `status` enum('Penyusun','Validator') NOT NULL,
  `no` tinyint(4) NOT NULL DEFAULT 1,
  `nama` varchar(255) DEFAULT NULL,
  `no_met` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `ttd_qr` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06A - Data penyusun dan validator soal';

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ia06_soal`
--

CREATE TABLE `tb_ia06_soal` (
  `id_soal` int(11) NOT NULL,
  `id_skema` int(11) NOT NULL,
  `no_soal` tinyint(4) NOT NULL COMMENT 'Nomor urut soal',
  `pertanyaan` text NOT NULL COMMENT 'Teks pertanyaan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FR.IA.06A - Master soal pertanyaan tertulis esai per skema';

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
(11, 3, 'Memenuhi Syarat', 16),
(12, 4, 'Memenuhi Syarat', 16),
(13, 1, 'Memenuhi Syarat', 13),
(14, 2, 'Memenuhi Syarat', 13);

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
(11, 3, 'Memenuhi Syarat', 16),
(12, 4, 'Memenuhi Syarat', 16),
(13, 1, 'Tidak Memenuhi Syarat', 13),
(14, 2, 'Tidak Memenuhi Syarat', 13);

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
(56, 23, '1', 'MAKANan'),
(57, 23, '2', 'Baka');

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
(3, 'asesor2', 'Admin1234', 'Asesor', NULL, 2, NULL),
(4, 'assesi1', 'Admin1234', 'Asesi', NULL, NULL, 1),
(23, 'assesi2', 'Admin1234', 'Asesi', NULL, NULL, NULL),
(27, 'Zee', '123456', 'Admin_lsp', NULL, NULL, NULL),
(30, 'althaf', 'ojorapak', 'Asesi', NULL, NULL, 8),
(32, 'ACE', '123', 'Asesi', NULL, NULL, 13),
(33, 'ASEPP', '123456', 'Asesi', NULL, NULL, 12),
(34, 'anda', '1', 'Asesi', NULL, NULL, 16),
(35, 'Ash', 'Admin1234', 'Asesi', NULL, NULL, 17),
(36, 'yah', '123', 'Asesi', NULL, NULL, 18);

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
-- Indeks untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD PRIMARY KEY (`id_detail_ak1`),
  ADD KEY `idx_detail_ak1_ak01` (`id_ak01`);

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
  ADD KEY `fx_detailia01_ia01` (`id_ia01`);

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
  ADD KEY `idx_ak01_asesi` (`id_asesi`),
  ADD KEY `idx_ak01_skema` (`id_skema`),
  ADD KEY `fk_ak01_asesor` (`id_asesor`);

--
-- Indeks untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  ADD PRIMARY KEY (`id_ak02`),
  ADD KEY `idx_ak02_asesi` (`id_asesi`),
  ADD KEY `idx_ak02_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  ADD PRIMARY KEY (`id_ak03`),
  ADD KEY `idx_ak03_asesi` (`id_asesi`),
  ADD KEY `idx_ak03_skema` (`id_skema`);

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
  ADD KEY `idx_ia01_asesi` (`id_asesi`),
  ADD KEY `idx_ia01_skema` (`id_skema`),
  ADD KEY `fx_ia01_unit` (`id_unit`),
  ADD KEY `fx_ia01_elemen` (`id_elemen`),
  ADD KEY `fx_ia01_kuk` (`id_kuk`),
  ADD KEY `fx_ia01_apl1` (`id_apl1`);

--
-- Indeks untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  ADD PRIMARY KEY (`id_ia06`),
  ADD KEY `idx_ia06_asesi` (`id_asesi`),
  ADD KEY `idx_ia06_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD KEY `idx_jawaban_ia06` (`id_ia06`),
  ADD KEY `idx_jawaban_soal` (`id_soal`),
  ADD KEY `fk_ia06_jawaban_asesi` (`id_asesi`);

--
-- Indeks untuk tabel `tb_ia06_penyusun`
--
ALTER TABLE `tb_ia06_penyusun`
  ADD PRIMARY KEY (`id_penyusun`),
  ADD KEY `idx_penyusun_skema` (`id_skema`);

--
-- Indeks untuk tabel `tb_ia06_soal`
--
ALTER TABLE `tb_ia06_soal`
  ADD PRIMARY KEY (`id_soal`),
  ADD KEY `idx_soal_skema` (`id_skema`);

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
-- AUTO_INCREMENT untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  MODIFY `id_detail_ak1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `detail_apl2`
--
ALTER TABLE `detail_apl2`
  MODIFY `id_detail_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `detail_ia01`
--
ALTER TABLE `detail_ia01`
  MODIFY `id_detail_ia1` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  MODIFY `id_ak01` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  MODIFY `id_ak02` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  MODIFY `id_ak03` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_apl1`
--
ALTER TABLE `tb_apl1`
  MODIFY `id_apl1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_apl2`
--
ALTER TABLE `tb_apl2`
  MODIFY `id_apl2` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_asesi`
--
ALTER TABLE `tb_asesi`
  MODIFY `id_asesi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `tb_asesor`
--
ALTER TABLE `tb_asesor`
  MODIFY `id_asesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_adm`
--
ALTER TABLE `tb_bukti_adm`
  MODIFY `id_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_bukti_dasar`
--
ALTER TABLE `tb_bukti_dasar`
  MODIFY `id_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_elemen`
--
ALTER TABLE `tb_elemen`
  MODIFY `id_elemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  MODIFY `id_ia01` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  MODIFY `id_ia06` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_penyusun`
--
ALTER TABLE `tb_ia06_penyusun`
  MODIFY `id_penyusun` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_ia06_soal`
--
ALTER TABLE `tb_ia06_soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_adm`
--
ALTER TABLE `tb_isi_bukti_adm`
  MODIFY `id_isi_ba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tb_isi_bukti_dasar`
--
ALTER TABLE `tb_isi_bukti_dasar`
  MODIFY `id_isi_bd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_ak1`
--
ALTER TABLE `detail_ak1`
  ADD CONSTRAINT `fk_detail_ak1_ak01` FOREIGN KEY (`id_ak01`) REFERENCES `tb_ak01` (`id_ak01`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Ketidakleluasaan untuk tabel `tb_ak01`
--
ALTER TABLE `tb_ak01`
  ADD CONSTRAINT `fk_ak01_apl1` FOREIGN KEY (`id_asesi`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak01_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ak02`
--
ALTER TABLE `tb_ak02`
  ADD CONSTRAINT `fk_ak02_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak02_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ak03`
--
ALTER TABLE `tb_ak03`
  ADD CONSTRAINT `fk_ak03_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ak03_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_elemen_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia01`
--
ALTER TABLE `tb_ia01`
  ADD CONSTRAINT `fk_ia01_apl1` FOREIGN KEY (`id_apl1`) REFERENCES `tb_apl1` (`id_apl1`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia01_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia01_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ia01_kuk` FOREIGN KEY (`id_kuk`) REFERENCES `tb_kuk` (`id_kuk`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ia01_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia01_unit` FOREIGN KEY (`id_unit`) REFERENCES `tb_unit_kompetensi` (`id_unit`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06`
--
ALTER TABLE `tb_ia06`
  ADD CONSTRAINT `fk_ia06_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia06_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06_jawaban`
--
ALTER TABLE `tb_ia06_jawaban`
  ADD CONSTRAINT `fk_ia06_jawaban_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jawaban_ia06` FOREIGN KEY (`id_ia06`) REFERENCES `tb_ia06` (`id_ia06`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jawaban_soal06` FOREIGN KEY (`id_soal`) REFERENCES `tb_ia06_soal` (`id_soal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06_penyusun`
--
ALTER TABLE `tb_ia06_penyusun`
  ADD CONSTRAINT `fk_penyusun_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_ia06_soal`
--
ALTER TABLE `tb_ia06_soal`
  ADD CONSTRAINT `fk_soal_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE ON UPDATE CASCADE;

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

ALTER TABLE `tb_kuk`
  ADD CONSTRAINT `fk_kuk_elemen` FOREIGN KEY (`id_elemen`) REFERENCES `tb_elemen` (`id_elemen`) ON DELETE CASCADE;

ALTER TABLE `tb_skema`
  ADD CONSTRAINT `fk_skema_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`);

ALTER TABLE `tb_unit_kompetensi`
  ADD CONSTRAINT `fk_unit_skema` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_unit_kompetensi_ibfk_1` FOREIGN KEY (`id_skema`) REFERENCES `tb_skema` (`id_skema`) ON DELETE CASCADE;

--

--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`id_admin`) REFERENCES `tb_admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesi` FOREIGN KEY (`id_asesi`) REFERENCES `tb_asesi` (`id_asesi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_asesor` FOREIGN KEY (`id_asesor`) REFERENCES `tb_asesor` (`id_asesor`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;
