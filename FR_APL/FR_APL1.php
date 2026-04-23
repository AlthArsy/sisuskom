<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Asesi', 'Admin_lsp', 'Admin_utm'])) {
    echo "<script>alert('Akses ditolak!'); window.location.href='../LOGIN/login.php';</script>";
    exit;
} 

$id_asesi = isset($_GET['id_asesi'])
    ? intval($_GET['id_asesi'])
    : (isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0);

$role     = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$is_asesi = ($role === 'Asesi');


$nama_admin_lsp = '';
$id_user_session = intval($_SESSION['id_user'] ?? 0);

if ($role === 'Admin_lsp' && $id_user_session) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.nama_admin FROM users u
         JOIN tb_admin a ON a.id_admin = u.id_admin
         WHERE u.id_user = '$id_user_session' LIMIT 1"));
    $nama_admin_lsp = $r['nama_admin'] ?? '';
} elseif ($role === 'Admin_utm') {
    $nama_admin_lsp = $_SESSION['username'] ?? 'Admin Utama';
}

if (empty($nama_admin_lsp)) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT a.nama_admin FROM tb_admin a
         JOIN users u ON u.id_admin = a.id_admin
         WHERE u.role = 'Admin_lsp'
         ORDER BY a.id_admin ASC LIMIT 1"));
    $nama_admin_lsp = $r['nama_admin'] ?? '-';
}

$res_bd = mysqli_query($koneksi, "SELECT * FROM tb_bukti_dasar ORDER BY id_bd ASC");
$res_ba = mysqli_query($koneksi, "SELECT * FROM tb_bukti_adm  ORDER BY id_ba ASC");

$mode_lihat = isset($_GET['view']) && $_GET['view'] == 1;
$data_apl1  = null;
if ($mode_lihat && $id_asesi) {
    $data_apl1 = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM tb_apl1 WHERE id_asesi = '$id_asesi' ORDER BY id_apl1 DESC LIMIT 1"));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_rekomendasi'])) {
    $id_apl1_upd = intval($_POST['id_apl1_upd']);
    $rek = mysqli_real_escape_string($koneksi, trim($_POST['rekomendasi'] ?? ''));
    $cat = mysqli_real_escape_string($koneksi, trim($_POST['catatan_admin'] ?? ''));
    mysqli_query($koneksi,
        "UPDATE tb_apl1
         SET rekomendasi=" . ($rek ? "'$rek'" : "NULL") . ",
             catatan_admin=" . ($cat ? "'$cat'" : "NULL") . "
         WHERE id_apl1 = '$id_apl1_upd'");
    echo "<script>alert('Rekomendasi berhasil diperbarui!'); window.location.reload();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
    $judul_skema = isset($_POST['judul_skema']) ? trim($_POST['judul_skema']) : '';
    $nomor_skema = isset($_POST['nomor_skema']) ? trim($_POST['nomor_skema']) : '';
    $tujuan_asesmen = isset($_POST['tujuan_asesmen']) ? trim($_POST['tujuan_asesmen']) : '';
    $tujuan_lainnya = isset($_POST['tujuan_lainnya']) ? trim($_POST['tujuan_lainnya']) : '';
    $nama_pemohon = isset($_POST['nama_pemohon']) ? trim($_POST['nama_pemohon']) : '';
    $tanggal_pemohon = isset($_POST['tanggal_pemohon']) ? trim($_POST['tanggal_pemohon']) : '';
    $qr_data = isset($_POST['qr_data']) ? trim($_POST['qr_data']) : '';
    $catatan_admin = '';
    $rekomendasi = ''; 
    $kondisi_bd = isset($_POST['kondisi_bd']) ? $_POST['kondisi_bd'] : [];
    $kondisi_ba = isset($_POST['kondisi_ba']) ? $_POST['kondisi_ba'] : [];

    if ($judul_skema && $nomor_skema && $tujuan_asesmen) {
        $a = fn($v) => mysqli_real_escape_string($koneksi, $v);

        $sql = "INSERT INTO tb_apl1
            (id_skema, id_asesi, judul_skema, nomor_skema, tujuan_asesmen, tujuan_lainnya,
             nama_pemohon, tanggal_pemohon, qr_data, catatan_admin, rekomendasi)
            VALUES (
                '$id_skema', '$id_asesi',
                '{$a($judul_skema)}', '{$a($nomor_skema)}',
                '{$a($tujuan_asesmen)}', '{$a($tujuan_lainnya)}',
                '{$a($nama_pemohon)}', '{$a($tanggal_pemohon)}',
                '{$a($qr_data)}',
                " . ($catatan_admin ? "'{$a($catatan_admin)}'" : "NULL") . ",
                " . ($rekomendasi   ? "'{$a($rekomendasi)}'"   : "NULL") . "
            )";
        $result = mysqli_query($koneksi, $sql);

        if (!$result) {
            $err = mysqli_error($koneksi);
            echo "<script>alert('Gagal menyimpan!\\nError: " . addslashes($err) . "');</script>";
        } else {
            foreach ($kondisi_bd as $id_bd => $kondisi) {
                $id_bd_i   = intval($id_bd);
                $kond_esc  = mysqli_real_escape_string($koneksi, $kondisi);
                mysqli_query($koneksi, "INSERT INTO tb_isi_bukti_dasar (id_bd, kondisi, id_asesi)
                                        VALUES ('$id_bd_i','$kond_esc','$id_asesi')");
            }
            foreach ($kondisi_ba as $id_ba => $kondisi) {
                $id_ba_i   = intval($id_ba);
                $kond_esc  = mysqli_real_escape_string($koneksi, $kondisi);
                mysqli_query($koneksi, "INSERT INTO tb_isi_bukti_adm (id_ba, kondisi, id_asesi)
                                        VALUES ('$id_ba_i','$kond_esc','$id_asesi')");
            }
            echo "<script>alert('Formulir berhasil disimpan!'); window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Field bertanda * wajib diisi!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR - Bagian 2 & 3</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1_B2.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/JS/lsp_common.js"></script>
    <script src="../assets/JS/fr_apl1_b2.js"></script>
    <script>
        var ID_ASESI       = <?php echo $id_asesi; ?>;
        var NAMA_ADMIN_LSP = <?php echo json_encode($nama_admin_lsp); ?>;
    </script>
</head>
<body>
<?php if ($mode_lihat): ?>
<div class="form-box">
    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FORMULIR PERMOHONAN SERTIFIKASI KOMPETENSI
    </h2>

    <?php if (!$data_apl1): ?>
        <p style="text-align:center; color:#c00; padding:20px;">Data tidak ditemukan.</p>
    <?php else: ?>

    <table style="width:100%; font-size:13px; border-collapse:collapse; margin-top:16px;">
        <?php
        $baris = [
            'Judul Skema'    => $data_apl1['judul_skema'],
            'Nomor Skema'    => $data_apl1['nomor_skema'],
            'Tujuan Asesmen' => $data_apl1['tujuan_asesmen'] .
                                ($data_apl1['tujuan_lainnya'] ? ' – '.$data_apl1['tujuan_lainnya'] : ''),
            'Nama Pemohon'   => $data_apl1['nama_pemohon'],
            'Tanggal'        => $data_apl1['tanggal_pemohon'],
        ];
        foreach ($baris as $lbl => $val): ?>
        <tr>
            <td style="padding:7px 12px; font-weight:bold; width:35%;
                       border:1px solid #ddd; background:#f8faff;">
                <?= htmlspecialchars($lbl) ?>
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd;">
                <?= htmlspecialchars($val ?? '-') ?>
            </td>
        </tr>
        <?php endforeach; ?>

        <?php
        $res_bd_v = mysqli_query($koneksi,
            "SELECT bd.bukti_dasar, ibd.kondisi
             FROM tb_bukti_dasar bd
             LEFT JOIN tb_isi_bukti_dasar ibd
                ON ibd.id_bd = bd.id_bd AND ibd.id_asesi = '$id_asesi'
             ORDER BY bd.id_bd ASC");
        $no = 1;
        while ($row = mysqli_fetch_assoc($res_bd_v)): ?>
        <tr>
            <td style="padding:7px 12px; font-weight:bold; border:1px solid #ddd; background:#f8faff;">
                Bukti Dasar <?= $no++ ?>
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd;">
                <?= htmlspecialchars($row['bukti_dasar']) ?>
                <span style="margin-left:10px; font-weight:bold; color:#1a237e;">
                    → <?= htmlspecialchars($row['kondisi'] ?? '-') ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>

        <?php
        $res_ba_v = mysqli_query($koneksi,
            "SELECT ba.bukti_adm, iba.kondisi
             FROM tb_bukti_adm ba
             LEFT JOIN tb_isi_bukti_adm iba
                ON iba.id_ba = ba.id_ba AND iba.id_asesi = '$id_asesi'
             ORDER BY ba.id_ba ASC");
        $no = 1;
        while ($row = mysqli_fetch_assoc($res_ba_v)): ?>
        <tr>
            <td style="padding:7px 12px; font-weight:bold; border:1px solid #ddd; background:#f8faff;">
                Bukti Administratif <?= $no++ ?>
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd;">
                <?= htmlspecialchars($row['bukti_adm']) ?>
                <span style="margin-left:10px; font-weight:bold; color:#1a237e;">
                    → <?= htmlspecialchars($row['kondisi'] ?? '-') ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>

        <tr>
            <td style="padding:7px 12px; font-weight:bold; border:1px solid #ddd; background:#f8faff;">
                Rekomendasi
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd;
                       font-weight:bold;
                       color:<?= $data_apl1['rekomendasi'] === 'Diterima' ? '#2e7d32' : ($data_apl1['rekomendasi'] ? '#c00' : '#888') ?>;">
                <?= $data_apl1['rekomendasi'] ? htmlspecialchars($data_apl1['rekomendasi']) : '— Belum diisi oleh Admin LSP —' ?>
            </td>
        </tr>
        <tr>
            <td style="padding:7px 12px; font-weight:bold; border:1px solid #ddd; background:#f8faff;">
                Catatan Admin
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd;">
                <?= $data_apl1['catatan_admin']
                    ? nl2br(htmlspecialchars($data_apl1['catatan_admin']))
                    : '<span style="color:#aaa;">-</span>' ?>
            </td>
        </tr>
        <tr>
            <td style="padding:7px 12px; font-weight:bold; border:1px solid #ddd; background:#f8faff;">
                Admin LSP
            </td>
            <td style="padding:7px 12px; border:1px solid #ddd; color:#1a237e;">
                <?= htmlspecialchars($nama_admin_lsp) ?>
            </td>
        </tr>
    </table>

    <?php if (in_array($role, ['Admin_lsp', 'Admin_utm'])): ?>
    <div style="margin-top:22px; border:2px solid #4A7AFF; border-radius:6px;
                padding:18px; background:#f4f7ff;">
        <div style="font-weight:bold; font-size:14px; margin-bottom:12px; color:#1a237e;">
            Rekomendasi
        </div>
        <form method="post">
            <input type="hidden" name="aksi_rekomendasi" value="1">
            <input type="hidden" name="id_apl1_upd" value="<?= $data_apl1['id_apl1'] ?>">

            <div style="margin-bottom:10px;">
                <label style="font-size:13px; font-weight:bold; display:block; margin-bottom:5px;">
                    Keputusan :
                </label>
                <label style="font-size:13px; margin-right:16px;">
                    <input type="radio" name="rekomendasi" value="Diterima"
                        <?= $data_apl1['rekomendasi'] === 'Diterima' ? 'checked' : '' ?>>
                    Diterima
                </label>
                <label style="font-size:13px;">
                    <input type="radio" name="rekomendasi" value="Tidak Diterima"
                        <?= $data_apl1['rekomendasi'] === 'Tidak Diterima' ? 'checked' : '' ?>>
                    Tidak Diterima
                </label>
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:13px; font-weight:bold; display:block; margin-bottom:5px;">
                    Catatan :
                </label>
                <textarea name="catatan_admin" class="form-control" rows="3"
                          placeholder="Catatan untuk asesi..."
                          style="width:100%; padding:7px; border:1px solid #ccc;
                                 border-radius:4px; font-size:13px; box-sizing:border-box;"
                ><?= htmlspecialchars($data_apl1['catatan_admin'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-submit">SIMPAN REKOMENDASI ✓</button>
        </form>
    </div>
    <?php endif;?>

    <?php endif;?>

    <div style="margin-top:18px;">
        <button class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
            ← Kembali
        </button>
    </div>
</div>

<?php else: ?>
<div class="form-box">
<form method="post" autocomplete="off" id="mainForm">
    <input type="hidden" name="id_asesi"  value="<?php echo $id_asesi; ?>">
    <input type="hidden" name="id_skema"  id="id_skema_hidden">
    <input type="hidden" name="qr_data"   id="qr_data_input">

    <h2 style="text-align:center; background:#cadbfc; padding:18px 0 12px 0; border-radius:6px 6px 0 0;">
        FORMULIR PERMOHONAN SERTIFIKASI KOMPETENSI
    </h2>

    <!--BAGIAN 2-->
    <div class="section-title" style="margin:18px 0 10px 0;">
        Bagian 2 : Data Sertifikasi<br>
        <span class="small-text">Tuliskan Judul dan Nomor Skema Sertifikasi yang anda ajukan berikut Daftar Unit Kompetensi sesuai kemasan pada skema sertifikasi.</span>
    </div>

    <div style="display:flex; flex-direction:column; gap:13px;">

        <div style="border:1px solid #ddd; border-radius:5px; padding:12px 14px; background:#fafbff;">
            <div class="label" style="margin-bottom:8px;">
                Skema Sertifikasi <span style="font-size:12px; color:#555;">(KKNI/Okupasi/Klaster)</span>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">

                <div style="flex:2; min-width:180px;">
                    <label class="small-text">Judul <span class="required">*</span></label>
                    <div class="skema-wrap">
                        <input type="text"
                               name="judul_skema"
                               id="judul_skema"
                               class="form-control"
                               placeholder="Ketik judul skema..."
                               autocomplete="off"
                               oninput="searchSkema(this.value)"
                               required>
                        <div class="skema-dropdown" id="skema-dropdown"></div>
                    </div>
                    <div class="skema-selected-badge" id="skema-badge">Skema dipilih</div>
                </div>

                <div style="flex:1; min-width:140px;">
                    <label class="small-text">Nomor <span class="required">*</span></label>
                    <input type="text"
                           name="nomor_skema"
                           id="nomor_skema"
                           class="form-control"
                           placeholder="Otomatis terisi"
                           readonly
                           style="background:#f5f5f5;"
                           required>
                </div>
            </div>
        </div>

        <div>
            <div class="label" style="margin-bottom:6px;">Tujuan Asesmen <span class="required">*</span></div>
            <div style="display:flex; flex-direction:column; gap:5px; padding-left:4px;">
                <label style="font-size:14px;"><input type="radio" name="tujuan_asesmen" value="Sertifikasi" required> &nbsp;Sertifikasi</label>
                <label style="font-size:14px;"><input type="radio" name="tujuan_asesmen" value="Pengakuan Kompetensi Terkini (PKT)"> &nbsp;Pengakuan Kompetensi Terkini (PKT)</label>
                <label style="font-size:14px;"><input type="radio" name="tujuan_asesmen" value="Rekognisi Pembelajaran Lampau (RPL)"> &nbsp;Rekognisi Pembelajaran Lampau (RPL)</label>
                <label style="font-size:14px; display:flex; align-items:center; gap:6px;">
                    <input type="radio" name="tujuan_asesmen" value="Lainnya" id="radio_lainnya"> &nbsp;Lainnya :
                    <input type="text" name="tujuan_lainnya" id="input_lainnya" class="form-control"
                           placeholder="Sebutkan..." style="width:200px; display:none;">
                </label>
            </div>
        </div>

        <div>
            <div class="label" style="margin-bottom:6px;">Daftar Unit Kompetensi Sesuai Kemasan:</div>
            <div id="unit-container">
                <div class="unit-placeholder" id="unit-placeholder">
                    Pilih skema terlebih dahulu untuk menampilkan unit kompetensi
                </div>
                <div style="overflow-x:auto; display:none;" id="unit-table-wrap">
                    <table class="tbl-unit" id="unit-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Unit</th>
                                <th>Judul Unit</th>
                                <th>Standar Kompetensi Kerja</th>
                            </tr>
                        </thead>
                        <tbody id="unit-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!--BAGIAN 3-->
    <div class="section-title" style="margin:26px 0 10px 0;">
        Bagian 3 : Bukti Kelengkapan Pemohon
    </div>

    <div style="margin-bottom:16px;">
        <div class="label" style="margin-bottom:6px; font-size:14px;">3.1 Bukti Persyaratan Dasar Pemohon</div>
        <div style="overflow-x:auto;">
            <table class="tbl-bukti">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Bukti Persyaratan Dasar</th>
                        <th colspan="2">Ada</th>
                        <th rowspan="2">Tidak Ada</th>
                    </tr>
                    <tr><th>Memenuhi Syarat</th><th>Tidak Memenuhi Syarat</th></tr>
                </thead>
                <tbody>
                    <?php $no_bd = 1; while ($bd = mysqli_fetch_assoc($res_bd)): ?>
                    <tr>
                        <td><?= $no_bd++ ?>.</td>
                        <td style="text-align:left;"><?= htmlspecialchars($bd['bukti_dasar']) ?></td>
                        <td><input type="radio" name="kondisi_bd[<?= $bd['id_bd'] ?>]" value="Memenuhi Syarat"></td>
                        <td><input type="radio" name="kondisi_bd[<?= $bd['id_bd'] ?>]" value="Tidak Memenuhi Syarat"></td>
                        <td><input type="radio" name="kondisi_bd[<?= $bd['id_bd'] ?>]" value="Tidak Ada"></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <div class="label" style="margin-bottom:6px; font-size:14px;">Bukti Administratif</div>
        <div style="overflow-x:auto;">
            <table class="tbl-bukti">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Bukti Administratif</th>
                        <th colspan="2">Ada</th>
                        <th rowspan="2">Tidak Ada</th>
                    </tr>
                    <tr><th>Memenuhi Syarat</th><th>Tidak Memenuhi Syarat</th></tr>
                </thead>
                <tbody>
                    <?php $no_ba = 1; while ($ba = mysqli_fetch_assoc($res_ba)): ?>
                    <tr>
                        <td><?= $no_ba++ ?>.</td>
                        <td style="text-align:left;"><?= htmlspecialchars($ba['bukti_adm']) ?></td>
                        <td><input type="radio" name="kondisi_ba[<?= $ba['id_ba'] ?>]" value="Memenuhi Syarat"></td>
                        <td><input type="radio" name="kondisi_ba[<?= $ba['id_ba'] ?>]" value="Tidak Memenuhi Syarat"></td>
                        <td><input type="radio" name="kondisi_ba[<?= $ba['id_ba'] ?>]" value="Tidak Ada"></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $dsb = $is_asesi ? 'disabled' : '';
    $readonly_style = $is_asesi ? 'pointer-events:none; opacity:0.75; background:#f5f5f5;' : '';
    ?>
    <div class="rekomendasi-box">

        <div class="rekomendasi-col">
            <div class="col-title">Rekomendasi (diisi oleh LSP):</div>
            <div style="font-size:13px; margin-bottom:8px;">Berdasarkan ketentuan persyaratan dasar, maka pemohon :</div>
            <div style="display:flex; gap:10px; margin-bottom:8px; <?= $readonly_style ?>">
                <label style="font-size:13px;"><input type="radio" name="rekomendasi" value="Diterima" <?= $dsb ?>> Diterima</label>
                <label style="font-size:13px;"><input type="radio" name="rekomendasi" value="Tidak Diterima" <?= $dsb ?>> Tidak Diterima</label>
            </div>
            <div style="font-size:12px; color:#888; margin-bottom:8px;">sebagai peserta sertifikasi</div>
            <div>
                <label class="small-text">Catatan :</label>
                <textarea name="catatan_admin" class="form-control" rows="3"
                          placeholder="Catatan admin..." <?= $dsb ?>
                          style="<?= $readonly_style ?>"></textarea>
            </div>
            <div style="margin-top:14px;">
                <div class="col-title">Admin LSP :</div>
                <div style="font-size:13px;">
                    <b>Nama :</b>
                    <span id="nama-admin-lsp" style="color:#1a237e;">
                        <?php echo htmlspecialchars($nama_admin_lsp); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="rekomendasi-col">
            <div class="col-title">Pemohon/Kandidat :</div>
            <div style="margin-bottom:8px;">
                <label class="small-text">Nama <span class="required">*</span></label>
                <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control"
                       placeholder="Nama Pemohon" oninput="scheduleQR()"
                       <?= $is_asesi ? '' : '' ?> required>
            </div>
            <div style="margin-bottom:12px;">
                <label class="small-text">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal_pemohon" id="tanggal_pemohon" class="form-control"
                       onchange="scheduleQR()" required>
            </div>

            <div class="qr-signature-box">
                <div class="qr-title">QR Code</div>
                <div id="qr-canvas">
                    <div class="qr-placeholder" id="qr-placeholder">
                        <span>Isi data dulu</span>
                    </div>
                </div>
                <div id="qr-meta"  class="qr-meta"  style="display:none;"></div>
                <div id="qr-badge" class="qr-badge"  style="display:none;">QR Siap Di-Scan</div>
                <br>
                <button type="button" id="btn-dl-qr" class="btn-dl-qr" onclick="downloadQR()">
                    Download QR
                </button>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px;">
        <button type="button" class="btn-back"
            onclick="window.location.href='../BERANDA/UTAMA.php?page=../list/list_form.php'">
            Kembali
        </button>
        <button type="submit" class="btn-submit" onclick="return prepareQRData()">SIMPAN ✓</button>
    </div>
</form>
</div>
<?php endif; ?>
</body>
</html>