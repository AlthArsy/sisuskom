<?php

if (session_status() == PHP_SESSION_NONE) {
session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin_utm', 'Admin_lsp'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}
include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
$periode_map = [];
$q_periode = mysqli_query($koneksi, "SELECT id_periode, tahun_ajaran FROM tb_periode ORDER BY id_periode DESC");
if ($q_periode) {
    while ($p = mysqli_fetch_assoc($q_periode)) {
        $periode_map[(int) $p['id_periode']] = $p['tahun_ajaran'];
    }
}
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT tb_jadwal.*, tb_periode.tahun_ajaran
        FROM tb_jadwal
        LEFT JOIN tb_periode ON tb_jadwal.id_periode = tb_periode.id_periode";
$conditions = [];
$params = [];

$role = $_SESSION['role'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$id_periode_session = isset($_SESSION['id_periode']) ? intval($_SESSION['id_periode']) : 0;

$periode_nama = '-';
if ($id_periode_session > 0) {
    $q_periode = mysqli_query($koneksi, "SELECT tahun_ajaran FROM tb_periode WHERE id_periode = $id_periode_session");
    if ($q_periode && $row = mysqli_fetch_assoc($q_periode)) {
        $periode_nama = htmlspecialchars($row['tahun_ajaran']);
    }
}


if ($search !== '') {
    $conditions[] = "(tb_jadwal.hari LIKE ? OR tb_jadwal.tanggal LIKE ? OR tb_jadwal.tuk LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

// If current user is not Admin_utm, restrict to the active periode in session.
if ($role !== 'Admin_utm') {
    if ($id_periode_session > 0) {
        $conditions[] = "tb_jadwal.id_periode = $id_periode_session";
    } else {
        // No periode selected for non-superuser — show no rows.
        $conditions[] = "1=0";
    }
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
    } else {
        die("Prepare error: " . mysqli_error($koneksi));
    }
} else {
    $hasil = mysqli_query($koneksi, $sql);
}

if (!$hasil) {
    die("Query error: " . mysqli_error($koneksi));
}

function buildSearchUrl($params) {
    $base_url = '';

    if (isset($_GET['page'])) {
        $base_url = '?page=' . urlencode($_GET['page']);

        if (!empty($params['search'])) {
            $base_url .= '&search=' . urlencode($params['search']);
        }
    } else {
        $query_params = [];
        if (!empty($query_params)) {
            $base_url = '?' . implode('&', $query_params);
        }
    }

    return $base_url;
}
?>
<link rel="stylesheet" href="../assets/CSS/manajeman_penguna.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="konten-user">
    <h2 class="jdm">Data Jadwal</h2>

    <?php if ($role !== 'Admin_utm'): ?>
    <div style="background:#e8f0fe; padding:8px 15px; border-radius:6px; margin-bottom:15px; font-size:14px;">
        <i class="fas fa-calendar-alt"></i> <strong>Periode Aktif:</strong> <?php echo ($id_periode_session > 0) ? $periode_nama : '<span style="color:red;">Belum ada periode dipilih</span>'; ?>
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
                placeholder="Cari jadwal, skema, atau asesor..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>

        <div class="cari-actions">
            <button type="submit" class="btn-cari"><i class="fas fa-search"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : $_SERVER['PHP_SELF']; ?>"
                   class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            <?php endif; ?>
            <a href="../BERANDA/UTAMA.php?page=../Jadwal/tambah_jdwl.php" class="Tambah">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>tuk</th>
                <th style="width: 175px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $rows = [];
            while ($row = mysqli_fetch_assoc($hasil)) {
                $rows[] = $row;
            }
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td data-label='NO'>" . $no++ . "</td>";
                    echo "<td data-label='Hari'>" . htmlspecialchars($row['hari'] ?? '') . "</td>";
                    echo "<td data-label='Tanggal'>" . htmlspecialchars($row['tanggal'] ?? '') . "</td>";
                    echo "<td data-label='Waktu'>" . htmlspecialchars($row['waktu'] ?? '') . "</td>";
                    echo "<td data-label='tuk'>" . htmlspecialchars($row['tuk'] ?? '') . "</td>";
                    echo "<td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../Jadwal/ubah.php&id=" . $row['id_jadwal'] . "' class='btn-ubah'>Ubah</a>
                        <a href='../BERANDA/UTAMA.php?page=../Jadwal/hapus.php&id=" . $row['id_jadwal'] . "'
                           class='btn-hapus'
                           onclick=\"return confirm('Yakin ingin menghapus jadwal ini?');\">Hapus</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;'>
                    Tidak ada data jadwal yang sesuai dengan pencarian.
                    </td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.cari');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
        });
    }
});
</script>
