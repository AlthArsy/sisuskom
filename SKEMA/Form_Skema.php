<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}
include '../koneksi.php';
$id_asesor   = $_SESSION['id_asesor'] ?? 0;
$nama_asesor = '-';
if ($id_asesor) {
    $res = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesor FROM tb_asesor WHERE id_asesor='$id_asesor' LIMIT 1"));
    $nama_asesor = $res['nama_asesor'] ?? '-';
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../assets/CSS/From_skema.css">
<div class="l-container">
    <div class="header">
        <i class="fas fa-clipboard-list"></i>
        <h2>Pendaftaran Skema Sertifikasi</h2>
    </div>
    <div class="form-container">
        <form action="../SKEMA/simpan_skema.php" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="no_skema">No Skema</label>
                <input type="text" id="no_skema" name="no_skema" required autocomplete="off" placeholder="Masukkan nomor skema">
            </div>
            <div class="form-group">
                <label for="judul_skema">Judul Skema</label>
                <input type="text" id="judul_skema" name="judul_skema" required placeholder="Masukkan judul skema">
            </div>
            <div class="form-group">
                <label for="standar_kompetensi">Standar Kompetensi Kerja</label>
                <textarea id="standar_kompetensi" name="standar_kompetensi" required placeholder="Masukkan standar kompetensi kerja"></textarea>
            </div>
            <div class="form-group">
                <label for="nama_asesor">Nama Asesor</label>
                <input type="text" id="nama_asesor"
                    value="<?php echo htmlspecialchars($nama_asesor); ?>"
                    class="form-control" readonly>
                <input type="hidden" name="id_asesor" value="<?php echo $_SESSION['id_asesor'] ?? ''; ?>">
            </div>
            <div class="btn-container">
                <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>