<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../LOGIN/login.php"); exit;
}

$id_asesi = isset($_SESSION['id_asesi']) ? intval($_SESSION['id_asesi']) : 0;

function sudahIsi($koneksi, $tabel, $kolom_id, $id) {
    $id = intval($id);
    $r  = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM `$tabel` WHERE `$kolom_id` = '$id' LIMIT 1"));
    return $r && $r['total'] > 0;
}

$forms = [
    [
        'label'  => 'FR APL 1',
        'sub'    => 'Formulir Permohonan Sertifikasi Kompetensi',
        'isi'    => "../FR_APL/FR_APL1.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_APL1.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_apl1', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR APL 2',
        'sub'    => 'Asesmen Mandiri',
        'isi'    => "../FR_APL/FR_APL02.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_APL02.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_apl2', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR AK 1',
        'sub'    => 'Persetujuan Asesmen dan Kerahasiaan',
        'isi'    => "../FR_APL/FR_AK01.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_AK01.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_ak01', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR IA 1',
        'sub'    => 'Ceklis Observasi Aktivitas di Tempat Kerja',
        'isi'    => "../FR_APL/FR_IA1.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_IA1.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_ia01', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR AK 3',
        'sub'    => 'Umpan Balik dan Catatan Asesmen',
        'isi'    => "../FR_APL/FR_AK03.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_AK03.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_ak03', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR AK 2',
        'sub'    => 'Rekaman Asesmen Kompetensi',
        'isi'    => "../FR_APL/FR_AK02.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_AK02.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_ak02', 'id_asesi', $id_asesi),
    ],
    [
        'label'  => 'FR IA 6',
        'sub'    => 'DPT Pertanyaan Tertulis Esai',
        'isi'    => "../FR_APL/FR_IA06C.php?id_asesi=$id_asesi",
        'lihat'  => "../FR_APL/FR_IA06C.php?id_asesi=$id_asesi&view=1",
        'done'   => sudahIsi($koneksi, 'tb_ia06_jawaban', 'id_asesi', $id_asesi),
    ],
];

$semua_selesai = !in_array(false, array_column($forms, 'done'));
?>

    <title>Daftar Form</title>
    <style>

        .page-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 28px;
            color: #1a237e;
        }

        .page-title.selesai {
            color: #2e7d32;
        }

        .form-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 14px;
            border: 1px solid #ddd;
            background: #e8e8e8;
            transition: background 0.2s;
        }

        .form-card.done {
            background: #d4edda;
            border-color: #a5d6a7;
        }

        .form-card-left {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .form-label {
            font-size: 17px;
            font-weight: bold;
            color: #222;
        }

        .form-card.done .form-label {
            color: #1b5e20;
        }

        .form-sub {
            font-size: 12px;
            color: #666;
        }

        .form-card.done .form-sub {
            color: #388e3c;
        }

        .form-status {
            font-size: 12px;
            font-style: italic;
            color: #388e3c;
            margin-top: 2px;
        }

        .form-card-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-isi {
            background: #fff;
            color: #333;
            border: 1.5px solid #aaa;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-isi:hover {
            background: #f0f0f0;
        }

        .btn-lihat {
            background: #4caf50;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-lihat:hover {
            background: #388e3c;
        }

        .progress-wrap {
            margin-bottom: 24px;
        }

        .progress-label {
            font-size: 13px;
            color: #555;
            margin-bottom: 5px;
        }

        .progress-bar-bg {
            background: #ddd;
            border-radius: 20px;
            height: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #4caf50;
            border-radius: 20px;
            transition: width 0.3s;
        }

        @media (max-width: 520px) {
            .form-card { flex-direction: column; align-items: flex-start; gap: 12px; }
            .form-card-right { width: 100%; justify-content: flex-end; }
            .form-label { font-size: 15px; }
        }
    </style>


    <?php
    $total  = count($forms);
    $selesai_count = count(array_filter(array_column($forms, 'done')));
    $persen = $total > 0 ? round($selesai_count / $total * 100) : 0;
    ?>

    <div class="page-title <?php echo $semua_selesai ? 'selesai' : ''; ?>">
        <?php if ($semua_selesai): ?>
            Form Sudah Selesai
        <?php else: ?>
            Daftar Form
        <?php endif; ?>
    </div>

    <div class="progress-wrap">
        <div class="progress-label">
            Progress: <strong><?php echo $selesai_count; ?> / <?php echo $total; ?></strong> form terisi
            (<?php echo $persen; ?>%)
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: <?php echo $persen; ?>%;"></div>
        </div>
    </div>

    <?php foreach ($forms as $f): ?>
    <div class="form-card <?php echo $f['done'] ? 'done' : ''; ?>">

        <div class="form-card-left">
            <div class="form-label"><?php echo htmlspecialchars($f['label']); ?></div>
            <div class="form-sub"><?php echo htmlspecialchars($f['sub']); ?></div>
            <?php if ($f['done']): ?>
                <div class="form-status">Form sudah di isi</div>
            <?php endif; ?>
        </div>

        <div class="form-card-right">
            <?php if ($f['done']): ?>
                <a href="<?php echo $f['lihat']; ?>" class="btn-lihat">Lihat From</a>
            <?php else: ?>
                <a href="<?php echo $f['isi']; ?>" class="btn-isi">Isi Form</a>
            <?php endif; ?>
        </div>

    </div>
    <?php endforeach; ?>

