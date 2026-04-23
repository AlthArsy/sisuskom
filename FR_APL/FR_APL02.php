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

$res_skema_list = mysqli_query($koneksi,
    "SELECT s.id_skema, s.nomor_skema, s.judul_skema, s.standar_kompetensi_kerja,
            a.nama_asesor, a.no_reg
     FROM tb_skema s
     LEFT JOIN tb_asesor a ON a.id_asesor = s.id_asesor
     ORDER BY s.judul_skema ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_skema        = isset($_POST['id_skema'])        ? intval($_POST['id_skema'])        : 0;
    $nama_asesi_ttd  = isset($_POST['nama_asesi_ttd'])  ? trim($_POST['nama_asesi_ttd'])    : '';
    $tanggal_asesi   = isset($_POST['tanggal_asesi'])   ? trim($_POST['tanggal_asesi'])     : '';
    $rekomendasi     = isset($_POST['rekomendasi'])     ? trim($_POST['rekomendasi'])       : '';
    $pendekatan      = isset($_POST['pendekatan'])      ? trim($_POST['pendekatan'])        : '';
    $tanggal_asesor  = isset($_POST['tanggal_asesor'])  ? trim($_POST['tanggal_asesor'])    : '';
    $jawaban         = isset($_POST['jawaban'])         ? $_POST['jawaban']                : [];
    $bukti           = isset($_POST['bukti'])           ? $_POST['bukti']                  : [];

    $info_asesor = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.nama_asesor, a.no_reg FROM tb_skema s
         LEFT JOIN tb_asesor a ON a.id_asesor = s.id_asesor
         WHERE s.id_skema = '$id_skema' LIMIT 1"));
    $nama_asesor_ttd = $info_asesor['nama_asesor'] ?? '';
    $no_reg_asesor   = $info_asesor['no_reg']       ?? '';

    if ($id_skema && $id_asesi) {
        $e = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql_apl2 = "INSERT INTO tb_apl2
            (id_asesi, id_skema, rekomendasi, pendekatan,
             nama_asesi_ttd, tanggal_asesi,
             nama_asesor_ttd, no_reg_asesor, tanggal_asesor)
            VALUES (
                '$id_asesi', '$id_skema',
                " . ($rekomendasi    ? "'{$e($rekomendasi)}'"    : "NULL") . ",
                " . ($pendekatan     ? "'{$e($pendekatan)}'"     : "NULL") . ",
                '{$e($nama_asesi_ttd)}',
                " . ($tanggal_asesi  ? "'{$e($tanggal_asesi)}'"  : "NULL") . ",
                '{$e($nama_asesor_ttd)}', '{$e($no_reg_asesor)}',
                " . ($tanggal_asesor ? "'{$e($tanggal_asesor)}'" : "NULL") . "
            )";
        $res = mysqli_query($koneksi, $sql_apl2);

        if (!$res) {
            echo "<script>alert('Gagal simpan APL2!\\n" . addslashes(mysqli_error($koneksi)) . "');</script>";
        } else {
            $id_apl2 = mysqli_insert_id($koneksi);
            foreach ($jawaban as $id_elemen => $k_bk) {
                $id_el  = intval($id_elemen);
                $kb_esc = mysqli_real_escape_string($koneksi, $k_bk);
                $bk_esc = mysqli_real_escape_string($koneksi, $bukti[$id_elemen] ?? '');
                mysqli_query($koneksi,
                    "INSERT INTO tb_jawaban_apl2 (id_apl2, id_elemen, k_bk, bukti)
                     VALUES ('$id_apl2','$id_el','$kb_esc','$bk_esc')");
            }
            echo "<script>alert('Asesmen Mandiri berhasil disimpan!');
                          window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih skema terlebih dahulu!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR APL-02 Asesmen Mandiri</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/JS/lsp_common.js"></script>
    <script src="../assets/JS/fr_apl02.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .form-box {
            margin: 35px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 25px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
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
        .section-title { font-weight:bold; font-size:15px; border-radius:3px; padding-bottom:7px; }
        .label { font-weight:bold; }
        .required { color:red; font-weight:normal; }
        .small-text { font-size:12px; color:#444; }

        .skema-wrap { position:relative; }
        .skema-dropdown {
            position:absolute; top:100%; left:0; right:0;
            background:#fff; border:1px solid #4A7AFF;
            border-radius:0 0 5px 5px; max-height:220px;
            overflow-y:auto; z-index:999; display:none;
            box-shadow:0 4px 12px rgba(0,0,0,0.12);
        }
        .skema-item { padding:9px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #eef; }
        .skema-item:hover { background:#eef3ff; }
        .skema-item .sk-judul { font-weight:bold; color:#1a237e; }
        .skema-item .sk-nomor { font-size:11px; color:#777; margin-top:2px; }

        .unit-box {
            border: 1px solid #b0bec5;
            border-radius: 6px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .unit-header {
            background: #cadbfc;
            padding: 10px 14px;
            font-weight: bold;
            font-size: 14px;
        }
        .unit-header .unit-sub {
            font-size: 12px;
            font-weight: normal;
            color: #333;
        }

        .tbl-apl2 { width:100%; border-collapse:collapse; font-size:13px; }
        .tbl-apl2 th {
            background:#dce8ff; padding:8px 10px;
            border:1px solid #b0c4de; text-align:center;
        }
        .tbl-apl2 td { padding:8px 10px; border:1px solid #d0d8e8; vertical-align:top; }
        .elemen-row td { background:#f0f4ff; font-weight:bold; font-size:12px; }
        .kuk-list { list-style:none; margin:0; padding:0; }
        .kuk-list li { padding:3px 0; font-size:12px; color:#333; }
        .kuk-list li::before { content:"• "; color:#4A7AFF; }

        .radio-kb { display:flex; justify-content:center; gap:10px; }
        .radio-kb label { font-size:13px; cursor:pointer; }

        .bukti-input {
            width:100%; min-height:48px; resize:vertical;
            border:1px solid #ccc; border-radius:4px;
            font-size:12px; padding:4px 6px; box-sizing:border-box;
        }

        .rek-box {
            display:flex; gap:16px; flex-wrap:wrap; margin-top:18px;
            border:1px solid #ccc; border-radius:5px; padding:14px;
            background:#fafbff;
        }
        .rek-col { flex:1; min-width:200px; }
        .rek-col .col-title {
            font-weight:bold; font-size:14px;
            border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:8px;
        }
        .placeholder-box {
            text-align:center; padding:24px; color:#aaa;
            font-size:13px; border:1px dashed #ccc;
            border-radius:5px; margin-top:8px;
        }

        @media(max-width:768px){
            .form-box { margin:6vw auto; padding:14px 4vw; }
            h2 { font-size:18px; }
            .btn-submit,.btn-back { width:48%; padding:10px; }
            .tbl-apl2 { font-size:11px; }
            .rek-box { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi" value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema" id="id_skema_hidden">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FR. APL-02. ASESMEN MANDIRI
    </h2>

    <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff; margin:16px 0 10px;">
        <div class="label" style="margin-bottom:8px;">
            Skema Sertifikasi <span style="font-size:12px; color:#555;">(KKNI/Okupasi/Klaster)</span>
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

    <div style="background:#fffde7; border:1px solid #ffe082; border-radius:5px;
                padding:10px 14px; font-size:13px; margin-bottom:14px;">
        <b>PANDUAN ASESMEN MANDIRI</b><br>
        <ul style="margin:6px 0 0 16px; padding:0;">
            <li>Baca setiap pertanyaan di kolom sebelah kiri</li>
            <li>Beri tanda <b>K</b> (Kompeten) atau <b>BK</b> (Belum Kompeten) pada kolom yang sesuai</li>
            <li>Isi kolom Bukti dengan bukti relevan yang anda miliki</li>
        </ul>
    </div>

    <div id="unit-container">
        <div class="placeholder-box" id="unit-placeholder">
            Pilih skema terlebih dahulu untuk menampilkan unit kompetensi
        </div>
    </div>

    <?php
    $dsb          = $is_asesi ? 'disabled' : '';
    $dsb_style    = $is_asesi ? 'pointer-events:none; opacity:0.75; background:#f5f5f5;' : '';
    ?>
    <div id="rek-section" style="display:none;">
        <div class="section-title" style="margin:22px 0 10px 0;">
            Rekomendasi &amp; Tanda Tangan
        </div>
        <div class="rek-box">
            <div class="rek-col">
                <div class="col-title">Asesi :</div>
                <div style="font-size:13px; margin-bottom:8px;">
                    Asesmen dapat / tidak dapat dilanjutkan melalui :
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:6px; <?= $dsb_style ?>">
                    <label style="font-size:13px;"><input type="radio" name="rekomendasi" value="Dapat" <?= $dsb ?>> Dapat</label>
                    <label style="font-size:13px;"><input type="radio" name="rekomendasi" value="Tidak Dapat" <?= $dsb ?>> Tidak Dapat</label>
                </div>
                <div style="margin-bottom:8px;">
                    <label class="small-text">Pendekatan :</label>
                    <input type="text" name="pendekatan" class="form-control"
                           placeholder="misal: Observasi, Portofolio..." <?= $dsb ?>
                           style="<?= $dsb_style ?>">
                </div>
                <div style="margin-bottom:8px;">
                    <label class="small-text">Nama <span class="required">*</span></label>
                    <input type="text" name="nama_asesi_ttd" class="form-control"
                           placeholder="Nama Asesi" required>
                </div>
                <div>
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesi" class="form-control">
                </div>
            </div>

            <div class="rek-col">
                <div class="col-title">Ditinjau Oleh Asesor :</div>
                <div style="margin-bottom:6px;">
                    <label class="small-text">Nama Asesor</label>
                    <div style="font-size:13px; font-weight:bold; color:#1a237e;" id="asesor-nama-rek">
                        — pilih skema dulu —
                    </div>
                </div>
                <div style="margin-bottom:10px;">
                    <label class="small-text">No. Reg</label>
                    <div style="font-size:12px; color:#555;" id="asesor-noreg-rek">-</div>
                </div>
                <div style="margin-bottom:8px; <?= $dsb_style ?>">
                    <label class="small-text">Tanda tangan / Tanggal</label>
                    <input type="date" name="tanggal_asesor" class="form-control"
                           <?= $dsb ?> style="<?= $dsb_style ?>">
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
            Kembali
        </button>
        <button type="submit" class="btn-submit">SIMPAN ✓</button>
    </div>
</form>
</div>
</body>
</html>