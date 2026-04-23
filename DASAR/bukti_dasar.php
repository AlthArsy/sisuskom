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

$sql = "SELECT * FROM tb_bukti_dasar";
$conditions = [];  
$params = [];

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
?>
<link rel="stylesheet" href="../assets/CSS/manajeman_penguna.css">
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
    
    <form method="get" action="" class="cari">
        <?php if (isset($_GET['page'])): ?>
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
        <?php endif; ?>
        
        <div style="margin: 15px 0; text-align: right;">
            <a href="../BERANDA/UTAMA.php?page=../DASAR/Tambah_bd.php" class="Tambah">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </form>
    
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
                    echo "<td data-label='Aksi' class='aksi'>
                        <a href='UTAMA.php?page=../ADM/ubah_ba.php&id=" . $row['id_bd'] . "' class='btn-ubah'>Ubah</a>
                        <a href='UTAMA.php?page=../ADM/hapus_ba.php&id=" . $row['id_bd'] . "' 
                           class='btn-hapus'
                           onclick=\"return confirm('Yakin ingin menghapus user ini?');\">Hapus</a>
                        </td>";
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