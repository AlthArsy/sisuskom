<?php
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


if ($role === 'Admin_utm') {
    $query = "
        SELECT
            tb_skema.id_skema,
            tb_skema.nomor_skema,
            tb_skema.judul_skema,
            tb_skema.standar_kompetensi_kerja,
            COUNT(tb_unit_kompetensi.id_unit) as jumlah_unit
        FROM tb_skema
        
        LEFT JOIN tb_unit_kompetensi ON tb_skema.id_skema = tb_unit_kompetensi.id_skema
    ";
    if (!empty($search)) {
        $query .= " WHERE tb_skema.nomor_skema LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
    }
    $query .= " GROUP BY tb_skema.id_skema ORDER BY tb_skema.id_skema DESC";
    $result = mysqli_query($koneksi, $query);

} elseif ($role === 'Admin_lsp') {
    if ($id_periode_session <= 0) {
        $result = mysqli_query($koneksi, "SELECT * FROM tb_skema WHERE 1=0");
    } else {
        $query = "
            SELECT
                tb_skema.id_skema,
                tb_skema.nomor_skema,
                tb_skema.judul_skema,
                tb_skema.standar_kompetensi_kerja,
                COUNT(tb_unit_kompetensi.id_unit) as jumlah_unit
            FROM tb_skema
            LEFT JOIN tb_unit_kompetensi ON tb_skema.id_skema = tb_unit_kompetensi.id_skema
           WHERE tb_skema.id_periode = $id_periode_session
        ";
        if (!empty($search)) {
            $query .= " AND tb_skema.nomor_skema LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
        }
        $query .= " GROUP BY tb_skema.id_skema ORDER BY tb_skema.id_skema DESC";
        $result = mysqli_query($koneksi, $query);
    }

} elseif ($role === 'Asesor') {
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

    if ($id_asesor_login <= 0 || $id_periode_session <= 0) {
        $result = mysqli_query($koneksi, "SELECT * FROM tb_skema WHERE 1=0");
    } else {
        $query = "
            SELECT 
                tb_skema.id_skema,
                tb_skema.nomor_skema,
                tb_skema.judul_skema,
                tb_skema.standar_kompetensi_kerja,
                EXISTS(
                    SELECT 1 FROM tb_skema_asesor 
                    WHERE tb_skema_asesor.id_skema = tb_skema.id_skema 
                    AND tb_skema_asesor.id_asesor = $id_asesor_login
                ) as sudah_dipilih
            FROM tb_skema
            WHERE tb_skema.id_periode = $id_periode_session
        ";
        if (!empty($search)) {
            $query .= " AND tb_skema.nomor_skema LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
        }
        $query .= " ORDER BY tb_skema.id_skema DESC";
        $result = mysqli_query($koneksi, $query);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Skema</title>
    <link rel="stylesheet" href="../assets/CSS/list_skema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .btn-pilih {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.2s;
        }
        .btn-pilih:hover {
            background-color: #388e3c;
        }
        .badge-dipilih {
            background-color: #e0e0e0;
            color: #2e7d32;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-dipilih i {
            margin-right: 5px;
        }
        .filter-info {
            margin: 15px 0;
            padding: 8px 12px;
            background: #e3f2fd;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="s-container">
    <div class="header-container">
        <h2 class="jdm">Data Skema</h2>
        <?php if ($role === 'Admin_lsp'): ?>
            <a href="UTAMA.php?page=../SKEMA/Form_Skema.php" class="btn-tambah">
                <i class="fas fa-plus"></i> Tambah Skema
            </a>
        <?php endif; ?>
    </div>

    <?php if ($role !== 'Admin_utm'): ?>
    <div style="background:#e8f0fe; padding:8px 15px; border-radius:6px; margin-bottom:15px; font-size:14px;">
        <i class="fas fa-calendar-alt"></i> <strong>Periode Aktif:</strong> <?php echo ($id_periode_session > 0) ? $periode_nama : '<span style="color:red;">Belum ada periode dipilih</span>'; ?>
    </div>
    <?php endif; ?>

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
            <input type="text" name="search" placeholder="Cari nomor skema..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="cari-actions">
            <button type="submit" class="btn-cari"><i class="fas fa-search"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : $_SERVER['PHP_SELF']; ?>" class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($search)): ?>
        <div class="filter-info">
            <strong>Filter aktif:</strong> nomor skema: &ldquo;<?php echo htmlspecialchars($search); ?>&rdquo;
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nomor Skema</th>
                <th>Judul Skema</th>
                <th>Standar Kompetensi Kerja</th>
                <?php if ($role === 'Asesor'): ?>
                <th>Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($result) && mysqli_num_rows($result) > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
            ?>
                <tr>
                    <td data-label="NO"><?= $no++ ?></td>
                    <td data-label="Nomor Skema"><?= htmlspecialchars($row['nomor_skema']) ?></td>
                    <td data-label="Judul Skema"><?= htmlspecialchars($row['judul_skema']) ?></td>
                    <td data-label="Standar Kompetensi Kerja"><?= htmlspecialchars($row['standar_kompetensi_kerja']) ?></td>
                    <?php if ($role === 'Asesor'): ?>
                    <td data-label="Aksi">
                        <?php if ($row['sudah_dipilih']): ?>
                            <span class="badge-dipilih"><i class="fas fa-check-circle"></i> Sudah Dipilih</span>
                        <?php else: ?>
                            <button class="btn-pilih" data-id_skema="<?= $row['id_skema'] ?>">
                                <i class="fas fa-hand-pointer"></i> Pilih Skema
                            </button>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="<?= ($role === 'Asesor') ? '5' : '4' ?>" style="text-align:center;color:#8692af;padding:32px;">
                        <?php if (!empty($search)): ?>
                            Tidak ada data skema dengan nomor "<?= htmlspecialchars($search) ?>".
                        <?php elseif ($role === 'Asesor'): ?>
                            Belum ada skema tersedia pada periode ini.
                        <?php elseif ($role === 'Admin_lsp' && $id_periode_session <= 0): ?>
                            Periode tidak tersedia. Silakan login ulang.
                        <?php else: ?>
                            Belum ada data skema.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($role === 'Asesor'): ?>
<script>
    document.querySelectorAll('.btn-pilih').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const id_skema = this.dataset.id_skema;
            if (confirm('Apakah Anda yakin ingin memilih skema ini? Skema akan tersedia untuk digunakan dalam asesmen.')) {
                fetch('../SKEMA/pilih_skema.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_skema=' + id_skema
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Berhasil memilih skema!');
                        location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan teknis.');
                    console.error(error);
                });
            }
        });
    });
</script>
<?php endif; ?>

</body>
</html>
<?php mysqli_close($koneksi); ?>