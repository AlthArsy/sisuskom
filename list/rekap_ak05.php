<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
require_once __DIR__ . '/rekap_helpers.php';

$role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'], true)) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

$p    = rekap_params();
$cari = $p['cari'];
$base = '../BERANDA/UTAMA.php';

$sql = "SELECT
            sk.id_skema,
            sk.judul_skema,
            sk.nomor_skema,
            ar.nama_asesor,
            ak5.id_ak5,
            ak5.catatan,
            (SELECT COUNT(DISTINCT d.id_asesi) FROM detail_ak5 d WHERE d.id_ak5 = ak5.id_ak5) AS jml_asesi,
            (SELECT MAX(d.tanggal) FROM detail_ak5 d WHERE d.id_ak5 = ak5.id_ak5) AS tanggal
        FROM tb_ak05 ak5
        INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak5.id_apl1
        INNER JOIN tb_skema sk ON sk.id_skema = ap.id_skema
        LEFT JOIN tb_asesor ar ON ar.id_asesor = ak5.id_asesor
        WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'ak5.id_asesor')
    . rekap_sql_cari($koneksi, $cari, ['sk.judul_skema', 'sk.nomor_skema', 'ar.nama_asesor']);

$batas = date('Y-m-d', strtotime('-2 months'));
$sql .= " GROUP BY sk.id_skema, ak5.id_ak5
          HAVING (tanggal IS NULL OR tanggal = '' OR tanggal >= '{$batas}')";

$result = mysqli_query($koneksi, $sql);
$rows   = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.AK.05 — Laporan Asesmen</div>

    <?php rekap_render_cari($base, '../list/rekap_ak05.php', 'semua', $cari); ?>

    <?php if ($role === 'Asesor'): ?>
    <a class="btn-baru" href="<?= $base ?>?page=../FR_APL/FR_AK05.php">+ Isi / Pilih Skema</a>
    
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Belum ada data FR.AK.05.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_ak05.php">reset pencarian</a>.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="rekap-table-wrap">
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Skema</th>
                        <th>Asesor</th>
                        <th>Jml Asesi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row): ?>
                    <tr>
                        <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                        <td data-label="Skema">
                            <?= htmlspecialchars($row['judul_skema']) ?>
                            <div class="rekap-skema-sub">No. <?= htmlspecialchars($row['nomor_skema']) ?></div>
                        </td>
                        <td data-label="Asesor"><?= htmlspecialchars($row['nama_asesor'] ?: '-') ?></td>
                        <td data-label="Jml Asesi" style="text-align:center;"><?= (int) $row['jml_asesi'] ?></td>
                        <td data-label="Tanggal"><?= htmlspecialchars($row['tanggal'] ?: '-') ?></td>
                        <td data-label="Aksi" class="rekap-aksi" style="text-align:center;">
                            <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_AK05.php&id_skema=<?= (int) $row['id_skema'] ?>&view=1">Lihat</a>
                            <a class="btn-cetak" target="_blank" href="../pdf/cetak_ak5.php?id_skema=<?= (int) $row['id_skema'] ?>">Cetak PDF</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
