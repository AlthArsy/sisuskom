<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if (mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_error($koneksi));
}

$message = '';
$message_type = ''; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
   
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    }
    
    if (empty($role)) {
        $errors[] = "Role harus dipilih";
    }
    
    
    if (strlen($username) > 50) {
        $errors[] = "Username maksimal 50 karakter";
    }
    
    if (strlen($password) > 255) {
        $errors[] = "Password terlalu panjang";
    }
    
    
    $check_sql = "SELECT id_user FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $errors[] = "Username sudah digunakan, silakan pilih username lain";
    }
    mysqli_stmt_close($check_stmt);
    
    
    if (empty($errors)) {

        // $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $password_hashed = $password;
        
        $allowed_roles = ['Admin', 'Asesor', 'Asesi'];
        // if (!in_array($role, $allowed_roles)) {
        //     // Coba lowercase jika uppercase error
        //     $role_lower = strtolower($role);
        //     if (in_array($role_lower, array_map('strtolower', $allowed_roles))) {
        //         $role = ucfirst($role_lower);
        //     } else {
        //         $errors[] = "Role tidak valid. Pilih antara: Admin, Asesor, atau Asesi";
        //     }
        // }
        
        if (empty($errors)) {
            $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
            
            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "sss", $username, $password_hashed, $role);
                
                try {
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $message = "User baru berhasil ditambahkan!";
                        $message_type = 'success';
                        
                        
                        $_POST = [];
                    }
                } catch (mysqli_sql_exception $e) {
                   
                    if (strpos($e->getMessage(), 'Data truncated for column') !== false) {
                        $message = "Error: Nilai role tidak valid untuk database. Silakan pilih role yang sesuai.";
                    } else {
                        $message = "Gagal menambahkan user: " . $e->getMessage();
                    }
                    $message_type = 'error';
                }
                mysqli_stmt_close($insert_stmt);
            } else {
                $message = "Gagal mempersiapkan statement: " . mysqli_error($koneksi);
                $message_type = 'error';
            }
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = 'error';
    }
}
?>
    <link rel="stylesheet" href="../assets/CSS/ubah_manajeman.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <div class="l-container">
        <div class="header">
            <i class="fas fa-user-plus"></i>
            <div>
                <h1>Tambah User Baru</h1>
                <p>Tambahkan user baru ke dalam sistem</p>
            </div>
        </div>
        
        <div class="user-info">
            <i class="fas fa-user-circle"></i> Logged in sebagai: 
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span> 
            (Role: <span><?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></span>)
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" action="" id="tambahUserForm">
                <div class="form-group">
                    <label for="username" class="required">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required
                           maxlength="50"
                           placeholder="Masukkan username">
                    <span class="form-hint">Username harus unik dan maksimal 50 karakter</span>
                </div>
                
                <div class="form-group">
                    <label for="password" class="required">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="text" 
                           id="password" 
                           name="password" 
                           value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>"
                           required
                           maxlength="255"
                           placeholder="Masukkan password">
                    <span class="form-hint">Password untuk login user</span>
                    <div id="password-strength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="role" class="required">
                        <i class="fas fa-user-tag"></i> Role
                    </label>
                    <select id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="Admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="Asesor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Asesor') ? 'selected' : ''; ?>>Asesor</option>
                        <option value="Asesi" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Asesi') ? 'selected' : ''; ?>>Asesi</option>
                    </select>
                    <span class="form-hint">Hak akses user dalam sistem</span>
                </div>
                
                <div class="btn-container">
                    <a href="../BERANDA/UTAMA.php?page=../MANAGEMENT/tampil2.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('tambahUserForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const role = document.getElementById('role').value;
            
            let errors = [];
            
            if (!username) {
                errors.push('Username harus diisi');
            } else if (username.length > 50) {
                errors.push('Username maksimal 50 karakter');
            }
            
            if (!password) {
                errors.push('Password harus diisi');
            } else if (password.length > 255) {
                errors.push('Password terlalu panjang');
            }
            
            if (!role) {
                errors.push('Role harus dipilih');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
                return false;
            }
        });
        
    </script>