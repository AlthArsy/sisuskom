<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

$role = $_SESSION['role'];
$username = $_SESSION['username'];

$query_user = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $query_user);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$profile_data = null;
if ($role === 'Asesor' && $user_data['id_asesor']) {
    $query = "SELECT * FROM tb_asesor WHERE id_asesor = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_data['id_asesor']);
    mysqli_stmt_execute($stmt);
    $profile_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
} elseif ($role === 'Asesi' && $user_data['id_asesi']) {
    $query = "SELECT * FROM tb_asesi WHERE id_asesi = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_data['id_asesi']);
    mysqli_stmt_execute($stmt);
    $profile_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $message = "Password baru tidak cocok!";
            $message_type = "error";
        } elseif (strlen($new_password) < 6) {
            $message = "Password minimal 6 karakter!";
            $message_type = "error";
        } else {
            $update = "UPDATE users SET password = ? WHERE id_user = ?";
            $stmt = mysqli_prepare($koneksi, $update);
            mysqli_stmt_bind_param($stmt, "si", $new_password, $user_data['id_user']);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Password berhasil diubah!";
                $message_type = "success";
                $user_data['password'] = $new_password;
            } else {
                $message = "Gagal mengubah password!";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['update_username']) && $role === 'Admin') {
        $new_username = trim($_POST['username']);
        
        if (empty($new_username)) {
            $message = "Username tidak boleh kosong!";
            $message_type = "error";
        } else {
            $update = "UPDATE users SET username = ? WHERE id_user = ?";
            $stmt = mysqli_prepare($koneksi, $update);
            mysqli_stmt_bind_param($stmt, "si", $new_username, $user_data['id_user']);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['username'] = $new_username;
                $message = "Username berhasil diubah!";
                $message_type = "success";
                header("Refresh:1");
            } else {
                $message = "Gagal mengubah username!";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['update_profile_asesor']) && $role === 'Asesor') {
        $no_reg = trim($_POST['no_reg']);
        $nama_asesor = trim($_POST['nama_asesor']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $alamat = trim($_POST['alamat']);
        
        $update = "UPDATE tb_asesor SET no_reg = ?, nama_asesor = ?, jenis_kelamin = ?, alamat = ? WHERE id_asesor = ?";
        $stmt = mysqli_prepare($koneksi, $update);
        mysqli_stmt_bind_param($stmt, "ssssi", $no_reg, $nama_asesor, $jenis_kelamin, $alamat, $user_data['id_asesor']);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['nama_user'] = $nama_asesor;
            $message = "Profil berhasil diperbarui!";
            $message_type = "success";
            header("Refresh:1");
        } else {
            $message = "Gagal memperbarui profil!";
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['update_profile_asesi']) && $role === 'Asesi') {
        $fields = [
            'nama_asesi', 'nik', 'jenis_kelamin', 'kebangsaan', 'alamat_rumah', 'kode_pos',
            'hp', 'email', 'pendidikan', 'nama_institusi', 'jabatan', 'alamat_institusi', 'kode_pos_institusi'
        ];
        
        $values = [];
        foreach ($fields as $field) {
            $values[] = trim($_POST[$field] ?? '');
        }
        
        $update = "UPDATE tb_asesi SET 
            nama_asesi = ?, nik = ?, jenis_kelamin = ?, kebangsaan = ?, alamat_rumah = ?, kode_pos = ?,
            hp = ?, email = ?, pendidikan = ?, nama_institusi = ?, jabatan = ?, alamat_institusi = ?, kode_pos_institusi = ?
            WHERE id_asesi = ?";
        
        $stmt = mysqli_prepare($koneksi, $update);
        $values[] = $user_data['id_asesi'];
        $types = str_repeat('s', count($fields)) . 'i';
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['nama_user'] = $values[0];
            $message = "Profil berhasil diperbarui!";
            $message_type = "success";
            header("Refresh:1");
        } else {
            $message = "Gagal memperbarui profil!";
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<link rel="stylesheet" href="../assets/CSS/profil.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="pengaturan-container">
    <div class="page-header">
        <h1><i class="fas fa-user-cog"></i> Pengaturan</h1>
        <p>Kelola profil dan keamanan akun Anda</p>
    </div>

    <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="info-box">
        <strong><i class="fas fa-info-circle"></i> Informasi Akun:</strong><br>
        Username: <strong><?= htmlspecialchars($username) ?></strong> | 
        Role: <strong><?= htmlspecialchars($role) ?></strong>
    </div>

    <?php if ($role === 'Admin'): ?>
    <div class="card">
        <div class="card-header">
            <h2>Edit Username</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="username" class="required">Username Baru</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?= htmlspecialchars($user_data['username']) ?>" required>
                </div>
                <button type="submit" name="update_username" class="btn btn-primary">
                   Simpan Username
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Ubah Password</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="new_password" class="required">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" 
                           minlength="6" required>
                    <small style="color: #6c757d;">Minimal 6 karakter</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="required">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           minlength="6" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-success">
                     Ubah Password
                </button>
            </form>
        </div>
    </div>

    <?php if ($role === 'Asesor' && $profile_data): ?>
    <div class="card">
        <div class="card-header">
            <h2> Edit Profil Asesor</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_reg" class="required">No Registrasi</label>
                        <input type="text" id="no_reg" name="no_reg" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['no_reg']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_asesor" class="required">Nama Lengkap</label>
                        <input type="text" id="nama_asesor" name="nama_asesor" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['nama_asesor']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="required">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                            <option value="Laki-laki" <?= $profile_data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $profile_data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alamat" class="required">Alamat</label>
                        <input type="text" id="alamat" name="alamat" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['alamat']) ?>" required>
                    </div>
                </div>
                <button type="submit" name="update_profile_asesor" class="btn btn-primary">
                    Simpan Profil
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($role === 'Asesi' && $profile_data): ?>
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-id-card"></i> Edit Profil Asesi</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <h3 style="margin: 0 0 20px 0; color: #667eea; font-size: 1.1em;">Data Pribadi</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_asesi" class="required">Nama Lengkap</label>
                        <input type="text" id="nama_asesi" name="nama_asesi" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['nama_asesi']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nik" class="required">NIK</label>
                        <input type="text" id="nik" name="nik" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['nik']) ?>" maxlength="16" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="required">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                            <option value="Laki-laki" <?= $profile_data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $profile_data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kebangsaan" class="required">Kebangsaan</label>
                        <input type="text" id="kebangsaan" name="kebangsaan" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['kebangsaan']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="alamat_rumah" class="required">Alamat Rumah</label>
                        <input type="text" id="alamat_rumah" name="alamat_rumah" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['alamat_rumah']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_pos" class="required">Kode Pos</label>
                        <input type="text" id="kode_pos" name="kode_pos" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['kode_pos']) ?>" maxlength="6" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hp" class="required">No HP</label>
                        <input type="text" id="hp" name="hp" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['hp']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['email']) ?>" required>
                    </div>
                </div>

                <h3 style="margin: 30px 0 20px 0; color: #667eea; font-size: 1.1em;">Data Institusi</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="pendidikan">Pendidikan Terakhir</label>
                        <input type="text" id="pendidikan" name="pendidikan" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['pendidikan'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_institusi" class="required">Nama Institusi</label>
                        <input type="text" id="nama_institusi" name="nama_institusi" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['nama_institusi']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="jabatan" class="required">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['jabatan']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat_institusi" class="required">Alamat Institusi</label>
                        <input type="text" id="alamat_institusi" name="alamat_institusi" class="form-control" 
                               value="<?= htmlspecialchars($profile_data['alamat_institusi']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="kode_pos_institusi" class="required">Kode Pos Institusi</label>
                    <input type="text" id="kode_pos_institusi" name="kode_pos_institusi" class="form-control" 
                           value="<?= htmlspecialchars($profile_data['kode_pos_institusi']) ?>" maxlength="6" required>
                </div>

                <button type="submit" name="update_profile_asesi" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Profil
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
setTimeout(function() {
    const message = document.querySelector('.message');
    if (message) {
        message.style.opacity = '0';
        message.style.transition = 'opacity 0.5s ease';
        setTimeout(() => message.remove(), 500);
    }
}, 5000);

document.getElementById('confirm_password')?.addEventListener('input', function() {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = this.value;
    
    if (newPass !== confirmPass) {
        this.setCustomValidity('Password tidak cocok!');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php mysqli_close($koneksi); ?>