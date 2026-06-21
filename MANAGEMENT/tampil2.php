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

$role_filter = isset($_GET['role_filter']) ? $_GET['role_filter'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$allowed_roles = ['Admin_lsp', 'Asesor', 'Asesi'];

$periode_map = [];
$q_periode = mysqli_query($koneksi, "SELECT id_periode, tahun_ajaran FROM tb_periode ORDER BY id_periode DESC");
if ($q_periode) {
    while ($p = mysqli_fetch_assoc($q_periode)) {
        $periode_map[(int) $p['id_periode']] = $p['tahun_ajaran'];
    }
}

$sql = "SELECT users.*, tb_periode.tahun_ajaran FROM users LEFT JOIN tb_periode ON users.id_periode = tb_periode.id_periode";
$conditions = [];
$params = [];
$types = "";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin_lsp') {
    $conditions[] = "(role = 'Asesor' OR role = 'Asesi')";
}

if ($role_filter && in_array($role_filter, $allowed_roles)) {
    $conditions[] = "role = ?";
    $params[] = $role_filter;
    $types .= "s";
}

if ($search !== '') {
    $conditions[] = "(username LIKE ?)";
    $params[] = '%' . $search . '%';
    $types .= "s";
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
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
        if (!empty($params['role_filter'])) {
            $base_url .= '&role_filter=' . urlencode($params['role_filter']);
        }
    } else {
        $query_params = [];
        if (!empty($params['search'])) {
            $query_params[] = 'search=' . urlencode($params['search']);
        }
        if (!empty($params['role_filter'])) {
            $query_params[] = 'role_filter=' . urlencode($params['role_filter']);
        }

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
    <h2 class="jdm">Data User</h2>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="message <?php echo htmlspecialchars($_SESSION['tipe'] ?? 'success'); ?>">
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
                placeholder="Cari username..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>

        <select name="role_filter" class="cari-select" aria-label="Filter role">
            <option value="">Semua Role</option>
            <?php
                foreach ($allowed_roles as $role) {
                    $selected = ($role_filter === $role) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($role) . "\" $selected>" . htmlspecialchars($role) . "</option>";
                }
            ?>
        </select>

        <div class="cari-actions">
            <button type="submit" class="btn-cari"><i class="fas fa-search"></i> Cari</button>
            <?php if (!empty($search) || !empty($role_filter)): ?>
                <a href="<?php echo isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : $_SERVER['PHP_SELF']; ?>"
                   class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            <?php endif; ?>
            <a href="../BERANDA/UTAMA.php?page=../PENAGATURAN/tambah-user-baru.php" class="Tambah">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </form>

    <div class="import-user-box">
        <div class="import-user-head">
            <i class="fas fa-file-excel"></i>
            <div>
                <strong>Import User dari Excel</strong>
                <p>Unggah file .ods berisi kolom Username, Password, dan Role. Pilih Tahun Ajaran untuk semua user yang diimport.</p>
            </div>
        </div>
        <form method="post" action="../MANAGEMENT/post_user.php" enctype="multipart/form-data" class="import-user-form">
            <select name="id_periode" class="cari-select import-periode-select" required aria-label="Tahun Ajaran import">
                <option value="">Pilih Tahun Ajaran</option>
                <?php foreach ($periode_map as $pid => $tahun): ?>
                    <option value="<?php echo (int) $pid; ?>"><?php echo htmlspecialchars($tahun); ?></option>
                <?php endforeach; ?>
            </select>
            <a href="../Exel/Contoh-Post%20manajemanAsesi.ods" class="btn-template" download>
                <i class="fas fa-download"></i> Download Contoh
            </a>
            <label class="import-file-label">
                <i class="fas fa-upload"></i>
                <span>Pilih file .ods</span>
                <input type="file" name="file_user" accept=".ods" required>
            </label>
            <button type="submit" class="btn-import">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </form>
    </div>

<!-- 
    <?php if (!empty($search) || !empty($role_filter)): ?>
        <div style="margin-bottom: 15px; padding: 10px; background: #e8f4f8; border-left: 4px solid #3498db; border-radius: 4px;">
            <strong>Filter aktif:</strong>
            <?php if (!empty($search)): ?>
                Username: "<?php echo htmlspecialchars($search); ?>"
            <?php endif; ?>
            <?php if (!empty($role_filter)): ?>
                <?php echo !empty($search) ? ' | ' : ''; ?>
                Role: <?php echo htmlspecialchars($role_filter); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?> -->

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Username</th>
                <th>Password</th>
                <th>Role</th>
                <th>Tahun Ajaran</th>
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
                    echo "<td data-label='Password'>" . htmlspecialchars($row['password']) . "</td>";
                    echo "<td data-label='Role'>" . strtoupper(htmlspecialchars($row['role'])) . "</td>";
                    $tahun_label = !empty($row['tahun_ajaran']) ? $row['tahun_ajaran'] : '—';
                    echo "<td data-label='Tahun Ajaran'>" . htmlspecialchars($tahun_label) . "</td>";
                    echo "<td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../PENAGATURAN/ubah.php&id=" . $row['id_user'] . "' class='btn-ubah'>Ubah</a>
                        <a href='../BERANDA/UTAMA.php?page=../PENAGATURAN/hapus.php&id=" . $row['id_user'] . "'
                           class='btn-hapus'
                           onclick=\"return confirm('Yakin ingin menghapus user ini?');\">Hapus</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;'>
                    Tidak ada data user yang sesuai dengan pencarian.
                    </td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('.import-user-form input[type="file"]');
    const fileLabel = document.querySelector('.import-file-label span');

    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function() {
            fileLabel.textContent = this.files.length ? this.files[0].name : 'Pilih file .ods';
        });
    }

    setTimeout(function() {
        document.querySelectorAll('.message').forEach(function(message) {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(function() { message.remove(); }, 500);
        });
    }, 6000);
});
</script>
