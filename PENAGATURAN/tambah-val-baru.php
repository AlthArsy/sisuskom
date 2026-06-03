<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp') {
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
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $noreg = mysqli_real_escape_string($koneksi, $_POST['noreg']);


    $errors = [];

    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }

    if (empty($noreg)) {
        $errors[] = "No Reg harus diisi"; 
    }

    if (strlen($username) > 64) {
        $errors[] = "Username maksimal 64 karakter";
    }

    if (strlen($noreg) > 255) {
        $errors[] = "No Reg terlalu panjang";
    }


    $check_sql = "SELECT id_validator FROM tb_validator WHERE username = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Username sudah digunakan, silakan pilih username lain";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {

        if (empty($errors)) {
            $insert_sql = "INSERT INTO tb_validator (username, noreg) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "ss", $username, $noreg);
                try {
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $message = "User baru berhasil ditambahkan!";
                        $message_type = 'success';


                        $_POST = [];
                    }
                } catch (mysqli_sql_exception $e) {

                    if (strpos($e->getMessage(), 'Data truncated for column') !== false) {
                        $message = "Error: Nilai role tidak valid untuk database. Silakan pilih role yang sesuai.";
                    } else {
                        $message = "Gagal menambahkan user: " . $e->getMessage();
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
                    <label for="username" class="required">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text"
                           id="username"
                           name="username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required
                           maxlength="64"
                           placeholder="Masukkan username">
                    <span class="form-hint">Username harus unik dan maksimal 64 karakter</span>
                </div>

                <div class="form-group">
                    <label for="noreg" class="required">
                        <i class="fas fa-lock"></i> No Reg
                    </label>
                    <input type="text"
                           id="noreg"
                           name="noreg"
                           value="<?php echo htmlspecialchars($_POST['noreg'] ?? ''); ?>"
                           required
                           maxlength="255"
                           placeholder="Masukkan No Reg">
                    <span class="form-hint">No Reg untuk validator</span>
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../MANAGEMENT/validator.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tambahUserForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const noreg = document.getElementById('noreg').value.trim();

            let errors = [];

            if (!username) {
                errors.push('Username harus diisi');
            } else if (username.length > 64) {
                errors.push('Username maksimal 64 karakter');
            }

            if (!noreg) {
                errors.push('No Reg harus diisi');
            } else if (noreg.length > 255) {
                errors.push('No Reg terlalu panjang');
            }


            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });

    </script>
