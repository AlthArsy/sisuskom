<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

$id_elemen = isset($_GET['id_elemen']) ? intval($_GET['id_elemen']) : 0;

if ($id_elemen > 0) {
    $query_skema = "
        SELECT 
            tb_elemen.no_elemen, 
            tb_elemen.nama_elemen,
            tb_elemen.id_unit,
            tb_asesor.nama_asesor
        FROM tb_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        WHERE tb_elemen.id_elemen = ?
    ";
    $stmt_skema = mysqli_prepare($koneksi, $query_skema);
    mysqli_stmt_bind_param($stmt_skema, "i", $id_elemen);
    mysqli_stmt_execute($stmt_skema);
    $result_skema = mysqli_stmt_get_result($stmt_skema);
    $skema_data = mysqli_fetch_assoc($result_skema);
    mysqli_stmt_close($stmt_skema);
    
    $query = " 
        SELECT 
            tb_kuk.id_kuk, 
            tb_kuk.no_kuk, 
            tb_kuk.kuk,
            tb_elemen.no_elemen, 
            tb_elemen.nama_elemen
        FROM tb_kuk
        LEFT JOIN tb_elemen ON tb_kuk.id_elemen = tb_elemen.id_elemen
        WHERE tb_kuk.id_elemen = ?
        ORDER BY tb_kuk.id_kuk ASC
    ";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_elemen);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
} else {
    if ($_SESSION['role'] === 'Admin') {
    $query = "
        SELECT 
            tb_kuk.id_kuk, 
            tb_elemen.id_elemen,
            tb_elemen.no_elemen, 
            tb_elemen.nama_elemen,
            tb_asesor.nama_asesor,
            tb_kuk.no_kuk, 
            tb_kuk.kuk
        FROM tb_kuk
        LEFT JOIN tb_elemen ON tb_kuk.id_elemen = tb_elemen.id_elemen
        LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
        LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
        LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
        ORDER BY tb_elemen.id_elemen ASC, tb_kuk.id_kuk ASC
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
                    tb_kuk.id_kuk, 
                    tb_elemen.id_elemen,
                    tb_elemen.no_elemen, 
                    tb_elemen.nama_elemen,
                    tb_kuk.no_kuk, 
                    tb_kuk.kuk
                FROM tb_kuk
                LEFT JOIN tb_elemen ON tb_kuk.id_elemen = tb_elemen.id_elemen
                LEFT JOIN tb_unit_kompetensi ON tb_elemen.id_unit = tb_unit_kompetensi.id_unit
                LEFT JOIN tb_skema ON tb_unit_kompetensi.id_skema = tb_skema.id_skema
                LEFT JOIN tb_asesor ON tb_skema.id_asesor = tb_asesor.id_asesor
                WHERE tb_skema.id_asesor = ?
                ORDER BY tb_elemen.id_elemen ASC, tb_kuk.id_kuk ASC
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

$units_by_unit = [];
if (isset($result) && $result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($id_elemen > 0) {
            $units_by_unit[$id_elemen][] = $row;
        } else {
            $unit_id = $row['id_elemen'];
            if (!isset($units_by_unit[$unit_id])) {
                $units_by_unit[$unit_id] = [
                    'info' => [
                        'no_elemen' => $row['no_elemen'] ?? '',
                        'nama_elemen' => $row['nama_elemen'] ?? '',
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
            <?php if ($id_elemen > 0 && isset($skema_data)): ?>
                KUK - <?= htmlspecialchars($skema_data['no_elemen']) ?>
            <?php else: ?>
                Daftar KUK
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
            <?php if ($id_elemen > 0): ?>
                <a href="UTAMA.php?page=../ELEMEN/elemen.php&id_unit=<?= $skema_data['id_unit'] ?>" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="UTAMA.php?page=../KUK/From_kuk.php&id_elemen=<?= $id_elemen ?>" class="btn-tambah">
                    <i class="fas fa-plus"></i> Tambah KuK
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($id_elemen > 0 && isset($skema_data)): ?>
        <div class="skema-info">
            <h3>Informasi Elemen</h3>
            <p><strong>No Elemen:</strong> <?= htmlspecialchars($skema_data['no_elemen']) ?></p>
            <p><strong>Nama Elemen:</strong> <?= htmlspecialchars($skema_data['nama_elemen']) ?></p>
            <p><strong>Asesor:</strong> <?= htmlspecialchars($skema_data['nama_asesor'])?></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>No KUK</th>
                    <th>KUK</th>
                    <th style="width: 190px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (isset($units_by_unit[$id_elemen]) && count($units_by_unit[$id_elemen]) > 0):
                $no = 1;
                foreach ($units_by_unit[$id_elemen] as $row): ?>
                    <tr>
                        <td data-label="No"><?= $no++; ?></td>
                        <td data-label="No KuK"><?= htmlspecialchars($row['no_kuk'] ?? '') ?></td>
                        <td data-label="KUK"><?= htmlspecialchars($row['kuk'] ?? '') ?></td>
                        <td data-label="Aksi" class="aksi">
                            <a href="UTAMA.php?page=../KUK/ubah_kuk.php&id=<?= $row['id_kuk'] ?>" class="btn-ubah">
                              Ubah
                            </a>
                            <a href="UTAMA.php?page=../KUK/hapus_kuk.php&id_kuk=<?= $row['id_kuk'] ?>" 
                              class="btn-hapus"
                              onclick="return confirm('Yakin ingin menghapus KUK ini?');">
                              Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:#8692af;padding:32px;background:#fcfdff;font-size:16px;">
                        Belum ada KUK untuk elemen ini. 
                        <a href="UTAMA.php?page=../KUK/From_kuk.php&id_elemen=<?= $id_elemen ?>" style="color:#4186e0;">Tambah KUK</a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada KUK</h3>
                <p>
                    <?php if ($_SESSION['role'] === 'Asesor'): ?>
                        Kamu belum memiliki KUK, Silakan tambahkan KUK ke Elemen
                <?php else: ?>
                Belum ada KUK
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