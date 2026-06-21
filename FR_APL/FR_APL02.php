<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi','Asesor','Admin_lsp','Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
}

function h($value)
{
    return htmlspecialchars((string) $value);
}

$id_asesi = isset($_GET['id_asesi']) 
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$role = $_SESSION['role'] ?? '';
$is_asesi = (isset($_SESSION['role']) && $_SESSION['role'] === 'Asesi');
$is_asesor = (isset($_SESSION['role']) && $_SESSION['role'] === 'Asesor');
$is_admin_role = in_array($role, ['Admin_lsp', 'Admin_utm']);

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
            $rk = mysqli_query($koneksi,
                "SELECT * FROM tb_kuk WHERE id_elemen='$id_el'");
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
        "SELECT id_elemen, nilai
         FROM detail_apl2
         WHERE id_apl2='$id_apl2_q'
           AND nilai != ''
           AND id_detail_apl2 IN (
               SELECT MAX(id_detail_apl2)
               FROM detail_apl2
               WHERE id_apl2='$id_apl2_q' AND nilai != ''
               GROUP BY id_elemen
           )");
    while ($j = mysqli_fetch_assoc($rj)) {
        $jawaban_exist[$j['id_elemen']] = $j['nilai'];
    }
}

$apl2_selesai_asesi = $apl2_exist && trim((string) ($apl2_exist['tertanda'] ?? '')) !== '';
$mode_edit_asesor   = $is_asesor && isset($_GET['edit']) && $_GET['edit'] == '1';
$mode_melihat       = isset($_GET['view']) && $_GET['view'] == 1;
$mode_lihat         = $mode_melihat || $is_admin_role
    || ($is_asesor && $apl2_exist && !$mode_edit_asesor);
$asesi_isi_form     = $is_asesi && $apl1 && !$apl2_selesai_asesi;
$asesor_edit_form   = $is_asesor && $apl2_exist && $mode_edit_asesor;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan_asesi' && $is_asesi) {
    $id_apl1_fk   = intval($apl1['id_apl1']  ?? 0);
    $id_asesor_fk = intval($apl1['id_asesor'] ?? 0);
    $nama_ttd     = trim($_POST['nama_asesi_ttd'] ?? $nama_asesi_db);
    $jawaban      = $_POST['jawaban'] ?? [];

    if ($id_skema && $id_asesi && $id_apl1_fk) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, (string) $v);

        if ($apl2_exist) {
            $id_apl2_upd = intval($apl2_exist['id_apl2']);
            mysqli_query($koneksi,
                "UPDATE tb_apl2 SET tertanda='{$e($nama_ttd)}' WHERE id_apl2='$id_apl2_upd'");
        } else {
            $res = mysqli_query($koneksi,
                "INSERT INTO tb_apl2 (id_apl1, id_asesi, id_asesor, rekomendasi, tertanda)
                 VALUES ('$id_apl1_fk','$id_asesi','$id_asesor_fk', NULL, '{$e($nama_ttd)}')");
            if (!$res) {
                echo "<script>alert('Gagal simpan!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
                exit;
            }
            $id_apl2_upd = mysqli_insert_id($koneksi);
            foreach ($units as $u) {
                foreach ($u['elemen'] as $el) {
                    $id_el_i = intval($el['id_elemen']);
                    foreach ($el['kuk'] as $k) {
                        $id_kuk_i = intval($k['id_kuk']);
                        mysqli_query($koneksi,
                            "INSERT INTO detail_apl2 (id_apl2,id_skema,id_unit,id_elemen,id_kuk,nilai)
                             VALUES ('$id_apl2_upd','$id_skema','{$u['id_unit']}','$id_el_i','$id_kuk_i','')");
                    }
                }
            }
        }

        foreach ($jawaban as $id_elemen => $nilai) {
            $id_el_i   = intval($id_elemen);
            $nilai_esc = $e(trim((string) $nilai));
            if ($id_el_i <= 0 || !in_array($nilai_esc, ['K', 'BK'], true)) {
                continue;
            }
            $cek = mysqli_num_rows(mysqli_query($koneksi,
                "SELECT id_detail_apl2 FROM detail_apl2
                 WHERE id_apl2='$id_apl2_upd' AND id_elemen='$id_el_i' LIMIT 1"));
            if ($cek > 0) {
                mysqli_query($koneksi,
                    "UPDATE detail_apl2 SET nilai='$nilai_esc'
                     WHERE id_apl2='$id_apl2_upd' AND id_elemen='$id_el_i'");
            } else {
                $row_unit = mysqli_fetch_assoc(mysqli_query($koneksi,
                    "SELECT u.id_unit, u.id_skema, MIN(k.id_kuk) AS id_kuk
                     FROM tb_elemen e
                     JOIN tb_unit_kompetensi u ON u.id_unit = e.id_unit
                     JOIN tb_kuk k ON k.id_elemen = e.id_elemen
                     WHERE e.id_elemen='$id_el_i'
                     GROUP BY u.id_unit, u.id_skema LIMIT 1"));
                if ($row_unit) {
                    mysqli_query($koneksi,
                        "INSERT INTO detail_apl2 (id_apl2, id_skema, id_unit, id_elemen, id_kuk, nilai)
                         VALUES ('$id_apl2_upd','" . intval($row_unit['id_skema']) . "','"
                         . intval($row_unit['id_unit']) . "','$id_el_i','"
                         . intval($row_unit['id_kuk']) . "','$nilai_esc')");
                }
            }
        }

        echo "<script>alert('APL-02 berhasil disimpan!');
              window.location.href='../BERANDA/UTAMA.php?page=../FR_APL/FR_APL02.php&view=1&id_asesi={$id_asesi}';</script>";
        exit;
    }
    echo "<script>alert('Data APL 1 belum ada atau skema tidak ditemukan!');</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan_asesor' && $is_asesor) {
    $id_apl2_upd = intval($_POST['id_apl2_upd'] ?? 0);
    $rekomendasi = trim($_POST['rekomendasi']   ?? '');
    $jawaban     = $_POST['jawaban']            ?? [];

    if ($id_apl2_upd) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);
        mysqli_query($koneksi,
            "UPDATE tb_apl2 SET rekomendasi=" . ($rekomendasi ? "'{$e($rekomendasi)}'" : "NULL") .
            " WHERE id_apl2='$id_apl2_upd'");

        foreach ($jawaban as $id_elemen => $nilai) {
            $id_el_i   = intval($id_elemen);
            $nilai_esc = mysqli_real_escape_string($koneksi, $nilai);
            $cek = mysqli_num_rows(mysqli_query($koneksi,
                "SELECT id_detail_apl2 FROM detail_apl2
                 WHERE id_apl2='$id_apl2_upd' AND id_elemen='$id_el_i' LIMIT 1"));
            if ($cek > 0) {
                mysqli_query($koneksi,
                    "UPDATE detail_apl2 SET nilai='$nilai_esc'
                     WHERE id_apl2='$id_apl2_upd' AND id_elemen='$id_el_i'");
            } else {
                $row_unit = mysqli_fetch_assoc(mysqli_query($koneksi,
                    "SELECT u.id_unit, u.id_skema, MIN(k.id_kuk) as id_kuk
                     FROM tb_elemen e
                     JOIN tb_unit_kompetensi u ON u.id_unit = e.id_unit
                     JOIN tb_kuk k ON k.id_elemen = e.id_elemen
                     WHERE e.id_elemen='$id_el_i'
                     GROUP BY u.id_unit, u.id_skema
                     LIMIT 1"));
                if ($row_unit && $row_unit['id_skema']) {
                    $id_unit_ins  = intval($row_unit['id_unit']);
                    $id_skema_ins = intval($row_unit['id_skema']);
                    $id_kuk_ins   = intval($row_unit['id_kuk']);
                    mysqli_query($koneksi,
                        "INSERT INTO detail_apl2 (id_apl2, id_skema, id_unit, id_elemen, id_kuk, nilai)
                         VALUES ('$id_apl2_upd','$id_skema_ins','$id_unit_ins','$id_el_i','$id_kuk_ins','$nilai_esc')");
                }
            }
        }
        echo "<script>alert('Penilaian K/BK berhasil disimpan!'); window.location.href='../BERANDA/UTAMA.php?page=../list/rekap_frapl2.php';</script>";
        exit;
    }
} 
 ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/fapl2.css">
    <title>FR APL-02 Asesmen Mandiri</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>var ID_ASESI = <?php echo $id_asesi; ?>;</script>
</head>
<body>
<div class="form-box">

<h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
    FR. APL-02. ASESMEN MANDIRI
</h2>

<?php if (!$apl1): ?>
<div style="text-align:center; padding:30px; color:#c00; font-size:14px;">
    Formulir APL 1 belum diisi. Silakan isi APL 1 terlebih dahulu.<br><br>
    <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">← Kembali</a>
</div>

<?php elseif (($mode_lihat || $asesor_edit_form) && $apl2_exist): ?>
<?php if ($asesor_edit_form): ?>
<form method="post" autocomplete="off">
    <input type="hidden" name="aksi"        value="simpan_asesor">
    <input type="hidden" name="id_apl2_upd" value="<?= $apl2_exist['id_apl2'] ?>">
<?php endif; ?>

    <div style="border:1px solid #ddd;border-radius:5px;padding:12px 14px;background:#fafbff;margin:16px 0 10px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi <span style="font-size:12px;color:#555;">(KKNI/Okupasi/Klaster)</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <div style="flex:2;min-width:180px;">
                <label class="small-text">Judul</label>
                <input type="text" class="form-control"
                       value="<?= h($apl1['judul_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
            
            <div style="flex:1;min-width:140px;">
                <label class="small-text">Nomor</label>
                <input type="text" class="form-control"
                       value="<?= h($apl1['nomor_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
        </div>
        <div>
               <div style="margin-bottom:8px;">
                <label class="small-text">Nama Asesi</label>
                <input type="text" class="form-control"
                       value="<?= h($nama_asesi_db) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
        </div>
    </div>

    <div style="background:#fffde7;border:1px solid #ffe082;border-radius:5px;
                padding:10px 14px;font-size:13px;margin-bottom:14px;">
        <b>PANDUAN ASESMEN MANDIRI</b>
        <ul style="margin:6px 0 0 16px;padding:0;">
            <li>Baca setiap pertanyaan di kolom sebelah kiri</li>
            <li>Kolom <b>K</b> / <b>BK</b> diisi oleh <b>Asesi</b> (asesmen mandiri)</li>
            <li>Rekomendasi <b>Dapat / Tidak Dapat</b> diisi oleh <b>Asesor</b></li>
            <li>Kolom <b>Bukti</b> diambil otomatis dari APL 1</li>
        </ul>
    </div>

    <?php foreach ($units as $ui => $u): ?>
    <div class="unit-box" style="margin-bottom:18px;">
        <div class="unit-header">
            Unit Kompetensi <?= $ui+1 ?><br>
            <span class="unit-sub">Kode Unit : <?= h($u['kode_unit']) ?></span><br>
            <span class="unit-sub">Judul Unit : <?= h($u['judul_unit']) ?></span>
        </div>
        <div style="overflow-x:auto;">
        <table class="tbl-apl2">
            <thead><tr>
                <th style="width:45%;">Dapatkah Saya.......?</th>
                <th style="width:6%;">K</th>
                <th style="width:6%;">BK</th>
                <th>Bukti yang relevan</th>
            </tr></thead>
            <tbody>
            <?php foreach ($u['elemen'] as $el):
                $nilai = $jawaban_exist[$el['id_elemen']] ?? '';
            ?>
            <tr class="elemen-row">
                <td colspan="4">
                    <?= h($el['no_elemen']) ?>. <?= h($el['nama_elemen']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:11px;color:#555;">Kriteria Unjuk Kerja:</span>
                    <ul class="kuk-list">
                        <?php foreach ($el['kuk'] as $k): ?>
                        <li><?= h($k['no_kuk']) ?> <?= h($k['kuk']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>

                <td style="text-align:center;vertical-align:middle;">
                    <?php if ($asesor_edit_form): ?>
                        <label style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <input type="radio" name="jawaban[<?= $el['id_elemen'] ?>]" value="K"
                                   <?= $nilai === 'K' ? 'checked' : '' ?>
                                   style="accent-color:#2e7d32;width:16px;height:16px;">
                        </label>
                    <?php else: ?>
                        <input type="radio" disabled <?= $nilai === 'K' ? 'checked' : '' ?>
                               style="accent-color:#2e7d32;width:16px;height:16px;
                                      <?= $nilai === 'K' ? '' : 'opacity:0.3;' ?>">
                    <?php endif; ?>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <?php if ($asesor_edit_form): ?>
                        <label style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <input type="radio" name="jawaban[<?= $el['id_elemen'] ?>]" value="BK"
                                   <?= $nilai === 'BK' ? 'checked' : '' ?>
                                   style="accent-color:#c00;width:16px;height:16px;">
                        </label>
                    <?php else: ?>
                        <input type="radio" disabled <?= $nilai === 'BK' ? 'checked' : '' ?>
                               style="accent-color:#c00;width:16px;height:16px;
                                      <?= $nilai === 'BK' ? '' : 'opacity:0.3;' ?>">
                    <?php endif; ?>
                </td>
                <td>
                    <textarea class="bukti-input" readonly
                              style="background:#f5f5f5;cursor:default;resize:none;height:100px;"
                    ><?= h($bukti_text) ?></textarea>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="rek-box">
        <div class="rek-col">
            <div class="col-title">Ditinjau Oleh Asesor :   </div>
              <div style="font-size:13px;font-weight:bold;color:#1a237e;margin-bottom:4px;">
                <?= h($apl1['nama_asesor'] ?? '-') ?>
            </div>
            <div style="font-size:12px;color:#888;margin-bottom:8px;">
                No. Reg: <?= h($apl1['no_reg'] ?? '-') ?>
            </div>
            <div style="font-size:11px;color:#888;font-style:italic;">
                * K/BK & Rekomendasi akan diisi Asesor setelah asesmen
            </div>
            <div style="font-size:13px;margin-bottom:8px;">
                Asesmen dapat / tidak dapat dilanjutkan melalui :
            </div>            

            <?php if ($asesor_edit_form): ?>
            <div style="display:flex;gap:14px;margin-bottom:10px;flex-wrap:wrap;">
                <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:5px;">
                    <input type="radio" name="rekomendasi" value="Dapat"
                           <?= $apl2_exist['rekomendasi'] === 'Dapat' ? 'checked' : '' ?>
                           style="accent-color:#2e7d32;width:15px;height:15px;">
                    <span style="font-weight:bold;">Dapat</span>
                </label>
                <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:5px;">
                    <input type="radio" name="rekomendasi" value="Tidak Dapat"
                           <?= $apl2_exist['rekomendasi'] === 'Tidak Dapat' ? 'checked' : '' ?>
                           style="accent-color:#c00;width:15px;height:15px;">
                    <span style="font-weight:bold;">Tidak Dapat</span>
                </label>
            </div>
            <?php else: ?>
            <div style="display:flex;gap:14px;margin-bottom:10px;pointer-events:none;opacity:0.6;">
                <label style="font-size:13px;display:flex;align-items:center;gap:5px;">
                    <input type="radio" disabled <?= $apl2_exist['rekomendasi'] === 'Dapat' ? 'checked' : '' ?>>
                    Dapat
                </label>
                <label style="font-size:13px;display:flex;align-items:center;gap:5px;">
                    <input type="radio" disabled <?= $apl2_exist['rekomendasi'] === 'Tidak Dapat' ? 'checked' : '' ?>>
                    Tidak Dapat
                </label>
            </div>
            <?php endif; ?>

            
        </div>

        <div class="rek-col">
            <div class="col-title">Asesi :</div> 
            <div style="font-size:13px; margin-bottom:6px;">
                <b>Nama :</b>
                <span id="asesi-ttd-nama" style="color:#1a237e;"><?= h($nama_asesi_db) ?></span>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:20px;">
        <?php if ($is_asesor || $is_admin_role): ?>
        <a href="../BERANDA/UTAMA.php?page=../list/rekap_frapl2.php" class="btn-back">Kembali</a>
        <?php if ($asesor_edit_form): ?>
        <button type="submit" class="btn-submit">SIMPAN PENILAIAN</button>
        <?php endif; ?>
        <a href="../pdf/cetak_apl2.php?id_asesi=<?= $id_asesi ?>" 
           target="_blank" 
           class="btn-submit" 
           style="background:#1a237e;text-decoration:none;">
           Cetak PDF
        </a>
        <?php endif; ?>
        <?php if ($is_asesi): ?>
        <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">Kembali</a>
        <?php endif; ?>
        
    </div>

<?php if ($asesor_edit_form): ?>
</form>
<?php endif; ?>

<?php elseif ($asesi_isi_form): ?>
<form method="post" autocomplete="off">
    <input type="hidden" name="aksi" value="simpan_asesi">

    <div style="border:1px solid #ddd;border-radius:5px;padding:12px 14px;background:#fafbff;margin:16px 0 10px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi <span style="font-size:12px;color:#555;">(KKNI/Okupasi/Klaster)</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <div style="flex:2;min-width:180px;">
                <label class="small-text">Judul</label>
                <input type="text" class="form-control"
                       value="<?= h($apl1['judul_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
            <div style="flex:1;min-width:140px;">
                <label class="small-text">Nomor</label>
                <input type="text" class="form-control"
                       value="<?= h($apl1['nomor_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
        </div>
    </div>

    <div style="background:#fffde7;border:1px solid #ffe082;border-radius:5px;
                padding:10px 14px;font-size:13px;margin-bottom:14px;">
        <b>PANDUAN ASESMEN MANDIRI</b>
        <ul style="margin:6px 0 0 16px;padding:0;">
            <li>Baca setiap pertanyaan di kolom sebelah kiri</li>
            <li>Kolom <b>K</b> / <b>BK</b> diisi oleh <b>Asesi</b> (asesmen mandiri)</li>
            <li>Rekomendasi <b>Dapat / Tidak Dapat</b> diisi oleh <b>Asesor</b></li>
            <li>Kolom <b>Bukti</b> diambil otomatis dari APL 1</li>
        </ul>
    </div>

    <?php foreach ($units as $ui => $u): ?>
    <div class="unit-box" style="margin-bottom:18px;">
        <div class="unit-header">
            Unit Kompetensi <?= $ui+1 ?><br>
            <span class="unit-sub">Kode Unit : <?= h($u['kode_unit']) ?></span><br>
            <span class="unit-sub">Judul Unit : <?= h($u['judul_unit']) ?></span>
        </div>
        <div style="overflow-x:auto;">
        <table class="tbl-apl2">
            <thead><tr>
                <th style="width:45%;">Dapatkah Saya.......?</th>
                <th style="width:6%;">K</th>
                <th style="width:6%;">BK</th>
                <th>Bukti yang relevan</th>
            </tr></thead>
            <tbody>
            <?php foreach ($u['elemen'] as $el): ?>
            <tr class="elemen-row">
                <td colspan="4">
                    <?= h($el['no_elemen']) ?>. <?= h($el['nama_elemen']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:11px;color:#555;">Kriteria Unjuk Kerja:</span>
                    <ul class="kuk-list">
                        <?php foreach ($el['kuk'] as $k): ?>
                        <li><?= h($k['no_kuk']) ?> <?= h($k['kuk']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <?php $nilai_asesi = $jawaban_exist[$el['id_elemen']] ?? ''; ?>
                <td style="text-align:center;vertical-align:middle;">
                    <label style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                        <input type="radio" name="jawaban[<?= $el['id_elemen'] ?>]" value="K"
                               <?= $nilai_asesi === 'K' ? 'checked' : '' ?>
                               style="accent-color:#2e7d32;width:16px;height:16px;">
                    </label>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <label style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                        <input type="radio" name="jawaban[<?= $el['id_elemen'] ?>]" value="BK"
                               <?= $nilai_asesi === 'BK' ? 'checked' : '' ?>
                               style="accent-color:#c00;width:16px;height:16px;">
                    </label>
                </td>
                <td>
                    <textarea class="bukti-input" readonly
                              style="background:#f5f5f5;cursor:default;resize:none;"
                    ><?= h($bukti_text) ?></textarea>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="rek-box">
        <div class="rek-col">
            <div class="col-title">Asesi :</div>
            <div style="font-size:13px;margin-bottom:6px;">
                <b>Nama :</b>
                <span style="color:#1a237e;"><?= h($nama_asesi_db) ?></span>
            </div>
            <input type="hidden" name="nama_asesi_ttd" value="<?= h($nama_asesi_db) ?>">
        </div>
        <div class="rek-col">
            <div class="col-title">Ditinjau Oleh Asesor :</div>
            <div style="font-size:13px;font-weight:bold;color:#1a237e;margin-bottom:4px;">
                <?= h($apl1['nama_asesor'] ?? '-') ?>
            </div>
            <div style="font-size:12px;color:#888;margin-bottom:8px;">
                No. Reg: <?= h($apl1['no_reg'] ?? '-') ?>
            </div>
            <div style="font-size:11px;color:#888;font-style:italic;">
                Rekomendasi Dapat/Tidak Dapat diisi Asesor setelah Anda menyimpan.
            </div>
        </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:20px;">
        <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">Kembali</a>
        <button type="submit" class="btn-submit">SIMPAN</button>
    </div>
</form>

<?php elseif ($is_asesi && $apl2_selesai_asesi && !$mode_melihat): ?>
<script>
window.location.href = '../BERANDA/UTAMA.php?page=../FR_APL/FR_APL02.php&view=1&id_asesi=<?= $id_asesi ?>';
</script>
<?php else: ?>
<div style="text-align:center;padding:24px;color:#666;">
    Data tidak tersedia atau mode tidak dikenali.
    <br><br>
    <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">Kembali</a>
</div>
<?php endif; ?>

</div>
</body>
</html> 