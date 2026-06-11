<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$message = '';
$message_type = '';
$user_data = [];

$periode_list = [];
$q_periode = mysqli_query($koneksi, "SELECT id_periode, tahun_ajaran FROM tb_periode ORDER BY id_periode DESC");
if ($q_periode) {
    while ($p = mysqli_fetch_assoc($q_periode)) {
        $periode_list[] = $p;
    }
}


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM users WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
        } else {
            $message = "Data user tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $message = "ID user tidak valid.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id_user']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $id_periode = isset($_POST['id_periode']) ? (int) $_POST['id_periode'] : 0;


    $errors = [];

    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }

    // if (empty($password)) {
    //     $errors[] = "Password harus diisi";
    // }

    if (empty($role)) {
        $errors[] = "Role harus dipilih";
    }

    if ($id_periode <= 0) {
        $errors[] = "Tahun Ajaran harus dipilih";
    }

    // Cek jika username sudah digunakan(kecuali yang lagi ngubah)
    $check_sql = "SELECT id_user FROM users WHERE username = ? AND id_user != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $username, $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Username sudah digunakan oleh user lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {

        if (!empty($password) && $password !== $user_data['password']) {
            $password_hashed = md5($password);
        } else {
            $password_hashed = $user_data['password'];
        }
        
        $update_sql = "UPDATE users SET username = ?, password = ?, role = ?, id_periode = ? WHERE id_user = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sssii", $username, $password_hashed, $role, $id_periode, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Data user berhasil diperbarui!";
                $message_type = 'success';


                $user_data['username'] = $username;
                $user_data['password'] = $password;
                $user_data['role'] = $role;
                $user_data['id_periode'] = $id_periode;
            } else {
                $message = "Gagal memperbarui data: " . mysqli_error($koneksi);
                $message_type = 'error';
            }
            mysqli_stmt_close($update_stmt);
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = 'error';
    }
}
?>

    <link rel="stylesheet" href="../assets/CSS/ubah_manajeman.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="l-container">
        <div class="header">
            <i class="fas fa-user-edit"></i>
            <div>
                <h1>Ubah Data User</h1>
                <p>Perbarui informasi user sesuai kebutuhan</p>
            </div>
        </div>

        <div class="user-info">
            <i class="fas fa-user-circle"></i> Logged in sebagai:
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            (Role: <span><?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></span>)
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($user_data)): ?>
            <div class="form-container">
                <form method="post" action="" id="editUserForm">
                    <input type="hidden" name="id_user" value="<?php echo $user_data['id_user']; ?>">

                    <div class="form-group">
                        <label for="username" class="required">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text"
                               id="username"
                               name="username"
                               value="<?php echo htmlspecialchars($user_data['username']); ?>"
                               required
                               maxlength="50">
                        <span class="form-hint">Username unik untuk login sistem</span>
                    </div>

                    <div class="form-group">
                        <label for="password" >
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="text" id="password" name="password" value="" placeholder="Isi jika ingin mengganti password">
                        <span class="form-hint">Kosongkan jika tidak ingin mengubah password user</span>
                    </div>

                    <div class="form-group">
                        <label for="role" class="required">
                            <i class="fas fa-user-tag"></i> Role
                        </label>
                        <select id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="Admin_lsp" <?php echo ($user_data['role'] == 'Admin_lsp') ? 'selected' : ''; ?>>Admin LSP</option>
                            <option value="Asesor" <?php echo ($user_data['role'] == 'Asesor') ? 'selected' : ''; ?>>Asesor</option>
                            <option value="Asesi"  <?php echo ($user_data['role'] == 'Asesi') ? 'selected' : ''; ?>>Asesi</option>
                        </select>
                        <span class="form-hint">Hak akses user dalam sistem</span>
                    </div>

                    <div class="form-group">
                        <label for="id_periode" class="required">
                            <i class="fas fa-calendar"></i> Tahun Ajaran
                        </label>
                        <select id="id_periode" name="id_periode" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php foreach ($periode_list as $p): ?>
                                <option value="<?php echo (int) $p['id_periode']; ?>"
                                    <?php echo ((int) ($user_data['id_periode'] ?? 0) === (int) $p['id_periode']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['tahun_ajaran']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-hint">Hanya Admin yang dapat mengubah periode user</span>
                    </div>

                    <div class="btn-container">
                        <a href="../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif (empty($message)): ?>
            <!-- <div class="message error" style="margin: 30px;">
                <i class="fas fa-exclamation-triangle"></i>
                Data user tidak ditemukan. Silakan pilih user yang valid.
                <br><br>
                <a href="../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php" class="btn btn-secondary" style="margin-top: 10px;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
                </a>
            </div> -->
        <?php endif; ?>
    </div>

    <script>

        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);


        document.getElementById('editUserForm')?.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            // const password = document.getElementById('password').value.trim();
            const role = document.getElementById('role').value;
            const idPeriode = document.getElementById('id_periode').value;

            let errors = [];

            if (!username) {
                errors.push('Username harus diisi');
            }

            // if (!password) {
            //     errors.push('Password harus diisi');
            // }

            if (!role) {
                errors.push('Role harus dipilih');
            }

            if (!idPeriode) {
                errors.push('Tahun Ajaran harus dipilih');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>
