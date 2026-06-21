<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}
function h($v) { return htmlspecialchars((string)$v); }
function field($label, $value, $colspan = false) {
    $val = $value !== '' && $value !== null ? htmlspecialchars((string)$value) : '&nbsp;';
    $cs  = $colspan ? ' colspan="3"' : '';
    return "<tr><td class='f-label'>$label</td><td class='f-sep'>:</td><td class='f-val'$cs>$val</td></tr>";
}

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$asesi = null;
if ($id_asesi) {
    $asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
}

$apl1 = null;
if ($id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_apl1 WHERE id_asesi='$id_asesi' ORDER BY id_apl1 DESC LIMIT 1"));
}
$id_skema = intval($apl1['id_skema'] ?? 0);

$skema = null;
if ($id_skema) {
    $skema = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_skema WHERE id_skema='$id_skema' LIMIT 1"));
}

$units = [];
if ($id_skema) {
    $ru = mysqli_query($koneksi,
        "SELECT * FROM tb_unit_kompetensi WHERE id_skema='$id_skema' ORDER BY id_unit ASC");
    while ($u = mysqli_fetch_assoc($ru)) $units[] = $u;
}

$bukti_dasar = [];
if ($id_skema) {
    $rb = mysqli_query($koneksi,
        "SELECT bd.id_bd, bd.bukti_dasar,
                COALESCE(ibd.kondisi,'') AS kondisi
         FROM tb_bukti_dasar bd
         LEFT JOIN tb_isi_bukti_dasar ibd
               ON ibd.id_bd = bd.id_bd AND ibd.id_asesi = '$id_asesi'
         WHERE bd.id_skema = '$id_skema'
         ORDER BY bd.id_bd ASC");
    while ($b = mysqli_fetch_assoc($rb)) $bukti_dasar[] = $b;
}

$bukti_adm = [];
if ($id_skema) {
    $ra = mysqli_query($koneksi,
        "SELECT ba.id_ba, ba.bukti_adm,
                COALESCE(iba.kondisi,'') AS kondisi
         FROM tb_bukti_adm ba
         LEFT JOIN tb_isi_bukti_adm iba
               ON iba.id_ba = ba.id_ba AND iba.id_asesi = '$id_asesi'
         WHERE ba.id_skema = '$id_skema'
         ORDER BY ba.id_ba ASC");
    while ($b = mysqli_fetch_assoc($ra)) $bukti_adm[] = $b;
}

$nama_admin = '';
$id_user    = intval($_SESSION['id_user'] ?? 0);
if ($id_user) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.nama_admin FROM users u
         JOIN tb_admin a ON a.id_admin = u.id_admin
         WHERE u.id_user = '$id_user' LIMIT 1"));
    $nama_admin = $r['nama_admin'] ?? '';
}
if (!$nama_admin) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.nama_admin FROM tb_admin a
         JOIN users u ON u.id_admin = a.id_admin
         WHERE u.role = 'Admin_lsp' ORDER BY a.id_admin ASC LIMIT 1"));
    $nama_admin = $r['nama_admin'] ?? '-';
}

$qr_asesi = "Nama: "          . ($asesi['nama_asesi']   ?? '')
          . "\nNIK: "          . ($asesi['nik']           ?? '')
          . "\nJenis Kelamin: " . ($asesi['jenis_kelamin'] ?? '')
          . "\nPhone: "         . ($asesi['phone_rumah']   ?? '-');

$qr_admin = "Nama Admin LSP: " . $nama_admin;

function cb($kondisi, $check) {
    return $kondisi === $check ? '☑' : '☐';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak FR.APL.01 – <?= h($asesi['nama_asesi'] ?? '') ?></title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Calibri, Arial, sans-serif; font-size:10pt; background:#bbb; color:#000; }

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
.mode-btn.active  { background:#fff; color:#1565c0; }
.mode-btn:hover:not(.active) { background:rgba(255,255,255,.2); }
.toolbar-sep { flex:1; }
.btn-print {
    background:#fff; color:#1565c0; border:none;
    padding:7px 22px; border-radius:4px; font-size:13px;
    font-weight:bold; cursor:pointer;
}
.btn-print:hover { background:#e3f2fd; }
.btn-back { color:#90caf9; font-size:12px; text-decoration:none; }

.halaman {
    width:210mm; min-height:297mm;
    margin:12px auto; background:#fff;
    padding:14mm 12mm 14mm 18mm;
    box-shadow:0 2px 12px rgba(0,0,0,.2);
}

.judul-utama {
    text-align:center; font-size:11.5pt; font-weight:bold;
    border:1.5px solid #000; padding:7px 10px; margin-bottom:8px;
}
.bagian-title {
    font-weight:bold; font-size:10.5pt;
    border:1px solid #000; background:#fff;
    padding:4px 8px; margin:10px 0 6px 0;
}
.bagian-sub {
    font-size:10pt; margin:6px 0 4px 0; font-weight:bold;
}
.bagian-desc {
    font-size:9.5pt; margin-bottom:6px; font-style:italic;
}

.tbl-profil {
    width:100%; border-collapse:collapse;
    font-size:10pt; margin-bottom:6px;
}
.tbl-profil td { padding:3px 5px; vertical-align:top; }
.f-label { white-space:nowrap; width:36%; }
.f-sep   { width:6px; text-align:center; }
.f-val   { border-bottom:1px solid #000; min-width:80px; }
.f-val-inline { display:flex; gap:16px; flex-wrap:wrap; }
.f-val-inline span { display:inline-flex; gap:4px; align-items:center; }

.tbl-2col {
    width:100%; border-collapse:collapse; font-size:10pt; margin-bottom:3px;
}
.tbl-2col td { padding:3px 5px; vertical-align:top; }
.tbl-2col .f-val { border-bottom:1px solid #000; }

.tbl-skema {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:6px;
}
.tbl-skema td { border:1px solid #000; padding:4px 6px; vertical-align:top; }
.td-skema-kiri { width:38%; }

.tbl-tujuan {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:10pt; margin-bottom:6px;
}
.tbl-tujuan td { border:1px solid #000; padding:4px 8px; vertical-align:middle; }
.tujuan-label { width:32%; }
.tujuan-sep   { width:8px; text-align:center; }
.tujuan-val   { }

.tbl-unit {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:9.5pt; margin-bottom:8px;
}
.tbl-unit th, .tbl-unit td {
    border:1px solid #000; padding:4px 6px; vertical-align:top; text-align:left;
}
.tbl-unit th { text-align:center; font-weight:bold; }
.tbl-unit .col-no    { width:5%;  text-align:center; }
.tbl-unit .col-kode  { width:22%; }
.tbl-unit .col-judul { width:48%; }
.tbl-unit .col-skkni { width:25%; }

.tbl-bukti {
    width:100%; border-collapse:collapse;
    border:1px solid #000; font-size:9.5pt; margin-bottom:8px;
}
.tbl-bukti th, .tbl-bukti td {
    border:1px solid #000; padding:4px 6px;
    text-align:center; vertical-align:middle;
}
.tbl-bukti td:nth-child(2) { text-align:left; }
.tbl-bukti .cb-cell { font-size:13pt; }

.tbl-ttd {
    width:100%; border-collapse:collapse;
    border:1px solid #000; margin-top:10px; font-size:10pt;
}
.tbl-ttd td { border:1px solid #000; padding:6px 8px; vertical-align:top; }
.ttd-rek-top  { width:48%; }
.ttd-rek-bot  { width:48%; }

.ttd-area  { height:100px; display:block; }
.ttd-qr-box { display:none; justify-content:center; align-items:center; padding:2px 0; }
.ttd-qr-box canvas, .ttd-qr-box img { width:80px !important; height:80px !important; }
.ttd-nama-line { border-top:1px solid #000; padding-top:3px; margin-top:4px; }

.mode-badge { display:inline-block; font-size:8pt; padding:1px 6px; border-radius:10px;
              margin-left:6px; vertical-align:middle; font-weight:normal; }

.page-break { page-break-before:always; }

@media print {
    body      { background:#fff; }
    .toolbar  { display:none !important; }
    .halaman  { width:100%; margin:0; padding:8mm 10mm 8mm 16mm; box-shadow:none; }
    .tbl-bukti tr, .tbl-unit tr { page-break-inside:avoid; break-inside:avoid; }
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

<div class="judul-utama">FR.APL.01. FORMULIR PERMOHONAN SERTIFIKASI KOMPETENSI</div>

<div class="bagian-title">Bagian 1 : Rincian Data Pemohon Sertifikasi</div>
<div class="bagian-desc">Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data pekerjaan anda pada saat ini.</div>

<div class="bagian-sub">a. Data Pribadi</div>
<table class="tbl-profil">
    <?= field('Nama', $asesi['nama_asesi'] ?? '') ?>
    <?= field('No. KTP / NIK / Paspor', $asesi['nik'] ?? '') ?>
    <tr>
        <td class="f-label">Jenis Kelamin</td>
        <td class="f-sep">:</td>
        <td class="f-val">
            <?php
            $jk = $asesi['jenis_kelamin'] ?? '';
            if ($jk === 'Laki-laki') echo '<u>Laki-laki</u> / Perempuan';
            elseif ($jk === 'Perempuan') echo 'Laki-laki / <u>Perempuan</u>';
            else echo 'Laki-laki / Perempuan';
            ?>
            <span style="font-size:8.5pt;"> *</span>
        </td>
    </tr>
    <?= field('Kebangsaan',    $asesi['kebangsaan']   ?? '') ?>
    <?= field('Alamat Rumah',  $asesi['alamat_rumah'] ?? '') ?>
    <?= field('Kode Pos',      $asesi['kode_pos']     ?? '') ?>
</table>

<table class="tbl-2col">
    <tr>
        <td style="width:40%;white-space:nowrap;">Phone / E-mail</td>
        <td style="width:6px;text-align:center;">:</td>
        <td style="width:26%;">Rumah : <span style="border-bottom:1px solid #000;display:inline-block;min-width:80px;"><?= h($asesi['phone_rumah'] ?? '') ?></span></td>
        <td style="width:28%;">Kantor : <span style="border-bottom:1px solid #000;display:inline-block;min-width:80px;"><?= h($asesi['phone_kantor'] ?? '') ?></span></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>HP : <span style="border-bottom:1px solid #000;display:inline-block;min-width:80px;"><?= h($asesi['hp'] ?? '') ?></span></td>
        <td>E-mail : <span style="border-bottom:1px solid #000;display:inline-block;min-width:80px;"><?= h($asesi['email'] ?? '') ?></span></td>
    </tr>
</table>
<table class="tbl-profil">
    <?= field('Kualifikasi / Pendidikan', $asesi['pendidikan'] ?? '') ?>
</table>
<div style="font-size:8.5pt;margin:2px 0 8px 0;">*) Coret yang tidak perlu</div>

<div class="bagian-sub">b. Data Pekerjaan Sekarang</div>
<table class="tbl-profil">
    <?= field('Nama Institusi / Perusahaan', $asesi['nama_institusi'] ?? '') ?>
    <?= field('Jabatan',                     $asesi['jabatan']         ?? '') ?>
    <?= field('Alamat kantor',               $asesi['alamat_institusi'] ?? '') ?>
    <?= field('Kode Pos',                    $asesi['kode_pos_institusi'] ?? '') ?>
</table>
<table class="tbl-2col">
    <tr>
        <td style="width:40%;white-space:nowrap;">No. Telp / Fax / E-mail</td>
        <td style="width:6px;text-align:center;">:</td>
        <td style="width:26%;">Telp : <span style="border-bottom:1px solid #000;display:inline-block;min-width:70px;"><?= h($asesi['telp_institusi'] ?? '') ?></span></td>
        <td style="width:28%;">Fax : <span style="border-bottom:1px solid #000;display:inline-block;min-width:70px;"><?= h($asesi['fax'] ?? '') ?></span></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="2">E-mail : <span style="border-bottom:1px solid #000;display:inline-block;min-width:120px;"><?= h($asesi['email_institusi'] ?? '') ?></span></td>
    </tr>
</table>

<div class="page-break"></div>

<div class="bagian-title">Bagian 2 : Data Sertifikasi</div>
<div class="bagian-desc" style="margin-bottom:8px;">
    Tuliskan Judul dan Nomor Skema Sertifikasi yang anda ajukan berikut Daftar Unit Kompetensi sesuai kemasan pada skema sertifikasi.
</div>

<table class="tbl-skema">
    <tr>
        <td class="td-skema-kiri" rowspan="2">
            Skema Sertifikasi:<br>
            <span style="font-size:9.5pt;">(KKNI/Okupasi/Klaster)</span>
        </td>
        <td style="white-space:nowrap;">Judul</td>
        <td style="width:8px;text-align:center;">:</td>
        <td><?= h($apl1['judul_skema'] ?? '') ?></td>
    </tr>
    <tr>
        <td>Nomor</td>
        <td style="text-align:center;">:</td>
        <td><?= h($apl1['nomor_skema'] ?? '') ?></td>
    </tr>
</table>

<table class="tbl-tujuan">
    <?php
    $tujuan = $apl1['tujuan_asesmen'] ?? '';
    $opts   = [
        'Sertifikasi',
        'Pengakuan Kompetensi Terkini (PKT)',
        'Rekognisi Pembelajaran Lampau (RPL)',
        'Lainnya',
    ];
    $first = true;
    foreach ($opts as $opt):
        $check = ($tujuan === $opt || ($opt === 'Lainnya' && $tujuan === 'Lainnya'));
        $mark  = $check ? '√' : '&nbsp;';
        if ($first): $first = false; ?>
        <tr>
            <td class="tujuan-label" rowspan="<?= count($opts) ?>">Tujuan Asesmen</td>
            <td class="tujuan-sep" rowspan="<?= count($opts) ?>">:</td>
            <td class="tujuan-val"><?= $mark ?> <?= h($opt) ?><?php if ($opt === 'Lainnya' && $apl1['tujuan_lainnya']): echo ' : ' . h($apl1['tujuan_lainnya']); endif; ?></td>
        </tr>
    <?php else: ?>
        <tr><td class="tujuan-val"><?= $mark ?> <?= h($opt) ?><?php if ($opt === 'Lainnya' && $apl1['tujuan_lainnya']): echo ' : ' . h($apl1['tujuan_lainnya']); endif; ?></td></tr>
    <?php endif;
    endforeach; ?>
</table>

<div style="font-size:10pt; margin-bottom:4px; font-weight:bold;">Daftar Unit Kompetensi sesuai kemasan:</div>
<table class="tbl-unit">
    <thead>
        <tr>
            <th class="col-no">No.</th>
            <th class="col-kode">Kode Unit</th>
            <th class="col-judul">Judul Unit</th>
            <th class="col-skkni">Standar Kompetensi Kerja</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($units as $ui => $u): ?>
    <tr>
        <td style="text-align:center;"><?= $ui+1 ?>.</td>
        <td><?= h($u['kode_unit']) ?></td>
        <td><?= h($u['judul_unit']) ?></td>
        <td><?= ($ui === 0) ? h($skema['standar_kompetensi_kerja'] ?? '') : '' ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="bagian-title">Bagian 3 : Bukti Kelengkapan Pemohon</div>
<div style="font-size:10pt;font-weight:bold;margin:6px 0 4px;">3.1 Bukti Persyaratan Dasar Pemohon</div>
<table class="tbl-bukti">
    <thead>
        <tr>
            <th rowspan="2" style="width:5%;">No.</th>
            <th rowspan="2">Bukti Persyaratan Dasar</th>
            <th colspan="2">Ada</th>
            <th rowspan="2" style="width:10%;">Tidak Ada</th>
        </tr>
        <tr>
            <th style="width:13%;">Memenuhi Syarat</th>
            <th style="width:16%;">Tidak Memenuhi Syarat</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($bukti_dasar as $i => $b): ?>
    <tr>
        <td><?= $i+1 ?>.</td>
        <td style="text-align:left;"><?= h($b['bukti_dasar']) ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Memenuhi Syarat') ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Tidak Memenuhi Syarat') ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Tidak Ada') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="page-break"></div>

<div style="font-size:10pt;font-weight:bold;margin:6px 0 4px;">Bukti Administratif</div>
<table class="tbl-bukti">
    <thead>
        <tr>
            <th rowspan="2" style="width:5%;">No.</th>
            <th rowspan="2">Bukti Administratif</th>
            <th colspan="2">Ada</th>
            <th rowspan="2" style="width:10%;">Tidak Ada</th>
        </tr>
        <tr>
            <th style="width:13%;">Memenuhi Syarat</th>
            <th style="width:16%;">Tidak Memenuhi Syarat</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($bukti_adm as $i => $b): ?>
    <tr>
        <td><?= $i+1 ?>.</td>
        <td style="text-align:left;"><?= h($b['bukti_adm']) ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Memenuhi Syarat') ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Tidak Memenuhi Syarat') ?></td>
        <td class="cb-cell"><?= cb($b['kondisi'], 'Tidak Ada') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table class="tbl-ttd">
    <tr>
        <td class="ttd-rek-top" rowspan="3" style="vertical-align:top;">
            <strong>Rekomendasi (diisi oleh LSP) :</strong><br><br>
            <span style="font-size:9.5pt;">Berdasarkan ketentuan persyaratan dasar, maka pemohon :</span><br><br>
            <?php $rek = $apl1['rekomendasi'] ?? ''; ?>
            <?php if ($rek === 'Diterima'): ?>
                <strong>Diterima</strong><s style="color:#aaa;">/Tidak diterima</s>
            <?php elseif ($rek === 'Tidak Diterima'): ?>
                <s style="color:#aaa;">Diterima/</s><strong>Tidak diterima</strong>
            <?php else: ?>
                <strong>Diterima/Tidak diterima *)</strong>
            <?php endif; ?>
            <span style="font-size:9.5pt;"> sebagai peserta sertifikasi</span><br>
            <span style="font-size:8.5pt;color:#666;">*) coret yang tidak sesuai</span>
        </td>
        <td colspan="2" style="font-weight:bold;">
            Pemohon/Kandidat :
            <span class="mode-badge badge-ttd"   id="badge-asesi-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-asesi-qr"  style="display:none;"></span>
        </td>
    </tr>
    <tr>
        <td style="width:22%;padding:4px 8px;">Nama</td>
        <td style="padding:4px 8px;"><?= h($apl1['nama_pemohon'] ?? $asesi['nama_asesi'] ?? '') ?></td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Tanda tangan/<br>tanggal</td>
        <td style="padding:4px 8px;min-height:60px;">
            <span class="ttd-area" id="area-asesi"></span>
            <div class="ttd-qr-box" id="qr-asesi-box"><div id="qr-asesi"></div></div>

        </td>
    </tr>

    <tr>
        <td class="ttd-rek-bot" rowspan="3" style="vertical-align:top;">
            <strong>Catatan :</strong><br><br>
            <?= h($apl1['catatan_admin'] ?? '') ?>
        </td>
        <td colspan="2" style="font-weight:bold;border-top:1.5px solid #000;">
            Admin LSP :
            <span class="mode-badge badge-ttd"   id="badge-admin-ttd"></span>
            <span class="mode-badge badge-qr"     id="badge-admin-qr"  style="display:none;"></span>
        </td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Nama</td>
        <td style="padding:4px 8px;"><?= h($nama_admin) ?></td>
    </tr>
    <tr>
        <td style="padding:4px 8px;">Tanda tangan/<br>Tanggal</td>
        <td style="padding:4px 8px;min-height:60px;">
            <span class="ttd-area" id="area-admin"></span>
            <div class="ttd-qr-box" id="qr-admin-box"><div id="qr-admin"></div></div>
        </td>
    </tr>
</table>

</div>


<script>
const QR_ASESI = <?= json_encode($qr_asesi) ?>;
const QR_ADMIN = <?= json_encode($qr_admin) ?>;
let qrAsesiDone = false, qrAdminDone = false;

function setMode(mode) {
    ['ttd','qr'].forEach(m =>
        document.getElementById('btn-'+m).classList.toggle('active', m === mode)
    );
    const isManual = mode === 'ttd', isQR = mode === 'qr';

    document.getElementById('area-asesi').style.display = isManual ? 'block' : 'none';
    document.getElementById('area-admin').style.display = isManual ? 'block' : 'none';
    document.getElementById('qr-asesi-box').style.display = isQR ? 'flex' : 'none';
    document.getElementById('qr-admin-box').style.display = isQR ? 'flex' : 'none';
    ['asesi','admin'].forEach(who => {
        document.getElementById('badge-'+who+'-ttd').style.display = isManual ? '' : 'none';
        document.getElementById('badge-'+who+'-qr' ).style.display = isQR     ? '' : 'none';
    });
    if (isQR) {
        if (!qrAsesiDone) {
            new QRCode(document.getElementById('qr-asesi'), {
                text: QR_ASESI, width:80, height:80,
                colorDark:'#000', colorLight:'#fff',
                correctLevel: QRCode.CorrectLevel.M
            });
            qrAsesiDone = true;
        }
        if (!qrAdminDone) {
            new QRCode(document.getElementById('qr-admin'), {
                text: QR_ADMIN, width:80, height:80,
                colorDark:'#000', colorLight:'#fff',
                correctLevel: QRCode.CorrectLevel.M
            });
            qrAdminDone = true;
        }
    }
}

<?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == 1): ?>
window.onload = function(){ window.print(); };
<?php endif; ?>
</script>
</body>
</html>