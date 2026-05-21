<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor', 'Asesi'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Asesor') {
    $query = "
        SELECT
            tb_isi_bukti_dasar.id_isi_bd,
            tb_isi_bukti_dasar.id_bd,
            tb_bukti_dasar.bukti_dasar,
            tb_isi_bukti_dasar.kondisi,
            tb_asesi.nama_asesi
        FROM tb_isi_bukti_dasar
        LEFT JOIN tb_asesi ON tb_isi_bukti_dasar.id_asesi = tb_asesi.id_asesi
        LEFT JOIN tb_bukti_dasar ON tb_isi_bukti_dasar.id_bd = tb_bukti_dasar.id_bd
    ";

    $query .= " GROUP BY tb_isi_bukti_dasar.id_isi_bd ORDER BY tb_isi_bukti_dasar.id_isi_bd DESC";

    if (!empty($search)) {
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($koneksi, $query);
    }

} else if ($_SESSION['role'] === 'Asesi') {
    if (!isset($_SESSION['id_asesi'])) {
        $username = $_SESSION['username'];
        $get_asesor = "SELECT id_asesi FROM tb_asesi WHERE nama_asesi = ?";
        $stmt_asesor = mysqli_prepare($koneksi, $get_asesor);
        mysqli_stmt_bind_param($stmt_asesor, "s", $username);
        mysqli_stmt_execute($stmt_asesor);
        $result_asesor = mysqli_stmt_get_result($stmt_asesor);

        if ($row_asesor = mysqli_fetch_assoc($result_asesor)) {
            $_SESSION['id_asesi'] = $row_asesor['id_asesi'];
        } else {
            $_SESSION['id_asesi'] = 0;
        }
        mysqli_stmt_close($stmt_asesor);
    }

    $id_asesi_login = intval($_SESSION['id_asesi']);

    if ($id_asesi_login > 0) {
        $query = "
            SELECT
                tb_isi_bukti_dasar.id_isi_bd,
                tb_isi_bukti_dasar.id_bd,
                tb_bukti_dasar.bukti_dasar,
                tb_isi_bukti_dasar.kondisi,
                tb_asesi.nama_asesi
            FROM tb_isi_bukti_dasar
            LEFT JOIN tb_asesi ON tb_isi_bukti_dasar.id_asesi = tb_asesi.id_asesi
            LEFT JOIN tb_bukti_dasar ON tb_isi_bukti_dasar.id_bd = tb_bukti_dasar.id_bd
            WHERE tb_isi_bukti_dasar.id_asesi = ?
        ";

        $query .= " GROUP BY tb_isi_bukti_dasar.id_isi_bd ORDER BY tb_isi_bukti_dasar.id_isi_bd DESC";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_asesi_login);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($koneksi, "SELECT * FROM tb_isi_bukti_dasar WHERE 1=0");
    }
}
?>
<link rel="stylesheet" href="../assets/CSS/list_skema.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="s-container">
    <div class="header-container">
        <h2 class="jdm">Isi Bukti Dasar</h2>
    </div>

    <form method="get" action="" class="cari">
        <?php if (isset($_GET['page'])): ?>
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
        <?php endif; ?>
    </form>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Bukti Dasar</th>
                <th>Kondisi</th>
                <th>Asesi</th>
                <th style="width: 280px;">Aksi</th>
            </tr>
        </thead>
            <tbody>
            <?php if (isset($result) && mysqli_num_rows($result) > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                //    $jumlah_unit = intval($row['jumlah_unit'] ?? 0);
                //    $color = getUnitButtonColor($jumlah_unit);
            ?>
                <tr>
                    <td data-label='NO'><?= $no++ ?></td>
                    <td data-label='Bukti Dasar'><?= htmlspecialchars($row['bukti_dasar']) ?></td>
                    <td data-label='Kondisi'><?= htmlspecialchars($row['kondisi']) ?></td>
                    <td data-label='Asesi'><?= htmlspecialchars($row['nama_asesi'] ?? '-') ?></td>
                    <td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../SKEMA/Ubah_Skema.php&id=<?= $row['id_isi_bd'] ?>' class='btn-ubah'>
                            Ubah
                        </a>
                        <a href='../SKEMA/Hapus_Skema.php?id=<?= $row['id_isi_bd'] ?>'
                           class='btn-hapus'
                           onclick="return confirm('Yakin ingin menghapus skema ini?');">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile;
            else: ?>
                <!-- <tr>
                    <td colspan="6" style="text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;">
                        <php if (!empty($search)): ?>
                            Tidak ada data skema dengan nomor "<= htmlspecialchars($search) ?>".
                        <php elseif ($_SESSION['role'] === 'Asesi'): ?>
                            Anda belum memiliki skema sertifikasi.
                        <php else: ?>
                            Belum ada data skema.
                        <php endif; ?>
                    </td>
                </tr> -->
            <?php endif; ?>
            </tbody>
    </table>
</div>
<?php
mysqli_close($koneksi);
?>
