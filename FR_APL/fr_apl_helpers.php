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

function fr_apl_normalize_date($v)
{
    $v = trim((string) $v);
    if ($v === '' || $v === '0000-00-00') {
        return '';
    }
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $v, $m)) {
        return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
    if (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/', $v, $m)) {
        return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
    }
    return '';
}

function fr_apl2_load_nilai($koneksi, $id_apl2)
{
    $out = [];
    $id_apl2 = intval($id_apl2);
    if ($id_apl2 <= 0) {
        return $out;
    }
    $rj = mysqli_query(
        $koneksi,
        "SELECT id_elemen, nilai FROM detail_apl2
         WHERE id_apl2 = '$id_apl2'
           AND nilai IN ('K', 'BK')
         ORDER BY id_detail_apl2 ASC"
    );
    if (!$rj) {
        return $out;
    }
    while ($j = mysqli_fetch_assoc($rj)) {
        $eid = intval($j['id_elemen']);
        $val = strtoupper(trim((string) $j['nilai']));
        if ($eid > 0 && in_array($val, ['K', 'BK'], true)) {
            $out[$eid] = $val;
        }
    }
    return $out;
}

function fr_apl2_sync_kuk($koneksi, $id_apl2, $id_skema, array $units)
{
    $id_apl2  = intval($id_apl2);
    $id_skema = intval($id_skema);
    if ($id_apl2 <= 0 || $id_skema <= 0) {
        return;
    }
    foreach ($units as $u) {
        $id_unit = intval($u['id_unit']);
        foreach ($u['elemen'] as $el) {
            $id_el = intval($el['id_elemen']);
            foreach ($el['kuk'] as $k) {
                $id_kuk = intval($k['id_kuk']);
                $cek = mysqli_fetch_assoc(mysqli_query(
                    $koneksi,
                    "SELECT id_detail_apl2 FROM detail_apl2
                     WHERE id_apl2='$id_apl2' AND id_kuk='$id_kuk' LIMIT 1"
                ));
                if (!$cek) {
                    mysqli_query(
                        $koneksi,
                        "INSERT INTO detail_apl2 (id_apl2, id_skema, id_unit, id_elemen, id_kuk, nilai)
                         VALUES ('$id_apl2','$id_skema','$id_unit','$id_el','$id_kuk','')"
                    );
                }
            }
        }
    }
}
