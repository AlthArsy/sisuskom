<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Asesor'], true)) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_skema_form = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
} else {
    $id_skema_form = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $id_skema_form <= 0) {
    $_SESSION['pesan'] = 'Tambah bukti adm harus melalui daftar skema (tombol Tambah dari halaman Bukti Adm).';
    $_SESSION['tipe'] = 'error';
    header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $bukti_adm = mysqli_real_escape_string($koneksi, $_POST['bukti_adm']);
    $id_skema_post = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;

    $errors = [];

    if ($id_skema_post <= 0) {
        $errors[] = "Skema wajib dipilih (buka dari daftar skema).";
    }

    if ($bukti_adm === '' || trim($_POST['bukti_adm'] ?? '') === '') {
        $errors[] = "Bukti ADM harus diisi";
    }

    if (empty($errors)) {
        $check_sql = "SELECT id_ba FROM tb_bukti_adm WHERE id_skema = ? AND bukti_adm = ?";
        $check_stmt = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "is", $id_skema_post, $bukti_adm);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $errors[] = "Bukti ADM sudah digunakan, silakan pilih bukti adm lain";
        }
        mysqli_stmt_close($check_stmt);
    }

    if (empty($errors)) {
        $insert_sql = "INSERT INTO tb_bukti_adm (id_skema, bukti_adm) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

        if ($insert_stmt) {
            mysqli_stmt_bind_param($insert_stmt, "is", $id_skema_post, $bukti_adm);

            try {
                if (mysqli_stmt_execute($insert_stmt)) {
                    $_SESSION['pesan'] = "Bukti adm baru berhasil ditambahkan!";
                    $_SESSION['tipe'] = 'success';
                    header("Location: ../BERANDA/UTAMA.php?page=../ADM/bukti_adm.php&id_skema=" . $id_skema_post);
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                if (strpos($e->getMessage(), 'Data truncated for column') !== false) {
                    $message = "Error: Nilai tidak valid untuk database. Silakan pilih sesuai.";
                } else {
                    $message = "Gagal menambahkan Bukti: " . $e->getMessage();
                }
                $message_type = 'error';
            }
            mysqli_stmt_close($insert_stmt);
        } else {
            $message = "Gagal mempersiapkan statement: " . mysqli_error($koneksi);
            $message_type = 'error';
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
            <i class="fas fa-user-plus"></i>
            <div>
                <h1>Tambah Bukti Adm Baru</h1>
                <p>Tambahkan Bukti Adm ke dalam sistem</p>
            </div>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="" id="tambahUserForm">
                <input type="hidden" name="id_skema" value="<?= (int) $id_skema_form ?>">
                <div class="form-group">
                    <label for="bukti_adm" class="required">
                         Bukti Adm
                    </label>
                    <input type="text"
                           id="bukti_adm"
                           name="bukti_adm"
                           value="<?php echo htmlspecialchars($_POST['bukti_adm'] ?? ''); ?>"
                           required
                           maxlength="50"
                           placeholder="Masukkan Bukti Adm">

                </div>

                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../ADM/bukti_adm.php&id_skema=<?= (int) $id_skema_form ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tambahUserForm').addEventListener('submit', function(e) {
            const bukti_adm = document.getElementById('bukti_adm').value.trim();

            let errors = [];

            if (!bukti_adm) {
                errors.push('Bukti adm harus diisi');
            } else if (bukti_adm.length > 50) {
                errors.push('Bukti adm maksimal 50 karakter');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>
