<?php
function fr_apl_sudah_isi_tabel($koneksi, $tabel, $kolom_id, $id)
{
    $id = intval($id);
    if ($id <= 0) {
        return false;
    }
    $r = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM `$tabel` WHERE `$kolom_id` = '$id' LIMIT 1"
    ));
    return $r && (int) $r['total'] > 0;
}

function fr_apl_sudah_ak01($koneksi, $id_asesi)
{
    return fr_apl_sudah_isi_tabel($koneksi, 'tb_ak01', 'id_asesi', $id_asesi);
}

function fr_apl_apl2_selesai_asesi($koneksi, $id_asesi)
{
    $id_asesi = intval($id_asesi);
    if ($id_asesi <= 0) {
        return false;
    }
    $r = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT tertanda FROM tb_apl2
         WHERE id_asesi = '$id_asesi'
         ORDER BY id_apl2 DESC LIMIT 1"
    ));
    return $r && trim((string) ($r['tertanda'] ?? '')) !== '';
}

function fr_apl_sudah_ak03_lengkap($koneksi, $id_asesi)
{
    $id_asesi = intval($id_asesi);
    if ($id_asesi <= 0) {
        return false;
    }
    $r = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT id_ak03 FROM tb_ak03
         WHERE id_asesi = '$id_asesi'
           AND tgl_selesai IS NOT NULL
           AND tgl_selesai != ''
         ORDER BY id_ak03 DESC LIMIT 1"
    ));
    return (bool) $r;
}

function fr_apl_ensure_ia01_stub($koneksi, $id_asesi, $id_apl1, $id_ak01, $id_asesor)
{
    $id_asesi  = intval($id_asesi);
    $id_apl1   = intval($id_apl1);
    $id_ak01   = intval($id_ak01);
    $id_asesor = intval($id_asesor);
    if ($id_asesi <= 0 || $id_apl1 <= 0 || $id_ak01 <= 0) {
        return false;
    }

    $cek = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT id_ia01 FROM tb_ia01
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1'
         LIMIT 1"
    ));
    if ($cek) {
        return true;
    }

    return (bool) mysqli_query(
        $koneksi,
        "INSERT INTO tb_ia01
            (id_apl1, id_ak01, id_asesi, id_asesor, tanggal, rekomendasi, umpan_balik, belum_kompeten)
         VALUES
            ('$id_apl1', '$id_ak01', '$id_asesi', '$id_asesor', NULL, NULL, NULL, NULL)"
    );
}

function fr_apl_ensure_ak02_stub($koneksi, $id_asesi, $id_apl1, $id_ak01, $id_asesor, $id_skema)
{
    $id_asesi  = intval($id_asesi);
    $id_apl1   = intval($id_apl1);
    $id_ak01   = intval($id_ak01);
    $id_asesor = intval($id_asesor);
    $id_skema  = intval($id_skema);
    if ($id_asesi <= 0 || $id_apl1 <= 0) {
        return false;
    }

    $cek = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT id_ak02 FROM tb_ak02
         WHERE id_asesi = '$id_asesi' AND id_apl1 = '$id_apl1'
         ORDER BY id_ak02 DESC LIMIT 1"
    ));
    if ($cek) {
        return true;
    }

    $res = mysqli_query(
        $koneksi,
        "INSERT INTO tb_ak02
            (id_apl1, id_ak01, id_asesi, id_asesor, rekomendasi, tindak_lanjut, komentar_asesor)
         VALUES
            ('$id_apl1', '$id_ak01', '$id_asesi', '$id_asesor', NULL, NULL, NULL)"
    );
    if (!$res) {
        return false;
    }

    $id_ak02 = mysqli_insert_id($koneksi);
    if ($id_skema > 0) {
        $qu = mysqli_query(
            $koneksi,
            "SELECT id_unit FROM tb_unit_kompetensi
             WHERE id_skema = '$id_skema' ORDER BY id_unit ASC"
        );
        while ($u = mysqli_fetch_assoc($qu)) {
            $id_unit = intval($u['id_unit']);
            mysqli_query(
                $koneksi,
                "INSERT INTO detail_ak02
                    (id_ak02, id_skema, id_unit, obs_demonstrasi, portofolio,
                     pyt_pihak_ketiga, pyt_wawancara, pyt_lisan, pyt_pertulis, proyek_kerja, lainnya)
                 VALUES
                    ('$id_ak02', '$id_skema', '$id_unit', 0, 0, 0, 0, 0, 0, 0, NULL)"
            );
        }
    }

    return true;
}
