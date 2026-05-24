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

$apl1 = null;
if ($id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.id_apl1, a.id_skema, a.judul_skema, a.nomor_skema,
                s.standar_kompetensi_kerja,
                as2.nama_asesor, as2.no_reg, as2.id_asesor
         FROM tb_apl1 a
         JOIN tb_skema s ON s.id_skema = a.id_skema
         LEFT JOIN tb_asesor as2 ON as2.id_asesor = s.id_asesor
         WHERE a.id_asesi = '$id_asesi'
         ORDER BY a.id_apl1 ASC LIMIT 1"));
}
$nama_asesi_db = '';
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
    $nama_asesi_db = $r['nama_asesi'] ?? '';
}
$bukti_list = [];
if ($id_asesi) {
    $rb = mysqli_query($koneksi,
        "SELECT bd.bukti_dasar, ibd.kondisi
         FROM tb_isi_bukti_dasar ibd
         JOIN tb_bukti_dasar bd ON bd.id_bd = ibd.id_bd
         WHERE ibd.id_asesi = '$id_asesi'
         ORDER BY ibd.id_bd ASC");
    while ($b = mysqli_fetch_assoc($rb)) {
        $bukti_list[] = $b['bukti_dasar'] . ' [' . $b['kondisi'] . ']';
    }
}
$bukti_text = implode("\n", $bukti_list);

$apl2_exist = null;
if ($id_asesi) {
    $apl2_exist = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_apl2 WHERE id_asesi='$id_asesi' ORDER BY id_apl2 DESC LIMIT 1"));
}
$id_skema = intval($apl1['id_skema'] ?? 0);
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
            $rk = mysqli_query($koneksi, "SELECT * FROM tb_kuk WHERE id_elemen='$id_el'");
            $el['kuk'] = [];
            while ($k = mysqli_fetch_assoc($rk)) $el['kuk'][] = $k;
            $u['elemen'][] = $el;
        }
        $units[] = $u;
    }
}
$jawaban_exist = [];
if ($apl2_exist) {
    $id_apl2_q = intval($apl2_exist['id_apl2']);
    $rj = mysqli_query($koneksi,
        "SELECT id_elemen, nilai FROM detail_apl2
         WHERE id_apl2='$id_apl2_q' AND nilai != ''
           AND id_detail_apl2 IN (
               SELECT MAX(id_detail_apl2) FROM detail_apl2
               WHERE id_apl2='$id_apl2_q' AND nilai != ''
               GROUP BY id_elemen)");
    while ($j = mysqli_fetch_assoc($rj)) $jawaban_exist[$j['id_elemen']] = $j['nilai'];
}
$rekomendasi = $apl2_exist['rekomendasi'] ?? '';
$nama_asesor = $apl1['nama_asesor'] ?? '';
$no_reg      = $apl1['no_reg'] ?? '';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR. APL-02 - <?= h($nama_asesi_db) ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Calibri, Arial, sans-serif; font-size: 10pt; background:#ccc; color:#000; }

.noprint {
    background:#1565c0; padding:10px; text-align:center; position:sticky; top:0; z-index:999;
}
.noprint button {
    background:#fff; color:#1565c0; border:none; padding:8px 24px;
    font-size:13px; font-weight:bold; border-radius:4px; cursor:pointer; margin:0 4px;
}
.noprint a { color:#90caf9; font-size:12px; margin-left:12px; text-decoration:none; }

.halaman {
    width: 210mm;
    min-height: 297mm;
    margin: 12px auto;
    background: #fff;
    padding: 14mm 12mm 14mm 18mm;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    text-align: center;
    font-size: 12pt;
    font-weight: bold;
    border: 1.5px solid #000;
    padding: 6px 10px;
    margin-bottom: 6px;
}

.tbl-skema {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
    margin-bottom: 0;
    font-size: 10pt;
}
.tbl-skema td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
.tbl-skema .td-label { white-space: nowrap; font-weight: normal; }
.tbl-skema .td-sep   { width: 8px; text-align: center; }
.tbl-skema .td-val   { }
.tbl-skema .td-skema-kiri { width: 38%; font-size: 10pt; }

.tbl-panduan {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
    border-top: none;
    margin-bottom: 8px;
    font-size: 10pt;
}
.tbl-panduan td { border: 1px solid #000; padding: 5px 8px; }
.panduan-judul  { font-weight: bold; text-align: center; border-bottom: 1px solid #000; padding: 4px; }
.panduan-isi ul { margin: 4px 0 0 18px; padding: 0; }
.panduan-isi ul li { margin-bottom: 2px; }

.tbl-unit-header {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
    font-size: 10pt;
    margin-top: 8px;
}
.tbl-unit-header td { border: 1px solid #000; padding: 3px 6px; vertical-align: middle; }
.td-unit-nama { font-weight: bold; font-size: 10.5pt; width: 42%; }
.td-unit-info-lbl { white-space: nowrap; padding: 2px 6px !important; }
.td-unit-info-sep { width: 8px; text-align: center; padding: 2px 4px !important; }
.td-unit-info-val { padding: 2px 6px !important; }

.tbl-apl2 {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
    border-top: none;
    font-size: 10pt;
    margin-bottom: 0;
}
.tbl-apl2 th {
    border: 1px solid #000;
    padding: 4px 6px;
    text-align: center;
    font-weight: bold;
    background: #fff;
    font-size: 10pt;
}
.tbl-apl2 td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; font-size: 10pt; }
.col-dapatkah { width: 52%; }
.col-k  { width: 5%; text-align: center; vertical-align: middle; }
.col-bk { width: 5%; text-align: center; vertical-align: middle; }
.col-bukti { width: 38%; }

.cb { font-size: 13pt; line-height: 1; display: block; text-align: center; }

.kuk-wrap { font-size: 10pt; line-height: 1.4; }
.kuk-wrap .kuk-hdr { font-style: italic; display: block; margin: 3px 0 1px 0; font-size: 9.5pt; }
.kuk-wrap ul { margin: 0 0 0 14px; padding: 0; }
.kuk-wrap ul li { margin-bottom: 2px; font-size: 9.5pt; line-height: 1.35; }

.bukti-cell { font-size: 9.5pt; white-space: pre-line; line-height: 1.4; }

.tbl-ttd {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
    margin-top: 10px;
    font-size: 10pt;
}
.tbl-ttd td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
.ttd-rek-cell { width: 42%; }
.ttd-right-lbl { font-weight: bold; padding-bottom: 2px; }
.ttd-space { height: 40px; }
.ttd-nama-line {
    border-top: 1px solid #000;
    padding-top: 3px;
    margin-top: 6px;
    font-size: 10pt;
}

.page-break { page-break-before: always; }

@media print {
    body    { background: #fff; }
    .noprint { display: none !important; }
    .halaman {
        width: 100%; margin: 0; padding: 8mm 10mm 8mm 16mm;
        box-shadow: none;
    }

    .tbl-apl2 tr { page-break-inside: avoid; break-inside: avoid; }
    .tbl-apl2 td { page-break-inside: avoid; break-inside: avoid; }
    .tbl-apl2 { page-break-before: avoid; break-before: avoid; }
    .tbl-unit-header { page-break-after: avoid; break-after: avoid; }
    .unit-wrapper { page-break-inside: avoid; break-inside: avoid; }
}
@page { size: A4; margin: 0; }
</style>
</head>
<body>

<div class="noprint">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
    <a href="javascript:history.back()">← Kembali</a>
</div>

<div class="halaman">

<div class="judul-utama">FR. APL-02. ASESMEN MANDIRI</div>

<table class="tbl-skema">
    <tr>
        <td class="td-skema-kiri" rowspan="2">
            Skema Sertifikasi:<br>
            <span style="font-size:9.5pt;">(KKNI/Okupasi/Klaster)</span>
        </td>
        <td class="td-label">Judul</td>
        <td class="td-sep">:</td>
        <td class="td-val"><?= h($apl1['judul_skema'] ?? '') ?></td>
    </tr>
    <tr>
        <td class="td-label">Nomor</td>
        <td class="td-sep">:</td>
        <td class="td-val"><?= h($apl1['nomor_skema'] ?? '') ?></td>
    </tr>
</table>

<table class="tbl-panduan">
    <tr><td colspan="1">
        <div class="panduan-judul">PANDUAN ASESMEN MANDIRI</div>
        <div class="panduan-isi" style="padding:4px 2px 2px 2px;">
            <strong>Instruksi:</strong>
            <ul>
                <li>Baca setiap pertanyaan di kolom sebelah kiri</li>
                <li>Beri tanda centang (√) pada kotak jika Anda yakin dapat melakukan tugas yang dijelaskan.</li>
                <li>Isi kolom di sebelah kanan dengan menuliskan bukti yang relevan anda miliki untuk menunjukkan bahwa anda melakukan pekerjaan.</li>
            </ul>
        </div>
    </td></tr>
</table>

<?php foreach ($units as $ui => $u): ?>

<div class="unit-wrapper">
<?php if ($ui > 0): ?><div style="height:6px;"></div><?php endif; ?>

<table class="tbl-unit-header">
    <tr>
        <td class="td-unit-nama" rowspan="2">Unit Kompetensi <?= $ui+1 ?></td>
        <td class="td-unit-info-lbl">Kode Unit</td>
        <td class="td-unit-info-sep">:</td>
        <td class="td-unit-info-val"><?= h($u['kode_unit']) ?></td>
    </tr>
    <tr>
        <td class="td-unit-info-lbl">Judul Unit</td>
        <td class="td-unit-info-sep">:</td>
        <td class="td-unit-info-val"><?= h($u['judul_unit']) ?></td>
    </tr>
</table>

<table class="tbl-apl2">
    <thead>
        <tr>
            <th class="col-dapatkah">Dapatkah Saya ......................... ?</th>
            <th class="col-k">K</th>
            <th class="col-bk">BK</th>
            <th class="col-bukti">Bukti yang relevan</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($u['elemen'] as $el):
        $nilai = $jawaban_exist[$el['id_elemen']] ?? '';
        $cb_K  = ($nilai === 'K')  ? '☑' : '☐';
        $cb_BK = ($nilai === 'BK') ? '☑' : '☐';
    ?>
    <tr>
        <td class="col-dapatkah">
            <div class="kuk-wrap">
                <strong><?= h($el['no_elemen']) ?>. Elemen: <?= h($el['nama_elemen']) ?></strong>
                <span class="kuk-hdr">&#9679; Kriteria Unjuk Kerja:</span>
                <ul>
                    <?php foreach ($el['kuk'] as $k): ?>
                    <li><?= h($k['no_kuk']) ?> <?= h($k['kuk']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </td>
        <td class="col-k"><span class="cb"><?= $cb_K ?></span></td>
        <td class="col-bk"><span class="cb"><?= $cb_BK ?></span></td>
        <td class="col-bukti bukti-cell"><?= nl2br(h($bukti_text)) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endforeach; ?>

<div class="unit-wrapper">
<?php if ($ui > 0): ?><div style="height:6px;"></div><?php endif; ?>

<table class="tbl-ttd">
    <tr>
        <td class="ttd-rek-cell" rowspan="7" style="vertical-align:top;">
            <strong>Rekomendasi Untuk Asesi:</strong><br><br>
            Asesmen dapat / tidak dapat dilanjutkan melalui<br>
            pendekatan &nbsp;
            <?php if ($rekomendasi): ?>
                <strong><?= h($rekomendasi) ?></strong>
            <?php else: ?>
                ……………………………………
            <?php endif; ?>
        </td>
        <td colspan="2" style="font-weight:bold; padding:4px 8px;">Asesi :</td>
    </tr>
    <tr>
        <td style="width:30%; padding:4px 8px;">Nama</td>
        <td style="padding:4px 8px;"><?= h($nama_asesi_db) ?></td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Tanda tangan/<br>Tanggal</td>
        <td style="height:45px; padding:4px 8px;"></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold; padding:4px 8px; border-top:1.5px solid #000;">
            Ditinjau Oleh Asesor :
        </td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Nama :</td>
        <td style="padding:4px 8px;"><?= h($nama_asesor) ?></td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">No. Reg:</td>
        <td style="padding:4px 8px;"><?= h($no_reg) ?></td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Tanda tangan/<br>Tanggal</td>
        <td style="height:45px; padding:4px 8px;"></td>
    </tr>
</table>
</div>

</div>

<?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == 1): ?>
<script>window.onload = function(){ window.print(); };</script>
<?php endif; ?>
</body>
</html>