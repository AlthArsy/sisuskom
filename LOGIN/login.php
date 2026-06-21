<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../koneksi.php';
if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
$query_periode = "SELECT id_periode, tahun_ajaran FROM tb_periode ORDER BY id_periode DESC";
$result_periode = mysqli_query($koneksi, $query_periode);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login LSP Mudikal</title>
    <link rel="icon" type="image/x-icon" href="../assets/IMG/iconlogo.ico">
    <link rel="stylesheet" href="../assets/CSS/login.css">
</head>
<body>
<div class="l-container">
    <div class="header">
        <div class="header-icon">
            <source><img src="../assets/IMG/iconlogo.ico" alt="LSP Mudikal Logo" style=""></source>
        </div>
        <h1>LSP Mudikal</h1>
    </div>
    <div class="form-container">
        <form action="proses.php" method="POST" autocomplete="off"> 
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required
                    <?php if (isset($_GET['username'])): ?> value="<?php echo htmlspecialchars($_GET['username']); ?>" <?php endif; ?>>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                <label style="display: flex; align-items: center; gap: 7px; margin-top: 8px; font-size: 13px; color: #555; cursor: pointer;">
                    <input type="checkbox" onclick="showHide()" style="width: 15px; height: 15px; accent-color: #3730a3; cursor: pointer;">
                    Tampilkan Password
                </label>
                </div>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="Admin_lsp" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='admin_lsp'){echo 'selected';} ?>>Admin LSP</option>
                    <option value="Asesor" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='asesor'){echo 'selected';} ?>>Asesor</option>
                    <option value="Asesi" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='asesi'){echo 'selected';} ?>>Asesi</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun_ajaran">Tahun Ajaran</label>
                <select id="tahun_ajaran" name="tahun_ajaran" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php
                    if ($result_periode && mysqli_num_rows($result_periode) > 0) {
                        while ($row = mysqli_fetch_assoc($result_periode)) {
                            $selected = (isset($_GET['tahun_ajaran']) && $_GET['tahun_ajaran'] == $row['id_periode']) ? 'selected' : '';
                            echo "<option value='" . $row['id_periode'] . "' $selected>" . htmlspecialchars($row['tahun_ajaran']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Tidak ada data periode</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-primary">Masuk</button>
            </div>
            
            <div>
                <p style="margin-top: 15px; font-size: 14px; color: #555; text-align: center;">LOGIN SEBAGAI<a href="login_admin.php" style="color: #3730a3; text-decoration: none;"> ADMIN</a></p>
            </div>
        </form>
    </div>
</div>

<script>
function showHide() {
    var pw = document.getElementById("password");
    pw.type = pw.type === "password" ? "text" : "password";
}
</script>
</body>
</html> 
