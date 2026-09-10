<?php
//megic , dari validator ini an
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp' && $_SESSION['role'] !== 'Admin_utm') {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

$message = '';
$message_type = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $tahun_ajaran = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran']);


    $errors = [];

    if (empty($tahun_ajaran)) {
        $errors[] = "Tahun Ajaran harus diisi";
    }

    if (strlen($tahun_ajaran) > 64) {
        $errors[] = "Tahun Ajaran maksimal 64 karakter";
    }


    $check_sql = "SELECT id_periode FROM tb_periode WHERE tahun_ajaran = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $tahun_ajaran);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Tahun Ajaran sudah digunakan, silakan pilih tahun ajaran lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {

        if (empty($errors)) {
            $insert_sql = "INSERT INTO tb_periode (tahun_ajaran) VALUES (?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "s", $tahun_ajaran);
                try {
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $message = "Tahun ajaran baru berhasil ditambahkan!";
                        $message_type = 'success';


                        $_POST = [];
                    }
                } catch (mysqli_sql_exception $e) {

                    if (strpos($e->getMessage(), 'Data truncated for column') !== false) {
                        $message = "Error: Nilai role tidak valid untuk database. Silakan pilih role yang sesuai.";
                    } else {
                        $message = "Gagal menambahkan tahun ajaran: " . $e->getMessage();
                    }
                    $message_type = 'error';
                }
                mysqli_stmt_close($insert_stmt);
            } else {
                $message = "Gagal mempersiapkan statement: " . mysqli_error($koneksi);
                $message_type = 'error';
            }
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
                <h1>Tambah Validator Baru</h1>
                <p>Tambahkan validator baru ke dalam sistem</p>
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

        <div class="form-container">
            <form method="post" action="" id="tambahUserForm">
                <div class="form-group">
                    <label for="tahun_ajaran" class="required">
                        <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                    </label>
                    <input type="text"
                           id="tahun_ajaran"
                           name="tahun_ajaran"
                           value="<?php echo htmlspecialchars($_POST['tahun_ajaran'] ?? ''); ?>"
                           required
                           maxlength="40"
                           placeholder="Masukkan tahun ajaran">
                    <span class="form-hint">Tahun ajaran harus unik dan maksimal 40 karakter</span>
                </div>
                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../Periode/periode.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tahun Ajaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tambahUserForm').addEventListener('submit', function(e) {
            const tahun_ajaran = document.getElementById('tahun_ajaran').value.trim();

            let errors = [];

            if (!tahun_ajaran) {
                errors.push('Tahun ajaran harus diisi');
            } else if (tahun_ajaran.length > 40) {
                errors.push('Tahun ajaran maksimal 40 karakter');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });

    </script>
