<?php 
ob_start();
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}
$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? 'User';
$nama_user = $_SESSION['nama_user'];


function get_initials($name) {
    if(!$name) return '';
    $words = explode(' ', $name);
    $res = '';
    foreach ($words as $w) {
        if ($w !== '') {
            $res .= strtoupper($w[0]);
            if(strlen($res) >= 2) break;
        }
    }
    return $res;
}
$roles_data = [
    'Admin' => [
        'icon' => 'fas fa-user-shield',
        'role_name' => '',
        'role_desc' => 'Administrator',
        'menu' => [
            [
                'href' => '../BERANDA/UTAMA.php',
                'icon' => 'fas fa-home',
                'label' => 'Dashboard',
                'active' => true
            ],
            [
                'href' => '../MANAGEMENT/tampil2.php',
                'icon' => 'fas fa-users',
                'label' => 'Manajemen Pengguna'
            ],
            [
                'href' => '../ASESI/Table_asesi.php',
                'icon' => 'fas fa-user-graduate',
                'label' => 'Manajemen Asesi'
            ],
                        [
                'href' => '../ASESOR/Table_asesor.php',
                'icon' => 'fas fa-user-tie',
                'label' => 'Manajemen Asesor'
            ],
            [
                'href' => '#',
                'icon' => 'fas fa-book',
                'label' => 'Manajemen Skema',
                'has_dropdown' => true,
                'submenu' => [
                    [
                        'href' => '../SKEMA/list_skema.php',
                        'icon' => 'fas fa-book',
                        'label' => 'Kelola Skema'
                    ],
                    [
                        'href' => '../SKEMA/list_skema2.php',
                        'icon' => 'fas fa-tasks',
                        'label' => 'Data Skema'
                    ]
                    // [
                    //     'href' => '../ELEMEN/elemen.php',
                    //     'icon' => 'fas fa-puzzle-piece',
                    //     'label' => 'Element'
                    // ],
                    // [
                    //     'href' => '../KUK/KUK.php',
                    //     'icon' => 'fas fa-check-circle',
                    //     'label' => 'KUK'
                    // ]
                ]
            ]
        ]
    ],
    'Asesor' => [
        'icon' => 'fas fa-user-tie',
        'role_name' => '',
        'role_desc' => '',
        'menu' => [
            [
                'href' => 'UTAMA.php',
                'icon' => 'fas fa-home',
                'label' => 'Dashboard',
                'active' => true
            ],            
            [
                'href' => '#',
                'icon' => 'fas fa-book',
                'label' => 'Manajemen Skema',
                'has_dropdown' => true,
                'submenu' => [
                    [
                        'href' => '../SKEMA/list_skema.php',
                        'icon' => 'fas fa-book',
                        'label' => 'Kelola Skema'
                    ],
                    [
                        'href' => '../SKEMA/list_skema2.php',
                        'icon' => 'fas fa-tasks',
                        'label' => 'Data Skema'
                    ]
                    // [
                    //     'href' => '../ELEMEN/elemen.php',
                    //     'icon' => 'fas fa-puzzle-piece',
                    //     'label' => 'Element'
                    // ],
                    // [
                    //     'href' => '../KUK/KUK.php',
                    //     'icon' => 'fas fa-check-circle',
                    //     'label' => 'KUK'
                    // ]
                ]
            ]
        ]
    ],
    'Asesi' => [
        'icon' => 'fas fa-user-graduate',
        'role_name' => '',
        'role_desc' => '',
        'menu' => [
            [
                'href' => 'UTAMA.php',
                'icon' => 'fas fa-home',
                'label' => 'Dashboard',
                'active' => true
            ]
        ]
    ]
];

$user_data = isset($roles_data[$role]) ? $roles_data[$role] : $roles_data['Admin'];
$init = get_initials($username);
if (!$init) $init = strtoupper(substr($role,0,2));

$current_page = basename($_SERVER['PHP_SELF']);
$allowed_pages = [
    'UTAMA.php',
    '../MANAGEMENT/tampil2.php',
    '../SKEMA/list_skema.php',
    '../SKEMA/list_skema2.php',
    '../UNIT/unit_kompetensi.php',
    '../SKEMA/Form_Skema.php',
    '../ELEMEN/elemen.php',
    '../KUK/KUK.php',
    '../SKEMA/simpan_skema.php',
    '../ASESI/Table_asesi.php',
    '../ASESOR/Table_asesor.php',
    '../PENAGATURAN/tambah-user-baru.php',
    '../PENAGATURAN/ubah.php',
    '../UNIT/From_unit_kompetensi.php',
    '../SKEMA/Ubah_Skema.php',
    '../ELEMEN/From_elemen.php',
    '../KUK/From_kuk.php',
    '../PENAGATURAN/ubah.php',
    '../PENAGATURAN/hapus.php',
    '../ASESOR/edit.php',
    '../ASESOR/hapus_asesor.php',
    '../',
    '../ASESI/detail_asesi.php',
    '../ASESI/edit.php',
    '../ASESI/hapus_asesi.php',
    '../PROFIL/profil.php',
    '../'
];

$page_to_include = '';
if (isset($_GET['page']) && in_array($_GET['page'], $allowed_pages)) {
    $page_to_include = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Manajemen LSP</title>
    <link rel="stylesheet" href="../assets/CSS/utama.css">   
    <link rel="icon" type="image/png" href="../assets/IMG/Mudikal.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <?php include '../INCLUDES/loading.php'; ?>
    <div class="container">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        <aside class="sidebar" id="sidebar">
            <div class="profile-section">
                <h2><?=htmlspecialchars(strtoupper($username))?></h2>
                <p><?=htmlspecialchars($user_data['role_desc'])?></p>
            </div>

            <div class="nav-header">NAVIGASI UTAMA</div>

            <nav class="nav-menu">
                <?php
                foreach ($user_data['menu'] as $index => $item) {
                    $href = $item['href'];
                    $is_active = '';
                    $has_dropdown = isset($item['has_dropdown']) && $item['has_dropdown'];
                    
                    $submenu_active = false;
                    if ($has_dropdown && isset($item['submenu'])) {
                        foreach ($item['submenu'] as $sub) {
                            if ($page_to_include === $sub['href']) {
                                $submenu_active = true;
                                break;
                            }
                        }
                    }
                    
                    if ($href === 'UTAMA.php') {
                        if (!$page_to_include) {
                            $is_active = 'active';
                        }
                    } elseif ($page_to_include === $href || $submenu_active) {
                        $is_active = 'active';
                    }
                    
                    if ($has_dropdown) {
                        echo '<div class="menu-item '.$is_active.'" onclick="toggleDropdown('.$index.')">';
                        echo '<div class="nav-item-content">';
                        echo '<i class="'.$item['icon'].'"></i>';
                        echo '<span>'.$item['label'].'</span>';
                        echo '</div>';
                        echo '<i class="fas fa-chevron-down dropdown-arrow" id="arrow-'.$index.'"></i>';
                        echo '</div>';
                        
                        $submenu_open = $submenu_active ? 'open' : '';
                        echo '<div class="submenu '.$submenu_open.'" id="submenu-'.$index.'">';
                        foreach ($item['submenu'] as $subitem) {
                            $sub_active = ($page_to_include === $subitem['href']) ? 'active' : '';
                            echo '<a href="?page='.htmlspecialchars($subitem['href']).'" class="'.$sub_active.'">';
                            echo '<div class="nav-item-content">';
                            echo '<i class="'.$subitem['icon'].'"></i>';
                            echo '<span>'.$subitem['label'].'</span>';
                            echo '</div>';
                            echo '</a>';
                        }
                        echo '</div>';
                    } else {
                        if ($href === 'UTAMA.php') {
                            $href = '?';
                        } elseif ($href !== '#') {
                            $href = '?page=' . $href;
                        }
                        
                        echo '<a href="'.htmlspecialchars($href).'" class="'.$is_active.'">';
                        echo '<div class="nav-item-content">';
                        echo '<i class="'.$item['icon'].'"></i>';
                        echo '<span>'.$item['label'].'</span>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                ?>
                <a href="#" onclick="event.preventDefault(); { window.location.href = 'UTAMA.php?page=../PROFIL/profil.php'; }">
                    <div class="nav-item-content">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </div>
                </a>
                <a href="#" onclick="event.preventDefault(); { window.location.href = '../LOGIN/logout.php'; }">
                    <div class="nav-item-content">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </div>
                </a>
            </nav>
            <div class="developer">
                <div class="copyright">
                    © 2025 Dev : <a href="http://althaf.com">Althaf Dan Riyan</a></div>
                <div class="version">
                    <b>Version: </b> M1
                </div>        
            </div>
        </aside>

        <main class="main-content" id="mainContent">
            <div class="breadcrumb">
                <button class="toggle-sidebar-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <?php 
                if ($page_to_include) {
                    $breadcrumb_found = false;
                    foreach ($user_data['menu'] as $item) {
                        if ($item['href'] === $page_to_include) {
                            echo htmlspecialchars($item['label']);
                            $breadcrumb_found = true;
                            break;
                        }
                        if (isset($item['submenu'])) {
                            foreach ($item['submenu'] as $subitem) {
                                if ($subitem['href'] === $page_to_include) {
                                    echo htmlspecialchars($item['label']) . ' / ' . htmlspecialchars($subitem['label']);
                                    $breadcrumb_found = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    if (!$breadcrumb_found) {
                        echo 'Dashboard';
                    }
                } else {
                    echo 'Dashboard';
                }
                ?>
            </div>

            <?php
            if ($page_to_include && file_exists($page_to_include)) {
                include $page_to_include;
            } else {
            ?>
            <div class="content-card">
                <h2>Beranda</h2>
                
                <p class="welcome-text">
                    Selamat datang <strong><?=htmlspecialchars($nama_user)?></strong>!
                </p>
            </div>
            <?php } ?>
        </main>
    </div>

    <script>
        function toggleDropdown(index) {
            const submenu = document.getElementById('submenu-' + index);
            const arrow = document.getElementById('arrow-' + index);
            
            submenu.classList.toggle('open');
            arrow.classList.toggle('rotated');
        }

        function initSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            overlay.classList.toggle('active');
            
            if (window.innerWidth > 768) {
                mainContent.classList.toggle('expanded');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        const sidebar = document.getElementById('sidebar');
                        const overlay = document.getElementById('sidebarOverlay');
                        
                        if (!sidebar.classList.contains('collapsed')) {
                            sidebar.classList.add('collapsed');
                            overlay.classList.remove('active');
                        }
                    }
                });
            });
        });

        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth > 768) {
                overlay.classList.remove('active');
                sidebar.classList.remove('collapsed');
            } else {
                mainContent.classList.remove('expanded');
                sidebar.classList.add('collapsed');
            }
        });

        initSidebar();
    </script>
</body>
</html>