<?php
function rekap_esc($koneksi, $v)
{
    return mysqli_real_escape_string($koneksi, (string) $v);
}

function rekap_params()
{
    return [
        'filter' => $_GET['filter'] ?? 'semua',
        'cari'   => trim($_GET['cari'] ?? ''),
    ];
}
function rekap_qs($filter, $cari, $extra = [])
{
    $q = ['filter' => $filter];
    if ($cari !== '') {
        $q['cari'] = $cari;
    }
    foreach ($extra as $k => $v) {
        if ($v !== '' && $v !== null) {
            $q[$k] = $v;
        }
    }
    return http_build_query($q);
}

// function rekap_sql_batas_2bulan($kolom_tanggal)
// {
//     $batas = date('Y-m-d', strtotime('-2 months'));
//     return " AND ({$kolom_tanggal} IS NULL OR {$kolom_tanggal} = '' OR {$kolom_tanggal} >= '{$batas}')";
// }

function rekap_sql_cari($koneksi, $cari, array $kolom)
{
    if ($cari === '') {
        return '';
    }
    $like = '%' . rekap_esc($koneksi, $cari) . '%';
    $parts = [];
    foreach ($kolom as $col) {
        $parts[] = "{$col} LIKE '{$like}'";
    }
    return ' AND (' . implode(' OR ', $parts) . ')';
}

function rekap_sql_asesor($role, $id_asesor, $kolom = 'id_asesor')
{
    if ($role === 'Asesor' && $id_asesor) {
        return " AND {$kolom} = '" . intval($id_asesor) . "'";
    }
    return '';
}

function rekap_sql_filter_status($filter, $tipe)
{
    if ($filter === 'semua') {
        return '';
    }

    switch ($tipe) {
        case 'apl1':
            if ($filter === 'belum')    return " AND (a.rekomendasi IS NULL OR a.rekomendasi = '')";
            if ($filter === 'diterima') return " AND a.rekomendasi = 'Diterima'";
            if ($filter === 'ditolak')  return " AND a.rekomendasi = 'Tidak Diterima'";
            // if ($filter === 'selesai')  return " AND a.rekomendasi IS NOT NULL AND a.rekomendasi != ''";
            break;

        case 'apl2':
            if ($filter === 'belum')        return " AND (a.rekomendasi IS NULL OR a.rekomendasi = '')";
            if ($filter === 'Dapat')        return " AND a.rekomendasi = 'Dapat'";
            if ($filter === 'Tidak Dapat')  return " AND a.rekomendasi = 'Tidak Dapat'";
            // if ($filter === 'selesai')      return " AND a.tertanda IS NOT NULL AND a.tertanda != '' AND a.rekomendasi IS NOT NULL AND a.rekomendasi != ''";
            break;

        case 'ak02':
            if ($filter === 'belum')          return " AND (ak.rekomendasi IS NULL OR ak.rekomendasi = '')";
            if ($filter === 'kompeten')       return " AND ak.rekomendasi = 'Kompeten'";
            if ($filter === 'belum_kompeten') return " AND ak.rekomendasi = 'Belum Kompeten'";
            // if ($filter === 'selesai')        return " AND ak.rekomendasi IS NOT NULL AND ak.rekomendasi != ''";
            break;

        case 'ia01':
            if ($filter === 'belum')          return " AND (i.rekomendasi IS NULL OR i.rekomendasi = '')";
            if ($filter === 'kompeten')       return " AND i.rekomendasi = 'Kompeten'";
            if ($filter === 'belum_kompeten') return " AND i.rekomendasi = 'Belum Kompeten'";
            // if ($filter === 'selesai')        return " AND i.rekomendasi IS NOT NULL AND i.rekomendasi != ''";
            break;

        case 'ia06':
            if ($filter === 'belum')          return " AND (i.aspek IS NULL OR i.aspek = '')";
            if ($filter === 'tercapai')       return " AND i.aspek = 'tercapai'";
            if ($filter === 'belum_tercapai') return " AND i.aspek = 'belum_tercapai'";
            // if ($filter === 'selesai')        return " AND i.aspek IS NOT NULL AND i.aspek != ''";
            break;
    }

    return '';
}

function rekap_render_cari($base, $page_path, $filter, $cari)
{
    $page = htmlspecialchars($page_path, ENT_QUOTES, 'UTF-8');
    $val  = htmlspecialchars($cari, ENT_QUOTES, 'UTF-8');
    $qs_reset = rekap_qs('semua', '');
    ?>
    <form class="cari rekap-cari" method="get" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="page" value="<?= $page ?>">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') ?>">
        <div class="cari-field">
            <input type="search" name="cari" value="<?= $val ?>"
                   placeholder="Cari nama asesi, judul skema, atau nomor skema…"
                   autocomplete="off">
        </div>
        <div class="cari-actions">
            <button type="submit" class="btn-cari">Cari</button>
            <?php if ($cari !== ''): ?>
            <a class="btn-reset" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>?page=<?= urlencode($page_path) ?>&<?= $qs_reset ?>">Reset</a>
            <?php endif; ?>
        </div>
    </form>
    <!-- <p class="rekap-info-batas">Data lebih dari 2 bulan otomatis disembunyikan dari rekap.</p> -->
    <?php
}

function rekap_count($koneksi, $sql)
{
    $r = mysqli_query($koneksi, $sql);
    if (!$r) {
        return 0;
    }
    $row = mysqli_fetch_assoc($r);
    return (int) ($row['c'] ?? 0);
}
