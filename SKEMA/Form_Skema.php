<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp') {
    header("Location: ../LOGIN/login.php");
    exit;
}
include '../koneksi.php';

if (!isset($_SESSION['id_periode']) || $_SESSION['id_periode'] <= 0) {
    $_SESSION['pesan'] = "Silakan pilih periode terlebih dahulu saat login.";
    $_SESSION['tipe']  = "error";
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
    exit;
}

$id_periode_session = $_SESSION['id_periode'];

$periode_nama = '-';
$q_periode = mysqli_query($koneksi, "SELECT tahun_ajaran FROM tb_periode WHERE id_periode = $id_periode_session");
if ($q_periode && $row = mysqli_fetch_assoc($q_periode)) {
    $periode_nama = htmlspecialchars($row['tahun_ajaran']);
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../assets/CSS/From_skema.css">
<div class="l-container">
    <div class="header">
        <i class="fas fa-clipboard-list"></i>
        <h2>Tambah Skema Sertifikasi</h2>
        <p style="font-size:14px; margin-top:5px;">Periode aktif: <strong><?php echo $periode_nama; ?></strong></p>
    </div>
    <div class="form-container">
        <form action="../SKEMA/simpan_skema.php" method="POST" autocomplete="off">
            <input type="hidden" name="id_periode" value="<?php echo $id_periode_session; ?>">
            
            <div class="form-group">
                <label for="no_skema">Nomor Skema <span style="color:red">*</span></label>
                <input type="text" id="no_skema" name="no_skema" required autocomplete="off" placeholder="Contoh: SKM-001">
            </div>
            <div class="form-group">
                <label for="judul_skema">Judul Skema <span style="color:red">*</span></label>
                <input type="text" id="judul_skema" name="judul_skema" required placeholder="Masukkan judul skema">
            </div>
            <div class="form-group">
                <label for="standar_kompetensi">Standar Kompetensi Kerja</label>
                <textarea id="standar_kompetensi" name="standar_kompetensi" rows="4" placeholder="Masukkan standar kompetensi kerja"></textarea>
            </div>
            <div class="btn-container">
                <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>