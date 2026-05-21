<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include "../koneksi.php";

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

$filter = $_GET['filter'] ?? 'semua';

$where = "WHERE 1=1";
if ($filter === 'belum')          $where .= " AND (i.aspek IS NULL OR i.aspek = '')";
if ($filter === 'tercapai')       $where .= " AND i.aspek = 'tercapai'";
if ($filter === 'belum_tercapai') $where .= " AND i.aspek = 'belum_tercapai'";

$sql = "SELECT i.id_ia06, i.id_apl1, i.id_asesi, i.id_asesor,
               i.aspek, i.umpan_balik,
               s.judul_skema, s.nomor_skema, s.id_skema,
               asi.nama_asesi,
            (SELECT COUNT(*) FROM tb_ia06_jawaban j WHERE j.id_ia06 = i.id_ia06) AS total_jawab
        FROM tb_ia06 i
        LEFT JOIN tb_apl1 apl  ON apl.id_apl1   = i.id_apl1
        LEFT JOIN tb_skema s   ON s.id_skema     = apl.id_skema
        LEFT JOIN tb_asesi asi ON asi.id_asesi   = i.id_asesi
        $where
        GROUP BY i.id_asesi, i.id_apl1
        ORDER BY i.id_ia06 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
$total = count($rows);

$total_all = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia06"))['c'] ?? 0;

$total_belum = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia06
     WHERE aspek IS NULL OR aspek=''"))['c'] ?? 0;

$total_tercapai = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia06
     WHERE aspek='tercapai'"))['c'] ?? 0;

$total_belum_tercapai = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia06
     WHERE aspek='belum_tercapai'"))['c'] ?? 0;

$base = '../BERANDA/UTAMA.php';
?>
<style>
    .rekap-wrap  { padding: 10px 4px; font-family: Arial, sans-serif; }
    .rekap-title { font-size: 20px; font-weight: bold; color: #1a237e; margin-bottom: 18px; }

    .rekap-cards { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .rekap-card  {
        flex: 1; min-width: 130px; border-radius: 8px; padding: 14px 16px;
        text-align: center; cursor: pointer; text-decoration: none; display: block;
        border: 2px solid transparent; transition: border 0.15s;
    }
    .rekap-card:hover  { border-color: #4A7AFF; }
    .rekap-card.active { border-color: #4A7AFF !important; }
    .rekap-card .num   { font-size: 26px; font-weight: bold; }
    .rekap-card .lbl   { font-size: 12px; margin-top: 2px; }
    .card-semua          { background: #e8eaf6; color: #1a237e; }
    .card-belum          { background: #fff8e1; color: #e65100; }
    .card-tercapai       { background: #e8f5e9; color: #2e7d32; }
    .card-belum-tercapai { background: #fce4ec; color: #c62828; }

    .rekap-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rekap-table th {
        background: #cadbfc; padding: 9px 10px;
        border: 1px solid #b0bec5; text-align: center;
    }
    .rekap-table td { padding: 8px 10px; border: 1px solid #ddd; vertical-align: middle; }
    .rekap-table tr:hover td { background: #f5f7ff; }

    .badge               { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
    .badge-belum         { background: #fff8e1; color: #e65100;  border: 1px solid #ffe082; }
    .badge-tercapai      { background: #e8f5e9; color: #2e7d32;  border: 1px solid #a5d6a7; }
    .badge-belum-tercapai{ background: #fce4ec; color: #c62828;  border: 1px solid #f48fb1; }

    .btn-lihat {
        background: #4A7AFF; color: white; border: none;
        padding: 5px 12px; border-radius: 4px; font-size: 11px;
        cursor: pointer; text-decoration: none; display: inline-block;
    }
    .btn-lihat:hover { background: #325fd6; }
    .empty-msg { color: #999; text-align: center; padding: 28px; font-size: 13px; }
</style>

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.IA.06C — Lembar Jawaban Pertanyaan Tertulis Esai</div>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&filter=semua"
           class="rekap-card card-semua <?= $filter==='semua' ? 'active':'' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Jawaban</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&filter=belum"
           class="rekap-card card-belum <?= $filter==='belum' ? 'active':'' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Dinilai</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&filter=tercapai"
           class="rekap-card card-tercapai <?= $filter==='tercapai' ? 'active':'' ?>">
            <div class="num"><?= $total_tercapai ?></div>
            <div class="lbl">Tercapai</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia06.php&filter=belum_tercapai"
           class="rekap-card card-belum-tercapai <?= $filter==='belum_tercapai' ? 'active':'' ?>">
            <div class="num"><?= $total_belum_tercapai ?></div>
            <div class="lbl">Belum Tercapai</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">Tidak ada data untuk filter ini.</div>
    <?php else: ?>
    <div style="overflow-x:auto;">
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
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['nama_asesi'] ?? '') ?></td>
                    <td>
                        <?= htmlspecialchars($r['judul_skema'] ?? '') ?>
                        <div style="font-size:11px; color:#888;">
                            No. <?= htmlspecialchars($r['nomor_skema'] ?? '') ?>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <?= intval($r['total_jawab']) ?> soal dijawab
                    </td>
                    <td style="text-align:center;">
                        <?php if ($is_belum): ?>
                            <span class="badge badge-belum">Belum Dinilai</span>
                        <?php elseif ($r['aspek'] === 'tercapai'): ?>
                            <span class="badge badge-tercapai">Tercapai</span>
                        <?php else: ?>
                            <span class="badge badge-belum-tercapai">Belum Tercapai</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <a class="btn-lihat"
                           href="<?= $base ?>?page=../FR_APL/FR_IA06C.php&mode=view&id_asesi=<?= $r['id_asesi'] ?>">
                            Lihat
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="font-size:12px; color:#888; margin-top:8px;">
        Menampilkan <?= $total ?> data
    </div>
    <?php endif; ?>
</div>