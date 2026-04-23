<?php
header('Content-Type: application/json; charset=utf-8');
include "../koneksi.php";

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

if ($action === 'search') {
    $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
    $keyword_esc = mysqli_real_escape_string($koneksi, $keyword);

    $sql = "SELECT id_skema, judul_skema, nomor_skema, standar_kompetensi_kerja
            FROM tb_skema
            WHERE judul_skema LIKE '%{$keyword_esc}%'
               OR nomor_skema LIKE '%{$keyword_esc}%'
            ORDER BY judul_skema ASC
            LIMIT 10";

    $result = mysqli_query($koneksi, $sql);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    echo json_encode(['status' => 'ok', 'data' => $data]);
    exit;
}

//detail skema
if ($action === 'detail') {
    $id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;

    if (!$id_skema) {
        echo json_encode(['status' => 'error', 'message' => 'id_skema tidak valid']);
        exit;
    }

    $sql_skema = "SELECT s.id_skema, s.nomor_skema, s.judul_skema,
                         s.standar_kompetensi_kerja,
                         a.nama_asesor, a.no_reg
                  FROM tb_skema s
                  LEFT JOIN tb_asesor a ON a.id_asesor = s.id_asesor
                  WHERE s.id_skema = '$id_skema'
                  LIMIT 1";
    $res_skema = mysqli_query($koneksi, $sql_skema);
    $skema = mysqli_fetch_assoc($res_skema);

    if (!$skema) {
        echo json_encode(['status' => 'error', 'message' => 'Skema tidak ditemukan']); 
        exit;
    }

    $sql_unit = "SELECT id_unit, kode_unit, judul_unit
                 FROM tb_unit_kompetensi
                 WHERE id_skema = '$id_skema'
                 ORDER BY id_unit ASC";
    $res_unit = mysqli_query($koneksi, $sql_unit);
    $units = [];
    if ($res_unit) {
        while ($u = mysqli_fetch_assoc($res_unit)) {
            $units[] = $u;
        }
    }

    echo json_encode([
        'status'  => 'ok',
        'skema'   => $skema,
        'units'   => $units
    ]);
    exit;
}
//APl 02
if ($action === 'apl2') {
    $id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
    if (!$id_skema) {
        echo json_encode(['status' => 'error', 'message' => 'id_skema tidak valid']);
        exit;
    }

    $res_sk = mysqli_query($koneksi,
        "SELECT s.*, a.nama_asesor, a.no_reg
         FROM tb_skema s
         LEFT JOIN tb_asesor a ON a.id_asesor = s.id_asesor
         WHERE s.id_skema = '$id_skema' LIMIT 1");
    $skema = mysqli_fetch_assoc($res_sk);
    if (!$skema) {
        echo json_encode(['status' => 'error', 'message' => 'Skema tidak ditemukan']);
        exit;
    }

    $res_unit = mysqli_query($koneksi,
        "SELECT * FROM tb_unit_kompetensi WHERE id_skema = '$id_skema' ORDER BY id_unit ASC");
    $units = [];
    while ($u = mysqli_fetch_assoc($res_unit)) {
        $id_unit = intval($u['id_unit']);

        $res_el = mysqli_query($koneksi,
            "SELECT * FROM tb_elemen WHERE id_unit = '$id_unit' ORDER BY id_elemen ASC");
        $elemen = [];
        while ($el = mysqli_fetch_assoc($res_el)) {
            $id_el = intval($el['id_elemen']);

            $res_kuk = mysqli_query($koneksi,
                "SELECT * FROM tb_kuk WHERE id_elemen = '$id_el' ORDER BY id_kuk ASC");
            $kuks = [];
            while ($k = mysqli_fetch_assoc($res_kuk)) {
                $kuks[] = $k;
            }
            $el['kuk'] = $kuks;
            $elemen[] = $el;
        }
        $u['elemen'] = $elemen;
        $units[] = $u;
    }

    echo json_encode(['status' => 'ok', 'skema' => $skema, 'units' => $units]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenal']);