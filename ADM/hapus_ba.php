<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

$id_ba = intval($_GET['id_ba'] ?? 0);
$id_skema = intval($_GET['id_skema'] ?? 0);

if ($id_ba <= 0) {
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT id_skema FROM tb_bukti_adm WHERE id_ba = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id_ba);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$r = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$r) {
    $_SESSION['pesan'] = 'Data tidak ditemukan.';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

if ($id_skema <= 0) {
    $id_skema = (int) $r['id_skema'];
}

mysqli_begin_transaction($koneksi);
$ok = true;
$d1 = mysqli_prepare($koneksi, "DELETE FROM tb_isi_bukti_adm WHERE id_ba = ?");
mysqli_stmt_bind_param($d1, "i", $id_ba);
if (!mysqli_stmt_execute($d1)) {
    $ok = false;
}
mysqli_stmt_close($d1);

if ($ok) {
    $d2 = mysqli_prepare($koneksi, "DELETE FROM tb_bukti_adm WHERE id_ba = ?");
    mysqli_stmt_bind_param($d2, "i", $id_ba);
    if (!mysqli_stmt_execute($d2)) {
        $ok = false;
    }
    mysqli_stmt_close($d2);
}

if ($ok) {
    mysqli_commit($koneksi);
    $_SESSION['pesan'] = 'Bukti Adminsitrasi berhasil dihapus.';
    $_SESSION['tipe'] = 'success';
} else {
    mysqli_rollback($koneksi);
    $_SESSION['pesan'] = 'Gagal menghapus: ' . mysqli_error($koneksi);
    $_SESSION['tipe'] = 'error';
}

$redir = '../BERANDA/UTAMA.php?page=../ADM/bukti_adm.php&id_skema=' . $id_skema;
header('Location: ' . $redir);
exit();
