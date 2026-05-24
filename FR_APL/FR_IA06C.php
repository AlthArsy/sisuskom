<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$role     = $_SESSION['role'] ?? '';
$is_asesi = ($role === 'Asesi');

$e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);

// $flash_msg  = $_SESSION['flash_ia06c'] ?? '';
// $flash_type = '';
// if ($flash_msg) {
//     [$flash_type, $flash_msg] = explode('|', $flash_msg, 2);
//     unset($_SESSION['flash_ia06c']);
// }

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

$nama_asesi_db = '';
if ($id_asesi) {
    $rn = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
    $nama_asesi_db = $rn['nama_asesi'] ?? '';
}

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

$hari_tanggal_db = '';
$waktu_db        = '';
$tuk_db          = '';
$id_ak01_db      = 0;

if ($id_asesi) {
    $qak1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_ak01, tuk, hari_tanggal, waktu
         FROM tb_ak01
         WHERE id_asesi = '$id_asesi'
         ORDER BY id_ak01 DESC LIMIT 1"));
    if ($qak1) {
        $id_ak01_db      = intval($qak1['id_ak01']);
        $tuk_db          = $qak1['tuk'] ?? '';
        $hari_tanggal_db = $qak1['hari_tanggal'] ?? '';
        $waktu_db        = $qak1['waktu'] ?? '';
    }
}


$id_ia06a_db = 0;
if ($id_skema_db && $id_asesor_db) {
    $qi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_ia06a FROM tb_ia06a
         WHERE id_skema='$id_skema_db' AND id_asesor='$id_asesor_db'
         LIMIT 1"));
    $id_ia06a_db = intval($qi['id_ia06a'] ?? 0);
}


$soal_list = [];
if ($id_ia06a_db) {
    $rs = mysqli_query($koneksi,
        "SELECT id_soal, soal, kunci_jawaban
         FROM tb_soal
         WHERE id_ia06a='$id_ia06a_db'
         ORDER BY id_soal ASC");
    $no = 1;
    while ($r = mysqli_fetch_assoc($rs)) {
        $r['no_urut'] = $no++;
        $soal_list[] = $r;
    }
}

$id_ia06_db  = 0;
$aspek = '';
$umpan_balik = '';
if ($id_asesi && $id_apl1_db && $id_ia06a_db) {
    $qh = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_ia06, aspek, umpan_balik FROM tb_ia06
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
               AND id_ia06a='$id_ia06a_db'
         LIMIT 1"));
    if ($qh) {
        $id_ia06_db  = intval($qh['id_ia06']);
        $aspek = $qh['aspek'] ?? '';
        $umpan_balik = $qh['umpan_balik'] ?? '';

    }
}

$has_data = false;
if ($id_asesi && $id_skema_db) {
    $chk = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) AS cnt FROM tb_ia06
         WHERE id_asesi='{$e($id_asesi)}' AND id_apl1='{$e($id_apl1_db)}'"));
    $has_data = ($chk && $chk['cnt'] > 0);
}

$mode = 'create';
if (isset($_GET['mode']) && in_array($_GET['mode'], ['view', 'create'])) {
    $mode = $_GET['mode'];
} elseif ($has_data) {
    $mode = 'view';
}

$jawaban_saved = [];
if ($id_ia06_db) {
    $rj = mysqli_query($koneksi,
        "SELECT id_soal, jawaban_asesi, hasil FROM tb_ia06_jawaban
         WHERE id_asesi='$id_asesi' AND id_ia06='$id_ia06_db'");
    while ($j = mysqli_fetch_assoc($rj)) {
        $jawaban_saved[intval($j['id_soal'])] = $j;
    }
}

$ada_jawaban = !empty($jawaban_saved);
$mode = isset($_GET['mode']) ? $_GET['mode'] : ($ada_jawaban ? 'view' : 'edit');
if (!$is_asesi) $mode = 'view'; // asesor/admin selalu readonly

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_asesi) {

    if (!$id_asesi || !$id_apl1_db || !$id_ia06a_db || !$id_asesor_db) {
        $_SESSION['alert'] = 'Data tidak lengkap. Pastikan APL-01, skema, dan asesor sudah terkonfigurasi.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi");
        exit;
    }

    $e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);

    if (!$id_ia06_db) {
        $ins_h = mysqli_query($koneksi,
            "INSERT INTO tb_ia06 (id_apl1, id_ak01, id_ia06a, id_asesor, id_asesi)
             VALUES ('$id_apl1_db','$id_ak01_db','$id_ia06a_db','$id_asesor_db','$id_asesi')");
        if (!$ins_h) {
            $_SESSION['alert'] = 'Gagal membuat sesi jawaban: ' . mysqli_error($koneksi);
            header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi");
            exit;
        }
        $id_ia06_db = mysqli_insert_id($koneksi);
    }

    mysqli_query($koneksi,
        "DELETE FROM tb_ia06_jawaban
         WHERE id_asesi='$id_asesi' AND id_ia06='$id_ia06_db'");

    $gagal     = false;
    $jw_post   = $_POST['jawaban'] ?? [];
    $kosong  = 0;
    foreach ($soal_list as $s) {
        $jwb = trim($jw_post[intval($s['id_soal'])] ?? '');
        if ($jwb === '') $kosong++;
    }

    if ($kosong > 0) {
        $_SESSION['alert'] = "Masih ada $kosong pertanyaan yang belum dijawab!";
        header("Location:  ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi&mode=edit");
        exit;
    }

    foreach ($soal_list as $s) {
        $id_soal = intval($s['id_soal']);
        $jwb     = trim($jw_post[$id_soal] ?? '');
        if ($jwb === '') continue;

        $ins = mysqli_query($koneksi,
            "INSERT INTO tb_ia06_jawaban
                (id_asesi, id_ia06, id_soal, jawaban_asesi)
             VALUES
                ('$id_asesi','$id_ia06_db','$id_soal','{$e($jwb)}')");
        if (!$ins) $gagal = true;
    }

    if ($gagal) {
        $_SESSION['alert'] = 'Sebagian jawaban gagal disimpan: ' . mysqli_error($koneksi);;
    } else {
        $_SESSION['alert'] = 'Jawaban berhasil disimpan!';
    }
    header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi&mode=view");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_asesi
    && isset($_POST['aspek']) && isset($_POST['save_umpan_balik'])) {

    if (!$id_ia06_db) {
        $_SESSION['alert'] = "DEBUG: id_ia06_db kosong! id_asesi=$id_asesi, id_apl1=$id_apl1_db, id_ia06a=$id_ia06a_db";
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi&mode=view");
        exit;
    }

    $aspek = mysqli_real_escape_string($koneksi, trim($_POST['aspek'] ?? ''));
    $ub    = mysqli_real_escape_string($koneksi, trim($_POST['umpan_balik'] ?? ''));
    mysqli_query($koneksi,
        "UPDATE tb_ia06 SET aspek='$aspek', umpan_balik='$ub'
         WHERE id_ia06='$id_ia06_db'");

    $hasil_post = $_POST['hasil'] ?? [];
    foreach ($hasil_post as $id_soal => $nilai) {
        $id_soal = intval($id_soal);
        $nilai   = in_array($nilai, ['Benar','Salah']) ? $nilai : '';
        if ($nilai) {
            mysqli_query($koneksi,
                "UPDATE tb_ia06_jawaban SET hasil='$nilai'
                 WHERE id_ia06='$id_ia06_db' AND id_soal='$id_soal'");
        }
    }

    $_SESSION['alert'] = 'Penilaian berhasil disimpan!';
    header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA06C.php&id_asesi=$id_asesi&mode=view");
    exit;
}
function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$total   = count($soal_list);
$dijawab = 0;
foreach ($soal_list as $s) {
    if (!empty($jawaban_saved[intval($s['id_soal'])]['jawaban_asesi'])) $dijawab++;
}
$persen = $total > 0 ? round($dijawab / $total * 100) : 0;

$saved_vals = [];
$id_skema = $id_skema_db;
$id_apl1  = $id_apl1_db;

$saved_hdr = [
    'aspek'       => $aspek,
    'umpan_balik' => $umpan_balik,
];

$is_asesi       = ($role === 'Asesi');
$is_asesor      = ($role === 'Asesor' || $role === 'Admin_lsp' || $role === 'Admin_utm');

$dsb_untuk_asesi  = $is_asesi  ? '' : 'readonly';
$dsb_untuk_asesor = ($is_asesor) ? '' : 'disabled';
$dsb_style        = $is_asesi ? 'pointer-events:none; opacity:0.65;' : '';
$dsb              = $dsb_untuk_asesor;  
$lock_asesi       = $dsb_untuk_asesi;    

$tgl_form = $hari_tanggal_db ?: date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.IA.06C – Lembar Jawaban Esai</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .field-readonly {
            padding:6px 8px; border:1px solid #e0e0e0; border-radius:4px;
            background:#f5f5f5; font-size:14px; color:#090a10;
            
        }
        .field-readonly.empty { color:#999; font-style:italic; }

        .progress-wrap {
            background:#eef1ff; border:1px solid #c5cae9;
            border-radius:5px; padding:9px 13px; margin-bottom:14px;
            font-size:12px; display:flex; align-items:center; gap:10px;
        }
        .progress-bar-outer {
            flex:1; background:#dde5ff; border-radius:20px; height:8px;
        }
        .progress-bar-inner {
            height:8px; border-radius:20px; background:#4A7AFF; transition:width .3s;
        }

        .soal-jawaban-card {
            border:1px solid #dde3f5; border-radius:7px;
            padding:14px 16px; margin-bottom:14px; background:#fafbff;
        }
        .soal-header {
            display:flex; align-items:flex-start; gap:8px; margin-bottom:8px;
        }
        .soal-no {
            min-width:28px; font-weight:bold; color:#1a237e;
            font-size:14px; flex-shrink:0; padding-top:1px;
        }
        .soal-text { font-size:13px; line-height:1.7; color:#333; }
        .jawaban-label {
            font-size:11px; font-weight:bold; color:#555;
            margin-bottom:4px; display:block;
        }
        .jawaban-textarea {
            width:100%; min-height:80px; resize:vertical;
            font-size:13px; padding:7px 9px; line-height:1.6;
            border:1px solid #b0bec5; border-radius:5px;
            box-sizing:border-box; font-family:inherit; background:#fff;
        }
        .jawaban-textarea:focus {
            outline:none; border-color:#4A7AFF;
            box-shadow:0 0 0 2px rgba(74,122,255,.15);
        }
        .jawaban-view {
            padding:8px 10px; min-height:44px;
            border:1px solid #e0e0e0; border-radius:5px;
            background:#fff; font-size:13px; line-height:1.7;
            white-space:pre-wrap; color:#222;
        }
        .jawaban-view.empty { color:#aaa; font-style:italic; }

        .hasil-wrap {
            margin-top:8px; display:flex; align-items:center; gap:8px;
            flex-wrap:wrap;
        }
        .hasil-label { font-size:11px; font-weight:bold; color:#555; }
        .hasil-badge {
            font-size:12px; font-weight:bold; padding:2px 12px;
            border-radius:12px; display:inline-block;
        }
        .hasil-k  { background:#e6f4ea; color:#2e7d32; }
        .hasil-bk { background:#fde8e8; color:#b71c1c; }
        .hasil-empty { background:#f5f5f5; color:#aaa; font-style:italic;
                        font-weight:normal; }

        .umpan-balik-box {
            border:1px solid #dde3f5; border-radius:7px;
            padding:14px 16px; margin:12px 0; background:#fafbff;
        }

        .ttd-box { display:flex; gap:16px; flex-wrap:wrap; margin-top:16px; }
        .ttd-col {
            flex:1; min-width:200px; border:1px solid #ccc;
            border-radius:5px; padding:12px 14px; background:#fafbff;
        }
        .ttd-col .col-title {
            font-weight:bold; font-size:14px;
            border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:10px;
        }

        .flash { padding:10px 14px; border-radius:5px; margin-bottom:12px;
                 font-size:13px; font-weight:bold; }
        .flash-success { background:#e6f4ea; color:#2e7d32; border:1px solid #a5d6a7; }
        .flash-error   { background:#fde8e8; color:#b71c1c; border:1px solid #ef9a9a; }
        .flash-warning { background:#fff8e1; color:#e65100; border:1px solid #ffe082; }
        #alasan-rek-wrap {
            display: none;
            margin-top: 10px;
            padding: 10px 14px;
            background: #fff3f3;
            border: 1px solid #ef9a9a;
            border-radius: 6px;
        }
        #alasan-rek-wrap label {
            font-size: 12px;
            font-weight: 700;
            color: #c62828;
            display: block;
            margin-bottom: 6px;
        }
        #alasan-rek-wrap textarea {
            width: 100%;
            font-size: 12px;
            padding: 6px 8px;
            border: 1px solid #ef9a9a;
            border-radius: 4px;
            resize: vertical;
            min-height: 60px;
            background: #fff;
            color: #b71c1c;
            box-sizing: border-box;
        }

        @media(max-width:600px){ .ttd-box{ flex-direction:column; } }
    </style>
</head>
<body>
<div class="form-box">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px; border-radius:6px 6px 0 0;">
        FR.IA.06C &nbsp;–&nbsp; LEMBAR JAWABAN PERTANYAAN TERTULIS ESAI
    </h2>

    <?php if (!empty($_SESSION['alert'])): ?>
    <script>alert('<?= addslashes($_SESSION['alert']) ?>');</script>
    <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

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
                <!-- <php if ($noreg_asesor_db): ?>
                    <span style="font-size:11px;color:#666;">&nbsp;No.Reg: <= h($noreg_asesor_db) ?></span>
                ?php endif; ?> -->
            </div>
            <div style="flex:1; min-width:130px;">
                <label class="small-text">Nama Asesi</label>
                <div class="field-readonly <?= $nama_asesi_db ? '' : 'empty' ?>">
                    <?= $nama_asesi_db ? h($nama_asesi_db) : '— tidak ditemukan —' ?>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="flex:1;">
                <label class="small-text">Tanggal</label>
                <div class="field-readonly <?= $hari_tanggal_db ? '' : 'empty' ?>">
                    <?= $hari_tanggal_db ? h($hari_tanggal_db) : '— AK-01 belum diisi —' ?>
                </div>
            </div>
            <div style="flex:1;">
                <label class="small-text">Waktu</label>
                <div class="field-readonly <?= $waktu_db ? '' : 'empty' ?>">
                    <?= $waktu_db ? h($waktu_db) : '— AK-01 belum diisi —' ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($total > 0): ?>
    <div class="progress-wrap">
        <span style="white-space:nowrap; font-weight:bold; color:#1a237e;">
            <?= $dijawab ?>/<?= $total ?> dijawab
        </span>
        <div class="progress-bar-outer">
            <div class="progress-bar-inner" style="width:<?= $persen ?>%"></div>
        </div>
        <span style="white-space:nowrap; color:#555;"><?= $persen ?>%</span>
    </div>
    <?php endif; ?>
    <form method="post" id="formJawaban" autocomplete="off">
        <input type="hidden" name="id_asesi" value="<?= $id_asesi ?>">

        <div class="section-title" style="margin:4px 0 12px;">JAWABAN :</div>

        <?php if (empty($soal_list)): ?>
            <div style="text-align:center; padding:28px; color:#aaa;
                        font-size:13px; border:1px dashed #ccc; border-radius:5px;">
                Belum ada pertanyaan untuk skema ini.
            </div>
        <?php else: ?>
            <?php foreach ($soal_list as $s):
                $sid     = intval($s['id_soal']);
                $jdata   = $jawaban_saved[$sid] ?? [];
                $jwb     = $jdata['jawaban_asesi'] ?? '';
                $hasil   = $jdata['hasil'] ?? '';
                $has_ans = $jwb !== '';
            ?>
            <div class="soal-jawaban-card">
                <div class="soal-header">
                    <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                    <span class="soal-text"><?= h($s['soal']) ?></span>
                </div>
            
                <div>
                    <span class="jawaban-label">
                        Jawaban
                        <?php if (!$is_asesi && $has_ans): ?>
                            <span style="color:#388e3c;">(sudah diisi)</span>
                        <?php endif; ?>
                        :
                    </span>
                        
                    <?php if ($is_asesi && $mode === 'edit'): ?>
                        <textarea
                            name="jawaban[<?= $sid ?>]"
                            class="jawaban-textarea"
                            placeholder="Tulis jawaban Anda di sini..."
                            rows="4"><?= h($jwb) ?></textarea>
                    <?php else: ?>
                        <div class="jawaban-view <?= $has_ans ? '' : 'empty' ?>">
                            <?= $has_ans ? h($jwb) : 'Belum diisi oleh asesi.' ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="hasil-wrap" style="margin-top:8px;">
                    <span class="hasil-label">Hasil :</span>
                        <?php if ($is_asesi): ?>
                            <span style="font-weight:bold; color:<?= $hasil === 'Benar' ? '#388e3c' : ($hasil === 'Salah' ? '#c62828' : '#999') ?>">
                                <?= $hasil ?: 'Belum dinilai' ?>
                            </span>
                        <?php else: ?>
                        <?php endif; ?>
                </div>
                    
                <?php if (!$is_asesi && $has_ans): ?>
                <div class="hasil-wrap" style="margin-top:8px;">
                    <span class="hasil-label">Hasil :</span>
                    <label style="margin-right:12px; cursor:pointer;">
                        <input type="radio"
                               name="hasil[<?= $sid ?>]"
                               value="Benar"
                               <?= $hasil === 'Benar' ? 'checked' : '' ?>>
                        Benar
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio"
                               name="hasil[<?= $sid ?>]"
                               value="Salah"
                               <?= $hasil === 'Salah' ? 'checked' : '' ?>>
                        Salah
                    </label>
                </div>
                
                <div style="margin-top:8px; padding:8px 12px; background:#f0f7ff;
                            border-left:3px solid #1565c0; border-radius:4px; font-size:12px;">
                    <b style="color:#1565c0;">Kunci Jawaban :</b><br>
                    <span style="color:#333;"><?= nl2br(h($s['kunci_jawaban'] ?? '—')) ?></span>
                </div>
                <?php endif; ?>
                
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="section-title" style="margin:20px 0 10px;">Umpan Balik untuk Asesi</div>
        <div class="umpan-balik-box">
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:6px;
                        font-size:13px; <?= $dsb_style ?>">
                <label style="display:flex; align-items:center; gap:6px;">
                Aspek pengetahuan seluruh unit pada kelompok pekerjaan yang
                diujikan &nbsp;                
                </label>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="radio" name="aspek" value="tercapai"
                           id="rek_kompeten"
                           <?= $saved_hdr['aspek'] === 'tercapai' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b>TERCAPAI</b>
                </label>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="radio" name="aspek" value="belum_tercapai"
                           id="rek_belum"
                           <?= strtolower($saved_hdr['aspek'] ?? '') === 'belum_tercapai' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b>BELUM TERCAPAI</b>
                </label>
            </div>
            <!-- <php if (!$is_asesi): ?>
                <textarea name="umpan_balik" class="form-control" rows="3"
                          placeholder="Tuliskan umpan balik untuk asesi..."
                          style="font-size:13px;"><= h($umpan_balik) ?></textarea>
                <div style="font-size:11px; color:#888; margin-top:4px;">
                    * Diisi oleh Asesor setelah proses penilaian selesai.
                </div>
            <php else: ?>
                <div class="field-readonly <= $umpan_balik ? '' : 'empty' ?>"
                     style="min-height:50px;">
                    <= $umpan_balik ? h($umpan_balik) : '— Umpan balik dari asesor belum tersedia —' ?>
                </div>
            <php endif; ?> -->
                <?php if (strtolower($saved_hdr['aspek'] ?? '') === 'belum_tercapai' && $saved_hdr['umpan_balik']): ?>
                <!-- <div style="margin-top:10px; padding:10px 14px; background:#fff3f3;
                            border:1px solid #ef9a9a; border-radius:6px;">
                     <div style="font-size:12px; font-weight:700; color:#c62828; margin-bottom:4px;">
                        Alasan Belum Tercapai (Rekomendasi) :
                    </div> -->
                    <!-- <div style="font-size:13px; color:#b71c1c;">
                        <= nl2br(h($saved_hdr['alasan_rekomendasi'])) ?>
                    </div>
                </div> -->
                <?php endif; ?>
                <div id="alasan-rek-wrap"
                    style="<?= strtolower($saved_hdr['aspek']) === 'belum_tercapai' ? 'display:block;' : 'display:none;' ?>">
                    <label>⚠ Kenapa Belum Tercapai? (Penjelasan Aspek)</label>
                    <textarea name="umpan_balik"
                              placeholder="Jelaskan alasan Belum Tercapai..."
                              <?= $dsb_untuk_asesor ?>><?= h($saved_hdr['umpan_balik']) ?></textarea>
                </div>
        </div>

        <div class="ttd-box">
            <div class="ttd-col">
                <div class="col-title">Asesi</div>
                <div style="font-size:13px; margin-bottom:6px;">
                    <b>Nama :</b>
                    <span style="color:#1a237e;"><?= $nama_asesi_db ? h($nama_asesi_db) : '—' ?></span>
                </div>
            </div>
            <div class="ttd-col">
                <div class="col-title">Asesor</div>
                <div style="font-size:13px; margin-bottom:4px;">
                    <b>Nama :</b>
                    <span style="color:#1a237e;"><?= $nama_asesor_db ? h($nama_asesor_db) : '—' ?></span>
                </div>
                <?php if ($noreg_asesor_db): ?>
                    <div style="font-size:11px; color:#666; margin-bottom:4px;">
                        No.Reg: <?= h($noreg_asesor_db) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:20px;">
                    
        <?php if ($is_asesor): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/rekap_ia06.php'">
                Kembali
            </button>
            <button type="submit" class="btn-submit" name="save_umpan_balik">SIMPAN ✓</button>
        <?php endif; ?>
        
        <?php if ($is_asesi): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
                Kembali
            </button>
            <?php if (!$has_data): ?>
                <button type="submit" class="btn-submit" >SIMPAN ✓</button>
            <?php endif; ?>
        <?php endif; ?>
            
    </div>

    </form>

</div>

<script>
function toggleAlasanRek(val) {
    var wrap = document.getElementById('alasan-rek-wrap');
    if (!wrap) return;
    if (val === 'belum_tercapai') {
        wrap.style.display = 'block';
        var ta = wrap.querySelector('textarea');
        if (ta) ta.focus();
    } else {
        wrap.style.display = 'none';
        var ta = wrap.querySelector('textarea');
        if (ta) ta.value = '';
    }
}
</script>
</body>
</html>