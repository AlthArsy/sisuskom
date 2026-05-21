<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$id = isset($_GET['id_kuk']) ? intval($_GET['id_kuk']) : 0;
$id_elemen = 0;


if ($id > 0) {
    mysqli_begin_transaction($koneksi);

    try {

        $query_elemen = "SELECT id_elemen From tb_kuk WHERE id_kuk = ?";
        $stmt_elemen = mysqli_prepare($koneksi, $query_elemen);
        mysqli_stmt_bind_param($stmt_elemen, 'i', $id);
        mysqli_stmt_execute($stmt_elemen);
        $result_elemen = mysqli_stmt_get_result($stmt_elemen);

        if ($row_elemen = mysqli_fetch_assoc($result_elemen)) {
            $id_elemen = intval($row_elemen['id_elemen']);
        }
        mysqli_stmt_close($stmt_elemen);

        $query_hapus_kuk = "DELETE FROM tb_kuk WHERE id_kuk = ?";
        $stmt_hapus_kuk = mysqli_prepare($koneksi, $query_hapus_kuk);
        mysqli_stmt_bind_param($stmt_hapus_kuk, 'i', $id);
        mysqli_stmt_execute($stmt_hapus_kuk);
        mysqli_stmt_close($stmt_hapus_kuk);

        mysqli_commit($koneksi);

        $_SESSION['pesan'] = "Kuk Berhasil Dihapus";
        $_SESSION['tipe'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan'] = "Gagal menghapus Kuk: " . $e->getMessage();
        $_SESSION['tipe'] = "error";
    }

} else {
    $_SESSION['pesan'] = "ID Kuk tidak valid!";
    $_SESSION['tipe'] = "error";
}
if ($id_elemen > 0) {
    header("Location: UTAMA.php?page=../KUK/KUK.php&id_elemen=" . $id_elemen);
}
exit();
?>
