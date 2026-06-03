<?php
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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($_SESSION['role'] === 'Admin_utm' || $_SESSION['role'] === 'Admin_lsp') {
    $query = "
        SELECT
            tb_skema.id_skema,
            tb_skema.nomor_skema,
            tb_skema.judul_skema,
            tb_skema.standar_kompetensi_kerja,
            tb_asesor.nama_asesor,
            COUNT(tb_unit_kompetensi.id_unit) as jumlah_unit
        FROM tb_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        LEFT JOIN tb_unit_kompetensi ON tb_skema.id_skema = tb_unit_kompetensi.id_skema

    ";

    if (!empty($search)) {
        $query .= " WHERE tb_skema.nomor_skema LIKE ?";
    }

    $query .= " GROUP BY tb_skema.id_skema ORDER BY tb_skema.id_skema DESC";

    if (!empty($search)) {
        $stmt = mysqli_prepare($koneksi, $query);
        $search_param = '%' . $search . '%';
        mysqli_stmt_bind_param($stmt, "s", $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($koneksi, $query);
    }

} else if ($_SESSION['role'] === 'Asesor') {
    if (!isset($_SESSION['id_asesor'])) {
        $username = $_SESSION['username'];
        $get_asesor = "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = ?";
        $stmt_asesor = mysqli_prepare($koneksi, $get_asesor);
        mysqli_stmt_bind_param($stmt_asesor, "s", $username);
        mysqli_stmt_execute($stmt_asesor);
        $result_asesor = mysqli_stmt_get_result($stmt_asesor);

        if ($row_asesor = mysqli_fetch_assoc($result_asesor)) {
            $_SESSION['id_asesor'] = $row_asesor['id_asesor'];
        } else {
            $_SESSION['id_asesor'] = 0;
        }
        mysqli_stmt_close($stmt_asesor);
    }

    $id_asesor_login = intval($_SESSION['id_asesor']);

    if ($id_asesor_login > 0) {
        $query = "
            SELECT
                tb_skema.id_skema,
                tb_skema.nomor_skema,
                tb_skema.judul_skema,
                tb_skema.standar_kompetensi_kerja,
                tb_asesor.nama_asesor,
                COUNT(tb_unit_kompetensi.id_unit) as jumlah_unit
            FROM tb_skema
            LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
            LEFT JOIN tb_unit_kompetensi ON tb_skema.id_skema = tb_unit_kompetensi.id_skema
            WHERE tb_skema.id_asesor = ?
        ";

        if (!empty($search)) {
            $query .= " AND tb_skema.nomor_skema LIKE ?";
        }

        $query .= " GROUP BY tb_skema.id_skema ORDER BY tb_skema.id_skema DESC";

        $stmt = mysqli_prepare($koneksi, $query);
        if (!empty($search)) {
            $search_param = '%' . $search . '%';
            mysqli_stmt_bind_param($stmt, "is", $id_asesor_login, $search_param);
        } else {
            mysqli_stmt_bind_param($stmt, "i", $id_asesor_login);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($koneksi, "SELECT * FROM tb_skema WHERE 1=0");
    }
}


?>
<link rel="stylesheet" href="../assets/CSS/list_skema.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="s-container">
    <div class="header-container">
        <h2 class="jdm">Kelola Skema Sertifikasi</h2>
        <?php if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Asesor'): ?>
            <a href="UTAMA.php?page=../SKEMA/Form_Skema.php" class="btn-tambah">
                <i class="fas fa-plus"></i> Tambah Skema
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="message <?php echo $_SESSION['tipe']; ?>">
            <?php
                echo htmlspecialchars($_SESSION['pesan']);
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        </div>
    <?php endif; ?>
    <form method="get" action="" class="cari">
        <?php if (isset($_GET['page'])): ?>
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
        <?php endif; ?>

        <div class="cari-field">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input
                type="text"
                name="search"
                placeholder="Cari nomor skema..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>

        <div class="cari-actions">
            <button type="submit" class="btn-cari">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : $_SERVER['PHP_SELF']; ?>"
                   class="btn-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
        <?php if (!empty($search)): ?>
        <div class="filter-info">
            <strong>Filter aktif:</strong>
            nomor skema: &ldquo;<?php echo htmlspecialchars($search); ?>&rdquo;
        </div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nomor Skema</th>
                <th>Judul Skema</th>
                <th>Standar Kompetensi Kerja</th>
                <th>Asesor</th>
                <th style="width: 50px;">Aksi</th>
            </tr>
        </thead>
            <tbody>
            <?php if (isset($result) && mysqli_num_rows($result) > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                    $jumlah_unit = intval($row['jumlah_unit'] ?? 0);
            ?>
                <tr>
                    <td data-label='NO'><?= $no++ ?></td>
                    <td data-label='Nomor Skema'><?= htmlspecialchars($row['nomor_skema']) ?></td>
                    <td data-label='Judul Skema'><?= htmlspecialchars($row['judul_skema']) ?></td>
                    <td data-label='Standar Kompetensi Kerja'><?= htmlspecialchars($row['standar_kompetensi_kerja']) ?></td>
                    <td data-label='Asesor'><?= htmlspecialchars($row['nama_asesor'] ?? '-') ?></td>
                    <td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../SKEMA/Ubah_Skema.php&id=<?= $row['id_skema'] ?>' class='btn-ubah'>
                            Ubah
                        </a>
                        <a href='../SKEMA/Hapus_Skema.php?id=<?= $row['id_skema'] ?>'
                           class='btn-hapus'
                           onclick="return confirm('Yakin ingin menghapus skema ini?');">
                            Hapus
                        </a>

                        <?php if ($jumlah_unit == 0): ?>
                            <a href='UTAMA.php?page=../UNIT/From_unit_kompetensi.php&id_skema=<?= $row['id_skema'] ?>'
                               class='btn-unit-empty'>
                                <i class='fas fa-plus'></i> Tambah Unit
                            </a>
                        <?php else: ?>
                            <a href='UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=<?= $row['id_skema'] ?>'
                               class='btn-lihat-unit'
                               title='Lihat Unit Kompetensi'>
                                Lihat Unit
                            </a>
                            <a href='UTAMA.php?page=../DASAR/bukti_dasar.php&id_skema=<?= $row['id_skema'] ?>' class='btn-bukti'>
                                Bukti Dasar
                            </a>
                            <a href='UTAMA.php?page=../ADM/bukti_adm.php&id_skema=<?= $row['id_skema'] ?>' class='btn-bukti'>
                                Bukti Adm
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;">
                        <?php if (!empty($search)): ?>
                            Tidak ada data skema dengan nomor "<?= htmlspecialchars($search) ?>".
                        <?php elseif ($_SESSION['role'] === 'Asesor'): ?>
                            Anda belum memiliki skema sertifikasi.
                        <?php else: ?>
                            Belum ada data skema.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
    </table>
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.cari');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
        });
    }
});
</script>

<?php
mysqli_close($koneksi);
?>
