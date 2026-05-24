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

$apl1 = null;
if ($id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.id_apl1, a.id_skema, s.judul_skema, s.nomor_skema
         FROM tb_apl1 a
         JOIN tb_skema s ON a.id_skema = s.id_skema
         WHERE a.id_asesi = '$id_asesi'
         ORDER BY a.id_apl1 DESC LIMIT 1"));
}
$id_apl1_db     = intval($apl1['id_apl1']     ?? 0);
$id_skema_db    = intval($apl1['id_skema']     ?? 0);
$judul_skema    = $apl1['judul_skema'] ?? '';
$nomor_skema    = $apl1['nomor_skema'] ?? '';

$asesi = null;
if ($id_asesi) {
    $asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
}
$nama_asesi = $asesi['nama_asesi'] ?? '';

$ak01 = null;
if ($id_asesi && $id_apl1_db) {
    $ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tuk, hari_tanggal FROM tb_ak01
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak01 DESC LIMIT 1"));
}
$tuk        = $ak01['tuk']          ?? '';
$tgl_mulai  = $ak01['hari_tanggal'] ?? '';

$asesor = null;
if ($id_skema_db) {
    $asesor = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ar.nama_asesor, ar.no_reg
         FROM tb_skema sk
         JOIN tb_asesor ar ON sk.id_asesor = ar.id_asesor
         WHERE sk.id_skema = '$id_skema_db' LIMIT 1"));
}
$nama_asesor = $asesor['nama_asesor'] ?? '';
$no_reg      = $asesor['no_reg']      ?? '';

$ak03 = null;
if ($id_asesi && $id_apl1_db) {
    $ak03 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_ak03
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak03 DESC LIMIT 1"));
}
$tgl_selesai    = $ak03['tgl_selesai']    ?? '';
$catatan_lainnya = $ak03['catatan_lainnya'] ?? '';
$id_ak03        = intval($ak03['id_ak03']  ?? 0);

$hasil = [];
if ($id_ak03) {
    $rd = mysqli_query($koneksi,
        "SELECT hasil, komentar_asesi FROM hasil_ak03
         WHERE id_ak03='$id_ak03' ORDER BY id_detail_ak03 ASC");
    while ($d = mysqli_fetch_assoc($rd)) $hasil[] = $d;
}

$komponen = [
    'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi.',
    'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya.',
    'Asesor memberikan kesempatan untuk mendiskusikan/menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen.',
    'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman yang saya miliki.',
    'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen.',
    'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen.',
    'Asesor memberikan umpan balik yang mendukung setelah asesmen serta tindak lanjutnya.',
    'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya.',
    'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen.',
    'Asesor menggunakan keterampilan komunikasi yang efektif selama asesmen.',
];

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
<title>Cetak FR.AK.03 – <?= h($nama_asesi) ?></title>
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
    font-size:11pt; font-weight:bold;
    border-bottom:2px solid #000;
    padding-bottom:5px; margin-bottom:8px;
}

.tbl-header {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:10px;
}
.tbl-header td { border:1px solid #000; padding:4px 6px; vertical-align:middle; }
.th-kiri { width:28%; white-space:nowrap; }
.th-sep  { width:6px; text-align:center; }

.tgl-row td { padding:3px 6px; }

.sub-judul {
    font-size:10pt; font-weight:bold;
    margin:8px 0 4px 0;
}
.sub-desc { font-size:9pt; font-style:italic; margin-bottom:5px; }

.tbl-komponen {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:9.5pt; margin-bottom:8px;
}
.tbl-komponen th, .tbl-komponen td {
    border:1px solid #000; padding:4px 6px;
    vertical-align:middle;
}
.tbl-komponen th { text-align:center; font-weight:bold; background:#fff; }
.col-no   { width:4%;  text-align:center; }
.col-komp { width:55%; text-align:left; }
.col-ya   { width:7%;  text-align:center; font-size:12pt; }
.col-tdk  { width:7%;  text-align:center; font-size:12pt; }
.col-cat  { width:27%; font-size:9pt; }

.catatan-box {
    border:1px solid #000; padding:6px 8px;
    min-height:100px; font-size:9.5pt;
    margin-bottom:10px;
}
.catatan-label { font-size:10pt; font-weight:bold; margin-bottom:4px; }

.tbl-ttd {
    width:100%; border-collapse:collapse;
    border:1px solid #000; margin-top:6px; font-size:10pt;
}
.tbl-ttd td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.ttd-space  { height:50px; display:block; }
.ttd-qr-box { display:none; justify-content:center; align-items:center; }
.ttd-qr-box canvas, .ttd-qr-box img { width:80px !important; height:80px !important; }

.mode-badge  { display:inline-block; font-size:8pt; padding:1px 6px;
               border-radius:10px; margin-left:6px; vertical-align:middle; font-weight:normal; }
.badge-ttd    { background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; }
.badge-qr     { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.badge-kosong { background:#f5f5f5; color:#777;    border:1px solid #ccc; }

@media print {
    body      { background:#fff; }
    .toolbar  { display:none !important; }
    .halaman  { width:100%; margin:0; padding:8mm 10mm 8mm 16mm; box-shadow:none; }
    .tbl-komponen tr { page-break-inside:avoid; break-inside:avoid; }
}
@page { size:A4; margin:0; }
</style>
</head>
<body>

<div class="toolbar">
    <span class="toolbar-label">Mode Tanda Tangan :</span>
    <button class="mode-btn active" id="btn-ttd"    onclick="setMode('ttd')">Tanda Tangan</button>
    <button class="mode-btn"        id="btn-qr"     onclick="setMode('qr')">QR Code</button>
    <button class="mode-btn"        id="btn-kosong" onclick="setMode('kosong')">Tanpa TTD</button>
    <span class="toolbar-sep"></span>
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn-back" href="javascript:history.back()">← Kembali</a>
</div>

<div class="halaman">

<div class="judul-utama">FR.AK.03.&nbsp;&nbsp;&nbsp;UMPAN BALIK DAN CATATAN ASESMEN</div>

<table class="tbl-header">
    <tr> 
        <td class="th-kiri" rowspan="2">
            Skema Sertifikasi<br>
            <span style="font-size:9.5pt;">(KKNI/Okupasi/Klaster)*</span>
        </td>
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
        <td></td>
        <td class="th-sep">:</td>
        <td><?= $tuk_html ?></td>
    </tr>
    <tr>
        <td class="th-kiri">Nama Asesor</td>
        <td></td>
        <td class="th-sep">:</td>
        <td><strong><?= h($nama_asesor) ?></strong></td>
    </tr>
    <tr>
        <td class="th-kiri">Nama Asesi</td>
        <td></td>
        <td class="th-sep">:</td>
        <td><?= h($nama_asesi) ?></td>
    </tr>
    <tr>
        <td class="th-kiri" rowspan="2">Tanggal Asesmen</td>
        <td style="padding:3px 6px; white-space:nowrap;">Mulai</td>
        <td class="th-sep">:</td>
        <td><?= $tgl_mulai  ? h(date('d-m-Y', strtotime($tgl_mulai)))  : '' ?></td>
    </tr>
    <tr>
        <td style="padding:3px 6px; white-space:nowrap;">Selesai</td>
        <td class="th-sep">:</td>
        <td><?= $tgl_selesai ? h(date('d-m-Y', strtotime($tgl_selesai))) : '' ?></td>
    </tr>
</table>
<div style="font-size:8.5pt; margin-bottom:8px;">*Coret yang tidak perlu</div>

<div class="sub-judul">Umpan balik dari Asesi :</div>
<div class="sub-desc">(diisi oleh Asesi setelah pengambilan keputusan asesmen)</div>

<table class="tbl-komponen">
    <thead>
        <tr>
            <th class="col-no"  rowspan="2">No.</th>
            <th class="col-komp" rowspan="2">KOMPONEN</th>
            <th colspan="2">Hasil</th>
            <th class="col-cat" rowspan="2">Catatan / Komentar Asesi</th>
        </tr>
        <tr>
            <th class="col-ya">Ya</th>
            <th class="col-tdk">Tidak</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($komponen as $i => $teks):
        $row   = $hasil[$i] ?? [];
        $jwb   = $row['hasil']          ?? '';
        $komen = $row['komentar_asesi'] ?? '';
    ?>
    <tr>
        <td class="col-no"><?= $i+1 ?>.</td>
        <td class="col-komp"><?= h($teks) ?></td>
        <td class="col-ya"><?= cb($jwb, 'Ya') ?></td>
        <td class="col-tdk"><?= cb($jwb, 'Tidak') ?></td>
        <td class="col-cat"><?= h($komen) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="catatan-label">Catatan / komentar lainnya (apabila ada) :</div>
<div class="catatan-box"><?= nl2br(h($catatan_lainnya)) ?>&nbsp;</div>

</div>

<script>
<?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == 1): ?>
window.onload = function(){ window.print(); };
<?php endif; ?>
</script>
</body>
</html>