<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../koneksi.php";

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
    $waktu = isset($_POST['waktu']) ? trim($_POST['waktu']) : '';
    $umpan_balik = isset($_POST['umpan_balik']) ? trim($_POST['umpan_balik']) : '';
    $rekomendasi = isset($_POST['rekomendasi']) ? trim($_POST['rekomendasi']) : '';
    $rek_detail = isset($_POST['rek_detail']) ? trim($_POST['rek_detail']) : '';
    $nama_asesi = isset($_POST['nama_asesi']) ? trim($_POST['nama_asesi']) : '';
    $tanggal_asesi = isset($_POST['tanggal_asesi']) ? trim($_POST['tanggal_asesi']) : '';
    $ttd_asesi_qr = isset($_POST['ttd_asesi_qr']) ? trim($_POST['ttd_asesi_qr']) : '';
    $nama_asesor = isset($_POST['nama_asesor']) ? trim($_POST['nama_asesor']) : '';
    $no_reg_asesor = isset($_POST['no_reg_asesor']) ? trim($_POST['no_reg_asesor']) : '';
    $tanggal_asesor= isset($_POST['tanggal_asesor']) ? trim($_POST['tanggal_asesor']) : '';
    $ttd_asesor_qr = isset($_POST['ttd_asesor_qr']) ? trim($_POST['ttd_asesor_qr']) : '';
    $jawaban = isset($_POST['jawaban']) ? $_POST['jawaban'] : [];

    if ($id_skema && $tuk && $id_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_ia06
            (id_asesi, id_skema, tuk, tanggal, waktu, umpan_balik, rekomendasi, rek_detail,
             nama_asesi, tanggal_asesi, ttd_asesi_qr,
             nama_asesor, no_reg_asesor, tanggal_asesor, ttd_asesor_qr)
            VALUES (
                '$id_asesi','$id_skema','{$e($tuk)}',
                " . ($tanggal ? "'{$e($tanggal)}'" : "NULL") . ",
                '{$e($waktu)}',
                " . ($umpan_balik ? "'{$e($umpan_balik)}'" : "NULL") . ",
                " . ($rekomendasi ? "'{$e($rekomendasi)}'" : "NULL") . ",
                " . ($rek_detail ? "'{$e($rek_detail)}'" : "NULL") . ",
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
            $id_ia06 = mysqli_insert_id($koneksi);
            foreach ($jawaban as $id_soal => $teks) {
                $id_soal_i = intval($id_soal);
                $jwb_esc   = mysqli_real_escape_string($koneksi, trim($teks));
                mysqli_query($koneksi,
                    "INSERT INTO tb_ia06_jawaban (id_ia06, id_soal, jawaban_asesi)
                     VALUES ('$id_ia06','$id_soal_i','$jwb_esc')");
            }
            echo "<script>alert('Jawaban berhasil disimpan!');
                          window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih skema dan TUK terlebih dahulu!');</script>";
    }
}

$dsb_asesor       = $is_asesi ? 'disabled' : '';
$dsb_style_asesor = $is_asesi ? 'pointer-events:none;opacity:0.75;background:#f5f5f5;' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR.IA.06C - Lembar Jawaban</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <style>
        .jawaban-box { border:1px solid #e0e0e0; border-radius:5px; margin-bottom:12px; }
        .jawaban-soal { background:#eef3ff; padding:8px 14px; font-size:13px;
                        font-weight:bold; color:#1a237e; border-radius:5px 5px 0 0; }
        .jawaban-input { width:100%; min-height:100px; resize:vertical; font-size:13px;
                         padding:8px 10px; border:none; border-top:1px solid #e0e0e0;
                         border-radius:0 0 5px 5px; box-sizing:border-box; }
        .ttd-box { display:flex; gap:16px; flex-wrap:wrap; margin-top:18px; }
        .ttd-col { flex:1; min-width:220px; border:1px solid #ccc;
                   border-radius:5px; padding:12px 14px; background:#fafbff; }
        .ttd-col .col-title { font-weight:bold; font-size:14px;
                              border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:10px; }
        .qr-box { border:2px dashed #4A7AFF; border-radius:8px;
                  padding:10px 8px; background:#f4f7ff; text-align:center; margin-top:10px; }
        .qr-box .qr-title { font-size:11px; font-weight:bold; color:#1a237e; margin-bottom:6px; }
        .qr-canvas-wrap { display:flex; justify-content:center; align-items:center;
                          min-height:88px; margin-bottom:4px; }
        .qr-canvas-wrap canvas, .qr-canvas-wrap img {
            border:2px solid #4A7AFF; border-radius:5px; padding:4px; background:#fff; }
        .qr-ph-sm { width:90px; height:90px; border:2px dashed #aac; border-radius:5px;
                    display:flex; align-items:center; justify-content:center;
                    color:#bbb; font-size:11px; background:#fff; flex-direction:column; gap:3px; margin:0 auto; }
        .qr-badge { display:none; background:#e6f4ea; color:#2e7d32;
                    border-radius:20px; padding:2px 8px; font-size:10px; font-weight:bold; }
        .btn-dl-qr { display:none; font-size:11px; background:#4A7AFF; color:#fff;
                     border:none; padding:3px 10px; border-radius:20px; cursor:pointer; margin-top:4px; }
        .btn-dl-qr:hover { background:#325fd6; }
        @media(max-width:768px){ .ttd-box { flex-direction:column; } }
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
        FR.IA.06C &nbsp;–&nbsp; LEMBAR JAWABAN PERTANYAAN TERTULIS ESAI
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
                <input type="text" id="nomor_skema" class="form-control" readonly style="background:#f5f5f5;">
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
                <div id="asesor-nama" style="padding:5px 8px; border:1px solid #e0e0e0; border-radius:4px;
                     background:#f5f5f5; font-size:14px; color:#1a237e; min-height:32px;">— pilih skema dulu —</div>
            </div>
            <div style="flex:2; min-width:180px;">
                <label class="small-text">Nama Asesi</label>
                <input type="text" name="nama_asesi" id="nama_asesi" class="form-control"
                       value="<?php echo htmlspecialchars($nama_asesi_db); ?>"
                       placeholder="Nama Asesi" oninput="scheduleQRAsesi()">
            </div>
            <div style="flex:1; min-width:120px;">
                <label class="small-text">Tanggal</label>
                <input type="date" name="tanggal" class="form-control">
            </div>
            <div style="flex:1; min-width:100px;">
                <label class="small-text">Waktu</label>
                <input type="time" name="waktu" class="form-control">
            </div>
        </div>
    </div>

    <div class="section-title" style="margin:16px 0 8px;">JAWAB SEMUA PERTANYAAN DI BAWAH INI:</div>
    <div class="placeholder-box" id="soal-ph">Pilih skema untuk menampilkan soal</div>
    <div id="jawaban-area"></div>

    <div id="umpan-section" style="display:none;">
        <div class="section-title" style="margin:20px 0 8px;">Umpan Balik untuk Asesi</div>
        <div style="border:1px solid #ccc; border-radius:5px; padding:14px; background:#fafbff;">
            <div style="margin-bottom:10px;">
                <label class="small-text">Aspek pengetahuan seluruh unit :</label>
                <div style="display:flex; gap:16px; margin-top:6px; <?= $dsb_style_asesor ?>">
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Tercapai" <?= $dsb_asesor ?>> <b>Tercapai</b>
                    </label>
                    <label style="font-size:13px;">
                        <input type="radio" name="rekomendasi" value="Belum Tercapai" <?= $dsb_asesor ?>> <b>Belum Tercapai</b>
                    </label>
                </div>
            </div>
            <div style="margin-bottom:10px;" id="rek-detail-wrap" style="display:none;">
                <label class="small-text">Tuliskan unit/elemen/KUK jika belum tercapai :</label>
                <textarea name="rek_detail" class="form-control" rows="2"
                          placeholder="unit / elemen / KUK ..."
                          <?= $dsb_asesor ?> style="<?= $dsb_style_asesor ?>"></textarea>
            </div>
            <div>
                <label class="small-text">Umpan balik :</label>
                <textarea name="umpan_balik" class="form-control" rows="3"
                          placeholder="Catatan umpan balik untuk asesi..."
                          <?= $dsb_asesor ?> style="<?= $dsb_style_asesor ?>"></textarea>
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
                    <button type="button" id="btn-dl-asesi" class="btn-dl-qr" onclick="dlQRAsesi()">⬇ Download</button>
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
                <div style="margin-bottom:8px; <?= $dsb_style_asesor ?>">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesor" id="tanggal_asesor"
                           class="form-control" onchange="scheduleQRAsesor()"
                           <?= $dsb_asesor ?> style="<?= $dsb_style_asesor ?>">
                </div>
                <!-- <div class="qr-box" style="<= $dsb_style_asesor ?>">
                    <div class="qr-title">QR Tanda Tangan Asesor</div>
                    <div class="qr-canvas-wrap" id="qr-asesor-canvas">
                        <div class="qr-ph-sm" id="qr-asesor-ph">
                           <span>Pilih skema dulu</span>
                        </div>
                    </div>
                    <div id="qr-asesor-badge" class="qr-badge">QR Siap</div><br>
                    <button type="button" id="btn-dl-asesor" class="btn-dl-qr" onclick="dlQRAsesor()">⬇ Download</button>
                </div> -->
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="../assets/JS/lsp_common.js"></script>
<script src="../assets/JS/fr_ia06c.js"></script>
<script>
    var ID_ASESI = <?php echo $id_asesi; ?>;

    document.querySelectorAll('input[name="rekomendasi"]').forEach(function(r) {
        r.addEventListener('change', function() {
            var w = document.getElementById('rek-detail-wrap');
            if (w) w.style.display = this.value === 'Belum Tercapai' ? 'block' : 'none';
        });
    });
</script>
</body>
</html>