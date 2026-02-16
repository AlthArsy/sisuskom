<?php
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
$id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;

if ($id_skema > 0) {
    $query_skema = "
        SELECT 
            tb_unit_kompetensi.kode_unit, 
            tb_unit_kompetensi.judul_unit,
            tb_unit_kompetensi.id_skema,
            tb_asesor.nama_asesor
        FROM tb_unit_kompetensi
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        WHERE tb_unit_kompetensi.id_unit = ?
    ";
    $stmt_skema = mysqli_prepare($koneksi, $query_skema);
    mysqli_stmt_bind_param($stmt_skema, "i", $id_skema);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $skema_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);
}

$message = '';
$message_type = '';
$skema_data = [];

if (isset($_GET['id_skema'])) {
    $id_skema = intval($_GET['id_skema']);
    
    $sql = "SELECT * FROM tb_skema WHERE id_skema = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_skema);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $skema_data = mysqli_fetch_assoc($result);
        } else {
            $message = "Data skema tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_skema = intval($_POST['id_skema']);
    $kode_unit = $_POST['kode_unit'] ?? [];
    $judul_unit = $_POST['judul_unit'] ?? [];
    
    $errors = [];
    $success_count = 0;
    
    $has_data = false;
    foreach ($kode_unit as $index => $kode) {
        if (!empty(trim($kode)) || !empty(trim($judul_unit[$index] ?? ''))) {
            $has_data = true;
            break;
        }
    }
    
    if (!$has_data) {
        $errors[] = "Minimal harus menambahkan satu unit kompetensi";
    }
    
    if (empty($errors)) {
        foreach ($kode_unit as $index => $kode) {
            $kode = trim($kode);
            $judul = trim($judul_unit[$index] ?? '');
            
            if (empty($kode) && empty($judul)) {
                continue;
            }
            
            if (empty($kode) || empty($judul)) {
                $errors[] = "Unit #" . ($index + 1) . ": Kode dan Judul  id_unit harus diisi"; 
                continue;
            }
            
            $check_sql = "SELECT id_unit FROM tb_unit_kompetensi WHERE id_skema = ? AND kode_unit = ?";
            $check_stmt = mysqli_prepare($koneksi, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "is", $id_skema, $kode);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $errors[] = "Kode unit '$kode' sudah ada dalam skema ini";
                mysqli_stmt_close($check_stmt);
                continue;
            }
            mysqli_stmt_close($check_stmt);
            
            $insert_sql = "INSERT INTO tb_unit_kompetensi (id_skema, kode_unit, judul_unit) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
            
            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "iss", $id_skema, $kode, $judul);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $success_count++;
                } else {
                    $errors[] = "Gagal menyimpan unit '$kode': " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($insert_stmt);
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['pesan'] = "$success_count unit kompetensi berhasil ditambahkan!";
            $_SESSION['tipe'] = 'success';
            header("Location: UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=" . $skema_data['id_skema'] ."");
            exit();
        }
    }
    
    if (!empty($errors)) {
        $message = implode("<br>", $errors);
        $message_type = 'error';
    }
}
?>
<link rel="stylesheet" href="../assets/CSS/From_UEK.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="unit-container">
<div class="unit-header">
    <h1>Tambah Unit Kompetensi</h1>
    <p>Tambahkan unit kompetensi untuk skema sertifikasi</p>
</div>

<?php if (!empty($skema_data)): ?>
    <div class="skema-info">
        <h3><i class="fas fa-certificate"></i> Informasi Skema</h3>
            <p><strong>Nomor Skema:</strong> <?php echo htmlspecialchars($skema_data['nomor_skema']); ?></p>
            <p><strong>Judul Skema:</strong> <?php echo htmlspecialchars($skema_data['judul_skema']); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($skema_data)): ?>
        <div class="form-container">
            <form method="post" action="" id="formUnit">
                <input type="hidden" name="id_skema" value="<?php echo $skema_data['id_skema']; ?>">
                
                <div class="unit-container" id="unitContainer">
                    <div class="unit-item" data-unit="1">
                        <div class="unit-item-header">
                            <span class="unit-number"><i class="fas fa-list-ol"></i> Unit #1</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_unit_1" class="required">
                                    Kode Unit
                                </label>
                                <input type="text" 
                                       id="kode_unit_1" 
                                       name="kode_unit[]" 
                                       class="form-control" 
                                       placeholder="Contoh: J.620100.004.02"
                                       maxlength="100">
                                <span class="form-hint">Kode unik unit kompetensi</span>
                            </div>
                            <div class="form-group">
                                <label for="judul_unit_1" class="required">
                                    Judul Unit
                                </label>
                                <input type="text" 
                                       id="judul_unit_1" 
                                       name="judul_unit[]" 
                                       class="form-control" 
                                       placeholder="Contoh: Menggunakan Struktur Data">
                                <span class="form-hint">unit kompetensi</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addUnit()">
                    <i class="fas fa-plus"></i> Tambah Unit Lagi
                </button>
                
                <div class="button-group">
                    <a href="UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=<?= $skema_data['id_skema'] ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal 
                    </a>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua Unit
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="message error">
            <i class="fas fa-exclamation-triangle"></i> 
            Data skema tidak ditemukan.
            <br><br>
            <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Skema
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    let unitCount = 1;
    
    function addUnit() {
        unitCount++;
        const container = document.getElementById('unitContainer');
        
        const unitHtml = `
            <div class="unit-item" data-unit="${unitCount}">
                <div class="unit-item-header">
                    <span class="unit-number"><i class="fas fa-list-ol"></i> Unit #${unitCount}</span>
                    <button type="button" class="btn-remove" onclick="removeUnit(this)">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">
                            <i class="fas fa-barcode"></i> Kode Unit
                        </label>
                        <input type="text" 
                               name="kode_unit[]" 
                               class="form-control" 
                               placeholder="Contoh: J.620100.004.02"
                               maxlength="100">
                        <span class="form-hint">Kode unik unit kompetensi</span>
                    </div>
                    <div class="form-group">
                        <label class="required">
                            <i class="fas fa-heading"></i> Judul Unit
                        </label>
                        <input type="text" 
                               name="judul_unit[]" 
                               class="form-control" 
                               placeholder="Contoh: Menggunakan Struktur Data">
                        <span class="form-hint">unit kompetensi</span>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', unitHtml);
    }
    
    function removeUnit(button) {
        const unitItem = button.closest('.unit-item');
        unitItem.remove();
        
        const units = document.querySelectorAll('.unit-item');
        units.forEach((unit, index) => {
            const number = index + 1;
            unit.querySelector('.unit-number').innerHTML = `<i class="fas fa-list-ol"></i> Unit #${number}`;
        });
        
        unitCount = units.length;
    }
    
    setTimeout(function() {
        const messages = document.querySelectorAll('.message');
        messages.forEach(message => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(() => message.remove(), 500);
        });
    }, 5000);
</script>