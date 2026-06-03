<?php

if (session_status() === PHP_SESSION_NONE) {
session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_utm') {
    echo "<script>alert('Akses ditolak! Hanya admin UTM.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}
include '../koneksi.php';

$id_admin = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_admin <= 0) {
    echo "<script>alert('ID admin tidak valid.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
    exit();
}

$cek = mysqli_prepare($koneksi, "SELECT nama_admin FROM tb_admin WHERE id_admin = ?");
mysqli_stmt_bind_param($cek, 'i', $id_admin);
mysqli_stmt_execute($cek);
mysqli_stmt_bind_result($cek, $nama_admin);
if (!mysqli_stmt_fetch($cek)) {
    echo "<script>alert('Data admin LSP tidak ditemukan.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
    exit();
}
mysqli_stmt_close($cek);

if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
    mysqli_begin_transaction($koneksi);

    try {
        //(set id_admin menjadi NULL)
        $update_user = mysqli_prepare($koneksi, "UPDATE users SET id_admin = NULL WHERE id_admin = ?");
        mysqli_stmt_bind_param($update_user, 'i', $id_admin);
        mysqli_stmt_execute($update_user);
        mysqli_stmt_close($update_user);

        // Hapus data admin dari tb_admin
        $delete_admin = mysqli_prepare($koneksi, "DELETE FROM tb_admin WHERE id_admin = ?");
        mysqli_stmt_bind_param($delete_admin, 'i', $id_admin);
        mysqli_stmt_execute($delete_admin);
        mysqli_stmt_close($delete_admin);

        mysqli_commit($koneksi);
        echo "<script>alert('Data Admin LSP \"".addslashes($nama_admin)."\" berhasil dihapus.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "<script>alert('Gagal menghapus data: ".addslashes($e->getMessage())."'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Admin LSP</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 0; }
        .confirm-box { max-width: 430px; width: 94vw; margin: 50px auto; background: #fff; padding: 30px 32px; border-radius: 12px; box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12); }
        .confirm-box h3 { margin-top: 0; margin-bottom: 18px; color: #d32f2f; font-size: 22px; }
        .confirm-box p { font-size: 15px; line-height: 1.7; color: #333; margin: 16px 0; }
        .confirm-box strong { color: #111; }
        .button-group { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 18px; }
        .btn { display: inline-block; min-width: 130px; padding: 11px 18px; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; text-decoration: none; text-align: center; transition: background 0.18s ease; }
        .btn-danger { background: #d32f2f; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-danger:hover { background: #b71c1c; }
        .btn-secondary:hover { background: #5a6268; }
        @media (max-width: 530px) { .confirm-box { padding: 20px 18px; } .button-group { flex-direction: column; } .btn { width: 100%; margin: 0; } }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Anda yakin akan menghapus data admin LSP:<br><strong><?= htmlspecialchars($nama_admin) ?></strong>?</p>
        <p style="font-size: 13px; color: #666;">Tindakan ini akan memutus relasi user (id_admin menjadi NULL) dan menghapus profil admin. Akun user tetap ada namun tidak memiliki profil Admin LSP.</p>
        <form method="post">
            <input type="hidden" name="confirm_delete" value="yes">
            <div class="button-group">
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                <a href="UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>