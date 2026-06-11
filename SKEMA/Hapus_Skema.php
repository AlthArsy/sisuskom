<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$role = $_SESSION['role'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    $_SESSION['pesan'] = "ID skema tidak valid!";
    $_SESSION['tipe'] = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
    exit();
}


if ($role === 'Asesor') {
    $_SESSION['pesan'] = "Anda tidak memiliki akses untuk menghapus skema!";
    $_SESSION['tipe'] = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
    exit();
}


if ($role === 'Admin_lsp') {
    $id_periode_session = isset($_SESSION['id_periode']) ? intval($_SESSION['id_periode']) : 0;
    if ($id_periode_session <= 0) {
        $_SESSION['pesan'] = "Periode tidak aktif. Silakan login ulang.";
        $_SESSION['tipe'] = "error";
        header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
        exit();
    }

    $cek_periode = mysqli_query($koneksi, "SELECT id_skema FROM tb_skema WHERE id_skema = $id AND id_periode = $id_periode_session LIMIT 1");
    if (!$cek_periode || mysqli_num_rows($cek_periode) == 0) {
        $_SESSION['pesan'] = "Anda hanya dapat menghapus skema pada periode aktif saat ini!";
        $_SESSION['tipe'] = "error";
        header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
        exit();
    }
}

mysqli_begin_transaction($koneksi);

try {

    $hapus_relasi = "DELETE FROM tb_skema_asesor WHERE id_skema = ?";
    $stmt_relasi = mysqli_prepare($koneksi, $hapus_relasi);
    mysqli_stmt_bind_param($stmt_relasi, 'i', $id);
    mysqli_stmt_execute($stmt_relasi);
    mysqli_stmt_close($stmt_relasi);

    $query_units = "SELECT id_unit FROM tb_unit_kompetensi WHERE id_skema = ?";
    $stmt_units = mysqli_prepare($koneksi, $query_units);
    mysqli_stmt_bind_param($stmt_units, 'i', $id);
    mysqli_stmt_execute($stmt_units);
    $result_units = mysqli_stmt_get_result($stmt_units);

    $unit_ids = [];
    while ($row = mysqli_fetch_assoc($result_units)) {
        $unit_ids[] = $row['id_unit'];
    }
    mysqli_stmt_close($stmt_units);

    $total_unit = count($unit_ids);
    $total_elemen = 0;
    $total_kuk = 0;

    if ($total_unit > 0) {
        $unit_ids_str = implode(',', $unit_ids);

        $query_elemen = "SELECT id_elemen FROM tb_elemen WHERE id_unit IN ($unit_ids_str)";
        $result_elemen = mysqli_query($koneksi, $query_elemen);
        $elemen_ids = [];
        while ($row = mysqli_fetch_assoc($result_elemen)) {
            $elemen_ids[] = $row['id_elemen'];
        }
        $total_elemen = count($elemen_ids);

        if ($total_elemen > 0) {
            $elemen_ids_str = implode(',', $elemen_ids);
    
            $query_count_kuk = "SELECT COUNT(*) as total FROM tb_kuk WHERE id_elemen IN ($elemen_ids_str)";
            $result_count = mysqli_query($koneksi, $query_count_kuk);
            $data_count = mysqli_fetch_assoc($result_count);
            $total_kuk = $data_count['total'];

    
            $query_hapus_kuk = "DELETE FROM tb_kuk WHERE id_elemen IN ($elemen_ids_str)";
            mysqli_query($koneksi, $query_hapus_kuk);
        }

        $query_hapus_elemen = "DELETE FROM tb_elemen WHERE id_unit IN ($unit_ids_str)";
        mysqli_query($koneksi, $query_hapus_elemen);

        $query_hapus_unit = "DELETE FROM tb_unit_kompetensi WHERE id_skema = ?";
        $stmt_hapus_unit = mysqli_prepare($koneksi, $query_hapus_unit);
        mysqli_stmt_bind_param($stmt_hapus_unit, 'i', $id);
        mysqli_stmt_execute($stmt_hapus_unit);
        mysqli_stmt_close($stmt_hapus_unit);
    }

    $query_hapus_skema = "DELETE FROM tb_skema WHERE id_skema = ?";
    $stmt_hapus_skema = mysqli_prepare($koneksi, $query_hapus_skema);
    mysqli_stmt_bind_param($stmt_hapus_skema, 'i', $id);
    mysqli_stmt_execute($stmt_hapus_skema);
    mysqli_stmt_close($stmt_hapus_skema);

    mysqli_commit($koneksi);

    $_SESSION['pesan'] = "Skema berhasil dihapus beserta $total_unit unit, $total_elemen elemen, dan $total_kuk KUK!";
    $_SESSION['tipe'] = "success";

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    $_SESSION['pesan'] = "Gagal menghapus skema: " . $e->getMessage();
    $_SESSION['tipe'] = "error";
}

header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
exit();
?>