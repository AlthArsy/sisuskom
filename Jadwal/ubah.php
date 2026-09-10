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
$periode_list = [];
$q_periode = mysqli_query($koneksi, "SELECT id_periode, tahun_ajaran FROM tb_periode ORDER BY id_periode DESC");
if ($q_periode) {
    while ($p = mysqli_fetch_assoc($q_periode)) {
        $periode_list[] = $p;
    }
}

// $skema_list = [];
// $q_skema = mysqli_query($koneksi, "SELECT id_skema, judul_skema FROM tb_skema ORDER BY id_skema DESC");
// if ($q_skema) {
//     while ($s = mysqli_fetch_assoc($q_skema)) {
//         $skema_list[] = $s;
//     }
// }   

// $asesor_list = [];
// $q_asesor = mysqli_query($koneksi, "SELECT id_asesor, nama_asesor, no_reg FROM tb_asesor ORDER BY nama_asesor ASC");
// if ($q_asesor) {
//     while ($a = mysqli_fetch_assoc($q_asesor)) {
//         $asesor_list[] = $a;
//     }
// }




$message = '';
$message_type = '';
$user_data = [];


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM tb_jadwal WHERE id_jadwal = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            
            $p_id = intval($user_data['id_periode']);
            $q_p = mysqli_query($koneksi, "SELECT tahun_ajaran FROM tb_periode WHERE id_periode = $p_id");
            if ($q_p && $p_row = mysqli_fetch_assoc($q_p)) {
                $user_data['tahun_ajaran'] = $p_row['tahun_ajaran'];
            } else {
                $user_data['tahun_ajaran'] = '-';
            }
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
    $id = intval($_POST['id_jadwal']);
    $hari = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $waktu = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $tuk = mysqli_real_escape_string($koneksi, $_POST['tuk']);
    $id_periode = intval($_POST['id_periode']);
    // $id_skema = intval($_POST['id_skema']);
    // $id_asesor = intval($_POST['id_asesor']);


    $errors = [];

    if (empty($hari)) {
        $errors[] = "Hari harus diisi";
    }

    if (empty($tanggal)) {
        $errors[] = "Tanggal harus diisi";
    }

    if (empty($waktu)) {
        $errors[] = "Waktu harus diisi";
    }

    if (empty($tuk)) {
        $errors[] = "TUK harus diisi";
    }

    if ($id_periode <= 0) {
        $errors[] = "Tahun Ajaran harus dipilih";
    }

    $check_sql = "SELECT id_jadwal FROM tb_jadwal WHERE hari = ? AND tanggal = ? AND waktu = ? AND tuk = ? AND id_jadwal != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ssssi", $hari, $tanggal, $waktu, $tuk, $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Hari sudah digunakan oleh jadwal lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {

        $update_sql = "UPDATE tb_jadwal SET hari = ?, tanggal = ?, waktu = ?, tuk = ?, id_periode = ? WHERE id_jadwal = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "ssssii", $hari, $tanggal, $waktu, $tuk, $id_periode, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Data jadwal berhasil diperbarui!";
                $message_type = 'success';


                $user_data['hari'] = $hari;
                $user_data['tanggal'] = $tanggal;
                $user_data['waktu'] = $waktu;
                $user_data['tuk'] = $tuk;
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
                <h1>Ubah Data Jadwal</h1>
                <p>Perbarui informasi jadwal sesuai kebutuhan</p>
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
                    <input type="hidden" name="id_jadwal" value="<?php echo $user_data['id_jadwal']; ?>">

                    <div class="form-group">
                        <label for="hari" class="required">
                            <i class="fas fa-calendar-day"></i> Hari
                        </label>
                        <input type="text"
                               id="hari"
                               name="hari"
                               value="<?php echo htmlspecialchars($user_data['hari']); ?>"
                               required
                               maxlength="50">
                        <span class="form-hint">Username unik untuk login sistem</span>
                    </div>

                    <div class="form-group">
                        <label for="tanggal" class="required">
                            <i class="fas fa-calendar"></i> Tanggal
                        </label>
                        <input type="date"
                               id="tanggal"
                               name="tanggal"
                               value="<?php echo htmlspecialchars($user_data['tanggal']); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="waktu" class="required">
                            <i class="fas fa-clock"></i> Waktu
                        </label>
                        <input type="time"
                               id="waktu"
                               name="waktu"
                               value="<?php echo htmlspecialchars($user_data['waktu']); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="tuk" class="required">
                            <i class="fas fa-building"></i> TUK
                        </label>
                        <select id="tuk" name="tuk" required>
                            <option value="">Pilih TUK</option>
                            <option value="Sewaktu" <?php echo ($user_data['tuk'] === 'Sewaktu') ? 'selected' : ''; ?>>Sewaktu</option>
                            <option value="Tempat Kerja" <?php echo ($user_data['tuk'] === 'Tempat Kerja') ? 'selected' : ''; ?>>Tempat Kerja</option>
                            <option value="Mandiri" <?php echo ($user_data['tuk'] === 'Mandiri') ? 'selected' : ''; ?>>Mandiri</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_periode" class="required">
                            <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                        </label>
                        <input type="hidden" name="id_periode" value="<?php echo $user_data['id_periode']; ?>">
                        <input type="text" 
                               id="id_periode_display" 
                               value="<?php echo htmlspecialchars($user_data['tahun_ajaran']); ?>" 
                               readonly 
                               style="background-color: #e9ecef; cursor: not-allowed;">
                    </div>
                    <div class="btn-container">
                        <a href="../BERANDA/UTAMA.php?page=../Jadwal/jadwal.php" class="btn btn-secondary">
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
            const hari = document.getElementById('hari').value.trim();
            const tanggal = document.getElementById('tanggal').value.trim();
            const waktu = document.getElementById('waktu').value.trim();
            const tuk = document.getElementById('tuk').value.trim();
            /**const idSkema = document.getElementById('id_skema').value;
            const idAsesor = document.getElementById('id_asesor').value;**/

            let errors = [];

            if (!hari) {
                errors.push('Hari harus diisi');
            }
            if (!tanggal) {
                errors.push('Tanggal harus diisi');
            }
            if (!waktu) {
                errors.push('Waktu harus diisi');
            }
            if (!tuk) {
                errors.push('TUK harus diisi');
            }
            // if (!idSkema) {
            //     errors.push('Skema harus dipilih');
            // }
            // if (!idAsesor) {
            //     errors.push('Asesor harus dipilih');
            // }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>
