<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$role = $_SESSION['role'] ?? '';
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak. Hanya untuk Asesor, Admin LSP, dan Admin Utama.</p>";
    exit;
}

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
        WHERE 1=1";

if ($role === 'Asesor' && $id_asesor_session) {
    $sql .= " AND ak01.id_asesor = '$id_asesor_session'";
}

$sql .= " ORDER BY ak03.id_ak03 DESC";

$result = mysqli_query($koneksi, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
$total = count($rows);

$f_asesor = ($role === 'Asesor' && $id_asesor_session)
    ? " AND ak01.id_asesor = '$id_asesor_session'"
    : '';

$total_all = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) c
     FROM tb_ak03 ak03
     LEFT JOIN tb_ak01 ak01 ON ak01.id_ak01 = ak03.id_ak01
     WHERE 1=1 $f_asesor"
))['c'] ?? 0;

$base = '../BERANDA/UTAMA.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap FR.AK.03 - Umpan Balik dan Catatan Asesmen</title>
    <link rel="stylesheet" href="../assets/CSS/rekap-shared.css">
    <style>
        .rekap-wrap { padding: 10px 4px; font-family: Arial, sans-serif; }
        .rekap-title { font-size: 20px; font-weight: bold; color: #1a237e; margin-bottom: 18px; }
        .rekap-cards { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
        .rekap-card {
            flex: 1; min-width: 130px;
            border-radius: 8px; padding: 14px 16px;
            text-align: center;
            text-decoration: none; display: block;
            border: 2px solid transparent;
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
        .btn-cetak {
            background: #4caf50; color: white; border: none;
            padding: 5px 14px; border-radius: 5px; font-size: 12px;
            cursor: pointer; text-decoration: none; white-space: nowrap;
            margin-left: 4px;
        }
        .btn-cetak:hover { background: #2e7d32; }
        .empty-msg {
            text-align: center; padding: 30px; background: #f9f9f9;
            border-radius: 8px; color: #666; margin-top: 20px;
        }
        @media (max-width: 700px) {
            .rekap-table { font-size: 11px; }
            .rekap-table th, .rekap-table td { padding: 6px 4px; }
        }
    </style>
</head>
<body>
<div class="rekap-wrap">
    <div class="rekap-title">Rekap FR.AK.03 — Umpan Balik dan Catatan Asesmen</div>

    <div class="rekap-cards">
        <div class="rekap-card">
            <div class="num"><?= $total_all ?></div>
            <div class="lbl">Total Umpan Balik Asesmen</div>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty-msg">
            Belum ada data FR.AK.03 yang tersimpan.
            <?php if ($role === 'Asesor'): ?>
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
                                <a class="btn-lihat"
                                   href="<?= $base ?>?page=../FR_APL/FR_AK03.php&id_asesi=<?= $row['id_asesi'] ?>&view=1">
                                    Lihat Detail
                                </a>
                                <a class="btn-cetak"
                                   href="../pdf/cetak_ak3.php?view=1&id_asesi=<?= $row['id_asesi'] ?>&print=1"
                                   target="_blank">
                                    Cetak PDF
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="rekap-foot">
            Menampilkan <?= $total ?> data
        </div>
    <?php endif; ?>
</div>
</body>
</html>
