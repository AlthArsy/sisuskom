<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>";
    exit;
}

function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

$id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
if (!$id_skema) {
    die('Parameter id_skema wajib.');
}

$skema = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT sk.id_skema, sk.judul_skema, sk.nomor_skema, sk.id_asesor,
            ar.nama_asesor, ar.no_reg
     FROM tb_skema sk
     LEFT JOIN tb_asesor ar ON ar.id_asesor = sk.id_asesor
     WHERE sk.id_skema = '$id_skema' LIMIT 1"));
if (!$skema) {
    die('Skema tidak ditemukan.');
}

$id_asesor_db = intval($skema['id_asesor']);
$role         = $_SESSION['role'] ?? '';
if ($role === 'Asesor') {
    $id_sess = intval($_SESSION['id_asesor'] ?? 0);
    if ($id_sess && $id_asesor_db !== $id_sess) {
        die('Akses ditolak untuk skema ini.');
    }
}

$asesi_rows = [];
$seen       = [];
$q = mysqli_query($koneksi,
    "SELECT a.id_asesi, s.nama_asesi
     FROM tb_apl1 a
     INNER JOIN tb_asesi s ON s.id_asesi = a.id_asesi
     WHERE a.id_skema = '$id_skema'
     ORDER BY s.nama_asesi ASC");
while ($r = mysqli_fetch_assoc($q)) {
    $aid = intval($r['id_asesi']);
    if (isset($seen[$aid])) {
        continue;
    }
    $seen[$aid]       = true;
    $asesi_rows[]     = $r;
}

$rtuk = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT ak.tuk FROM tb_ak01 ak
     INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak.id_apl1
     WHERE ap.id_skema = '$id_skema' AND ak.tuk IS NOT NULL AND ak.tuk != ''
     ORDER BY ak.id_ak01 DESC LIMIT 1"));
$tuk = $rtuk['tuk'] ?? '';

$ak05 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT ak5.*
     FROM tb_ak05 ak5
     INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak5.id_apl1
     WHERE ap.id_skema = '$id_skema' AND ak5.id_asesor = '$id_asesor_db'
     ORDER BY ak5.id_ak5 DESC LIMIT 1"));

if (!$ak05) {
    die('Data FR.AK-05 belum diisi. Silakan simpan formulir terlebih dahulu.');
}

$id_ak5      = intval($ak05['id_ak5']);
$catatan     = $ak05['catatan'] ?? '';
$detail_map  = [];
$tanggal     = '';
$aspek       = '';
$pencatatan  = '';
$saran       = '';

$rd = mysqli_query($koneksi,
    "SELECT * FROM detail_ak5 WHERE id_ak5 = '$id_ak5' ORDER BY id_detail_ak5 ASC");
while ($d = mysqli_fetch_assoc($rd)) {
    $detail_map[intval($d['id_asesi'])] = $d;
    if ($tanggal === '' && !empty($d['tanggal'])) {
        $tanggal = $d['tanggal'];
    }
    if ($aspek === '' && !empty($d['aspek'])) {
        $aspek = $d['aspek'];
    }
    if ($pencatatan === '' && !empty($d['pencatatan'])) {
        $pencatatan = $d['pencatatan'];
    }
    if ($saran === '' && !empty($d['saran'])) {
        $saran = $d['saran'];
    }
}

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
$tuk_html = implode('/', array_map(
    fn($t) => ($t === $tuk) ? "<u>$t</u>" : "<s style='color:#aaa;'>$t</s>",
    $tuk_opts
)) . '*';

$qr_asesor = 'Laporan Asesmen FR.AK-05'
    . "\nSkema: " . ($skema['judul_skema'] ?? '')
    . "\nAsesor: " . ($skema['nama_asesor'] ?? '')
    . "\nNo.Reg: " . ($skema['no_reg'] ?? '')
    . "\nTanggal: " . $tanggal;

$min_rows = 9;
while (count($asesi_rows) < $min_rows) {
    $asesi_rows[] = ['id_asesi' => 0, 'nama_asesi' => ''];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.AK-05 – <?= h($skema['judul_skema']) ?></title>
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
    cursor: pointer; font-weight: bold;
}
.mode-btn.active { background: #fff; color: #1565c0; }
.toolbar-sep { flex:1; }
.btn-print {
    background:#fff; color:#1565c0; border:none;
    padding:7px 22px; border-radius:4px; font-size:13px;
    font-weight:bold; cursor:pointer;
}

.halaman {
    width: 210mm; min-height: 297mm;
    margin: 12px auto;
    background: #fff;
    padding: 14mm 12mm 14mm 18mm;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    text-align: center; font-size: 12pt; font-weight: bold;
    margin-bottom: 4px;
}
.judul-kode { text-align: center; font-size: 11pt; font-weight: bold; margin-bottom: 10px; }

.tbl-skema { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom:8px; }
.tbl-skema td { border:1px solid #000; padding:5px 8px; vertical-align:top; }
.td-label { width:28%; }

.tbl-rek { width:100%; border-collapse:collapse; border:1px solid #000; margin:10px 0 4px; font-size:9.5pt; }
.tbl-rek th, .tbl-rek td { border:1px solid #000; padding:5px 6px; }
.tbl-rek th { background:#f0f0f0; text-align:center; font-weight:bold; }
.tbl-rek .c-no { width:28px; text-align:center; }
.tbl-rek .c-k { width:32px; text-align:center; }
.foot-note { font-size:8.5pt; margin-bottom:10px; }

.blk-label { font-weight:bold; margin:12px 0 4px; font-size:10pt; }
.blk-box {
    border:1px solid #000; min-height:42px; padding:6px 8px;
    margin-bottom:8px; white-space:pre-wrap; font-size:9.5pt;
}

.tbl-ttd { width:100%; border-collapse:collapse; border:1px solid #000; margin-top:14px; }
.tbl-ttd td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.ttd-area { min-height: 70px; position: relative; }
.ttd-manual-space { height: 45px; display: block; }
.ttd-qr-box { display: none; justify-content: center; align-items: center; }

@media print {
    body { background:#fff; }
    .toolbar { display:none !important; }
    .halaman { width:100%; margin:0; padding:8mm 10mm 8mm 16mm; box-shadow:none; }
}
@page { size:A4; margin:0; }
</style>
</head>
<body>

<div class="toolbar">
    <span class="toolbar-label">Mode Tanda Tangan :</span>
    <button class="mode-btn active" id="btn-ttd" onclick="setMode('ttd')">Tanda Tangan</button>
    <button class="mode-btn" id="btn-qr" onclick="setMode('qr')">QR Code</button>
    <span class="toolbar-sep"></span>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="halaman">
    <div class="judul-kode">FR.AK.05.</div>
    <div class="judul-utama">LAPORAN ASESMEN</div>

    <table class="tbl-skema">
        <tr>
            <td rowspan="2" class="td-label">Skema Sertifikasi:<br><span style="font-size:9pt;">(KKNI/Okupasi/Klaster)</span></td>
            <td style="width:12%">Judul</td><td style="width:8px">:</td>
            <td><?= h($skema['judul_skema']) ?></td>
        </tr>
        <tr>
            <td>Nomor</td><td>:</td><td><?= h($skema['nomor_skema']) ?></td>
        </tr>
        <tr>
            <td class="td-label">TUK</td><td>:</td><td colspan="2"><?= $tuk_html ?></td>
        </tr>
        <tr>
            <td class="td-label">Nama Asesor</td><td>:</td>
            <td colspan="2"><?= h($skema['nama_asesor'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="td-label">Tanggal</td><td>:</td>
            <td colspan="2"><?= h($tanggal) ?></td>
        </tr>
    </table>

    <table class="tbl-rek">
        <thead>
            <tr>
                <th class="c-no" rowspan="2">No.</th>
                <th rowspan="2">Nama Asesi</th>
                <th colspan="2">Rekomendasi</th>
                <th rowspan="2">Keterangan**</th>
            </tr>
            <tr>
                <th class="c-k">K</th>
                <th class="c-k">BK</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0; foreach ($asesi_rows as $ar):
                $no++;
                $aid = intval($ar['id_asesi']);
                $det = $detail_map[$aid] ?? [];
                $rek = strtoupper($det['rekomend'] ?? '');
            ?>
            <tr>
                <td class="c-no"><?= $no ?></td>
                <td><?= $aid ? h($ar['nama_asesi']) : '&nbsp;' ?></td>
                <td class="c-k"><?= $rek === 'K' ? '☑' : '☐' ?></td>
                <td class="c-k"><?= $rek === 'BK' ? '☑' : '☐' ?></td>
                <td><?= $aid ? h($det['keterangan'] ?? '') : '&nbsp;' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="foot-note">** tuliskan Kode dan Judul Unit Kompetensi yang dinyatakan BK bila mengases satu skema</div>

    <div class="blk-label">Aspek Negatif dan Positif dalam Asesemen</div>
    <div class="blk-box"><?= h($aspek) ?: '&nbsp;' ?></div>

    <div class="blk-label">Pencatatan Penolakan Hasil Asesmen</div>
    <div class="blk-box"><?= h($pencatatan) ?: '&nbsp;' ?></div>

    <div class="blk-label">Saran Perbaikan : (Asesor/Personil Terkait)</div>
    <div class="blk-box"><?= h($saran) ?: '&nbsp;' ?></div>

    <div class="blk-label">Catatan :</div>
    <div class="blk-box"><?= h($catatan) ?: '&nbsp;' ?></div>

    <table class="tbl-ttd">
        <tr>
            <td style="width:35%; font-weight:bold;">Asesor :</td>
            <td style="width:65%; font-weight:bold;">Tanda tangan / Tanggal</td>
        </tr>
        <tr>
            <td>
                Nama<br><?= h($skema['nama_asesor'] ?? '-') ?><br><br>
                No. Reg<br><?= h($skema['no_reg'] ?? '-') ?>
            </td>
            <td>
                <div class="ttd-area">
                    <span class="ttd-manual-space" id="space-asesor"></span>
                    <div class="ttd-qr-box" id="qr-asesor-box"><div id="qr-asesor"></div></div>
                </div>
            </td>
        </tr>
    </table>
</div>

<script>
const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
let qrDone = false;
let currentMode = 'ttd';

function setMode(mode) {
    currentMode = mode;
    document.getElementById('btn-ttd').classList.toggle('active', mode === 'ttd');
    document.getElementById('btn-qr').classList.toggle('active', mode === 'qr');
    document.getElementById('space-asesor').style.display = mode === 'ttd' ? 'block' : 'none';
    document.getElementById('qr-asesor-box').style.display = mode === 'qr' ? 'flex' : 'none';
    if (mode === 'qr' && !qrDone) {
        new QRCode(document.getElementById('qr-asesor'), {
            text: QR_ASESOR, width: 80, height: 80,
            colorDark: '#000', colorLight: '#fff',
            correctLevel: QRCode.CorrectLevel.M
        });
        qrDone = true;
    }
}
setMode('ttd');
</script>
</body>
</html>
