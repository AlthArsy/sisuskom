<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();
include "../koneksi.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'] ?? '', ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<script>window.location.href='../LOGIN/login.php';</script>";
    exit;
}

$id_asesor = intval($_SESSION['id_asesor'] ?? 0);
if (!$id_asesor) {
    die("ID Asesor tidak ditemukan.");
}

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$id_skema = isset($_GET['id_skema']) ? intval($_GET['id_skema']) : 0;
$print_mode = isset($_GET['print']) && $_GET['print'] == 1;

if ($print_mode && $id_skema) {

    $skema = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT id_skema, judul_skema, nomor_skema
         FROM tb_skema
         WHERE id_skema = '$id_skema' AND id_asesor = '$id_asesor'"
    ));
    if (!$skema) {
        die("Skema tidak ditemukan atau bukan milik Anda.");
    }

    $ia06a = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ia.id_ia06a, ia.id_validator, v.username as validator_name, v.noreg as validator_noreg
         FROM tb_ia06a ia
         LEFT JOIN tb_validator v ON v.id_validator = ia.id_validator
         WHERE ia.id_skema = '$id_skema' AND ia.id_asesor = '$id_asesor'
         LIMIT 1"
    ));
    if (!$ia06a) {
        die("Belum ada FR.IA.06A untuk skema ini. Silakan buat soal terlebih dahulu.");
    }
    $id_ia06a = $ia06a['id_ia06a'];

    $soal_list = [];
    $res_soal = mysqli_query($koneksi,
        "SELECT id_soal, soal, kunci_jawaban
         FROM tb_soal
         WHERE id_ia06a = '$id_ia06a'
         ORDER BY id_soal ASC"
    );
    $no = 1;
    while ($row = mysqli_fetch_assoc($res_soal)) {
        $row['no_urut'] = $no++;
        $soal_list[] = $row;
    }
    if (empty($soal_list)) {
        die("Belum ada soal untuk skema ini.");
    }

    $asesor = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT nama_asesor, no_reg FROM tb_asesor WHERE id_asesor = '$id_asesor'"
    ));
    $qr_asesor = "Nama: {$asesor['nama_asesor']}\nNo.Reg: {$asesor['no_reg']}\nSkema: {$skema['nomor_skema']} - {$skema['judul_skema']}";
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Cetak FR.IA.06B - Kunci Jawaban Esai</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: Calibri, Arial, sans-serif;
                font-size: 10pt;
                background: #bbb;
                color: #000;
            }
            .toolbar {
                position: sticky; top: 0; z-index: 999;
                background: #1565c0;
                padding: 8px 16px;
                display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
            }
            .toolbar-label { color:#fff; font-size:13px; font-weight:bold; margin-right:4px; }
            .mode-btn {
                padding: 6px 18px; border-radius: 20px; border: 2px solid #fff;
                background: transparent; color: #fff; font-size: 12px;
                cursor: pointer; font-weight: bold; transition: all .2s;
            }
            .mode-btn.active  { background: #fff; color: #1565c0; }
            .mode-btn:hover:not(.active) { background: rgba(255,255,255,.2); }
            .toolbar-sep { flex:1; }
            .btn-print {
                background:#fff; color:#1565c0; border:none;
                padding:7px 22px; border-radius:4px; font-size:13px;
                font-weight:bold; cursor:pointer;
            }
            .btn-print:hover { background:#e3f2fd; }
            .btn-back { color:#90caf9; font-size:12px; text-decoration:none; }

            .print-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
            }
            .page {
                padding: 10mm 12mm 15mm 18mm;
                width: 100%;
                min-height: 297mm;
                background: white;
                page-break-after: avoid;
            }
            .judul {
                text-align: center;
                font-weight: bold;
                font-size: 14pt;
                border: 1.5px solid #000;
                padding: 6px;
                margin-bottom: 12px;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
                margin-bottom: 12px;
            }
            .info-table td, .info-table th {
                border: 1px solid #000;
                padding: 5px 8px;
                vertical-align: top;
            }
            .label-cell {
                width: 30%;
                background: #f9f9f9;
            }
            .validator-table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
                margin-top: 20px;
            }
            .validator-table th, .validator-table td {
                border: 1px solid #000;
                padding: 6px 8px;
                text-align: center;
                vertical-align: middle;
            }
            .validator-table th {
                background: #eef2fc;
            }
            .soal-item {
                margin-bottom: 14px;
                page-break-inside: avoid;
            }
            .soal-no {
                font-weight: bold;
                display: inline-block;
                width: 28px;
                vertical-align: top;
            }
            .soal-teks {
                display: inline-block;
                width: calc(100% - 32px);
                font-weight: bold;
            }
            .kunci-area {
                margin-top: 6px;
                margin-left: 28px;
                border: 1px solid #ddd;
                padding: 8px;
                background: #f5f9ff;
            }
            .tbl-ttd {
                width:100%;
                border-collapse:collapse;
                border:1px solid #000;
                margin-top:25px;
            }
            .tbl-ttd td {
                border:1px solid #000;
                padding:8px 10px;
                vertical-align:top;
            }
            .ttd-area {
                min-height: 80px;
                position: relative;
            }
            .ttd-manual-space {
                height: 50px;
                display: block;
            }
            .ttd-qr-box {
                display: none;
                justify-content: center;
                align-items: center;
                padding: 4px 0;
                min-height: 80px;
            }
            .ttd-qr-box.active {
                display: flex;
            }
            .ttd-qr-box canvas,
            .ttd-qr-box img {
                max-width: 80px !important;
                max-height: 80px !important;
                width: auto !important;
                height: auto !important;
            }

            @media print {
                body { background:#fff; }
                .toolbar { display: none !important; }
                .print-container { box-shadow: none; margin: 0; }
                .page { padding: 10mm 12mm 10mm 16mm; }
            }
        </style>
    </head>
    <body>
    <div class="toolbar">
        <span class="toolbar-label">Mode Tanda Tangan :</span>
        <button class="mode-btn active" id="btn-ttd" onclick="setMode('ttd')">Tanda Tangan</button>
        <button class="mode-btn" id="btn-qr" onclick="setMode('qr')">QR Code</button>
        <span class="toolbar-sep"></span>
        <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <!-- <a class="btn-back" href="javascript:history.back()">← Kembali</a> -->
    </div>

    <div class="print-container">
        <div class="page">
            <div class="judul">FR.IA.06B DPT – LEMBAR KUNCI JAWABAN PERTANYAAN TERTULIS ESAI</div>

            <table class="info-table">
                <tr><td class="label-cell">Skema Sertifikasi (KKNI/Okupasi/Klaster)</td>
                    <td colspan="3">Judul: <?= h($skema['judul_skema']) ?></td>
                </tr>
                <tr><td class="label-cell"></td>
                    <td colspan="3">Nomor: <?= h($skema['nomor_skema']) ?></td>
                </tr>
                <tr><td class="label-cell">Nama Asesor</td>
                    <td colspan="3"><?= h($asesor['nama_asesor']) ?> (<?= h($asesor['no_reg']) ?>)</td>
                </tr>
                <tr><td class="label-cell">Validator</td>
                    <td colspan="3"><?= h($ia06a['validator_name'] ?? '-') ?> (<?= h($ia06a['validator_noreg'] ?? '-') ?>)</td>
                </tr>
            </table>

            <div style="font-weight:bold; margin:16px 0 10px;">DAFTAR SOAL DAN KUNCI JAWABAN :</div>
            <?php foreach ($soal_list as $s): ?>
                <div class="soal-item">
                    <div>
                        <span class="soal-no"><?= $s['no_urut'] ?>.</span>
                        <span class="soal-teks"><?= h($s['soal']) ?></span>
                    </div>
                    <div class="kunci-area">
                        <strong>Kunci Jawaban:</strong><br>
                        <?= nl2br(h($s['kunci_jawaban'] ?? '(belum tersedia)')) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <table class="validator-table">
                <thead>
                    <tr><th>STATUS</th><th>NAMA</th><th>NOMOR REG</th><th>TANDA TANGAN DAN TANGGAL</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PENYUSUN</td>
                        <td><?= h($asesor['nama_asesor']) ?></td>
                        <td><?= h($asesor['no_reg']) ?></td>
                        <td style="height:80px; text-align:center;">
                            <div class="ttd-area">
                                <span class="ttd-manual-space" id="space-asesor"></span>
                                <div class="ttd-qr-box" id="qr-asesor-box">
                                    <div id="qr-asesor"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr><td>VALIDATOR</td><td><?= h($ia06a['validator_name'] ?? '-') ?></td><td><?= h($ia06a['validator_noreg'] ?? '-') ?></td><td style="height:80px;"></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const QR_ASESOR = <?= json_encode($qr_asesor) ?>;
        let qrAsesorGenerated = false;

        function setMode(mode) {
            const isManual = (mode === 'ttd');
            const isQR = (mode === 'qr');

            const btnTtd = document.getElementById('btn-ttd');
            const btnQr = document.getElementById('btn-qr');
            const spaceAsesor = document.getElementById('space-asesor');
            const qrAsesorBox = document.getElementById('qr-asesor-box');
            const qrAsesorDiv = document.getElementById('qr-asesor');

            if (!btnTtd || !btnQr || !spaceAsesor || !qrAsesorBox) {
                console.error('QR Code: Elemen tidak ditemukan');
                return;
            }

            btnTtd.classList.toggle('active', isManual);
            btnQr.classList.toggle('active', isQR);

            spaceAsesor.style.display = isManual ? 'block' : 'none';
            qrAsesorBox.classList.toggle('active', isQR);

            if (isQR && !qrAsesorGenerated) {
                try {
                    if (qrAsesorDiv) {
                        qrAsesorDiv.innerHTML = '';
                        new QRCode(qrAsesorDiv, {
                            text: QR_ASESOR,
                            width: 80,
                            height: 80,
                            colorDark: '#000',
                            colorLight: '#fff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        qrAsesorGenerated = true;
                    }
                } catch (e) {
                    console.error('QR Code error:', e);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setMode('ttd');
        });

        // Fallback untuk jika DOMContentLoaded sudah terpicu
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            setMode('ttd');
        }
    </script>
    </body>
    </html>
    <?php
    exit;
}

//Jika tidak dalam mode print, tampilkan form pilih skema
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kunci Jawaban - Pilih Skema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px 30px;
        }
        h2 {
            color: #1a237e;
            margin-top: 0;
            border-left: 5px solid #4A7AFF;
            padding-left: 15px;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #1b5e20; }
        .info {
            background: #e8eaf6;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            font-size: 13px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1565c0;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Cetak FR.IA.06B - Kunci Jawaban Esai</h2>
    <div class="info">
        Pilih skema yang ingin dicetak daftar soal beserta kunci jawabannya.
        Hanya skema yang sudah memiliki soal akan ditampilkan.
    </div>

    <?php
    // Ambil daftar skema milik asesor
    $query = "
        SELECT DISTINCT
            s.id_skema,
            s.judul_skema,
            s.nomor_skema
        FROM tb_skema s
        INNER JOIN tb_ia06a ia ON ia.id_skema = s.id_skema AND ia.id_asesor = s.id_asesor
        INNER JOIN tb_soal so ON so.id_ia06a = ia.id_ia06a
        WHERE s.id_asesor = '$id_asesor'
        ORDER BY s.nomor_skema
    ";
    $res = mysqli_query($koneksi, $query);
    $skema_list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $skema_list[] = $row;
    }

    if (empty($skema_list)): ?>
        <div style="color: #c62828; padding: 15px 0;">Anda belum memiliki skema yang sudah dilengkapi soal. Silakan buat soal terlebih dahulu.</div>
    <?php else: ?>
        <form method="get" action="cetak_ia6b.php" target="_blank">
            <input type="hidden" name="print" value="1">
            <div class="form-group">
                <label>Pilih Skema:</label>
                <select name="id_skema" required>
                    <option value="">-- Pilih Skema --</option>
                    <?php foreach ($skema_list as $sk): ?>
                        <option value="<?= $sk['id_skema'] ?>">
                            <?= h($sk['nomor_skema']) ?> - <?= h($sk['judul_skema']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Cetak PDF / Print</button>
        </form>
    <?php endif; ?>

    <a href="javascript:history.back()" class="back-link">← Kembali ke halaman sebelumnya</a>
</div>
</body>
</html>