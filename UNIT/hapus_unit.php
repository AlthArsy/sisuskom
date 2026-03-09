<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$id = isset($_GET['id_unit']) ? intval($_GET['id_unit']) : 0;
$id_skema = 0;


if ($id > 0) {
    mysqli_begin_transaction($koneksi);
    
    try {

        $query_skema = "SELECT id_skema From tb_unit_kompetensi WHERE id_unit = ?";
        $stmt_skema = mysqli_prepare($koneksi, $query_skema);
        mysqli_stmt_bind_param($stmt_skema, 'i', $id);
        mysqli_stmt_execute($stmt_skema);
        $result_skema = mysqli_stmt_get_result($stmt_skema);

        if ($row_skema = mysqli_fetch_assoc($result_skema)) {
            $id_skema = intval($row_skema['id_skema']);
        }
        mysqli_stmt_close($stmt_skema);

        $query_units = "SELECT id_elemen FROM tb_elemen WHERE id_unit = ?";
        $stmt_units = mysqli_prepare($koneksi, $query_units);
        mysqli_stmt_bind_param($stmt_units, 'i', $id);
        mysqli_stmt_execute($stmt_units);
        $result_units = mysqli_stmt_get_result($stmt_units);
        
        $elemen_ids = [];
        while ($row = mysqli_fetch_assoc($result_units)) {
            $elemen_ids[] = $row['id_elemen'];
        }
        mysqli_stmt_close($stmt_units);
        
        $total_elemen = count($elemen_ids);
        $total_kuk = 0;
        
        if ($total_elemen > 0) {
            $elemen_ids_str = implode(',', $elemen_ids);
            
            $query_kuk = "SELECT id_elemen FROM tb_kuk WHERE id_kuk IN ($elemen_ids_str)";
            $result_kuk = mysqli_query($koneksi, $query_kuk);
            
            $kuk_ids = [];
            while ($row = mysqli_fetch_assoc($result_kuk)) {
                $kuk_ids[] = $row['id_kuk'];
            }
                  
            $query_hapus_kuk = "DELETE FROM tb_kuk WHERE id_elemen IN ($elemen_ids_str)";
            mysqli_query($koneksi, $query_hapus_kuk);
            

            $query_hapus_elemen = "DELETE FROM tb_elemen WHERE id_unit = ?";
            $stmt_hapus_elemen = mysqli_prepare($koneksi, $query_hapus_elemen);
            mysqli_stmt_bind_param($stmt_hapus_elemen, 'i', $id);
            mysqli_stmt_execute($stmt_hapus_elemen);
            mysqli_stmt_close($stmt_hapus_elemen);
        }
        
        $query_hapus_unit = "DELETE FROM tb_unit_kompetensi WHERE id_unit = ?";
        $stmt_hapus_unit = mysqli_prepare($koneksi, $query_hapus_unit);
        mysqli_stmt_bind_param($stmt_hapus_unit, 'i', $id);
        mysqli_stmt_execute($stmt_hapus_unit);
        mysqli_stmt_close($stmt_hapus_unit);
        
        mysqli_commit($koneksi);
        
        $_SESSION['pesan'] = "Unit Berhasil Dihapus Beserta Elemen Dan Kuk";
        $_SESSION['tipe'] = "success";
        
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan'] = "Gagal menghapus Unit: " . $e->getMessage();
        $_SESSION['tipe'] = "error";
    }
    
} else {
    $_SESSION['pesan'] = "ID Unit tidak valid!";
    $_SESSION['tipe'] = "error";
}
if ($id_skema > 0) {
    header("Location: UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=" . $id_skema);
}
exit();
?>