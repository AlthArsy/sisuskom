<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'], true)) {
    header('Location: ../LOGIN/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php');
    exit();
}

include '../koneksi.php';
require_once __DIR__ . '/ods_helper.php';

$redirect = '../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php';
$session_role = $_SESSION['role'];
$allowed_roles = ['Admin_lsp', 'Asesor', 'Asesi'];
$id_periode = isset($_POST['id_periode']) ? (int) $_POST['id_periode'] : 0;

if ($session_role === 'Admin_lsp') {
    $allowed_roles = ['Asesor', 'Asesi'];
}

if ($id_periode <= 0) {
    $_SESSION['pesan'] = 'Pilih Tahun Ajaran terlebih dahulu sebelum import.';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

$cek_periode = mysqli_prepare($koneksi, 'SELECT id_periode FROM tb_periode WHERE id_periode = ? LIMIT 1');
mysqli_stmt_bind_param($cek_periode, 'i', $id_periode);
mysqli_stmt_execute($cek_periode);
mysqli_stmt_store_result($cek_periode);
if (mysqli_stmt_num_rows($cek_periode) === 0) {
    mysqli_stmt_close($cek_periode);
    $_SESSION['pesan'] = 'Tahun Ajaran tidak valid.';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}
mysqli_stmt_close($cek_periode);

if (!isset($_FILES['file_user']) || $_FILES['file_user']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['pesan'] = 'Gagal mengunggah file. Pastikan file Excel (.ods) dipilih.';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

$tmp_path = $_FILES['file_user']['tmp_name'];
$original_name = $_FILES['file_user']['name'] ?? '';
$extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

if ($extension !== 'ods') {
    $_SESSION['pesan'] = 'Format file tidak didukung. Gunakan file .ods (LibreOffice / Excel).';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

try {
    $rows = parse_ods_rows($tmp_path);
    $users = parse_user_import_rows($rows);
} catch (RuntimeException $e) {
    $_SESSION['pesan'] = $e->getMessage();
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

if (count($users) === 0) {
    $_SESSION['pesan'] = 'Tidak ada data user untuk diimpor.';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

$inserted = 0;
$skipped = 0;
$errors = [];

$check_stmt = mysqli_prepare($koneksi, 'SELECT id_user FROM users WHERE username = ? LIMIT 1');
$insert_stmt = mysqli_prepare($koneksi, 'INSERT INTO users (username, password, role, id_periode) VALUES (?, ?, ?, ?)');

if (!$check_stmt || !$insert_stmt) {
    $_SESSION['pesan'] = 'Gagal mempersiapkan query database.';
    $_SESSION['tipe'] = 'error';
    header('Location: ' . $redirect);
    exit();
}

foreach ($users as $user) {
    $row = (int) $user['row'];
    $username = $user['username'];
    $password = $user['password'];
    $role = $user['role'];

    if ($username === '') {
        $errors[] = "Baris {$row}: Username wajib diisi.";
        continue;
    }

    if ($password === '') {
        $errors[] = "Baris {$row}: Password wajib diisi.";
        continue;
    }

    if ($role === '') {
        $errors[] = "Baris {$row}: Role '{$user['role_raw']}' tidak valid. Gunakan Admin_lsp, Asesor, atau Asesi.";
        continue;
    }

    if (!in_array($role, $allowed_roles, true)) {
        $errors[] = "Baris {$row}: Anda tidak memiliki izin menambahkan role {$role}.";
        continue;
    }

    if (strlen($username) > 32) {
        $errors[] = "Baris {$row}: Username maksimal 32 karakter.";
        continue;
    }

    if (strlen($password) > 255) {
        $errors[] = "Baris {$row}: Password terlalu panjang.";
        continue;
    }

    mysqli_stmt_bind_param($check_stmt, 's', $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $skipped++;
        continue;
    }

    $password_hashed = md5($password);
    mysqli_stmt_bind_param($insert_stmt, 'sssi', $username, $password_hashed, $role, $id_periode);

    if (mysqli_stmt_execute($insert_stmt)) {
        $inserted++;
    } else {
        $errors[] = "Baris {$row}: Gagal menyimpan user {$username}.";
    }
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($insert_stmt);

$summary = "Import selesai: {$inserted} user ditambahkan";
if ($skipped > 0) {
    $summary .= ", {$skipped} dilewati (username sudah ada)";
}
if (count($errors) > 0) {
    $summary .= '. ' . implode(' ', array_slice($errors, 0, 5));
    if (count($errors) > 5) {
        $summary .= ' ...dan ' . (count($errors) - 5) . ' error lainnya.';
    }
}

$_SESSION['pesan'] = $summary;
$_SESSION['tipe'] = ($inserted > 0 && count($errors) === 0) ? 'success' : (($inserted > 0) ? 'success' : 'error');

header('Location: ' . $redirect);
exit();
