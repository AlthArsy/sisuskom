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

$flash_msg  = $_SESSION['flash_ak02'] ?? '';
$flash_type = '';
if ($flash_msg) {
    [$flash_type, $flash_msg] = explode('|', $flash_msg, 2);
    unset($_SESSION['flash_ak02']);
}



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
         ORDER BY a.id_apl1 DESC
         LIMIT 1");
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
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi = '$id_asesi' LIMIT 1"));
    $nama_asesi_db = $rn['nama_asesi'] ?? '';
}

$hari_tanggal_db = '';
$tuk_db = '';
if ($id_asesi && $id_skema_db) {
    $qak1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tuk, hari_tanggal, id_ak01 FROM tb_ak01
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1_db'
         ORDER BY id_ak01 DESC
         LIMIT 1"));
    $tuk_db = $qak1['tuk'] ?? '';
    $hari_tanggal_db = $qak1['hari_tanggal'] ?? '';
    $id_ak01_db = intval($qak1['id_ak01'] ?? 0);
}

$tgl_selesai_db = '';
if ($id_asesi && $id_skema_db) {
    $qak1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT tgl_selesai,  id_ak03 FROM tb_ak03
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1_db'
         ORDER BY id_ak03 DESC
         LIMIT 1"));
    $tgl_selesai_db = $qak1['tgl_selesai'] ?? '';
}
$id_skema = $id_skema_db;
$data_ak02_saved   = null;
$detail_ak02_saved = [];

if ($id_asesi && $id_apl1_db) {
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_ak02
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1_db'
         ORDER BY id_ak02 DESC LIMIT 1"));
    if ($cek) {
        $data_ak02_saved = $cek;
        $res_detail = mysqli_query($koneksi,
            "SELECT * FROM detail_ak02 WHERE id_ak02 = '{$cek['id_ak02']}'");
        while ($d = mysqli_fetch_assoc($res_detail)) {
            $detail_ak02_saved[] = $d;
        }
    }
}

$mode = 'create';
$id_skema = $id_skema_db;
if (isset($_GET['mode']) && in_array($_GET['mode'], ['view','create','edit'])) {
    $mode = $_GET['mode'];
} elseif ($data_ak02_saved) {
    $chk_rek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT rekomendasi FROM tb_ak02
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'
         LIMIT 1"));
    $mode = ($chk_rek && $chk_rek['rekomendasi']) ? 'view' : 'edit';
}

$id_asesor_db   = 0;
$nama_asesor_db = '';
$noreg_asesor_db = '';
if ($id_skema_db) {
    $qas = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ar.id_asesor, ar.nama_asesor, ar.no_reg
         FROM tb_skema sk
         JOIN tb_asesor ar ON sk.id_asesor = ar.id_asesor
         WHERE sk.id_skema = '$id_skema_db'
         LIMIT 1"));
    if ($qas) {
        $id_asesor_db    = intval($qas['id_asesor']);
        $nama_asesor_db  = $qas['nama_asesor'] ?? '';
        $noreg_asesor_db = $qas['no_reg']       ?? '';
    }
}

$list_unit = [];
if ($id_skema_db) {
    $qu = mysqli_query($koneksi,
        "SELECT id_unit, judul_unit
        FROM tb_unit_kompetensi
        WHERE id_skema = '$id_skema_db'
        ORDER BY id_unit ASC");
    while ($u = mysqli_fetch_assoc($qu)) {
        $list_unit[] = $u;
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_id_skema = $id_skema_db;
    $p_id_apl1  = $id_apl1_db;
    $rekomendasi = isset($_POST['rekomendasi']) ? trim($_POST['rekomendasi']) : '';
    $tindak_lanjut = isset($_POST['tindak_lanjut']) ? trim($_POST['tindak_lanjut']) : '';
    $komentar_asesor  = isset($_POST['komentar_asesor']) ? trim($_POST['komentar_asesor']) : '';

    if (!$p_id_skema || !$id_asesi) {
        $_SESSION['flash_ak02'] = 'error|Data tidak lengkap – skema tidak ditemukan.';
        header("Location: FR_AK02.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
        exit;
    }
    if (!$p_id_apl1) {
        $_SESSION['flash_ak02'] = 'error|APL-01 untuk asesi + skema ini belum ditemukan.';
        header("Location: FR_AK02.php?id_asesi=$id_asesi&id_skema=$p_id_skema&mode=create");
        exit;
    } else {
        $e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);

        $sql = "INSERT INTO tb_ak02
            (id_asesi, id_asesor, id_apl1, id_ak01,
             rekomendasi, tindak_lanjut, komentar_asesor)
            VALUES (
                '$id_asesi','$id_asesor_db','$id_apl1_db','$id_ak01_db',
                " . ($rekomendasi ? "'{$e($rekomendasi)}'" : "NULL") . ",
                " . ($tindak_lanjut ? "'{$e($tindak_lanjut)}'"  : "NULL") . ",
                " . ($komentar_asesor? "'{$e($komentar_asesor)}'" : "NULL") . "
            )";
        $res = mysqli_query($koneksi, $sql);

        if (!$res) {
            $pesan      = 'Gagal menyimpan header: ' . mysqli_error($koneksi);
            $pesan_type = 'error';
} else {
    $e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);

    $old = mysqli_query($koneksi,
        "SELECT id_ak02 FROM tb_ak02
         WHERE id_asesi='$id_asesi' AND id_apl1='$id_apl1_db'");
    $old_ids = [];
    while ($o = mysqli_fetch_assoc($old)) $old_ids[] = intval($o['id_ak02']);
    if ($old_ids) {
        $ids_str = implode(',', $old_ids);
        mysqli_query($koneksi, "DELETE FROM detail_ak02 WHERE id_ak02 IN ($ids_str)");
        mysqli_query($koneksi, "DELETE FROM tb_ak02 WHERE id_ak02 IN ($ids_str)");
    }

    $sql = "INSERT INTO tb_ak02
        (id_asesi, id_asesor, id_apl1, id_ak01,
         rekomendasi, tindak_lanjut, komentar_asesor)
        VALUES (
            '$id_asesi','$id_asesor_db','$id_apl1_db','$id_ak01_db',
            " . ($rekomendasi    ? "'{$e($rekomendasi)}'"    : "NULL") . ",
            " . ($tindak_lanjut  ? "'{$e($tindak_lanjut)}'"  : "NULL") . ",
            " . ($komentar_asesor? "'{$e($komentar_asesor)}'" : "NULL") . "
        )";
    $res = mysqli_query($koneksi, $sql);

    if (!$res) {
        $pesan      = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        $pesan_type = 'error';
        } else {
                $id_ak02      = mysqli_insert_id($koneksi);
                $gagal_detail = false;
        
                foreach (($_POST['metode'] ?? []) as $id_unit => $m) {
                    $id_unit_i = intval($id_unit);
                    $obs  = isset($m['obs_demonstrasi'])  ? 1 : 0;
                    $port = isset($m['portofolio'])        ? 1 : 0;
                    $pp3  = isset($m['pyt_pihak_ketiga'])  ? 1 : 0;
                    $pww  = isset($m['pyt_wawancara'])     ? 1 : 0;
                    $pls  = isset($m['pyt_lisan'])         ? 1 : 0;
                    $ptr  = isset($m['pyt_pertulis'])      ? 1 : 0;
                    $prk  = isset($m['proyek_kerja'])      ? 1 : 0;
                    $lain = isset($m['lainnya'])
                            ? mysqli_real_escape_string($koneksi, $m['lainnya']) : '';
        
                    $ins = mysqli_query($koneksi,
                        "INSERT INTO detail_ak02
                         (id_ak02, id_skema, id_unit, obs_demonstrasi, portofolio,
                          pyt_pihak_ketiga, pyt_wawancara, pyt_lisan, pyt_pertulis,
                          proyek_kerja, lainnya)
                         VALUES ('$id_ak02','$id_skema_db','$id_unit_i',
                                 '$obs','$port','$pp3','$pww','$pls','$ptr','$prk',
                                 " . ($lain ? "'$lain'" : "NULL") . ")");
                    if (!$ins) $gagal_detail = true;
                }
        
                $_SESSION['flash_ak02'] = $gagal_detail
                    ? 'warning|Header tersimpan, sebagian detail gagal.'
                    : 'success|FR.AK.02 berhasil disimpan!';
                header("Location: FR_AK02.php?id_asesi=$id_asesi&mode=view");
                exit;
            }
        }
    }
}
function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }


$role = $_SESSION['role'] ?? '';
$is_asesi       = ($role === 'Asesi');
$is_asesor      = ($role === 'Asesor' || $role === 'Admin_lsp' || $role === 'Admin_utm');

$dsb_untuk_asesi  = $is_asesi  ? '' : 'readonly';
$dsb_untuk_asesor = $is_asesor ? '' : 'disabled';
$dsb_style        = $is_asesi ? 'pointer-events:none; opacity:0.65;' : '';
$dsb              = $dsb_untuk_asesor;  
$lock_asesi       = $dsb_untuk_asesi;    
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>FR.AK.02 Rekaman Asesmen</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .tbl-ak02 { width:100%; border-collapse:collapse; font-size:12px; }
        .tbl-ak02 th {
            background:#cadbfc; padding:7px 6px;
            border:1px solid #b0bec5; text-align:center;
            font-size:11px; writing-mode:vertical-rl;
            transform:rotate(180deg); height:90px; white-space:nowrap;
        }
        .tbl-ak02 th.th-unit {
            writing-mode:horizontal-tb; transform:none;
            height:auto; width:35%; text-align:left; font-size:12px;
        }
        .tbl-ak02 td {
            padding:6px 8px; border:1px solid #ccc; vertical-align:middle;
        }
        .tbl-ak02 td:not(:first-child) { text-align:center; }
        .tbl-ak02 td input[type="checkbox"] { width:16px; height:16px; cursor:pointer; }
        .tbl-ak02 .lainnya-cell input[type="text"] {
            width:90%; font-size:11px; padding:3px 4px;
            border:1px solid #ccc; border-radius:3px;
        }

        .hasil-box {
            border:1px solid #ccc; border-radius:5px;
            padding:14px; background:#fafbff; margin-top:16px;
        }
        .ttd-box { display:flex; gap:16px; flex-wrap:wrap; margin-top:18px; }
        .ttd-col {
            flex:1; min-width:220px; border:1px solid #ccc;
            border-radius:5px; padding:12px 14px; background:#fafbff;
        }
        .ttd-col .col-title {
            font-weight:bold; font-size:14px;
            border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:10px;
        }
        .qr-box {
            border:2px dashed #4A7AFF; border-radius:8px;
            padding:10px 8px; background:#f4f7ff; text-align:center; margin-top:10px;
        }
        .qr-box .qr-title { font-size:11px; font-weight:bold; color:#1a237e; margin-bottom:6px; }
        .qr-canvas-wrap {
            display:flex; justify-content:center; align-items:center;
            min-height:88px; margin-bottom:4px;
        }
        .qr-canvas-wrap canvas, .qr-canvas-wrap img {
            border:2px solid #4A7AFF; border-radius:5px; padding:4px; background:#fff;
        }
        .qr-ph-sm {
            width:90px; height:90px; border:2px dashed #aac; border-radius:5px;
            display:flex; align-items:center; justify-content:center;
            color:#bbb; font-size:11px; background:#fff; flex-direction:column; gap:3px; margin:0 auto;
        }
        .qr-badge {
            display:none; background:#e6f4ea; color:#2e7d32;
            border-radius:20px; padding:2px 8px; font-size:10px; font-weight:bold;
        }
        .btn-dl-qr {
            display:none; font-size:11px; background:#4A7AFF; color:#fff;
            border:none; padding:3px 10px; border-radius:20px; cursor:pointer; margin-top:4px;
        }
        .btn-dl-qr:hover { background:#325fd6; }
        .placeholder-box {
            text-align:center; padding:24px; color:#aaa;
            font-size:13px; border:1px dashed #ccc; border-radius:5px; margin:10px 0;
        }
        .field-readonly {
            padding: 6px 8px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #f5f5f5;
            font-size: 14px;
            color: #090a10;
        }
        .field-readonly.empty { color: #999; font-style: italic; }
        @media(max-width:768px){
            .form-box { margin:6vw auto; padding:14px 4vw; }
            .ttd-box { flex-direction:column; }
            .tbl-ak02 th { writing-mode:horizontal-tb; transform:none; height:auto; }
        }
    </style>
</head>
<body>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi"      value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema"      id="id_skema_hidden">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.AK.02. REKAMAN ASESMEN KOMPETENSI
    </h2>
    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin:16px 0 14px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Skema Sertifikasi – Judul</label>
                <div class="field-readonly <?= $judul_skema_db ? '' : 'empty' ?>">
                    <?= $judul_skema_db
                        ? htmlspecialchars($judul_skema_db)
                        : '— APL-1 belum diisi —' ?>
                </div>
            </div>
            <div style="flex:1; min-width:120px;">
                <label class="small-text">Nomor Skema</label>
                <div class="field-readonly <?= $nomor_skema_db ? '' : 'empty' ?>">
                    <?= $nomor_skema_db
                        ? htmlspecialchars($nomor_skema_db)
                        : '—' ?>
                </div>
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">TUK
                </label>
                <div class="field-readonly <?= $tuk_db ? '' : 'empty' ?>">
                    <?= $tuk_db
                        ? htmlspecialchars($tuk_db)
                        : '— AK-01 belum diisi —' ?>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Nama Asesor</label>
                <div style="flex:2; min-width:180px;">
                    <div class="field-readonly <?= $nama_asesor_db ? '' : 'empty' ?>">
                        <?= $nama_asesor_db
                            ? htmlspecialchars($nama_asesor_db)
                            : '— tidak ditemukan —' ?>
                    </div>
                    <!-- <div style="font-size:11px; color:#888; margin-top:4px;">
                        <php if ($noreg_asesor_db): ?>
                            <span style="font-size:11px; color:#666;" id="asesor-ttd-noreg">
                                &nbsp;No.Reg: <= htmlspecialchars($noreg_asesor_db) ?>
                            </span>
                        <php endif; ?>
                    </div> -->
                </div>
            </div>
            <div style="flex:2; min-width:180px;">
                  <label class="small-text">Nama Asesi</label>
                <div class="field-readonly <?= $nama_asesi_db ? '' : 'empty' ?>">
                    <?= $nama_asesi_db ? h($nama_asesi_db) : '— tidak ditemukan —' ?>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal Mulai</label>
                <div class="field-readonly <?= $hari_tanggal_db ? '' : 'empty' ?>">
                    <?= $hari_tanggal_db ? htmlspecialchars($hari_tanggal_db) : '— AK-01 belum diisi —' ?>
                </div>
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal Selesai</label>
                <div class="field-readonly <?= $tgl_selesai_db ? '' : 'empty' ?>">
                    <?= $tgl_selesai_db
                        ? htmlspecialchars($tgl_selesai_db)
                        : '— AK-03 belum diisi —' ?>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title" style="margin:16px 0 8px;">
    Unit Kompetensi &amp; Metode Asesmen
</div>

<?php if (empty($list_unit)): ?>
    <div class="placeholder-box">
        Unit kompetensi tidak ditemukan untuk skema ini.
    </div>
<?php else: ?>
<div style="overflow-x:auto;">
    <table class="tbl-ak02">
        <thead>
            <tr>
                <th class="th-unit">Unit Kompetensi</th>
                <th>Observasi Demonstrasi</th>
                <th>Portofolio</th>
                <th>Pernyataan Pihak Ketiga</th>
                <th>Pertanyaan Wawancara</th>
                <th>Pertanyaan Lisan</th>
                <th>Pertanyaan Tertulis</th>
                <th>Proyek Kerja</th>
                <th class="th-unit">Lainnya</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list_unit as $unit):
                $uid = $unit['id_unit'];
                $saved = [];
                foreach ($detail_ak02_saved as $ds) {
                    if ($ds['id_unit'] == $uid) { $saved = $ds; break; }
                }
            ?>
            <tr>
                <td>
                    <?= htmlspecialchars($unit['judul_unit']) ?>
                    <input type="hidden" name="metode[<?= $uid ?>][id_unit]" value="<?= $uid ?>" <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][obs_demonstrasi]"
                           <?= !empty($saved['obs_demonstrasi']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][portofolio]"
                           <?= !empty($saved['portofolio']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][pyt_pihak_ketiga]"
                           <?= !empty($saved['pyt_pihak_ketiga']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][pyt_wawancara]"
                           <?= !empty($saved['pyt_wawancara']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][pyt_lisan]"
                           <?= !empty($saved['pyt_lisan']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][pyt_pertulis]"
                           <?= !empty($saved['pyt_pertulis']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td>
                    <input type="checkbox" name="metode[<?= $uid ?>][proyek_kerja]"
                           <?= !empty($saved['proyek_kerja']) ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>>
                </td>
                <td class="lainnya-cell">
                    <input type="text" name="metode[<?= $uid ?>][lainnya]"
                           value="<?= htmlspecialchars($saved['lainnya'] ?? '') ?>"
                           placeholder="Lainnya..." <?= $dsb_untuk_asesor ?>>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</div>
<?php endif; ?>

    <div id="hasil-section">
        <div class="section-title" style="margin:20px 0 8px;">Hasil Asesmen</div>
        <div class="hasil-box">
            <div style="margin-bottom:12px;">
                <label class="label" style="font-size:13px;">
                    Rekomendasi hasil asesmen <span class="required">*</span>
                </label>
                <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:6px; <?= $dsb_style ?>">
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Kompeten" <?= ($data_ak02_saved['rekomendasi'] ?? '') === 'Kompeten' ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>> <b>Kompeten</b>
                    </label>
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Belum Kompeten" <?= ($data_ak02_saved['rekomendasi'] ?? '') === 'Belum Kompeten' ? 'checked' : '' ?> <?= $dsb_untuk_asesor ?>> <b>Belum Kompeten</b>
                    </label>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="small-text">Tindak lanjut yang dibutuhkan :</label>
                <textarea name="tindak_lanjut" class="form-control" rows="3"
                          placeholder="Masukkan pekerjaan tambahan dan asesmen yang diperlukan..."
                          <?= $dsb_untuk_asesor ?> style="<?= $dsb_style ?>"><?= htmlspecialchars($data_ak02_saved['tindak_lanjut'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="small-text">Komentar / Observasi oleh Asesor :</label>
                <textarea name="komentar_asesor" class="form-control" rows="3"
                          placeholder="Komentar asesor..." 
                          <?= $dsb_untuk_asesor ?> style="<?= $dsb_style ?>"><?= htmlspecialchars($data_ak02_saved['komentar_asesor'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="ttd-box">
            <div class="ttd-col">
                <div class="col-title">Asesi</div>
                <div style="font-size:13px; margin-bottom:6px;">
                    <b>Nama :</b>
                    <span id="asesi-ttd-nama" style="color:#1a237e;">
                        <?php echo htmlspecialchars($nama_asesi_db); ?>
                    </span>
                </div>
                <!-- <div style="margin-bottom:8px;">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesi" id="tanggal_asesi"
                           class="form-control" onchange="scheduleQRAsesi()">
                </div> -->
                <!-- <div class="qr-box">
                    <div class="qr-title">QR Tanda Tangan Asesi</div>
                    <div class="qr-canvas-wrap" id="qr-asesi-canvas">
                        <div class="qr-ph-sm" id="qr-asesi-ph">
                        <span>Isi nama dulu</span>
                        </div>
                    </div>
                    <div id="qr-asesi-badge" class="qr-badge">QR Siap</div><br>
                    <button type="button" id="btn-dl-asesi" class="btn-dl-qr"
                            onclick="dlQRAsesi()">Download</button>
                </div> -->
            </div>

            <div class="ttd-col">
                <div class="col-title">Asesor</div>
                <div style="font-size:13px; margin-bottom:2px;">
                    <b>Nama :</b>
                        <?= $nama_asesor_db
                            ? htmlspecialchars($nama_asesor_db)
                            : '— tidak ditemukan —' ?>
                    </div>
                    <div style="font-size:11px; color:#888; margin-top:4px;">
                        <?php if ($noreg_asesor_db): ?>
                            <span style="font-size:11px; color:#666;" id="asesor-ttd-noreg">
                                &nbsp;No.Reg: <?= htmlspecialchars($noreg_asesor_db) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <!-- <div style="margin-bottom:8px; <= $dsb_style ?>">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesor" id="tanggal_asesor"
                           class="form-control" onchange="scheduleQRAsesor()"
                           <= $dsb ?> style="<= $dsb_style ?>">
                </div> -->
                <!-- <div class="qr-box" style="< $dsb_style ">
                    <div class="qr-title">QR Tanda Tangan Asesor</div>
                    <div class="qr-canvas-wrap" id="qr-asesor-canvas">
                        <div class="qr-ph-sm" id="qr-asesor-ph">
                        <span>Pilih skema dulu</span>
                        </div>
                    </div>
                    <div id="qr-asesor-badge" class="qr-badge">QR Siap</div><br>
                    <button type="button" id="btn-dl-asesor" class="btn-dl-qr"
                            onclick="dlQRAsesor()">⬇ Download</button>
                </div> -->
            </div>
        </div>
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
             Kembali
        </button>
    <?php if ($mode === 'view' && !$is_asesi): ?>
        <button type="submit" class="btn-submit"
                >
            UPDATE
        </button>
    <?php elseif ($mode !== 'view'): ?>
        <button type="submit" class="btn-submit">SIMPAN ✓</button>
    <?php endif; ?>
    </div>
</form>
</div>
</body>
</html>
