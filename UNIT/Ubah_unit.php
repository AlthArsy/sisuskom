<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}

$message = '';
$message_type = ''; 
$unit_data = [];

if (isset($_GET['id'])) {
    $id_unit = intval($_GET['id']);
    
    $sql = "SELECT 
                uk.*, 
                s.nomor_skema, 
                s.judul_skema,
                s.id_asesor
            FROM tb_unit_kompetensi uk
            LEFT JOIN tb_skema s ON uk.id_skema = s.id_skema
            WHERE uk.id_unit = ?";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_unit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $unit_data = mysqli_fetch_assoc($result);
            
            if ($_SESSION['role'] === 'Asesor') {
                $id_asesor_login = $_SESSION['id_asesor'] ?? 0;
                
                if ($unit_data['id_asesor'] != $id_asesor_login) {
                    $message = "Anda tidak memiliki akses untuk mengubah unit ini.";
                    $message_type = 'error';
                    $unit_data = [];
                }
            }
        } else {
            $message = "Data unit kompetensi tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $message = "ID unit tidak valid.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id_unit = intval($_POST['id_unit']);
    $kode_unit = mysqli_real_escape_string($koneksi, trim($_POST['kode_unit']));
    $judul_unit = mysqli_real_escape_string($koneksi, trim($_POST['judul_unit']));
    
    $errors = [];
    
    if (empty($kode_unit)) {
        $errors[] = "Kode unit harus diisi";
    }
    
    if (empty($judul_unit)) {
        $errors[] = "Judul unit harus diisi";
    }
    
    $check_sql = "SELECT id_unit FROM tb_unit_kompetensi WHERE kode_unit = ? AND id_unit != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $kode_unit, $id_unit);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Kode unit sudah digunakan";
    }
    mysqli_stmt_close($check_stmt);
    
    if (empty($errors)) {
        $update_sql = "UPDATE tb_unit_kompetensi SET kode_unit = ?, judul_unit = ? WHERE id_unit = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "ssi", $kode_unit, $judul_unit, $id_unit);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['pesan'] = "Data unit kompetensi berhasil diperbarui!";
                $_SESSION['tipe'] = 'success';
                
                $id_skema = intval($_POST['id_skema']);
                header("Location: ../BERANDA/UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=" . $id_skema);
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
        <h1>Ubah Unit Kompetensi</h1>
        <p>Perbarui informasi unit kompetensi</p>
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
    
    <?php if (!empty($unit_data)): ?>
        <div class="form-container">
            <div class="skema-info-box">
                <h3>Informasi Skema</h3>
                <p><strong>Nomor Skema:</strong> <?php echo htmlspecialchars($unit_data['nomor_skema']); ?></p>
                <p><strong>Judul Skema:</strong> <?php echo htmlspecialchars($unit_data['judul_skema']); ?></p>
            </div>
            
            <form method="post" action="" id="editUnitForm">
                <input type="hidden" name="id_unit" value="<?php echo $unit_data['id_unit']; ?>">
                <input type="hidden" name="id_skema" value="<?php echo $unit_data['id_skema']; ?>">
                
                <div class="form-group">
                    <label for="kode_unit" class="required">
                         Kode Unit
                    </label>
                    <input type="text" 
                           id="kode_unit" 
                           name="kode_unit" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($unit_data['kode_unit']); ?>"
                           required
                           maxlength="50">
                    <span class="form-hint">Kode unik identifikasi unit kompetensi</span>
                </div>
                
                <div class="form-group">
                    <label for="judul_unit" class="required">
                         Judul Unit Kompetensi
                    </label>
                    <textarea 
                        id="judul_unit" 
                        name="judul_unit" 
                        class="form-control" 
                        required
                        rows="3"><?php echo htmlspecialchars($unit_data['judul_unit']); ?></textarea>
                    <span class="form-hint">Nama lengkap unit kompetensi</span>
                </div>
                
                <div class="button-group">
                    <a href="../BERANDA/UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=<?php echo $unit_data['id_skema']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
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
            Data unit kompetensi tidak ditemukan. Silakan pilih unit yang valid.
            <br><br>
            <!-- <a href="../BERANDA/UTAMA.php?page=../UNIT/unit_kompetensi.php" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
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
    
    document.getElementById('editUnitForm')?.addEventListener('submit', function(e) {
        const kode_unit = document.getElementById('kode_unit').value.trim();
        const judul_unit = document.getElementById('judul_unit').value.trim();
        
        let errors = [];
        
        if (!kode_unit) {
            errors.push('Kode unit harus diisi');
        }
        
        if (!judul_unit) {
            errors.push('Judul unit harus diisi');
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