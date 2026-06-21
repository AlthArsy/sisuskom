<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Asesor','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

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

$id_apl1    = $apl1['id_apl1'];
$judul_skema = $apl1['judul_skema'];
$nomor_skema = $apl1['nomor_skema'];
$id_skema    = $apl1['id_skema'];
$nama_asesor = $apl1['nama_asesor'] ?? '-';
$no_reg_asesor = $apl1['no_reg'] ?? '-';

$asesi_data = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT nama_asesi, nik, jenis_kelamin, phone_rumah
     FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
$nama_asesi = $asesi_data['nama_asesi'] ?? '';

$ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT tuk, hari_tanggal, waktu FROM tb_ak01
     WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1'
     ORDER BY id_ak01 DESC LIMIT 1"));
$tuk     = $ak01['tuk'] ?? '-';
$tanggal = $ak01['hari_tanggal'] ?? '';
$waktu   = $ak01['waktu'] ?? '';

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
$tuk_html = implode('/', array_map(
    fn($t) => ($t === h($tuk)) ? "<u>$t</u>" : "<s style='color:#aaa;'>$t</s>",
    $tuk_opts
)) . '*';

$ia06a = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT id_ia06a, id_validator FROM tb_ia06a
     WHERE id_skema='$id_skema' AND id_asesor = (SELECT id_asesor FROM tb_skema WHERE id_skema='$id_skema')"));
$id_ia06a    = $ia06a['id_ia06a'] ?? 0;
$id_validator = $ia06a['id_validator'] ?? 0;

$validator = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT username, noreg FROM tb_validator WHERE id_validator='$id_validator' LIMIT 1"));
$validator_nama = $validator['username'] ?? '-';
$validator_noreg = $validator['noreg'] ?? '-';

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
if (empty($soal_list)) {
    die("Belum ada soal untuk skema ini. Silakan input soal terlebih dahulu.");
}

// Data jawaban, aspek, umpan balik (FR.IA.06C)
$ia06 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT id_ia06, aspek, umpan_balik FROM tb_ia06
     WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1' AND id_ia06a='$id_ia06a' LIMIT 1"));
$id_ia06    = $ia06['id_ia06'] ?? 0;
$aspek      = $ia06['aspek'] ?? '';
$umpan_balik = $ia06['umpan_balik'] ?? '';

$jawaban = [];
if ($id_ia06) {
    $res_jawab = mysqli_query($koneksi,
        "SELECT id_soal, jawaban_asesi FROM tb_ia06_jawaban WHERE id_ia06='$id_ia06'");
    while ($j = mysqli_fetch_assoc($res_jawab)) {
        $jawaban[$j['id_soal']] = $j['jawaban_asesi'];
    }
}

$qr_asesi  = "Nama: {$nama_asesi}\nNIK: {$asesi_data['nik']}\nTUK: {$tuk}\nTanggal: {$tanggal}";
$qr_asesor = "Asesor: {$nama_asesor}\nNo.Reg: {$no_reg_asesor}\nID Skema: {$nomor_skema}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.IA.06A & FR.IA.06C - Pertanyaan Tertulis Esai</title>
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
    page-break-after: avoid;
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
    vertical-align: top;
}
.soal-teks {
    display: inline-block;
    width: calc(100% - 32px);
}
.jawaban-area {
    margin-top: 6px;
    margin-left: 28px;
    border: 1px solid #ddd;
    padding: 8px;
    min-height: 60px;
    background: #fefefe;
}
.umpan-box {
    border: 1px solid #000;
    padding: 10px;
    margin: 16px 0;
}
.tbl-ttd {
    width:100%;
    border-collapse:collapse;
    border:1px solid #000;
    margin-top:16px;
}
.tbl-ttd td {
    border:1px solid #000;
    padding:8px 10px;
    vertical-align:top;
}
.ttd-area {
    min-height: 80px;
    position: relative;
}
.ttd-manual-space {
    height: 50px;
    display: block;
}
.ttd-qr-box {
    display: none;
    justify-content: center;
    align-items: center;
    padding: 4px 0;
}
.ttd-qr-box canvas,
.ttd-qr-box img {
    width:80px !important;
    height:80px !important;
}
.clearfix { clear: both; }

@media print {
    body { background:#fff; }
    .toolbar { display:none !important; }
    .print-container { box-shadow: none; margin: 0; }
    .page { padding: 10mm 12mm 10mm 16mm; }
    .validator-table, .tbl-ttd { break-inside: avoid; }
}
</style>
</head>
<body>

<div class="toolbar">
    <span class="toolbar-label">Mode Tanda Tangan (IA.06C) :</span>
    <button class="mode-btn active" id="btn-ttd" onclick="setMode('ttd')">Tanda Tangan</button>
    <button class="mode-btn" id="btn-qr" onclick="setMode('qr')">QR Code</button>
    <span class="toolbar-sep"></span>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <!-- <a class="btn-back" href="javascript:history.back()">← Kembali</a> -->
</div>

<div class="print-container">
    <!-- h1 -->
    <div class="page">
        <div class="judul">FR.IA.06A DPT - PERTANYAAN TERTULIS ESAI</div>

        <table class="info-table">
            <tr><td class="label-cell">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                <td colspan="3">Judul: <?= h($judul_skema) ?></td>
            </tr>
            <tr><td class="label-cell"></td>
                <td colspan="3">Nomor: <?= h($nomor_skema) ?></td>
            </tr>
            <tr><td class="label-cell">TUK</td>
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
        <?php foreach ($soal_list as $s): ?>
            <div class="soal-item">
                <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                <span class="soal-teks"><?= h($s['soal']) ?></span>
            </div>
        <?php endforeach; ?>

        <table class="validator-table">
            <thead>
                <tr><th>STATUS</th><th>NAMA</th><th>NOMOR MET</th><th>TANDA TANGAN DAN TANGGAL</th></tr>
            </thead>
            <tbody>
                <tr><td>PENYUSUN</td><td><?= h($nama_asesor) ?></td><td><?= h($no_reg_asesor) ?></td><td style="height:50px;"></td></tr>
                <tr><td>VALIDATOR</td><td><?= h($validator_nama) ?></td><td><?= h($validator_noreg) ?></td><td style="height:50px;"></td></tr>
            </tbody>
        </table>
    </div> 
    <!-- h2 -->
    <div class="page">
        <div class="judul">FR.IA.06C DPT – LEMBAR JAWABAN PERTANYAAN TERTULIS ESAI</div>

        <table class="info-table">
            <tr><td class="label-cell">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                <td colspan="3">Judul: <?= h($judul_skema) ?></td>
            </tr>
            <tr><td class="label-cell"></td>
                <td colspan="3">Nomor: <?= h($nomor_skema) ?></td>
            </tr>
            <tr><td class="label-cell">TUK</td>
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
        <?php foreach ($soal_list as $s):
            $jwb = $jawaban[$s['id_soal']] ?? '';
        ?>
            <div class="soal-item">
                <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                <!-- <span class="soal-teks"><?= h($s['soal']) ?></span> -->
                <div class="jawaban-area">
                    <?= $jwb ? nl2br(h($jwb)) : '<span style="color:#aaa;">(Belum diisi)</span>' ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="font-weight:bold; margin:16px 0 8px;">Umpan balik untuk asesi</div>
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
            <tr><td colspan="2" style="font-weight:bold; background:#f5f5f5;">Tanda Tangan</td></tr>
            <tr>
                <td style="width:45%;">Asesor : <?= h($nama_asesor) ?><br>
                <span style="font-size:9pt;color:#555;">No. Reg : <?= h($no_reg_asesor) ?></span>
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
                <td>Asesi : <?= h($nama_asesi) ?></td>
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

<script>
const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
const QR_ASESI  = <?= json_encode($qr_asesi) ?>;
let qrAsesorGenerated = false, qrAsesiGenerated = false;

function setMode(mode) {
    const isManual = (mode === 'ttd');
    const isQR = (mode === 'qr');

    document.getElementById('btn-ttd').classList.toggle('active', isManual);
    document.getElementById('btn-qr').classList.toggle('active', isQR);

    document.getElementById('space-asesor').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesor-box').style.display = isQR ? 'flex' : 'none';
    document.getElementById('space-asesi').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display = isQR ? 'flex' : 'none';

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