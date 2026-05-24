<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Asesi') {
    echo "<script>alert('Akses ditolak! Silakan login sebagai Asesi.'); window.location.href='../LOGIN/login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_asesi         = trim($_POST['nama_asesi']         ?? '');
    $nik                = trim($_POST['nik']                ?? '');
    $jenis_kelamin      = trim($_POST['jenis_kelamin']      ?? '');
    $kebangsaan         = trim($_POST['kebangsaan']         ?? '');
    $alamat_rumah       = trim($_POST['alamat_rumah']       ?? '');
    $kode_pos           = trim($_POST['kode_pos']           ?? '');
    $phone_rumah        = trim($_POST['phone_rumah']        ?? '');
    $phone_kantor       = trim($_POST['phone_kantor']       ?? '');
    $hp                 = trim($_POST['hp']                 ?? '');
    $email              = trim($_POST['email']              ?? '');
    $pendidikan         = trim($_POST['pendidikan']         ?? '');
    $nama_institusi     = trim($_POST['nama_institusi']     ?? '');
    $jabatan            = trim($_POST['jabatan']            ?? '');
    $alamat_institusi   = trim($_POST['alamat_institusi']   ?? '');
    $kode_pos_institusi = trim($_POST['kode_pos_institusi'] ?? '');
    $telp_institusi     = trim($_POST['telp_institusi']     ?? '');
    $fax                = trim($_POST['fax']                ?? '');
    $email_institusi    = trim($_POST['email_institusi']    ?? '');

    if (
        $nama_asesi && $nik && $jenis_kelamin && $kebangsaan &&
        $alamat_rumah && $kode_pos && $hp && $email && $pendidikan &&
        $nama_institusi && $jabatan && $alamat_institusi && $kode_pos_institusi
    ) {
        $nama_asesi_esc         = mysqli_real_escape_string($koneksi, $nama_asesi);
        $nik_esc                = mysqli_real_escape_string($koneksi, $nik);
        $jenis_kelamin_esc      = mysqli_real_escape_string($koneksi, $jenis_kelamin);
        $kebangsaan_esc         = mysqli_real_escape_string($koneksi, $kebangsaan);
        $alamat_rumah_esc       = mysqli_real_escape_string($koneksi, $alamat_rumah);
        $kode_pos_esc           = mysqli_real_escape_string($koneksi, $kode_pos);
        $phone_rumah_esc        = mysqli_real_escape_string($koneksi, $phone_rumah);
        $phone_kantor_esc       = mysqli_real_escape_string($koneksi, $phone_kantor);
        $hp_esc                 = mysqli_real_escape_string($koneksi, $hp);
        $email_esc              = mysqli_real_escape_string($koneksi, $email);
        $pendidikan_esc         = mysqli_real_escape_string($koneksi, $pendidikan);
        $nama_institusi_esc     = mysqli_real_escape_string($koneksi, $nama_institusi);
        $jabatan_esc            = mysqli_real_escape_string($koneksi, $jabatan);
        $alamat_institusi_esc   = mysqli_real_escape_string($koneksi, $alamat_institusi);
        $kode_pos_institusi_esc = mysqli_real_escape_string($koneksi, $kode_pos_institusi);
        $telp_institusi_esc     = mysqli_real_escape_string($koneksi, $telp_institusi);
        $fax_esc                = mysqli_real_escape_string($koneksi, $fax);
        $email_institusi_esc    = mysqli_real_escape_string($koneksi, $email_institusi);

        if (strlen($hp_esc) > 15) {
            echo "<script>alert('Nomor HP terlalu panjang. Maksimum 15 karakter.');</script>";
        } elseif (!empty($telp_institusi_esc) && strlen($telp_institusi_esc) > 15) {
            echo "<script>alert('Nomor telepon institusi terlalu panjang. Maksimum 15 karakter.');</script>";
        } elseif (!empty($email_institusi_esc) && !filter_var($email_institusi_esc, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Format email institusi tidak valid!');</script>";
        } elseif (!empty($email_institusi_esc) && strlen($email_institusi_esc) > 50) {
            echo "<script>alert('Email institusi terlalu panjang. Maksimum 50 karakter.');</script>";
        } else {
            $sql_insert = "INSERT INTO tb_asesi
                (nama_asesi, nik, jenis_kelamin, kebangsaan, alamat_rumah, kode_pos, phone_rumah, phone_kantor, hp, email, pendidikan, nama_institusi, jabatan, alamat_institusi, kode_pos_institusi, telp_institusi, fax, email_institusi)
                VALUES (
                    '$nama_asesi_esc', '$nik_esc', '$jenis_kelamin_esc', '$kebangsaan_esc', '$alamat_rumah_esc', '$kode_pos_esc',
                    " . ($phone_rumah_esc ? "'$phone_rumah_esc'" : "NULL") . ",
                    " . ($phone_kantor_esc ? "'$phone_kantor_esc'" : "NULL") . ",
                    '$hp_esc', '$email_esc', '$pendidikan_esc', '$nama_institusi_esc', '$jabatan_esc', '$alamat_institusi_esc', '$kode_pos_institusi_esc',
                    " . ($telp_institusi_esc ? "'$telp_institusi_esc'" : "NULL") . ",
                    " . ($fax_esc ? "'$fax_esc'" : "NULL") . ",
                    " . ($email_institusi_esc ? "'$email_institusi_esc'" : "NULL") . "
                )";
            $query_insert = mysqli_query($koneksi, $sql_insert);

            if (!$query_insert) {
                $error_msg = mysqli_error($koneksi);
                echo "<script>alert('Gagal menyimpan profil!\\nPesan error: " . addslashes($error_msg) . "');</script>";
            } else {
                $id_asesi = mysqli_insert_id($koneksi);
                $_SESSION['id_asesi'] = $id_asesi;

                $id_user  = intval($_SESSION['id_user']);
                $update_q = mysqli_query($koneksi, "UPDATE users SET id_asesi='$id_asesi' WHERE id_user='$id_user'");

                if (!$update_q) {
                    $update_err = mysqli_error($koneksi);
                    echo "<script>alert('Profil tersimpan, tetapi gagal menghubungkan user ke profil asesi.\\nPesan error: " . addslashes($update_err) . "');</script>";
                } else {
                    echo "<script>window.location.href='../BERANDA/UTAMA.php?id_asesi={$id_asesi}';</script>";
                    exit;
                }
            }
        }
    } else {
        echo "<script>alert('Semua field yang bertanda * wajib diisi!');</script>";
    }
}

$v = [];
$fields = ['nama_asesi','nik','jenis_kelamin','kebangsaan','alamat_rumah','kode_pos',
           'phone_rumah','phone_kantor','hp','email','pendidikan','nama_institusi',
           'jabatan','alamat_institusi','kode_pos_institusi','telp_institusi','fax','email_institusi'];
foreach ($fields as $f) {
    $v[$f] = htmlspecialchars($_POST[$f] ?? '');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR APL 1</title>
    <link rel="stylesheet" href="../assets/CSS/CSS_APL/FR_APL1.css">
</head>
<body>
    <div class="form-box">
        <form method="post" autocomplete="off">
            <h2 style="text-align:center; background: #cadbfc; padding: 18px 0 12px 0; border-radius:6px 6px 0 0;">
                FORMULIR ASESI
            </h2>
            <div class="section-title" style="margin-bottom:18px;">
                1: Rincian Data Mohon Agar Mengisi Bagian ( <span style="color:red">*</span> )<br>
                <span class="small-text">Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data Institusi anda pada saat ini.</span>
                <br>
                <span style="color:red;">Apabila terjadi trouble saat simpan profil, silakan cek pesan error di atas atau hubungi admin/operator untuk memastikan data atau koneksi database sudah sesuai.</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:13px;">
                <div>
                    <label class="label" for="nama_asesi">Nama <span class="required">*</span></label>
                    <input type="text" id="nama_asesi" name="nama_asesi" class="form-control"
                           placeholder="Nama" maxlength="100" value="<?= $v['nama_asesi'] ?>" required>
                </div>
                <div>
                    <label class="label" for="nik">NIK <span class="required">*</span></label>
                    <input type="text" id="nik" name="nik" class="form-control"
                           placeholder="NIK" maxlength="16" value="<?= $v['nik'] ?>" required>
                </div>
                <div>
                    <label class="label" for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" <?= $v['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan"  <?= $v['jenis_kelamin'] === 'Perempuan'  ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="kebangsaan">Kebangsaan <span class="required">*</span></label>
                    <select id="kebangsaan" name="kebangsaan" class="form-control" required>
                        <option value="">Pilih Kebangsaan</option>
                        <option value="WNI" <?= $v['kebangsaan'] === 'WNI' ? 'selected' : '' ?>>WNI</option>
                        <option value="WNA" <?= $v['kebangsaan'] === 'WNA' ? 'selected' : '' ?>>WNA</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="alamat_rumah">Alamat Rumah <span class="required">*</span></label>
                    <textarea id="alamat_rumah" name="alamat_rumah" class="form-control"
                              placeholder="Alamat Rumah" maxlength="100" required><?= $v['alamat_rumah'] ?></textarea>
                </div>
                <div>
                    <label class="label" for="kode_pos">Kode Pos Rumah <span class="required">*</span></label>
                    <input type="text" id="kode_pos" name="kode_pos" class="form-control"
                           placeholder="Kode Pos" maxlength="6" value="<?= $v['kode_pos'] ?>" required>
                </div>

                <div class="label" style="margin-bottom:2px;">Phone/E-mail</div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:120px;">
                        <label for="phone_rumah" class="small-text">Rumah</label>
                        <input type="text" id="phone_rumah" name="phone_rumah" class="form-control"
                               placeholder="Phone Rumah" maxlength="15" value="<?= $v['phone_rumah'] ?>">
                    </div>
                    <div style="flex:1; min-width:120px;">
                        <label for="phone_kantor" class="small-text">Kantor</label>
                        <input type="text" id="phone_kantor" name="phone_kantor" class="form-control"
                               placeholder="Phone Kantor" maxlength="15" value="<?= $v['phone_kantor'] ?>">
                    </div>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:120px;">
                        <label for="hp" class="small-text">HP <span class="required">*</span></label>
                        <input type="text" id="hp" name="hp" class="form-control"
                               placeholder="HP" maxlength="15" value="<?= $v['hp'] ?>" required>
                    </div>
                    <div style="flex:1; min-width:120px;">
                        <label for="email" class="small-text">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                               placeholder="E-mail" maxlength="50" value="<?= $v['email'] ?>" required>
                    </div>
                </div>

                <div>
                    <label class="label" for="pendidikan">Pendidikan <span class="required">*</span></label>
                    <input type="text" id="pendidikan" name="pendidikan" class="form-control"
                           placeholder="Pendidikan" maxlength="50" value="<?= $v['pendidikan'] ?>" required>
                </div>
                <div>
                    <label class="label" for="nama_institusi">Nama Institusi <span class="required">*</span></label>
                    <input type="text" id="nama_institusi" name="nama_institusi" class="form-control"
                           placeholder="Nama Institusi" maxlength="30" value="<?= $v['nama_institusi'] ?>" required>
                </div>
                <div>
                    <label class="label" for="jabatan">Jabatan <span class="required">*</span></label>
                    <input type="text" id="jabatan" name="jabatan" class="form-control"
                           placeholder="Jabatan" maxlength="17" value="<?= $v['jabatan'] ?>" required>
                </div>
                <div>
                    <label class="label" for="alamat_institusi">Alamat Institusi <span class="required">*</span></label>
                    <input type="text" id="alamat_institusi" name="alamat_institusi" class="form-control"
                           placeholder="Alamat Institusi" maxlength="100" value="<?= $v['alamat_institusi'] ?>" required>
                </div>
                <div>
                    <label class="label" for="kode_pos_institusi">Kode Pos Institusi <span class="required">*</span></label>
                    <input type="text" id="kode_pos_institusi" name="kode_pos_institusi" class="form-control"
                           placeholder="Kode Pos Institusi" maxlength="6" value="<?= $v['kode_pos_institusi'] ?>" required>
                </div>

                <div class="label" style="margin-bottom:2px;">No. Telp/Fax/E-mail</div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:120px;">
                        <label for="telp_institusi" class="small-text">Telp</label>
                        <input type="text" id="telp_institusi" name="telp_institusi" class="form-control"
                               placeholder="Telp" maxlength="15" value="<?= $v['telp_institusi'] ?>">
                    </div>
                    <div style="flex:1; min-width:120px;">
                        <label for="fax" class="small-text">Fax</label>
                        <input type="text" id="fax" name="fax" class="form-control"
                               placeholder="Fax" maxlength="15" value="<?= $v['fax'] ?>">
                    </div>
                </div>
                <div>
                    <label for="email_institusi" class="small-text">E-mail</label>
                    <input type="email" id="email_institusi" name="email_institusi" class="form-control"
                           placeholder="E-mail Kantor" maxlength="50" value="<?= $v['email_institusi'] ?>">
                </div>
            </div>
            <button type="submit" class="btn-submit" style="margin-top: 18px;">SIMPAN</button>
        </form>
    </div>
</body>
</html>