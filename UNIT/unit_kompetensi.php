<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

$id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;

if ($id_skema > 0) {
    $query_skema = "
        SELECT
            tb_skema.nomor_skema,
            tb_skema.judul_skema,
            tb_skema.standar_kompetensi_kerja,
            tb_asesor.nama_asesor
        FROM tb_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        WHERE tb_skema.id_skema = ?
    ";
    $stmt_skema = mysqli_prepare($koneksi, $query_skema);
    mysqli_stmt_bind_param($stmt_skema, "i", $id_skema);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $skema_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);

    $query = "
        SELECT
            tb_unit_kompetensi.id_unit,
            tb_unit_kompetensi.kode_unit,
            tb_unit_kompetensi.judul_unit,
            COUNT(tb_elemen.id_elemen) as jumlah_elemen
        FROM tb_unit_kompetensi
        LEFT JOIN tb_elemen ON tb_unit_kompetensi.id_unit = tb_elemen.id_unit
        WHERE tb_unit_kompetensi.id_skema = ?
        GROUP BY tb_unit_kompetensi.id_unit
        ORDER BY tb_unit_kompetensi.id_unit ASC
    ";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_skema);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    if ($_SESSION['role'] === 'Admin') {
    $query = "
        SELECT
            tb_unit_kompetensi.id_unit,
            tb_skema.id_skema,
            tb_skema.nomor_skema,
            tb_skema.judul_skema,
            tb_skema.standar_kompetensi_kerja,
            tb_asesor.nama_asesor,
            tb_unit_kompetensi.kode_unit,
            tb_unit_kompetensi.judul_unit,
            COUNT(tb_elemen.id_elemen) as jumlah_elemen
        FROM tb_unit_kompetensi
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        LEFT JOIN tb_elemen ON tb_unit_kompetensi.id_unit = tb_elemen.id_unit
        GROUP BY tb_unit_kompetensi.id_unit
        ORDER BY tb_skema.id_skema ASC, tb_unit_kompetensi.id_unit ASC
    ";
    $result = mysqli_query($koneksi, $query);

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
                    tb_unit_kompetensi.id_unit,
                    tb_skema.id_skema,
                    tb_skema.nomor_skema,
                    tb_skema.judul_skema,
                    tb_skema.standar_kompetensi_kerja,
                    tb_unit_kompetensi.kode_unit,
                    tb_unit_kompetensi.judul_unit,
                    COUNT(tb_elemen.id_elemen) as jumlah_elemen
                FROM tb_unit_kompetensi
                LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
                LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
                LEFT JOIN tb_elemen ON tb_unit_kompetensi.id_unit = tb_elemen.id_unit
                WHERE tb_skema.id_asesor = ?
                GROUP BY tb_unit_kompetensi.id_unit
                ORDER BY tb_skema.id_skema ASC, tb_unit_kompetensi.id_unit ASC
            ";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_asesor_login);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $result = mysqli_query($koneksi, "SELECT * FROM tb_unit_kompetensi WHERE 1=0");
        }
    }
}

// function getElemenButtonColor($jumlah){
//     $color = [
//         0  => '#54b4bbff',
//         1  => '#00b9f1ff',
//         2  => '#6067c7ff',
//         3  => '#1947a8ff',
//         4  => '#082da7ff',
//         5  => '#b14b4bff',
//         6  => '#d13f3fff',
//         7  => '#cf3333ff',
//         8  => '#ab00eeff',
//         9  => '#4f0db8ff',
//         10 => '#1e023dff'
//     ];

//     if ($jumlah > 10) $jumlah = 10;
//     return $color[$jumlah];
// }

$units_by_skema = [];
if (isset($result) && $result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($id_skema > 0) {
            $units_by_skema[$id_skema][] = $row;
        } else {
            $skema_id = $row['id_skema'];
            if (!isset($units_by_skema[$skema_id])) {
                $units_by_skema[$skema_id] = [
                    'info' => [
                        'nomor_skema' => $row['nomor_skema'],
                        'judul_skema' => $row['judul_skema'],
                        'standar_kompetensi_kerja' => $row['standar_kompetensi_kerja'],
                        'nama_asesor' => $row['nama_asesor']
                    ],
                    'units' => []
                ];
            }
            $units_by_skema[$skema_id]['units'][] = $row;
        }
    }
}
?>
<link rel="stylesheet" href="../assets/CSS/list_UEK.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="elemen-container">
    <div class="header-container">
        <h2 class="jd">
            <?php if ($id_skema > 0 && isset($skema_data)): ?>
               Unit Kompetensi - <?= htmlspecialchars($skema_data['nomor_skema']) ?> <!-- <= htmlspecialchars($skema_group['info']['nama_asesor']) ?>  -->
            <?php else: ?>
                Daftar Unit Kompetensi
            <?php endif; ?>
        </h2>
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="message <?php echo $_SESSION['tipe']; ?>">
            <?php
                echo htmlspecialchars($_SESSION['pesan']);
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        </div>
    <?php endif; ?>
        <div>
            <?php if ($id_skema > 0): ?>
                <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="UTAMA.php?page=../UNIT/From_unit_kompetensi.php&id_skema=<?= $id_skema ?>" class="btn-tambah">
                    <i class="fas fa-plus"></i> Tambah Unit
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($id_skema > 0 && isset($skema_data)): ?>
        <div class="skema-info">
            <h3>Informasi Skema</h3>
            <p><strong>Nomor Skema:</strong> <?= htmlspecialchars($skema_data['nomor_skema']) ?></p>
            <p><strong>Judul Skema:</strong> <?= htmlspecialchars($skema_data['judul_skema']) ?></p>
            <p><strong>Standar Kompetensi:</strong> <?= htmlspecialchars($skema_data['standar_kompetensi_kerja']) ?></p>
            <p><strong>Asesor:</strong> <?= htmlspecialchars($skema_data['nama_asesor'])?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Unit</th>
                    <th>Judul Unit Kompetensi</th>
                    <th style="width: 315px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (isset($units_by_skema[$id_skema]) && count($units_by_skema[$id_skema]) > 0):
                $no = 1;
                foreach ($units_by_skema[$id_skema] as $row):
                    $jumlah_elemen = intval($row['jumlah_elemen'] ?? 0);
                    // $color = getElemenButtonColor($jumlah_elemen);
                ?>
                    <tr>
                        <td data-label="No"><?= $no++; ?></td>
                        <td data-label="Kode Unit"><?= htmlspecialchars($row['kode_unit']) ?></td>
                        <td data-label="Judul Unit"><?= htmlspecialchars($row['judul_unit']) ?></td>
                        <td data-label="Aksi" class="aksi">
                        <a href='UTAMA.php?page=../UNIT/Ubah_unit.php&id=<?= $row['id_unit'] ?>' class='btn-ubah'>
                            Ubah
                        </a>
                            <a href="UTAMA.php?page=../UNIT/hapus_unit.php&id_unit=<?= $row['id_unit'] ?>"
                             class="btn-hapus"
                                 data-id="<?= $row['id_unit'] ?>"
                                 data-id-skema="<?= $id_skema ?>"
                                    onclick="return confirm('Yakin ingin menghapus unit kompetensi ini?');">
                                Hapus
                            </a>
                            <?php if ($jumlah_elemen == 0): ?>
                                <a href="UTAMA.php?page=../ELEMEN/From_elemen.php&id_unit=<?= $row['id_unit'] ?>"
                                   class="btn-elemen-empty">
                                    Tambah Elemen
                                </a>
                            <?php else: ?>
                                <!-- <a href="UTAMA.php?page=../ELEMEN/From_elemen.php&id_unit=<?= $row['id_unit'] ?>"
                                   class="btn-elemen-badge"
                                   style="background-color: <?= $color ?>; border-color: <?= $color ?>;"
                                   title="Tambah Elemen">
                                    <i class="fas fa-plus"></i>
                                </a> -->
                                <a href="UTAMA.php?page=../ELEMEN/elemen.php&id_unit=<?= $row['id_unit'] ?>"
                                   class="btn-lihat-elemen"
                                   title="Lihat Elemen">
                                    Lihat Elemen
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;">
                        Belum ada unit kompetensi untuk skema ini.
                        <a href="UTAMA.php?page=../UNIT/From_unit_kompetensi.php&id_skema=<?= $id_skema ?>" style="color:#4186e0;">Tambah Unit</a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

    <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Unit</h3>
                <p>
                    <?php if ($_SESSION['role'] === 'Asesor'): ?>
                        Kamu belum memiliki unit kompetensi. Silakan tambahkan unit ke skema.
                    <?php else: ?>
                        Belum ada unit kompetensi.
                    <?php endif; ?>
                </p>
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
</script>

<?php
mysqli_close($koneksi);
?>
