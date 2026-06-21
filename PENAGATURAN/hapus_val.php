<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    echo "<script>alert('Akses ditolak! Silakan login sebagai Admin_lsp.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

if (isset($_GET['id'])) {
    $id_validator = intval($_GET['id']);


    $sql_select = "SELECT * FROM tb_validator WHERE id_validator = ?";
    $stmt_select = mysqli_prepare($koneksi, $sql_select);

    if ($stmt_select) {
        mysqli_stmt_bind_param($stmt_select, "i", $id_validator);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $username = $user_data['username'];
            $noreg = $user_data['noreg'];


            mysqli_begin_transaction($koneksi);

            $success = true;
            $error_message = '';


            if ($success) {
                $sql_delete_user = "DELETE FROM tb_validator WHERE id_validator = ?";
                $stmt_delete_user = mysqli_prepare($koneksi, $sql_delete_user);

                if ($stmt_delete_user) {
                    mysqli_stmt_bind_param($stmt_delete_user, "i", $id_validator);

                    if (mysqli_stmt_execute($stmt_delete_user)) {
                        mysqli_commit($koneksi);
                        echo "<script>alert('Validator {$username} berhasil dihapus!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php';</script>";
                    } else {
                        $success = false;
                        $error_message = mysqli_error($koneksi);
                    }

                    mysqli_stmt_close($stmt_delete_user);
                }
            }


            if (!$success) {
                mysqli_rollback($koneksi);
                echo "<script>alert('Gagal menghapus validator: {$error_message}'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php';</script>";
            }

        } else {
            echo "<script>alert('Validator tidak ditemukan!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php';</script>";
        }
        mysqli_stmt_close($stmt_select);
    } else {
        echo "<script>alert('Gagal memproses penghapusan!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php';</script>";
}
?>
