<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Asesor','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

function h($v) { return htmlspecialchars((string)$v); }

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

if (!$id_asesi) {
    die("ID Asesi tidak ditemukan.");
}

$apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT a.id_apl1, a.id_skema, a.judul_skema, a.nomor_skema,
            s.standar_kompetensi_kerja,
            as2.nama_asesor, as2.no_reg, as2.id_asesor
     FROM tb_apl1 a
     JOIN tb_skema s ON s.id_skema = a.id_skema
     LEFT JOIN tb_asesor as2 ON as2.id_asesor = s.id_asesor
     WHERE a.id_asesi = '$id_asesi'
     ORDER BY a.id_apl1 ASC LIMIT 1"));

if (!$apl1) {
    die("Data APL-01 tidak ditemukan untuk asesi ini.");
}

$asesi_data = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT nama_asesi, nik, jenis_kelamin, phone_rumah
     FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
$nama_asesi = $asesi_data['nama_asesi'] ?? '';

$ak01 = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM tb_ak01 WHERE id_asesi='$id_asesi' ORDER BY id_ak01 DESC LIMIT 1"));

if (!$ak01) {
    die("Data FR.AK-01 belum diisi. Silakan isi formulir terlebih dahulu.");
}

$detail_bukti_rows = [];
$res_detail = mysqli_query($koneksi,
    "SELECT bukti FROM detail_ak1 WHERE id_ak01='{$ak01['id_ak01']}' ORDER BY id_detail_ak1 ASC");
while ($row = mysqli_fetch_assoc($res_detail)) {
    $detail_bukti_rows[] = $row['bukti'];
}
$saved_bukti = [];
foreach ($detail_bukti_rows as $baris) {
    $items = explode(', ', $baris);
    foreach ($items as $item) {
        $item = trim($item);
        if ($item !== '') $saved_bukti[] = $item;
    }
}
$bukti_labels = [
    'Hasil Verifikasi Portofolio',
    'Hasil Reviu Produk',
    'Hasil Observasi Langsung',
    'Hasil Kegiatan Terstruktur',
    'Hasil Tanya Jawab',
    'Hasil Pertanyaan Tulis',
    'Hasil Pertanyaan Lisan',
    'Hasil Pertanyaan Wawancara',
];
$lainnya = '';
foreach ($saved_bukti as $b) {
    if (!in_array($b, $bukti_labels) && $b !== 'Bukti Lainnya') {
        $lainnya = $b;
        break;
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
<title>Cetak FR.AK-01 – <?= h($nama_asesi) ?></title>
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

.halaman {
    width: 210mm; min-height: 297mm;
    margin: 12px auto;
    background: #fff;
    padding: 14mm 12mm 14mm 18mm;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    text-align:center; font-size:12pt; font-weight:bold;
    border:1.5px solid #000; padding:6px 10px; margin-bottom:6px;
}
.sub-judul {
    text-align:center; font-size:10pt; margin:4px 0 12px;
    font-style:italic;
}

.tbl-skema { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom:8px; }
.tbl-skema td { border:1px solid #000; padding:5px 8px; vertical-align:top; }
.td-skema-kiri { width:32%; background:#f9f9f9; }

.bukti-grid {
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 20px;
    margin: 8px 0 12px;
    border:1px solid #ccc;
    padding:8px 12px;
    background:#fafbff;
}
.bukti-item {
    display:flex;
    align-items:center;
    gap:8px;
    font-size:10pt;
}
.bukti-check {
    width:16px; height:16px;
    border:1px solid #000;
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
.bukti-check.checked::before { content:"✓"; font-size:13px; font-weight:bold; }

.tbl-pelaksanaan { width:100%; border-collapse:collapse; border:1px solid #000; margin:12px 0; }
.tbl-pelaksanaan td { border:1px solid #000; padding:5px 8px; }

.pernyataan {
    background:#fff8e1;
    border:1px solid #ffe082;
    border-radius:5px;
    padding:8px 12px;
    margin:12px 0;
    font-size:10pt;
    line-height:1.4;
}

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
.th-kiri { width:28%; white-space:nowrap; }
.th-sep  { width:6px; text-align:center; }


@media print {
    body { background:#fff; }
    .toolbar { display:none !important; }
    .halaman { width:100%; margin:0; padding:8mm 10mm 8mm 16mm; box-shadow:none; }
    .bukti-grid { break-inside:avoid; }
    .tbl-ttd { break-inside:avoid; }
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

<div class="judul-utama">FR.AK-01. PERSETUJUAN ASESMEN DAN KERAHASIAAN</div>
<div class="sub-judul">
    Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen
</div>

<table class="tbl-skema">
    <tr><td class="td-skema-kiri">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
    <td style="width:12%">Judul</td><td>:</td><td><?= h($apl1['judul_skema']) ?></td>
    </tr>
    <tr><td></td><td>Nomor</td><td>:</td><td><?= h($apl1['nomor_skema']) ?></td></tr>
</table>

<table class="tbl-skema">
    <tr>
        <td class="th-kiri">TUK</td>
        <td class="th-sep">:</td>
        <td><?= $tuk_html ?></td>
    </tr>
    <tr><td class="td-skema-kiri">Nama Asesor</td><td>:</td><td><?= h($apl1['nama_asesor'] ?? '-') ?></td></tr>
    <tr><td class="td-skema-kiri">Nama Asesi</td><td>:</td><td><?= h($nama_asesi) ?></td></tr>
</table>

<div style="font-weight:bold; margin:16px 0 6px;">Bukti yang akan dikumpulkan</div>
<div class="bukti-grid">
    <?php foreach ($bukti_labels as $lbl): ?>
    <div class="bukti-item">
        <span class="bukti-check <?= in_array($lbl, $saved_bukti) ? 'checked' : '' ?>"></span>
        <span><?= h($lbl) ?></span>
    </div>
    <?php endforeach; ?>
    <div class="bukti-item" style="grid-column:1/-1;">
        <span class="bukti-check <?= ($lainnya != '') ? 'checked' : '' ?>"></span>
        <span>Lainnya :</span>
        <span><?= h($lainnya ?: '__________________') ?></span>
    </div>
</div>

<div style="font-weight:bold; margin:16px 0 6px;">Pelaksanaan asesmen disepakati pada</div>
<table class="tbl-pelaksanaan">
    <tr><td style="width:30%">Hari / Tanggal</td><td>:</td><td><?= h($ak01['hari_tanggal'] ?? '') ?></td></tr>
    <tr><td>Waktu</td><td>:</td><td><?= h($ak01['waktu'] ?? '') ?></td></tr>
    <tr><td>TUK pelaksanaan (nama / alamat)</td><td>:</td><td><?= h($ak01['tuk_pelaksanaan'] ?? '') ?></td></tr>
</table>

<div class="pernyataan">
    <strong>Asesor</strong><br>
    Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor
    dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang
    sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
</div>
<div class="pernyataan">
    <strong>Asesi</strong><br>
    Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan
    untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
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
        <td style="width:45%;">Asesor : <?= h($apl1['nama_asesor'] ?? '-') ?><br>
        <span style="font-size:9pt;color:#555;">No. Reg : <?= h($apl1['no_reg'] ?? '-') ?></span>
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