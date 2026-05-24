<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";
if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}
function h($v) { return htmlspecialchars((string)$v); }
function ck($v) { return ($v === '1' || $v === 1 || $v === true) ? '☑' : '☐'; }

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$apl1 = null;
if ($id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.id_apl1, a.id_skema, s.judul_skema, s.nomor_skema
         FROM tb_apl1 a JOIN tb_skema s ON a.id_skema=s.id_skema
         WHERE a.id_asesi='$id_asesi' ORDER BY a.id_apl1 DESC LIMIT 1"));
}
$id_apl1_db  = intval($apl1['id_apl1']  ?? 0);
$id_skema_db = intval($apl1['id_skema'] ?? 0);
$judul_skema = $apl1['judul_skema'] ?? '';
$nomor_skema = $apl1['nomor_skema'] ?? '';

$asesi = null;
if ($id_asesi) {
    $asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
}
$nama_asesi = $asesi['nama_asesi'] ?? '';

$ak01 = null;
if ($id_asesi && $id_apl1_db) {
    $ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tuk, hari_tanggal, id_ak01 FROM tb_ak01
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak01 DESC LIMIT 1"));
}
$tuk       = $ak01['tuk']          ?? '';
$tgl_mulai = $ak01['hari_tanggal'] ?? '';
$id_ak01   = intval($ak01['id_ak01'] ?? 0);

$tgl_selesai = '';
if ($id_asesi && $id_apl1_db) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tgl_selesai FROM tb_ak03
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak03 DESC LIMIT 1"));
    $tgl_selesai = $r['tgl_selesai'] ?? '';
}
$asesor = null;
if ($id_skema_db) {
    $asesor = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ar.nama_asesor, ar.no_reg FROM tb_skema sk
         JOIN tb_asesor ar ON sk.id_asesor=ar.id_asesor
         WHERE sk.id_skema='$id_skema_db' LIMIT 1"));
}
$nama_asesor = $asesor['nama_asesor'] ?? '';
$no_reg      = $asesor['no_reg']      ?? '';

$ak02 = null;
if ($id_asesi && $id_apl1_db) {
    $ak02 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_ak02
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak02 DESC LIMIT 1"));
}
$id_ak02         = intval($ak02['id_ak02']         ?? 0);
$rekomendasi     = $ak02['rekomendasi']     ?? '';
$tindak_lanjut   = $ak02['tindak_lanjut']   ?? '';
$komentar_asesor = $ak02['komentar_asesor'] ?? '';

$units = [];
if ($id_skema_db) {
    $ru = mysqli_query($koneksi,
        "SELECT id_unit, judul_unit FROM tb_unit_kompetensi
         WHERE id_skema='$id_skema_db' ORDER BY id_unit ASC");
    while ($u = mysqli_fetch_assoc($ru)) $units[] = $u;
}
$detail_map = [];
if ($id_ak02) {
    $rd = mysqli_query($koneksi,
        "SELECT * FROM detail_ak02 WHERE id_ak02='$id_ak02'");
    while ($d = mysqli_fetch_assoc($rd)) $detail_map[$d['id_unit']] = $d;
}

$qr_asesi  = "Nama: "           . ($asesi['nama_asesi']   ?? '')
           . "\nNIK: "           . ($asesi['nik']           ?? '')
           . "\nJenis Kelamin: " . ($asesi['jenis_kelamin'] ?? '')
           . "\nPhone: "         . ($asesi['phone_rumah']   ?? '-');
$qr_asesor = "Nama: " . $nama_asesor . "\nNo. Reg: " . $no_reg;

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
$tuk_html = implode('/', array_map(
    fn($t) => ($t === $tuk) ? "<u>$t</u>" : "<s style='color:#aaa;'>$t</s>",
    $tuk_opts
)) . '*';

$rek_html = '';
if ($rekomendasi === 'Kompeten')
    $rek_html = '<u>Kompeten</u>/<s style="color:#aaa;">Belum Kompeten</s>';
elseif ($rekomendasi === 'Belum Kompeten')
    $rek_html = '<s style="color:#aaa;">Kompeten</s>/<u>Belum Kompeten</u>';
else
    $rek_html = 'Kompeten / Belum Kompeten';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.AK.02 – <?= h($nama_asesi) ?></title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Calibri, Arial, sans-serif; font-size:10pt; background:#bbb; color:#000; }

.toolbar {
    position:sticky; top:0; z-index:999;
    background:#1565c0; padding:8px 16px;
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
}
.toolbar-label { color:#fff; font-size:13px; font-weight:bold; }
.mode-btn {
    padding:6px 18px; border-radius:20px; border:2px solid #fff;
    background:transparent; color:#fff; font-size:12px;
    cursor:pointer; font-weight:bold; transition:all .2s;
}
.mode-btn.active { background:#fff; color:#1565c0; }
.mode-btn:hover:not(.active) { background:rgba(255,255,255,.2); }
.toolbar-sep { flex:1; }
.btn-print { background:#fff; color:#1565c0; border:none; padding:7px 22px;
             border-radius:4px; font-size:13px; font-weight:bold; cursor:pointer; }
.btn-back  { color:#90caf9; font-size:12px; text-decoration:none; }

.halaman {
    width:210mm; min-height:297mm;
    margin:12px auto; background:#fff;
    padding:13mm 10mm 13mm 16mm;
    box-shadow:0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    font-size:11pt; font-weight:bold;
    border-bottom:2px solid #000;
    padding-bottom:5px; margin-bottom:8px;
}

.tbl-header {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:6px;
}
.tbl-header td { border:1px solid #000; padding:3px 6px; vertical-align:middle; }
.th-kiri { width:28%; white-space:nowrap; }
.th-sep  { width:6px; text-align:center; }

.instruksi { font-size:9.5pt; margin-bottom:8px; font-style:italic; }

.tbl-metode {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:9pt; margin-bottom:8px;
}
.tbl-metode th, .tbl-metode td {
    border:1px solid #000; padding:3px 4px;
    text-align:center; vertical-align:middle;
}
.tbl-metode .col-unit { text-align:left; width:28%; font-size:9pt; padding:3px 6px; }
.tbl-metode th { font-weight:bold; font-size:8.5pt; writing-mode:vertical-lr;
                  transform:rotate(180deg); height:80px; white-space:nowrap; }
.tbl-metode .th-unit { writing-mode:horizontal-tb; transform:none;
                        height:auto; font-size:9pt; }
.cb-cell { font-size:12pt; }
.lainnya-cell { font-size:8.5pt; max-width:60px; word-break:break-word; }

.hasil-box {
    border:1px solid #000; padding:7px 10px;
    margin-bottom:6px; font-size:10pt;
}
.hasil-label { font-weight:bold; font-size:10pt; margin-bottom:4px; }
.hasil-nilai { font-size:10pt; margin-bottom:8px; }
.textarea-box {
    border:1px solid #000; padding:5px 8px;
    min-height:28px; font-size:9.5pt;
    margin-bottom:6px; white-space:pre-wrap;
}

.tbl-ttd {
    width:100%; border-collapse:collapse;
    border:1px solid #000; margin-top:8px; font-size:10pt;
}
.tbl-ttd td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.ttd-space  { height:110px; display:block; }
.ttd-qr-box { display:none; justify-content:center; align-items:center; height:110px; }
.ttd-qr-box canvas, .ttd-qr-box img { width:80px !important; height:80px !important; }

.mode-badge  { display:inline-block; font-size:8pt; padding:1px 6px;
               border-radius:10px; margin-left:6px; vertical-align:middle; font-weight:normal; }
@media print {
    body      { background:#fff; }
    .toolbar  { display:none !important; }
    .halaman  { width:100%; margin:0; padding:8mm 8mm 8mm 14mm; box-shadow:none; }
    .tbl-metode tr { page-break-inside:avoid; break-inside:avoid; }
}
@page { size:A4; margin:0; }
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

<div class="halaman">

<div class="judul-utama">FR.AK.02.&nbsp;&nbsp;&nbsp;REKAMAN ASESMEN KOMPETENSI</div>

<table class="tbl-header">
    <tr>
        <td class="th-kiri" rowspan="2">Skema Sertifikasi<br>
            <span style="font-size:9.5pt;">(KKNI/Okupasi/Klaster)*</span></td>
        <td style="white-space:nowrap;padding:3px 6px;">Judul</td>
        <td class="th-sep">:</td>
        <td><?= h($judul_skema) ?></td>
    </tr>
    <tr>
        <td style="padding:3px 6px;">Nomor</td>
        <td class="th-sep">:</td>
        <td><?= h($nomor_skema) ?></td>
    </tr>
    <tr>
        <td class="th-kiri">TUK</td>
        <td></td><td class="th-sep">:</td>
        <td><?= $tuk_html ?></td>
    </tr>
    <tr>
        <td class="th-kiri">Nama Asesor</td>
        <td></td><td class="th-sep">:</td>
        <td><strong><?= h($nama_asesor) ?></strong></td>
    </tr>
    <tr>
        <td class="th-kiri">Nama Asesi</td>
        <td></td><td class="th-sep">:</td>
        <td><?= h($nama_asesi) ?></td>
    </tr>
    <tr>
        <td class="th-kiri" rowspan="2">Tanggal Asesmen</td>
        <td style="padding:3px 6px;white-space:nowrap;">Mulai</td>
        <td class="th-sep">:</td>
        <td><?= $tgl_mulai   ? h(date('d-m-Y', strtotime($tgl_mulai)))   : '' ?></td>
    </tr>
    <tr>
        <td style="padding:3px 6px;white-space:nowrap;">Selesai</td>
        <td class="th-sep">:</td>
        <td><?= $tgl_selesai ? h(date('d-m-Y', strtotime($tgl_selesai))) : '' ?></td>
    </tr>
</table>
<div style="font-size:8.5pt;margin-bottom:6px;">*Coret yang tidak perlu</div>

<div class="instruksi">
    Beri tanda centang (√) di kolom yang sesuai untuk menentukan Kompetensi Asesi untuk setiap Unit Kompetensi.
</div>

<table class="tbl-metode">
    <thead>
        <tr>
            <th class="th-unit col-unit">Unit kompetensi</th>
            <th>Observasi demonstrasi</th>
            <th>Portofolio</th>
            <th>Pernyataan Pihak Ketiga</th>
            <th>Pertanyaan Wawancara</th>
            <th>Pertanyaan Lisan</th>
            <th>Pertanyaan Tertulis</th>
            <th>Proyek Kerja</th>
            <th class="th-unit" style="writing-mode:horizontal-tb;transform:none;height:auto;">Lainnya</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($units as $u):
        $uid = $u['id_unit'];
        $d   = $detail_map[$uid] ?? [];
    ?>
    <tr>
        <td class="col-unit"><?= h($u['judul_unit']) ?></td>
        <td class="cb-cell"><?= ck($d['obs_demonstrasi']  ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['portofolio']       ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['pyt_pihak_ketiga'] ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['pyt_wawancara']    ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['pyt_lisan']        ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['pyt_pertulis']     ?? '0') ?></td>
        <td class="cb-cell"><?= ck($d['proyek_kerja']     ?? '0') ?></td>
        <td class="lainnya-cell"><?= h($d['lainnya'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="hasil-box">
    <div class="hasil-label">Rekomendasi hasil asesmen</div>
    <div class="hasil-nilai"><?= $rek_html ?></div>

    <div class="hasil-label">Tindak lanjut yang dibutuhkan</div>
    <div style="font-size:9pt;font-style:italic;margin-bottom:3px;">
        (Masukkan pekerjaan tambahan dan asesmen yang diperlukan untuk mencapai kompetensi)
    </div>
    <div class="textarea-box"><?= nl2br(h($tindak_lanjut)) ?>&nbsp;</div>

    <div class="hasil-label">Komentar / Observasi oleh Asesor</div>
    <div class="textarea-box"><?= nl2br(h($komentar_asesor)) ?>&nbsp;</div>
</div>

<table class="tbl-ttd">
    <tr>
        <td style="font-weight:bold; width:50%;">
            Asesi :
            <span class="mode-badge badge-ttd"   id="badge-asesi-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesi-qr"  style="display:none;"></span>
        </td>
        <td style="font-weight:bold;">
            Asesor :
            <span class="mode-badge badge-ttd"   id="badge-asesor-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesor-qr"  style="display:none;"></span>
        </td>
    </tr>
    <tr>
        <td>Nama : <?= h($nama_asesi) ?></td>
        <td>
            Nama : <?= h($nama_asesor) ?><br>
            <span style="font-size:9pt;color:#555;">No. Reg : <?= h($no_reg) ?></span>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 8px;">
            Tanda tangan / Tanggal :
            <span class="ttd-space" id="space-asesi"></span>
            <div class="ttd-qr-box" id="qr-asesi-box"><div id="qr-asesi"></div></div>
        </td>
        <td style="padding:6px 8px;">
            Tanda tangan / Tanggal :
            <span class="ttd-space" id="space-asesor"></span>
            <div class="ttd-qr-box" id="qr-asesor-box"><div id="qr-asesor"></div></div>
        </td>
    </tr>
</table>

</div>

<script>
const QR_ASESI  = <?= json_encode($qr_asesi) ?>;
const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
let doneAsesi = false, doneAsesor = false;

function setMode(mode) {
    ['ttd','qr'].forEach(m =>
        document.getElementById('btn-'+m).classList.toggle('active', m === mode)
    );
    const isManual = mode === 'ttd', isQR = mode === 'qr';
    document.getElementById('space-asesi').style.display   = isManual ? 'block' : 'none';
    document.getElementById('space-asesor').style.display  = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display  = isQR ? 'flex' : 'none';
    document.getElementById('qr-asesor-box').style.display = isQR ? 'flex' : 'none';
    ['asesi','asesor'].forEach(w => {
        document.getElementById('badge-'+w+'-ttd').style.display = isManual        ? '' : 'none';
        document.getElementById('badge-'+w+'-qr' ).style.display = isQR            ? '' : 'none';
    });
    if (isQR) {
        if (!doneAsesi) {
            new QRCode(document.getElementById('qr-asesi'),
                { text:QR_ASESI, width:80, height:80,
                  colorDark:'#000', colorLight:'#fff', correctLevel:QRCode.CorrectLevel.M });
            doneAsesi = true;
        }
        if (!doneAsesor) {
            new QRCode(document.getElementById('qr-asesor'),
                { text:QR_ASESOR, width:80, height:80,
                  colorDark:'#000', colorLight:'#fff', correctLevel:QRCode.CorrectLevel.M });
            doneAsesor = true;
        }
    }
}
<?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == 1): ?>
window.onload = function(){ window.print(); };
<?php endif; ?>
</script>
</body>
</html>