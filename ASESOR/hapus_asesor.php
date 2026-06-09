<?php

if (session_status() === PHP_SESSION_NONE) {
session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_utm' && $_SESSION['role'] !== 'Admin_lsp') {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';
if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

if (isset($_GET['all']) && $_GET['all'] == '1') {
    if (!isset($_GET['confirm']) || $_GET['confirm'] != '1') {
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Konfirmasi Hapus Semua Asesor</title></head><body>';
        echo '<h2>Konfirmasi: Hapus Semua Data Asesor</h2>';
        echo '<p>Semua data pada tabel <strong>tb_asesor</strong> akan dihapus dan referensi pada tabel <strong>users</strong> dan <strong>tb_skema</strong> akan di-set NULL. Tindakan ini tidak dapat dibatalkan.</p>';
        echo '<p><a href="?all=1&confirm=1">Ya, hapus semua</a> &nbsp; <a href="../ASESOR/Table_asesor.php">Kembali</a></p>';
        echo '</body></html>';
        exit;
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID tidak valid.");
}

mysqli_begin_transaction($koneksi);
try {
    $stmt = mysqli_prepare($koneksi, "UPDATE tb_skema SET id_asesor = NULL WHERE id_asesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    $stmt = mysqli_prepare($koneksi, "DELETE FROM tb_asesor WHERE id_asesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_error($koneksi));
    }
    mysqli_stmt_close($stmt);

    mysqli_commit($koneksi);
    header("Location: ../BERANDA/UTAMA.php?page=../ASESOR/Table_asesor.php");
    exit;
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die("Gagal menghapus asesor: " . $e->getMessage());
}

mysqli_close($koneksi);
?>
