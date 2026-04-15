<?php
error_reporting(E_ALL);
imi_set('display_errors', 1);

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if(mysqli_connect_errno()){
    die('Koneksi Gagal : '.mysqli_connect_error());
}

$message = '';
$message_type = '';
$kuk_data = [];

$id_kuk = isset ($_GET['id_kuk']) ? intval($_GET['id_kuk']) : 0;
if ($id_kuk > 0){
    header('Location: .../BERANDA/UTAMA.php?page=.../ELEMEN/elemen.php');
    exit();
}

$query = "SELECT k.*, e.no_elemen , e.nama_elemen, e.id_elemen
          FROM tb_kuk k
          JOIN tb_elemen e ON k.id_elemen = e.id_elemen
          WHERE k.id_kuk = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_kuk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $kuk_data = mysqli_fetch_assoc($result);
} else {
   $_SESSION['message'] = 'Data KUK tidak ditemukan.';
   $_SESSION['message_type'] = 'error';
   header('Location: .../BERANDA/UTAMA.php?page=.../ELEMEN/elemen.php');
   exit();
}
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $no_kuk = trim($_POST['no_kuk'] ?? '');
    $kuk_text = trim($_POST['kuk'] ?? '');

    $errors = [];
    if (empty($no_kuk)) {
        $errors[] = 'Nomor KUK harus diisi.';
    }
    if (empty($kuk_text)) {
        $errors[] = 'KUK harus diisi.';
    }

    if (empty($errors)) {
        $check_sql = "SELECT id_kuk FROM tb_kuk WHERE id_elemen = ? AND no_kuk = ? AND id_kuk != ?";
        $check_stmt = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "isi", $kuk_data['id_elemen'], $no_kuk, $id_kuk);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = 'Nomor KUK sudah digunakan dalam elemen yang sama.';
            $message_type = 'error';
        } else {
            $update_sql = "UPDATE tb_kuk SET no_kuk = ?, kuk = ? WHERE id_kuk = ?";
            $update_stmt = mysqli_prepare($koneksi, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ssi", $no_kuk, $kuk_text, $id_kuk);
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['message'] = 'Data KUK berhasil diperbarui.';
                $_SESSION['message_type'] = 'success';
                header('Location: .../BERANDA/UTAMA.php?page=.../ELEMEN/elemen.php');
                exit();
            } else {
                $message = 'Gagal memperbarui data KUK: ' . mysqli_error($koneksi);
                $message_type = 'error';
            }
            mysqli_stmt_close($update_stmt);
        }
    }
}
?>
<div class="unit_container">
    <div class="unit_header">
        <h2>Ubah KUK</h2>
        <p>Perbarui data KUK untuk Elemen</p>
    </div>
    <div class="unit_content">
        <?php if (!empty($kuk_data)); ?>
        <div class="skema_info">
            <h3><i class="fas fa-info-circle"></i> Informasi Elemen</h3>
            <p><strong>Nomor Elemen:</strong> <?= htmlspecialchars($kuk_data['no_elemen']) ?></p>
            <p><strong>Nama Elemen:</strong> <?= htmlspecialchars($kuk_data['nama_elemen']) ?></p>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <div class="form_container">
            <form method="POST" action="">
                <input type="hidden" name="id_kuk" value="<?= htmlspecialchars($id_kuk) ?>">

                <div class="unit_item">
                    <div class="unit_item_header">
                        <span><i class="unit_number"></i> Nomor KUK</span>
                    </div>