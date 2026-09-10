<?php
$koneksi = mysqli_connect("localhost", "zee", "Admin123", "tester");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$cek_kolom_periode = @mysqli_query($koneksi, "SHOW COLUMNS FROM tb_skema LIKE 'id_periode'");
if ($cek_kolom_periode && mysqli_num_rows($cek_kolom_periode) === 0) {
    mysqli_query($koneksi, "ALTER TABLE tb_skema ADD COLUMN id_periode INT NULL DEFAULT NULL AFTER standar_kompetensi_kerja");
}
?>
