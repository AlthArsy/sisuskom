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

$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$is_asesi = ($role === 'Asesi');

$nama_asesi_db = '';
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
    $nama_asesi_db = $r['nama_asesi'] ?? '';
}

//(sesuai PDF)
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
    $id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
    $tuk = isset($_POST['tuk']) ? trim($_POST['tuk']) : '';
    $tgl_mulai = isset($_POST['tgl_mulai']) ? trim($_POST['tgl_mulai']) : '';
    $tgl_selesai = isset($_POST['tgl_selesai']) ? trim($_POST['tgl_selesai']) : '';
    $catatan_lain = isset($_POST['catatan_lainnya']) ? trim($_POST['catatan_lainnya']) : '';
    $nama_asesi = isset($_POST['nama_asesi']) ? trim($_POST['nama_asesi']) : '';
    $tanggal_asesi = isset($_POST['tanggal_asesi']) ? trim($_POST['tanggal_asesi']) : '';
    $ttd_asesi_qr = isset($_POST['ttd_asesi_qr']) ? trim($_POST['ttd_asesi_qr']) : '';
    $nama_asesor = isset($_POST['nama_asesor']) ? trim($_POST['nama_asesor']) : '';
    $no_reg_asesor = isset($_POST['no_reg_asesor']) ? trim($_POST['no_reg_asesor']) : '';
    $jawaban = isset($_POST['jawaban']) ? $_POST['jawaban'] : [];
    $catatan_komp = isset($_POST['catatan_komp']) ? $_POST['catatan_komp'] : [];

    if ($id_skema && $tuk && $id_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_ak03
            (id_asesi, id_skema, tuk, tgl_mulai, tgl_selesai, catatan_lainnya,
             nama_asesi, tanggal_asesi, ttd_asesi_qr, nama_asesor, no_reg_asesor)
            VALUES (
                '$id_asesi','$id_skema','{$e($tuk)}',
                " . ($tgl_mulai ? "'{$e($tgl_mulai)}'" : "NULL") . ",
                " . ($tgl_selesai ? "'{$e($tgl_selesai)}'" : "NULL") . ",
                " . ($catatan_lain ? "'{$e($catatan_lain)}'" : "NULL") . ",
                '{$e($nama_asesi)}',
                " . ($tanggal_asesi ? "'{$e($tanggal_asesi)}'" : "NULL") . ",
                '{$e($ttd_asesi_qr)}',
                '{$e($nama_asesor)}','{$e($no_reg_asesor)}'
            )";
        $res = mysqli_query($koneksi, $sql);

        if (!$res) {
            echo "<script>alert('Gagal simpan!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
        } else {
            $id_ak03 = mysqli_insert_id($koneksi);

            foreach ($komponen as $no => $teks) {
                $jwb  = isset($jawaban[$no])      ? mysqli_real_escape_string($koneksi, $jawaban[$no])      : '';
                $cat  = isset($catatan_komp[$no]) ? mysqli_real_escape_string($koneksi, $catatan_komp[$no]) : '';
                mysqli_query($koneksi,
                    "INSERT INTO tb_ak03_umpan (id_ak03, no_komponen, jawaban, catatan)
                     VALUES ('$id_ak03','$no',
                             " . ($jwb ? "'$jwb'" : "NULL") . ",
                             " . ($cat ? "'$cat'" : "NULL") . ")");
            }

            echo "<script>alert('FR.AK.03 berhasil disimpan!');
                          window.location.href='../BERANDA/UTAMA.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih skema dan TUK terlebih dahulu!');</script>";
    }
}

$dsb_asesi = $is_asesi ? '' : 'disabled';
$dsb_style_asesi = $is_asesi ? '' : 'pointer-events:none;opacity:0.75;background:#f5f5f5;';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.AK.03 Umpan Balik</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .tbl-ak03 { width:100%; border-collapse:collapse; font-size:13px; margin-top:8px; }
        .tbl-ak03 th {
            background:#cadbfc; padding:8px 10px;
            border:1px solid #b0bec5; text-align:center;
        }
        .tbl-ak03 td { padding:8px 10px; border:1px solid #ccc; vertical-align:middle; }
        .tbl-ak03 td:first-child { text-align:center; width:36px; }
        .tbl-ak03 td:nth-child(3),
        .tbl-ak03 td:nth-child(4) { text-align:center; width:60px; }
        .tbl-ak03 td:last-child { width:30%; }
        .tbl-ak03 .catatan-input {
            width:100%; min-height:36px; resize:vertical;
            border:1px solid #ccc; border-radius:3px;
            font-size:12px; padding:3px 5px; box-sizing:border-box;
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
        @media(max-width:768px){
            .form-box { margin:6vw auto; padding:14px 4vw; }
            .ttd-box { flex-direction:column; }
            .tbl-ak03 { font-size:11px; }
        }
    </style>
</head>
<body>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi" value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema" id="id_skema_hidden">
    <input type="hidden" name="nama_asesor" id="nama_asesor_hidden">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.AK.03. UMPAN BALIK DAN CATATAN ASESMEN
    </h2>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin:16px 0 14px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Skema Sertifikasi – Judul <span class="required">*</span></label>
                <div class="skema-wrap">
                    <input type="text" id="judul_skema" class="form-control"
                           placeholder="Ketik judul skema..." autocomplete="off"
                           oninput="searchSkema(this.value)" required>
                    <div class="skema-dropdown" id="skema-dropdown"></div>
                </div>
                <div class="skema-selected-badge" id="skema-badge"></div>
            </div>
            <div style="flex:1; min-width:120px;">
                <label class="small-text">Nomor</label>
                <input type="text" id="nomor_skema" class="form-control"
                       placeholder="Otomatis" readonly style="background:#f5f5f5;">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">TUK <span class="required">*</span></label>
                <select name="tuk" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="Sewaktu">Sewaktu</option>
                    <option value="Tempat Kerja">Tempat Kerja</option>
                    <option value="Mandiri">Mandiri</option>
                </select>
            </div>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Nama Asesor</label>
                <div id="asesor-nama"
                     style="padding:5px 8px; border:1px solid #e0e0e0; border-radius:4px;
                            background:#f5f5f5; font-size:14px; color:#1a237e; min-height:32px;">
                    — pilih skema dulu —
                </div>
            </div>
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Nama Asesi</label>
                <input type="text" name="nama_asesi" id="nama_asesi" class="form-control"
                       value="<?php echo htmlspecialchars($nama_asesi_db); ?>"
                       placeholder="Nama Asesi" oninput="scheduleQRAsesi()">
            </div>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control">
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
                    <th>No.</th>
                    <th>Komponen</th>
                    <th>Ya</th>
                    <th>Tidak</th>
                    <th>Catatan / Komentar Asesi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($komponen as $no => $teks): ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= htmlspecialchars($teks) ?></td>
                    <td>
                        <input type="radio" name="jawaban[<?= $no ?>]" value="Ya">
                    </td>
                    <td>
                        <input type="radio" name="jawaban[<?= $no ?>]" value="Tidak">
                    </td>
                    <td>
                        <textarea class="catatan-input"
                                  name="catatan_komp[<?= $no ?>]"
                                  placeholder="Komentar..."></textarea>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:14px;">
        <label class="small-text">Catatan  :</label>
        <textarea name="catatan_lainnya" class="form-control" rows="3"
                  placeholder="Tuliskan catatan lainnya..."></textarea>
    </div>

    <!-- <div class="section-title" style="margin:20px 0 8px;"></div>
    <div class="ttd-box">
        <div class="ttd-col">
            <div class="col-title">Asesi</div>
            <div style="font-size:13px; margin-bottom:6px;">
                <b>Nama :</b>
                <span id="asesi-ttd-nama" style="color:#1a237e;">
                    <php echo htmlspecialchars($nama_asesi_db); ?>
                </span>
            </div>
            <div style="margin-bottom:8px;">
                <label class="small-text">Tanda tangan / Tanggal</label>
                <input type="date" name="tanggal_asesi" id="tanggal_asesi"
                       class="form-control" onchange="scheduleQRAsesi()">
            </div>
            <div class="qr-box">
                <div class="qr-title">QR Tanda Tangan Asesi</div>
                <div class="qr-canvas-wrap" id="qr-asesi-canvas">
                    <div class="qr-ph-sm" id="qr-asesi-ph">
                    <span>Isi nama dulu</span>
                    </div>
                </div>
                <div id="qr-asesi-badge" class="qr-badge">QR Siap</div><br>
                <button type="button" id="btn-dl-asesi" class="btn-dl-qr"
                        onclick="dlQRAsesi()">⬇ Download</button>
            </div>
        </div>

        <div class="ttd-col">
            <div class="col-title">Asesor</div>
            <div style="font-size:13px; margin-bottom:4px;">
                <b>Nama :</b> <span id="asesor-ttd-nama" style="color:#1a237e;">— pilih skema —</span>
            </div>
            <div style="font-size:11px; color:#888;">
                No. Reg: <span id="asesor-ttd-noreg">-</span>
            </div>
            <div style="font-size:11px; color:#888; margin-top:10px; font-style:italic;">
                * Asesor terisi otomatis dari skema yang dipilih
            </div> 
        </div>
    </div> -->

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
             Kembali
        </button>
        <button type="submit" class="btn-submit" onclick="return prepareQR()">SIMPAN ✓</button>
    </div>
</form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="../assets/JS/lsp_common.js"></script>
<script src="../assets/JS/fr_ak03.js"></script>
<script>
    var ID_ASESI = <?php echo $id_asesi; ?>;
</script>
</body>
</html>