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

$p      = rekap_params();
$filter = $p['filter'];
$cari   = $p['cari'];
$base   = '../BERANDA/UTAMA.php';

$sql = "SELECT 
            ak.id_ak02,
            ak.id_asesi,
            ak.rekomendasi,
            ak.tindak_lanjut,
            ak.komentar_asesor,
            asi.nama_asesi,
            s.judul_skema,
            s.nomor_skema,
            ak01.tuk AS tuk_asesi,
            ak01.hari_tanggal,
            asr.nama_asesor AS nama_asesor_penilai
        FROM tb_ak02 ak
        LEFT JOIN tb_asesi asi ON asi.id_asesi = ak.id_asesi
        LEFT JOIN tb_apl1 apl ON apl.id_apl1 = ak.id_apl1
        LEFT JOIN tb_skema s ON s.id_skema = apl.id_skema
        LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak.id_ak01
        LEFT JOIN tb_asesor asr ON asr.id_asesor = ak.id_asesor
        WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'ak.id_asesor')
    . rekap_sql_filter_status($filter, 'ak02')
    // . rekap_sql_batas_2bulan('ak01.hari_tanggal')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$sql .= " ORDER BY ak.id_ak02 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$cnt_base = "SELECT COUNT(*) c FROM tb_ak02 ak
             LEFT JOIN tb_asesi asi ON asi.id_asesi = ak.id_asesi
             LEFT JOIN tb_apl1 apl ON apl.id_apl1 = ak.id_apl1
             LEFT JOIN tb_skema s ON s.id_skema = apl.id_skema
             LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak.id_ak01
             WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'ak.id_asesor')
    // . rekap_sql_batas_2bulan('ak01.hari_tanggal')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$total_all        = rekap_count($koneksi, $cnt_base);
$total_belum      = rekap_count($koneksi, $cnt_base . " AND (ak.rekomendasi IS NULL OR ak.rekomendasi='')");
$total_kompeten   = rekap_count($koneksi, $cnt_base . " AND ak.rekomendasi='Kompeten'");
$total_belum_komp = rekap_count($koneksi, $cnt_base . " AND ak.rekomendasi='Belum Kompeten'");
$total_selesai    = rekap_count($koneksi, $cnt_base . " AND ak.rekomendasi IS NOT NULL AND ak.rekomendasi != ''");

$qs = fn($f) => rekap_qs($f, $cari);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.AK.02 — Rekaman Asesmen Kompetensi</div>

    <?php rekap_render_cari($base, '../list/rekap_ak02.php', $filter, $cari); ?>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('semua') ?>"
           class="rekap-card card-semua <?= $filter === 'semua' ? 'active' : '' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Asesmen</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('belum') ?>"
           class="rekap-card card-belum <?= $filter === 'belum' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <!-- <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('selesai') ?>"
           class="rekap-card card-selesai <?= $filter === 'selesai' ? 'active' : '' ?>">
            <div class="num"><?= $total_selesai ?></div>
            <div class="lbl">Sudah Direkomendasikan</div>
        </a> -->
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('kompeten') ?>"
           class="rekap-card card-kompeten <?= $filter === 'kompeten' ? 'active' : '' ?>">
            <div class="num"><?= $total_kompeten ?></div>
            <div class="lbl">Kompeten</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('belum_kompeten') ?>"
           class="rekap-card card-belumkompeten <?= $filter === 'belum_kompeten' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum_komp ?></div>
            <div class="lbl">Belum Kompeten</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Tidak ada data untuk filter ini.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_ak02.php&<?= $qs('semua') ?>">reset pencarian</a>.
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
                    <th>Rekomendasi</th>
                    <th>Tindak Lanjut</th>
                    <th>Asesor Penilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <?php
                    $rekom = $r['rekomendasi'] ?? '';
                    $is_belum = empty($rekom);
                    if ($rekom === 'Kompeten') {
                        $badge_class = 'badge-kompeten';
                    } elseif ($rekom === 'Belum Kompeten') {
                        $badge_class = 'badge-belumkompeten';
                    } else {
                        $badge_class = 'badge-belum';
                    }
                    $badge_text = $rekom ?: 'Belum Diproses';
                ?>
                <tr>
                    <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                    <td data-label="Nama Asesi"><?= htmlspecialchars($r['nama_asesi']) ?></td>
                    <td data-label="Skema Sertifikasi">
                        <?= htmlspecialchars($r['judul_skema']) ?>
                        <div class="rekap-skema-sub">No. <?= htmlspecialchars($r['nomor_skema']) ?></div>
                    </td>
                    <td data-label="TUK"><?= htmlspecialchars($r['tuk_asesi'] ?: '-') ?></td>
                    <td data-label="Rekomendasi" style="text-align:center;">
                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($badge_text) ?></span>
                    </td>
                    <td data-label="Tindak Lanjut" style="max-width:200px;"><?= nl2br(htmlspecialchars($r['tindak_lanjut'] ?? '-')) ?></td>
                    <td data-label="Asesor Penilai"><?= htmlspecialchars($r['nama_asesor_penilai'] ?: '-') ?></td>
                    <td data-label="Aksi" class="rekap-aksi" style="text-align:center;">
                        <?php if ($role === 'Asesor'): ?>
                        <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_AK02.php&id_asesi=<?= $r['id_asesi'] ?>&mode=edit">Lihat</a>
                        <?php elseif ($role === 'Admin_lsp' || $role === 'Admin_utm'): ?>
                        <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_AK02.php&id_asesi=<?= $r['id_asesi'] ?>&mode=view">Lihat</a>
                        <?php endif; ?>
                        <a class="btn-cetak" href="../pdf/cetak_ak2.php?view=1&id_asesi=<?= $r['id_asesi'] ?>&print=1" target="_blank">Cetak PDF</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
