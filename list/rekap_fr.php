<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
require_once __DIR__ . '/rekap_helpers.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

$base = '../BERANDA/UTAMA.php';
$p      = rekap_params();
$filter = $_POST['_filter'] ?? $p['filter'];
$cari   = $_POST['_cari'] ?? $p['cari'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rekomendasi'])) {
    if ($role !== 'Admin_lsp') {
        header("Location: {$base}?page=../list/rekap_fr.php&error=forbidden");
        exit;
    }
    $id_apl1 = intval($_POST['id_apl1']);
    $rekomendasi = $_POST['rekomendasi'] ?? '';
    $catatan = $_POST['catatan'] ?? '';

    if (in_array($rekomendasi, ['Diterima', 'Tidak Diterima'])) {
        $stmt = mysqli_prepare($koneksi, "UPDATE tb_apl1 SET rekomendasi = ?, catatan_admin = ? WHERE id_apl1 = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $rekomendasi, $catatan, $id_apl1);
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE tb_apl1 SET rekomendasi = NULL, catatan_admin = ? WHERE id_apl1 = ?");
        mysqli_stmt_bind_param($stmt, "si", $catatan, $id_apl1);
    }
    mysqli_stmt_execute($stmt);

    header("Location: {$base}?page=../list/rekap_fr.php&" . rekap_qs($filter, $cari));
    exit;
}

$where = "WHERE 1=1"
    . rekap_sql_filter_status($filter, 'apl1')
    // . rekap_sql_batas_2bulan('a.tanggal_pemohon')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 'a.nama_pemohon', 'a.judul_skema', 'a.nomor_skema']);

$sql = "SELECT a.id_apl1, a.id_asesi, a.judul_skema, a.nomor_skema,
               a.nama_pemohon, a.tanggal_pemohon, a.rekomendasi, a.catatan_admin,
               asi.nama_asesi
        FROM tb_apl1 a
        LEFT JOIN tb_asesi asi ON asi.id_asesi = a.id_asesi
        $where
        ORDER BY a.id_apl1 DESC";

$result = mysqli_query($koneksi, $sql);
$rows   = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$cnt_base = "SELECT COUNT(*) c FROM tb_apl1 a
             LEFT JOIN tb_asesi asi ON asi.id_asesi = a.id_asesi
             WHERE 1=1"
    // . rekap_sql_batas_2bulan('a.tanggal_pemohon')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 'a.nama_pemohon', 'a.judul_skema', 'a.nomor_skema']);

$total_all    = rekap_count($koneksi, $cnt_base);
$total_belum  = rekap_count($koneksi, $cnt_base . " AND (a.rekomendasi IS NULL OR a.rekomendasi='')");
$total_terima = rekap_count($koneksi, $cnt_base . " AND a.rekomendasi='Diterima'");
$total_tolak  = rekap_count($koneksi, $cnt_base . " AND a.rekomendasi='Tidak Diterima'");
$total_selesai = rekap_count($koneksi, $cnt_base . " AND a.rekomendasi IS NOT NULL AND a.rekomendasi != ''");

$qs = fn($f) => rekap_qs($f, $cari);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap APL 1 — Formulir Permohonan Sertifikasi Kompetensi</div>

    <?php rekap_render_cari($base, '../list/rekap_fr.php', $filter, $cari); ?>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('semua') ?>"
           class="rekap-card card-semua <?= $filter === 'semua' ? 'active' : '' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Pengajuan</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('belum') ?>"
           class="rekap-card card-belum <?= $filter === 'belum' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <!-- <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('selesai') ?>"
           class="rekap-card card-selesai <?= $filter === 'selesai' ? 'active' : '' ?>">
            <div class="num"><?= $total_selesai ?></div>
            <div class="lbl">Sudah Diproses</div>
        </a> -->
        <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('diterima') ?>"
           class="rekap-card card-diterima <?= $filter === 'diterima' ? 'active' : '' ?>">
            <div class="num"><?= $total_terima ?></div>
            <div class="lbl">Diterima</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('ditolak') ?>"
           class="rekap-card card-ditolak <?= $filter === 'ditolak' ? 'active' : '' ?>">
            <div class="num"><?= $total_tolak ?></div>
            <div class="lbl">Tidak Diterima</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Tidak ada data untuk filter ini.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_fr.php&<?= $qs('semua') ?>">reset pencarian</a>.
            <?php endif; ?>
        </div>
    <?php else: ?>
    <div class="rekap-table-wrap">
        <table class="rekap-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Asesi</th>
                    <th>Skema</th>
                    <th>Tanggal Submit</th>
                    <th>Status Rekomendasi</th>
                    <th>Komentar Admin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <?php
                    $is_belum = (is_null($r['rekomendasi']) || $r['rekomendasi'] === '');
                    $is_admin_utm = ($role === 'Admin_utm');
                ?>
                <?php if (!$is_admin_utm && $is_belum): ?>
                <form method="post" class="aksi-form">
                    <input type="hidden" name="id_apl1" value="<?= $r['id_apl1'] ?>">
                    <input type="hidden" name="update_rekomendasi" value="1">
                    <input type="hidden" name="_filter" value="<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="_cari" value="<?= htmlspecialchars($cari, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
                <tr>
                    <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                    <td data-label="Nama Asesi"><?= htmlspecialchars($r['nama_asesi'] ?? $r['nama_pemohon']) ?></td>
                    <td data-label="Skema">
                        <?= htmlspecialchars($r['judul_skema']) ?>
                        <div class="rekap-skema-sub">No. <?= htmlspecialchars($r['nomor_skema']) ?></div>
                    </td>
                    <td data-label="Tanggal Submit" style="text-align:center;"><?= htmlspecialchars($r['tanggal_pemohon']) ?></td>
                    <td data-label="Status Rekomendasi" style="text-align:center;">
                        <?php if ($is_belum): ?>
                            <span class="badge badge-belum">Belum Diproses</span>
                        <?php elseif ($r['rekomendasi'] === 'Diterima'): ?>
                            <span class="badge badge-diterima">Diterima</span>
                        <?php else: ?>
                            <span class="badge badge-ditolak">Tidak Diterima</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Komentar Admin" style="max-width:200px;">
                        <?php if ($is_admin_utm || !$is_belum): ?>
                            <?= nl2br(htmlspecialchars($r['catatan_admin'] ?? '')) ?>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($r['catatan_admin'] ?? '')) ?>
                            <textarea name="catatan" class="komentar-textarea" placeholder="Komentar admin (opsional)"><?= htmlspecialchars($r['catatan_admin'] ?? '') ?></textarea>
                        <?php endif; ?>
                    </td>
                    <td data-label="Aksi" class="rekap-aksi" style="text-align:left;">
                        <?php if ($is_admin_utm): ?>
                            <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_APL1.php&view=1&id_asesi=<?= $r['id_asesi'] ?>">Lihat</a>
                            <a class="btn-cetak" href="<?= $base ?>?page=../FR_APL/FR_APL1.php&view=1&print=1&id_asesi=<?= $r['id_asesi'] ?>" target="_blank">Cetak</a>
                        <?php elseif ($is_belum): ?>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="rekomendasi" value="Diterima"
                                        <?= ($r['rekomendasi'] === 'Diterima') ? 'checked' : '' ?>>
                                    Diterima
                                </label>
                                <label>
                                    <input type="radio" name="rekomendasi" value="Tidak Diterima"
                                        <?= ($r['rekomendasi'] === 'Tidak Diterima') ? 'checked' : '' ?>>
                                    Tidak Diterima
                                </label>
                            </div>
                            <button type="submit" class="btn-update">Update</button>
                        <?php else: ?>
                            <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_APL1.php&view=1&id_asesi=<?= $r['id_asesi'] ?>">Lihat</a>
                            <a class="btn-cetak" href="<?= $base ?>?page=../FR_APL/FR_APL1.php&view=1&print=1&id_asesi=<?= $r['id_asesi'] ?>" target="_blank">Cetak</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if (!$is_admin_utm && $is_belum): ?>
                </form>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
