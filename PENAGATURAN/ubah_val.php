<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp') {
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


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM tb_validator WHERE id_validator = ?";
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
    $id = intval($_POST['id_validator']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $noreg = mysqli_real_escape_string($koneksi, $_POST['noreg']);


    $errors = [];

    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }

    if (empty($noreg)) {
        $errors[] = "No Reg harus diisi";
    }


    $check_sql = "SELECT id_validator FROM tb_validator WHERE username = ? AND id_validator != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $username, $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Username sudah digunakan oleh user lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {

        if (!empty($noreg) && $noreg !== $user_data['noreg']) {

            $noreg_v = $noreg;
        } else {
            $noreg_v = $noreg;
        }

        $update_sql = "UPDATE tb_validator SET username = ?, noreg = ? WHERE id_validator = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "ssi", $username, $noreg_v, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Data user berhasil diperbarui!";
                $message_type = 'success';


                $user_data['username'] = $username;
                $user_data['noreg'] = $noreg;
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
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($user_data)): ?>
            <div class="form-container">
                <form method="post" action="" id="editUserForm">
                    <input type="hidden" name="id_validator" value="<?php echo $user_data['id_validator']; ?>">

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
                        <label for="noreg" class="required">
                            <i class="fas fa-lock"></i> No Reg
                        </label>
                        <input type="text"
                               id="noreg"
                               name="noreg"
                               value="<?php echo htmlspecialchars($user_data['noreg']); ?>"
                               required>
                        <span class="form-hint">No Reg untuk login user</span>
                    </div>
                    <div class="btn-container">
                        <a href="../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
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
            const noreg = document.getElementById('noreg').value.trim();

            let errors = [];

            if (!username) {
                errors.push('Username harus diisi');
            }

            if (!noreg) {
                errors.push('No Reg harus diisi');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>
