<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

 $role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

 $filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$where = "WHERE 1=1";
if ($id_asesor_session) $where .= " AND a.id_asesor = '$id_asesor_session'";
if ($filter === 'belum') $where .= " AND (a.rekomendasi IS NULL OR a.rekomendasi = '')";
if ($filter === 'Dapat') $where .= " AND a.rekomendasi = 'Dapat'";
if ($filter === 'Tidak Dapat')  $where .= " AND a.rekomendasi = 'Tidak Dapat'";

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
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
$total  = count($rows);

$f = $id_asesor_session ? "WHERE id_asesor='$id_asesor_session'" : "WHERE 1=1";

$total_all    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_apl2 $f"))['c'] ?? 0;
$total_belum  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_apl2 $f AND (rekomendasi IS NULL OR rekomendasi='')"))['c'] ?? 0;
$total_terima = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_apl2 $f AND rekomendasi='Dapat'"))['c'] ?? 0;
$total_tolak  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_apl2 $f AND rekomendasi='Tidak Dapat'"))['c'] ?? 0;

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
    .card-diterima { background: #e8f5e9; color: #2e7d32; }
    .card-ditolak { background: #fce4ec; color: #c62828; }

     .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .filter-btn {
        padding: 6px 16px; border-radius: 20px; font-size: 13px;
        text-decoration: none; border: 1.5px solid #ddd;
        background: #fff; color: #555;
    }
    .filter-btn.active { background: #4A7AFF; color: #fff; border-color: #4A7AFF; }

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
    .badge-diterima { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .badge-ditolak { background: #fce4ec; color: #c62828; border: 1px solid #f48fb1; }

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
    <div class="rekap-title">Rekap APL 2 — ASESMEN MANDIRI</div>

    <div class="rekap-cards">
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&filter=semua"
           class="rekap-card card-semua <?= $filter==='semua'?'active':'' ?>">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Pengajuan</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&filter=belum"
           class="rekap-card card-belum <?= $filter==='belum'?'active':'' ?>">
            <div class="num"><?= $total_belum ?></div>
            <div class="lbl">Belum Diproses</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&filter=Dapat"
           class="rekap-card card-diterima <?= $filter==='Dapat'?'active':'' ?>">
            <div class="num"><?= $total_terima ?></div>
            <div class="lbl">Dapat</div>
        </a>
        <a href="<?= $base ?>?page=../list/rekap_frapl2.php&filter=Tidak Dapat"
           class="rekap-card card-ditolak <?= $filter==='Tidak Dapat'?'active':'' ?>">
            <div class="num"><?= $total_tolak ?></div>
            <div class="lbl">Tidak Dapat</div>
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
                    <th>Tanggal Submit</th>
                    <th>Status Rekomendasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['nama_asesi'] ?: $r['nama_pemohon']) ?></td>
                    <td>
                        <?= htmlspecialchars($r['judul_skema']) ?>
                        <div style="font-size:11px; color:#888;">No. <?= htmlspecialchars($r['nomor_skema']) ?></div>
                    </td>
                    <td style="text-align:center;"><?= $r['tanggal_pemohon'] ?></td>
                    <td style="text-align:center;">
                        <?php if (empty($r['rekomendasi'])): ?>
                            <span class="badge badge-belum">Belum Diproses</span>
                        <?php elseif ($r['rekomendasi'] === 'Dapat'): ?>
                            <span class="badge badge-diterima">Dapat</span>
                        <?php else: ?>
                            <span class="badge badge-ditolak">Tidak Dapat</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        <a class="btn-lihat"
                           href="<?= $base ?>?page=../FR_APL/FR_APL02.php&view=1&id_asesi=<?= $r['id_asesi'] ?>">
                        Lihat
                        </a>
                        <?php if (empty($r['rekomendasi'])): ?>
                        <a class="btn-rek"
                           href="<?= $base ?>?page=../FR_APL/FR_APL02.php&view=1&id_asesi=<?= $r['id_asesi'] ?>#form-rek">
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
