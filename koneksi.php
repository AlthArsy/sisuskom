<?php
$koneksi = mysqli_connect("localhost", "Zee", "Admin1234!", "tester");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
