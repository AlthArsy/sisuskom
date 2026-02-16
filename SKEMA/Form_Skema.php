<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}
?>
<!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet"> -->
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
                <input type="text"
                       id="nama_asesor"
                       value="<?php echo isset($_SESSION['nama_user']) ? htmlspecialchars($_SESSION['nama_user']) : ''; ?>"
                       class="form-control"
                       readonly>
                <input type="hidden" name="id_referensi" value="<?php echo $_SESSION['id_referensi'] ?? ''; ?>">
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Skema
                </button>
            </div>
        </form>
    </div>
</div>
