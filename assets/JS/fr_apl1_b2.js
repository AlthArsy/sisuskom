function onSkemaSelected(s, res) {
    loadUnitTable(res.units, res.skema);
    loadBuktiTable(s.id_skema);
    scheduleQR();
}

function loadUnitTable(units, skema) {
    var ph    = document.getElementById('unit-placeholder');
    var wrap  = document.getElementById('unit-table-wrap');
    var tbody = document.getElementById('unit-tbody');
    if (!tbody) return;

    if (!units || units.length === 0) {
        if (ph) { ph.textContent = 'Tidak ada unit kompetensi untuk skema ini'; ph.style.display = 'block'; }
        if (wrap) wrap.style.display = 'none';
        return;
    }

    tbody.innerHTML = '';
    var skn = escHtml(skema.standar_kompetensi_kerja || '-');

    units.forEach(function (u, i) {
        var tr = document.createElement('tr');
        var skCell = (i === 0)
            ? '<td rowspan="' + units.length + '" style="text-align:center;vertical-align:middle;font-size:12px;">' + skn + '</td>'
            : '';
        tr.innerHTML =
            '<td>' + (i + 1) + '</td>' +
            '<td>' + escHtml(u.kode_unit) + '</td>' +
            '<td>' + escHtml(u.judul_unit) + '</td>' +
            skCell;
        tbody.appendChild(tr);
    });

    if (ph)   ph.style.display   = 'none';
    if (wrap) wrap.style.display = 'block';
}

function loadBuktiTable(idSkema) {
    fetch('../FR_APL/ambil_skema.php?action=bukti&id_skema=' + idSkema)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'ok') return;
            var tbodyBd = document.querySelector('#tbl-bukti-dasar tbody');
            if (tbodyBd) {
                tbodyBd.innerHTML = '';
                res.bukti_dasar.forEach(function (bd, i) {
                    tbodyBd.innerHTML +=
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align:left;">' + escHtml(bd.bukti_dasar) + '</td>' +
                        '<td><input type="radio" name="kondisi_bd['+bd.id_bd+']" value="Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_bd['+bd.id_bd+']" value="Tidak Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_bd['+bd.id_bd+']" value="Tidak Ada"></td>' +
                        '</tr>';
                });
            }
            var tbodyBa = document.querySelector('#tbl-bukti-adm tbody');
            if (tbodyBa) {
                tbodyBa.innerHTML = '';
                res.bukti_adm.forEach(function (ba, i) {
                    tbodyBa.innerHTML +=
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align:left;">' + escHtml(ba.bukti_adm) + '</td>' +
                        '<td><input type="radio" name="kondisi_ba['+ba.id_ba+']" value="Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_ba['+ba.id_ba+']" value="Tidak Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_ba['+ba.id_ba+']" value="Tidak Ada"></td>' +
                        '</tr>';
                });
            }
        });
}

var qrTimer = null;

function scheduleQR() {
    clearTimeout(qrTimer);
    qrTimer = setTimeout(doGenerateQR, 600);
}

function buildQRContent() {
    var idAsesi = getVal('id_asesi') || '-';
    var idSkema     = getVal('id_skema_hidden') || '-';
    var nama = getVal('nama_pemohon') || '';
    var tanggal = getVal('tanggal_pemohon') || '';
    var skema = getVal('judul_skema') || '';
    var adminLsp = (typeof NAMA_ADMIN_LSP !== 'undefined') ? NAMA_ADMIN_LSP : '-';
    var ts = new Date().toISOString().slice(0, 19).replace('T', ' ');
    return 'LSP MUDIKAL | APL1B2' +
           ' | ID_ASESI:' + idAsesi +
           ' | ID_SKEMA:' + idSkema +
           ' | NAMA:' + nama +
           ' | SKEMA:' + skema +
           ' | ADMIN_LSP:' + adminLsp +
           ' | TGL:' + tanggal +
           ' | GEN:' + ts;
}

function doGenerateQR() {
    var nama    = getVal('nama_pemohon');
    var tanggal = getVal('tanggal_pemohon');
    if (!nama || !tanggal) {
        generateQR('qr-canvas', 'qr-placeholder', 'qr-badge', 'btn-dl-qr', 'qr_data_input', '');
        return;
    }
    generateQR('qr-canvas', 'qr-placeholder', 'qr-badge', 'btn-dl-qr', 'qr_data_input', buildQRContent());
}

function prepareQRData() {
    var idSkema = getVal('id_skema_hidden');
    var judul = getVal('judul_skema');
    var nomor = getVal('nomor_skema');
    var tujuan = document.querySelector('input[name="tujuan_asesmen"]:checked');
    var tanggal = getVal('tanggal_pemohon');

    if (!idSkema || !judul || !nomor) {
        alert('Pilih skema dari daftar dropdown (klik salah satu hasil pencarian). Nomor skema akan terisi otomatis.');
        return false;
    }
    if (!tujuan) {
        alert('Pilih tujuan asesmen.');
        return false;
    }
    if (!tanggal) {
        alert('Isi tanggal pada bagian Pemohon/Kandidat.');
        return false;
    }

    doGenerateQR();
    return true;
}

function dlQR() {
    var idAsesi = getVal('id_asesi') || '0';
    downloadQR('qr-canvas', 'ttd_qr_asesi_' + idAsesi);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="tujuan_asesmen"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var inp = document.getElementById('input_lainnya');
            if (!inp) return;
            if (this.value === 'Lainnya') {
                inp.style.display = 'inline-block';
                inp.required      = true;
            } else {
                inp.style.display = 'none';
                inp.required      = false;
                inp.value         = '';
            }
        });
    });
});
