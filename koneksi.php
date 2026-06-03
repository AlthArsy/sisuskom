<?php
$koneksi = mysqli_connect("localhost", "root", "Admin123", "sisuskom");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
