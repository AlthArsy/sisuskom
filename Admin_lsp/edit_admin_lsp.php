<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_utm') {
    echo "<script>alert('Akses ditolak! Hanya admin utama.'); window.location.href='../LOGIN/login.php';</script>";
    exit();
}
include '../koneksi.php';

$id_admin = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_admin <= 0) {
    echo "<script>alert('ID tidak valid.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
    exit();
}

$query = "SELECT nik, nama_admin FROM tb_admin WHERE id_admin = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_admin);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nik, $nama_admin);
if (!mysqli_stmt_fetch($stmt)) {
    echo "<script>alert('Data tidak ditemukan.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
    exit();
}
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik_baru = trim($_POST['nik']);
    $nama_baru = trim($_POST['nama_admin']);

    if (!empty($nik_baru) && !empty($nama_baru)) {
        $update = "UPDATE tb_admin SET nik = ?, nama_admin = ? WHERE id_admin = ?";
        $stmt_up = mysqli_prepare($koneksi, $update);
        mysqli_stmt_bind_param($stmt_up, 'ssi', $nik_baru, $nama_baru, $id_admin);
        if (mysqli_stmt_execute($stmt_up)) {
            echo "<script>alert('Data berhasil diupdate.'); window.location.href='UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php';</script>";
            exit();
        } else {
            $error = mysqli_stmt_error($stmt_up);
            echo "<script>alert('Gagal update: $error');</script>";
        }
        mysqli_stmt_close($stmt_up);
    } else {
        echo "<script>alert('NIK dan Nama tidak boleh kosong.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin LSP</title>
    <style> body {font-family: 'Segoe UI', Arial, sans-serif;background: #f5f7fb;margin: 0;padding: 0;}
        .form-container {max-width: 430px;width: 97vw;margin: 42px auto 0 auto;background: #fff;border-radius: 10px;box-shadow: 0 6px 25px #0002;padding: 30px 32px 22px 32px;}
        h2 {text-align: center;margin-top: 0;margin-bottom: 27px;font-size: 23px;color: #252B3A;letter-spacing: 1px;}
        .form-group {margin-bottom: 18px;}
        label {font-weight: 600;color: #27314D;margin-bottom: 6px;display: block;font-size: 15px;}
        .required {color: #e3304d;font-weight: 400;font-size: 13px;margin-left: 3px;}
        input[type="text"], textarea, select {width: 100%;border: 1.5px solid #cdcdde;background: #f7f9fd;color: #242B30;padding: 9px 11px;border-radius: 4px;font-size: 15px;margin-top: 3px;margin-bottom: 2px;transition: border .2s;box-sizing: border-box;resize: none;}
        input[type="text"]:focus, textarea:focus, select:focus {border: 1.5px solid #4A7AFF;outline: none;background: #fff;}
        textarea {min-height: 60px;max-height: 180px;}
        .btn-submit {width: 100%;background: #275dfa;color: #fff;border: none;padding: 11px 0;margin-top: 8px;border-radius: 6px;font-size: 16px;font-weight: 600;cursor: pointer;transition: background .18s;letter-spacing: 0.5px;}
        .btn-submit:hover {background: #1f48bd;}
        .info-box {background: #e8f4ff;border-left: 4px solid #4A7AFF;padding: 10px 15px;margin-bottom: 20px;border-radius: 4px;font-size: 14px;}
        @media (max-width: 530px) {
            .form-container {padding: 15px 7vw 13px 7vw;}
            h2 {font-size: 19px;}}</style>
</head>
<body>
<div class="form-container">
    <h2>Edit Data Admin LSP</h2>
    <form method="post">
        <div class="form-group">
            <label>NIK</label>
            <input type="text" name="nik" maxlength="16" value="<?= htmlspecialchars($nik) ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Admin LSP</label>
            <input type="text" name="nama_admin" maxlength="100" value="<?= htmlspecialchars($nama_admin) ?>" required>
        </div>
        <button type="submit" class="btn-submit">Simpan Perubahan</button>
        <a href="UTAMA.php?page=../Admin_lsp/Table_admin_lsp.php" style="display:inline-block; margin-top:10px;">Kembali</a>
    </form>
</div>
</body>
</html>