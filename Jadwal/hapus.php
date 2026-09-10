<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    echo "<script>alert('Akses ditolak! Anda tidak memiliki izin untuk menghapus data.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>alert('ID Jadwal tidak valid!'); window.location.href='../BERANDA/UTAMA.php?page=../Jadwal/jadwal.php';</script>";
    exit();
}

$sql = "DELETE FROM tb_jadwal WHERE id_jadwal = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['alert'] = 'Data jadwal berhasil dihapus!';
    } else {
        $_SESSION['alert'] = 'error|Gagal menghapus data jadwal. Data mungkin sedang digunakan oleh formulir APL1.';
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['alert'] = 'error|Terjadi kesalahan sistem saat menghapus data.';
}

header('Location: ../BERANDA/UTAMA.php?page=../Jadwal/jadwal.php');
exit();
?>
