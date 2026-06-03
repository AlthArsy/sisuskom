<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../koneksi.php";
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin_utm') {
    echo "<script>alert('Akses ditolak! Silakan login terlebih dahulu.'); window.location.href='../LOGIN/login.php';</script>";
    exit;
}
$query_all = "SELECT * FROM tb_admin ORDER BY nik ASC";
$result_all = mysqli_query($koneksi, $query_all);
$all_admin = [];
while ($row = mysqli_fetch_assoc($result_all)) {
    $all_admin[] = $row;
}
$display_admin = $all_admin;
$search_performed = false;
$search_criteria = [
    'id_admin' => '',
    'nama_admin' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $id_admin = isset($_POST['id_admin']) ? trim($_POST['id_admin']) : '';
    $nama_admin = isset($_POST['nama_admin']) ? trim($_POST['nama_admin']) : '';
    $search_criteria = [
        'id_admin' => $id_admin,
        'nama_admin' => $nama_admin
    ];
    $search_performed = true;
    $query = "SELECT * FROM tb_admin WHERE 1=1";
    $params = [];
    $types = "";
    if (!empty($id_admin)) {
        $query .= " AND id_admin = ?";
        $params[] = $id_admin;
        $types .= "i";
    }
    if (!empty($nama_admin)) {
        $query .= " AND nama_admin LIKE ?";
        $params[] = "%$nama_admin%";
        $types .= "s";
    }
    $query .= " ORDER BY nik ASC";
    if (empty($id_admin) && empty($nama_admin)) {
        $display_admin = $all_admin;
    } else {
        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt && !empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $display_admin = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $display_admin[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<link rel="stylesheet" href="../assets/CSS/cari-shared.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .btn {display: inline-block; padding: 6px 14px; border: none;border-radius: 4px;font-size: 13px;font-weight: 500;cursor: pointer;text-decoration: none;transition: all 0.3s ease;
    }

    .btn-primary {background-color: #007bff;color: white;
    }.btn-primary:hover {background-color: #0056b3;}
    .btn-secondary {background-color: #6c757d;color: white;}
    .btn-secondary:hover { background-color: #545b62;}
    .btn-sm {padding: 4px 10px;font-size: 12px;}
    .btn-view {background-color: #17a2b8;color: white;}
    .btn-view:hover {background-color: #117a8b;}
    .btn-edit {background-color: #28a745;color: white;}
    .btn-edit:hover {background-color: #218838;}
    .btn-delete {background-color: #dc3545;color: white;}
    .btn-delete:hover {background-color: #c82333;}
    .form-control {display: block;width: 100%;padding: 8px 12px;font-size: 14px;border: 1px solid #ccc;border-radius: 4px;}
    .form-control:focus {outline: none;border-color: #007bff;box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);}
    .alert {padding: 12px 20px;margin-bottom: 20px;border-radius: 4px;background-color: #d1ecf1;color: #004085;border: 1px solid #bee5eb;}
    .btn-group {display: flex;gap: 10px;flex-wrap: wrap;}
    .content-card.TBS-full-width {margin-left: -30px;margin-right: -30px;}
    .TBS-container {max-width: 100%;margin: 0;padding: 30px;}
    .TBS-header {text-align: center;margin-bottom: 30px;}
    .TBS-header h1 {font-size: 2.5em;margin-bottom: 10px;color: #333;}
    .TBS-header p {font-size: 1.2em;color: #666;}
    .TBS-user-info {display: flex;justify-content: space-between;align-items: center;margin-bottom: 20px;}
    .TBS-user-info span {font-weight: bold;}
    .TBS-search-box {margin-bottom: 30px;}
    .TBS-results-section {margin-top: 30px;}
    .TBS-results-header {display: flex;justify-content: space-between;align-items: center;margin-bottom: 15px;}
    .TBS-results-header h2 {margin: 0;color: #333;}
    .results-count {color: #666;}
    .table-container {overflow-x: auto;}
    table {width: 100%;border-collapse: collapse;}
    table th,
    table td {padding: 12px;text-align: left;border-bottom: 1px solid #ddd;}
    table th {background-color: #f5f5f5;font-weight: 600;color: #333;}
    table tbody tr:hover {background-color: #f9f9f9;}
    .TBS-field-label {display: none;font-weight: bold;color: #555;}
    .TBS-field-value {font-size: 1em;color: #333;}
    .TBS-action-buttons {display: flex;gap: 5px;flex-wrap: wrap;}
    .TBS-action-buttons a {text-decoration: none;white-space: nowrap;}
    @media (max-width: 768px) {
        .TBS-header h1 {font-size: 1.8em;}
        .TBS-user-info {flex-direction: column;gap: 10px;align-items: flex-start;}
        .TBS-results-header {flex-direction: column;gap: 10px;align-items: flex-start;}
        .TBS-field-label {display: block;}
    }
</style>
<div class="content-card TBS-full-width">
    <div class="TBS-container">
        <div class="TBS-header">
            <h1>Data Admin LSP</h1>
            <p>Pencarian dan Pengelolaan Data Admin LSP</p>
        </div>

        <div class="TBS-search-box">
            <form method="post" action="" class="search-form cari-grid">
                <div class="cari-grid-fields">
                    <div class="TBS-form-group">
                        <label for="id_admin">ID Admin LSP</label>
                        <input type="number" id="id_admin" name="id_admin" class="form-control"
                               placeholder="Masukkan ID Admin" value="<?php echo htmlspecialchars($search_criteria['id_admin']); ?>">
                    </div>
                    <div class="TBS-form-group">
                        <label for="nama_admin">Nama Admin LSP</label>
                        <input type="text" id="nama_admin" name="nama_admin" class="form-control"
                               placeholder="Masukkan Nama" value="<?php echo htmlspecialchars($search_criteria['nama_admin']); ?>">
                    </div>
                </div>
                <div class="cari-actions btn-group">
                    <button type="submit" name="search" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari Data
                    </button>
                    <!-- <button type="button" onclick="clearForm()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Bersihkan
                    </button> -->
                </div>
            </form>
        </div>
        <div class="TBS-results-section">
            <div class="TBS-results-header">
                <h2>
                    <?php if ($search_performed && !empty(array_filter($search_criteria))): ?>
                        Hasil Pencarian
                    <?php else: ?>
                        Semua Data Admin LSP
                    <?php endif; ?>
                </h2>
                <div class="results-count">
                    Ditemukan: <span><?php echo count($display_admin); ?></span> data admin
                </div>
            </div>
            <?php if (count($display_admin) === 0): ?>
                <div class="alert alert-info">
                     Tidak ditemukan data admin yang sesuai dengan kriteria pencarian.
                </div>
            <?php endif; ?>
            <?php if (count($display_admin) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Nama Admin LSP</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($display_admin as $row): ?>
                                <tr>
                                    <td data-label="ID">
                                        <br>
                                        <div class="TBS-field-value"><?php echo htmlspecialchars($row['id_admin']); ?></div>
                                    </td>
                                    <td data-label="Nama Admin">
                                        <br>
                                        <div class="TBS-field-value"><?php echo htmlspecialchars($row['nama_admin']); ?></div>
                                    </td>
                                    <td data-label="Aksi" class="aksi">
                                        <div class="TBS-action-buttons">
                                            <a href="UTAMA.php?page=../Admin_lsp/edit_admin_lsp.php&id=<?php echo $row['id_admin']; ?>"
                                               class="btn btn-edit btn-sm"> Edit</a>
                                            <a href="UTAMA.php?page=../Admin_lsp/hapus_admin_lsp.php&id=<?php echo $row['id_admin']; ?>" onclick="return confirmDelete(<?php echo $row['id_admin']; ?>, '<?php echo addslashes($row['nama_admin']); ?>')"
                                               class="btn btn-delete btn-sm"> Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
