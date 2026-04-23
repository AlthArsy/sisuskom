<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
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
    $bukti_dasar = mysqli_real_escape_string($koneksi, $_POST['bukti_dasar']);
;
    
   
    $errors = [];
    
    if (empty($bukti_dasar)) {
        $errors[] = "Bukti dasar harus diisi";
    }
    
    $check_sql = "SELECT id_bd FROM tb_bukti_dasar WHERE bukti_dasar = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $bukti_dasar);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Bukti Basar sudah digunakan, silakan pilih bukti dasar lain";
    }
    mysqli_stmt_close($check_stmt);
    
    
    if (empty($errors)) {
        
        if (empty($errors)) {
            $insert_sql = "INSERT INTO tb_bukti_dasar (bukti_dasar) VALUES (?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
            
            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "s", $bukti_dasar);
                
                try {
                    if (mysqli_stmt_execute($insert_stmt)) { 
                        $message = "Bukti dasar baru berhasil ditambahkan!";
                        $message_type = 'success';
                        $_SESSION['pesan'] = $message;
                        $_SESSION['tipe'] = $message_type;
                        header("Location: ../BERANDA/UTAMA.php?page=../DASAR/bukti_dasar.php");
                        exit();

                        
                        
                        $_POST = [];
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
                <h1>Tambah Bukti Dasar Baru</h1>
                <p>Tambahkan Bukti Dasar ke dalam sistem</p>
            </div>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" action="" id="tambahUserForm">
                <div class="form-group">
                    <label for="bukti_dasar" class="required">
                         Bukti Dasar
                    </label>
                    <input type="text" 
                           id="bukti_dasar" 
                           name="bukti_dasar" 
                           value="<?php echo htmlspecialchars($_POST['bukti_dasar'] ?? ''); ?>"
                           required
                           maxlength="50"
                           placeholder="Masukkan Bukti Dasar">

                </div>
                
                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../DASAR/bukti_dasar.php" class="btn btn-secondary">
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
            const bukti_dasar = document.getElementById('bukti_dasar').value.trim();
            
            let errors = [];
            
            if (!bukti_dasar) {
                errors.push('bukti dasar harus diisi');
            } else if (bukti_dasar.length > 50) {
                errors.push('bukti asar maksimal 50 karakter');
            }

            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
        
    </script>