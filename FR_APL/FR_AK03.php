<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";

$e = fn($s) => mysqli_real_escape_string($koneksi, (string)$s);
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

if (!isset($_SESSION['username'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

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

$data_ak03_saved   = null;
$detail_ak03_saved = [];
if ($id_asesi && $id_skema_db) {
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_ak03
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1_db'
         ORDER BY id_ak03 DESC LIMIT 1"));
    if ($cek) {
        $data_ak03_saved = $cek;
        $res_detail = mysqli_query($koneksi,
            "SELECT * FROM hasil_ak03 WHERE id_ak03 = '{$cek['id_ak03']}'");
        while ($d = mysqli_fetch_assoc($res_detail)) {
            $detail_ak03_saved[] = $d;
        }
    }
}

$has_data = false;
if ($id_asesi && $id_skema_db) {
    $chk = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) AS cnt FROM tb_ak03
         WHERE id_asesi='{$e($id_asesi)}' AND id_apl1='{$e($id_apl1_db)}'"));
    $has_data = ($chk && $chk['cnt'] > 0);
}
$mode = 'create';
if (isset($_GET['mode']) && in_array($_GET['mode'], ['view', 'create'])) {
    $mode = $_GET['mode'];
} elseif ($has_data) {
    $mode = 'view';
}

$is_view = isset($_GET['view']) && $_GET['view'] == '1' && $data_ak03_saved;

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

$komponen = [
    1  => 'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi.',
    2  => 'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya.',
    3  => 'Asesor memberikan kesempatan untuk mendiskusikan/menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen.',
    4  => 'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman yang saya miliki.',
    5  => 'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen.',
    6  => 'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen.',
    7  => 'Asesor memberikan umpan balik yang mendukung setelah asesmen serta tindak lanjutnya.',
    8  => 'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya.',
    9  => 'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen.',
    10 => 'Asesor menggunakan keterampilan komunikasi yang efektif selama asesmen.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // $tgl_mulai    = trim($_POST['tgl_mulai']       ?? '');
    $tgl_selesai  = trim($_POST['tgl_selesai']     ?? '');
    $catatan_lain = trim($_POST['catatan_lainnya'] ?? '');
    $jawaban      = $_POST['jawaban']              ?? [];
    $catatan_komp = $_POST['catatan_komp']         ?? [];

    if (!$id_asesi || !$id_skema_db || !$id_apl1_db) {
        $_SESSION['alert'] = 'Data APL-1 tidak ditemukan untuk asesi ini. Pastikan APL-1 sudah diisi terlebih dahulu.';
    } else {
        $e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);

        $sql = "INSERT INTO tb_ak03
                    (id_apl1, id_ak01, id_asesi, id_asesor,
                    tgl_selesai, catatan_lainnya)
                VALUES (
                    '$id_apl1_db',
                    '$id_ak01_db',
                    '$id_asesi',
                    '$id_asesor_db',
                    -- " . ($tgl_mulai   ? "'{$e($tgl_mulai)}'"   : "NULL") . ",
                    " . ($tgl_selesai ? "'{$e($tgl_selesai)}'" : "NULL") . ",
                    " . ($catatan_lain ? "'{$e($catatan_lain)}'" : "NULL") . "
                )";

        $res = mysqli_query($koneksi, $sql);

        if (!$res) {
            $_SESSION['alert'] = 'Gagal menyimpan header: ' . mysqli_error($koneksi);
        } else {
            $id_ak03 = mysqli_insert_id($koneksi);

            $gagal_detail = false;
            foreach ($komponen as $no => $teks) {
                $jwb = $e($jawaban[$no]      ?? '');
                $cat = $e($catatan_komp[$no] ?? '');
                $ins = mysqli_query($koneksi,
                    "INSERT INTO hasil_ak03 (id_ak03, hasil, komentar_asesi)
                     VALUES ('$id_ak03', '$jwb', '$cat')");
                if (!$ins) $gagal_detail = true;
            }

            if ($gagal_detail) {
                $_SESSION['alert'] = 'Header tersimpan, namun sebagian detail gagal: ' . mysqli_error($koneksi);
            } else {
                header("Location: ../BERANDA/UTAMA.php?page=../list/list_form.php&saved=ak03");
                exit;
            }
        }
    }
}
$role = $_SESSION['role'] ?? '';
$is_asesi       = ($role === 'Asesi');
$is_asesor      = ($role === 'Asesor' || $role === 'Admin_lsp' || $role === 'Admin_utm');

$dsb_untuk_asesi  = $is_asesi  ? '' : 'readonly';
$dsb_untuk_asesor = ($is_asesor) ? '' : 'disabled';
$dsb_style        = $is_asesi ? 'pointer-events:none; opacity:0.65;' : '';
$dsb              = $dsb_untuk_asesor;  
$lock_asesi       = $dsb_untuk_asesi;    

$tgl_form = $hari_tanggal_db ?: date('Y-m-d');
?>
<html lang="id">
<head>
    <title>FR.AK.03 Umpan Balik dan Catatan Asesmen</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .tbl-ak03 {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 8px;
        }
        .tbl-ak03 th {
            background: #cadbfc;
            padding: 8px 10px;
            border: 1px solid #b0bec5;
            text-align: center;
        }
        .tbl-ak03 td {
            padding: 8px 10px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }
        .tbl-ak03 td:first-child       { text-align: center; width: 36px; }
        .tbl-ak03 td:nth-child(3),
        .tbl-ak03 td:nth-child(4)      { text-align: center; width: 60px; }
        .tbl-ak03 td:last-child        { width: 30%; }
        .tbl-ak03 .catatan-input {
            width: 100%;
            min-height: 36px;
            resize: vertical;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 12px;
            padding: 3px 5px;
            box-sizing: border-box;
        }


        .field-readonly {
            padding: 6px 8px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #f5f5f5;
            font-size: 14px;
        }
        .field-readonly.empty { color: #999; font-style: italic; }


        .msg-box {
            padding: 10px 14px;
            border-radius: 5px;
            margin-bottom: 14px;
            font-size: 14px;
            font-weight: bold;
        }
        .msg-error   { background: #ffebee; color: #b71c1c; border: 1px solid #ef9a9a; }
        .msg-warning { background: #fff8e1; color: #e65100; border: 1px solid #ffe082; }

        @media (max-width: 600px) {
            .tbl-ak03 { font-size: 11px; }
        }
    </style>
</head>
<body>
<div class="form-box">
<?php if ($is_view): ?>

<h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0;
           border-radius:6px 6px 0 0; margin-top:0;">
    FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN
</h2>

<div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px;
            background:#fafbff; margin:16px 0 14px;">

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <div style="flex:2; min-width:180px;">
            <label class="small-text">Skema Sertifikasi – Judul</label>
            <div class="field-readonly <?= $judul_skema_db ? '' : 'empty' ?>">
                <?= $judul_skema_db ? htmlspecialchars($judul_skema_db) : '— APL-1 belum diisi —' ?>
            </div>
        </div>
        <div style="flex:1; min-width:120px;">
            <label class="small-text">Nomor Skema</label>
            <div class="field-readonly <?= $nomor_skema_db ? '' : 'empty' ?>">
                <?= $nomor_skema_db ? htmlspecialchars($nomor_skema_db) : '—' ?>
            </div>
        </div>
        <div style="flex:1; min-width:140px;">
            <label class="small-text">TUK</label>
            <div class="field-readonly <?= $tuk_db ? '' : 'empty' ?>">
                <?= $tuk_db ? htmlspecialchars($tuk_db) : '— AK-01 belum diisi —' ?>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
        <div style="flex:2; min-width:180px;">
            <label class="small-text">Nama Asesor</label>
            <div class="field-readonly <?= $nama_asesor_db ? '' : 'empty' ?>">
                <?= $nama_asesor_db ? htmlspecialchars($nama_asesor_db) : '— tidak ditemukan —' ?>
                <!-- <php if ($noreg_asesor_db): ?>
                    <span style="font-size:11px;color:#666;">&nbsp;(No.Reg: <?= htmlspecialchars($noreg_asesor_db) ?>)</span>
                <php endif; ?> -->
            </div>
        </div>
        <div style="flex:2; min-width:180px;">
            <label class="small-text">Nama Asesi</label>
            <div class="field-readonly <?= $nama_asesi_db ? '' : 'empty' ?>">
                <?= $nama_asesi_db ? htmlspecialchars($nama_asesi_db) : '—' ?>
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
            <div class="field-readonly">
                <?= htmlspecialchars($data_ak03_saved['tgl_selesai'] ?? '—') ?>
            </div>
        </div>
    </div>
</div>

<div class="section-title" style="margin:16px 0 8px;">
    Umpan Balik dari Asesi
    <span class="small-text" style="font-weight:normal;">
        (diisi oleh Asesi setelah pengambilan keputusan asesmen)
    </span>
</div>

<div style="overflow-x:auto;">
    <table class="tbl-ak03">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Komponen</th>
                <th colspan="2">Hasil</th>
                <th rowspan="2">Catatan / Komentar Asesi</th>
            </tr>
            <tr>
                <th>Ya</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($komponen as $no => $teks):
                $d   = $detail_ak03_saved[$no - 1] ?? [];
                $jwb = $d['hasil'] ?? '';
                $cat = $d['komentar_asesi'] ?? '';
            ?>
            <tr>
                <td><?= $no ?>.</td>
                <td><?= htmlspecialchars($teks) ?></td>
                <td style="text-align:center;">
                    <input type="radio" name="jwb_<?= $no ?>" value="Ya"
                           <?= $jwb === 'Ya' ? 'checked' : '' ?> disabled>
                </td>
                <td style="text-align:center;">
                    <input type="radio" name="jwb_<?= $no ?>" value="Tidak"
                           <?= $jwb === 'Tidak' ? 'checked' : '' ?> disabled>
                </td>
                <td>
                    <div class="field-readonly <?= $cat ? '' : 'empty' ?>"
                         style="min-height:36px; font-size:12px;">
                        <?= $cat ? htmlspecialchars($cat) : '—' ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div style="margin-top:14px;">
    <label class="small-text">Catatan :</label>
    <div class="field-readonly <?= ($data_ak03_saved['catatan_lainnya'] ?? '') ? '' : 'empty' ?>"
         style="min-height:60px;">
        <?= ($data_ak03_saved['catatan_lainnya'] ?? '')
            ? htmlspecialchars($data_ak03_saved['catatan_lainnya'])
            : '—' ?>
    </div>
</div>

<div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
    <?php if ($is_asesor): ?>
        <button type="button" class="btn-back"
                onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/rekap_ak3.php'">
            Kembali
        </button>
    <?php endif; ?>
    <?php if ($is_asesi): ?>
        <button type="button" class="btn-back"
                onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
            Kembali
        </button>
    <?php endif; ?>
</div>

<?php else: ?>

    <?php if (!empty($_SESSION['alert'])): ?>
    <script>alert('<?= addslashes($_SESSION['alert']) ?>');</script>
    <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    <form method="post" autocomplete="off">

        <input type="hidden" name="id_asesi"  value="<?= $id_asesi ?>">
        <input type="hidden" name="id_skema"  value="<?= $id_skema_db ?>">
        <input type="hidden" name="id_apl1"   value="<?= $id_apl1_db ?>">
        <input type="hidden" name="id_asesor" value="<?= $id_asesor_db ?>">

        <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0;
                   border-radius:6px 6px 0 0; margin-top:0;">
            FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN
        </h2>

        <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px;
                    background:#fafbff; margin:16px 0 14px;">

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
                    <label class="small-text">Nama Asesor
                    </label>
                    <div class="field-readonly <?= $nama_asesor_db ? '' : 'empty' ?>">
                        <?= $nama_asesor_db
                            ? htmlspecialchars($nama_asesor_db)
                            : '— tidak ditemukan —' ?>
                        <!-- <php if ($noreg_asesor_db): ?>
                            <span style="font-size:11px; color:#666;">
                                &nbsp;(No.Reg: <?= htmlspecialchars($noreg_asesor_db) ?>)
                            </span>
                        <php endif; ?> -->
                    </div>
                </div>

                <div style="flex:2; min-width:180px;">
                    <label class="small-text">Nama Asesi</label>
                    <div class="field-readonly <?= $nama_asesi_db ? '' : 'empty' ?>">
                        <?= $nama_asesi_db
                            ? htmlspecialchars($nama_asesi_db)
                            : '—' ?>
                    </div>
                </div>

            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">

                <div style="flex:1; min-width:140px;">
                    <label class="small-text">Tanggal Mulai</label>
                    <!-- <input type="date" name="tgl_mulai" class="form-control"
                           value="<= htmlspecialchars($_POST['tgl_mulai'] ?? '') ?>"> -->
                    <div class="field-readonly <?= $hari_tanggal_db ? '' : 'empty' ?>">
                        <?= $hari_tanggal_db
                            ? htmlspecialchars($hari_tanggal_db)
                            : '— AK-01 belum diisi —' ?>
                    </div>
                </div>

                <div style="flex:1; min-width:140px;">
                    <label class="small-text">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control"
                           value="<?= htmlspecialchars($_POST['tgl_selesai'] ?? '') ?>">
                </div>

            </div>
        </div>

        <div class="section-title" style="margin:16px 0 8px;">
            Umpan Balik dari Asesi
            <span class="small-text" style="font-weight:normal;">
                (diisi oleh Asesi setelah pengambilan keputusan asesmen)
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="tbl-ak03">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Komponen</th>
                        <th colspan="2">Hasil</th>
                        <th rowspan="2">Catatan / Komentar Asesi</th>
                    </tr>
                    <tr>
                        <th>Ya</th>
                        <th>Tidak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($komponen as $no => $teks): ?>
                    <?php
                        $val_jawaban = $_POST['jawaban'][$no]      ?? '';
                        $val_catatan = $_POST['catatan_komp'][$no] ?? '';
                    ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= htmlspecialchars($teks) ?></td>
                        <td>
                            <input type="radio"
                                   name="jawaban[<?= $no ?>]"
                                   value="Ya"
                                   <?= $val_jawaban === 'Ya' ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type="radio"
                                   name="jawaban[<?= $no ?>]"
                                   value="Tidak"
                                   <?= $val_jawaban === 'Tidak' ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <textarea class="catatan-input"
                                      name="catatan_komp[<?= $no ?>]"
                                      placeholder="Komentar..."><?= htmlspecialchars($val_catatan) ?></textarea>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <div style="margin-top:14px;">
            <label class="small-text">Catatan :</label>
            <textarea name="catatan_lainnya" class="form-control" rows="3"
                      placeholder="Tuliskan catatan lainnya..."><?=
                htmlspecialchars($_POST['catatan_lainnya'] ?? '')
            ?></textarea>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
            <a href="../BERANDA/UTAMA.php?page=../list/list_form.php"
               class="btn-back"
               style="text-decoration:none; display:inline-block;">
                Kembali
            </a>
            <button type="submit" class="btn-submit">SIMPAN ✓</button>
        </div>
        

    </form> 
<?php endif; ?>
</div>
</body>
</html>