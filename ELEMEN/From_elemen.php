<?php
ob_start();

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

$id_unit = isset($_GET['id_unit']) ? intval($_GET['id_unit']) : 0;

if ($id_unit > 0) {
    $query_skema = "
        SELECT
            tb_elemen.no_elemen,
            tb_elemen.nama_elemen,
            tb_elemen.id_unit,
            tb_asesor.nama_asesor
        FROM tb_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        WHERE tb_elemen.id_elemen = ?
    ";
    $stmt_skema = mysqli_prepare($koneksi, $query_skema);
    mysqli_stmt_bind_param($stmt_skema, "i", $id_unit);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $unit_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);
}

$message = '';
$message_type = '';
$unit_data = [];

if (isset($_GET['id_unit'])) {
    $id_unit = intval($_GET['id_unit']);

    $sql = "SELECT * FROM tb_unit_kompetensi WHERE id_unit = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_unit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $unit_data = mysqli_fetch_assoc($result);
        } else {
            $message = "Data Unit tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: ../BERANDA/UTAMA.php?page=../UNIT/unit_kompetensi.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_unit = intval($_POST['id_unit']);
    $no_elemen = $_POST['no_elemen'] ?? [];
    $nama_elemen = $_POST['nama_elemen'] ?? [];

    $errors = [];
    $success_count = 0;

    $has_data = false;
    foreach ($no_elemen as $index => $no) {
        if (!empty(trim($no)) || !empty(trim($nama_elemen[$index] ?? ''))) {
            $has_data = true;
            break;
        }
    }

    if (!$has_data) {
        $errors[] = "Minimal harus menambahkan satu Elemen";
    }

    if (empty($errors)) {
        foreach ($no_elemen as $index => $no) {
            $no = trim($no);
            $nama = trim($nama_elemen[$index] ?? '');

            if (empty($no) && empty($nama)) {
                continue;
            }

            if (empty($no) || empty($nama)) {
                $errors[] = "Elemen #" . ($index + 1) . ": No dan Judul Elemen harus diisi";
                continue;
            }

            $check_sql = "SELECT id_elemen FROM tb_elemen WHERE id_unit = ? AND no_elemen = ?";
            $check_stmt = mysqli_prepare($koneksi, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "is", $id_unit, $no);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $errors[] = "No Elemen '$no' sudah ada dalam Unit ini";
                mysqli_stmt_close($check_stmt);
                continue;
            }
            mysqli_stmt_close($check_stmt);

            $insert_sql = "INSERT INTO tb_elemen (id_unit, no_elemen, nama_elemen) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "iss", $id_unit, $no, $nama);

                if (mysqli_stmt_execute($insert_stmt)) {
                    $success_count++;
                } else {
                    $errors[] = "Gagal menyimpan unit '$no': " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($insert_stmt);
            }
        }

        if ($success_count > 0) {
            $_SESSION['pesan'] = "$success_count elemen kompetensi berhasil ditambahkan!";
            $_SESSION['tipe'] = 'success';
            header("Location: UTAMA.php?page=../ELEMEN/elemen.php&id_unit=" . $unit_data['id_unit'] ."");
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
    <h1>Tambah Elemen</h1>
    <p>Tambahkan Elemen Untuk Unit Kompetensi</p>
</div>

<?php if (!empty($unit_data  )): ?>
    <div class="skema-info">
        <h3><i class="fas fa-certificate"></i> Informasi Uint</h3>
            <p><strong>Kode Unit:</strong> <?php echo htmlspecialchars($unit_data['kode_unit']); ?></p>
            <p><strong>Judul Unit:</strong> <?php echo htmlspecialchars($unit_data['judul_unit']); ?></p>
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
                <input type="hidden" name="id_unit" value="<?php echo $unit_data['id_unit']; ?>">

                <div class="unit-container" id="unitContainer">
                    <div class="unit-item" data-unit="1">
                        <div class="unit-item-header">
                            <span class="unit-number">Elemen #1</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="no_elemen_1" class="required">
                                    No Elemen
                                </label>
                                <input type="text"
                                       id="no_elemen_1"
                                       name="no_elemen[]"
                                       class="form-control"
                                       placeholder="Contoh: 1">
                                <span class="form-hint">No Elemen</span>
                            </div>
                            <div class="form-group">
                                <label for="nama_elemen_1" class="required">
                                    Nama Elemen
                                </label>
                                <input type="text"
                                       id="nama_elemen_1"
                                       name="nama_elemen[]"
                                       class="form-control"
                                       placeholder="Contoh: Mengidentifikasi konsep data dan struktur data">
                                <span class="form-hint">Nama Elemen  </span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-more" onclick="addUnit()">
                    <i class="fas fa-plus"></i> Tambah Elemen Lagi
                </button>

                <div class="button-group">
                    <a href="UTAMA.php?page=../ELEMEN/elemen.php&id_unit=<?= $unit_data['id_unit'] ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua Elemen
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="message error">
            <i class="fas fa-exclamation-triangle"></i>
            Data Unit tidak ditemukan.
            <br><br>
            <a href="../BERANDA/UTAMA.php?page=../UNIT/unit_kompetensi.php" class="btn btn-secondary" style="padding: 10px 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar unit
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    let elemenCount = 1;

    function addUnit() {
        elemenCount++;
        const container = document.getElementById('unitContainer');

        const elemenHtml = `
            <div class="unit-item" data-unit="${elemenCount}">
                <div class="unit-item-header">
                    <span class="unit-number">Elemen #${elemenCount}</span>
                    <button type="button" class="btn-remove" onclick="removeUnit(this)">
                        Hapus
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">
                            No Elemen
                        </label>
                        <input type="text"
                               name="no_elemen[]"
                               class="form-control"
                               placeholder="Contoh: 1"
                               maxlength="100">
                        <span class="form-hint">No Elemen</span>
                    </div>
                    <div class="form-group">
                        <label class="required">
                            Nama Elemen
                        </label>
                        <input type="text"
                               name="nama_elemen[]"
                               class="form-control"
                               placeholder="Contoh: Mengidentifikasi konsep data dan struktur data">
                        <span class="form-hint">Nama Elemen</span>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', elemenHtml);
    }

    function removeUnit(button) {
        const elemenItem = button.closest('.unit-item');
        elemenItem.remove();

        const elemens = document.querySelectorAll('.unit-item');
        elemens.forEach((unit, index) => {
            const number = index + 1;
            unit.querySelector('.unit-number').innerHTML = `Elemen #${number}`;
        });

        elemenCount = units.length;
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
