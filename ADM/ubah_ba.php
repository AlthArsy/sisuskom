<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'], true)) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$id_ba = isset($_GET['id_ba']) ? intval($_GET['id_ba']) : (isset($_POST['id_ba']) ? intval($_POST['id_ba']) : 0);

if ($id_ba <= 0) {
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT id_ba, id_skema, bukti_adm FROM tb_bukti_adm WHERE id_ba = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id_ba);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    $_SESSION['pesan'] = 'Data tidak ditemukan.';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $txt = trim($_POST['bukti_adm'] ?? '');
    if ($txt === '') {
        $message = 'Bukti adm harus diisi';
        $message_type = 'error';
    } else {
        $up = mysqli_prepare($koneksi, "UPDATE tb_bukti_adm SET bukti_adm = ? WHERE id_ba = ?");
        mysqli_stmt_bind_param($up, "si", $txt, $id_ba);
        if (mysqli_stmt_execute($up)) {
            mysqli_stmt_close($up);
            $_SESSION['pesan'] = 'Bukti adm berhasil diubah.';
            $_SESSION['tipe'] = 'success';
            header('Location: ../BERANDA/UTAMA.php?page=../ADM/bukti_adm.php&id_skema=' . (int) $row['id_skema']);
            exit();
        }
        $message = mysqli_error($koneksi);
        $message_type = 'error';
        mysqli_stmt_close($up);
    }
}

$val = htmlspecialchars($_POST['bukti_adm'] ?? $row['bukti_adm']);
?>
<link rel="stylesheet" href="../assets/CSS/ubah_manajeman.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="l-container">
    <div class="header">
        <i class="fas fa-edit"></i>
        <div>
            <h1>Ubah Bukti adm</h1>
            <p>Skema ID: <?= (int) $row['id_skema'] ?></p>
        </div>
    </div>
    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($message_type); ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="post" action="">
            <input type="hidden" name="id_ba" value="<?= (int) $id_ba ?>">
            <div class="form-group">
                <label for="bukti_adm" class="required">Bukti adm</label>
                <input type="text" id="bukti_adm" name="bukti_adm" value="<?= $val ?>" required maxlength="500"
                       placeholder="Uraian bukti adm">
            </div>
            <div class="btn-container">
                <a href="../BERANDA/UTAMA.php?page=../ADM/bukti_adm.php&id_skema=<?= (int) $row['id_skema'] ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
