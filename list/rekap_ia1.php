<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

// Fungsi update rekomendasi
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rekomendasi'])) {
//     $id_ia01 = intval($_POST['id_ia01']);
//     $rekomendasi = $_POST['rekomendasi'] ?? '';
//     $catatan = $_POST['catatan'] ?? '';

//     if (in_array($rekomendasi, ['Kompeten', 'Belum Kompeten'])) {
//         $stmt = mysqli_prepare($koneksi, "UPDATE tb_ia01 SET rekomendasi = ?, umpan_balik = ? WHERE id_ia01 = ?");
//         mysqli_stmt_bind_param($stmt, "ssi", $rekomendasi, $catatan, $id_ia01);
//     } else {
//         $stmt = mysqli_prepare($koneksi, "UPDATE tb_ia01 SET rekomendasi = NULL, umpan_balik = ? WHERE id_ia01 = ?");
//         mysqli_stmt_bind_param($stmt, "si", $catatan, $id_ia01);
//     }
//     mysqli_stmt_execute($stmt);

//     $filter_param = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';
//     header("Location: {$base}?page=../list/rekap_ia1.php{$filter_param}");
//     exit;
// }

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$where = "WHERE 1=1";
if ($filter === 'belum') $where .= " AND (i.rekomendasi IS NULL OR i.rekomendasi = '')";
if ($filter === 'kompeten') $where .= " AND i.rekomendasi = 'Kompeten'";
if ($filter === 'belum_kompeten') $where .= " AND i.rekomendasi = 'Belum Kompeten'";

$sql = "SELECT i.id_ia01, i.id_apl1, i.id_ak01, i.id_asesi,
               i.rekomendasi, i.umpan_balik, i.tanggal,
               s.judul_skema, s.nomor_skema, s.id_skema,
               asi.nama_asesi
        FROM tb_ia01 i
        LEFT JOIN tb_apl1 apl ON apl.id_apl1 = i.id_apl1
        LEFT JOIN tb_skema s  ON s.id_skema  = apl.id_skema
        LEFT JOIN tb_asesi asi ON asi.id_asesi = i.id_asesi
        $where
        GROUP BY i.id_asesi, apl.id_skema
        ORDER BY i.id_ia01 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
$total = count($rows);

$total_all = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia01"))['c'] ?? 0;

$total_belum = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia01
     WHERE rekomendasi IS NULL OR rekomendasi=''"))['c'] ?? 0;

$total_kompeten = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia01
     WHERE rekomendasi='Kompeten'"))['c'] ?? 0;

$total_belum_kompeten = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(DISTINCT id_asesi, id_apl1) c FROM tb_ia01
     WHERE rekomendasi='Belum Kompeten'"))['c'] ?? 0;

$base = '../BERANDA/UTAMA.php';
?>
<style>
    .rekap-wrap { padding: 10px 4px; font-family: Arial, sans-serif; }
    .rekap-title { font-size: 20px; font-weight: bold; color: #1a237e; margin-bottom: 18px; }
    .rekap-cards { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .rekap-card {
        flex: 1; min-width: 130px;
        border-radius: 8px; padding: 14px 16px;
        text-align: center; cursor: pointer;
        text-decoration: none; display: block;
        border: 2px solid transparent;
        transition: border 0.15s;
    }
    .rekap-card:hover { border-color: #4A7AFF; }
    .rekap-card.active { border-color: #4A7AFF !important; }
    .rekap-card .num { font-size: 26px; font-weight: bold; }
    .rekap-card .lbl { font-size: 12px; margin-top: 2px; }
    .card-semua { background: #e8eaf6; color: #1a237e; }
    .card-belum { background: #fff8e1; color: #e65100; }
    .card-kompeten { background: #e8f5e9; color: #2e7d32; }
    .card-belum-kompeten { background: #fce4ec; color: #c62828; }
    .rekap-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rekap-table th {
        background: #cadbfc; padding: 9px 10px;
        border: 1px solid #b0bec5; text-align: center;
    }
    .rekap-table td {
        padding: 8px 10px; border: 1px solid #ddd; vertical-align: middle;
    }
    .rekap-table tr:hover td { background: #f5f7ff; }
    .badge {
        display: inline-block; padding: 2px 10px;
        border-radius: 20px; font-size: 11px; font-weight: bold;
    }
    .badge-belum { background: #fff8e1; color: #e65100; border: 1px solid #ffe082; }
    .badge-kompeten { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .badge-belum-kompeten { background: #fce4ec; color: #c62828; border: 1px solid #f48fb1; }
    .radio-group { margin-bottom: 6px; }
    .radio-group label { margin-right: 12px; font-size: 12px; cursor: pointer; }
    .komentar-textarea {
        width: 180px; padding: 5px; font-size: 11px;
        border-radius: 4px; border: 1px solid #ccc;
        resize: vertical; margin: 5px 0;
    }
    .btn-update {
        background: #f35555; color: white; border: none;
        padding: 5px 12px; border-radius: 4px; font-size: 11px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-lihat {
        background: #4A7AFF; color: white; border: none;
        padding: 5px 12px; border-radius: 4px; font-size: 11px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-update:hover { background: #8f4646; }
    .aksi-form { display: contents; }
    @media (max-width: 700px) {
        .komentar-textarea { width: 130px; }
        .rekap-table { font-size: 11px; }
    }
    .btn-lihat:hover { background: #325fd6; }
</style>

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.IA.01 — Ceklis Observasi Aktivitas</div>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_ia1.php&filter=semua"
           class="rekap-card card-semua <?= $filter==='semua'?'active':'' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Observasi</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia1.php&filter=belum"
           class="rekap-card card-belum <?= $filter==='belum'?'active':'' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia1.php&filter=kompeten"
           class="rekap-card card-kompeten <?= $filter==='kompeten'?'active':'' ?>">
            <div class="num"><?= $total_kompeten ?></div>
            <div class="lbl">Kompeten</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ia1.php&filter=belum_kompeten"
           class="rekap-card card-belum-kompeten <?= $filter==='belum_kompeten'?'active':'' ?>">
            <div class="num"><?= $total_belum_kompeten ?></div>
            <div class="lbl">Belum Kompeten</div>
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
                    <th>Tanggal Observasi</th>
                    <th>Status Rekomendasi</th>
                    <!-- <th>Umpan Balik</th> -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <?php
                    $is_belum = (is_null($r['rekomendasi']) || $r['rekomendasi'] === '');
                ?>
                <?php if ($is_belum): ?>
                <form method="post" class="aksi-form">
                    <input type="hidden" name="id_ia01" value="<?= $r['id_ia01'] ?>">
                    <input type="hidden" name="update_rekomendasi" value="1">
                <?php endif; ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['nama_asesi'] ?? '') ?></td>
                    <td>
                        <?= htmlspecialchars($r['judul_skema'] ?? '') ?>
                        <div style="font-size:11px; color:#888;">No. <?= htmlspecialchars($r['nomor_skema'] ?? '') ?></div>
                    </td>
                    <td style="text-align:center;"><?= htmlspecialchars($r['tanggal'] ?? '') ?></td>
                    <td style="text-align:center;">
                        <?php if ($is_belum): ?>
                            <span class="badge badge-belum">Belum Diproses</span>
                        <?php elseif ($r['rekomendasi'] === 'Kompeten'): ?>
                            <span class="badge badge-kompeten">Kompeten</span>
                        <?php else: ?>
                            <span class="badge badge-belum-kompeten">Belum Kompeten</span>
                        <?php endif; ?>
                    </td>
                    <!-- <td style="max-width:200px;">
                        <php if ($is_belum): ?>
                            <= nl2br(htmlspecialchars($r['umpan_balik'] ?? '')) ?>
                            <textarea name="catatan" class="komentar-textarea" placeholder="Umpan balik (opsional)"><?= htmlspecialchars($r['umpan_balik'] ?? '') ?></textarea>
                        <php else: ?>
                            <= nl2br(htmlspecialchars($r['umpan_balik'] ?? '')) ?>
                        <php endif; ?>
                    </td> -->
                    <td style="text-align:left;">
                            <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_IA1.php&mode=view&id_asesi=<?= $r['id_asesi'] ?>&id_skema=<?= $r['id_skema'] ?>">
                                Lihat
                            </a>
                        <!-- <?php if ($is_belum): ?>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="rekomendasi" value="Kompeten"
                                        <?= ($r['rekomendasi'] === 'Kompeten') ? 'checked' : '' ?>>
                                    Kompeten
                                </label>
                                <label>
                                    <input type="radio" name="rekomendasi" value="Belum Kompeten"
                                        <?= ($r['rekomendasi'] === 'Belum Kompeten') ? 'checked' : '' ?>>
                                    Belum Kompeten
                                </label>
                            </div>
                            <button type="submit" class="btn-update">Update</button>
                        <?php else: ?>
                            <a class="btn-lihat" href="<?= $base ?>?page=../FR_APL/FR_IA1.php&mode=view&id_asesi=<?= $r['id_asesi'] ?>&id_skema=<?= $r['id_skema'] ?>">
                                Lihat
                            </a>
                        <?php endif; ?> -->
                    </td>
                </tr>
                <?php if ($is_belum): ?>
                </form>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="font-size:12px; color:#888; margin-top:8px;">
        Menampilkan <?= $total ?> data
    </div>
    <?php endif; ?>
</div>