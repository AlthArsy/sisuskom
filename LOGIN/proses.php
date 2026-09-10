<?php
include "../koneksi.php";

$username = isset($_POST['username']) ? trim($_POST['username']) : (isset($_GET['username']) ? trim($_GET['username']) : '');
$password = isset($_POST['password']) ? trim($_POST['password']) : (isset($_GET['password']) ? trim($_GET['password']) : '');
$role_raw = isset($_POST['role']) ? trim($_POST['role']) : (isset($_GET['role']) ? trim($_GET['role']) : '');
$id_periode_login = isset($_POST['tahun_ajaran']) ? (int) $_POST['tahun_ajaran'] : 0;

$role_map = [
    'admin_utm' => 'Admin_utm',
    'admin_lsp' => 'Admin_lsp',
    'asesor' => 'Asesor',
    'asesi' => 'Asesi',
];
$role_key = strtolower($role_raw);
$role = isset($role_map[$role_key]) ? $role_map[$role_key] : '';

if ($role !== '' && $role !== 'Admin_utm' && $id_periode_login <= 0) {
    echo "<script>alert('Silakan pilih Tahun Ajaran terlebih dahulu!'); window.location.href='../LOGIN/login.php';</script>";
    exit;
}

if (!empty($role_key) && $role !== '' && !empty($password) && !empty($username)) {

    if ($role === 'Admin_utm') {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users_admin WHERE username = ? AND role = ? LIMIT 1");
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? AND role = ? LIMIT 1");
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (strlen($user['password']) > 40) {
            $pass_ok = password_verify($password, $user['password']);
        } else {
            $pass_ok = md5($password) === $user['password'];
        }

        if ($pass_ok) {
            session_start();
            $_SESSION['username'] = $user['username'] ?? '';
            $_SESSION['role'] = $user['role'];
            $_SESSION['id_user'] = $user['id_user'] ?? ($user['id_user_admin'] ?? 0);
            $_SESSION['id_asesor'] = $user['id_asesor'] ?? null;
            $_SESSION['id_asesi'] = $user['id_asesi'] ?? null;
            $_SESSION['id_admin'] = $user['id_admin'] ?? null;

            if ($role !== 'Admin_utm') {
                $id_periode_user = isset($user['id_periode']) ? (int) $user['id_periode'] : 0;

                if ($id_periode_user <= 0) {
                    echo "<script>alert('Akun belum memiliki Tahun Ajaran. Hubungi Admin untuk mengatur periode user.'); window.location.href='../LOGIN/login.php';</script>";
                    exit;
                }

                if ($id_periode_user !== $id_periode_login) {
                    echo "<script>alert('Tahun Ajaran tidak sesuai dengan periode akun Anda. Pilih tahun ajaran yang benar atau hubungi Admin.'); window.location.href='../LOGIN/login.php';</script>";
                    exit;
                }

                $_SESSION['id_periode'] = $id_periode_user;

                $stmt_periode = mysqli_prepare($koneksi, "SELECT tahun_ajaran FROM tb_periode WHERE id_periode = ? LIMIT 1");
                if ($stmt_periode) {
                    mysqli_stmt_bind_param($stmt_periode, "i", $id_periode_user);
                    mysqli_stmt_execute($stmt_periode);
                    $r_periode = mysqli_stmt_get_result($stmt_periode);
                    if ($r_periode && ($data_periode = mysqli_fetch_assoc($r_periode))) {
                        $_SESSION['tahun_ajaran'] = $data_periode['tahun_ajaran'];
                    }
                    mysqli_stmt_close($stmt_periode);
                }
            }

            if (!empty($user['id_admin']) && !is_null($user['id_admin'])) {
                $id_admin = $user['id_admin'];

                if ($role === 'Admin_lsp') {

                    $profil = mysqli_query($koneksi, "SELECT nama_admin FROM tb_admin WHERE id_admin = '$id_admin'");
                    $data_profil = mysqli_fetch_assoc($profil);

                    if ($data_profil) {
                        $_SESSION['nama_user'] = $data_profil['nama_admin'];
                    }
                }
            }

            if (!empty($user['id_asesor']) && !is_null($user['id_asesor'])) {
                $id_asesor = $user['id_asesor'];

                if ($role === 'Asesor') {

                    $profil = mysqli_query($koneksi, "SELECT nama_asesor FROM tb_asesor WHERE id_asesor = '$id_asesor'");
                    $data_profil = mysqli_fetch_assoc($profil);

                    if ($data_profil) {
                        $_SESSION['nama_user'] = $data_profil['nama_asesor'];
                    }
                }
            }

            if (!empty($user['id_asesi']) && !is_null($user['id_asesi'])) {
                $id_asesi = $user['id_asesi'];

                if ($role === 'Asesi') {
                    $profil = mysqli_query($koneksi, "SELECT nama_asesi FROM tb_asesi WHERE id_asesi = '$id_asesi'");
                    $data_profil = mysqli_fetch_assoc($profil);

                    if ($data_profil) {
                        $_SESSION['nama_user'] = $data_profil['nama_asesi'];
                    }
                }
            }

            if ($role === 'Admin_lsp') {
                if (empty($user['id_admin']) || is_null($user['id_admin'])) {
                    if ($role === 'Admin_lsp') {
                        echo "<script>alert('Silakan lengkapi profil terlebih dahulu.'); window.location.href='../Admin_lsp/input_profil.php';</script>";
                        exit;
                    }
                }
            }

            if ($role === 'Asesor') {
                if (empty($user['id_asesor']) || is_null($user['id_asesor'])) {
                    if ($role === 'Asesor') {
                        echo "<script>alert('Silakan lengkapi profil terlebih dahulu.'); window.location.href='../ASESOR/input_profil.php';</script>";
                        exit;
                    }
                }
            }

            if ($role === 'Asesi') {
                if (empty($user['id_asesi']) || is_null($user['id_asesi'])) {
                    if ($role === 'Asesi') {
                        echo "<script>alert('Silakan lengkapi profil terlebih dahulu.'); window.location.href='../ASESI/input_profil.php';</script>";
                        exit;
                    }
                }
            }

            if ($role === 'Admin_utm') {
                echo "<script>alert('Login berhasil sebagai ADMIN UTAMA'); window.location.href='../BERANDA/UTAMA.php';</script>";
            } elseif ($role === 'Admin_lsp') {
                echo "<script>alert('Login berhasil sebagai ADMIN LSP'); window.location.href='../BERANDA/UTAMA.php';</script>";
            } elseif ($role === 'Asesor') {
                echo "<script>alert('Login berhasil sebagai Asesor'); window.location.href='../BERANDA/UTAMA.php';</script>";
            } elseif ($role === 'Asesi') {
                echo "<script>alert('Login berhasil sebagai Asesi'); window.location.href='../BERANDA/UTAMA.php';</script>";
            } else {
                echo "<script>alert('Role tidak dikenali!'); window.location.href='../LOGIN/login.php';</script>";
            }
        } else {
            echo "<script>alert('Password salah!'); window.location.href='../LOGIN/login.php';</script>";
        }
    } else {
        echo "<script>alert('Username atau Role tidak sesuai!'); window.location.href='../LOGIN/login.php';</script>";
    }

    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
} else {
    if (isset($role) && $role === 'Admin_lsp' && empty($password)) {
        echo "<script>alert('Harap isi password!'); window.location.href='../LOGIN/login.php';</script>";
    } else {
        echo "<script>alert('Harap isi semua field!'); window.location.href='../LOGIN/login.php';</script>";
    }
}

mysqli_close($koneksi);
?>
