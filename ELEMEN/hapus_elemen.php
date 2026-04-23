<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$id = isset($_GET['id_elemen']) ? intval($_GET['id_elemen']) : 0;
$id_unit = 0;


if ($id > 0) {
    mysqli_begin_transaction($koneksi);
    
    try {

        $query_skema = "SELECT id_unit From tb_elemen WHERE id_elemen = ?";
        $stmt_skema = mysqli_prepare($koneksi, $query_skema);
        mysqli_stmt_bind_param($stmt_skema, 'i', $id);
        mysqli_stmt_execute($stmt_skema);
        $result_skema = mysqli_stmt_get_result($stmt_skema);

        if ($row_skema = mysqli_fetch_assoc($result_skema)) {
            $id_unit = intval($row_skema['id_unit']);
        }
        mysqli_stmt_close($stmt_skema);

        $query_kuk = "SELECT id_kuk FROM tb_kuk WHERE id_elemen = ?";
        $stmt_kuk = mysqli_prepare($koneksi, $query_kuk);
        mysqli_stmt_bind_param($stmt_kuk, 'i', $id);
        mysqli_stmt_execute($stmt_kuk);
        $result_kuk = mysqli_stmt_get_result($stmt_kuk);
        
        $kuk_ids = [];
        while ($row = mysqli_fetch_assoc($result_kuk)) {
            $kuk_ids[] = $row['id_kuk'];
        }
        mysqli_stmt_close($stmt_kuk);
        
        $total_kuk = count($kuk_ids);
        
        
        $query_hapus_elemen = "DELETE FROM tb_elemen WHERE id_elemen = ?";
        $stmt_hapus_elemen = mysqli_prepare($koneksi, $query_hapus_elemen);
        mysqli_stmt_bind_param($stmt_hapus_elemen, 'i', $id);
        mysqli_stmt_execute($stmt_hapus_elemen);
        mysqli_stmt_close($stmt_hapus_elemen);
        
        mysqli_commit($koneksi);
        
        $_SESSION['pesan'] = "Elemen Berhasil Dihapus Beserta Kuk";
        $_SESSION['tipe'] = "success";
        
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan'] = "Gagal menghapus Elemen: " . $e->getMessage();
        $_SESSION['tipe'] = "error";
    }
    
} else {
    $_SESSION['pesan'] = "ID Elemen tidak valid!";
    $_SESSION['tipe'] = "error";
}
if ($id_unit > 0) {
    header("Location: UTAMA.php?page=../ELEMEN/elemen.php&id_unit=" . $id_unit);
}
exit();
?>