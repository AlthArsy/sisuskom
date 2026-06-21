<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
require_once __DIR__ . '/rekap_helpers.php';

$role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

$p      = rekap_params();
$filter = $p['filter'];
$cari   = $p['cari'];
$base   = '../BERANDA/UTAMA.php';

$where = "WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'a.id_asesor')
    . rekap_sql_filter_status($filter, 'apl2')
    // . rekap_sql_batas_2bulan('ap1.tanggal_pemohon')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$sql = "SELECT a.id_apl2, a.id_apl1, a.id_asesi, a.id_asesor,
               a.rekomendasi, a.tertanda,
               asi.nama_asesi,
               s.judul_skema, s.nomor_skema,
               ap1.tanggal_pemohon
        FROM tb_apl2 a
        LEFT JOIN tb_asesi asi ON asi.id_asesi = a.id_asesi
        LEFT JOIN tb_apl1 ap1 ON ap1.id_apl1 = a.id_apl1
        LEFT JOIN tb_skema s ON s.id_skema = ap1.id_skema
        $where
        ORDER BY a.id_apl2 DESC";

$result = mysqli_query($koneksi, $sql);
$rows   = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$cnt_base = "SELECT COUNT(*) c FROM tb_apl2 a
             LEFT JOIN tb_asesi asi ON asi.id_asesi = a.id_asesi
             LEFT JOIN tb_apl1 ap1 ON ap1.id_apl1 = a.id_apl1
             LEFT JOIN tb_skema s ON s.id_skema = ap1.id_skema
             WHERE 1=1"
    . rekap_sql_asesor($role, $id_asesor_session, 'a.id_asesor')
    // . rekap_sql_batas_2bulan('ap1.tanggal_pemohon')
    . rekap_sql_cari($koneksi, $cari, ['asi.nama_asesi', 's.judul_skema', 's.nomor_skema']);

$total_all     = rekap_count($koneksi, $cnt_base);
$total_belum   = rekap_count($koneksi, $cnt_base . " AND (a.rekomendasi IS NULL OR a.rekomendasi='')");
$total_terima  = rekap_count($koneksi, $cnt_base . " AND a.rekomendasi='Dapat'");
$total_tolak   = rekap_count($koneksi, $cnt_base . " AND a.rekomendasi='Tidak Dapat'");
$total_selesai = rekap_count($koneksi, $cnt_base . " AND a.tertanda IS NOT NULL AND a.tertanda != '' AND a.rekomendasi IS NOT NULL AND a.rekomendasi != ''");

$qs = fn($f) => rekap_qs($f, $cari);
?>
<link rel="stylesheet" href="../assets/CSS/rekap-shared.css">

<div class="rekap-wrap">
    <div class="rekap-title">Rekap APL 2 — Asesmen Mandiri</div>

    <?php rekap_render_cari($base, '../list/rekap_frapl2.php', $filter, $cari); ?>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('semua') ?>"
           class="rekap-card card-semua <?= $filter === 'semua' ? 'active' : '' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Pengajuan</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('belum') ?>"
           class="rekap-card card-belum <?= $filter === 'belum' ? 'active' : '' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <!-- <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('selesai') ?>"
           class="rekap-card card-selesai <?= $filter === 'selesai' ? 'active' : '' ?>">
            <div class="num"><?= $total_selesai ?></div>
            <div class="lbl">Asesi Selesai + Rekomendasi</div>
        </a> -->
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('Dapat') ?>"
           class="rekap-card card-diterima <?= $filter === 'Dapat' ? 'active' : '' ?>">
            <div class="num"><?= $total_terima ?></div>
            <div class="lbl">Dapat</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('Tidak Dapat') ?>"
           class="rekap-card card-ditolak <?= $filter === 'Tidak Dapat' ? 'active' : '' ?>">
            <div class="num"><?= $total_tolak ?></div>
            <div class="lbl">Tidak Dapat</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Tidak ada data untuk filter ini.
            <?php if ($cari !== ''): ?>
                <br>Coba kata kunci lain atau <a href="<?= $base ?>?page=../list/rekap_frapl2.php&<?= $qs('semua') ?>">reset pencarian</a>.
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td data-label="No." style="text-align:center;"><?= $i + 1 ?></td>
                    <td data-label="Nama Asesi"><?= htmlspecialchars($r['nama_asesi'] ?? '') ?></td>
                    <td data-label="Skema">
                        <?= htmlspecialchars($r['judul_skema']) ?>
                        <div class="rekap-skema-sub">No. <?= htmlspecialchars($r['nomor_skema']) ?></div>
                    </td>
                    <td data-label="Tanggal Submit" style="text-align:center;"><?= htmlspecialchars($r['tanggal_pemohon'] ?? '-') ?></td>
                    <td data-label="Status Rekomendasi" style="text-align:center;">
                        <?php if (empty($r['rekomendasi'])): ?>
                            <span class="badge badge-belum">Belum Diproses</span>
                        <?php elseif ($r['rekomendasi'] === 'Dapat'): ?>
                            <span class="badge badge-diterima">Dapat</span>
                        <?php else: ?>
                            <span class="badge badge-ditolak">Tidak Dapat</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Aksi" class="rekap-aksi" style="text-align:center;">
                        <?php if ($role === 'Asesor'): ?>
                        <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_APL02.php&edit=1&id_asesi=<?= $r['id_asesi'] ?>">Lihat</a>
                        <?php elseif ($role === 'Admin_lsp' || $role === 'Admin_utm'): ?>
                        <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_APL02.php&view=1&id_asesi=<?= $r['id_asesi'] ?>">Lihat</a>
                        <?php endif; ?>
                        <a class="btn-cetak" href="../pdf/cetak_apl2.php?view=1&id_asesi=<?= $r['id_asesi'] ?>&print=1" target="_blank">Cetak PDF</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="rekap-foot">Menampilkan <?= $total ?> data</div>
    <?php endif; ?>
</div>
