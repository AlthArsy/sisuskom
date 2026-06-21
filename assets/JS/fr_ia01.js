function onSkemaSelected(s, res) {
    renderUnitListTable(res.units);
    renderObsTable(res.units, res.skema);
    var rek = document.getElementById('rek-section');
    if (rek) rek.style.display = 'block';
    scheduleQRAsesor();
}

function renderUnitListTable(units) {
    var ph    = document.getElementById('unit-list-ph');
    var wrap  = document.getElementById('unit-list-wrap');
    var tbody = document.getElementById('unit-list-tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    (units || []).forEach(function (u, i) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + (i + 1) + '</td>' +
            '<td>' + escHtml(u.kode_unit) + '</td>' +
            '<td>' + escHtml(u.judul_unit) + '</td>';
        tbody.appendChild(tr);
    });

    if (ph)   ph.style.display   = 'none';
    if (wrap) wrap.style.display = 'block';
}

function renderObsTable(units, skema) {
    var container = document.getElementById('obs-container');
    if (!container) return;
    container.innerHTML = '';

    (units || []).forEach(function (unit, ui) {
        var box = document.createElement('div');
        box.className = 'unit-obs-box';

        var html = '<div class="unit-obs-header">' +
            'Unit Kompetensi ' + (ui + 1) + '<br>' +
            '<span class="unit-sub">Kode Unit : ' + escHtml(unit.kode_unit) + '</span>&emsp;' +
            '<span class="unit-sub">Judul Unit : ' + escHtml(unit.judul_unit) + '</span>' +
            '</div>';

        html += '<div style="overflow-x:auto;"><table class="tbl-obs">' +
            '<thead>' +
            '<tr>' +
            '<th style="width:4%;">No.</th>' +
            '<th style="width:14%;">Elemen</th>' +
            '<th style="width:30%;">Kriteria Unjuk Kerja</th>' +
            '<th style="width:20%;">Standar Industri / Tempat Kerja</th>' +
            '<th colspan="2" style="width:16%;">Pencapaian</th>' +
            '<th style="width:16%;">Penilaian Lanjut</th>' +
            '</tr>' +
            '<tr><th colspan="4"></th><th>Ya</th><th>Tidak</th><th></th></tr>' +
            '</thead><tbody>';

        if (unit.elemen && unit.elemen.length > 0) {
            unit.elemen.forEach(function (el) {
                html += '<tr class="elemen-row">' +
                    '<td>' + escHtml(el.no_elemen) + '</td>' +
                    '<td colspan="6">' + escHtml(el.nama_elemen) + '</td>' +
                    '</tr>';

                (el.kuk || []).forEach(function (k) {
                    html += '<tr>' +
                        '<td style="font-size:11px;text-align:center;color:#888;">' + escHtml(k.no_kuk) + '</td>' +
                        '<td></td>' +
                        '<td class="kuk-text">' + escHtml(k.kuk) + '</td>' +
                        '<td><textarea class="obs-input" name="standar[' + k.id_kuk + ']"' +
                            ' placeholder="Isi standar..."></textarea></td>' +
                        '<td style="text-align:center;">' +
                            '<input type="radio" name="pencapaian[' + k.id_kuk + ']" value="Ya"></td>' +
                        '<td style="text-align:center;">' +
                            '<input type="radio" name="pencapaian[' + k.id_kuk + ']" value="Tidak"></td>' +
                        '<td><textarea class="obs-input" name="penilaian_lanjut[' + k.id_kuk + ']"' +
                            ' placeholder="Penilaian lanjut..."></textarea></td>' +
                        '</tr>';
                });
            });
        } else {
            html += '<tr><td colspan="7" style="text-align:center;color:#aaa;padding:12px;">Tidak ada elemen</td></tr>';
        }

        html += '</tbody></table></div>';
        box.innerHTML = html;
        container.appendChild(box);
    });
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
    var idAsesi = getVal('id_asesi') || '-';
    if (!nama) {
        generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', '');
        return;
    }
    var content = 'LSP MUDIKAL | IA01-ASESI' +
        ' | ID:' + idAsesi + ' | NAMA:' + nama +
        ' | SKEMA:' + skema + ' | TGL:' + (tanggal || '-') +
        ' | GEN:' + new Date().toISOString().slice(0, 19).replace('T', ' ');
    generateQR('qr-asesi-canvas', 'qr-asesi-ph', 'qr-asesi-badge', 'btn-dl-asesi', 'ttd_asesi_qr_input', content);
}

function dlQRAsesi() {
    downloadQR('qr-asesi-canvas', 'ttd_asesi_ia01_' + (getVal('id_asesi') || '0'));
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
    if (!nm || nm === '-' || !tanggal) {
        generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', '');
        return;
    }
    var content = 'LSP MUDIKAL | IA01-ASESOR' +
        ' | ASESOR:' + nm + ' | NO_REG:' + nr +
        ' | SKEMA:' + skema + ' | TGL:' + tanggal +
        ' | GEN:' + new Date().toISOString().slice(0, 19).replace('T', ' ');
    generateQR('qr-asesor-canvas', 'qr-asesor-ph', 'qr-asesor-badge', 'btn-dl-asesor', 'ttd_asesor_qr_input', content);
}

function dlQRAsesor() {
    downloadQR('qr-asesor-canvas', 'ttd_asesor_ia01_' + (getVal('id_asesi') || '0'));
}

function prepareQR() {
    doGenerateQRAsesi();
    doGenerateQRAsesor();
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="rekomendasi"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var detail = document.getElementById('rek-detail');
            if (detail) detail.style.display = this.value === 'Belum Kompeten' ? 'block' : 'none';
        });
    });

    if (getVal('nama_asesi')) scheduleQRAsesi();
});
