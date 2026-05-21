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

$id_unit = isset($_GET['id_unit']) ? intval($_GET['id_unit']) : 0;

if ($id_unit > 0) {
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
    mysqli_stmt_bind_param($stmt_skema, "i", $id_unit);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $skema_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);

    $query = "
        SELECT
            tb_elemen.id_elemen,
            tb_elemen.nama_elemen,
            tb_unit_kompetensi.kode_unit,
            tb_elemen.no_elemen,
            tb_unit_kompetensi.judul_unit,
            COUNT(tb_kuk.id_kuk) as jumlah_kuk
        FROM tb_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_kuk ON tb_elemen.id_elemen = tb_kuk.id_elemen
        WHERE tb_elemen.id_unit = ?
        GROUP BY tb_elemen.id_elemen
        ORDER BY tb_elemen.id_elemen ASC
    ";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_unit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

} else {
    if ($_SESSION['role'] === 'Admin') {
    $query = "
        SELECT
            tb_elemen.id_elemen,
            tb_unit_kompetensi.id_unit,
            tb_unit_kompetensi.kode_unit,
            tb_unit_kompetensi.judul_unit,
            tb_asesor.nama_asesor,
            tb_elemen.no_elemen,
            tb_elemen.nama_elemen,
            COUNT(tb_kuk.id_kuk) as jumlah_kuk
        FROM tb_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        LEFT JOIN tb_kuk ON tb_elemen.id_elemen = tb_kuk.id_elemen
        GROUP BY tb_elemen.id_elemen
        ORDER BY tb_unit_kompetensi.id_unit ASC, tb_elemen.id_elemen ASC
    ";
    $result = mysqli_query($koneksi, $query);

    } else if ($_SESSION['role'] === 'Asesor') {


        if (!isset($_SESSION['id_referensi'])) {
            $username = $_SESSION['username'];
            $get_asesor = "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = ?";
            $stmt_asesor = mysqli_prepare($koneksi, $get_asesor);
            mysqli_stmt_bind_param($stmt_asesor, "s", $username);
            mysqli_stmt_execute($stmt_asesor);
            $result_asesor = mysqli_stmt_get_result($stmt_asesor);

            if ($row_asesor = mysqli_fetch_assoc($result_asesor)) {
                $_SESSION['id_referensi'] = $row_asesor['id_asesor'];
            } else {
                $_SESSION['id_referensi'] = 0;
            }
            mysqli_stmt_close($stmt_asesor);
        }

        $id_asesor_login = intval($_SESSION['id_referensi']);

        if ($id_asesor_login > 0) {
            $query = "
                SELECT
                    tb_elemen.id_elemen,
                    tb_unit_kompetensi.id_unit,
                    tb_unit_kompetensi.kode_unit,
                    tb_unit_kompetensi.judul_unit,
                    tb_elemen.no_elemen,
                    tb_elemen.nama_elemen,
                    COUNT(tb_kuk.id_kuk) as jumlah_kuk
                FROM tb_elemen
                LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
                LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
                LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
                LEFT JOIN tb_kuk ON tb_elemen.id_elemen = tb_kuk.id_elemen
                WHERE tb_skema.id_asesor = ?
                GROUP BY tb_elemen.id_elemen
                ORDER BY tb_unit_kompetensi.id_unit ASC, tb_elemen.id_elemen ASC
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

function getElemenButtonColor($jumlah){
    $color = [
        0  => '#54b4bbff',
        1  => '#00b9f1ff',
        2  => '#6067c7ff',
        3  => '#1947a8ff',
        4  => '#082da7ff',
        5  => '#b14b4bff',
        6  => '#d13f3fff',
        7  => '#cf3333ff',
        8  => '#ab00eeff',
        9  => '#4f0db8ff',
        10 => '#1e023dff'
    ];

    if ($jumlah > 10) $jumlah = 10;
    return $color[$jumlah];
}

$units_by_unit = [];
if (isset($result) && $result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($id_unit > 0) {
            $units_by_unit[$id_unit][] = $row;
        } else {
            $unit_id = $row['id_unit'];
            if (!isset($units_by_unit[$unit_id])) {
                $units_by_unit[$unit_id] = [
                    'info' => [
                        'kode_unit' => $row['kode_unit'] ?? '',
                        'judul_unit' => $row['judul_unit'] ?? '',
                        'nama_asesor' => $row['nama_asesor'] ?? ''
                    ],
                    'units' => []
                ];
            }
            $units_by_unit[$unit_id]['units'][] = $row;
        }
    }
}
?>

<link rel="stylesheet" href="../assets/CSS/list_UEK.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="elemen-container">
    <div class="header-container">
        <h2 class="jd">
            <?php if ($id_unit > 0 && isset($skema_data)): ?>
                Elemen - <?= htmlspecialchars($skema_data['kode_unit']) ?>
            <?php else: ?>
                Daftar Elemen
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
            <?php if ($id_unit > 0): ?>
                <a href="UTAMA.php?page=../UNIT/unit_kompetensi.php&id_skema=<?= $skema_data['id_skema'] ?>" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="UTAMA.php?page=../ELEMEN/From_elemen.php&id_unit=<?= $id_unit ?>" class="btn-tambah">
                    <i class="fas fa-plus"></i> Tambah Elemen
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($id_unit > 0 && isset($skema_data)): ?>
        <div class="skema-info">
            <h3>Informasi unit</h3>
            <p><strong>Kode Unit:</strong> <?= htmlspecialchars($skema_data['kode_unit']) ?></p>
            <p><strong>Judul Unit:</strong> <?= htmlspecialchars($skema_data['judul_unit']) ?></p>
            <p><strong>Asesor:</strong> <?= htmlspecialchars($skema_data['nama_asesor'])?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>No Elemen</th>
                    <th>Nama Elemen</th>
                    <th style="width: 310px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
           <?php if (isset($units_by_unit[$id_unit]) && count($units_by_unit[$id_unit]) > 0):
                $no = 1;
                foreach ($units_by_unit[$id_unit] as $row):
                    $jumlah_kuk = intval($row['jumlah_kuk'] ?? 0);
                    $color = getElemenButtonColor($jumlah_kuk);
                ?>
                    <tr>
                        <td data-label="No"><?= $no++; ?></td>
                        <td data-label="No Elemen"><?= htmlspecialchars($row['no_elemen']) ?></td>
                        <td data-label="Nama Elemen"><?= htmlspecialchars($row['nama_elemen']) ?></td>
                        <td data-label="Aksi" class="aksi">
                            <a href="UTAMA.php?page=../ELEMEN/ubah_elemen.php&id=<?= $row['id_elemen'] ?>" class="btn-ubah">
                                Ubah
                            </a>
                            <a href="UTAMA.php?page=../ELEMEN/hapus_elemen.php&id_elemen=<?= $row['id_elemen'] ?>"
                               class="btn-hapus"
                               onclick="return confirm('Yakin ingin menghapus Elemen ini?');">
                                Hapus
                            </a>
                            <?php if ($jumlah_kuk == 0): ?>
                                <a href="UTAMA.php?page=../KUK/From_kuk.php&id_elemen=<?= $row['id_elemen'] ?>"
                                   class="btn-elemen-empty">
                                    Tambah KuK
                                </a>
                            <?php else: ?>
                                <a href="UTAMA.php?page=../KUK/From_kuk.php&id_elemen=<?= $row['id_elemen'] ?>"
                                   class="btn-elemen-badge"
                                   style="background-color: <?= $color ?>; border-color: <?= $color ?>;"
                                   title="Tambah KUK">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="UTAMA.php?page=../KUK/KUK.php&id_elemen=<?= $row['id_elemen'] ?>"
                                   class="btn-lihat-elemen"
                                   title="Lihat KUK">
                                    Lihat
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;">
                        Belum ada unit kompetensi untuk skema ini.
                        <a href="UTAMA.php?page=../ELEMEN/From_elemen.php&id_unit=<?= $id_unit ?>" style="color:#4186e0;">Tambah Elemen</a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Elemen</h3>
                <p>
                    <?php if ($_SESSION['role'] === 'Asesor'): ?>
                        Kamu belum memiliki Elemen, Silakan tambahkan Elemen ke Unit
                <?php else: ?>
                Belum ada Elemen
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
