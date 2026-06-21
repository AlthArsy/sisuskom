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

$tuk_db = '';
if ($id_asesi && $id_apl1_db) {
    $qa = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tuk FROM tb_ak01
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         ORDER BY id_ak01 DESC LIMIT 1"));
    $tuk_db = $qa['tuk'] ?? '';
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

function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$active_tab = (!$is_asesi && isset($_GET['tab']) && $_GET['tab'] === 'kunci')
    ? 'kunci' : 'soal';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.IA.06A – Pertanyaan Tertulis Esai</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .field-readonly {
            padding:6px 8px; border:1px solid #e0e0e0; border-radius:4px;
            background:#f5f5f5; font-size:14px; color:#090a10;
            min-height:34px; line-height:1.5;
        }
        .field-readonly.empty { color:#999; font-style:italic; }

        .tab-nav {
            display:flex; gap:0; margin:18px 0 0;
            border-bottom:2px solid #cadbfc;
        }
        .tab-btn {
            padding:9px 22px; font-size:13px; font-weight:bold;
            background:#f0f3ff; border:1px solid #cadbfc;
            border-bottom:none; border-radius:5px 5px 0 0;
            cursor:pointer; color:#555; margin-right:4px;
            transition:background .15s;
        }
        .tab-btn.active {
            background:#fff; color:#1a237e;
            border-bottom:2px solid #fff; margin-bottom:-2px;
        }
        .tab-btn:hover:not(.active) { background:#dde5ff; }
        .tab-content { display:none; padding:14px 0; }
        .tab-content.active { display:block; }

        .soal-card {
            border:1px solid #dde3f5; border-radius:6px;
            padding:13px 16px; margin-bottom:10px; background:#fafbff;
            display:flex; gap:10px; align-items:flex-start;
        }
        .soal-no {
            min-width:28px; font-weight:bold; color:#1a237e;
            font-size:14px; flex-shrink:0; padding-top:1px;
        }
        .soal-text { font-size:13px; line-height:1.7; color:#222; }

        .kunci-card {
            border:1px solid #c8e6c9; border-radius:6px;
            padding:13px 16px; margin-bottom:12px; background:#f1faf2;
        }
        .kunci-soal-label {
            font-size:12px; color:#555; margin-bottom:6px;
            display:flex; gap:8px; align-items:flex-start;
        }
        .kunci-no {
            min-width:28px; font-weight:bold; color:#1b5e20;
            font-size:13px; flex-shrink:0;
        }
        .kunci-soal-text { font-size:12px; color:#555; line-height:1.6; }
        .kunci-jawaban-wrap {
            border-top:1px dashed #a5d6a7; margin-top:8px; padding-top:7px;
        }
        .kunci-label {
            font-size:11px; font-weight:bold; color:#388e3c; margin-bottom:4px;
        }
        .kunci-text { font-size:13px; line-height:1.7; color:#1b5e20; white-space:pre-wrap; }

        .empty-box {
            text-align:center; padding:28px; color:#aaa;
            font-size:13px; border:1px dashed #ccc; border-radius:5px; margin:12px 0;
        }
        .soal-count-badge {
            display:inline-block; background:#e8eaf6; color:#1a237e;
            font-size:11px; padding:2px 9px; border-radius:12px;
            font-weight:bold; margin-left:8px; vertical-align:middle;
        }
        @media(max-width:600px){ .tab-btn{ padding:7px 12px; font-size:12px; } }
    </style>
</head>
<body>
<div class="form-box">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px; border-radius:6px 6px 0 0;">
        FR.IA.06A <?= !$is_asesi ? '/ 06B' : '' ?> &nbsp;–&nbsp; DPT PERTANYAAN TERTULIS ESAI
    </h2>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px;
                background:#fafbff; margin:16px 0 4px;">
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

    <div class="tab-nav">
        <button type="button"
                class="tab-btn <?= $active_tab === 'soal' ? 'active' : '' ?>"
                onclick="switchTab('soal')">
            FR.IA.06A – Pertanyaan
            <span class="soal-count-badge"><?= count($soal_list) ?></span>
        </button>
        <?php if (!$is_asesi): ?>
        <button type="button"
                class="tab-btn <?= $active_tab === 'kunci' ? 'active' : '' ?>"
                onclick="switchTab('kunci')">
            FR.IA.06B – Kunci Jawaban
        </button>
        <?php endif; ?>
    </div>

    <div id="tab-soal" class="tab-content <?= $active_tab === 'soal' ? 'active' : '' ?>">

        <div class="section-title" style="margin:14px 0 10px;">
            JAWAB SEMUA PERTANYAAN DIBAWAH INI :
        </div>

        <?php if (empty($soal_list)): ?>
            <div class="empty-box">
                <?php if (!$id_ia06a_db): ?>
                    Daftar pertanyaan belum dikonfigurasi untuk skema ini.<br>
                    <span style="font-size:11px; color:#bbb;">
                        (tb_ia06a belum ada record untuk skema ini)
                    </span>
                <?php else: ?>
                    Belum ada soal di tb_soal untuk id_ia06a = <?= $id_ia06a_db ?>.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($soal_list as $s): ?>
                <div class="soal-card">
                    <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                    <span class="soal-text"><?= h($s['soal']) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <?php if (!$is_asesi): ?>
    <div id="tab-kunci" class="tab-content <?= $active_tab === 'kunci' ? 'active' : '' ?>">

        <div class="section-title" style="margin:14px 0 10px;">KUNCI JAWABAN</div>

        <?php if (empty($soal_list)): ?>
            <div class="empty-box">Belum ada soal – kunci jawaban tidak tersedia.</div>
        <?php else: ?>
            <?php foreach ($soal_list as $s): ?>
                <div class="kunci-card">
                    <div class="kunci-soal-label">
                        <span class="kunci-no"><?= $s['no_urut'] ?>.</span>
                        <span class="kunci-soal-text"><?= h($s['soal']) ?></span>
                    </div>
                    <div class="kunci-jawaban-wrap">
                        <div class="kunci-label">✔ KUNCI JAWABAN :</div>
                        <?php if (!empty($s['kunci_jawaban'])): ?>
                            <div class="kunci-text"><?= h($s['kunci_jawaban']) ?></div>
                        <?php else: ?>
                            <div style="color:#aaa; font-style:italic; font-size:12px;">
                                Kunci jawaban belum diisi.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:22px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='rekap_ia06.php?id_asesi=<?= $id_asesi ?>'">
            &lt;- BACK
        </button>
        <button type="button" class="btn-submit"
            onclick="window.location.href='FR_IA06C.php?id_asesi=<?= $id_asesi ?>'">
            Lembar Jawaban (06C) →
        </button>
    </div>

</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    const el = document.getElementById('tab-' + tab);
    if (el) el.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick') === "switchTab('" + tab + "')") {
            b.classList.add('active');
        }
    });
}
</script>
</body>
</html>