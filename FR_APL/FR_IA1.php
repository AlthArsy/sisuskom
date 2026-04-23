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

$role     = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$is_asesi = ($role === 'Asesi'); 

$nama_asesi_db = '';
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi='$id_asesi' LIMIT 1"));
    $nama_asesi_db = $r['nama_asesi'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
    $tuk = isset($_POST['tuk']) ? trim($_POST['tuk']) : '';
    $tanggal = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';
    $rekomendasi = isset($_POST['rekomendasi']) ? trim($_POST['rekomendasi']) : '';
    $umpan_balik = isset($_POST['umpan_balik']) ? trim($_POST['umpan_balik']) : '';
    $rek_kelompok = isset($_POST['rek_kelompok']) ? trim($_POST['rek_kelompok']) : '';
    $rek_unit = isset($_POST['rek_unit']) ? trim($_POST['rek_unit']) : '';
    $rek_elemen = isset($_POST['rek_elemen']) ? trim($_POST['rek_elemen']) : '';
    $rek_kuk = isset($_POST['rek_kuk']) ? trim($_POST['rek_kuk']) : '';
    $nama_asesi = isset($_POST['nama_asesi']) ? trim($_POST['nama_asesi']) : '';
    $tanggal_asesi = isset($_POST['tanggal_asesi']) ? trim($_POST['tanggal_asesi']) : '';
    $ttd_asesi_qr = isset($_POST['ttd_asesi_qr']) ? trim($_POST['ttd_asesi_qr']) : '';
    $nama_asesor = isset($_POST['nama_asesor']) ? trim($_POST['nama_asesor']) : '';
    $no_reg_asesor = isset($_POST['no_reg_asesor']) ? trim($_POST['no_reg_asesor']) : '';
    $tanggal_asesor = isset($_POST['tanggal_asesor']) ? trim($_POST['tanggal_asesor']) : '';
    $ttd_asesor_qr = isset($_POST['ttd_asesor_qr']) ? trim($_POST['ttd_asesor_qr']) : '';
    $standar = isset($_POST['standar']) ? $_POST['standar'] : [];
    $pencapaian = isset($_POST['pencapaian']) ? $_POST['pencapaian'] : [];
    $penilaian_lanjut= isset($_POST['penilaian_lanjut'])? $_POST['penilaian_lanjut'] : [];
 
    if ($id_skema && $tuk && $id_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_ia01
            (id_asesi, id_skema, tuk, tanggal, rekomendasi, umpan_balik,
             rek_kelompok, rek_unit, rek_elemen, rek_kuk,
             nama_asesi, tanggal_asesi, ttd_asesi_qr,
             nama_asesor, no_reg_asesor, tanggal_asesor, ttd_asesor_qr)
            VALUES (
                '$id_asesi','$id_skema','{$e($tuk)}',
                " . ($tanggal ? "'{$e($tanggal)}'" : "NULL") . ",
                " . ($rekomendasi ? "'{$e($rekomendasi)}'" : "NULL") . ",
                " . ($umpan_balik ? "'{$e($umpan_balik)}'" : "NULL") . ",
                '{$e($rek_kelompok)}','{$e($rek_unit)}','{$e($rek_elemen)}','{$e($rek_kuk)}',
                '{$e($nama_asesi)}',
                " . ($tanggal_asesi ? "'{$e($tanggal_asesi)}'" : "NULL") . ",
                '{$e($ttd_asesi_qr)}',
                '{$e($nama_asesor)}','{$e($no_reg_asesor)}',
                " . ($tanggal_asesor ? "'{$e($tanggal_asesor)}'" : "NULL") . ",
                '{$e($ttd_asesor_qr)}'
            )";
        $res = mysqli_query($koneksi, $sql);

        if (!$res) {
            echo "<script>alert('Gagal simpan!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
        } else {
            $id_ia01 = mysqli_insert_id($koneksi);
            foreach ($pencapaian as $id_kuk => $penc) {
                $id_kuk_i = intval($id_kuk);
                $penc_esc = mysqli_real_escape_string($koneksi, $penc);
                $std_esc  = mysqli_real_escape_string($koneksi, $standar[$id_kuk] ?? '');
                $pnl_esc  = mysqli_real_escape_string($koneksi, $penilaian_lanjut[$id_kuk] ?? '');
                mysqli_query($koneksi,
                    "INSERT INTO tb_jawaban_ia01 (id_ia01, id_kuk, standar_industri, pencapaian, penilaian_lanjut)
                     VALUES ('$id_ia01','$id_kuk_i','$std_esc','$penc_esc','$pnl_esc')");
            }
            echo "<script>alert('FR.IA.01 berhasil disimpan!');
                          window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih skema dan TUK terlebih dahulu!');</script>";
    }
}

$dsb       = $is_asesi ? 'disabled' : '';
$dsb_style = $is_asesi ? 'pointer-events:none;opacity:0.75;background:#f5f5f5;' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.IA.01 Ceklis Observasi</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/JS/lsp_common.js"></script>
    <script src="../assets/JS/fr_ia01.js"></script>

    <style>
        body { font-family: Arial, sans-serif; }
        .form-box {
            margin:35px auto; background:#fff;
            border:1px solid #ddd; border-radius:6px;
            padding:25px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.05);
        }
        .form-control {
            width:99%; padding:5px 7px; border:1px solid #ccc;
            border-radius:4px; box-sizing:border-box; font-size:14px;
        }
        .btn-submit {
            background:#4A7AFF; color:#fff; border:none;
            padding:8px 22px; border-radius:4px; font-size:15px; cursor:pointer;
        }
        .btn-submit:hover { background:#325fd6; }
        .btn-back {
            background:#888; color:#fff; border:none;
            padding:8px 22px; border-radius:4px; font-size:15px; cursor:pointer; margin-right:8px;
        }
        .btn-back:hover { background:#666; }
        .label { font-weight:bold; }
        .required { color:red; font-weight:normal; }
        .small-text { font-size:12px; color:#444; }
        .section-title {
            font-weight:bold; font-size:14px;
            border-left:4px solid #4A7AFF;
            padding-left:8px; margin:20px 0 10px;
        }

        .skema-wrap { position:relative; }
        .skema-dropdown {
            position:absolute; top:100%; left:0; right:0;
            background:#fff; border:1px solid #4A7AFF;
            border-radius:0 0 5px 5px; max-height:200px;
            overflow-y:auto; z-index:999; display:none;
            box-shadow:0 4px 12px rgba(0,0,0,0.12);
        }
        .skema-item { padding:9px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #eef; }
        .skema-item:hover { background:#eef3ff; }
        .skema-item .sk-judul { font-weight:bold; color:#1a237e; }
        .skema-item .sk-nomor { font-size:11px; color:#777; }

        .panduan-box {
            background:#fffde7; border:1px solid #ffe082;
            border-radius:5px; padding:10px 14px;
            font-size:12px; margin-bottom:16px;
        }
        .panduan-box ul { margin:6px 0 0 16px; padding:0; }

        .tbl-units { width:100%; border-collapse:collapse; font-size:13px; margin-bottom:18px; }
        .tbl-units th { background:#cadbfc; padding:7px 10px; border:1px solid #b0bec5; text-align:center; }
        .tbl-units td { padding:6px 10px; border:1px solid #ccc; vertical-align:top; }
        .tbl-units td:first-child { text-align:center; width:40px; }

        .unit-obs-box {
            border:1px solid #b0bec5; border-radius:6px;
            margin-bottom:20px; overflow:hidden;
        }
        .unit-obs-header {
            background:#cadbfc; padding:10px 14px;
            font-weight:bold; font-size:14px;
        }
        .unit-obs-header .unit-sub { font-size:12px; font-weight:normal; color:#333; }

        .tbl-obs { width:100%; border-collapse:collapse; font-size:12px; }
        .tbl-obs th {
            background:#dce8ff; padding:7px 8px;
            border:1px solid #b0c4de; text-align:center; font-size:12px;
        }
        .tbl-obs td { padding:6px 8px; border:1px solid #d0d8e8; vertical-align:middle; }
        .tbl-obs .elemen-row td {
            background:#eef4ff; font-weight:bold;
            font-size:12px; padding:5px 8px;
        }
        .tbl-obs .kuk-text { font-size:12px; color:#333; }

        .radio-yt { display:flex; justify-content:center; gap:8px; }
        .radio-yt label { font-size:12px; cursor:pointer; white-space:nowrap; }

        .obs-input {
            width:100%; min-height:36px; resize:vertical;
            border:1px solid #ccc; border-radius:3px;
            font-size:11px; padding:3px 5px; box-sizing:border-box;
        }

        .placeholder-box {
            text-align:center; padding:24px; color:#aaa;
            font-size:13px; border:1px dashed #ccc;
            border-radius:5px; margin:10px 0;
        }

        .rek-box {
            border:1px solid #ccc; border-radius:5px;
            padding:14px; background:#fafbff; margin-top:16px;
        }
        .rek-grid { display:flex; gap:16px; flex-wrap:wrap; margin-top:14px; }
        .rek-col { flex:1; min-width:220px; }
        .rek-col .col-title {
            font-weight:bold; font-size:14px;
            border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:10px;
        }

        .qr-box {
            border:2px dashed #4A7AFF; border-radius:8px;
            padding:10px 8px; background:#f4f7ff;
            text-align:center; margin-top:10px;
        }
        .qr-box .qr-title { font-size:11px; font-weight:bold; color:#1a237e; margin-bottom:6px; }
        .qr-canvas-wrap {
            display:flex; justify-content:center; align-items:center;
            min-height:88px; margin-bottom:4px;
        }
        .qr-canvas-wrap canvas, .qr-canvas-wrap img {
            border:2px solid #4A7AFF; border-radius:5px;
            padding:4px; background:#fff;
        }
        .qr-ph-sm {
            width:90px; height:90px; border:2px dashed #aac; border-radius:5px;
            display:flex; align-items:center; justify-content:center;
            color:#bbb; font-size:11px; background:#fff;
            flex-direction:column; gap:3px; margin:0 auto;
        }
        .qr-badge {
            display:none; background:#e6f4ea; color:#2e7d32;
            border-radius:20px; padding:2px 8px; font-size:10px; font-weight:bold;
        }
        .btn-dl-qr {
            display:none; font-size:11px; background:#4A7AFF; color:#fff;
            border:none; padding:3px 10px; border-radius:20px;
            cursor:pointer; margin-top:4px;
        }
        .btn-dl-qr:hover { background:#325fd6; }

        @media(max-width:768px){
            .form-box { margin:6vw auto; padding:14px 4vw; }
            h2 { font-size:17px; }
            .btn-submit,.btn-back { width:48%; padding:10px; }
            .tbl-obs { font-size:11px; }
            .rek-grid { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi"      value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema"      id="id_skema_hidden">
    <input type="hidden" name="nama_asesor"   id="nama_asesor_hidden">
    <input type="hidden" name="no_reg_asesor" id="no_reg_asesor_hidden">
    <input type="hidden" name="ttd_asesi_qr"  id="ttd_asesi_qr_input">
    <input type="hidden" name="ttd_asesor_qr" id="ttd_asesor_qr_input">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.IA.01. CL – CEKLIS OBSERVASI AKTIVITAS<br>
        <span style="font-size:14px; font-weight:normal;">DI TEMPAT KERJA ATAU TEMPAT KERJA SIMULASI</span>
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
            </div>
            <div style="flex:1; min-width:130px;">
                <label class="small-text">Nomor</label>
                <input type="text" id="nomor_skema" class="form-control"
                       placeholder="Otomatis" readonly style="background:#f5f5f5;">
            </div>
            <div style="flex:1; min-width:150px;">
                <label class="small-text">TUK <span class="required">*</span></label>
                <select name="tuk" class="form-control" required <?= $dsb ?> style="<?= $dsb_style ?>">
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
                <div id="asesor-display"
                     style="padding:5px 8px; border:1px solid #e0e0e0; border-radius:4px;
                            background:#f5f5f5; font-size:14px; color:#1a237e; min-height:32px;">
                    — pilih skema dulu —
                </div>
            </div>
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Nama Asesi</label>
                <input type="text" name="nama_asesi" id="nama_asesi" class="form-control"
                       placeholder="Nama Asesi"
                       value="<?php echo htmlspecialchars($nama_asesi_db); ?>"
                       oninput="scheduleQRAsesi()">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       <?= $dsb ?> style="<?= $dsb_style ?>">
            </div>
        </div>
    </div>

    <div class="panduan-box">
        <b>PANDUAN BAGI ASESOR</b>
        <ul>
            <li>Lengkapi nama unit kompetensi, elemen, dan KUK sesuai kolom dalam tabel.</li>
            <li>Isi standar industri atau tempat kerja.</li>
            <li>Centang <b>Ya</b> jika asesi dapat mendemonstrasikan tugas sesuai KUK, atau <b>Tidak</b> bila sebaliknya.</li>
            <li>Penilaian Lanjut diisi bila hasil belum dapat disimpulkan.</li>
        </ul>
    </div>

    <div id="unit-list-container">
        <div class="placeholder-box" id="unit-list-ph">
            Pilih skema untuk menampilkan daftar unit kompetensi
        </div>
        <div id="unit-list-wrap" style="display:none; overflow-x:auto;">
            <table class="tbl-units">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Unit</th>
                        <th>Judul Unit</th>
                    </tr>
                </thead>
                <tbody id="unit-list-tbody"></tbody>
            </table>
        </div>
    </div>

    <div id="obs-container"></div>

    <div id="rek-section" style="display:none;">
        <div class="section-title">Umpan Balik &amp; Rekomendasi</div>
        <div class="rek-box">
            <div>
                <label class="small-text">Umpan Balik untuk Asesi :</label>
                <textarea name="umpan_balik" class="form-control" rows="3"
                          placeholder="Tuliskan umpan balik..."
                          <?= $dsb ?> style="<?= $dsb_style ?>"></textarea>
            </div>
            <div style="margin-top:12px;">
                <label class="label" style="font-size:13px;">Rekomendasi :</label>
                <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:6px; <?= $dsb_style ?>">
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Kompeten" <?= $dsb ?>>
                        Asesi telah memenuhi seluruh KUK → <b>KOMPETEN</b>
                    </label>
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Belum Kompeten" <?= $dsb ?>>
                        Asesi belum memenuhi seluruh KUK → <b>BELUM KOMPETEN</b>
                    </label>
                </div>
            </div>
            <div id="rek-detail" style="display:none; margin-top:10px;">
                <div style="font-size:12px; color:#888; margin-bottom:6px;">
                    Pada: Kelompok Pekerjaan: ___ Unit: ___ Elemen: ___ KUK: ___
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:100px;">
                        <label class="small-text">Kelompok</label>
                        <input type="text" name="rek_kelompok" class="form-control" placeholder="Kelompok"
                               <?= $dsb ?> style="<?= $dsb_style ?>">
                    </div>
                    <div style="flex:1; min-width:100px;">
                        <label class="small-text">Unit</label>
                        <input type="text" name="rek_unit" class="form-control" placeholder="Unit"
                               <?= $dsb ?> style="<?= $dsb_style ?>">
                    </div>
                    <div style="flex:1; min-width:100px;">
                        <label class="small-text">Elemen</label>
                        <input type="text" name="rek_elemen" class="form-control" placeholder="Elemen"
                               <?= $dsb ?> style="<?= $dsb_style ?>">
                    </div>
                    <div style="flex:1; min-width:100px;">
                        <label class="small-text">KUK</label>
                        <input type="text" name="rek_kuk" class="form-control" placeholder="KUK"
                               <?= $dsb ?> style="<?= $dsb_style ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="rek-grid">
            <div class="rek-col">
                <div class="col-title">Asesi</div>
                <div style="font-size:13px; margin-bottom:6px;">
                    <b>Nama :</b>
                    <span id="asesi-ttd-nama" style="color:#1a237e;">
                        <?php echo htmlspecialchars($nama_asesi_db); ?>
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
                            <span style="font-size:18px;"></span><span>Isi nama dulu</span>
                        </div>
                    </div>
                    <div id="qr-asesi-badge" class="qr-badge">QR Siap</div><br>
                    <button type="button" id="btn-dl-asesi" class="btn-dl-qr"
                            onclick="downloadQR('qr-asesi-canvas','ttd_asesi')">⬇ Download</button>
                </div>
            </div>

            <div class="rek-col">
                <div class="col-title">Asesor</div>
                <div style="font-size:13px; margin-bottom:2px;">
                    <b>Nama :</b> <span id="asesor-ttd-nama" style="color:#1a237e;">— pilih skema —</span>
                </div>
                <div style="font-size:11px; color:#888; margin-bottom:8px;">
                    No. Reg: <span id="asesor-ttd-noreg">-</span>
                </div>
                <!-- <div style="margin-bottom:8px; <= $dsb_style ?>">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesor" id="tanggal_asesor"
                           class="form-control" onchange="scheduleQRAsesor()"
                           <= $dsb ?> style="<= $dsb_style ?>">
                </div> -->
<!-- `                <div class="qr-box" style="<= $dsb_style ?>">
                    <div class="qr-title">QR Tanda Tangan Asesor</div>
                    <div class="qr-canvas-wrap" id="qr-asesor-canvas">
                        <div class="qr-ph-sm" id="qr-asesor-ph">
                            <span style="font-size:18px;"></span><span>Pilih skema dulu</span>
                        </div>
                    </div>
                    <div id="qr-asesor-badge" class="qr-badge">QR Siap</div><br>
                    <button type="button" id="btn-dl-asesor" class="btn-dl-qr"
                            onclick="downloadQR('qr-asesor-canvas','ttd_asesor')">⬇ Download</button>
                </div>` -->
            </div>
        </div>
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
            Kembali
        </button>
        <button type="submit" class="btn-submit" onclick="return prepareQR()">SIMPAN ✓</button>
    </div>
</form>
</div>
</body>
</html>