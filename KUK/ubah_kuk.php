<?php
error_reporting(E_ALL);
imi_set('display_errors', 1);

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Asesor'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

include '../koneksi.php';

if(mysqli_connect_error()){
    die('Koneksi Gagal : '.mysqli_connect_error());
}