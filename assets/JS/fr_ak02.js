function onSkemaSelected(s, res) {
    renderTabelMetode(res.units);
    scheduleQRAsesor();
    document.getElementById('hasil-section').style.display = 'block';
}

function renderTabelMetode(units) {
    var ph    = document.getElementById('unit-placeholder');
    var wrap  = document.getElementById('tabel-metode-wrap');
    var tbody = document.getElementById('unit-tbody');
    if (!tbody) return;

    if (!units || units.length === 0) {
        if (ph) { ph.textContent = '⚠️ Tidak ada unit untuk skema ini'; ph.style.display = 'block'; }
        if (wrap) wrap.style.display = 'none';
        return;
    }

    tbody.innerHTML = '';

    var metodeKeys = [
        'obs_demonstrasi',
        'portofolio',
        'pernyataan_pihak3',
        'pertanyaan_wawancara',
        'pertanyaan_lisan',
        'pertanyaan_tertulis',
        'proyek_kerja'
    ];

    units.forEach(function (u) {
        var tr = document.createElement('tr');
        var html = '<td>' + escHtml(u.judul_unit) + '</td>';

        metodeKeys.forEach(function (key) {
            html += '<td>' +
                '<input type="checkbox" name="metode[' + u.id_unit + '][' + key + ']" value="1">' +
                '</td>';
        });

        html += '<td class="lainnya-cell">' +
            '<input type="text" name="metode[' + u.id_unit + '][lainnya]"' +
            ' placeholder="Sebutkan..." style="width:95%;font-size:11px;' +
            'border:1px solid #ccc;border-radius:3px;padding:3px 5px;">' +
            '</td>';

        tr.innerHTML = html;
        tbody.appendChild(tr);
    });

    if (ph)   ph.style.display   = 'none';
    if (wrap) wrap.style.display = 'block';
}

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
        generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', '');
        return;
    }
    var content = 'LSP MUDIKAL | AK02-ASESI' +
        ' | ID:' + idAsesi +
        ' | NAMA:' + nama +
        ' | SKEMA:' + skema +
        ' | TGL:' + (tanggal || '-') +
        ' | GEN:' + new Date().toISOString().slice(0, 19).replace('T', ' ');
    generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', content);
}

function dlQRAsesi() {
    var idAsesi = (typeof ID_ASESI !== 'undefined') ? ID_ASESI : '0';
    downloadQR('qr-asesi-canvas', 'ttd_asesi_ak02_' + idAsesi);
}

var qrAsesorTimer = null;

function scheduleQRAsesor() {
    clearTimeout(qrAsesorTimer);
    qrAsesorTimer = setTimeout(doGenerateQRAsesor, 500);
}

function doGenerateQRAsesor() {
    var nm      = currentAsesor.nama;
    var nr      = currentAsesor.noreg;
    var tanggal = getVal('tanggal_asesor');
    var skema   = getVal('judul_skema');
    var idSkema = getVal('id_skema_hidden') || '-';

    if (!nm || nm === '-' || !tanggal) {
        generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', '');
        return;
    }
    var content = 'LSP MUDIKAL | AK02-ASESOR' +
        ' | ASESOR:' + nm +
        ' | NO_REG:' + nr +
        ' | SKEMA:' + skema +
        ' | ID_SKEMA:' + idSkema +
        ' | TGL:' + tanggal +
        ' | GEN:' + new Date().toISOString().slice(0, 19).replace('T', ' ');
    generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', content);
}

function dlQRAsesor() {
    var idAsesi = (typeof ID_ASESI !== 'undefined') ? ID_ASESI : '0';
    downloadQR('qr-asesor-canvas', 'ttd_asesor_ak02_' + idAsesi);
}

function prepareQR() {
    doGenerateQRAsesi();
    doGenerateQRAsesor();
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    if (getVal('nama_asesi')) scheduleQRAsesi();
});