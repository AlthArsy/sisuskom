<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
require_once __DIR__ . '/fr_apl_helpers.php';

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesi', 'Asesor', 'Admin_lsp', 'Admin_utm'])) {
    header('Location: ../LOGIN/login.php');
    exit;
}


function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


$is_asesi = (isset($_SESSION['role']) && $_SESSION['role'] === 'Asesi');
$is_admin_lsp = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin_lsp');
$is_admin_utm = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin_utm');
$is_asesor = (isset($_SESSION['role']) && $_SESSION['role'] === 'Asesor');
$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$mode_lihat = isset($_GET['view']) && (string) $_GET['view'] === '1';

$apl1 = null;
if ($id_asesi) {
    $apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.id_apl1, a.id_jadwal, dp.id_skema,
                s.judul_skema, s.nomor_skema, s.standar_kompetensi_kerja,
                as2.nama_asesor, as2.no_reg, as2.id_asesor,
                j.hari, j.tanggal, j.waktu, j.tuk
         FROM tb_apl1 a
         LEFT JOIN tb_jadwal j ON j.id_jadwal = a.id_jadwal
         LEFT JOIN tb_det_periode dp ON dp.id_det_periode = a.id_det_periode
         LEFT JOIN tb_skema s ON s.id_skema = COALESCE(j.id_skema, dp.id_skema)
         LEFT JOIN tb_asesor as2 ON as2.id_asesor = COALESCE(j.id_asesor, dp.id_asesor)
         WHERE a.id_asesi = '$id_asesi'
         ORDER BY a.id_apl1 DESC LIMIT 1"));
}

$id_apl1 = intval($apl1['id_apl1'] ?? 0);
$id_skema = intval($apl1['id_skema'] ?? 0);
$id_asesor_apl = intval($apl1['id_asesor'] ?? 0);
$jadwal_tuk = trim($apl1['tuk'] ?? '');
$jadwal_hari_tanggal = trim(($apl1['hari'] ?? '') . (($apl1['hari'] ?? '') && ($apl1['tanggal'] ?? '') ? ', ' : '') . ($apl1['tanggal'] ?? ''));
$jadwal_waktu = trim($apl1['waktu'] ?? '');

$nama_asesi_db = '';
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi = '$id_asesi' LIMIT 1"));
    $nama_asesi_db = $r['nama_asesi'] ?? '';
}

$ak01_exist = null;
if ($id_apl1) {
    $ak01_exist = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_ak01 WHERE id_asesi = '$id_asesi' ORDER BY id_ak01 DESC LIMIT 1"));
}

$detail_bukti_rows = [];
if ($ak01_exist) {
    $id_ak_q = intval($ak01_exist['id_ak01']);
    $rd = mysqli_query($koneksi,
        "SELECT bukti FROM detail_ak1 WHERE id_ak01 = '$id_ak_q' ORDER BY id_detail_ak1 ASC");
    while ($row = mysqli_fetch_assoc($rd)) {
        $detail_bukti_rows[] = $row['bukti'];
    }
}


$bukti_labels = [
    'Hasil Verifikasi Portofolio',
    'Hasil Reviu Produk',
    'Hasil Observasi Langsung',
    'Hasil Kegiatan Terstruktur',
    'Hasil Tanya Jawab',
    'Hasil Pertanyaan Tulis',
    'Hasil Pertanyaan Lisan',
    'Hasil Pertanyaan Wawancara',
];
$saved_bukti = [];
foreach ($detail_bukti_rows as $baris) {
    foreach (explode(', ', $baris) as $item) {
        $item = trim($item);
        if ($item !== '') $saved_bukti[] = $item;
    }
}
$saved_lainnya = '';
foreach ($saved_bukti as $b) {
    if (!in_array($b, $bukti_labels) && $b !== 'Bukti Lainnya') {
        $saved_lainnya = $b;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan_ak01') {
    if (!$is_asesi) {
        echo "<script>alert('Hanya Asesi yang dapat mengisi FR.AK.01.'); history.back();</script>";
        exit;
    }
    if (!$id_asesi || mysqli_num_rows(mysqli_query($koneksi, "SELECT id_asesi FROM tb_asesi WHERE id_asesi = '$id_asesi'")) == 0) {
        echo "<script>alert('Error: ID Asesi tidak valid!'); history.back();</script>";
        exit;
    }

    $tuk = $jadwal_tuk;
    $hari_tanggal = trim((string) ($apl1['tanggal'] ?? ''));
    $waktu = $jadwal_waktu;
    $tuk_pelaksanaan = $jadwal_tuk;

    $bukti_parts = [];
    if (!empty($_POST['bukti']) && is_array($_POST['bukti'])) {
        foreach ($_POST['bukti'] as $b) {
            $b = trim((string) $b);
            if ($b !== '') { $bukti_parts[] = $b; }
        }
    }
    $bukti = implode(', ', $bukti_parts);

    if ($id_skema && $id_asesor_apl && $bukti !== '' && $tuk !== '') {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);
        
        $hari_sql = $hari_tanggal !== '' ? "'" . $e($hari_tanggal) . "'" : 'NULL';
        $sql1 = "INSERT INTO tb_ak01 
                (id_apl1, id_asesi, id_asesor, hari_tanggal, waktu, tuk_pelaksanaan) 
                VALUES 
                ('$id_apl1', '$id_asesi', '$id_asesor_apl',
                $hari_sql, '" . $e($waktu) . "', '" . $e($tuk_pelaksanaan) . "')";

        $res1 = mysqli_query($koneksi, $sql1);
        
        if ($res1) {
            $id_ak01_new = mysqli_insert_id($koneksi);
            $sql2 = "INSERT INTO detail_ak1 (id_ak01, bukti) VALUES ('$id_ak01_new', '" . $e($bukti) . "')";
            $res2 = mysqli_query($koneksi, $sql2);
            
            if ($res2) {
                fr_apl_ensure_ia01_stub($koneksi, $id_asesi, $id_apl1, $id_ak01_new, $id_asesor_apl);
                echo "<script>alert('FR.AK.01 berhasil disimpan!'); window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            }
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}

if ($ak01_exist && !$mode_lihat) {
    $id_q = (int) $id_asesi;
    if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'UTAMA.php') {
        header('Location: ../FR_APL/FR_AK01.php?id_asesi=' . $id_q . '&view=1');
    } else {
        header('Location: FR_AK01.php?id_asesi=' . $id_q . '&view=1');
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/fapl2.css">
    <title>FR.AK.01 Persetujuan Asesmen</title>
</head>
<body>
<div class="form-box">

<h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
    FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN
</h2>

<?php if (!$apl1): ?>
<div class="AA">
    Formulir APL 1 belum diisi. Silakan isi APL 1 terlebih dahulu.<br><br>
    <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">← Kembali</a>
</div>

<?php elseif ($mode_lihat && $ak01_exist): ?>
    <p style="font-size:12px; color:#555; margin:10px 0 16px; text-align:center;">
        Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen.
    </p>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin-bottom:14px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi <span style="font-size:12px;color:#555;">(KKNI/Okupasi/Klaster)</span>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Judul</label>
                <input type="text" class="form-control" value="<?= h($apl1['judul_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Nomor</label>
                <input type="text" class="form-control" value="<?= h($apl1['nomor_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:14px; display:flex; gap:14px; flex-wrap:wrap;">
        <div style="flex:1; min-width:140px;">
            <label class="label">TUK</label>
            <input type="text" class="form-control" value="<?= h($jadwal_tuk ?: ($ak01_exist['tuk_pelaksanaan'] ?? '')) ?>"
                   readonly style="background:#f5f5f5;">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="label">Nama Asesor</label>
            <input type="text" class="form-control" value="<?= h($apl1['nama_asesor'] ?? '-') ?>"
                   readonly style="background:#f5f5f5;">
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <label class="label">Nama Asesi</label>
        <input type="text" class="form-control" value="<?= h($nama_asesi_db) ?>"
               readonly style="background:#f5f5f5;">
    </div>

    <div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
    Bukti yang akan dikumpulkan</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px 20px; margin-top:8px;">
        <?php foreach ($bukti_labels as $lbl): ?>
        <label style="display:flex; align-items:center; gap:7px; font-size:13px;">
            <input type="checkbox"
                   <?= in_array($lbl, $saved_bukti) ? 'checked' : '' ?>
                   disabled
                   style="width:16px; height:16px;">
            <?= h($lbl) ?>
        </label>
        <?php endforeach; ?>

        <div style="grid-column:1/-1; display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:4px;">
            <label style="display:flex; align-items:center; gap:7px; font-size:13px; white-space:nowrap;">
                <input type="checkbox"
                       <?= ($saved_lainnya !== '') ? 'checked' : '' ?>
                       disabled
                       style="width:16px; height:16px;">
                Lainnya :
            </label>
            <input type="text" class="form-control"
                   value="<?= h($saved_lainnya) ?>"
                   readonly
                   style="max-width:320px; flex:1; min-width:120px; background:#f5f5f5;">
        </div>
    </div>
    <div class="section-title" style="font-weight:bold;font-size:14px;border-left:4px solid #4A7AFF;padding-left:8px;margin:20px 0 10px;">
        Pelaksanaan asesmen disepakati pada</div>
    <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:8px;">
        <div style="flex:1; min-width:140px;">
            <label class="small-text">Hari / Tanggal</label>
            <input type="text" class="form-control" value="<?= h($jadwal_hari_tanggal ?: ($ak01_exist['hari_tanggal'] ?? '')) ?>"
                   readonly style="background:#f5f5f5;">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="small-text">Waktu</label>
            <input type="text" class="form-control" value="<?= h($ak01_exist['waktu'] ?? '') ?>"
                   readonly style="background:#f5f5f5;">
        </div>
    </div>
    <div style="margin-bottom:14px;">
        <label class="small-text">TUK pelaksanaan (nama / alamat)</label>
        <input type="text" class="form-control" value="<?= h($ak01_exist['tuk_pelaksanaan'] ?? '') ?>"
               readonly style="background:#f5f5f5;">
    </div> 


    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 16px 0 8px;">Asesor</div>
    <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:5px; padding:10px 14px; font-size:12px; color:#555; line-height:1.6;">
        Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor
        dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang
        sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
    </div>

    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 16px 0 8px;">Asesi</div>
    <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:5px; padding:10px 14px; font-size:12px; color:#555; line-height:1.6;">
        Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan
        untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
    </div>

    <?php if ($is_asesi): ?>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">Kembali</a>
    </div>
    <?php endif; ?>
    <?php if ($is_admin_lsp || $is_admin_utm): ?>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <a href="../BERANDA/UTAMA.php?page=../list/rekap_ak01.php" class="btn-back">Kembali</a>
        <a href="../pdf/cetak_ak1.php?id_asesi=<?= $id_asesi ?>" 
           target="_blank" 
           class="btn-submit" 
           style="background:#1a237e;text-decoration:none;">
           Cetak PDF
        </a>
    </div>
    <?php endif; ?>
    <?php if ($is_asesor): ?>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <a href="../BERANDA/UTAMA.php?page=../list/rekap_ak01.php" class="btn-back">Kembali</a>
        <a href="../pdf/cetak_ak1.php?id_asesi=<?= $id_asesi ?>" 
           target="_blank" 
           class="btn-submit" 
           style="background:#1a237e;text-decoration:none;">
           Cetak PDF
        </a>
    </div>
    <?php endif; ?>
    

<?php elseif ($mode_lihat && !$ak01_exist): ?>
<div style="text-align:center; padding:24px; color:#555;">
    Data FR.AK.01 belum ada.<br><br>
    <a href="FR_AK01.php?id_asesi=<?= (int) $id_asesi ?>" class="btn-submit" style="display:inline-block;text-decoration:none;padding:8px 18px;">Isi formulir</a>
    <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back" style="display:inline-block;text-decoration:none;margin-left:8px;">Kembali</a>
</div>

<?php elseif (!$ak01_exist): ?>
<form method="post" autocomplete="off">
    <input type="hidden" name="aksi" value="simpan_ak01">

    <p style="font-size:12px; color:#555; margin:10px 0 16px; text-align:center;">
        Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen.
    </p>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin-bottom:14px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi <span style="font-size:12px;color:#555;">(KKNI/Okupasi/Klaster)</span>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Judul</label>
                <input type="text" class="form-control" value="<?= h($apl1['judul_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Nomor</label>
                <input type="text" class="form-control" value="<?= h($apl1['nomor_skema']) ?>"
                       readonly style="background:#f5f5f5;">
            </div>
        </div>
    </div>

    <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
        <div style="flex:1; min-width:140px;">
            <label class="label">TUK</label>
            <input type="text" class="form-control" value="<?= h($jadwal_tuk ?: '-') ?>" readonly style="background:#f5f5f5;">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="label">Nama Asesor</label>
            <input type="text" class="form-control"
                   value="<?= h($apl1['nama_asesor'] ?: '(belum diatur)') ?>"
                   readonly style="background:#f5f5f5;">
            <?php if (!$id_asesor_apl): ?>
            <div style="font-size:12px;color:#c00;margin-top:6px;">Jadwal APL1 ini belum memiliki asesor.</div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <label class="label">Nama Asesi <span style="color:red;">*</span></label>
        <input type="text" class="form-control" value="<?= h($nama_asesi_db) ?>"
               readonly style="background:#f5f5f5;">
    </div>

    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 20px 0 10px;">
        Bukti yang akan dikumpulkan <span style="color:red;font-weight:normal;">*</span></div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; margin-top: 8px;">
        <?php foreach ($bukti_labels as $lbl): ?>
        <label style="display:flex; align-items:center; gap:7px; font-size:13px;">
            <input type="checkbox" name="bukti[]" value="<?= h($lbl) ?>"
                   style="width:16px; height:16px; cursor:pointer;">
            <?= h($lbl) ?>
        </label>
        <?php endforeach; ?>
        <div style="grid-column:1/-1; display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:4px;">
            <label style="display:flex; align-items:center; gap:7px; font-size:13px; white-space:nowrap;">
                <input type="checkbox" name="bukti[]" value="Bukti Lainnya" style="width:16px; height:16px;">
                Lainnya :
            </label>
            <input type="text" name="bukti[]" class="form-control" placeholder="Sebutkan..."
                   style="max-width:320px; flex:1; min-width:120px;">
        </div>
    </div>

    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 20px 0 10px;">
        Pelaksanaan asesmen disepakati pada</div>
    <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:6px;">
        <div style="flex:1; min-width:140px;">
            <label class="small-text">Hari / Tanggal</label>
            <input type="text" class="form-control" value="<?= h($jadwal_hari_tanggal ?: '-') ?>" readonly style="background:#f5f5f5;">
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="small-text">Waktu</label>
            <input type="text" class="form-control" value="<?= h($jadwal_waktu ?: '-') ?>" readonly style="background:#f5f5f5;">
        </div>
    </div>
    <div style="margin-bottom:14px;">
        <label class="small-text">TUK pelaksanaan (nama / alamat)</label>
        <input type="text" class="form-control" value="<?= h($jadwal_tuk ?: '-') ?>" readonly style="background:#f5f5f5;">
    </div>

    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 16px 0 8px;">Asesor</div>
    <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:5px; padding:10px 14px; font-size:12px; color:#555; line-height:1.6;">
        Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor
        dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang
        sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
    </div>

    <div style="font-weight:bold; font-size:14px; border-left: 4px solid #4A7AFF; padding-left: 8px; margin: 16px 0 8px;">Asesi</div>
    <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:5px; padding:10px 14px; font-size:12px; color:#555; line-height:1.6;">
        Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan
        untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <a href="../BERANDA/UTAMA.php?page=../list/list_form.php" class="btn-back">← Kembali</a>
        <button type="submit" class="btn-submit"<?= (!$id_asesor_apl || !$jadwal_tuk) ? ' disabled' : '' ?>>SIMPAN</button>
        
    </div>
    
    <?php if (!$id_asesor_apl || !$jadwal_tuk): ?>
    <p style="font-size:12px;color:#c00;margin-top:8px;">Tombol simpan dinonaktifkan sampai APL1 memiliki jadwal dan asesor yang lengkap.</p>
    <?php endif; ?>
</form>

<?php endif; ?>

</div>
</body>
</html>
