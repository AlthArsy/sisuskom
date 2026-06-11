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
$id_skema_param = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
$can_manage = ($role === 'Asesor' || $role === 'Admin_lsp');

if ($role === 'Asesor') {
    if (!isset($_SESSION['id_asesor'])) {
        $username = $_SESSION['username'];
        $get_asesor = "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = ?";
        $stmt_a = mysqli_prepare($koneksi, $get_asesor);
        mysqli_stmt_bind_param($stmt_a, "s", $username);
        mysqli_stmt_execute($stmt_a);
        $res_a = mysqli_stmt_get_result($stmt_a);
        if ($row_a = mysqli_fetch_assoc($res_a)) {
            $_SESSION['id_asesor'] = $row_a['id_asesor'];
        } else {
            $_SESSION['id_asesor'] = 0;
        }
        mysqli_stmt_close($stmt_a);
    }
    $id_asesor_login = intval($_SESSION['id_asesor']);
    if ($id_skema_param <= 0) {
        $_SESSION['pesan'] = 'Buka Bukti Dasar dari menu skema (pilih skema terlebih dahulu).';
        $_SESSION['tipe'] = 'error';
        header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
        exit();
    }
    if ($id_asesor_login <= 0) {
        header('Location: ../LOGIN/login.php');
        exit();
    }
    $chk = mysqli_prepare($koneksi, "SELECT id_skema FROM tb_skema WHERE id_skema = ? AND id_asesor = ? LIMIT 1");
    mysqli_stmt_bind_param($chk, "ii", $id_skema_param, $id_asesor_login);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) === 0) {
        mysqli_stmt_close($chk);
        $_SESSION['pesan'] = 'Anda tidak memiliki akses ke skema ini.';
        $_SESSION['tipe'] = 'error';
        header('Location: ../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php');
        exit();
    }
    mysqli_stmt_close($chk);
}

if ($id_skema_param > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_bukti_dasar WHERE id_skema = ? ORDER BY id_bd ASC");
    mysqli_stmt_bind_param($stmt, "i", $id_skema_param);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    $hasil = mysqli_query($koneksi, "SELECT * FROM tb_bukti_dasar ORDER BY id_skema ASC, id_bd ASC");
}

if (!$hasil) {
    die("Query error: " . mysqli_error($koneksi));
}
?>
<link rel="stylesheet" href="../assets/CSS/manajeman_penguna.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="konten-user">
    <h2 class="jdm">Bukti Dasar</h2>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="message <?php echo $_SESSION['tipe']; ?>">
            <?php
                echo htmlspecialchars($_SESSION['pesan']);
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        </div>
    <?php endif; ?>

    <div class="cari cari--actions-only">
        <?php if ($can_manage && $id_skema_param > 0): ?>
            <a href="../BERANDA/UTAMA.php?page=../DASAR/Tambah_bd.php&id_skema=<?php echo (int) $id_skema_param; ?>" class="Tambah">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        <?php elseif ($can_manage && $id_skema_param <= 0): ?>
            <p style="margin:0;padding:10px;background:#fff3cd;border-radius:8px;font-size:14px;flex:1;">
                Untuk menambah data, buka Bukti Dasar dari daftar skema agar terikat ke skema yang benar.
            </p>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
        <a href="../BERANDA/UTAMA.php?page=../SKEMA/list_skema.php" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Skema
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bukti Dasar</th>
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
                    echo "<td data-label='Bukti'>" . htmlspecialchars($row['bukti_dasar'] ?? '') . "</td>";
                    $aksi = '';
                    if ($can_manage) {
                        $back = '&id_skema=' . (int) ($row['id_skema'] ?? $id_skema_param);
                        $aksi = "<td data-label='Aksi' class='aksi'>
                            <a href='../BERANDA/UTAMA.php?page=../DASAR/ubah_bd.php&id_bd=" . (int)$row['id_bd'] . $back . "' class='btn-ubah'>Ubah</a>
                            <a href='../BERANDA/UTAMA.php?page=../DASAR/hapus_bd.php&id_bd=" . (int)$row['id_bd'] . $back . "'
                               class='btn-hapus'
                               onclick=\"return confirm('Yakin ingin menghapus bukti dasar ini?');\">Hapus</a>
                        </td>";
                    }
                    echo $aksi;
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;border-radius:7px;'>
                    Tidak ada data user yang sesuai dengan pencarian.
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
setTimeout(function() {
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        message.style.opacity = '0';
        message.style.transition = 'opacity 0.5s ease';
        setTimeout(() => message.remove(), 500);
    });
}, 5000);
</script>
