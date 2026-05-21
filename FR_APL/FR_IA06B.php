<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../koneksi.php";

if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$role     = $_SESSION['role'] ?? '';
$is_asesi = ($role === 'Asesi');

// ── APL1 → Skema ─────────────────────────────────────────────────────────
$id_apl1_db     = 0;
$id_skema_db    = 0;
$judul_skema_db = '';
$nomor_skema_db = '';

if ($id_asesi) {
    $q = mysqli_query($koneksi,
        "SELECT a.id_apl1, a.id_skema, s.judul_skema, s.nomor_skema
         FROM tb_apl1 a
         JOIN tb_skema s ON a.id_skema = s.id_skema
         WHERE a.id_asesi = '$id_asesi'
         ORDER BY a.id_apl1 DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row            = mysqli_fetch_assoc($q);
        $id_apl1_db     = intval($row['id_apl1']);
        $id_skema_db    = intval($row['id_skema']);
        $judul_skema_db = $row['judul_skema'] ?? '';
        $nomor_skema_db = $row['nomor_skema']  ?? '';
    }
}

// ── Nama Asesi ───────────────────────────────────────────────────────────
$nama_asesi_db = '';
if ($id_asesi) {
    $rn = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
    $nama_asesi_db = $rn['nama_asesi'] ?? '';
}

// ── Asesor via tb_skema ──────────────────────────────────────────────────
$id_asesor_db    = 0;
$nama_asesor_db  = '';
$noreg_asesor_db = '';
if ($id_skema_db) {
    $qas = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ar.id_asesor, ar.nama_asesor, ar.no_reg
         FROM tb_skema sk
         JOIN tb_asesor ar ON sk.id_asesor = ar.id_asesor
         WHERE sk.id_skema='$id_skema_db' LIMIT 1"));
    if ($qas) {
        $id_asesor_db    = intval($qas['id_asesor']);
        $nama_asesor_db  = $qas['nama_asesor'] ?? '';
        $noreg_asesor_db = $qas['no_reg']       ?? '';
    }
}

// ── TUK dari AK01 ────────────────────────────────────────────────────────
$tuk_db = '';
if ($id_asesi && $id_apl1_db) {
    $qa = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tuk FROM tb_ak01
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak01 DESC LIMIT 1"));
    $tuk_db = $qa['tuk'] ?? '';
}

// ── tb_ia06a: master soal per skema+asesor ───────────────────────────────
// Struktur: id_ia06a, id_asesor, id_skema
$id_ia06a_db = 0;
if ($id_skema_db && $id_asesor_db) {
    $qi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_ia06a FROM tb_ia06a
         WHERE id_skema='$id_skema_db' AND id_asesor='$id_asesor_db'
         LIMIT 1"));
    $id_ia06a_db = intval($qi['id_ia06a'] ?? 0);
}

// ── Hitung soal: tb_soal.id_ia06a ───────────────────────────────────────
// Struktur: id_soal, id_ia06a, soal, kunci_jawaban
$jumlah_soal = 0;
if ($id_ia06a_db) {
    $qs = mysqli_query($koneksi,
        "SELECT COUNT(*) AS cnt FROM tb_soal WHERE id_ia06a='$id_ia06a_db'");
    if ($qs) $jumlah_soal = intval(mysqli_fetch_assoc($qs)['cnt']);
}

// ── tb_ia06: header sesi jawaban asesi ──────────────────────────────────
// Struktur: id_ia06, id_apl1, id_ia06a, id_asesor, id_asesi, umpan_balik
$id_ia06_db = 0;
if ($id_asesi && $id_apl1_db && $id_ia06a_db) {
    $qh = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_ia06 FROM tb_ia06
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
               AND id_ia06a='$id_ia06a_db'
         LIMIT 1"));
    $id_ia06_db = intval($qh['id_ia06'] ?? 0);
}

// ── Hitung jawaban: tb_ia06_jawaban ─────────────────────────────────────
// Struktur: id_jawaban, id_asesi, id_ia06, id_soal, jawaban_asesi, hasil
$jumlah_jawaban = 0;
if ($id_ia06_db) {
    $qj = mysqli_query($koneksi,
        "SELECT COUNT(*) AS cnt FROM tb_ia06_jawaban
         WHERE id_asesi='$id_asesi' AND id_ia06='$id_ia06_db'
               AND jawaban_asesi IS NOT NULL AND jawaban_asesi != ''");
    if ($qj) $jumlah_jawaban = intval(mysqli_fetch_assoc($qj)['cnt']);
}

function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.IA.06 – Pertanyaan Tertulis Esai</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .field-readonly {
            padding:6px 8px; border:1px solid #e0e0e0; border-radius:4px;
            background:#f5f5f5; font-size:14px; color:#090a10;
            min-height:34px; line-height:1.5;
        }
        .field-readonly.empty { color:#999; font-style:italic; }
        .card-nav { display:flex; gap:14px; flex-wrap:wrap; margin:20px 0; }
        .card-nav-item {
            flex:1; min-width:200px; border:1px solid #cdd6f0;
            border-radius:8px; padding:18px 20px; background:#fafbff;
            cursor:pointer; transition:box-shadow .2s, border-color .2s;
            text-decoration:none; color:inherit; display:block;
        }
        .card-nav-item:hover {
            box-shadow:0 3px 12px rgba(74,122,255,.18);
            border-color:#4A7AFF;
        }
        .card-icon { font-size:28px; margin-bottom:7px; }
        .card-title { font-weight:bold; font-size:14px; color:#1a237e; margin-bottom:5px; }
        .card-desc { font-size:12px; color:#666; margin-bottom:8px; }
        .card-badge {
            display:inline-block; font-size:11px;
            padding:2px 10px; border-radius:12px; font-weight:bold;
        }
        .badge-green  { background:#e6f4ea; color:#2e7d32; }
        .badge-orange { background:#fff3e0; color:#e65100; }
        .badge-blue   { background:#e8eaf6; color:#1a237e; }
        .badge-gray   { background:#f5f5f5; color:#888; border:1px solid #e0e0e0; }
        .locked-overlay { pointer-events:none; opacity:0.45; filter:grayscale(0.5); }
        @media(max-width:600px){ .card-nav{ flex-direction:column; } }
    </style>
</head>
<body>
<div class="form-box">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px; border-radius:6px 6px 0 0;">
        FR.IA.06 &nbsp;–&nbsp; DPT PERTANYAAN TERTULIS ESAI
    </h2>

    <!-- Info Header -->
    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px;
                background:#fafbff; margin:16px 0 14px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Skema Sertifikasi – Judul</label>
                <div class="field-readonly <?= $judul_skema_db ? '' : 'empty' ?>">
                    <?= $judul_skema_db ? h($judul_skema_db) : '— APL-01 belum diisi —' ?>
                </div>
            </div>
            <div style="flex:1; min-width:100px;">
                <label class="small-text">Nomor</label>
                <div class="field-readonly <?= $nomor_skema_db ? '' : 'empty' ?>">
                    <?= $nomor_skema_db ? h($nomor_skema_db) : '—' ?>
                </div>
            </div>
            <div style="flex:1; min-width:120px;">
                <label class="small-text">TUK</label>
                <div class="field-readonly <?= $tuk_db ? '' : 'empty' ?>">
                    <?= $tuk_db ? h($tuk_db) : '— AK-01 belum diisi —' ?>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
            <div style="flex:1; min-width:180px;">
                <label class="small-text">Nama Asesor</label>
                <div class="field-readonly <?= $nama_asesor_db ? '' : 'empty' ?>">
                    <?= $nama_asesor_db ? h($nama_asesor_db) : '— tidak ditemukan —' ?>
                </div>
                <?php if ($noreg_asesor_db): ?>
                    <span style="font-size:11px;color:#666;">&nbsp;No.Reg: <?= h($noreg_asesor_db) ?></span>
                <?php endif; ?>
            </div>
            <div style="flex:1; min-width:180px;">
                <label class="small-text">Nama Asesi</label>
                <div class="field-readonly <?= $nama_asesi_db ? '' : 'empty' ?>">
                    <?= $nama_asesi_db ? h($nama_asesi_db) : '— tidak ditemukan —' ?>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title" style="margin:18px 0 8px;">Dokumen Tersedia</div>

    <div class="card-nav">

        <!-- Card 06A / 06B -->
        <a class="card-nav-item <?= !$id_ia06a_db ? 'locked-overlay' : '' ?>"
           href="form_ia06a.php?id_asesi=<?= $id_asesi ?>">
            <div class="card-icon">📋</div>
            <?php if (!$is_asesi): ?>
                <div class="card-title">FR.IA.06A – Pertanyaan &amp; 06B – Kunci</div>
                <div class="card-desc">Daftar soal esai. Tab kunci jawaban (06B) hanya untuk asesor.</div>
            <?php else: ?>
                <div class="card-title">FR.IA.06A – Daftar Pertanyaan</div>
                <div class="card-desc">Lihat pertanyaan esai yang harus Anda jawab.</div>
            <?php endif; ?>
            <?php if (!$id_ia06a_db): ?>
                <span class="card-badge badge-gray">Soal belum dikonfigurasi</span>
            <?php else: ?>
                <span class="card-badge <?= $jumlah_soal > 0 ? 'badge-blue' : 'badge-orange' ?>">
                    <?= $jumlah_soal > 0 ? "$jumlah_soal pertanyaan tersedia" : 'Belum ada soal' ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Card 06C -->
        <a class="card-nav-item <?= !$id_ia06a_db ? 'locked-overlay' : '' ?>"
           href="form_ia06c.php?id_asesi=<?= $id_asesi ?>">
            <div class="card-icon">✏️</div>
            <div class="card-title">FR.IA.06C – Lembar Jawaban</div>
            <div class="card-desc">
                <?= $is_asesi
                    ? 'Isi jawaban esai untuk setiap pertanyaan.'
                    : 'Lihat jawaban yang telah diisi oleh asesi.' ?>
            </div>
            <?php if ($jumlah_soal > 0): ?>
                <span class="card-badge <?= $jumlah_jawaban >= $jumlah_soal
                    ? 'badge-green'
                    : ($jumlah_jawaban > 0 ? 'badge-orange' : 'badge-gray') ?>">
                    <?= $jumlah_jawaban ?>/<?= $jumlah_soal ?> dijawab
                </span>
            <?php else: ?>
                <span class="card-badge badge-gray">Soal belum tersedia</span>
            <?php endif; ?>
        </a>

    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:24px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='FR_AK03.php?id_asesi=<?= $id_asesi ?>'">
            &lt;- BACK
        </button>
    </div>

</div>
</body>
</html>