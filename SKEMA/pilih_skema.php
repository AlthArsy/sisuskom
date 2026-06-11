<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Asesor') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

include '../koneksi.php';

$id_asesor = $_SESSION['id_asesor'] ?? 0;
if (!$id_asesor) {
    echo json_encode(['status' => 'error', 'message' => 'Data asesor tidak ditemukan']);
    exit;
}


$id_periode_session = $_SESSION['id_periode'] ?? 0;
if ($id_periode_session <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Periode tidak aktif. Silakan login ulang.']);
    exit;
}

$id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
if (!$id_skema) {
    echo json_encode(['status' => 'error', 'message' => 'ID skema tidak valid']);
    exit;
}


$cek_periode = mysqli_query($koneksi, "SELECT id_skema FROM tb_skema WHERE id_skema = $id_skema AND id_periode = $id_periode_session LIMIT 1");
if (!$cek_periode || mysqli_num_rows($cek_periode) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Skema tidak tersedia pada periode aktif Anda.']);
    exit;
}

$cek_tabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_skema_asesor'");
if (mysqli_num_rows($cek_tabel) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Tabel tb_skema_asesor belum dibuat. Hubungi admin.']);
    exit;
}


$cek = mysqli_query($koneksi, "SELECT id FROM tb_skema_asesor WHERE id_skema = $id_skema AND id_asesor = $id_asesor");
if ($cek && mysqli_num_rows($cek) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Skema sudah pernah dipilih sebelumnya']);
    exit;
}


$insert = mysqli_query($koneksi, "INSERT INTO tb_skema_asesor (id_skema, id_asesor) VALUES ($id_skema, $id_asesor)");
if ($insert) {
    echo json_encode(['status' => 'success', 'message' => 'Skema berhasil dipilih']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)]);
}
?>