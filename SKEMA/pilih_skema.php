<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Asesor') {
    $_SESSION['pesan'] = 'Akses ditolak';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

include '../koneksi.php';

$id_asesor = $_SESSION['id_asesor'] ?? 0;
if (!$id_asesor) {
    $_SESSION['pesan'] = 'Data asesor tidak ditemukan';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

$id_periode_session = $_SESSION['id_periode'] ?? 0;
if ($id_periode_session <= 0) {
    $_SESSION['pesan'] = 'Periode tidak aktif. Silakan login ulang.';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

$id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : (isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0);
if (!$id_skema) {
    $_SESSION['pesan'] = 'ID skema tidak valid';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

$cek_skema_periode = mysqli_query($koneksi, "
    SELECT id_skema FROM tb_skema
    WHERE id_skema = $id_skema AND id_periode = $id_periode_session
    LIMIT 1
");
if (!$cek_skema_periode || mysqli_num_rows($cek_skema_periode) === 0) {
    $_SESSION['pesan'] = 'Skema tidak tersedia pada periode aktif';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

$cek = mysqli_query($koneksi, "
    SELECT id_det_periode
    FROM tb_det_periode
    WHERE id_skema = $id_skema
      AND id_asesor = $id_asesor
      AND id_periode = $id_periode_session
    LIMIT 1
");
if ($cek && mysqli_num_rows($cek) > 0) {
    $_SESSION['pesan'] = 'Skema sudah pernah dipilih sebelumnya';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit;
}

$insert = mysqli_query($koneksi, "
    INSERT INTO tb_det_periode (id_asesor, id_skema, id_periode)
    VALUES ($id_asesor, $id_skema, $id_periode_session)
");
if ($insert) {
    $_SESSION['pesan'] = 'Skema berhasil dipilih';
    $_SESSION['tipe'] = 'success';
} else {
    $_SESSION['pesan'] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    $_SESSION['tipe'] = 'error';
}

header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
exit;
?>
