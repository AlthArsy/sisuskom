/**
 * fr_ak03.js
 * Khusus FR.AK.03 Umpan Balik dan Catatan Asesmen
 * Depends: lsp_common.js, qrcode.min.js
 */

/* ── Hook dari lsp_common.js ────────────────── */
function onSkemaSelected(s, res) {
    // Tidak perlu render tabel (sudah statis dari PHP)
    // lsp_common sudah set nama asesor ke semua elemen yang sesuai
}

/* ── QR ASESI ───────────────────────────────── */
var qrAsesiTimer = null;

function scheduleQRAsesi() {
    clearTimeout(qrAsesiTimer);
    qrAsesiTimer = setTimeout(doGenerateQRAsesi, 500);
}

function doGenerateQRAsesi() {
    var nama    = getVal('nama_asesi');
    var tanggal = getVal('tanggal_asesi');
    var skema   = getVal('judul_skema');
    var idAsesi = (typeof ID_ASESI !== 'undefined') ? ID_ASESI : '-';

    if (!nama) {
        generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge',
                   'btn-dl-asesi', 'ttd_asesi_qr_input', '');
        return;
    }

    var content = 'LSP MUDIKAL | AK03-ASESI' +
        ' | ID:'    + idAsesi +
        ' | NAMA:'  + nama +
        ' | SKEMA:' + skema +
        ' | TGL:'   + (tanggal || '-') +
        ' | GEN:'   + new Date().toISOString().slice(0, 19).replace('T', ' ');

    generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge',
               'btn-dl-asesi', 'ttd_asesi_qr_input', content);
}

function dlQRAsesi() {
    var id = (typeof ID_ASESI !== 'undefined') ? ID_ASESI : '0';
    downloadQR('qr-asesi-canvas', 'ttd_asesi_ak03_' + id);
}

/* ── Prepare sebelum submit ─────────────────── */
function prepareQR() {
    doGenerateQRAsesi();
    return true;
}

/* ── Auto QR saat load ──────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    if (getVal('nama_asesi')) scheduleQRAsesi();
});