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
$role      = $_SESSION['role'] ?? '';
$is_asesi  = ($role === 'Asesi');
$is_asesor = ($role === 'Asesor');
$is_admin  = ($role === 'Admin_lsp' || $role === 'Admin_utm');

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
if (isset($_GET['mode']) && in_array($_GET['mode'], ['view', 'create', 'edit'], true)) {
    $mode = $_GET['mode'] === 'edit' ? 'create' : $_GET['mode'];
} elseif ($has_data) {
    $mode = ($is_asesor && isset($_GET['edit']) && $_GET['edit'] === '1') ? 'create' : 'view';
}
$is_readonly = ($mode === 'view')
    || (isset($_GET['view']) && (string) $_GET['view'] === '1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_asesor) {
        $_SESSION['alert'] = 'Hanya Asesor yang dapat mengisi FR.IA.01.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$id_skema&mode=view");
        exit;
    }
    if ($is_admin) {
        $_SESSION['alert'] = 'error|Admin tidak dapat mengedit data. Hanya dapat melihat dan mencetak.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$id_skema&mode=view");
        exit;
    }

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
        $_SESSION['alert'] = 'error|Data tidak lengkap – skema tidak ditemukan.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
        exit;
    }
    if (!$p_id_apl1) {
        $_SESSION['alert'] = 'error|APL-01 untuk asesi + skema ini belum ditemukan.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
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
        $_SESSION['alert'] = 'error|Skema ini belum memiliki unit/elemen/KUK.';
        header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create"); 
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

    if (!$ok) {
        $_SESSION['alert'] = 'Sebagian data gagal disimpan: ' . mysqli_error($koneksi);
    } else {
        $_SESSION['alert'] = 'Data berhasil disimpan!';
    }
    header("Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=$id_asesi&id_skema=$p_id_skema&mode=view");
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

// FIX: muat saved_vals saat view DAN saat edit (mode=create + has_data)
if (($mode === 'view' || ($mode === 'create' && $has_data)) && $id_asesi && $id_skema) {
    $res_ed = mysqli_query($koneksi,
        "SELECT d.id_kuk, i.tanggal, i.rekomendasi, i.umpan_balik, i.belum_kompeten,
                d.pencapaian,
                d.`Penilaian Lanjut` AS penilaian_lanjut
         FROM tb_ia01 i
         LEFT JOIN detail_ia01 d ON d.id_ia01 = i.id_ia01
         WHERE i.id_asesi='{$e($id_asesi)}' AND i.id_apl1='{$e($id_apl1)}'
         ORDER BY i.id_ia01 DESC");
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
        if ($er['id_kuk']) {
            $saved_vals[intval($er['id_kuk'])] = [
                'pencapaian'       => $er['pencapaian'],
                'penilaian_lanjut' => $er['penilaian_lanjut'],
            ];
        }
    }
}
if (($mode === 'view' || (!$is_readonly && $has_data)) && $id_asesi && $id_skema) {
    $res_tidak = mysqli_query($koneksi,
        "SELECT 
            u.id_unit, u.kode_unit, u.judul_unit,
            e.id_elemen, e.no_elemen, e.nama_elemen,
            k.id_kuk, k.no_kuk, k.kuk AS kuk_teks,
            d.pencapaian
         FROM tb_ia01 i
         JOIN detail_ia01 d ON d.id_ia01 = i.id_ia01
         JOIN tb_kuk k ON k.id_kuk = d.id_kuk
         JOIN tb_elemen e ON e.id_elemen = d.id_elemen
         JOIN tb_unit_kompetensi u ON u.id_unit = d.id_unit
         WHERE i.id_asesi='{$e($id_asesi)}' AND i.id_apl1='{$e($id_apl1)}'
           AND d.pencapaian = 'Tidak'
         ORDER BY u.id_unit, e.id_elemen, k.id_kuk");

    $grp_unit = [];
    $unit_no_map = [];
    foreach ($units as $ui => $u) {
        $unit_no_map[$u['id_unit']] = $ui + 1;
    }

    while ($tr = mysqli_fetch_assoc($res_tidak)) {
        $uid = $tr['id_unit'];
        $eid = $tr['id_elemen'];
        if (!isset($grp_unit[$uid])) {
            $grp_unit[$uid] = [
                'no'        => $unit_no_map[$uid] ?? '?',
                'kode'      => $tr['kode_unit'],
                'judul'     => $tr['judul_unit'],
                'elemen'    => []
            ];
        }
        if (!isset($grp_unit[$uid]['elemen'][$eid])) {
            $grp_unit[$uid]['elemen'][$eid] = [
                'no'    => $tr['no_elemen'],
                'nama'  => $tr['nama_elemen'],
                'kuk'   => []
            ];
        }
        $grp_unit[$uid]['elemen'][$eid]['kuk'][] = $tr['no_kuk'] . ' ' . $tr['kuk_teks'];
    }
    if (!empty($grp_unit)) {
        $lines = [];
        foreach ($grp_unit as $uid => $gu) {
            $lines[] = 'KELOMPOK PENGERJAAN: UNIT KOMPETENSI KE ' . $gu['no'];
            $lines[] = '  Unit = ' . $gu['no'] . ' (' . $gu['kode'] . ')';
            foreach ($gu['elemen'] as $eid => $ge) {
                $kuk_list = implode(', ', $ge['kuk']);
                $lines[] = '    Elemen = ' . $ge['no'] . ' (' . $ge['nama'] . ')';
                $lines[] = '      KUK = ' . $kuk_list;
            }
            $lines[] = '';
        }
        $auto_belum = trim(implode("\n", $lines));
        if ($auto_belum !== trim((string)$saved_hdr['alasan_rekomendasi'])) {
            $ia01_row = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT id_ia01 FROM tb_ia01
                 WHERE id_asesi='{$e($id_asesi)}' AND id_apl1='{$e($id_apl1)}'
                 ORDER BY id_ia01 DESC LIMIT 1"));
            if ($ia01_row) {
                mysqli_query($koneksi,
                    "UPDATE tb_ia01 SET belum_kompeten='{$e($auto_belum)}'
                     WHERE id_ia01='" . intval($ia01_row['id_ia01']) . "'");
            }
            $saved_hdr['alasan_rekomendasi'] = $auto_belum;
        }
    }
}

$nama_asesi_db  = $asesi['nama_asesi'] ?? '';
$is_view_only   = ($is_admin);

$dsb_untuk_asesi  = $is_asesi  ? '' : 'readonly';
$dsb_untuk_asesor = ($is_asesor && !$is_view_only && !$is_readonly) ? '' : 'disabled';
$dsb_style        = ($is_asesi || $is_view_only || $is_readonly) ? 'pointer-events:none; opacity:0.65;' : '';
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
</head>
<body>
<div class="form-box">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.IA.01. CL – CEKLIS OBSERVASI AKTIVITAS<br>
    </h2>

    <?php if (!empty($_SESSION['alert'])): ?>
    <script>alert('<?= addslashes($_SESSION['alert']) ?>');</script>
    <?php unset($_SESSION['alert']); ?>
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
            <div class="info-col" style="flex:0.97; min-width:90px;">
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
                    <th rowspan="2" style="width:42px;">No.</th>
                    <th rowspan="2" style="width:160px;">Elemen</th>
                    <th rowspan="2" >Kriteria Unjuk Kerja</th>
                    <th rowspan="2" style="width:175px;">Standar Industri / Tempat Kerja</th>
                    <th colspan="2" style="width:46px;">Pencapaian</th>
                    <!-- <th style="width:46px;">Tidak</th> -->
                    <th rowspan="2" style="width:185px;">Penilaian Lanjut</th>
                </tr>
                <tr><th>Ya</th><th>Tidak</th></tr>
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

                    <?php if ($ki === 0): ?>
                    <td rowspan="<?= $jml_kuk ?>">
                    <div
                           name="standar_elemen[<?= $el['id_elemen'] ?>]"
                           class="form-control"
                           style="font-size:12px;"
                           value="<?= h($standar_skema) ?>"
                           placeholder="Standar industri..."
                           <?= $lock_asesi ?>><?= h($standar_skema) ?></div>
                    </td>
                    <?php endif; ?>

                    <td>
                        <div class="radio-yt">
                            <input type="radio"
                                   name="pencapaian[<?= $kuk_id ?>]"
                                   id="ya_<?= $kuk_id ?>"
                                   value="Ya" style="accent-color:#2e7d32;"
                                   <?= $penc_val === 'Ya' ? 'checked' : '' ?>
                                   <?= $dsb_untuk_asesor ?>>
                        </div>
                    </td>

                    <td>
                        <div class="radio-yt">
                            <input type="radio"
                                   name="pencapaian[<?= $kuk_id ?>]"
                                   id="tidak_<?= $kuk_id ?>"
                                   value="Tidak" style="accent-color:#c00;"
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
                           id="rek_kompeten" style="accent-color:#2e7d32;width:15px;height:15px;"
                           <?= $saved_hdr['rekomendasi'] === 'Kompeten' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b style="accent-color:#2e7d32;" >KOMPETEN</b>
                </label>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="radio" name="rekomendasi" value="Belum Kompeten"
                           id="rek_belum" style="accent-color:#c00;width:15px;height:15px;"
                           <?= $saved_hdr['rekomendasi'] === 'Belum Kompeten' ? 'checked' : '' ?>
                           <?= $dsb_untuk_asesor ?>
                           onchange="toggleAlasanRek(this.value)">
                    <b style="accent-color:#c00;" >BELUM KOMPETEN</b>
                </label>
            </div>

                <?php if ($saved_hdr['rekomendasi'] === 'Belum Kompeten' && $saved_hdr['alasan_rekomendasi']): ?>
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
            <?php if ($has_data && $is_readonly): ?>
            <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=<?= $id_asesi ?>&id_skema=<?= $id_skema ?>&mode=edit"
               class="btn-submit" style="background:#ff9800;text-decoration:none;">EDIT</a>
            <?php endif; ?>
            <?php if ($has_data && !$is_readonly): ?>
            <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=<?= $id_asesi ?>&id_skema=<?= $id_skema ?>&mode=view"
               class="btn-submit" style="background:#607d8b;text-decoration:none;">LIHAT</a>
            <?php endif; ?>
            <?php if (!$is_readonly): ?>
            <button type="submit" class="btn-submit">SIMPAN ✓</button>
            <?php endif; ?>
            <a href="../pdf/cetak_ia1.php?id_asesi=<?= $id_asesi ?>"
               target="_blank"
               class="btn-submit"
               style="background:#1565c0; text-decoration:none;">
               Cetak PDF
            </a>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/rekap_ia1.php'">
                Kembali
            </button>
            <a href="../pdf/cetak_ia1.php?id_asesi=<?= $id_asesi ?>"
               target="_blank"
               class="btn-submit"
               style="background:#1565c0; text-decoration:none;">
               Cetak PDF
            </a>
        <?php endif; ?>
        
        <?php if ($is_asesi): ?>
            <button type="button" class="btn-back"
                    onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
                Kembali
            </button>
            <?php if ($has_data): ?>
            <!-- <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_IA1.php&id_asesi=<?= $id_asesi ?>&id_skema=<?= $id_skema ?>&mode=view"
               class="btn-submit" style="background:#4caf50;text-decoration:none;">LIHAT</a> -->
            <?php endif; ?>
        <?php endif; ?>
            
    </div>
</form>
</div>

<script>
function toggleAlasanRek(val) {
    var wrap = document.getElementById('alasan-rek-wrap');
    if (!wrap) return;
    if (val === 'Belum Kompeten') {
        wrap.style.display = 'block';
        var ta = wrap.querySelector('textarea');
        if (ta) ta.focus();
    } else {
        wrap.style.display = 'none';
    }
}
</script>
</body>
</html>