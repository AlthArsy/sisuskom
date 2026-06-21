<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp' && $_SESSION['role'] !== 'Admin_utm') {
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

    $sql = "SELECT * FROM tb_periode WHERE id_periode = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
        } else {
            $message = "Data periode tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $message = "ID periode tidak valid.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id_periode']);
    $tahun_ajaran = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran']);


    $errors = [];

    if (empty($tahun_ajaran)) {
        $errors[] = "Tahun Ajaran harus diisi";
    }

    $check_sql = "SELECT id_periode FROM tb_periode WHERE tahun_ajaran = ? AND id_periode != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $tahun_ajaran, $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Tahun Ajaran sudah digunakan oleh periode lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {
        $update_sql = "UPDATE tb_periode SET tahun_ajaran = ? WHERE id_periode = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "si", $tahun_ajaran, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Data periode berhasil diperbarui!";
                $message_type = 'success';


                $user_data['tahun_ajaran'] = $tahun_ajaran;
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
                <h1>Ubah Data Periode</h1>
                <p>Perbarui informasi periode sesuai kebutuhan</p>
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
                    <input type="hidden" name="id_periode" value="<?php echo $user_data['id_periode']; ?>">

                    <div class="form-group">
                        <label for="tahun_ajaran" class="required">
                            <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                        </label>
                        <input type="text"
                               id="tahun_ajaran"
                               name="tahun_ajaran"
                               value="<?php echo htmlspecialchars($user_data['tahun_ajaran']); ?>"
                               required
                               maxlength="40">
                        <span class="form-hint">Tahun ajaran untuk periode</span>
                    </div>
                    <div class="btn-container">
                        <a href="../BERANDA/UTAMA.php?page=../Periode/periode.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
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
            const tahun_ajaran = document.getElementById('tahun_ajaran').value.trim();

            let errors = [];

            if (!tahun_ajaran) {
                errors.push('Tahun Ajaran harus diisi');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>
