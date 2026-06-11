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
$standar_komp = trim($_POST['standar_kompetensi'] ?? '');

if ($id_periode <= 0 || empty($no_skema) || empty($judul_skema)) {
    $_SESSION['pesan'] = "Data tidak lengkap. Periode, nomor skema, dan judul skema wajib diisi.";
    $_SESSION['tipe']  = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit;
}

$cek = mysqli_query($koneksi, "SELECT id_skema FROM tb_skema WHERE nomor_skema = '$no_skema' AND id_periode = '$id_periode' LIMIT 1");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['pesan'] = "Nomor skema sudah terdaftar untuk periode ini!";
    $_SESSION['tipe']  = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit;
}

$query = "INSERT INTO tb_skema (id_periode, nomor_skema, judul_skema, standar_kompetensi_kerja) 
          VALUES ('$id_periode', '$no_skema', '$judul_skema', '$standar_komp')";
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