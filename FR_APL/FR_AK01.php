<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../koneksi.php";

// if (!isset($_SESSION['username'])) {
//     echo "<script>window.location.href='../LOGIN/login.php';</script>"; exit;
// }

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$role     = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$is_asesi = ($role === 'Asesi');

$nama_asesi_db = '';
if ($id_asesi) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesi FROM tb_asesi WHERE id_asesi = '$id_asesi' LIMIT 1"));
    $nama_asesi_db = $r['nama_asesi'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_skema             = isset($_POST['id_skema'])             ? intval($_POST['id_skema'])              : 0;
    $tuk                  = isset($_POST['tuk'])                  ? trim($_POST['tuk'])                     : '';
    $hari_tanggal         = isset($_POST['hari_tanggal'])         ? trim($_POST['hari_tanggal'])            : '';
    $waktu                = isset($_POST['waktu'])                ? trim($_POST['waktu'])                   : '';
    $tuk_pelaksanaan      = isset($_POST['tuk_pelaksanaan'])      ? trim($_POST['tuk_pelaksanaan'])         : '';
    $asesor_pelaksanaan   = isset($_POST['asesor_pelaksanaan'])   ? trim($_POST['asesor_pelaksanaan'])      : '';
    $nama_asesor          = isset($_POST['nama_asesor'])          ? trim($_POST['nama_asesor'])             : '';
    $no_reg_asesor        = isset($_POST['no_reg_asesor'])        ? trim($_POST['no_reg_asesor'])           : '';
    $ttd_asesor_qr        = isset($_POST['ttd_asesor_qr'])        ? trim($_POST['ttd_asesor_qr'])           : '';
    $tanggal_asesor       = isset($_POST['tanggal_asesor'])       ? trim($_POST['tanggal_asesor'])          : '';
    $nama_asesi           = isset($_POST['nama_asesi'])           ? trim($_POST['nama_asesi'])              : '';
    $ttd_asesi_qr         = isset($_POST['ttd_asesi_qr'])         ? trim($_POST['ttd_asesi_qr'])            : '';
    $tanggal_asesi        = isset($_POST['tanggal_asesi'])        ? trim($_POST['tanggal_asesi'])           : '';
    $bukti_lainnya        = isset($_POST['bukti_lainnya'])        ? trim($_POST['bukti_lainnya'])           : '';

    $bp   = isset($_POST['bukti_portofolio'])       ? 1 : 0;
    $brp  = isset($_POST['bukti_reviu_produk'])      ? 1 : 0;
    $bo   = isset($_POST['bukti_observasi'])         ? 1 : 0;
    $bk   = isset($_POST['bukti_kegiatan'])          ? 1 : 0;
    $btj  = isset($_POST['bukti_tanya_jawab'])       ? 1 : 0;
    $bpt  = isset($_POST['bukti_pertanyaan_tulis'])  ? 1 : 0;
    $bpl  = isset($_POST['bukti_pertanyaan_lisan'])  ? 1 : 0;
    $bw   = isset($_POST['bukti_wawancara'])         ? 1 : 0;

    if ($id_skema && $tuk && $nama_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_ak01
            (id_asesi, id_skema, tuk,
             bukti_portofolio, bukti_reviu_produk, bukti_observasi, bukti_kegiatan,
             bukti_tanya_jawab, bukti_pertanyaan_tulis, bukti_pertanyaan_lisan,
             bukti_wawancara, bukti_lainnya,
             hari_tanggal, waktu, tuk_pelaksanaan, asesor_pelaksanaan,
             nama_asesor, no_reg_asesor, ttd_asesor_qr, tanggal_asesor,
             nama_asesi, ttd_asesi_qr, tanggal_asesi)
            VALUES (
                '$id_asesi','$id_skema','{$e($tuk)}',
                '$bp','$brp','$bo','$bk','$btj','$bpt','$bpl','$bw',
                " . ($bukti_lainnya   ? "'{$e($bukti_lainnya)}'"   : "NULL") . ",
                " . ($hari_tanggal    ? "'{$e($hari_tanggal)}'"    : "NULL") . ",
                '{$e($waktu)}','{$e($tuk_pelaksanaan)}','{$e($asesor_pelaksanaan)}',
                '{$e($nama_asesor)}','{$e($no_reg_asesor)}',
                '{$e($ttd_asesor_qr)}',
                " . ($tanggal_asesor  ? "'{$e($tanggal_asesor)}'"  : "NULL") . ",
                '{$e($nama_asesi)}','{$e($ttd_asesi_qr)}',
                " . ($tanggal_asesi   ? "'{$e($tanggal_asesi)}'"   : "NULL") . "
            )";

        $res = mysqli_query($koneksi, $sql);
        if (!$res) {
            echo "<script>alert('Gagal simpan!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
        } else {
            echo "<script>alert('FR.AK.01 berhasil disimpan!');
                          window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih skema, TUK, dan isi nama asesi!');</script>";
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
    <title>FR.AK.01 Persetujuan Asesmen</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/JS/lsp_common.js"></script>
    <script src="../assets/JS/fr_ak01.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .form-box {
            margin: 35px auto; background: #fff;
            border: 1px solid #ddd; border-radius: 6px;
            padding: 25px 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .form-control {
            width: 99%; padding: 5px 7px;
            border: 1px solid #ccc; border-radius: 4px;
            box-sizing: border-box; font-size: 14px;
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
        .section-title {
            font-weight:bold; font-size:14px;
            border-left: 4px solid #4A7AFF;
            padding-left: 8px; margin: 20px 0 10px;
        }
        .label { font-weight:bold; }
        .required { color:red; font-weight:normal; }
        .small-text { font-size:12px; color:#444; }

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

        .bukti-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            margin-top: 8px;
        }
        .bukti-item { display:flex; align-items:center; gap:7px; font-size:13px; }
        .bukti-item input[type="checkbox"] { width:16px; height:16px; cursor:pointer; }

        .grid-2 { display:flex; gap:14px; flex-wrap:wrap; }
        .grid-2 > div { flex:1; min-width:140px; }

        .ttd-box {
            display:flex; gap:16px; flex-wrap:wrap; margin-top:18px;
        }
        .ttd-col {
            flex:1; min-width:220px;
            border:1px solid #ccc; border-radius:5px;
            padding:12px 14px; background:#fafbff;
        }
        .ttd-col .col-title {
            font-weight:bold; font-size:14px;
            border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:10px;
        }

        .qr-box {
            border:2px dashed #4A7AFF; border-radius:8px;
            padding:12px 8px; background:#f4f7ff;
            text-align:center; margin-top:10px;
        }
        .qr-box .qr-title { font-size:11px; font-weight:bold; color:#1a237e; margin-bottom:6px; }
        .qr-canvas-wrap {
            display:flex; justify-content:center; align-items:center;
            min-height:90px; margin-bottom:4px;
        }
        .qr-canvas-wrap canvas,
        .qr-canvas-wrap img {
            border:2px solid #4A7AFF; border-radius:5px;
            padding:4px; background:#fff;
        }
        .qr-placeholder-sm {
            width:100px; height:100px; border:2px dashed #aac;
            border-radius:5px; display:flex; align-items:center;
            justify-content:center; color:#bbb; font-size:11px;
            background:#fff; flex-direction:column; gap:3px; margin:0 auto;
        }
        .qr-badge {
            display:none; background:#e6f4ea; color:#2e7d32;
            border-radius:20px; padding:2px 10px;
            font-size:10px; font-weight:bold; margin-top:3px;
        }
        .btn-dl-qr {
            display:none; font-size:11px; background:#4A7AFF; color:#fff;
            border:none; padding:3px 10px; border-radius:20px;
            cursor:pointer; margin-top:5px;
        }
        .btn-dl-qr:hover { background:#325fd6; }

        .pernyataan-box {
            background:#fff8e1; border:1px solid #ffe082;
            border-radius:5px; padding:10px 14px; font-size:12px;
            color:#555; margin-top:8px; line-height:1.6;
        }

        @media(max-width:768px){
            .form-box { margin:6vw auto; padding:14px 4vw; }
            h2 { font-size:18px; }
            .btn-submit,.btn-back { width:48%; padding:10px; }
            .bukti-grid { grid-template-columns:1fr; }
            .ttd-box { flex-direction:column; }
            .grid-2 { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi"     value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema"     id="id_skema_hidden">
    <input type="hidden" name="nama_asesor"  id="nama_asesor_hidden">
    <input type="hidden" name="no_reg_asesor" id="no_reg_asesor_hidden">
    <input type="hidden" name="ttd_asesor_qr" id="ttd_asesor_qr_input">
    <input type="hidden" name="ttd_asesi_qr"  id="ttd_asesi_qr_input">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN
    </h2>
    <p style="font-size:12px; color:#555; margin:10px 0 16px; text-align:center;">
        Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen.
    </p>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin-bottom:14px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi / KKNI / Okupasi / Klaster
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Judul <span class="required">*</span></label>
                <div class="skema-wrap">
                    <input type="text" id="judul_skema" class="form-control"
                           placeholder="Ketik judul skema..." autocomplete="off"
                           oninput="searchSkema(this.value)" required>
                    <div class="skema-dropdown" id="skema-dropdown"></div>
                </div>
            </div>
            <div style="flex:1; min-width:140px;">
                <label class="small-text">Nomor</label>
                <input type="text" id="nomor_skema" class="form-control"
                       placeholder="Otomatis terisi" readonly style="background:#f5f5f5;">
            </div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:14px;">
        <div>
            <label class="label">TUK <span class="required">*</span></label>
            <select name="tuk" class="form-control" required>
                <option value="">-- Pilih TUK --</option>
                <option value="Sewaktu">Sewaktu</option>
                <option value="Tempat Kerja">Tempat Kerja</option>
                <option value="Mandiri">Mandiri</option>
            </select>
        </div>
        <div>
            <label class="label">Nama Asesor</label>
            <div id="asesor-nama-display"
                 style="padding:6px 8px; border:1px solid #e0e0e0; border-radius:4px;
                        background:#f5f5f5; font-size:14px; color:#1a237e; min-height:34px;">
                — pilih skema dulu —
            </div>
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <label class="label">Nama Asesi <span class="required">*</span></label>
        <input type="text" name="nama_asesi" id="nama_asesi" class="form-control"
               placeholder="Nama Asesi"
               value="<?php echo htmlspecialchars($nama_asesi_db); ?>"
               oninput="scheduleQRAsesi()" required>
    </div>

    <div class="section-title">Bukti yang akan dikumpulkan :</div>
    <div class="bukti-grid">
        <label class="bukti-item">
            <input type="checkbox" name="bukti_portofolio" value="1"> Hasil Verifikasi Portofolio
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_reviu_produk" value="1"> Hasil Reviu Produk
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_observasi" value="1"> Hasil Observasi Langsung
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_kegiatan" value="1"> Hasil Kegiatan Terstruktur
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_tanya_jawab" value="1"> Hasil Tanya Jawab
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_pertanyaan_tulis" value="1"> Hasil Pertanyaan Tulis
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_pertanyaan_lisan" value="1"> Hasil Pertanyaan Lisan
        </label>
        <label class="bukti-item">
            <input type="checkbox" name="bukti_wawancara" value="1"> Hasil Pertanyaan Wawancara
        </label>
        <div style="grid-column:1/-1; display:flex; align-items:center; gap:8px;">
            <label class="bukti-item" style="white-space:nowrap;">
                <input type="checkbox" name="bukti_lainnya_check" id="cb_lainnya" value="1"
                       onchange="toggleLainnya(this)"> Lainnya :
            </label>
            <input type="text" name="bukti_lainnya" id="input_lainnya"
                   class="form-control" placeholder="Sebutkan..."
                   style="display:none; max-width:300px;">
        </div>
    </div>

    <div class="section-title">Pelaksanaan asesmen disepakati pada :</div>
    <div class="grid-2" style="margin-bottom:6px;">
        <div>
            <label class="small-text">Hari / Tanggal</label>
            <input type="date" name="hari_tanggal" class="form-control">
        </div>
        <div>
            <label class="small-text">Waktu</label>
            <input type="time" name="waktu" class="form-control">
        </div>
    </div>
    <div class="grid-2">
        <div>
            <label class="small-text">TUK</label>
            <input type="text" name="tuk_pelaksanaan" class="form-control" placeholder="Nama / Alamat TUK">
        </div>
        <div>
            <label class="small-text">Asesor</label>
            <input type="text" name="asesor_pelaksanaan" id="asesor_pelaksanaan"
                   class="form-control" placeholder="Nama Asesor" readonly style="background:#f5f5f5;">
        </div>
    </div>

    <div class="section-title">Asesor :</div>
    <div class="pernyataan-box">
        Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor
        dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang
        sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
    </div>

    <div class="section-title">Asesi :</div>
    <div class="pernyataan-box">
        Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan
        untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
    </div>

    <div class="ttd-box">

        <div class="ttd-col">
            <div class="col-title">Tanda Tangan Asesor</div>
            <div style="font-size:13px; margin-bottom:4px;">
                <b>Nama :</b>
                <span id="asesor-ttd-nama" style="color:#1a237e;">— pilih skema —</span>
            </div>
            <div style="font-size:11px; color:#888; margin-bottom:8px;">
                No. Reg: <span id="asesor-ttd-noreg">-</span>
            </div>
            <div style="margin-bottom:8px; <?= $dsb_style ?>">
                <label class="small-text">Tanggal</label>
                <input type="date" name="tanggal_asesor" id="tanggal_asesor"
                       class="form-control" onchange="scheduleQRAsesor()"
                       <?= $dsb ?> style="<?= $dsb_style ?>">
            </div>
            <!-- <div class="qr-box">
                <div class="qr-title">QR Tanda Tangan Asesor</div>
                <div class="qr-canvas-wrap" id="qr-asesor-canvas">
                    <div class="qr-placeholder-sm" id="qr-asesor-ph">
                        <span>Pilih skema dulu</span>
                    </div>
                </div>
                <div id="qr-asesor-badge" class="qr-badge">QR Siap</div>
                <br>
                <button type="button" id="btn-dl-asesor" class="btn-dl-qr"
                        onclick="downloadQR('qr-asesor-canvas','ttd_asesor')">⬇ Download</button>
            </div> -->
        </div>

        <div class="ttd-col">
            <div class="col-title">Tanda Tangan Asesi</div>
            <div style="margin-bottom:8px;">
                <label class="small-text">Tanggal</label>
                <input type="date" name="tanggal_asesi" id="tanggal_asesi"
                       class="form-control" onchange="scheduleQRAsesi()">
            </div>
            <div class="qr-box">
                <div class="qr-title">QR Tanda Tangan Asesi</div>
                <div class="qr-canvas-wrap" id="qr-asesi-canvas">
                    <div class="qr-placeholder-sm" id="qr-asesi-ph">
                        <span>Isi nama dulu</span>
                    </div>
                </div>
                <div id="qr-asesi-badge" class="qr-badge">QR Siap</div>
                <br>
                <button type="button" id="btn-dl-asesi" class="btn-dl-qr"
                        onclick="downloadQR('qr-asesi-canvas','ttd_asesi')">⬇ Download</button>
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