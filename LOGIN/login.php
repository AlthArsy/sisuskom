<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login LSP Mudikal</title>
    <link rel="icon" type="image/png" href="../assets/IMG/Mudikal.png">
    <link rel="stylesheet" href="../assets/CSS/login.css">
       <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="l-container">
        <div class="header">
            <i class="fas fa-sign-in-alt"></i>
            <h1>Login</h1>
        </div>

        <div class="form-container">
            <form action="proses.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Username" require
                        <?php if (isset($_GET['username'])): ?> value="<?php echo htmlspecialchars($_GET['username']); ?>" <?php endif; ?>
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Password" required >
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="Admin" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='admin'){echo 'selected';} ?>>Admin</option>
                        <option value="Asesor" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='asesor'){echo 'selected';} ?>>Asesor</option>
                        <option value="Asesi" <?php if(isset($_GET['role']) && strtolower($_GET['role'])=='asesi'){echo 'selected';} ?>>Asesi</option>
                    </select>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>  
</body>
</html>