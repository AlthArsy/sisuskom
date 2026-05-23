<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Admin_lsp', 'Admin_utm', 'Asesor'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak. Hanya untuk Admin LSP, Admin UTM, dan Asesor.</p>";
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$sql = "SELECT 
            ak.id_ak01,
            ak.id_asesi,
            ak.tuk,
            ak.hari_tanggal,
            ak.waktu,
            ak.tuk_pelaksanaan,
            a.nama_asesi,
            apl.judul_skema,
            apl.nomor_skema,
            asr.nama_asesor
        FROM tb_ak01 ak
        JOIN tb_asesi a ON a.id_asesi = ak.id_asesi
        JOIN tb_apl1 apl ON apl.id_apl1 = ak.id_apl1
        LEFT JOIN tb_asesor asr ON asr.id_asesor = ak.id_asesor
        ORDER BY ak.id_ak01 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$total_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM tb_ak01"))['c'] ?? 0;

$base = '../BERANDA/UTAMA.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap FR.AK.01 - Persetujuan Asesmen</title>
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
            background: #e8eaf6; color: #1a237e;
        }
        .rekap-card .num { font-size: 26px; font-weight: bold; }
        .rekap-card .lbl { font-size: 12px; margin-top: 2px; }
        .rekap-table {
            width: 100%; border-collapse: collapse; font-size: 13px;
            margin-top: 10px;
        }
        .rekap-table th {
            background: #cadbfc; padding: 9px 10px;
            border: 1px solid #b0bec5; text-align: center;
        }
        .rekap-table td {
            padding: 8px 10px; border: 1px solid #ddd; vertical-align: middle;
        }
        .rekap-table tr:hover td { background: #f5f7ff; }
        .btn-lihat {
            background: #4A7AFF; color: white; border: none;
            padding: 5px 12px; border-radius: 4px; font-size: 11px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-lihat:hover { background: #325fd6; }
        .empty-msg {
            text-align: center; padding: 30px; background: #f9f9f9;
            border-radius: 8px; color: #666; margin-top: 20px;
        }
        @media (max-width: 700px) {
            .rekap-table { font-size: 11px; }
            .rekap-table th, .rekap-table td { padding: 6px 4px; }
        }
        .badge {
            display: inline-block; padding: 2px 10px;
            border-radius: 20px; font-size: 11px; font-weight: bold;
            background: #e8eaf6; color: #1a237e;
        }
    </style>
</head>
<body>
<div class="rekap-wrap">
    <div class="rekap-title">   Rekap FR.AK.01 — Persetujuan Asesmen dan Kerahasiaan</div>

    <div class="rekap-cards">
        <div class="rekap-card">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Persetujuan Asesmen</div>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Belum ada data FR.AK.01 yang tersimpan.
            <?php if ($role === 'Asesor'): ?>
                <br><br>Asesi harus mengisi FR.AK.01 setelah APL 1 disetujui.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Asesi</th>
                        <th>Skema Sertifikasi</th>
                        <th>TUK</th>
                        <th>Hari / Tanggal</th>
                        <th>Waktu</th>
                        <th>TUK Pelaksanaan</th>
                        <th>Asesor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td style="text-align:center;"><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['nama_asesi']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['judul_skema']) ?>
                                <div style="font-size:11px; color:#888;">No. <?= htmlspecialchars($row['nomor_skema']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($row['tuk'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['hari_tanggal'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['waktu'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['tuk_pelaksanaan'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['nama_asesor'] ?: '(belum ditentukan)') ?></td>
                            <td style="text-align:center;">
                                <a class="btn-lihat" 
                                   href="<?= $base ?>?page=../FR_APL/FR_AK01.php&id_asesi=<?= $row['id_asesi'] ?>&view=1">
                                    Lihat Detail
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
</body>
</html>