<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login LSP Mudikal</title>
    <link rel="icon" type="image/x-icon" href="../assets/IMG/iconlogo.ico">
    <link rel="stylesheet" href="../assets/CSS/login.css">
       <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
</head>
<body>
<div class="l-container">
    <div class="header">
        <div class="header-icon">
            <source><img src="../assets/IMG/iconlogo.ico" alt="LSP Mudikal Logo" style=""></source>
        </div>
        <h1>ADMIN Utama Mudikal</h1>
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
                    <option value="Admin_utm" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='admin_utm'){echo 'selected';} ?>>Admin UTAMA</option>
                </select>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn-primary">Masuk</button>
            </div>
            <div>
                <p style="margin-top: 15px; font-size: 14px; color: #555;"><a href="login.php" style="color: #3730a3; text-decoration: none;">Kembali</a></p>
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
