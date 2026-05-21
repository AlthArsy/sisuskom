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

$id_bd = isset($_GET['id_bd']) ? intval($_GET['id_bd']) : (isset($_POST['id_bd']) ? intval($_POST['id_bd']) : 0);

if ($id_bd <= 0) {
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT id_bd, id_skema, bukti_dasar FROM tb_bukti_dasar WHERE id_bd = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id_bd);
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
    $txt = trim($_POST['bukti_dasar'] ?? '');
    if ($txt === '') {
        $message = 'Bukti dasar harus diisi';
        $message_type = 'error';
    } else {
        $up = mysqli_prepare($koneksi, "UPDATE tb_bukti_dasar SET bukti_dasar = ? WHERE id_bd = ?");
        mysqli_stmt_bind_param($up, "si", $txt, $id_bd);
        if (mysqli_stmt_execute($up)) {
            mysqli_stmt_close($up);
            $_SESSION['pesan'] = 'Bukti dasar berhasil diubah.';
            $_SESSION['tipe'] = 'success';
            header('Location: ../BERANDA/UTAMA.php?page=../DASAR/bukti_dasar.php&id_skema=' . (int) $row['id_skema']);
            exit();
        }
        $message = mysqli_error($koneksi);
        $message_type = 'error';
        mysqli_stmt_close($up);
    }
}

$val = htmlspecialchars($_POST['bukti_dasar'] ?? $row['bukti_dasar']);
?>
<link rel="stylesheet" href="../assets/CSS/ubah_manajeman.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="l-container">
    <div class="header">
        <i class="fas fa-edit"></i>
        <div>
            <h1>Ubah Bukti Dasar</h1>
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
            <input type="hidden" name="id_bd" value="<?= (int) $id_bd ?>">
            <div class="form-group">
                <label for="bukti_dasar" class="required">Bukti Dasar</label>
                <input type="text" id="bukti_dasar" name="bukti_dasar" value="<?= $val ?>" required maxlength="500"
                       placeholder="Uraian bukti dasar">
            </div>
            <div class="btn-container">
                <a href="../BERANDA/UTAMA.php?page=../DASAR/bukti_dasar.php&id_skema=<?= (int) $row['id_skema'] ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
