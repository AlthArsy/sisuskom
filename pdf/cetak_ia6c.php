<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Asesor','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function generate_qr($data, $size = 85) {
    $encoded = urlencode($data);
    return "https://quickchart.io/qr?text={$encoded}&size={$size}x{$size}&margin=2";
}

$id_asesi = isset($_GET['id_asesi']) ? intval($_GET['id_asesi']) : 0;
if (!$id_asesi) die("ID Asesi tidak valid.");

$apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT a.id_apl1, a.id_skema, a.judul_skema, a.nomor_skema,
            s.standar_kompetensi_kerja,
            as2.nama_asesor, as2.no_reg, as2.id_asesor
     FROM tb_apl1 a
     JOIN tb_skema s ON s.id_skema = a.id_skema
     LEFT JOIN tb_asesor as2 ON as2.id_asesor = s.id_asesor
     WHERE a.id_asesi = '$id_asesi'
     ORDER BY a.id_apl1 ASC LIMIT 1"));

if (!$apl1) die("Data APL-01 tidak ditemukan.");

$id_apl1 = $apl1['id_apl1'];
$judul_skema = $apl1['judul_skema'];
$nomor_skema = $apl1['nomor_skema'];
$id_skema = $apl1['id_skema'];

$asesi_data = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT nama_asesi, nik, jenis_kelamin, phone_rumah
     FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
$nama_asesi = $asesi_data['nama_asesi'] ?? '';


$ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT tuk, hari_tanggal, waktu FROM tb_ak01
     WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1'
     ORDER BY id_ak01 DESC LIMIT 1"));
$tuk = $ak01['tuk'] ?? '-';
$tanggal = $ak01['hari_tanggal'] ?? '';
$waktu = $ak01['waktu'] ?? '';

$skema_asesor = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT ar.nama_asesor, ar.no_reg
     FROM tb_skema s
     JOIN tb_asesor ar ON s.id_asesor = ar.id_asesor
     WHERE s.id_skema='$id_skema' LIMIT 1"));
$nama_asesor = $skema_asesor['nama_asesor'] ?? '-';
$no_reg_asesor = $skema_asesor['no_reg'] ?? '-';

$ia06a = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT id_ia06a FROM tb_ia06a
     WHERE id_skema='$id_skema' AND id_asesor = (SELECT id_asesor FROM tb_skema WHERE id_skema='$id_skema')"));
$id_ia06a = $ia06a['id_ia06a'] ?? 0;

$soal_list = [];
if ($id_ia06a) {
    $res = mysqli_query($koneksi,
        "SELECT id_soal, soal FROM tb_soal
         WHERE id_ia06a='$id_ia06a' ORDER BY id_soal ASC");
    $no = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        $row['no_urut'] = $no++;
        $soal_list[] = $row;
    }
}
$ia06 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT id_ia06, aspek, umpan_balik FROM tb_ia06
     WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1' AND id_ia06a='$id_ia06a'
     LIMIT 1"));
$id_ia06 = $ia06['id_ia06'] ?? 0;
$aspek = $ia06['aspek'] ?? '';
$umpan_balik = $ia06['umpan_balik'] ?? '';

$jawaban = [];
if ($id_ia06) {
    $res_jawab = mysqli_query($koneksi,
        "SELECT id_soal, jawaban_asesi FROM tb_ia06_jawaban
         WHERE id_ia06='$id_ia06'");
    while ($j = mysqli_fetch_assoc($res_jawab)) {
        $jawaban[$j['id_soal']] = $j['jawaban_asesi'];
    }
}
$qr_asesi  = "Nama: {$nama_asesi}\nNIK: {$asesi_data['nik']}\nTUK: {$ak01['tuk']}\nTanggal: {$ak01['hari_tanggal']}";
$qr_asesor = "Asesor: {$apl1['nama_asesor']}\nNo.Reg: {$apl1['no_reg']}\nID Skema: {$apl1['nomor_skema']}";

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
$tuk_html = implode('/', array_map(
    fn($t) => ($t ===  h($ak01['tuk']) ) ? "<u>$t</u>" : "<s style='color:#aaa;'>$t</s>",
    $tuk_opts
)) . '*';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak FR.IA.06A - Pertanyaan Tertulis Esai</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Calibri, Arial, sans-serif; font-size:10pt; background:#bbb; color:#000; }

.toolbar {
    position: sticky; top: 0; z-index: 999;
    background: #1565c0;
    padding: 8px 16px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.toolbar-label { color:#fff; font-size:13px; font-weight:bold; margin-right:4px; }
.mode-btn {
    padding: 6px 18px; border-radius: 20px; border: 2px solid #fff;
    background: transparent; color: #fff; font-size: 12px;
    cursor: pointer; font-weight: bold; transition: all .2s;
}
.mode-btn.active  { background: #fff; color: #1565c0; }
.mode-btn:hover:not(.active) { background: rgba(255,255,255,.2); }
.toolbar-sep { flex:1; }
.btn-print {
    background:#fff; color:#1565c0; border:none;
    padding:7px 22px; border-radius:4px; font-size:13px;
    font-weight:bold; cursor:pointer;
}
.btn-print:hover { background:#e3f2fd; }
.btn-back { color:#90caf9; font-size:12px; text-decoration:none; }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .page {
            padding: 10mm 12mm 15mm 18mm;
            width: 100%;
            min-height: 297mm;
            background: white;
            page-break-after: always;
            position: relative;
        }
        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            border: 1.5px solid #000;
            padding: 6px;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 12px;
        }
        .info-table td, .info-table th {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        .label-cell {
            width: 30%;
            background: #f9f9f9;
        }
        .validator-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 20px;
        }
        .validator-table th, .validator-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .validator-table th {
            background: #eef2fc;
        }
        .soal-item {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .soal-no {
            font-weight: bold;
            display: inline-block;
            width: 28px;
        }
        .qr-container {
            float: right;
            text-align: center;
            margin-left: 10px;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        .clearfix { clear: both; }
        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-family: inherit;
        }
        .btn-print {
            background: #1565c0;
            color: white;
        }
        .label-cell {
            width: 30%;
            background: #f9f9f9;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            text-decoration: none;
        }
                .soal-item {
            margin-bottom: 14px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .soal-no {
            font-weight: bold;
            display: inline-block;
            width: 28px;
        }
                .umpan-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 16px 0;
        }
        .jawaban-area {
            margin-top: 6px;
            margin-left: 10px;
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 60px;
            background: #fefefe;
        }
    .unit-wrapper { page-break-inside:avoid; break-inside:avoid; }

.tbl-ttd { width:100%; border-collapse:collapse; border:1px solid #000; margin-top:16px; }
.tbl-ttd td { border:1px solid #000; padding:8px 10px; vertical-align:top; }
.ttd-area { min-height: 80px; position: relative; }
.ttd-manual-space { height: 50px; display: block; }
.ttd-qr-box { display: none; justify-content: center; align-items: center; padding: 4px 0; }
.ttd-qr-box canvas,
.ttd-qr-box img { width:80px !important; height:80px !important; }
.ttd-nama-line { border-top:1px solid #000; padding-top:4px; margin-top:6px; font-size:9pt; }

.mode-badge {
    display:inline-block; font-size:8pt; padding:1px 6px;
    border-radius:10px; margin-left:6px; vertical-align:middle;
    font-weight:normal;
}
.btn-print {
    background:#fff; color:#1565c0; border:none;
    padding:7px 22px; border-radius:4px; font-size:13px;
    font-weight:bold; cursor:pointer;
}
.btn-print:hover { background:#e3f2fd; }
.btn-back { color:#90caf9; font-size:12px; text-decoration:none; }
        @media print {
                body { background:#fff; }
    .toolbar { display:none !important; }
            body { background: white; padding: 0; margin: 0; }
            .print-container { box-shadow: none; margin: 0; }
            .action-buttons { display: none; }
            .page { padding: 10mm 12mm 10mm 16mm; }
            .bukti-grid { break-inside:avoid; }
    .tbl-ttd { break-inside:avoid; }
        }
    s { text-decoration: line-through; color: #aaa; }
    u { text-decoration: underline; }
    </style>
</head>
<body>

<div class="toolbar">
    <span class="toolbar-label">Mode Tanda Tangan :</span>
    <button class="mode-btn active" id="btn-ttd"    onclick="setMode('ttd')">Tanda Tangan</button>
    <button class="mode-btn"        id="btn-qr"     onclick="setMode('qr')">QR Code</button>
    <span class="toolbar-sep"></span>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn-back" href="javascript:history.back()">← Kembali</a>
</div>

<div class="unit-wrapper">

<div class="print-container">
    <div class="page">

        <div class="judul">FR.IA.06A DPT - PERTANYAAN TERTULIS ESAI</div>
        <div class="clearfix"></div>

        <table class="info-table">
            <tr><td class="label-cell">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                <td colspan="1">Judul: </td><td> <?= h($judul_skema) ?></td>
            </tr>
            <tr><td class="label-cell"></td>
                <td colspan="1">Nomor: </td><td><?= h($nomor_skema) ?></td>
            </tr>
            <tr>
                <td class="th-kiri">TUK</td>
                <td colspan="3"><?= $tuk_html ?></td>
            </tr>
            <tr><td class="label-cell">Nama Asesor</td>
                <td colspan="3"><?= h($nama_asesor) ?> (<?= h($no_reg_asesor) ?>)</td>
            </tr>
            <tr><td class="label-cell">Nama Asesi</td>
                <td colspan="3"><?= h($nama_asesi) ?></td>
            </tr>
            <tr><td class="label-cell">Tanggal</td>
                <td colspan="3"><?= h($tanggal) ?></td>
            </tr>
            <tr><td class="label-cell">Waktu</td>
                <td colspan="3"><?= h($waktu) ?></td>
            </tr>
        </table>

        <div style="font-weight:bold; margin:16px 0 10px;">SOAL :</div>

        <?php if (empty($soal_list)): ?>
            <div style="color:#999; text-align:center; padding:20px;">Belum ada pertanyaan untuk skema ini.</div>
        <?php else: ?>
            <?php foreach ($soal_list as $s): ?>
                <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                <div class="soal-item">
                    <div> <span class="soal-text"><?= h($s['soal']) ?></span></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <table class="validator-table">
            <thead>
                <tr><th>STATUS</th><th>NAMA</th><th>NOMOR MET</th><th>TANDA TANGAN DAN TANGGAL</th></tr>
            </thead>
            <tbody>
                <tr><td>PENYUSUN</td><td><?= h($nama_asesor) ?></td><td><?= h($no_reg_asesor) ?></td><td style="height:35px;"></td></tr>
                <tr><td>VALIDATOR</td><td>-</td><td>-</td><td></td></tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<div class="unit-wrapper">
<div class="print-container">
    <div class="page">

        <div class="judul">FR.IA.06C DPT – LEMBAR JAWABAN PERTANYAAN TERTULIS ESAI</div>
        <div class="clearfix"></div>

        <table class="info-table">
            <tr><td class="label-cell">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                <td colspan="1">Judul: </td><td> <?= h($judul_skema) ?></td>
            </tr>
            <tr><td class="label-cell"></td>
                <td colspan="1">Nomor: </td><td><?= h($nomor_skema) ?></td>
            </tr>
            <tr>
                <td class="th-kiri">TUK</td>
                <td colspan="3"><?= $tuk_html ?></td>
            </tr>
            <tr><td class="label-cell">Nama Asesor</td>
                <td colspan="3"><?= h($nama_asesor) ?> (<?= h($no_reg_asesor) ?>)</td>
            </tr>
            <tr><td class="label-cell">Nama Asesi</td>
                <td colspan="3"><?= h($nama_asesi) ?></td>
            </tr>
            <tr><td class="label-cell">Tanggal</td>
                <td colspan="3"><?= h($tanggal) ?></td>
            </tr>
            <tr><td class="label-cell">Waktu</td>
                <td colspan="3"><?= h($waktu) ?></td>
            </tr>
        </table>

        <div style="font-weight:bold; margin:16px 0 10px;">JAWABAN :</div>
        <?php if (empty($soal_list)): ?>
            <div style="color:#999;">Belum ada pertanyaan.</div>
        <?php else: ?>
            <?php foreach ($soal_list as $s):
                $jwb = $jawaban[$s['id_soal']] ?? '';
            ?>
            <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                                <div class="jawaban-area">
                        <?= $jwb ? nl2br(h($jwb)) : '<span style="color:#aaa;">(Belum diisi)</span>' ?>
                    </div>
                <!-- <div class="soal-item">
                    <div></span> <span class="soal-text"><?= h($s['soal']) ?></span></div>

                </div> -->
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="sub-judul" style="font-weight:bold; margin:16px 0 8px;">Umpan balik untuk asesi</div>
        <div class="umpan-box">
            <div>Aspek pengetahuan seluruh unit pada kelompok pekerjaan yang diujikan : 
                <strong><?= ($aspek == 'tercapai') ? 'Tercapai' : (($aspek == 'belum_tercapai') ? 'Belum Tercapai' : '—') ?></strong>
            </div>
            <?php if ($aspek == 'belum_tercapai' && $umpan_balik): ?>
                <div style="margin-top:10px;"><strong>Tuliskan unit/elemen/KUK jika belum tercapai:</strong><br><?= nl2br(h($umpan_balik)) ?></div>
            <?php elseif ($umpan_balik): ?>
                <div style="margin-top:10px;"><strong>Komentar / Catatan:</strong><br><?= nl2br(h($umpan_balik)) ?></div>
            <?php endif; ?>
        </div>
        <table class="tbl-ttd">
    <tr>
        <td colspan="1" style="font-weight:bold; background:#f5f5f5;">
            Nama     
    </td>
        <td colspan="1" style="font-weight:bold; background:#f5f5f5;">
            Tanda Tangan
        </td>
    </tr>
    <tr>
        <td style="width:45%;">Asesor : <?= h($nama_asesor) ?><br>
        <span style="font-size:9pt;color:#555;">No. Reg : <?= h($no_reg_asesor) ?></span>
            <span class="mode-badge badge-ttd"   id="badge-asesor-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesor-qr"  style="display:none;"></span>
    </td>
        <td style="width:55%;">
            <div class="ttd-area">
                <span class="ttd-manual-space" id="space-asesor"></span>
                <div class="ttd-qr-box" id="qr-asesor-box">
                    <div id="qr-asesor"></div>
                </div>
            </div>
         </td>
    </tr>
    <tr>
        <td>Asesi : <?= h($nama_asesi) ?>
            <span class="mode-badge badge-ttd"   id="badge-asesi-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesi-qr"  style="display:none;"></span>
        </td>
        <td>
            <div class="ttd-area">
                <span class="ttd-manual-space" id="space-asesi"></span>
                <div class="ttd-qr-box" id="qr-asesi-box">
                    <div id="qr-asesi"></div>
                </div>
            </div>
        </td>
    </tr>
</table>
    </div>
            </div>
</div>
<script>
const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
const QR_ASESI  = <?= json_encode($qr_asesi) ?>;
let qrAsesorGenerated = false, qrAsesiGenerated = false;
let currentMode = 'ttd';

function setMode(mode) {
    currentMode = mode;
    document.getElementById('btn-ttd').classList.toggle('active', mode === 'ttd');
    document.getElementById('btn-qr').classList.toggle('active', mode === 'qr');

    const isManual = (mode === 'ttd');
    const isQR     = (mode === 'qr');

    document.getElementById('space-asesor').style.display   = isManual ? 'block' : 'none';
    document.getElementById('qr-asesor-box').style.display  = isQR ? 'flex' : 'none';
    document.getElementById('badge-asesor-ttd').style.display = isManual ? '' : 'none';
    document.getElementById('badge-asesor-qr').style.display  = isQR ? '' : 'none';

    document.getElementById('space-asesi').style.display     = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display    = isQR ? 'flex' : 'none';
    document.getElementById('badge-asesi-ttd').style.display = 'none';
    document.getElementById('badge-asesi-qr').style.display  = 'none';

    if (isQR) {
        if (!qrAsesorGenerated) {
            new QRCode(document.getElementById('qr-asesor'), {
                text: QR_ASESOR,
                width: 80, height: 80,
                colorDark: '#000', colorLight: '#fff',
                correctLevel: QRCode.CorrectLevel.M
            });
            qrAsesorGenerated = true;
        }
        if (!qrAsesiGenerated) {
            new QRCode(document.getElementById('qr-asesi'), {
                text: QR_ASESI,
                width: 80, height: 80,
                colorDark: '#000', colorLight: '#fff',
                correctLevel: QRCode.CorrectLevel.M
            });
            qrAsesiGenerated = true;
        }
    }
}

setMode('ttd');
</script>
</body>
</html>