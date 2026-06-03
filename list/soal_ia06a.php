<?php
include "../koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Asesor', 'Admin_lsp', 'Admin_utm'])) {
    echo "<p style='color:red;padding:20px;'>Akses ditolak.</p>";
    exit;
}

$e = fn($v) => mysqli_real_escape_string($koneksi, (string)$v);
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$base      = '../BERANDA/UTAMA.php';
$id_asesor = intval($_SESSION['id_asesor'] ?? 0);
$id_ia06a  = intval($_GET['id_ia06a'] ?? 0);

$flash = $_SESSION['flash_soal'] ?? '';
$flash_type = '';
if ($flash) {
    [$flash_type, $flash] = explode('|', $flash, 2);
    unset($_SESSION['flash_soal']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi       = $_POST['aksi']       ?? '';
    $p_id_ia06a = intval($_POST['id_ia06a'] ?? 0);

    if ($aksi === 'tambah') {
        $p_soal  = trim($_POST['soal']          ?? '');
        $p_kunci = trim($_POST['kunci_jawaban'] ?? '');

        if ($p_soal && $p_id_ia06a) {
            mysqli_query($koneksi,
                "INSERT INTO tb_soal (id_ia06a, soal, kunci_jawaban)
                 VALUES ('{$e($p_id_ia06a)}', '{$e($p_soal)}', '{$e($p_kunci)}')");
            $_SESSION['flash_soal'] = 'success|Soal berhasil ditambahkan.';
        } else {
            $_SESSION['flash_soal'] = 'error|Soal tidak boleh kosong.';
        }
    }

    if ($aksi === 'hapus') {
        $p_id_soal = intval($_POST['id_soal'] ?? 0);
        mysqli_query($koneksi, "DELETE FROM tb_soal WHERE id_soal='$p_id_soal'");
        $_SESSION['flash_soal'] = 'success|Soal berhasil dihapus.';
    }

    if ($aksi === 'buat_ia06a') {
        $p_id_skema = intval($_POST['id_skema'] ?? 0);
        $id_validator = intval($_POST['id_validator']  ?? 0);
        if ($p_id_skema && $id_asesor) {
            $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT id_ia06a FROM tb_ia06a
                 WHERE id_asesor='$id_asesor' AND id_skema='$p_id_skema' AND id_validator='$id_validator' LIMIT 1"));
            if ($cek) {
                $p_id_ia06a = intval($cek['id_ia06a']);
                $_SESSION['flash_soal'] = 'success|soal sudah ada, diarahkan ke sana.';
            } else {
                mysqli_query($koneksi,
                    "INSERT INTO tb_ia06a (id_asesor, id_skema, id_validator)
                     VALUES ('$id_asesor', '$p_id_skema', '$id_validator')");
                $p_id_ia06a = intval(mysqli_insert_id($koneksi));
                $_SESSION['flash_soal'] = 'success|Soal baru berhasil dibuat.';
            }
        }
    }

    header("Location: {$base}?page=../list/soal_ia06a.php&id_ia06a=$p_id_ia06a");
    exit;
}


$list_ia06a = [];
$res_ia = mysqli_query($koneksi,
    "SELECT ia.id_ia06a, s.judul_skema, s.nomor_skema
     FROM tb_ia06a ia
     LEFT JOIN tb_skema s ON s.id_skema = ia.id_skema
     WHERE ia.id_asesor = '$id_asesor'
     ORDER BY ia.id_ia06a DESC");
while ($r = mysqli_fetch_assoc($res_ia)) $list_ia06a[] = $r;

if (!$id_ia06a && !empty($list_ia06a)) {
    $id_ia06a = intval($list_ia06a[0]['id_ia06a']);
}

$ia06a_info = null;
if ($id_ia06a) {
    $ia06a_info = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT ia.*, s.judul_skema, s.nomor_skema, v.username
         FROM tb_ia06a ia
         LEFT JOIN tb_skema s ON s.id_skema = ia.id_skema
         LEFT JOIN tb_validator v ON v.id_validator = ia.id_validator
         WHERE ia.id_ia06a = '$id_ia06a' LIMIT 1"));
}

$soal_list = [];
if ($id_ia06a) {
    $res_soal = mysqli_query($koneksi,
        "SELECT * FROM tb_soal WHERE id_ia06a = '$id_ia06a' ORDER BY id_soal");
    while ($r = mysqli_fetch_assoc($res_soal)) $soal_list[] = $r;
}

$list_validator = [];
$res_val = mysqli_query($koneksi, "SELECT id_validator, username, noreg FROM tb_validator ORDER BY username");
while ($r = mysqli_fetch_assoc($res_val)) $list_validator[] = $r;

$list_skema = [];
$res_sk = mysqli_query($koneksi,
    "SELECT id_skema, nomor_skema, judul_skema
     FROM tb_skema
     WHERE id_asesor = '$id_asesor'
     ORDER BY id_skema");
while ($r = mysqli_fetch_assoc($res_sk)) $list_skema[] = $r;
?>
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; }

    .page-title { font-size: 20px; font-weight: bold; color: #1a237e; margin-bottom: 18px; }

    .flash { padding: 9px 14px; border-radius: 5px; margin-bottom: 14px; font-size: 13px; }
    .flash.success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .flash.error   { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }

    .toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
    .toolbar select {
        padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px;
        font-size: 13px; min-width: 220px;
    }
    .btn {
        padding: 7px 18px; border: none; border-radius: 4px;
        font-size: 13px; cursor: pointer; font-weight: bold;
    }
    .btn-blue  { background: #4A7AFF; color: #fff; }
    .btn-green { background: #2e7d32; color: #fff; }
    .btn-red   { background: #e53935; color: #fff; font-size: 12px; padding: 4px 12px; font-weight: normal; }
    .btn-blue:hover  { background: #325fd6; }
    .btn-green:hover { background: #1b5e20; }
    .btn-red:hover   { background: #b71c1c; }

    .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tbl th {
        background: #cadbfc; color: #1a237e;
        padding: 9px 12px; border: 1px solid #b0bec5;
        text-align: center;
    }
    .tbl td { padding: 8px 12px; border: 1px solid #dde; vertical-align: top; }
    .tbl tr:nth-child(even) td { background: #f5f7ff; }
    .tbl tr:hover td { background: #eef2ff; }
    .tbl td:first-child { text-align: center; width: 46px; }
    .tbl td:last-child  { text-align: center; width: 80px; }

    .info-skema {
        background: #e8eaf6; border: 1px solid #c5cae9;
        border-radius: 5px; padding: 8px 14px;
        font-size: 13px; color: #1a237e; margin-bottom: 14px;
    }

    .form-tambah {
        background: #fafbff; border: 1px solid #c5cae9;
        border-radius: 6px; padding: 14px 16px; margin-bottom: 18px;
    }
    .form-tambah-title {
        font-weight: bold; font-size: 14px; color: #1a237e;
        border-left: 4px solid #4A7AFF; padding-left: 8px;
        margin-bottom: 12px;
    }
    .form-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .form-col { display: flex; flex-direction: column; flex: 1; min-width: 200px; }
    .form-col label { font-size: 12px; color: #555; margin-bottom: 4px; }
    .form-col textarea {
        border: 1px solid #ccc; border-radius: 4px;
        padding: 6px 8px; font-size: 13px; resize: vertical;
        min-height: 60px; font-family: Arial, sans-serif;
    }
    .form-col textarea:focus { outline: none; border-color: #4A7AFF; }

    .panel-buat {
        display: none; background: #fffde7; border: 1px solid #ffe082;
        border-radius: 6px; padding: 12px 16px; margin-bottom: 16px;
    }
    .panel-buat.open { display: block; }
    .panel-buat select {
        padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px;
        font-size: 13px; min-width: 220px;
    }
    .btn-cetak {
        background: #4caf50; color: white; border: none;
        padding: 5px 14px; border-radius: 5px; font-size: 12px;
        cursor: pointer; text-decoration: none; white-space: nowrap;
        margin-left: 4px;
    }

    .empty-msg { color: #999; text-align: center; padding: 24px; font-size: 13px; }

    @media (max-width: 700px) {
        .form-row { flex-direction: column; }
        .toolbar  { flex-direction: column; align-items: flex-start; }
    }
</style>
<div class="page-title">Kelola Soal — FR.IA.06A DPT Pertanyaan Tertulis Esai</div>

<?php if ($flash): ?>
<div class="flash <?= h($flash_type) ?>"><?= h($flash) ?></div>
<?php endif; ?>

<div class="toolbar">
    <?php if (!empty($list_ia06a)): ?>
    <form method="get" action="<?= h($base) ?>" style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="page" value="../list/soal_ia06a.php">
        <select name="id_ia06a" onchange="this.form.submit()">
            <?php foreach ($list_ia06a as $ia): ?>
            <option value="<?= $ia['id_ia06a'] ?>"
                <?= intval($ia['id_ia06a']) === $id_ia06a ? 'selected' : '' ?>>
                <?= h($ia['judul_skema']) ?> (No. <?= h($ia['nomor_skema']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php endif; ?>

    <button class="btn btn-blue" onclick="togglePanel()">+ Buat Soal Baru</button>
   <?php if ($ia06a_info && !empty($soal_list)): ?>
    <a class="btn-cetak" href="../pdf/cetak_ia6b.php?id_skema=<?= $ia06a_info['id_skema'] ?>&print=1" target="_blank">
        Cetak PDF
    </a>
    <?php else: ?>
        <span class="btn-cetak" style="background:#aaa; cursor:not-allowed;" title="Belum ada soal untuk dicetak">Cetak PDF</span>
    <?php endif; ?>
</div>

<div class="panel-buat" id="panelBuat">
    <b style="font-size:13px;">Pilih Skema untuk Soal Baru :</b>
    <form method="post" action="<?= h($base) ?>?page=../list/soal_ia06a.php"
          style="display:flex;gap:10px;align-items:center;margin-top:8px;flex-wrap:wrap;">
        <input type="hidden" name="aksi" value="buat_ia06a">
        <select name="id_skema" required>
            <option value="">-- Pilih Skema --</option>
            <?php foreach ($list_skema as $sk): ?>
            <option value="<?= $sk['id_skema'] ?>">
                <?= h($sk['judul_skema']) ?> (No. <?= h($sk['nomor_skema']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <select name="id_validator" required>
            <option value="">-- Pilih Validator --</option>
            <?php foreach ($list_validator as $v): ?>
            <option value="<?= $v['id_validator'] ?>">
                <?= h($v['username']) ?> (<?= h($v['noreg']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-green">Buat</button>
        <button type="button" class="btn" style="background:#888;color:#fff;"
                onclick="togglePanel()">Batal</button>
    </form>
</div>

<?php if ($ia06a_info): ?>
<div class="info-skema">
    Soal untuk Skema :
    <b><?= h($ia06a_info['judul_skema']) ?></b>
    &nbsp;|&nbsp; Nomor : <?= h($ia06a_info['nomor_skema']) ?>
    &nbsp;|&nbsp; Total Soal : <b><?= count($soal_list) ?></b>
    &nbsp;|&nbsp; Validator :
    <b><?= h($ia06a_info['username']) ?></b>

</div>

<div class="form-tambah">
    <div class="form-tambah-title">Tambah Soal</div>
    <form method="post" action="<?= h($base) ?>?page=../list/soal_ia06a.php">
        <input type="hidden" name="aksi"     value="tambah">
        <input type="hidden" name="id_ia06a" value="<?= $id_ia06a ?>">
        <div class="form-row">
            <div class="form-col" style="flex:2;">
                <label>Pertanyaan / Soal <span style="color:red;">*</span></label>
                <textarea name="soal" placeholder="Tulis soal di sini..." required></textarea>
            </div>
            <div class="form-col" style="flex:2;">
                <label>Kunci Jawaban</label>
                <textarea name="kunci_jawaban" placeholder="Tulis kunci jawaban (opsional)..."></textarea>
            </div>
            <div class="form-col" style="flex:0; justify-content:flex-end; min-width:80px;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-green" style="height:42px;">Simpan</button>
            </div>
        </div>
    </form>
</div>

<?php if (empty($soal_list)): ?>
    <div class="empty-msg">Belum ada soal. Tambahkan soal di atas.</div>
<?php else: ?>
<div style="overflow-x:auto;">
    <table class="tbl">
        <thead>
            <tr>
                <th>No</th>
                <th>Soal / Pertanyaan</th>
                <th>Kunci Jawaban</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soal_list as $i => $s): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= h($s['soal']) ?></td>
                <td style="color:#555;"><?= h($s['kunci_jawaban'] ?? '—') ?></td>
                <td>
                    <form method="post" action="<?= h($base) ?>?page=../list/soal_ia06a.php"
                          onsubmit="return confirm('Hapus soal ini?')">
                        <input type="hidden" name="aksi"      value="hapus">
                        <input type="hidden" name="id_soal"   value="<?= $s['id_soal'] ?>">
                        <input type="hidden" name="id_ia06a"  value="<?= $id_ia06a ?>">
                        <button type="submit" class="btn btn-red">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div style="font-size:12px;color:#888;margin-top:8px;">
    Menampilkan <?= count($soal_list) ?> soal
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-msg">
    Belum ada soal. Klik <b>+ Buat Soal Baru</b> untuk memulai.
</div>
<?php endif; ?>

<script>
function togglePanel() {
    var p = document.getElementById('panelBuat');
    p.classList.toggle('open');
}
</script>
