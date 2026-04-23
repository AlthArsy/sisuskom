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
    $tgl_mulai = isset($_POST['tgl_mulai']) ? trim($_POST['tgl_mulai']) : '';
    $tgl_selesai = isset($_POST['tgl_selesai']) ? trim($_POST['tgl_selesai']) : '';
    $rekomendasi = isset($_POST['rekomendasi']) ? trim($_POST['rekomendasi']) : '';
    $tindak_lanjut = isset($_POST['tindak_lanjut']) ? trim($_POST['tindak_lanjut']) : '';
    $komentar_asesor  = isset($_POST['komentar_asesor']) ? trim($_POST['komentar_asesor']) : '';
    $nama_asesi = isset($_POST['nama_asesi']) ? trim($_POST['nama_asesi']) : '';
    $tanggal_asesi = isset($_POST['tanggal_asesi']) ? trim($_POST['tanggal_asesi']) : '';
    $ttd_asesi_qr = isset($_POST['ttd_asesi_qr']) ? trim($_POST['ttd_asesi_qr']) : '';
    $nama_asesor = isset($_POST['nama_asesor']) ? trim($_POST['nama_asesor']) : '';
    $no_reg_asesor = isset($_POST['no_reg_asesor']) ? trim($_POST['no_reg_asesor']) : '';
    $tanggal_asesor = isset($_POST['tanggal_asesor']) ? trim($_POST['tanggal_asesor']) : '';
    $ttd_asesor_qr = isset($_POST['ttd_asesor_qr']) ? trim($_POST['ttd_asesor_qr']) : '';
    $metode = isset($_POST['metode']) ? $_POST['metode'] : [];

    if ($id_skema && $tuk && $id_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_ak02
            (id_asesi, id_skema, tuk, tgl_mulai, tgl_selesai,
             rekomendasi, tindak_lanjut, komentar_asesor,
             nama_asesi, tanggal_asesi, ttd_asesi_qr,
             nama_asesor, no_reg_asesor, tanggal_asesor, ttd_asesor_qr)
            VALUES (
                '$id_asesi','$id_skema','{$e($tuk)}',
                " . ($tgl_mulai ? "'{$e($tgl_mulai)}'" : "NULL") . ",
                " . ($tgl_selesai ? "'{$e($tgl_selesai)}'" : "NULL") . ",
                " . ($rekomendasi ? "'{$e($rekomendasi)}'" : "NULL") . ",
                " . ($tindak_lanjut ? "'{$e($tindak_lanjut)}'"  : "NULL") . ",
                " . ($komentar_asesor? "'{$e($komentar_asesor)}'" : "NULL") . ",
                '{$e($nama_asesi)}',
                " . ($tanggal_asesi  ? "'{$e($tanggal_asesi)}'"  : "NULL") . ",
                '{$e($ttd_asesi_qr)}',
                '{$e($nama_asesor)}','{$e($no_reg_asesor)}',
                " . ($tanggal_asesor ? "'{$e($tanggal_asesor)}'" : "NULL") . ",
                '{$e($ttd_asesor_qr)}'
            )";
        $res = mysqli_query($koneksi, $sql);

        if (!$res) {
            echo "<script>alert('Gagal simpan!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
        } else {
            $id_ak02 = mysqli_insert_id($koneksi);

            foreach ($metode as $id_unit => $m) {
                $id_unit_i = intval($id_unit);
                $obs  = isset($m['obs_demonstrasi']) ? 1 : 0;
                $port = isset($m['portofolio']) ? 1 : 0;
                $pp3  = isset($m['pernyataan_pihak3']) ? 1 : 0;
                $pww  = isset($m['pertanyaan_wawancara']) ? 1 : 0;
                $pls  = isset($m['pertanyaan_lisan']) ? 1 : 0;
                $ptr  = isset($m['pertanyaan_tertulis']) ? 1 : 0;
                $prk  = isset($m['proyek_kerja']) ?1 : 0;
                $lain = isset($m['lainnya']) ? mysqli_real_escape_string($koneksi, $m['lainnya']) : '';

                mysqli_query($koneksi,
                    "INSERT INTO tb_ak02_metode
                     (id_ak02, id_unit, obs_demonstrasi, portofolio, pernyataan_pihak3,
                      pertanyaan_wawancara, pertanyaan_lisan, pertanyaan_tertulis,
                      proyek_kerja, lainnya)
                     VALUES ('$id_ak02','$id_unit_i','$obs','$port','$pp3',
                             '$pww','$pls','$ptr','$prk',
                             " . ($lain ? "'$lain'" : "NULL") . ")");
            }

            echo "<script>alert('FR.AK.02 berhasil disimpan!');
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
    <input type="hidden" name="nama_asesor"   id="nama_asesor_hidden">
    <input type="hidden" name="no_reg_asesor" id="no_reg_asesor_hidden">
    <input type="hidden" name="ttd_asesi_qr"  id="ttd_asesi_qr_input">
    <input type="hidden" name="ttd_asesor_qr" id="ttd_asesor_qr_input">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.AK.02. REKAMAN ASESMEN KOMPETENSI
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
                <input type="date" name="tgl_mulai" class="form-control"
                       <?= $dsb ?> style="<?= $dsb_style ?>">
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control"
                       <?= $dsb ?> style="<?= $dsb_style ?>">
            </div>
        </div>
    </div>

    <div class="section-title" style="margin:16px 0 8px;">
        Unit Kompetensi &amp; Metode Asesmen
    </div>
    <div class="placeholder-box" id="unit-placeholder">
        Pilih skema untuk menampilkan unit kompetensi
    </div>
    <div id="tabel-metode-wrap" style="display:none; overflow-x:auto;">
        <table class="tbl-ak02" id="tabel-metode">
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
            <tbody id="unit-tbody"></tbody>
        </table>
    </div>

    <div id="hasil-section" style="display:none;">
        <div class="section-title" style="margin:20px 0 8px;">Hasil Asesmen</div>
        <div class="hasil-box">
            <div style="margin-bottom:12px;">
                <label class="label" style="font-size:13px;">
                    Rekomendasi hasil asesmen <span class="required">*</span>
                </label>
                <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:6px; <?= $dsb_style ?>">
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Kompeten" <?= $dsb ?>> <b>Kompeten</b>
                    </label>
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Belum Kompeten" <?= $dsb ?>> <b>Belum Kompeten</b>
                    </label>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="small-text">Tindak lanjut yang dibutuhkan :</label>
                <textarea name="tindak_lanjut" class="form-control" rows="3"
                          placeholder="Masukkan pekerjaan tambahan dan asesmen yang diperlukan..."
                          <?= $dsb ?> style="<?= $dsb_style ?>"></textarea>
            </div>
            <div>
                <label class="small-text">Komentar / Observasi oleh Asesor :</label>
                <textarea name="komentar_asesor" class="form-control" rows="3"
                          placeholder="Komentar asesor..."
                          <?= $dsb ?> style="<?= $dsb_style ?>"></textarea>
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
                <div style="font-size:13px; margin-bottom:2px;">
                    <b>Nama :</b> <span id="asesor-ttd-nama" style="color:#1a237e;">— pilih skema —</span>
                </div>
                <div style="font-size:11px; color:#888; margin-bottom:8px;">
                    No. Reg: <span id="asesor-ttd-noreg">-</span>
                </div>
                <div style="margin-bottom:8px; <?= $dsb_style ?>">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesor" id="tanggal_asesor"
                           class="form-control" onchange="scheduleQRAsesor()"
                           <?= $dsb ?> style="<?= $dsb_style ?>">
                </div>
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

    <!-- Tombol -->
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
<script src="../assets/JS/fr_ak02.js"></script>
<script>
    // Kirim id_asesi ke JS
    var ID_ASESI = <?php echo $id_asesi; ?>;
</script>
</body>
</html>