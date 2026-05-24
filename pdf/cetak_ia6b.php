<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Asesor','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

function h($v) { return htmlspecialchars((string)$v); }

$id_asesi = isset($_GET['id_asesi']) ? intval($_GET['id_asesi']) : 0;
if (!$id_asesi) die("ID Asesi tidak ditemukan.");

$apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT a.id_apl1, a.id_skema, a.judul_skema, a.nomor_skema,
            as2.nama_asesor, as2.no_reg
     FROM tb_apl1 a
     JOIN tb_skema s ON s.id_skema = a.id_skema
     LEFT JOIN tb_asesor as2 ON as2.id_asesor = s.id_asesor
     WHERE a.id_asesi = '$id_asesi'
     ORDER BY a.id_apl1 ASC LIMIT 1"));
if (!$apl1) die("Data APL-01 tidak ditemukan.");
$id_skema = $apl1['id_skema'];

$asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT nama_asesi, nik FROM tb_asesi WHERE id_asesi='$id_asesi'"));
$nama_asesi = $asesi['nama_asesi'] ?? '';

$ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT tuk, hari_tanggal, waktu FROM tb_ak01 WHERE id_asesi='$id_asesi' ORDER BY id_ak01 DESC LIMIT 1"));
if (!$ak01) die("Data FR.AK-01 belum diisi.");

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
$tuk_html = implode('/', array_map(
    fn($t) => ($t === h($ak01['tuk'])) ? "<u>$t</u>" : "<s style='color:#aaa;'>$t</s>",
    $tuk_opts
)) . '*';

$qr_asesi  = "Nama: {$nama_asesi}\nNIK: {$asesi['nik']}\nTUK: {$ak01['tuk']}\nTanggal: {$ak01['hari_tanggal']}";
$qr_asesor = "Asesor: {$apl1['nama_asesor']}\nNo.Reg: {$apl1['no_reg']}\nID Skema: {$apl1['nomor_skema']}";

$ia06a = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT id_ia06a FROM tb_ia06a
     WHERE id_skema='$id_skema' AND id_asesor = (SELECT id_asesor FROM tb_skema WHERE id_skema='$id_skema')"));
$id_ia06a = $ia06a['id_ia06a'] ?? 0;

$soal_list = [];
if ($id_ia06a) {
    $res = mysqli_query($koneksi,
        "SELECT id_soal, soal, kunci_jawaban FROM tb_soal
         WHERE id_ia06a='$id_ia06a' ORDER BY id_soal ASC");
    $no = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        $row['no_urut'] = $no++;
        $soal_list[] = $row;
    }
}
if (empty($soal_list)) {
    die("Belum ada soal untuk skema ini.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.IA.06B - Kunci Jawaban Esai</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Calibri, Arial, sans-serif; font-size:10pt; background:#bbb; color:#000; }
.toolbar { position:sticky; top:0; z-index:999; background:#1565c0; padding:8px 16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.toolbar-label { color:#fff; font-size:13px; font-weight:bold; }
.mode-btn { padding:6px 18px; border-radius:20px; border:2px solid #fff; background:transparent; color:#fff; font-size:12px; cursor:pointer; font-weight:bold; }
.mode-btn.active { background:#fff; color:#1565c0; }
.toolbar-sep { flex:1; }
.btn-print { background:#fff; color:#1565c0; border:none; padding:7px 22px; border-radius:4px; font-size:13px; font-weight:bold; cursor:pointer; }
.btn-back { color:#90caf9; font-size:12px; text-decoration:none; }
.halaman { width:210mm; min-height:297mm; margin:12px auto; background:#fff; padding:14mm 12mm 14mm 18mm; box-shadow:0 2px 12px rgba(0,0,0,.2); }
.judul-utama { text-align:center; font-size:12pt; font-weight:bold; border:1.5px solid #000; padding:6px 10px; margin-bottom:6px; }
.tbl-skema { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom:8px; }
.tbl-skema td { border:1px solid #000; padding:5px 8px; vertical-align:top; }
.td-skema-kiri { width:32%; background:#f9f9f9; }
.tbl-pelaksanaan { width:100%; border-collapse:collapse; border:1px solid #000; margin:12px 0; }
.tbl-pelaksanaan td { border:1px solid #000; padding:5px 8px; }
.soal-list { margin:15px 0; }
.soal-item { margin-bottom:20px; page-break-inside:avoid; }
.soal-nomor { font-weight:bold; display:inline-block; width:30px; vertical-align:top; }
.soal-teks { font-weight:bold; }
.jawaban { margin-left:35px; margin-top:5px; background:#f5f9ff; padding:6px 8px; border-left:3px solid #1565c0; white-space:pre-wrap; }
.tbl-ttd { width:100%; border-collapse:collapse; border:1px solid #000; margin-top:25px; }
.tbl-ttd td { border:1px solid #000; padding:8px 10px; vertical-align:top; }
.ttd-area { min-height:80px; position:relative; }
.ttd-manual-space { height:50px; display:block; }
.ttd-qr-box { display:none; justify-content:center; align-items:center; padding:4px 0; }
.ttd-qr-box canvas, .ttd-qr-box img { width:80px !important; height:80px !important; }
@media print { body{background:#fff;} .toolbar{display:none;} .halaman{width:100%;margin:0;padding:8mm;box-shadow:none;} }
@page { size:A4; margin:0; }
</style>
</head>
<body>

<div class="toolbar">
    <span class="toolbar-label">Mode Tanda Tangan :</span>
    <button class="mode-btn active" onclick="setMode('ttd')">Tanda Tangan</button>
    <button class="mode-btn" onclick="setMode('qr')">QR Code</button>
    <span class="toolbar-sep"></span>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn-back" href="javascript:history.back()">← Kembali</a>
</div>

<div class="halaman">
    <div class="judul-utama">FR.IA.06B DPT – LEMBAR KUNCI JAWABAN PERTANYAAN TERTULIS ESAI</div>
    <table class="tbl-skema">
        <tr><td class="td-skema-kiri">Skema Sertifikasi</td><td colspan="3"><?= h($apl1['judul_skema']) ?></td> </tr>
        <tr><td class="td-skema-kiri">Nomor</td><td>: <?= h($apl1['nomor_skema']) ?></td> </tr>
    </table>
    <table class="tbl-skema">
        <tr><td class="td-skema-kiri">TUK</td><td>: <?= $tuk_html ?></td></tr>
        <tr><td class="td-skema-kiri">Nama Asesor</td><td>: <?= h($apl1['nama_asesor'] ?? '-') ?></td></tr>
        <tr><td class="td-skema-kiri">Nama Asesi</td><td>: <?= h($nama_asesi) ?></td></tr>
    </table>
    <table class="tbl-pelaksanaan">
        <tr><td style="width:30%">Hari / Tanggal</td><td>: <?= h($ak01['hari_tanggal']) ?></td></tr>
        <tr><td>Waktu</td><td>: <?= h($ak01['waktu']) ?></td></tr>
    </table>

    <div class="soal-list">
        <?php foreach ($soal_list as $s): ?>
        <div class="soal-item">
            <div><span class="soal-nomor"><?= $s['no_urut'] ?>.</span> <span class="soal-teks"><?= h($s['soal']) ?></span></div>
            <div class="jawaban"><strong>Kunci Jawaban:</strong><br><?= nl2br(h($s['kunci_jawaban'] ?? '(belum tersedia)')) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <table class="tbl-ttd">
        <tr><th colspan="2">Verifikasi & Tanda Tangan</th></tr>
        <tr>
            <td style="width:45%;">Asesor : <?= h($apl1['nama_asesor']) ?><br>No.Reg : <?= h($apl1['no_reg']) ?></td>
            <td><div class="ttd-area"><span class="ttd-manual-space" id="space-asesor"></span><div class="ttd-qr-box" id="qr-asesor-box"><div id="qr-asesor"></div></div></div></td>
        </tr>
        <tr>
            <td>Asesi : <?= h($nama_asesi) ?></td>
            <td><div class="ttd-area"><span class="ttd-manual-space" id="space-asesi"></span><div class="ttd-qr-box" id="qr-asesi-box"><div id="qr-asesi"></div></div></div></td>
        </tr>
    </table>
</div>

<script>
const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
const QR_ASESI  = <?= json_encode($qr_asesi) ?>;
let genAsesor=false, genAsesi=false;
function setMode(mode) {
    let isManual = (mode==='ttd'), isQR=(mode==='qr');
    document.getElementById('space-asesor').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesor-box').style.display = isQR ? 'flex' : 'none';
    document.getElementById('space-asesi').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display = isQR ? 'flex' : 'none';
    if(isQR){
        if(!genAsesor){ new QRCode(document.getElementById('qr-asesor'),{text:QR_ASESOR,width:80,height:80}); genAsesor=true; }
        if(!genAsesi){ new QRCode(document.getElementById('qr-asesi'),{text:QR_ASESI,width:80,height:80}); genAsesi=true; }
    }
}
setMode('ttd');
</script>
</body>
</html>