<?php

if (session_status() == PHP_SESSION_NONE) {
session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_lsp') {
    header("Location: ../LOGIN/login.php");
    exit();
}
include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM tb_validator";
$conditions = [];
$params = [];



if ($search !== '') {
    $conditions[] = "(username LIKE ?)";
    $params[] = '%' . $search . '%';
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
    <h2 class="jdm">Data Validator</h2>

    <form method="get" action="" class="cari">
        <?php if (isset($_GET['page'])): ?>
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
        <?php endif; ?>

        <div class="cari-field">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input
                type="text"
                name="search"
                placeholder="Cari username..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>

        <div class="cari-actions">
            <button type="submit" class="btn-cari"><i class="fas fa-search"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : $_SERVER['PHP_SELF']; ?>"
                   class="btn-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
            <a href="../BERANDA/UTAMA.php?page=../PENAGATURAN/tambah-val-baru.php" class="Tambah">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Username</th>
                <th>No Reg</th>
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
                    echo "<td data-label='Username'>" . htmlspecialchars($row['username'] ?? '') . "</td>";
                    echo "<td data-label='No Reg'>" . htmlspecialchars($row['noreg'] ?? '') . "</td>";
                    echo "<td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../PENAGATURAN/ubah_val.php&id=" . $row['id_validator'] . "' class='btn-ubah'>Ubah</a>
                        <a href='../BERANDA/UTAMA.php?page=../PENAGATURAN/hapus_val.php&id=" . $row['id_validator'] . "'
                           class='btn-hapus'
                           onclick=\"return confirm('Yakin ingin menghapus validator ini?');\">Hapus</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;'>
                    Tidak ada data validator yang sesuai dengan pencarian.
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
