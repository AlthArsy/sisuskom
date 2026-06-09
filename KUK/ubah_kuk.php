<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$message = '';
$message_type = '';
$kuk_data = [];

if (isset($_GET['id'])) {
    $id_kuk = intval($_GET['id']);

    $sql = "SELECT
                k.*,
                el.no_elemen,
                el.nama_elemen,
                el.id_unit,
                uk.id_skema,
                s.id_asesor
            FROM tb_kuk k
            LEFT JOIN tb_elemen el ON k.id_elemen = el.id_elemen
            LEFT JOIN tb_unit_kompetensi uk ON el.id_unit = uk.id_unit
            LEFT JOIN tb_skema s ON uk.id_skema = s.id_skema
            WHERE k.id_kuk = ?";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_kuk);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $kuk_data = mysqli_fetch_assoc($result);

            if ($_SESSION['role'] === 'Asesor') {
                $id_asesor_login = $_SESSION['id_asesor'] ?? 0;

                if ($kuk_data['id_asesor'] != $id_asesor_login) {
                    $message = "Anda tidak memiliki akses untuk mengubah Kuk ini.";
                    $message_type = 'error';
                    $kuk_data = [];
                }
            }
        } else {
            $message = "Data Kuk kompetensi tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $message = "ID Kuk tidak valid.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id_kuk = intval($_POST['id_kuk']);
    $id_elemen = intval($_POST['id_kuk']);
    $no_kuk = mysqli_real_escape_string($koneksi, trim($_POST['no_kuk']));
    $kuk = mysqli_real_escape_string($koneksi, trim($_POST['kuk']));

    $errors = [];

    if (empty($no_kuk)) {
        $errors[] = "No Kuk harus diisi";
    }

    if (empty($kuk)) {
        $errors[] = "Kuk harus diisi";
    }

    $check_sql = "SELECT id_kuk FROM tb_kuk WHERE no_kuk = ? AND id_elemen = ? AND id_kuk != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "sii", $no_kuk, $id_elemen, $id_kuk );
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "No Kuk sudah digunakan";
    }
    mysqli_stmt_close($check_stmt);

    if (empty($errors)) {
        $update_sql = "UPDATE tb_kuk SET no_kuk = ?, kuk = ? WHERE id_kuk = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "ssi", $no_kuk, $kuk, $id_kuk);

            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['pesan'] = "Data Kuk berhasil diperbarui!";
                $_SESSION['tipe'] = 'success';

                $id_elemen = intval($_POST['id_elemen']);
                header("Location: UTAMA.php?page=../KUK/KUK.php&id_elemen=" . $id_elemen);
                exit();
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
<link rel="stylesheet" href="../assets/CSS/ubah_skema.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="ubah-container">
    <div class="ubah-header">
        <h1>Ubah Kuk</h1>
        <p>Perbarui informasi Kuk</p>
    </div>

    <div class="user-info">
        Logged in sebagai:
        <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
        (Role: <span><?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></span>)
    </div>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($kuk_data)): ?>
        <div class="form-container">
            <div class="skema-info-box">
                <h3>Informasi Elemen</h3>
                <p><strong>No Elemen :</strong> <?php echo htmlspecialchars($kuk_data['no_elemen']); ?></p>
                <p><strong>Nama Elemen:</strong> <?php echo htmlspecialchars($kuk_data['nama_elemen']); ?></p>
            </div>

            <form method="post" action="" id="editElemenForm">
                <input type="hidden" name="id_kuk" value="<?php echo $kuk_data['id_kuk']; ?>">
                <input type="hidden" name="id_elemen" value="<?php echo $kuk_data['id_elemen']; ?>">

                <div class="form-group">
                    <label for="no_kuk" class="required">
                        No Kuk
                    </label>
                    <input type="text"
                           id="no_kuk"
                           name="no_kuk"
                           class="form-control"
                           value="<?php echo htmlspecialchars($kuk_data['no_kuk']); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="kuk" class="required">
                        KuK
                    </label>
                    <textarea
                        id="kuk"
                        name="kuk"
                        class="form-control"
                        required
                        rows="3"><?php echo htmlspecialchars($kuk_data['kuk']); ?></textarea>
                </div>

                <div class="button-group">
                    <a href="UTAMA.php?page=../KUK/KUK.php&id_elemen=<?php echo $kuk_data['id_elemen']; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    <?php elseif (empty($message)): ?>
        <div class="message error">
            <i class="fas fa-exclamation-triangle"></i>
            Data Kuk tidak ditemukan. Silakan pilih Kuk yang valid.
            <br><br>
            <!-- <a href="../BERANDA/UTAMA.php?page=../" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Unit
            </a> -->
        </div>
    <?php endif; ?>
</div>

<style>
.skema-info-box {
    background: #f8f9fa;
    border-left: 4px solid #4186e0;
    padding: 15px;
    margin-bottom: 25px;
    border-radius: 4px;
}

.skema-info-box h3 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 12px;
}

.skema-info-box p {
    margin: 8px 0;
    color: #555;
    font-size: 14px;
}

.skema-info-box strong {
    color: #2c3e50;
}
</style>

<script>
    setTimeout(function() {
        const messages = document.querySelectorAll('.message');
        messages.forEach(message => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(() => message.remove(), 500);
        });
    }, 5000);

    document.getElementById('editElemenForm')?.addEventListener('submit', function(e) {
        const no_kuk = document.getElementById('no_kuk').value.trim();
        const kuk = document.getElementById('kuk').value.trim();

        let errors = [];

        if (!no_kuk) {
            errors.push('No Kuk harus diisi');
        }

        if (!kuk) {
            errors.push('Nama Kuk harus diisi');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
            return false;
        }
    });
</script>

<?php
mysqli_close($koneksi);
?>
