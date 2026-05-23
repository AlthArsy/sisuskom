<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak. Hanya untuk Asesor, Admin LSP, dan Admin UTM.</p>";
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

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
            asr.nama_asesor AS nama_asesor_penilai
        FROM tb_ak02 ak
        LEFT JOIN tb_asesi asi ON asi.id_asesi = ak.id_asesi
        LEFT JOIN tb_apl1 apl ON apl.id_apl1 = ak.id_apl1
        LEFT JOIN tb_skema s ON s.id_skema = apl.id_skema
        LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak.id_ak01
        LEFT JOIN tb_asesor asr ON asr.id_asesor = ak.id_asesor
        WHERE 1=1";

if ($role === 'Asesor' && $id_asesor_session) {
    $sql .= " AND ak.id_asesor = '$id_asesor_session'";
}

if ($filter === 'belum') {
    $sql .= " AND (ak.rekomendasi IS NULL OR ak.rekomendasi = '')";
} elseif ($filter === 'kompeten') {
    $sql .= " AND ak.rekomendasi = 'Kompeten'";
} elseif ($filter === 'belum_kompeten') {
    $sql .= " AND ak.rekomendasi = 'Belum Kompeten'";
}

$sql .= " ORDER BY ak.id_ak02 DESC";
$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$where_role = ($role === 'Asesor' && $id_asesor_session) ? "WHERE id_asesor = '$id_asesor_session'" : "";
$total_all    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_ak02 $where_role"))['c'] ?? 0;
$total_belum  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_ak02 $where_role AND (rekomendasi IS NULL OR rekomendasi='')"))['c'] ?? 0;
$total_kompeten   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_ak02 $where_role AND rekomendasi='Kompeten'"))['c'] ?? 0;
$total_belum_komp = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_ak02 $where_role AND rekomendasi='Belum Kompeten'"))['c'] ?? 0;

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
    .card-belumkompeten { background: #fce4ec; color: #c62828; }

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
    .badge-belumkompeten { background: #fce4ec; color: #c62828; border: 1px solid #f48fb1; }

    .btn-lihat {
        background: #4A7AFF; color: #fff; border: none;
        padding: 5px 14px; border-radius: 5px; font-size: 12px;
        cursor: pointer; text-decoration: none; white-space: nowrap;
    }
    .btn-lihat:hover { background: #325fd6; }
    .btn-rek {
        background: #ff9800; color: #fff; border: none;
        padding: 5px 14px; border-radius: 5px; font-size: 12px;
        cursor: pointer; text-decoration: none; white-space: nowrap; margin-left: 4px;
    }
    .btn-rek:hover { background: #e65100; }

    .empty-msg { text-align: center; padding: 30px; color: #aaa; font-size: 14px; }

    @media (max-width: 600px) {
        .rekap-cards { flex-direction: row; }
        .rekap-table { font-size: 11px; }
        .btn-lihat, .btn-rek { padding: 4px 8px; font-size: 11px; }
    }
</style>

<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.AK.02 — REKAMAN ASESMEN KOMPETENSI</div>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&filter=semua"
           class="rekap-card card-semua <?= $filter==='semua'?'active':'' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Asesmen</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&filter=belum"
           class="rekap-card card-belum <?= $filter==='belum'?'active':'' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&filter=kompeten"
           class="rekap-card card-kompeten <?= $filter==='kompeten'?'active':'' ?>">
            <div class="num"><?= $total_kompeten ?></div>
            <div class="lbl">Kompeten</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_ak02.php&filter=belum_kompeten"
           class="rekap-card card-belumkompeten <?= $filter==='belum_kompeten'?'active':'' ?>">
            <div class="num"><?= $total_belum_komp ?></div>
            <div class="lbl">Belum Kompeten</div>
        </a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">Tidak ada data FR.AK.02 untuk filter ini.</div>
    <?php else: ?>
    <div style="overflow-x:auto;">
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
                    if ($rekom === 'Kompeten') $badge_class = 'badge-kompeten';
                    elseif ($rekom === 'Belum Kompeten') $badge_class = 'badge-belumkompeten';
                    else $badge_class = 'badge-belum';
                    $badge_text = $rekom ?: 'Belum Diproses';
                ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['nama_asesi']) ?></td>
                    <td>
                        <?= htmlspecialchars($r['judul_skema']) ?>
                        <div style="font-size:11px; color:#888;">No. <?= htmlspecialchars($r['nomor_skema']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($r['tuk_asesi'] ?: '-') ?></td>
                    <td style="text-align:center;">
                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($badge_text) ?></span>
                    </td>
                    <td style="max-width:200px;"><?= nl2br(htmlspecialchars($r['tindak_lanjut'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars($r['nama_asesor_penilai'] ?: '-') ?></td>
                    <td style="text-align:center; white-space:nowrap;">
                        <a class="btn-lihat"
                           href="<?= $base ?>?page=../FR_APL/FR_AK02.php&id_asesi=<?= $r['id_asesi'] ?>&mode=view">
                            Lihat
                        </a>
                        <?php if ($is_belum && in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])): ?>
                        <a class="btn-rek"
                           href="<?= $base ?>?page=../FR_APL/FR_AK02.php&id_asesi=<?= $r['id_asesi'] ?>&mode=edit">
                            Isi Data
                        </a>
                        <?php endif; ?>
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