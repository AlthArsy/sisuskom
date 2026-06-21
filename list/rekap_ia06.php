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

$sql = "SELECT i.id_ia06, i.id_apl1, i.id_asesi, i.id_asesor,
               i.aspek, i.umpan_balik,
               s.judul_skema, s.nomor_skema, s.id_skema,
               asi.nama_asesi, asr.nama_asesor,
               ak.hari_tanggal,
               (SELECT COUNT(*) FROM tb_ia06_jawaban j WHERE j.id_ia06 = i.id_ia06) AS total_jawab
        FROM tb_ia06 i
        LEFT JOIN tb_apl1 apl  ON apl.id_apl1   = i.id_apl1
        LEFT JOIN tb_skema s   ON s.id_skema     = apl.id_skema
        LEFT JOIN tb_asesi asi ON asi.id_asesi   = i.id_asesi
        LEFT JOIN tb_asesor asr ON asr.id_asesor = i.id_asesor
        LEFT JOIN tb_ak01 ak ON ak.id_apl1 = i.id_apl1 AND ak.id_asesi = i.id_asesi
        WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'i.id_asesor')
    . rekap_sql_filter_status($filter, 'ia06')
    // . rekap_sql_batas_2bulan('ak.hari_tanggal')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$cnt_base = "SELECT COUNT(*) c FROM tb_ia06 i
             LEFT JOIN tb_apl1 apl ON apl.id_apl1 = i.id_apl1
             LEFT JOIN tb_skema s ON s.id_skema = apl.id_skema
             LEFT JOIN tb_asesi asi ON asi.id_asesi = i.id_asesi
             LEFT JOIN tb_ak01 ak ON ak.id_apl1 = i.id_apl1 AND ak.id_asesi = i.id_asesi
             WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'i.id_asesor')
    . rekap_sql_batas_2bulan('ak.hari_tanggal')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$total_all            = rekap_count($koneksi, $cnt_base);
$total_belum          = rekap_count($koneksi, $cnt_base . " AND (i.aspek IS NULL OR i.aspek = '')");
$total_tercapai       = rekap_count($koneksi, $cnt_base . " AND i.aspek = 'tercapai'");
$total_belum_tercapai = rekap_count($koneksi, $cnt_base . " AND i.aspek = 'belum_tercapai'");
$total_selesai        = rekap_count($koneksi, $cnt_base . " AND i.aspek IS NOT NULL AND i.aspek != ''");

$qs = fn($f) => rekap_qs($f, $cari);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.IA.06C — Lembar Jawaban Pertanyaan Tertulis Esai</div>

    <?php rekap_render_cari($base, '../list/rekap_ia06.php', $filter, $cari); ?>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('semua') ?>"
           class="rekap-card card-semua <?= $filter === 'semua' ? 'active' : '' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Jawaban</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('belum') ?>"
           class="rekap-card card-belum <?= $filter === 'belum' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Dinilai</div>
        </a>
        <!-- <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('selesai') ?>"
           class="rekap-card card-selesai <?= $filter === 'selesai' ? 'active' : '' ?>">
            <div class="num"><?= $total_selesai ?></div>
            <div class="lbl">Asesi Selesai + Dinilai</div>
        </a> -->
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('tercapai') ?>"
           class="rekap-card card-tercapai <?= $filter === 'tercapai' ? 'active' : '' ?>">
            <div class="num"><?= $total_tercapai ?></div>
            <div class="lbl">Tercapai</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('belum_tercapai') ?>"
           class="rekap-card card-belum-tercapai <?= $filter === 'belum_tercapai' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum_tercapai ?></div>
            <div class="lbl">Belum Tercapai</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Tidak ada data untuk filter ini.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_ia06.php&<?= $qs('semua') ?>">reset pencarian</a>.
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
                    <th>Jumlah Jawaban</th>
                    <th>Status Aspek</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <?php $is_belum = (is_null($r['aspek']) || $r['aspek'] === ''); ?>
                <tr>
                    <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                    <td data-label="Nama Asesi"><?= htmlspecialchars($r['nama_asesi'] ?? '') ?></td>
                    <td data-label="Skema">
                        <?= htmlspecialchars($r['judul_skema'] ?? '') ?>
                        <div class="rekap-skema-sub">No. <?= htmlspecialchars($r['nomor_skema'] ?? '') ?></div>
                    </td>
                    <td data-label="Jumlah Jawaban" style="text-align:center;">
                        <?= intval($r['total_jawab']) ?> soal dijawab
                    </td>
                    <td data-label="Status Aspek" style="text-align:center;">
                        <?php if ($is_belum): ?>
                            <span class="badge badge-belum">Belum Dinilai</span>
                        <?php elseif ($r['aspek'] === 'tercapai'): ?>
                            <span class="badge badge-tercapai">Tercapai</span>
                        <?php else: ?>
                            <span class="badge badge-belum-tercapai">Belum Tercapai</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Aksi" class="rekap-aksi" style="text-align:center;">
                        <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_IA06C.php&mode=view&id_asesi=<?= $r['id_asesi'] ?>">Lihat</a>
                        <a class="btn-cetak" href="../pdf/cetak_ia6a.php?view=1&id_asesi=<?= $r['id_asesi'] ?>&print=1" target="_blank">Cetak PDF</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
