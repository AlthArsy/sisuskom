<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = trim(str_replace($_SERVER['SCRIPT_NAME'], '', $request), '/');
$bypass = ['assets', 'phpmyadmin', 'pdf', 'sql', '_obfuscated'];
foreach ($bypass as $dir) {
    if (strpos($request, $dir) === 0 || strpos($request, "/$dir") === 0) {
        return false;
    }
}
$routes = [
    // LOGIN
    'login' => '/_obfuscated/LOGIN/login.php',
    'login.php' => '/_obfuscated/LOGIN/login.php',
    'login_admin' => '/_obfuscated/LOGIN/login_admin.php',
    'login_admin.php' => '/_obfuscated/LOGIN/login_admin.php',
    'proses' => '/_obfuscated/LOGIN/proses.php',
    'proses.php' => '/_obfuscated/LOGIN/proses.php',
    'logout' => '/_obfuscated/LOGIN/logout.php',
    'logout.php' => '/_obfuscated/LOGIN/logout.php',
    
    // BERANDA
    'utama' => '/_obfuscated/BERANDA/UTAMA.php',
    'utama.php' => '/_obfuscated/BERANDA/UTAMA.php',
    
    // PROFIL
    'profil' => '/_obfuscated/PROFIL/profil.php',
    'profil.php' => '/_obfuscated/PROFIL/profil.php',
    
    // ADM (Bukti Administratif)
    'adm/bukti_adm' => '/_obfuscated/ADM/bukti_adm.php',
    'adm/isi_bukti_adm' => '/_obfuscated/ADM/isi_bukti_adm.php',
    'adm/tambah_ba' => '/_obfuscated/ADM/Tambah_ba.php',
    'adm/ubah_ba' => '/_obfuscated/ADM/ubah_ba.php',
    'adm/hapus_ba' => '/_obfuscated/ADM/hapus_ba.php',
    
    // ASESI
    'asesi/table_asesi' => '/_obfuscated/ASESI/Table_asesi.php',
    'asesi/detail_asesi' => '/_obfuscated/ASESI/detail_asesi.php',
    'asesi/input_profil' => '/_obfuscated/ASESI/input_profil.php',
    'asesi/edit' => '/_obfuscated/ASESI/edit.php',
    'asesi/hapus_asesi' => '/_obfuscated/ASESI/hapus_asesi.php',
    
    // ASESOR
    'asesor/table_asesor' => '/_obfuscated/ASESOR/Table_asesor.php',
    'asesor/input_profil' => '/_obfuscated/ASESOR/input_profil.php',
    'asesor/edit' => '/_obfuscated/ASESOR/edit.php',
    'asesor/hapus_asesor' => '/_obfuscated/ASESOR/hapus_asesor.php',
    
    // ADMIN_LSP
    'admin_lsp/input_profil' => '/_obfuscated/Admin_lsp/input_profil.php',
    'admin_lsp/table_admin_lsp' => '/_obfuscated/Admin_lsp/Table_admin_lsp.php',
    
    // DASAR (Bukti Dasar)
    'dasar/bukti_dasar' => '/_obfuscated/DASAR/bukti_dasar.php',
    'dasar/isi_bukti_dasar' => '/_obfuscated/DASAR/isi_bukti_dasar.php',
    'dasar/tambah_bd' => '/_obfuscated/DASAR/Tambah_bd.php',
    'dasar/ubah_bd' => '/_obfuscated/DASAR/ubah_bd.php',
    'dasar/hapus_bd' => '/_obfuscated/DASAR/hapus_bd.php',
    
    // ELEMEN
    'elemen/elemen' => '/_obfuscated/ELEMEN/elemen.php',
    'elemen/from_elemen' => '/_obfuscated/ELEMEN/From_elemen.php',
    'elemen/ubah_elemen' => '/_obfuscated/ELEMEN/ubah_elemen.php',
    'elemen/hapus_elemen' => '/_obfuscated/ELEMEN/hapus_elemen.php',
    
    // KUK
    'kuk/kuk' => '/_obfuscated/KUK/KUK.php',
    'kuk/from_kuk' => '/_obfuscated/KUK/From_kuk.php',
    'kuk/ubah_kuk' => '/_obfuscated/KUK/ubah_kuk.php',
    'kuk/hapus_kuk' => '/_obfuscated/KUK/hapus_kuk.php',
    
    // SKEMA
    'skema/list_skema' => '/_obfuscated/SKEMA/list_skema.php',
    'skema/list_skema2' => '/_obfuscated/SKEMA/list_skema2.php',
    'skema/form_skema' => '/_obfuscated/SKEMA/Form_Skema.php',
    'skema/ubah_skema' => '/_obfuscated/SKEMA/Ubah_Skema.php',
    'skema/hapus_skema' => '/_obfuscated/SKEMA/Hapus_Skema.php',
    'skema/simpan_skema' => '/_obfuscated/SKEMA/simpan_skema.php',
    
    // UNIT
    'unit/unit_kompetensi' => '/_obfuscated/UNIT/unit_kompetensi.php',
    'unit/from_unit_kompetensi' => '/_obfuscated/UNIT/From_unit_kompetensi.php',
    'unit/ubah_unit' => '/_obfuscated/UNIT/Ubah_unit.php',
    'unit/hapus_unit' => '/_obfuscated/UNIT/hapus_unit.php',
    
    // FR_APL
    'fr_apl/fr_ak01' => '/_obfuscated/FR_APL/FR_AK01.php',
    'fr_apl/fr_ak02' => '/_obfuscated/FR_APL/FR_AK02.php',
    'fr_apl/fr_ak03' => '/_obfuscated/FR_APL/FR_AK03.php',
    'fr_apl/fr_ak05' => '/_obfuscated/FR_APL/FR_AK05.php',
    'fr_apl/fr_apl1' => '/_obfuscated/FR_APL/FR_APL1.php',
    'fr_apl/fr_apl02' => '/_obfuscated/FR_APL/FR_APL02.php',
    'fr_apl/fr_ia1' => '/_obfuscated/FR_APL/FR_IA1.php',
    'fr_apl/fr_ia06a' => '/_obfuscated/FR_APL/FR_IA06A.php',
    'fr_apl/fr_ia06c' => '/_obfuscated/FR_APL/FR_IA06C.php',
    'fr_apl/ambil_skema' => '/_obfuscated/FR_APL/ambil_skema.php',
    
    // LIST (Laporan)
    'list/list_form' => '/_obfuscated/list/list_form.php',
    'list/soal_ia06a' => '/_obfuscated/list/soal_ia06a.php',
    'list/rekap_fr' => '/_obfuscated/list/rekap_fr.php',
    'list/rekap_frapl2' => '/_obfuscated/list/rekap_frapl2.php',
    'list/rekap_ak01' => '/_obfuscated/list/rekap_ak01.php',
    'list/rekap_ak02' => '/_obfuscated/list/rekap_ak02.php',
    'list/rekap_ak3' => '/_obfuscated/list/rekap_ak3.php',
    'list/rekap_ia1' => '/_obfuscated/list/rekap_ia1.php',
    'list/rekap_ia06' => '/_obfuscated/list/rekap_ia06.php',
    'list/rekap_ak05' => '/_obfuscated/list/rekap_ak05.php',
    
    // MANAGEMENT
    'management/validator' => '/_obfuscated/MANAGEMENT/validator.php',
    'management/tampil2' => '/_obfuscated/MANAGEMENT/tampil2.php',
    
    // PENAGATURAN (Settings)
    'penagaturan/ubah' => '/_obfuscated/PENAGATURAN/ubah.php',
    'penagaturan/hapus' => '/_obfuscated/PENAGATURAN/hapus.php',
    'penagaturan/hapus_val' => '/_obfuscated/PENAGATURAN/hapus_val.php',
    'penagaturan/ubah_val' => '/_obfuscated/PENAGATURAN/ubah_val.php',
    'penagaturan/tambah-user-baru' => '/_obfuscated/PENAGATURAN/tambah-user-baru.php',
    'penagaturan/tambah-val-baru' => '/_obfuscated/PENAGATURAN/tambah-val-baru.php',
    
    // INCLUDES
    'includes/loading' => '/_obfuscated/INCLUDES/loading.php',
    
    // PDF/CETAK
    'cetak/cetak_ak1' => '/_obfuscated/pdf/cetak_ak1.php',
    'cetak/cetak_ak2' => '/_obfuscated/pdf/cetak_ak2.php',
    'cetak/cetak_ak3' => '/_obfuscated/pdf/cetak_ak3.php',
    'cetak/cetak_ak5' => '/_obfuscated/pdf/cetak_ak5.php',
    'cetak/cetak_apl1' => '/_obfuscated/pdf/cetak_apl1.php',
    'cetak/cetak_apl2' => '/_obfuscated/pdf/cetak_apl2.php',
    'cetak/cetak_ia1' => '/_obfuscated/pdf/cetak_ia1.php',
    'cetak/cetak_ia6a' => '/_obfuscated/pdf/cetak_ia6a.php',
    'cetak/cetak_ia6b' => '/_obfuscated/pdf/cetak_ia6b.php',
    'cetak/cetak_ia6c' => '/_obfuscated/pdf/cetak_ia6c.php',
    
    // Short aliases
    'rekap_frapl2' => '/_obfuscated/list/rekap_frapl2.php',
    'rekap_fr' => '/_obfuscated/list/rekap_fr.php',
];

if (isset($routes[$request])) {
    $_SERVER['REQUEST_URI'] = $routes[$request];
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . $routes[$request];
    include __DIR__ . $routes[$request];
    exit;
}

$request_lower = strtolower($request);
foreach ($routes as $route => $file) {
    if (strtolower($route) === $request_lower) {
        $_SERVER['REQUEST_URI'] = $file;
        $_SERVER['SCRIPT_FILENAME'] = __DIR__ . $file;
        include __DIR__ . $file;
        exit;
    }
}

http_response_code(404);
echo "404 - Page Not Found: $request";
exit;

