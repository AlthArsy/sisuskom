<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp') {
    header("Location: ../LOGIN/login.php");
    exit;
}
include '../koneksi.php';

$id_periode = isset($_POST['id_periode']) ? intval($_POST['id_periode']) : (isset($_SESSION['id_periode']) ? intval($_SESSION['id_periode']) : 0);
$no_skema     = trim($_POST['no_skema'] ?? '');
$judul_skema  = trim($_POST['judul_skema'] ?? '');
$standar_comp = trim($_POST['standar_kompetensi'] ?? '');

if ($id_periode <= 0 || empty($no_skema) || empty($judul_skema)) {
    $_SESSION['pesan'] = "Data tidak lengkap. Periode, nomor skema, dan judul skema wajib diisi.";
    $_SESSION['tipe']  = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit;
}

$no_skema_esc = mysqli_real_escape_string($koneksi, $no_skema);
$judul_skema_esc = mysqli_real_escape_string($koneksi, $judul_skema);
$standar_comp_esc = mysqli_real_escape_string($koneksi, $standar_comp);

$cek = mysqli_query($koneksi, "
    SELECT s.id_skema
    FROM tb_skema s
    WHERE s.nomor_skema = '$no_skema_esc'
      AND s.id_periode = $id_periode
    LIMIT 1
");
if ($cek && mysqli_num_rows($cek) > 0) {
    $_SESSION['pesan'] = "Nomor skema sudah terdaftar untuk periode ini!";
    $_SESSION['tipe']  = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit;
}

$query = "INSERT INTO tb_skema (nomor_skema, judul_skema, standar_kompetensi_kerja, id_periode)
          VALUES ('$no_skema_esc', '$judul_skema_esc', '$standar_comp_esc', $id_periode)";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['pesan'] = "Skema berhasil ditambahkan!";
    $_SESSION['tipe']  = "success";
} else {
    $_SESSION['pesan'] = "Gagal menambahkan skema: " . mysqli_error($koneksi);
    $_SESSION['tipe']  = "error";
}

header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
exit;
?>
