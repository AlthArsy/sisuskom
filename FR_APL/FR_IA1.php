<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

if (!isset($_SESSION['username'])) {
    header('Location: ../LOGIN/login.php');
    exit;
}

$e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);
$role     = $_SESSION['role'] ?? '';
$is_asesi = ($role === 'Asesi');

$flash_msg  = $_SESSION['flash_ia01'] ?? '';
$flash_type = '';
if ($flash_msg) {
    [$flash_type, $flash_msg] = explode('|', $flash_msg, 2);
    unset($_SESSION['flash_ia01']);
}

$asesi = null;
if ($id_asesi) {
    $asesi = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_asesi WHERE id_asesi='{$e($id_asesi)}' LIMIT 1"));
}

$ak01 = null;
if ($id_asesi) {
    $sql_ak01 = "SELECT a.*, 
                        apl.id_skema,
                        s.judul_skema, s.nomor_skema,
                        asr.nama_asesor, asr.no_reg, asr.id_asesor
                 FROM tb_ak01 a
                 LEFT JOIN tb_apl1 apl ON a.id_apl1 = apl.id_apl1
                 LEFT JOIN tb_skema s   ON apl.id_skema = s.id_skema
                 LEFT JOIN tb_asesor asr ON a.id_asesor = asr.id_asesor
                 WHERE a.id_asesi = '$id_asesi'
                 ORDER BY a.id_ak01 DESC LIMIT 1";
        $ak01 = mysqli_fetch_assoc(mysqli_query($koneksi, $sql_ak01));
}

$id_skema_auto      = $ak01['id_skema'] ?? 0;
$nama_asesor_final  = $ak01['nama_asesor'] ?? 'Belum Dipanggil';
$noreg_asesor_final = $ak01['no_reg'] ?? '-';

$asesor_info = null;
if (!$ak01 || empty($ak01['nama_asesor'])) {
    $id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);
    if ($id_asesor_session) {
        $asesor_info = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT * FROM tb_asesor WHERE id_asesor='{$id_asesor_session}' LIMIT 1"));
    }
}
$nama_asesor_final  = $ak01['nama_asesor']   ?? $asesor_info['nama_asesor']   ?? '';
$noreg_asesor_final = $ak01['no_reg'] ?? $asesor_info['no_reg'] ?? '';
$id_asesor_final    = intval($ak01['id_asesor'] ?? $asesor_info['id_asesor'] ?? $_SESSION['id_asesor'] ?? 0);

$apl1     = null;
$id_apl1  = 0;
$id_skema = 0;

if (!empty($_GET['id_skema'])) {
    $id_skema = intval($_GET['id_skema']);
}

if (!$id_skema && $id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.*, s.judul_skema, s.nomor_skema 
         FROM tb_apl1 a
         LEFT JOIN tb_skema s ON a.id_skema = s.id_skema
         WHERE a.id_asesi='{$e($id_asesi)}'
         ORDER BY a.id_apl1 DESC LIMIT 1"));
    if ($apl1) {
        $id_apl1  = intval($apl1['id_apl1']);
        $id_skema = intval($apl1['id_skema']);
    }
}

if (!$id_skema && $ak01) {
    $id_skema = intval($ak01['id_skema']);
}

if (!$apl1 && $id_asesi && $id_skema) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.*, s.judul_skema, s.nomor_skema
         FROM tb_apl1 a
         LEFT JOIN tb_skema s ON a.id_skema = s.id_skema
         WHERE a.id_asesi='{$e($id_asesi)}' AND a.id_skema='{$e($id_skema)}'
         LIMIT 1"));
    if ($apl1) $id_apl1 = intval($apl1['id_apl1']);
}

$has_data = false;
if ($id_asesi && $id_skema) {
    $chk = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) AS cnt FROM tb_ia01
         WHERE id_asesi='{$e($id_asesi)}' AND id_apl1='{$e($id_apl1)}'"));
    $has_data = ($chk && $chk['cnt'] > 0);
}

$mode = 'create';
if (isset($_GET['mode']) && in_array($_GET['mode'], ['view', 'create'])) {
    $mode = $_GET['mode'];
} elseif ($has_data) {
    $mode = 'view';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $p_id_skema          = intval($_POST['id_skema'] ?? 0);
    $p_id_apl1           = intval($_POST['id_apl1'] ?? 0);
    $p_tanggal           = trim($_POST['tanggal'] ?? '');
    $p_rekomendasi       = trim($_POST['rekomendasi'] ?? '');
    $p_umpan_balik       = trim($_POST['umpan_balik'] ?? '');
    $p_alasan_rekomendasi = trim($_POST['alasan_rekomendasi'] ?? '');
    $p_standar           = is_array($_POST['standar'] ?? null) ? $_POST['standar'] : [];
    $p_pencapaian        = is_array($_POST['pencapaian'] ?? null) ? $_POST['pencapaian'] : [];
    $p_penilaian_lanjut  = is_array($_POST['penilaian_lanjut'] ?? null) ? $_POST['penilaian_lanjut'] : [];

    if (!$p_id_skema || !$id_asesi) {
        $_SESSION['flash_ia01'] = 'error|Data tidak lengkap – skema tidak ditemukan.';
        header("Location: FR_IA1.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
        exit;
    }
    if (!$p_id_apl1) {
        $_SESSION['flash_ia01'] = 'error|APL-01 untuk asesi + skema ini belum ditemukan.';
        header("Location: FR_IA1.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
        exit;
    }

    $all_kuk = [];
    $res_kuk = mysqli_query($koneksi,
        "SELECT k.id_kuk, e.id_elemen, u.id_unit
         FROM tb_kuk k
         JOIN tb_elemen          e ON k.id_elemen = e.id_elemen
         JOIN tb_unit_kompetensi u ON e.id_unit   = u.id_unit
         WHERE u.id_skema = '{$e($p_id_skema)}'
         ORDER BY u.id_unit, e.id_elemen, k.id_kuk");
    while ($kr = mysqli_fetch_assoc($res_kuk)) $all_kuk[] = $kr;

    if (!$all_kuk) {
        $_SESSION['flash_ia01'] = 'error|Skema ini belum memiliki unit/elemen/KUK.';
        header("Location: FR_IA1.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create"); 
        exit;
    }

    $old_ids = [];
    $r_old = mysqli_query($koneksi,
        "SELECT id_ia01 FROM tb_ia01
         WHERE id_asesi='{$e($id_asesi)}' AND id_apl1='{$e($p_id_apl1)}'");
    while ($ro = mysqli_fetch_assoc($r_old)) $old_ids[] = intval($ro['id_ia01']);
    if ($old_ids) {
        $ids_str = implode(',', $old_ids);
        mysqli_query($koneksi, "DELETE FROM detail_ia01 WHERE id_ia01 IN ($ids_str)");
        mysqli_query($koneksi, "DELETE FROM tb_ia01    WHERE id_ia01 IN ($ids_str)");
    }

    $ok = true;
    $id_ak01_val = intval($ak01['id_ak01'] ?? 0);

    $sql = "INSERT INTO tb_ia01
    (id_apl1, id_ak01, id_asesi, id_asesor,
     tanggal, rekomendasi, umpan_balik, belum_kompeten)
    VALUES (
        '{$e($p_id_apl1)}', '$id_ak01_val', '{$e($id_asesi)}', '{$e($id_asesor_final)}',
        " . ($p_tanggal              ? "'{$e($p_tanggal)}'"              : "NULL") . ",                   
        " . ($p_rekomendasi          ? "'{$e($p_rekomendasi)}'"          : "NULL") . ",
        " . ($p_umpan_balik          ? "'{$e($p_umpan_balik)}'"          : "NULL") . ",
        " . ($p_alasan_rekomendasi   ? "'{$e($p_alasan_rekomendasi)}'"   : "NULL") . "                    
    )";

    if (mysqli_query($koneksi, $sql)) {
        $new_id_ia01 = mysqli_insert_id($koneksi);
        foreach ($all_kuk as $kr) {
            $id_kuk_i    = intval($kr['id_kuk']);
            $id_elemen_i = intval($kr['id_elemen']);
            $id_unit_i   = intval($kr['id_unit']);
            // $std    = $p_standar[$id_kuk_i], ?? '';  
            $penc = $p_pencapaian[$id_kuk_i]       ?? '';
            $pnl  = $p_penilaian_lanjut[$id_kuk_i] ?? '';

            $ok_d = mysqli_query($koneksi,
                "INSERT INTO detail_ia01
                    (id_ia01, id_skema, id_unit, id_elemen, id_kuk,
                    --  standar_industri,
                     pencapaian, `Penilaian Lanjut`)
                 VALUES (
                    '$new_id_ia01',
                    '{$e($p_id_skema)}',
                    '$id_unit_i', '$id_elemen_i', '$id_kuk_i',
                     -- '{$e($std)}',
                    '{$e($penc)}',
                    '{$e($pnl)}'
                 )");
            if (!$ok_d) $ok = false;
        }
    } else {
        $ok = false;
    }

    $_SESSION['flash_ia01'] = $ok
        ? 'success|FR.IA.01 berhasil disimpan!'
        : 'error|Sebagian data gagal disimpan: ' . mysqli_error($koneksi);
    header("Location: FR_IA1.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=view");
    exit;
}



$units = [];
if ($id_skema) {
    $res_u = mysqli_query($koneksi,
        "SELECT * FROM tb_unit_kompetensi
         WHERE id_skema='{$e($id_skema)}' ORDER BY id_unit");
    while ($unit = mysqli_fetch_assoc($res_u)) {
        $unit['elemen'] = [];
        $res_el = mysqli_query($koneksi,
            "SELECT * FROM tb_elemen
             WHERE id_unit='{$e($unit['id_unit'])}' ORDER BY id_elemen");
        while ($el = mysqli_fetch_assoc($res_el)) {
            $el['kuk'] = [];
            $res_kk = mysqli_query($koneksi,
                "SELECT * FROM tb_kuk
                 WHERE id_elemen='{$e($el['id_elemen'])}' ORDER BY id_kuk");
            while ($kk = mysqli_fetch_assoc($res_kk)) $el['kuk'][] = $kk;
            $unit['elemen'][] = $el;
        }
        $units[] = $unit;
    }
}
$standar_skema = '';
if ($id_skema) {
    $rs = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT standar_kompetensi_kerja FROM tb_skema
         WHERE id_skema='{$e($id_skema)}' LIMIT 1"));
    $standar_skema = $rs['standar_kompetensi_kerja'] ?? '';
}

$saved_vals = [];
$saved_hdr  = ['tanggal'=>'','rekomendasi'=>'','umpan_balik'=>'', 'alasan_rekomendasi'=>'']; 
if ($mode === 'view' && $id_asesi && $id_skema) {
    $res_ed = mysqli_query($koneksi,
        "SELECT d.id_kuk, i.tanggal, i.rekomendasi, i.umpan_balik, i.belum_kompeten,
                -- d.standar_industri,
                d.pencapaian,
                d.`Penilaian Lanjut` AS penilaian_lanjut
         FROM tb_ia01 i
         LEFT JOIN detail_ia01 d ON d.id_ia01 = i.id_ia01
         WHERE i.id_asesi='{$e($id_asesi)}' AND i.id_apl1='{$e($id_apl1)}'");
    $first = true;
    while ($er = mysqli_fetch_assoc($res_ed)) {
        if ($first) {
            $saved_hdr = [
                'tanggal'            => $er['tanggal'],
                'rekomendasi'        => $er['rekomendasi'],
                'umpan_balik'        => $er['umpan_balik'],
                'alasan_rekomendasi' => $er['belum_kompeten'],
            ];
            $first = false;
        }
        $saved_vals[intval($er['id_kuk'])] = [
            // 'standar'               => $er['standar_industri'],
            'pencapaian'            => $er['pencapaian'],
            'penilaian_lanjut'      => $er['penilaian_lanjut'],
        ];
    }
}

$nama_asesi_db  = $asesi['nama_asesi'] ?? '';
$is_asesi       = ($role === 'Asesi');
$is_asesor      = ($role === 'Asesor' || $role === 'Admin_lsp' || $role === 'Admin_utm');

$dsb_untuk_asesi  = $is_asesi  ? '' : 'readonly';
$dsb_untuk_asesor = ($is_asesor) ? '' : 'disabled';
$dsb_style        = $is_asesi ? 'pointer-events:none; opacity:0.65;' : '';
$dsb              = $dsb_untuk_asesor;  
$lock_asesi       = $dsb_untuk_asesi;    

$tgl_form = $mode === 'create'
    ? date('Y-m-d')
    : ($saved_hdr['tanggal'] ?: ($ak01['hari_tanggal'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FR.IA.01 Ceklis Observasi</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/fr_ia01.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/JS/lsp_common.js"></script>
</head>
<body>
<div class="form-box">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.IA.01. CL – CEKLIS OBSERVASI AKTIVITAS<br>
    </h2>

    <?php if ($flash_msg): ?>
    <div class="flash <?= h($flash_type) ?>"><?= h($flash_msg) ?></div>
    <?php endif; ?>

<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi" value="<?= $id_asesi ?>">
    <input type="hidden" name="id_skema"  value="<?= $id_skema ?>">
    <input type="hidden" name="id_apl1"   value="<?= $id_apl1 ?>">

    <div class="info-box">
        <div class="info-row">
            <div class="info-col" style="flex:3; min-width:200px;">
                <span class="small-text label">Skema Sertifikasi – Judul <span class="required">*</span></span>
                <input type="text" class="form-control"
                       value="<?= h($apl1['judul_skema'] ?? '') ?>"
                       readonly style="background:#f5f5f5; color:#1a237e;">
            </div>
            <div class="info-col" style="flex:1; min-width:90px;">
                <span class="small-text label">Nomor</span>
                <input type="text" class="form-control"
                       value="<?= h($apl1['nomor_skema'] ?? '') ?>"
                       readonly style="background:#f5f5f5;">
            </div>
            <div class="info-col" style="flex:1; min-width:130px;">
                <span class="small-text label">TUK </span>
                <input type="text" name="tuk" value="<?= h($ak01['tuk'] ?? '') ?>"  class="form-control" disabled style="<?= $dsb_style ?>">
            </div>
        </div>
        <div class="info-row">
            <div class="info-col" style="flex:2; min-width:180px;">
                <span class="small-text label">Nama Asesor</span>
                <input type="text" class="form-control"
                       value="<?= h($nama_asesor_final) ?>"
                       readonly style="background:#f5f5f5; color:#1a237e;">
            </div>
            <div class="info-col" style="flex:2; min-width:180px;">
                <span class="small-text label">Nama Asesi</span>
                <input type="text" name="nama_asesi" id="nama_asesi" class="form-control"
                       value="<?= h($nama_asesi_db) ?>"
                       readonly placeholder="Nama Asesi"
                       oninput="if(typeof scheduleQRAsesi==='function') scheduleQRAsesi()">
            </div>
            <div class="info-col" style="flex:1; min-width:130px;">
                <span class="small-text label">Tanggal</span>
                <input type="date" name="tanggal" class="form-control"
                       value="<?= h($tgl_form) ?>" disabled 
                       <?= $dsb ?> style="<?= $dsb_style ?>">
            </div>
        </div>
    </div>

    <div class="panduan-box">
        <b>PANDUAN BAGI ASESOR</b>
        <ul>
            <li>Lengkapi nama unit kompetensi, elemen, dan KUK sesuai kolom dalam tabel.</li>
            <li>Isi standar industri atau tempat kerja.</li>
            <li>Klik <b>Ya</b> jika asesi dapat mendemonstrasikan tugas sesuai KUK, atau <b>Tidak</b> bila sebaliknya.</li>
            <li>Jika memilih <b>Tidak</b>, wajib mengisi alasan mengapa belum kompeten.</li>
            <li>Penilaian Lanjut diisi bila hasil belum dapat disimpulkan.</li>
        </ul>
    </div>

    <?php if (empty($units)): ?>
    <div class="placeholder-box">
        Pilih skema untuk menampilkan daftar unit kompetensi
    </div>

    <?php else: ?>

    <table class="tbl-units">
        <thead>
            <tr>
                <th style="width:46px;">No.</th>
                <th style="width:200px;">Kode Unit</th>
                <th>Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($units as $ui => $unit): ?>
            <tr>
                <td><?= $ui + 1 ?></td>
                <td><?= h($unit['kode_unit']) ?></td>
                <td><?= h($unit['judul_unit']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php foreach ($units as $ui => $unit): ?>
    <div class="unit-obs-box">
        <div class="unit-obs-header">
            Unit Kompetensi <?= $ui + 1 ?>
            <div class="unit-sub">
                Kode Unit : <?= h($unit['kode_unit']) ?> &nbsp;&nbsp;
                Judul Unit : <?= h($unit['judul_unit']) ?>
            </div>
        </div>
        <div style="overflow-x:auto;">
        <table class="tbl-obs">
            <thead>
                <tr>
                    <th style="width:42px;">No.</th>
                    <th style="width:160px;">Elemen</th>
                    <th>Kriteria Unjuk Kerja</th>
                    <th style="width:175px;">Standar Industri / Tempat Kerja</th>
                    <th style="width:46px;">Ya</th>
                    <th style="width:46px;">Tidak</th>
                    <th style="width:185px;">Penilaian Lanjut</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($unit['elemen'] as $ei => $el):
                $jml_kuk = count($el['kuk']);
            ?>
                <?php foreach ($el['kuk'] as $ki => $kk):
                    $sv       = $saved_vals[intval($kk['id_kuk'])] ?? [];
                    $std_val  = $sv['standar']               ?? '';
                    $penc_val = $sv['pencapaian']             ?? '';
                    $pl_val   = $sv['penilaian_lanjut']       ?? '';
                    $abk_val  = $sv['alasan_belum_kompeten']  ?? '';
                    $kuk_id   = $kk['id_kuk'];
                ?>
                <tr>
                    <td style="text-align:center; font-size:12px; white-space:nowrap;">
                        <?= ($ei + 1) . '.' . ($ki + 1) ?>
                    </td>

                    <?php if ($ki === 0): ?>
                    <td class="td-elemen" rowspan="<?= $jml_kuk ?>">
                        <?= ($ei + 1) . '. ' . h($el['nama_elemen']) ?>
                    </td>
                    <?php endif; ?>

                    <td class="kuk-text"><?= h($kk['kuk']) ?></td>

                    <td>
                    <input type="text"
                           name="standar[<?= $kuk_id ?>]"
                           class="form-control"
                           style="font-size:12px;"
                           value="<?= h($std_val ?: $standar_skema) ?>"
                           placeholder="Standar industri..."
                           <?= $lock_asesi ?>>
                    </td>

                    <td>
                        <div class="radio-yt">
                            <input type="radio"
                                   name="pencapaian[<?= $kuk_id ?>]"
                                   id="ya_<?= $kuk_id ?>"
                                   value="Ya"
                                   <?= $penc_val === 'Ya' ? 'checked' : '' ?>
                                   <?= $dsb_untuk_asesor ?>>
                        </div>
                    </td>

                    <td>
                        <div class="radio-yt">
                            <input type="radio"
                                   name="pencapaian[<?= $kuk_id ?>]"
                                   id="tidak_<?= $kuk_id ?>"
                                   value="Tidak"
                                   <?= $penc_val === 'Tidak' ? 'checked' : '' ?>
                                   <?= $dsb_untuk_asesor ?>>
                        </div>
                    </td>

                    <td>
                        <textarea name="penilaian_lanjut[<?= $kuk_id ?>]"
                                  class="obs-input"
                                  placeholder="Penilaian lanjut..."
                                  <?= $dsb_untuk_asesor ?>><?= h($pl_val) ?></textarea>
                    </td>

                    <!-- <td>
                        <php if ($mode === 'view'): ?>
                            <span style="font-size:12px;"><= h($pl_val) ?></span>
                        <php else: ?>
                            <textarea name="penilaian_lanjut[<= $kuk_id ?>]"
                                      class="obs-input"
                                      placeholder="Penilaian lanjut..."
                                      <= $dsb_untuk_asesor ?>><= h($pl_val) ?></textarea>
                        <php endif; ?>
                    </td> -->
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="section-title">Rekomendasi</div>
    <div class="rek-box">
        <div>
        <label class="small-text">Umpan Balik untuk Asesi :</label>
        <textarea name="umpan_balik" class="form-control" rows="3"
                  placeholder="Tuliskan umpan balik..."
                  <?= $dsb_untuk_asesor ?> style="margin-top:4px;"
        ><?= h($saved_hdr['umpan_balik']) ?></textarea>
        </div>

        <div style="margin-top:12px;">
            <label class="small-text">Rekomendasi :</label>
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:6px;
                        font-size:13px; <?= $dsb_style ?>">
                <label style="display:flex; align-items:center; gap:6px;">
                    Asesi telah memenuhi seluruh KUK
                </label>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="radio" name="rekomendasi" value="Kompeten"
                           id="rek_kompeten"
                           <?= $saved_hdr['rekomendasi'] === 'Kompeten' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b>KOMPETEN</b>
                </label>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="radio" name="rekomendasi" value="Belum Kompeten"
                           id="rek_belum"
                           <?= $saved_hdr['rekomendasi'] === 'Belum Kompeten' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b>BELUM KOMPETEN</b>
                </label>
            </div>

                <?php if ($saved_hdr['rekomendasi'] === 'Belum Kompeten' && $saved_hdr['alasan_rekomendasi']): ?>
                <!-- <div style="margin-top:10px; padding:10px 14px; background:#fff3f3;
                            border:1px solid #ef9a9a; border-radius:6px;">
                     <div style="font-size:12px; font-weight:700; color:#c62828; margin-bottom:4px;">
                        Alasan Belum Kompeten (Rekomendasi) :
                    </div> -->
                    <!-- <div style="font-size:13px; color:#b71c1c;">
                        <= nl2br(h($saved_hdr['alasan_rekomendasi'])) ?>
                    </div>
                </div> -->
                <?php endif; ?>
                <div id="alasan-rek-wrap"
                     style="<?= $saved_hdr['rekomendasi'] === 'Belum Kompeten' ? 'display:block;' : 'display:none;' ?>">
                    <label>⚠ Kenapa Belum Kompeten? (Penjelasan Rekomendasi)</label>
                    <textarea name="alasan_rekomendasi"
                              placeholder="Jelaskan alasan rekomendasi Belum Kompeten..."
                              <?= $dsb_untuk_asesor ?>><?= h($saved_hdr['alasan_rekomendasi']) ?></textarea>
                </div>
        </div>
    </div>

    <div class="rek-grid">
        <div class="rek-col">
            <div class="col-title">Asesi</div>
            <div style="font-size:13px; margin-bottom:6px;">
                <b>Nama :</b>
                <span id="asesi-ttd-nama" style="color:#1a237e;"><?= h($nama_asesi_db) ?></span>
            </div>
        </div>
        <div class="rek-col">
            <div class="col-title">Asesor</div>
            <div style="font-size:13px; margin-bottom:4px;">
                <b>Nama :</b>
                <span style="color:#1a237e;"><?= h($nama_asesor_final) ?></span>
            </div>
            <div style="font-size:12px; color:#888;">
                No. Reg: <?= h($noreg_asesor_final) ?>
            </div>
        </div>
    </div>

    <?php endif; ?>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:20px;">
                    
        <?php if ($is_asesor): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/rekap_ia1.php'">
                Kembali
            </button>
            <button type="submit" class="btn-submit">SIMPAN ✓</button>
        <?php endif; ?>
        
        <?php if ($is_asesi): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
                Kembali
            </button>
            <?php if (!$has_data): ?>
                <button type="submit" class="btn-submit" style="background:blue;">SIMPAN ✓</button>
            <?php endif; ?>
        <?php endif; ?>
            
    </div>
</form>
</div>

<script>
// function toggleAlasanBK(kukId, val) {
//     var wrap = document.getElementById('alasan_bk_wrap_' + kukId);
//     if (!wrap) return;
//     if (val === 'Tidak') {
//         wrap.style.display = 'block';
//         var ta = wrap.querySelector('textarea');
//         if (ta) ta.focus();
//     } else {
//         wrap.style.display = 'none';
//         var ta = wrap.querySelector('textarea');
//         if (ta) ta.value = '';  
//     }
// }

function toggleAlasanRek(val) {
    var wrap = document.getElementById('alasan-rek-wrap');
    if (!wrap) return;
    if (val === 'Belum Kompeten') {
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