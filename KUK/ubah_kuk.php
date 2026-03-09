<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["role"]) || !in_array( $_SESSION["role"], ['Admin','Asesor'])) {
    header('Location: ../LOGIN/login.php');
    exit();
}
include '../koneksi.php';
 
if(mysqli_connect_errno()) {
    die("Gagal koneksi ke database: " . mysqli_connect_errno());
}

$error_message = "";
$succes_message = "";
$data_elemen = null;
$id_unit = 0;
$id_elemen = iseet($_GET["id"]) ? intval($_GET["id"]) :0;

if($id_elemen > 0) {
    $query = "SELECT e.*, u.id_unit, u.kode_unit, s.id_asesor, a.nama_asesor
            FROM tb_elemen e
            JOIN tb_unit_kompetensi u ON e.id_skema = s.id_skema
            LEFT JOIN tb_asesor a ON s.id_asesor = a.id_asesor
            WHERE e.id_elemen =?";

            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_elemen);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if($result && mysqli_stmt_num_rows($result) > 0) {
                $data_elemen = mysqli_fetch_assoc($result);
                $id_unit = $data_elemen["id_unit"];

                if($_SESSION["role"] === "Asesor") {
                    if(!isset($_SESSION["id_referensi"])) {
                        $username = $_SESSION["username"];
                        $get_asesor = "SELECT id_asesor FROM tb_asesor WHERE nama_asesor = ?";
                    
                        $stmt_asesor = mysqli_prepare($koneksi, $get_asesor);
                        mysqli_stmt_bind_param($stmt_asesor,"s", $username);
                        mysqli_stmt_execute($stmt_asesor);
                        $result_asesor = mysqli_stmt_get_result($stmt_asesor);
                         if($row_asesor = mysqli _fetch_assoc($result_asesor)) {
                            $_SESSION["id_referensi"] = $roe_asesor["id_asesor"];
                         } else {
                            $_SESSION["id_referensi"] = "0";
                         }
                }
            }
        }
}