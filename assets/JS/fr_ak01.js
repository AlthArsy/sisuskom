
function onSkemaSelected(s, res) {
    scheduleQRAsesor();
}

var qrAsesorTimer = null;

function scheduleQRAsesor() {
    clearTimeout(qrAsesorTimer);
    qrAsesorTimer = setTimeout(doGenerateQRAsesor, 500);
}

function buildQRAsesorContent() {
    var tanggal = getVal('tanggal_asesor');
    var skema   = getVal('judul_skema');
    var idSkema = getVal('id_skema_hidden') || '-';
    var ts      = new Date().toISOString().slice(0, 19).replace('T', ' ');
    return 'LSP MUDIKAL | AK01-ASESOR' +
           ' | ASESOR:'    + currentAsesor.nama  +
           ' | NO_REG:'    + currentAsesor.noreg +
           ' | SKEMA:'     + skema    +
           ' | ID_SKEMA:'  + idSkema  +
           ' | TGL:'       + tanggal  +
           ' | GEN:'       + ts;
}

function doGenerateQRAsesor() {
    var nm      = currentAsesor.nama;
    var tanggal = getVal('tanggal_asesor');
    if (!nm || nm === '-' || !tanggal) {
        generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', '');
        return;
    }
    generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', buildQRAsesorContent());
}

function dlQRAsesor() {
    downloadQR('qr-asesor-canvas', 'ttd_asesor_qr_' + (getVal('id_asesi') || '0'));
}

var qrAsesiTimer = null;

function scheduleQRAsesi() {
    clearTimeout(qrAsesiTimer);
    qrAsesiTimer = setTimeout(doGenerateQRAsesi, 500);
}

function buildQRAsesiContent() {
    var idAsesi = getVal('id_asesi')  || '-';
    var nama    = getVal('nama_asesi') || '';
    var tanggal = getVal('tanggal_asesi') || '-';
    var skema   = getVal('judul_skema') || '';
    var ts      = new Date().toISOString().slice(0, 19).replace('T', ' ');
    return 'LSP MUDIKAL | AK01-ASESI' +
           ' | ID_ASESI:' + idAsesi  +
           ' | NAMA:'     + nama     +
           ' | SKEMA:'    + skema    +
           ' | TGL:'      + tanggal  +
           ' | GEN:'      + ts;
}

function doGenerateQRAsesi() {
    var nama = getVal('nama_asesi');
    if (!nama) {
        generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', '');
        return;
    }
    generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', buildQRAsesiContent());
}

function dlQRAsesi() {
    downloadQR('qr-asesi-canvas', 'ttd_asesi_qr_' + (getVal('id_asesi') || '0'));
}

function prepareQR() {
    doGenerateQRAsesor();
    doGenerateQRAsesi();
    return true;
}

function toggleLainnya(cb) {
    var inp = document.getElementById('input_lainnya');
    if (!inp) return;
    inp.style.display = cb.checked ? 'inline-block' : 'none';
    if (!cb.checked) inp.value = '';
}

document.addEventListener('DOMContentLoaded', function () {
    if (getVal('nama_asesi')) scheduleQRAsesi();
});

