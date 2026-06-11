<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    echo "<script>alert('Akses ditolak! Silakan login sebagai Admin.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

if (isset($_GET['id'])) {
    $id_user = intval($_GET['id']);


    $sql_select = "SELECT * FROM users WHERE id_user = ?";
    $stmt_select = mysqli_prepare($koneksi, $sql_select);

    if ($stmt_select) {
        mysqli_stmt_bind_param($stmt_select, "i", $id_user);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $username = $user_data['username'];
            $role = $user_data['role'];
            $id_asesi = $user_data['id_asesi'] ?? null;
            $id_asesor = $user_data['id_asesor'] ?? null;
            $id_admin = $user_data['id_admin'] ?? null;



            mysqli_begin_transaction($koneksi);

            $success = true;
            $error_message = '';


            if ($id_asesor || $id_asesi) {
                if ($role === 'Asesi') {
                    $sql_delete_asesi = "DELETE FROM tb_asesi WHERE id_asesi = ?";
                    $stmt_delete_asesi = mysqli_prepare($koneksi, $sql_delete_asesi);
                    if ($stmt_delete_asesi) {
                        mysqli_stmt_bind_param($stmt_delete_asesi, "i", $id_asesi);
                        if (!mysqli_stmt_execute($stmt_delete_asesi)) {
                            $success = false;
                            $error_message = mysqli_error($koneksi);
                        }
                        mysqli_stmt_close($stmt_delete_asesi);
                    }
                } elseif ($role === 'Asesor') {
                    $sql_delete_asesor = "DELETE FROM tb_asesor WHERE id_asesor = ?";
                    $stmt_delete_asesor = mysqli_prepare($koneksi, $sql_delete_asesor);
                    if ($stmt_delete_asesor) {
                        mysqli_stmt_bind_param($stmt_delete_asesor, "i", $id_asesor);
                        if (!mysqli_stmt_execute($stmt_delete_asesor)) {
                            $success = false;
                            $error_message = mysqli_error($koneksi);
                        }
                        mysqli_stmt_close($stmt_delete_asesor);
                    }
                }
            }


            if ($id_admin) {
                if ($role === 'Admin_lsp') {
                    $sql_delete_asesi = "DELETE FROM tb_admin WHERE id_admin = ?";
                    $stmt_delete_asesi = mysqli_prepare($koneksi, $sql_delete_asesi);
                    if ($stmt_delete_asesi) {
                        mysqli_stmt_bind_param($stmt_delete_asesi, "i", $id_admin);
                        if (!mysqli_stmt_execute($stmt_delete_asesi)) {
                            $success = false;
                            $error_message = mysqli_error($koneksi);
                        }
                        mysqli_stmt_close($stmt_delete_asesi);
                    }
                }
            }


            if ($success) {
                $sql_delete_user = "DELETE FROM users WHERE id_user = ?";
                $stmt_delete_user = mysqli_prepare($koneksi, $sql_delete_user);

                if ($stmt_delete_user) {
                    mysqli_stmt_bind_param($stmt_delete_user, "i", $id_user);

                    if (mysqli_stmt_execute($stmt_delete_user)) {
                        mysqli_commit($koneksi);
                        echo "<script>alert('User {$username} berhasil dihapus!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';</script>";
                    } else {
                        $success = false;
                        $error_message = mysqli_error($koneksi);
                    }

                    mysqli_stmt_close($stmt_delete_user);
                }
            }


            if (!$success) {
                mysqli_rollback($koneksi);
                echo "<script>alert('Gagal menghapus user: {$error_message}'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';</script>";
            }

        } else {
            echo "<script>alert('User tidak ditemukan!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';</script>";
        }
        mysqli_stmt_close($stmt_select);
    } else {
        echo "<script>alert('Gagal memproses penghapusan!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href='../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';</script>";
}
?>
