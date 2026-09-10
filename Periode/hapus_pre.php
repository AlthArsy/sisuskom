<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp' && $_SESSION['role'] !== 'Admin_utm') {
    echo "<script>alert('Akses ditolak! Silakan login sebagai Admin_lsp.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

if (isset($_GET['id'])) {
    $id_periode = intval($_GET['id']);


    $sql_select = "SELECT * FROM tb_periode WHERE id_periode = ?";
    $stmt_select = mysqli_prepare($koneksi, $sql_select);

    if ($stmt_select) {
        mysqli_stmt_bind_param($stmt_select, "i", $id_periode);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $tahun_ajaran = $user_data['tahun_ajaran'];


            mysqli_begin_transaction($koneksi);

            $success = true;
            $error_message = '';


            if ($success) {
                $sql_delete_user = "DELETE FROM tb_periode WHERE id_periode = ?";
                $stmt_delete_user = mysqli_prepare($koneksi, $sql_delete_user);

                if ($stmt_delete_user) {
                    mysqli_stmt_bind_param($stmt_delete_user, "i", $id_periode);

                    if (mysqli_stmt_execute($stmt_delete_user)) {
                        mysqli_commit($koneksi);
                        echo "<script>alert('Periode {$tahun_ajaran} berhasil dihapus!'); window.location.href='../BERANDA/UTAMA.php?page=../Periode/periode.php';</script>";
                    } else {
                        $success = false;
                        $error_message = mysqli_error($koneksi);
                    }

                    mysqli_stmt_close($stmt_delete_user);
                }
            }


            if (!$success) {
                mysqli_rollback($koneksi);
                echo "<script>alert('Gagal menghapus periode: {$error_message}'); window.location.href='../BERANDA/UTAMA.php?page=../Periode/periode.php';</script>";
            }

        } else {
            echo "<script>alert('Periode tidak ditemukan!'); window.location.href='../BERANDA/UTAMA.php?page=../Periode/periode.php';</script>";
        }
        mysqli_stmt_close($stmt_select);
    } else {
        echo "<script>alert('Gagal memproses penghapusan!'); window.location.href='../BERANDA/UTAMA.php?page=../Periode/periode.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href='../BERANDA/UTAMA.php?page=../Periode/periode.php';</script>";
}
?>
