<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

$message = '';
$message_type = '';

$periode_list = [];
$id_periode_session = intval($_SESSION['id_periode'] ?? 0);
$current_periode_nama = '-';

if ($id_periode_session > 0) {
    $q_p = mysqli_query($koneksi, "SELECT tahun_ajaran FROM tb_periode WHERE id_periode = $id_periode_session");
    if ($q_p && $p_row = mysqli_fetch_assoc($q_p)) {
        $current_periode_nama = $p_row['tahun_ajaran'];
    }
}


$skema_list = [];
if ($id_periode_session > 0) {
    $q_skema = mysqli_query($koneksi, "
        SELECT s.id_skema, s.judul_skema
        FROM tb_skema s
        WHERE s.id_periode = $id_periode_session
    ");

    if ($q_skema) {
        while ($s = mysqli_fetch_assoc($q_skema)) {
            $skema_list[] = $s;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $hari = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $waktu = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $tuk = mysqli_real_escape_string($koneksi, $_POST['tuk']);
    $id_skema = isset($_POST['id_skema']) ? intval($_POST['id_skema']) : 0;
    $id_periode = $id_periode_session;

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

    if ($id_skema <= 0) {
        $errors[] = "Skema harus dipilih";
    }

    $check_sql = "SELECT id_jadwal FROM tb_jadwal WHERE hari = ? AND tanggal = ? AND waktu = ? AND tuk = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ssss", $hari, $tanggal, $waktu, $tuk);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Jadwal pada waktu dan tempat tersebut sudah ada";
    }
    mysqli_stmt_close($check_stmt);


    if (empty($errors)) {
            $insert_sql = "INSERT INTO tb_jadwal (hari, tanggal, waktu, tuk, id_skema, id_periode) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "ssssii", $hari, $tanggal, $waktu, $tuk, $id_skema, $id_periode);

                try {
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $message = "Jadwal baru berhasil ditambahkan!";
                        $message_type = 'success';
                        $_POST = [];
                    }
                } catch (mysqli_sql_exception $e) {
                    $message = "Gagal menambahkan jadwal: " . $e->getMessage();
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
                <h1>Tambah Jadwal Baru</h1>
                <p>Tambahkan jadwal baru ke dalam sistem</p>
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
                    <label for="hari" class="required">
                        <i class="fas fa-calendar-day"></i> Hari
                    </label>
                <select id="hari" name="hari" required>
                    <option value="">Pilih Hari</option>
                    <option value="Senin" <?php if(isset($_GET['hari']) && $_GET['hari']=='Senin'){echo 'selected';} ?>>Senin</option>
                    <option value="Selasa" <?php if(isset($_GET['hari']) && $_GET['hari']=='Selasa'){echo 'selected';} ?>>Selasa</option>
                    <option value="Rabu" <?php if(isset($_GET['hari']) && $_GET['hari']=='Rabu'){echo 'selected';} ?>>Rabu</option>
                    <option value="Kamis" <?php if(isset($_GET['hari']) && $_GET['hari']=='Kamis'){echo 'selected';} ?>>Kamis</option>
                    <option value="Jumat" <?php if(isset($_GET['hari']) && $_GET['hari']=='Jumat'){echo 'selected';} ?>>Jumat</option>
                    <option value="Sabtu" <?php if(isset($_GET['hari']) && $_GET['hari']=='Sabtu'){echo 'selected';} ?>>Sabtu</option>
                    <option value="Ahad" <?php if(isset($_GET['hari']) && $_GET['hari']=='Ahad'){echo 'selected';} ?>>Ahad</option>
                </select>
                    <span class="form-hint">Hari harus unik dan maksimal 50 karakter</span>
                </div>

                <div class="form-group">
                    <label for="tanggal" class="required">
                        <i class="fas fa-calendar"></i> Tanggal
                    </label>
                    <input type="date"
                           id="tanggal"
                           name="tanggal"
                           value="<?php echo htmlspecialchars($_POST['tanggal'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="waktu" class="required">
                        <i class="fas fa-clock"></i> Waktu
                    </label>
                    <input type="time"
                           id="waktu"
                           name="waktu"
                           value="<?php echo htmlspecialchars($_POST['waktu'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="tuk" class="required">
                        <i class="fas fa-building"></i> TUK
                    </label>
                <select id="tuk" name="tuk" required>
                    <option value="">Pilih TUK</option>
                    <option value="Sewaktu" <?php if(isset($_GET['tuk']) && $_GET['tuk']=='Sewaktu'){echo 'selected';} ?>>Sewaktu</option>
                    <option value="Tempat Kerja" <?php if(isset($_GET['tuk']) && $_GET['tuk']=='Tempat Kerja'){echo 'selected';} ?>>Tempat Kerja</option>
                    <option value="Mandiri" <?php if(isset($_GET['tuk']) && $_GET['tuk']=='Mandiri'){echo 'selected';} ?>>Mandiri</option>
                </select>
                    <span class="form-hint">Tempat uji kompetensi harus unik dan maksimal 100 karakter</span>
                </div>

                <div class="form-group">
                    <label for="id_skema" class="required">
                        <i class="fas fa-book"></i> Skema
                    </label>
                    <select id="id_skema" name="id_skema" required>
                        <option value="">Pilih Skema</option>
                        <?php foreach ($skema_list as $skema): ?>
                            <option value="<?php echo $skema['id_skema']; ?>" <?php echo (isset($_POST['id_skema']) && $_POST['id_skema'] == $skema['id_skema']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($skema['judul_skema']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-hint">Pilih skema yang tersedia untuk periode ini</span>
                </div>

                <div class="form-group">
                    <label for="id_periode" class="required">
                        <i class="fas fa-calendar"></i> Tahun Ajaran
                    </label>
                    <input type="text" 
                           id="id_periode" 
                           value="<?php echo htmlspecialchars($current_periode_nama); ?>" 
                           readonly 
                           style="background-color: #e9ecef; cursor: not-allowed;">
                    <span class="form-hint">Periode otomatis ditentukan oleh sesi login Anda</span>
                </div>

                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../Jadwal/jadwal.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> kembali
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Jadwal
                    </button>
                </div>
                </form>
                </div>
                </div>


    <script>
        document.getElementById('tambahUserForm').addEventListener('submit', function(e) {
            const hari = document.getElementById('hari').value.trim();
            const tanggal = document.getElementById('tanggal').value.trim();
            const waktu = document.getElementById('waktu').value.trim();
            const tuk = document.getElementById('tuk').value;
            const id_skema = document.getElementById('id_skema').value;

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
                errors.push('TUK harus dipilih');
            }
            if (!id_skema) {
                errors.push('Skema harus dipilih');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });

    </script>
