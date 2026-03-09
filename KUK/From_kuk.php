<?php
ob_start();

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

$id_elemen = isset($_GET['id_elemen']) ? intval($_GET['id_elemen']) : 0;

if ($id_elemen > 0) {
    $query_skema = "
        SELECT 
            tb_kuk.no_kuk, 
            tb_kuk.kuk,
            tb_kuk.id_elemen,
            tb_asesor.nama_asesor
        FROM tb_kuk
        LEFT JOIN tb_elemen ON tb_kuk.id_elemen = tb_elemen.id_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        WHERE tb_kuk.id_kuk = ?
    ";
    $stmt_skema = mysqli_prepare($koneksi, $query_skema);
    mysqli_stmt_bind_param($stmt_skema, "i", $id_elemen);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $unit_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);
}


$message = '';
$message_type = '';
$unit_data = [];

if (isset($_GET['id_elemen'])) {
    $id_elemen = intval($_GET['id_elemen']);
    
    $sql = "SELECT * FROM tb_elemen WHERE id_elemen = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_elemen);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $unit_data = mysqli_fetch_assoc($result);
        } else {
            $message = "Data Elemen tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: ../BERANDA/UTAMA.php?page=../ELEMEN/elemen.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_elemen = intval($_POST['id_elemen']);
    $no_kuk = $_POST['no_kuk'] ?? [];
    $kuk = $_POST['kuk'] ?? [];
    
    $errors = [];
    $success_count = 0;
    
    $has_data = false;
    foreach ($no_kuk as $index => $no) {
        if (!empty(trim($no)) || !empty(trim($kuk[$index] ?? ''))) {
            $has_data = true;
            break;
        }
    }
    
    if (!$has_data) {
        $errors[] = "Minimal harus menambahkan satu KUK ";
    }
    
    if (empty($errors)) {
        foreach ($no_kuk as $index => $no) {
            $no = trim($no);
            $kuk_text = trim($kuk[$index] ?? '');
                
            if (empty($no) && empty($kuk_text)) {
                continue;
            }
            
            if (empty($no) || empty($kuk_text)) {
                $errors[] = "KUK #" . ($index + 1) . ": No dan Judul KUK harus diisi";
                continue;
            }
            
            $check_sql = "SELECT id_kuk FROM tb_kuk WHERE id_elemen = ? AND no_kuk = ?";
            $check_stmt = mysqli_prepare($koneksi, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "is", $id_elemen, $no);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $errors[] = "No KuK '$no' sudah ada dalam Elemen ini";
                mysqli_stmt_close($check_stmt);
                continue;
            }
            mysqli_stmt_close($check_stmt);
            
            $insert_sql = "INSERT INTO tb_kuk (id_elemen, no_kuk, kuk) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "iss", $id_elemen, $no, $kuk_text);

                if (mysqli_stmt_execute($insert_stmt)) {
                    $success_count++;
                } else {
                    $errors[] = "Gagal menyimpan KUK '$no': " . mysqli_stmt_error($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['pesan'] = "$success_count KUK berhasil ditambahkan!";
            $_SESSION['tipe'] = 'success';
            header("Location: UTAMA.php?page=../KUK/KUK.php&id_elemen=" . $unit_data['id_elemen'] ."");
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
    <h1>Tambah KUK</h1>
    <p>Tambahkan KUK untuk Elemen</p>
</div>

<?php if (!empty($unit_data  )): ?>
    <div class="skema-info">
        <h3><i class="fas fa-certificate"></i> Informasi Elemen</h3>
            <p><strong>No Elemen:</strong> <?php echo htmlspecialchars($unit_data['no_elemen']); ?></p>
            <p><strong>Nama Elemen:</strong> <?php echo htmlspecialchars($unit_data['nama_elemen']); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?> 
    
    <?php if (!empty($unit_data)): ?>
        <div class="form-container">
            <form method="post" action="" id="formUnit">
                <input type="hidden" name="id_elemen" value="<?php echo $unit_data['id_elemen']; ?>">
                
                <div class="unit-container" id="unitContainer">
                    <div class="unit-item" data-unit="1">
                        <div class="unit-item-header">
                            <span class="unit-number">Kuk #1</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="no_kuk_1" class="required">
                                     No KUK
                                </label>
                                <input type="text" 
                                       id="no_kuk_1" 
                                       name="no_kuk[]" 
                                       class="form-control"
                                       placeholder="Contoh: 1.1">
                                <span class="form-hint">No KUK</span>
                            </div>
                            <div class="form-group">
                                <label for="no_kuk_1" class="required">
                                     KUK
                                </label>
                                <textarea
                                    name="kuk[]"
                                    id="kuk_1"
                                    type="text"
                                    class="form-control"
                                    placeholder="Contoh: Konsep data dan struktur data diidentifikasi sesuai
dengan konteks permasalahan"></textarea>
                                <span class="form-hint">KUK</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addUnit()">
                    <i class="fas fa-plus"></i> Tambah KUK
                </button>
                
                <div class="button-group">
                    <a href="UTAMA.php?page=../KUK/KUK.php&id_elemen=<?= $unit_data['id_elemen'] ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan KUK
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="message error">
            <i class="fas fa-exclamation-triangle"></i> 
            Data KUK tidak ditemukan
            <br><br>
            <a href="../BERANDA/UTAMA.php?page=../ELEMEN/elemen.php" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Elemen
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    let kukCount = 1;
    
    function addUnit() {
        kukCount++;
        const container = document.getElementById('unitContainer');
        
        const kukHtml = `
            <div class="unit-item" data-unit="${kukCount}">
                <div class="unit-item-header">
                    <span class="unit-number">kuk #${kukCount}</span>
                    <button type="button" class="btn-remove" onclick="removeUnit(this)">
                        Hapus
                    </button> 
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">
                            No KUK
                        </label>
                        <input type="text" 
                               name="no_kuk[]" 
                               class="form-control">
                        <span class="form-hint">No KUK</span>
                    </div>
                    <div class="form-group">
                        <label class="required">
                            KUK
                        </label>
                            <textarea
                                name="kuk[]"
                                id="kuk_1"
                                type="text"
                                class="form-control"
                                placeholder="Contoh: Konsep data dan struktur data diidentifikasi sesuai
dengan konteks permasalahan"></textarea>
                        <span class="form-hint">KUK</span>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', kukHtml);
    }
    
    function removeUnit(button) {
        const kukItem = button.closest('.unit-item');
        kukItem.remove();
        
        const kuk = document.querySelectorAll('.unit-item');
        kuk.forEach((unit, index) => {
            const number = index + 1;
            unit.querySelector('.unit-number').innerHTML = `KUK #${number}`;
        });
        
        kukCount = units.length;
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