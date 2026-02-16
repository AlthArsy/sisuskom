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
$skema_data = [];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT s.*, a.nama_asesor 
            FROM tb_skema s 
            LEFT JOIN tb_asesor a ON s.id_asesor = a.id_asesor 
            WHERE s.id_skema = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $skema_data = mysqli_fetch_assoc($result);
            
            if ($_SESSION['role'] === 'Asesor') {
                $id_asesor_login = $_SESSION['id_referensi'] ?? 0;
                
                if ($skema_data['id_asesor'] != $id_asesor_login) {
                    $message = "Anda tidak memiliki akses untuk mengubah skema ini.";
                    $message_type = 'error';
                    $skema_data = [];
                }
            }
        } else {
            $message = "Data skema tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $message = "ID skema tidak valid.";
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nomor_skema = mysqli_real_escape_string($koneksi, trim($_POST['nomor_skema']));
    $judul_skema = mysqli_real_escape_string($koneksi, trim($_POST['judul_skema']));
    $standar_kompetensi = mysqli_real_escape_string($koneksi, trim($_POST['standar_kompetensi_kerja']));
    
    $errors = [];
    
    if (empty($nomor_skema)) {
        $errors[] = "Nomor skema harus diisi";
    }
    
    if (empty($judul_skema)) {
        $errors[] = "Judul skema harus diisi";
    }
    
    if (empty($standar_kompetensi)) {
        $errors[] = "Standar kompetensi harus diisi";
    }
    
    $check_sql = "SELECT id_skema FROM tb_skema WHERE nomor_skema = ? AND id_skema != ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $nomor_skema, $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Nomor skema sudah digunakan";
    }
    mysqli_stmt_close($check_stmt);
    
    if (empty($errors)) {
        $update_sql = "UPDATE tb_skema SET nomor_skema = ?, judul_skema = ?, standar_kompetensi_kerja = ? WHERE id_skema = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sssi", $nomor_skema, $judul_skema, $standar_kompetensi, $id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['pesan'] = "Data skema berhasil diperbarui!";
                $_SESSION['tipe'] = 'success';
                header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
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
        <h1><i class="fas fa-edit"></i> Ubah Data Skema</h1>
        <p>Perbarui informasi skema sertifikasi</p>
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
    
    <?php if (!empty($skema_data)): ?>
        <div class="form-container">
            <form method="post" action="" id="editSkemaForm">
                <input type="hidden" name="id" value="<?php echo $skema_data['id_skema']; ?>">
                
                <div class="form-group">
                    <label for="nomor_skema" class="required">
                        <i class="fas fa-hashtag"></i> Nomor Skema
                    </label>
                    <input type="text" 
                           id="nomor_skema" 
                           name="nomor_skema" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($skema_data['nomor_skema']); ?>"
                           required
                           maxlength="100">
                    <span class="form-hint">Nomor unik identifikasi skema</span>
                </div>
                
                <div class="form-group">
                    <label for="judul_skema" class="required">
                        <i class="fas fa-heading"></i> Judul Skema
                    </label>
                    <input type="text" 
                           id="judul_skema" 
                           name="judul_skema" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($skema_data['judul_skema']); ?>"
                           required
                           maxlength="100">
                    <span class="form-hint">Nama lengkap skema sertifikasi</span>
                </div>
                
                <div class="form-group">
                    <label for="standar_kompetensi_kerja" class="required">
                        <i class="fas fa-clipboard-list"></i> Standar Kompetensi Kerja
                    </label>
                    <textarea 
                        id="standar_kompetensi_kerja" 
                        name="standar_kompetensi_kerja" 
                        class="form-control" 
                        required><?php echo htmlspecialchars($skema_data['standar_kompetensi_kerja']); ?></textarea>
                    <span class="form-hint">Deskripsi standar kompetensi yang digunakan</span>
                </div>
                
                <div class="form-group">
                    <label for="nama_asesor">
                        <i class="fas fa-user-tie"></i> Asesor
                    </label>
                    <input type="text" 
                           id="nama_asesor" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($skema_data['nama_asesor'] ?? '-'); ?>"
                           readonly>
                    <span class="form-hint">Asesor yang bertanggung jawab (tidak dapat diubah)</span>
                </div>
                
                <div class="button-group">
                    <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn btn-secondary">
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
            Data skema tidak ditemukan. Silakan pilih skema yang valid.
            <br><br>
            <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Skema
            </a>
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
    
    document.getElementById('editSkemaForm')?.addEventListener('submit', function(e) {
        const nomor_skema = document.getElementById('nomor_skema').value.trim();
        const judul_skema = document.getElementById('judul_skema').value.trim();
        const standar = document.getElementById('standar_kompetensi_kerja').value.trim();
        
        let errors = [];
        
        if (!nomor_skema) {
            errors.push('Nomor skema harus diisi');
        }
        
        if (!judul_skema) {
            errors.push('Judul skema harus diisi');
        }
        
        if (!standar) {
            errors.push('Standar kompetensi harus diisi');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
            return false;
        }
    });
</script>