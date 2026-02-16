<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . "/../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Asesor') {
    die("Akses ditolak! Hanya Asesor yang bisa menambah skema.");
}

if (!isset($_SESSION['id_asesor']) || empty($_SESSION['id_asesor'])) {
    $username = $_SESSION['username'];
    $query_asesor = "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = ?";
    $stmt_asesor = mysqli_prepare($koneksi, $query_asesor);
    mysqli_stmt_bind_param($stmt_asesor, "s", $username);
    mysqli_stmt_execute($stmt_asesor);
    $result_asesor = mysqli_stmt_get_result($stmt_asesor);
    
    if ($row = mysqli_fetch_assoc($result_asesor)) {
        $_SESSION['id_asesor'] = $row['id_asesor'];
    } else {
        die("Data asesor tidak ditemukan. Silakan lengkapi profil terlebih dahulu.");
    }
    mysqli_stmt_close($stmt_asesor);
}

$id_asesor = (int)$_SESSION['id_asesor'];

$check_asesor = "SELECT id_asesor FROM tb_asesor WHERE id_asesor = ?";
$stmt_check = mysqli_prepare($koneksi, $check_asesor);
mysqli_stmt_bind_param($stmt_check, "i", $id_asesor);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) === 0) {
    mysqli_stmt_close($stmt_check);
    die("ID Asesor tidak valid. Silakan hubungi administrator.");
}
mysqli_stmt_close($stmt_check);

$no_skema  = trim($_POST['no_skema'] ?? '');
$judul     = trim($_POST['judul_skema'] ?? '');
$standar   = trim($_POST['standar_kompetensi'] ?? '');

if ($no_skema === '' || $judul === '' || $standar === '') {
    die("Input tidak lengkap. Semua field harus diisi.");
}

$check_duplicate = "SELECT id_skema FROM tb_skema WHERE nomor_skema = ? AND id_asesor = ?";
$stmt_dup = mysqli_prepare($koneksi, $check_duplicate);
mysqli_stmt_bind_param($stmt_dup, "si", $no_skema, $id_asesor);
mysqli_stmt_execute($stmt_dup);
$result_dup = mysqli_stmt_get_result($stmt_dup);

if (mysqli_num_rows($result_dup) > 0) {
    mysqli_stmt_close($stmt_dup);
    $_SESSION['pesan'] = "Nomor skema '$no_skema' sudah ada! Gunakan nomor lain.";
    $_SESSION['tipe'] = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit();
}
mysqli_stmt_close($stmt_dup);

$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO tb_skema (nomor_skema, judul_skema, standar_kompetensi_kerja, id_asesor)
     VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    die("Gagal prepare: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "sssi", $no_skema, $judul, $standar, $id_asesor);
$query = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($query) {
    $_SESSION['pesan'] = "Skema berhasil ditambahkan!";
    $_SESSION['tipe'] = "success";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
    exit();
} else {
    $_SESSION['pesan'] = "Gagal menyimpan skema: " . mysqli_error($koneksi);
    $_SESSION['tipe'] = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/Form_Skema.php");
    exit();
}
?>