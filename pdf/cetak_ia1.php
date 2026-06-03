<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";
if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}
function h($v) { return htmlspecialchars((string)$v); }
function cb($val, $check) { return $val === $check ? '☑' : '☐'; }

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$asesi = null;
if ($id_asesi) {
    $asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
}

$ak01 = null;
if ($id_asesi) {
    $ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.*, s.judul_skema, s.nomor_skema, s.standar_kompetensi_kerja,
                asr.nama_asesor, asr.no_reg, asr.id_asesor,
                apl.id_skema
         FROM tb_ak01 a
         LEFT JOIN tb_apl1  apl ON a.id_apl1   = apl.id_apl1
         LEFT JOIN tb_skema s   ON apl.id_skema = s.id_skema
         LEFT JOIN tb_asesor asr ON a.id_asesor = asr.id_asesor
         WHERE a.id_asesi = '$id_asesi'
         ORDER BY a.id_ak01 DESC LIMIT 1"));
}
$id_skema        = intval($ak01['id_skema'] ?? 0);
$nama_asesor     = $ak01['nama_asesor'] ?? '-';
$no_reg          = $ak01['no_reg'] ?? '-';
$tuk             = $ak01['tuk'] ?? '';
$tanggal         = $ak01['hari_tanggal'] ?? '';
$judul_skema     = $ak01['judul_skema'] ?? '';
$nomor_skema     = $ak01['nomor_skema'] ?? '';
$standar_skema   = $ak01['standar_kompetensi_kerja'] ?? '';

$ia01 = null;
$id_apl1 = 0;
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_apl1 FROM tb_apl1 WHERE id_asesi='$id_asesi' ORDER BY id_apl1 DESC LIMIT 1"));
    $id_apl1 = intval($r['id_apl1'] ?? 0);
    if ($id_apl1) {
        $ia01 = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT * FROM tb_ia01 WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1'
             ORDER BY id_ia01 DESC LIMIT 1"));
    }
}
$rekomendasi    = $ia01['rekomendasi']    ?? '';
$umpan_balik    = $ia01['umpan_balik']   ?? '';
$belum_kompeten = $ia01['belum_kompeten'] ?? '';
$id_ia01        = intval($ia01['id_ia01'] ?? 0);

$units = [];
if ($id_skema) {
    $ru = mysqli_query($koneksi,
        "SELECT * FROM tb_unit_kompetensi WHERE id_skema='$id_skema' ORDER BY id_unit ASC");
    while ($u = mysqli_fetch_assoc($ru)) {
        $id_unit = intval($u['id_unit']);
        $re = mysqli_query($koneksi,
            "SELECT * FROM tb_elemen WHERE id_unit='$id_unit' ORDER BY id_elemen ASC");
        $u['elemen'] = [];
        while ($el = mysqli_fetch_assoc($re)) {
            $id_el = intval($el['id_elemen']);
            $rk = mysqli_query($koneksi,
                "SELECT * FROM tb_kuk WHERE id_elemen='$id_el' ORDER BY id_kuk ASC");
            $el['kuk'] = [];
            while ($k = mysqli_fetch_assoc($rk)) $el['kuk'][] = $k;
            $u['elemen'][] = $el;
        }
        $units[] = $u;
    }
}

$detail = [];
if ($id_ia01) {
    $rd = mysqli_query($koneksi,
        "SELECT id_kuk, pencapaian, `Penilaian Lanjut` AS penilaian_lanjut
         FROM detail_ia01 WHERE id_ia01='$id_ia01'");
    while ($d = mysqli_fetch_assoc($rd)) $detail[$d['id_kuk']] = $d;
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
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.IA.01 – <?= h($asesi['nama_asesi'] ?? '') ?></title>
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
    padding:13mm 12mm 13mm 18mm;
    box-shadow:0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    font-size:10.5pt; font-weight:bold;
    border-bottom:2px solid #000;
    padding-bottom:4px; margin-bottom:8px;
}

.tbl-header {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:8px;
}
.tbl-header td { border:1px solid #000; padding:4px 6px; vertical-align:middle; }
.th-label { white-space:nowrap; width:30%; }
.th-sep   { width:6px; text-align:center; }
.th-val   { }

.tbl-panduan {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:8px;
}
.tbl-panduan td { border:1px solid #000; padding:5px 8px; }
.panduan-title { font-weight:bold; }
.panduan-list  { margin:4px 0 0 16px; }
.panduan-list li { margin-bottom:2px; font-size:9.5pt; }

.tbl-kelompok {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:10px;
}
.tbl-kelompok th, .tbl-kelompok td {
    border:1px solid #000; padding:4px 6px; vertical-align:middle;
}
.tbl-kelompok th { text-align:center; font-weight:bold; }
.td-kelompok-label { text-align:center; font-weight:bold; font-size:10pt; }

.tbl-unit-header {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-top:8px;
}
.tbl-unit-header td { border:1px solid #000; padding:3px 6px; vertical-align:middle; }
.td-unit-nama { font-weight:bold; font-size:10.5pt; width:42%; }

.tbl-kuk {
    width:100%; border-collapse:collapse;
    border:1px solid #000; border-top:none;
    font-size:9.5pt; margin-bottom:0;
}
.tbl-kuk th, .tbl-kuk td {
    border:1px solid #000; padding:4px 5px;
    vertical-align:middle; text-align:left;
}
.tbl-kuk th { text-align:center; font-weight:bold; font-size:9.5pt; background:#fff; }
.col-no     { width:5%;  text-align:center !important; }
.col-elemen { width:15%; vertical-align:top !important; }
.col-kuk    { width:35%; }
.col-std    { width:18%; }
.col-ya     { width:6%;  text-align:center !important; font-size:12pt; }
.col-tidak  { width:6%;  text-align:center !important; font-size:12pt; }
.col-pnl    { width:15%; font-size:9pt; }

.tbl-umpan {
    width:100%; border-collapse:collapse;
    border:1px solid #000; margin-top:10px; font-size:10pt;
}
.tbl-umpan td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.umpan-label { font-weight:bold; font-size:10pt; }
.rek-item    { display:flex; align-items:flex-start; gap:6px; margin-bottom:4px; font-size:9.5pt; }
.rek-cb      { font-size:13pt; line-height:1; flex-shrink:0; margin-top:-1px; }

.tbl-ttd {
    width:100%; border-collapse:collapse;
    border:1px solid #000; margin-top:6px; font-size:10pt;
}
.tbl-ttd td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.ttd-space  { height:50px; display:block; }
.ttd-qr-box { display:none; justify-content:center; align-items:center; }
.ttd-qr-box canvas, .ttd-qr-box img { width:80px !important; height:80px !important; }

.mode-badge  { display:inline-block; font-size:8pt; padding:1px 6px; border-radius:10px;
               margin-left:6px; vertical-align:middle; font-weight:normal; }

.page-break { page-break-before:always; }
.unit-wrapper { page-break-inside:avoid; break-inside:avoid; }

@media print {
    body      { background:#fff; }
    .toolbar  { display:none !important; }
    .halaman  { width:100%; margin:0; padding:8mm 10mm 8mm 16mm; box-shadow:none; }
    .tbl-kuk tr  { page-break-inside:avoid; break-inside:avoid; }
    .tbl-unit-header { page-break-after:avoid; break-after:avoid; }
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
    <!-- <a class="btn-back" href="javascript:history.back()">← Kembali</a> -->
</div>

<div class="halaman">

<div class="judul-utama">FR.IA.01.&nbsp;&nbsp;&nbsp;CL - CEKLIS OBSERVASI AKTIVITAS DI TEMPAT KERJA ATAU TEMPAT KERJA SIMULASI</div>

<table class="tbl-header">
    <tr>
        <td class="th-label" rowspan="2">
            Skema Sertifikasi<br>
            <span style="font-size:9.5pt;">(KKNI/Okupasi/Klaster)</span>
        </td>
        <td style="white-space:nowrap;padding:3px 6px;">Judul</td>
        <td class="th-sep">:</td>
        <td class="th-val"><?= h($judul_skema) ?></td>
    </tr>
    <tr>
        <td style="padding:3px 6px;">Nomor</td>
        <td class="th-sep">:</td>
        <td class="th-val"><?= h($nomor_skema) ?></td>
    </tr>
    <tr>
        <td class="th-label" colspan="1">TUK</td>
        <td></td>
        <td class="th-sep">:</td>
        <td class="th-val"><?= $tuk_html ?></td>
    </tr>
    <tr>
        <td class="th-label" colspan="1">Nama Asesor</td>
        <td></td>
        <td class="th-sep">:</td>
        <td class="th-val"><strong><?= h($nama_asesor) ?></strong></td>
    </tr>
    <tr>
        <td class="th-label" colspan="1">Nama Asesi</td>
        <td></td>
        <td class="th-sep">:</td>
        <td class="th-val"><?= h($asesi['nama_asesi'] ?? '') ?></td>
    </tr>
    <tr>
        <td class="th-label" colspan="1">Tanggal</td>
        <td></td>
        <td class="th-sep">:</td>
        <td class="th-val"><?= $tanggal ? h(date('d-m-Y', strtotime($tanggal))) : '' ?></td>
    </tr>
</table>
<div style="font-size:8.5pt; margin-bottom:8px;">*Coret yang tidak perlu</div>

<table class="tbl-panduan">
    <tr><td>
        <div class="panduan-title">PANDUAN BAGI ASESOR</div>
        <ul class="panduan-list">
            <li>Lengkapi nama unit kompetensi, elemen, dan kriteria unjuk kerja sesuai kolom dalam tabel.</li>
            <li>Isilah standar industri atau tempat kerja</li>
            <li>Beri tanda centang (☑) pada kolom "Ya" jika Anda yakin asesi dapat melakukan/mendemonstrasikan tugas sesuai KUK, atau centang (☑) pada kolom "Tidak" bila sebaliknya.</li>
            <li>Penilaian Lanjut diisi bila hasil belum dapat disimpulkan, untuk itu gunakan metode lain sehingga keputusan dapat dibuat.</li>
            <li>Isilah kolom KUK sesuai dengan Unit Kompetensi/SKKNI</li>
        </ul>
    </td></tr>
</table>

<table class="tbl-kelompok">
    <thead>
        <tr>
            <th rowspan="<?= count($units) + 1 ?>" style="width:18%;">Kelompok<br>Pekerjaan</th>
            <th style="width:8%;">No.</th>
            <th style="width:25%;">Kode Unit</th>
            <th>Judul Unit</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($units as $ui => $u): ?>
    <tr>
        <td style="text-align:center;"><?= $ui+1 ?></td>
        <td style="font-weight:bold;"><?= h($u['kode_unit']) ?></td>
        <td style="font-weight:bold;"><?= h($u['judul_unit']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php foreach ($units as $ui => $u): ?>
<div class="unit-wrapper">
<?php if ($ui > 0): ?><div class="page-break"></div><?php endif; ?>

<table class="tbl-unit-header">
    <tr>
        <td class="td-unit-nama" rowspan="2">Unit Kompetensi <?= $ui+1 ?></td>
        <td style="white-space:nowrap;padding:2px 6px;">Kode Unit</td>
        <td style="width:8px;text-align:center;padding:2px 4px;">:</td>
        <td style="padding:2px 6px;font-weight:bold;"><?= h($u['kode_unit']) ?></td>
    </tr>
    <tr>
        <td style="padding:2px 6px;">Judul Unit</td>
        <td style="text-align:center;padding:2px 4px;">:</td>
        <td style="padding:2px 6px;font-weight:bold;"><?= h($u['judul_unit']) ?></td>
    </tr>
</table>

<table class="tbl-kuk">
    <thead>
        <tr>
            <th class="col-no">No.</th>
            <th class="col-elemen">Elemen</th>
            <th class="col-kuk">Kriteria Unjuk Kerja</th>
            <th class="col-std">Standar Industri atau Tempat Kerja</th>
            <th colspan="2" style="text-align:center;">Pencapaian</th>
            <th class="col-pnl">Penilaian<br>Lanjut</th>
        </tr>
        <tr>
            <th></th><th></th><th></th><th></th>
            <th class="col-ya">Ya</th>
            <th class="col-tidak">Tidak</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($u['elemen'] as $ei => $el):
        $jml_kuk = count($el['kuk']);
        foreach ($el['kuk'] as $ki => $kk):
            $kuk_id  = intval($kk['id_kuk']);
            $det     = $detail[$kuk_id] ?? [];
            $penc    = $det['pencapaian']      ?? '';
            $pnl     = $det['penilaian_lanjut'] ?? '';
    ?>
    <tr>
        <td class="col-no" style="font-size:9pt;"><?= ($ei+1) . '.' . ($ki+1) ?></td>

        <?php if ($ki === 0): ?>
        <td class="col-elemen" rowspan="<?= $jml_kuk ?>" style="vertical-align:top; font-size:9.5pt;">
            <?= ($ei+1) . '. ' . h($el['nama_elemen']) ?>
        </td>
        <?php endif; ?>

        <td class="col-kuk"><?= h($kk['kuk']) ?></td>
        <td class="col-std" style="font-size:9pt;"><?= h($standar_skema) ?></td>
        <td class="col-ya"><?= cb($penc, 'Ya') ?></td>
        <td class="col-tidak"><?= cb($penc, 'Tidak') ?></td>
        <td class="col-pnl"><?= h($pnl) ?></td>
    </tr>
    <?php endforeach; endforeach; ?>
    </tbody>
</table>
</div>
<?php endforeach; ?>

<div class="page-break"></div>

<table class="tbl-umpan">
    <tr>
        <td style="font-weight:bold;font-size:10pt;border-bottom:1px solid #000;" colspan="1">
            Umpan Balik untuk asesi:
        </td>
    </tr>
    <tr>
        <td style="min-height:40px; padding:6px 8px; font-size:10pt;">
            <?= nl2br(h($umpan_balik)) ?>&nbsp;
        </td>
    </tr>
    <tr>
        <td style="padding:6px 8px;">
            <div style="font-weight:bold; margin-bottom:6px; font-size:10pt;">Rekomendasi:</div>
            <div class="rek-item">
                <span class="rek-cb"><?= cb($rekomendasi, 'Kompeten') ?></span>
                <span>Asesi telah memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>KOMPETEN</strong></span>
            </div>
            <div class="rek-item">
                <span class="rek-cb"><?= cb($rekomendasi, 'Belum Kompeten') ?></span>
                <span>
                    Asesi belum memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>BELUM KOMPETEN</strong>
                    <?php if ($belum_kompeten): ?>
                    <br><span style="font-size:9pt;color:#555;">pada: <?= nl2br(h($belum_kompeten)) ?></span>
                    <?php endif; ?>
                </span>
            </div>
        </td>
    </tr>
</table>

<table class="tbl-ttd">
    <tr>
        <td style="font-weight:bold; width:50%;">
            Asesi
            <span class="mode-badge badge-ttd"   id="badge-asesi-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesi-qr"  style="display:none;"></span>
        </td>
        <td style="font-weight:bold;">
            Asesor
            <span class="mode-badge badge-ttd"   id="badge-asesor-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesor-qr"  style="display:none;"></span>
        </td>
    </tr>
    <tr>
        <td>Nama : <?= h($asesi['nama_asesi'] ?? '') ?></td>
        <td>
            Nama : <?= h($nama_asesor) ?><br>
            <span style="font-size:9pt;color:#555;">No. Reg : <?= h($no_reg) ?></span>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 8px;">
            Tanda tangan/ Tanggal :
            <span class="ttd-space" id="space-asesi"></span>
            <div class="ttd-qr-box" id="qr-asesi-box"><div id="qr-asesi"></div></div>
        </td>
        <td style="padding:6px 8px;">
            Tanda tangan/ Tanggal :
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
    document.getElementById('space-asesi').style.display  = isManual ? 'block' : 'none';
    document.getElementById('space-asesor').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display  = isQR ? 'flex' : 'none';
    document.getElementById('qr-asesor-box').style.display = isQR ? 'flex' : 'none';
    ['asesi','asesor'].forEach(w => {
        document.getElementById('badge-'+w+'-ttd').style.display = isManual        ? '' : 'none';
        document.getElementById('badge-'+w+'-qr' ).style.display = isQR            ? '' : 'none';
    });
    if (isQR) {
        if (!doneAsesi) {
            new QRCode(document.getElementById('qr-asesi'),
                { text:QR_ASESI, width:80, height:80, colorDark:'#000', colorLight:'#fff',
                  correctLevel:QRCode.CorrectLevel.M });
            doneAsesi = true;
        }
        if (!doneAsesor) {
            new QRCode(document.getElementById('qr-asesor'),
                { text:QR_ASESOR, width:80, height:80, colorDark:'#000', colorLight:'#fff',
                  correctLevel:QRCode.CorrectLevel.M });
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