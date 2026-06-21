<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
require_once __DIR__ . '/rekap_helpers.php';

$role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak. Hanya untuk Asesor, Admin LSP, dan Admin Utama.</p>";
    exit;
}

$p    = rekap_params();
$cari = $p['cari'];
$base = '../BERANDA/UTAMA.php';

$sql = "SELECT 
            ak03.id_ak03,
            ak03.id_asesi,
            ak03.tgl_selesai,
            ak03.catatan_lainnya,
            a.nama_asesi,
            apl.judul_skema,
            apl.nomor_skema,
            ak01.tuk,
            ak01.hari_tanggal,
            asr.nama_asesor
        FROM tb_ak03 ak03
        JOIN tb_asesi a ON a.id_asesi = ak03.id_asesi
        JOIN tb_apl1 apl ON apl.id_apl1 = ak03.id_apl1
        LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak03.id_ak01
        LEFT JOIN tb_asesor asr ON asr.id_asesor = ak01.id_asesor
        WHERE 1=1"
    . ($role === 'Asesor' && $id_asesor_session ? " AND ak01.id_asesor = '" . intval($id_asesor_session) . "'" : '')
    // . rekap_sql_batas_2bulan('COALESCE(ak03.tgl_selesai, ak01.hari_tanggal)')
    . rekap_sql_cari($koneksi, $cari, ['a.nama_asesi', 'apl.judul_skema', 'apl.nomor_skema']);

$sql .= " ORDER BY ak03.id_ak03 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$f_asesor = ($role === 'Asesor' && $id_asesor_session)
    ? " AND ak01.id_asesor = '" . intval($id_asesor_session) . "'"
    : '';

$cnt_base = "SELECT COUNT(*) c
             FROM tb_ak03 ak03
             JOIN tb_asesi a ON a.id_asesi = ak03.id_asesi
             JOIN tb_apl1 apl ON apl.id_apl1 = ak03.id_apl1
             LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak03.id_ak01
             WHERE 1=1 $f_asesor"
    // . rekap_sql_batas_2bulan('COALESCE(ak03.tgl_selesai, ak01.hari_tanggal)')
    . rekap_sql_cari($koneksi, $cari, ['a.nama_asesi', 'apl.judul_skema', 'apl.nomor_skema']);

$total_all = rekap_count($koneksi, $cnt_base);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.AK.03 — Umpan Balik dan Catatan Asesmen</div>

    <?php rekap_render_cari($base, '../list/rekap_ak3.php', 'semua', $cari); ?>

    <div class="rekap-cards">
        <div class="rekap-card card-semua">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Umpan Balik Asesmen</div>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Belum ada data FR.AK.03 yang tersimpan.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_ak3.php">reset pencarian</a>.
            <?php elseif ($role === 'Asesor'): ?>
                <br><br>Asesi harus mengisi FR.AK.03 setelah asesmen selesai.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="rekap-table-wrap">
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Asesi</th>
                        <th>Skema Sertifikasi</th>
                        <th>TUK</th>
                        <th>Hari / Tanggal</th>
                        <th>Tgl Selesai</th>
                        <th>Asesor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                            <td data-label="Nama Asesi"><?= htmlspecialchars($row['nama_asesi']) ?></td>
                            <td data-label="Skema Sertifikasi">
                                <?= htmlspecialchars($row['judul_skema']) ?>
                                <div class="rekap-skema-sub">No. <?= htmlspecialchars($row['nomor_skema']) ?></div>
                            </td>
                            <td data-label="TUK"><?= htmlspecialchars($row['tuk'] ?: '-') ?></td>
                            <td data-label="Hari / Tanggal"><?= htmlspecialchars($row['hari_tanggal'] ?: '-') ?></td>
                            <td data-label="Tgl Selesai"><?= htmlspecialchars($row['tgl_selesai'] ?: '-') ?></td>
                            <td data-label="Asesor"><?= htmlspecialchars($row['nama_asesor'] ?: '(belum ditentukan)') ?></td>
                            <td data-label="Aksi" class="rekap-aksi" style="text-align:center;">
                                <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_AK03.php&id_asesi=<?= $row['id_asesi'] ?>&view=1">Lihat Detail</a>
                                <a class="btn-cetak" href="../pdf/cetak_ak3.php?view=1&id_asesi=<?= $row['id_asesi'] ?>&print=1" target="_blank">Cetak PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
