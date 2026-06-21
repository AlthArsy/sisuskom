<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$e = fn($s) => mysqli_real_escape_string($koneksi, (string) $s);
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    header('Location: ../LOGIN/login.php');
    exit;
}

$role              = $_SESSION['role'] ?? '';
$is_asesor         = ($role === 'Asesor');
$is_admin          = in_array($role, ['Admin_lsp', 'Admin_utm'], true);
$id_asesor_session = intval($_SESSION['id_asesor'] ?? 0);

if ($is_asesor && !$id_asesor_session) {
    $uname = $e($_SESSION['username'] ?? '');
    $ra = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = '$uname' LIMIT 1"));
    if ($ra) {
        $id_asesor_session = intval($ra['id_asesor']);
        $_SESSION['id_asesor'] = $id_asesor_session;
    }
}

$id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
$mode     = (isset($_GET['view']) && (string) $_GET['view'] === '1') ? 'view' : 'edit';

function ak05_asesi_skema($koneksi, $id_skema)
{
    $id_skema = intval($id_skema);
    $list     = [];
    $seen     = [];
    $q = mysqli_query($koneksi,
        "SELECT a.id_asesi, a.id_apl1, a.judul_skema, a.nomor_skema, s.nama_asesi
         FROM tb_apl1 a
         INNER JOIN tb_asesi s ON s.id_asesi = a.id_asesi
         WHERE a.id_skema = '$id_skema'
         ORDER BY s.nama_asesi ASC, a.id_apl1 ASC");
    if (!$q) {
        return $list;
    }
    while ($r = mysqli_fetch_assoc($q)) {
        $aid = intval($r['id_asesi']);
        if (isset($seen[$aid])) {
            continue;
        }
        $seen[$aid] = true;
        $list[]     = $r;
    }
    return $list;
}

function ak05_skema_asesor($koneksi, $id_asesor)
{
    $id_asesor = intval($id_asesor);
    $rows      = [];
    $q = mysqli_query($koneksi,
        "SELECT id_skema, judul_skema, nomor_skema
         FROM tb_skema
         WHERE id_asesor = '$id_asesor'
         ORDER BY judul_skema ASC");
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
    }
    return $rows;
}

$skema_info     = null;
$asesi_list     = [];
$id_asesor_db   = 0;
$nama_asesor_db = '';
$noreg_asesor_db = '';
$tuk_db         = '';
$tanggal_db     = '';
$catatan_db     = '';
$aspek_db       = '';
$pencatatan_db  = '';
$saran_db       = '';
$detail_map     = [];
$ak05_saved     = null;
$has_data       = false;

if ($id_skema) {
    $skema_info = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT sk.id_skema, sk.judul_skema, sk.nomor_skema, sk.id_asesor,
                ar.nama_asesor, ar.no_reg
         FROM tb_skema sk
         LEFT JOIN tb_asesor ar ON ar.id_asesor = sk.id_asesor
         WHERE sk.id_skema = '$id_skema'
         LIMIT 1"));

    if (!$skema_info) {
        $id_skema = 0;
    } else {
        $id_asesor_db    = intval($skema_info['id_asesor']);
        $nama_asesor_db  = $skema_info['nama_asesor'] ?? '';
        $noreg_asesor_db = $skema_info['no_reg'] ?? '';

        if ($is_asesor && $id_asesor_session && $id_asesor_db !== $id_asesor_session) {
            echo "<script>alert('Anda tidak berwenang mengakses skema ini.'); history.back();</script>";
            exit;
        }

        $asesi_list = ak05_asesi_skema($koneksi, $id_skema);

        $rtuk = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT ak.tuk FROM tb_ak01 ak
             INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak.id_apl1
             WHERE ap.id_skema = '$id_skema' AND ak.tuk IS NOT NULL AND ak.tuk != ''
             ORDER BY ak.id_ak01 DESC LIMIT 1"));
        $tuk_db = $rtuk['tuk'] ?? '';

        $ak05_saved = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT ak5.*
             FROM tb_ak05 ak5
             INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak5.id_apl1
             WHERE ap.id_skema = '$id_skema'
               AND ak5.id_asesor = '$id_asesor_db'
             ORDER BY ak5.id_ak5 DESC
             LIMIT 1"));

        if ($ak05_saved) {
            $has_data   = true;
            $catatan_db = $ak05_saved['catatan'] ?? '';
            $id_ak5     = intval($ak05_saved['id_ak5']);

            $rd = mysqli_query($koneksi,
                "SELECT d.*, s.nama_asesi
                 FROM detail_ak5 d
                 INNER JOIN tb_asesi s ON s.id_asesi = d.id_asesi
                 WHERE d.id_ak5 = '$id_ak5'
                 ORDER BY s.nama_asesi ASC");
            while ($d = mysqli_fetch_assoc($rd)) {
                $detail_map[intval($d['id_asesi'])] = $d;
                if ($tanggal_db === '' && !empty($d['tanggal'])) {
                    $tanggal_db = $d['tanggal'];
                }
                if ($aspek_db === '' && !empty($d['aspek'])) {
                    $aspek_db = $d['aspek'];
                }
                if ($pencatatan_db === '' && !empty($d['pencatatan'])) {
                    $pencatatan_db = $d['pencatatan'];
                }
                if ($saran_db === '' && !empty($d['saran'])) {
                    $saran_db = $d['saran'];
                }
            }
        }

        if ($tanggal_db === '') {
            $rtgl = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT MAX(ak3.tgl_selesai) AS tgl
                 FROM tb_ak03 ak3
                 INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak3.id_apl1
                 WHERE ap.id_skema = '$id_skema'
                   AND ak3.tgl_selesai IS NOT NULL AND ak3.tgl_selesai != ''"));
            $tanggal_db = $rtgl['tgl'] ?? date('Y-m-d');
        }
    }
}

if ($has_data && $mode !== 'view' && !isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php&id_skema=' . $id_skema . '&view=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan_ak05') {
    if (!$is_asesor) {
        echo "<script>alert('Hanya Asesor yang dapat mengisi FR.AK.05.'); history.back();</script>";
        exit;
    }
    $asesi_save = ak05_asesi_skema($koneksi, $id_skema);
    if (!$id_skema || !$skema_info || empty($asesi_save)) {
        echo "<script>alert('Data skema atau daftar asesi tidak ditemukan.'); history.back();</script>";
        exit;
    }

    $tanggal_post    = trim($_POST['tanggal'] ?? '');
    $catatan_post    = trim($_POST['catatan'] ?? '');
    $aspek_post      = trim($_POST['aspek'] ?? '');
    $pencatatan_post = trim($_POST['pencatatan'] ?? '');
    $saran_post      = trim($_POST['saran'] ?? '');
    $rekom_post      = $_POST['rekomend'] ?? [];
    $ket_post        = $_POST['keterangan'] ?? [];

    $id_apl1_ref = intval($asesi_save[0]['id_apl1']);

    $old = mysqli_query($koneksi,
        "SELECT ak5.id_ak5 FROM tb_ak05 ak5
         INNER JOIN tb_apl1 ap ON ap.id_apl1 = ak5.id_apl1
         WHERE ap.id_skema = '$id_skema' AND ak5.id_asesor = '$id_asesor_db'");
    while ($o = mysqli_fetch_assoc($old)) {
        mysqli_query($koneksi, "DELETE FROM tb_ak05 WHERE id_ak5 = '" . intval($o['id_ak5']) . "'");
    }

    $ins_head = mysqli_query($koneksi,
        "INSERT INTO tb_ak05 (id_asesor, id_apl1, catatan)
         VALUES ('$id_asesor_db', '$id_apl1_ref', " .
        ($catatan_post !== '' ? "'" . $e($catatan_post) . "'" : 'NULL') . ")");

    if (!$ins_head) {
        echo "<script>alert('Gagal menyimpan: " . addslashes(mysqli_error($koneksi)) . "'); history.back();</script>";
        exit;
    }

    $id_ak5_new = mysqli_insert_id($koneksi);
    $gagal      = false;

    foreach ($asesi_save as $as) {
        $aid = intval($as['id_asesi']);
        $rek = strtoupper(trim((string) ($rekom_post[$aid] ?? '')));
        if (!in_array($rek, ['K', 'BK'], true)) {
            $rek = 'K';
        }
        $ket = trim((string) ($ket_post[$aid] ?? ''));

        $sql_d = "INSERT INTO detail_ak5
            (id_ak5, id_asesi, rekomend, keterangan, tanggal, aspek, pencatatan, saran)
            VALUES (
                '$id_ak5_new', '$aid', '$rek',
                " . ($ket !== '' ? "'" . $e($ket) . "'" : 'NULL') . ",
                " . ($tanggal_post !== '' ? "'" . $e($tanggal_post) . "'" : 'NULL') . ",
                " . ($aspek_post !== '' ? "'" . $e($aspek_post) . "'" : 'NULL') . ",
                " . ($pencatatan_post !== '' ? "'" . $e($pencatatan_post) . "'" : 'NULL') . ",
                " . ($saran_post !== '' ? "'" . $e($saran_post) . "'" : 'NULL') . "
            )";
        if (!mysqli_query($koneksi, $sql_d)) {
            $gagal = true;
        }
    }

    if ($gagal) {
        echo "<script>alert('Header tersimpan, sebagian detail gagal.');</script>";
    } else {
        echo "<script>alert('FR.AK.05 berhasil disimpan!');</script>";
    }
    header('Location: ../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php&id_skema=' . $id_skema . '&view=1');
    exit;
}

$is_readonly = ($mode === 'view' || $is_admin);
$can_edit    = $is_asesor && !$is_readonly;
$dsb         = $can_edit ? '' : 'disabled';
$ro_style    = $can_edit ? '' : 'background:#f5f5f5;';

$asesi_list_real = $asesi_list;
$min_rows        = 9;
$display_rows    = max($min_rows, count($asesi_list_real));
while (count($asesi_list) < $display_rows) {
    $asesi_list[] = ['id_asesi' => 0, 'nama_asesi' => '', 'id_apl1' => 0];
}

$skema_picker = [];
if (!$id_skema) {
    if ($is_asesor && $id_asesor_session) {
        $skema_picker = ak05_skema_asesor($koneksi, $id_asesor_session);
    } elseif ($is_admin) {
        $qsk = mysqli_query($koneksi,
            "SELECT id_skema, judul_skema, nomor_skema FROM tb_skema ORDER BY judul_skema ASC");
        while ($s = mysqli_fetch_assoc($qsk)) {
            $skema_picker[] = $s;
        }
    }
}

$tuk_opts = ['Sewaktu', 'Tempat Kerja', 'Mandiri'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/fapl2.css">
    <title>FR.AK.05 Laporan Asesmen</title>
    <style>
        .tbl-ak05 { width:100%; border-collapse:collapse; font-size:12px; margin:12px 0; }
        .tbl-ak05 th {
            background:#cadbfc; padding:8px 6px;
            border:1px solid #b0bec5; text-align:center;
        }
        .tbl-ak05 td { padding:6px 8px; border:1px solid #ccc; vertical-align:middle; }
        .tbl-ak05 .col-no { width:40px; text-align:center; }
        .tbl-ak05 .col-rek { width:50px; text-align:center; }
        .tbl-ak05 input[type="radio"] { width:16px; height:16px; cursor:pointer; }
        .tbl-ak05 input[type="text"], .tbl-ak05 textarea { width:100%; font-size:12px; }
        .note-foot { font-size:11px; color:#555; margin-top:4px; }
        .skema-pick {
            display:grid; gap:10px; max-width:640px; margin:20px auto;
        }
        .skema-pick a {
            display:block; padding:12px 16px; border:1px solid #ddd;
            border-radius:8px; text-decoration:none; color:#1a237e;
            background:#fafbff;
        }
        .skema-pick a:hover { border-color:#4A7AFF; background:#eef3ff; }
    </style>
</head>
<body>
<div class="form-box">

<h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
    FR.AK.05. LAPORAN ASESMEN
</h2>

<?php if (!$id_skema): ?>
    <p style="text-align:center; color:#555; margin:16px 0;">
        Pilih skema sertifikasi. Daftar asesi menyesuaikan skema yang dipilih.
    </p>
    <?php if (empty($skema_picker)): ?>
        <div class="AA" style="text-align:center;">
            Belum ada skema yang dapat diakses.<br><br>
            <a href="../BERANDA/UTAMA.php" class="btn-back">← Kembali</a>
        </div>
    <?php else: ?>
        <div class="skema-pick">
            <?php foreach ($skema_picker as $sp): ?>
            <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php&id_skema=<?= (int) $sp['id_skema'] ?>">
                <strong><?= $h($sp['judul_skema']) ?></strong><br>
                <span style="font-size:12px;color:#555;">No. <?= $h($sp['nomor_skema']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:16px;">
            <a href="../BERANDA/UTAMA.php" class="btn-back">← Kembali</a>
        </div>
    <?php endif; ?>

<?php elseif (empty($asesi_list_real)): ?>
    <div class="AA" style="text-align:center;">
        Belum ada asesi yang memilih skema <strong><?= $h($skema_info['judul_skema']) ?></strong>.<br><br>
        <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php" class="btn-back">← Pilih Skema Lain</a>
    </div>

<?php else: ?>

<?php if ($mode === 'view' && $has_data): ?>
<p style="font-size:12px; color:#555; margin:10px 0 16px; text-align:center;">
    Laporan asesmen untuk semua asesi pada skema ini.
</p>
<?php endif; ?>

<form method="post" autocomplete="off">
<?php if ($can_edit): ?>
<input type="hidden" name="aksi" value="simpan_ak05">
<?php endif; ?>

<div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin-bottom:14px;">
    <div class="label" style="margin-bottom:8px;">
        Skema Sertifikasi <span style="font-size:12px;color:#555;">(KKNI/Okupasi/Klaster)</span>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <div style="flex:2; min-width:180px;">
            <label class="small-text">Judul</label>
            <input type="text" class="form-control" value="<?= $h($skema_info['judul_skema']) ?>"
                   readonly style="background:#f5f5f5;">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="small-text">Nomor</label>
            <input type="text" class="form-control" value="<?= $h($skema_info['nomor_skema']) ?>"
                   readonly style="background:#f5f5f5;">
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:14px; display:flex; gap:14px; flex-wrap:wrap;">
    <div style="flex:1; min-width:140px;">
        <label class="label">TUK</label>
        <input type="text" class="form-control" value="<?= $h($tuk_db) ?>"
               readonly style="background:#f5f5f5;">
        <div style="font-size:11px;color:#777;margin-top:4px;">
            <?= implode(' / ', $tuk_opts) ?>*
        </div>
    </div>
    <div style="flex:1; min-width:140px;">
        <label class="label">Nama Asesor</label>
        <input type="text" class="form-control" value="<?= $h($nama_asesor_db ?: '-') ?>"
               readonly style="background:#f5f5f5;">
    </div>
    <div style="flex:1; min-width:140px;">
        <label class="label">Tanggal <?= $can_edit ? '<span style="color:red;">*</span>' : '' ?></label>
        <input type="date" name="tanggal" class="form-control"
               value="<?= $h($tanggal_db) ?>"
               <?= $dsb ?> required style="<?= $ro_style ?>">
    </div>
</div>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Rekomendasi hasil asesmen
</div>

<table class="tbl-ak05">
    <thead>
        <tr>
            <th class="col-no">No.</th>
            <th>Nama Asesi</th>
            <th class="col-rek">K</th>
            <th class="col-rek">BK</th>
            <th>Keterangan**</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 0;
        foreach ($asesi_list as $row):
            $no++;
            $aid  = intval($row['id_asesi']);
            $nama = $row['nama_asesi'] ?? '';
            if ($aid === 0) {
                $nama = '';
            }
            $det  = $detail_map[$aid] ?? [];
            $rek  = strtoupper($det['rekomend'] ?? 'K');
            $ket  = $det['keterangan'] ?? '';
        ?>
        <tr>
            <td class="col-no"><?= $no ?></td>
            <td><?= $aid ? $h($nama) : '&nbsp;' ?></td>
            <?php if ($aid && $can_edit): ?>
            <td class="col-rek">
                <input type="radio" name="rekomend[<?= $aid ?>]" value="K"
                       <?= $rek === 'K' ? 'checked' : '' ?>>
            </td>
            <td class="col-rek">
                <input type="radio" name="rekomend[<?= $aid ?>]" value="BK"
                       <?= $rek === 'BK' ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="text" name="keterangan[<?= $aid ?>]" class="form-control"
                       value="<?= $h($ket) ?>" placeholder="Kode & judul unit (jika BK)">
            </td>
            <?php elseif ($aid): ?>
            <td class="col-rek">
                <input type="radio" name="rekomend[<?= $aid ?>]" value="K"
                       <?= $rek === 'K' ? 'checked' : '' ?> disabled>
            </td>
            <td class="col-rek">
                <input type="radio" name="rekomend[<?= $aid ?>]" value="BK"
                       <?= $rek === 'BK' ? 'checked' : '' ?> disabled>
            </td>
            <td><input type="text" class="form-control" value="<?= $h($ket) ?>" readonly style="<?= $ro_style ?>"></td>
            <?php else: ?>
            <td class="col-rek">&nbsp;</td>
            <td class="col-rek">&nbsp;</td>
            <td>&nbsp;</td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="note-foot">** tuliskan Kode dan Judul Unit Kompetensi yang dinyatakan BK bila mengases satu skema</p>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Aspek Negatif dan Positif dalam Asesmen
</div>
<textarea name="aspek" class="form-control" rows="3" <?= $dsb ?>
          style="<?= $ro_style ?>"><?= $h($aspek_db) ?></textarea>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Pencatatan Penolakan Hasil Asesmen
</div>
<textarea name="pencatatan" class="form-control" rows="2" <?= $dsb ?>
          style="<?= $ro_style ?>"><?= $h($pencatatan_db) ?></textarea>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Saran Perbaikan (Asesor/Personil Terkait)
</div>
<textarea name="saran" class="form-control" rows="2" <?= $dsb ?>
          style="<?= $ro_style ?>"><?= $h($saran_db) ?></textarea>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Catatan
</div>
<textarea name="catatan" class="form-control" rows="2" <?= $dsb ?>
          style="<?= $ro_style ?>"><?= $h($catatan_db) ?></textarea>

<div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:24px 0 10px;">
    Asesor
</div>
<div style="display:flex; gap:14px; flex-wrap:wrap; border:1px solid #ddd; border-radius:5px; padding:12px; background:#fafbff;">
    <div style="flex:1; min-width:160px;">
        <label class="small-text">Nama</label>
        <input type="text" class="form-control" value="<?= $h($nama_asesor_db) ?>" readonly style="background:#f5f5f5;">
    </div>
    <div style="flex:1; min-width:160px;">
        <label class="small-text">No. Reg</label>
        <input type="text" class="form-control" value="<?= $h($noreg_asesor_db) ?>" readonly style="background:#f5f5f5;">
    </div>
    <div style="flex:1; min-width:160px;">
        <label class="small-text">Tanda tangan / Tanggal</label>
        <input type="text" class="form-control" value="<?= $h($tanggal_db) ?>" readonly style="background:#f5f5f5;">
    </div>
</div>

<div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
    <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php" class="btn-back">← Pilih Skema</a>
    <?php if ($is_admin || $is_asesor): ?>
    <a href="../BERANDA/UTAMA.php?page=../list/rekap_ak05.php" class="btn-back">Rekap</a>
    <?php endif; ?>
    <?php if ($has_data): ?>
    <a href="../pdf/cetak_ak5.php?id_skema=<?= (int) $id_skema ?>" target="_blank"
       class="btn-submit" style="background:#1a237e;text-decoration:none;">Cetak PDF</a>
    <?php endif; ?>
    <?php if ($can_edit): ?>
    <button type="submit" class="btn-submit">SIMPAN</button>
    <?php elseif ($is_asesor && $has_data): ?>
    <a href="../BERANDA/UTAMA.php?page=../FR_APL/FR_AK05.php&id_skema=<?= (int) $id_skema ?>&edit=1"
       class="btn-submit" style="text-decoration:none;">Ubah</a>
    <?php endif; ?>
</div>

</form>
<?php endif; ?>

</div>
</body>
</html>
