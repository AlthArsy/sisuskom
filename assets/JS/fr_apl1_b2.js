function searchSkemaDP(val) {
    clearTimeout(skemaTimer); 
    var dd = document.getElementById('skema-dropdown');
    if (!dd) return;

    if (val.trim().length < 1) {
        dd.style.display = 'none';
        return;
    }

    skemaTimer = setTimeout(function () {
        dd.innerHTML = '<div class="skema-loading">Mencari...</div>';
        dd.style.display = 'block';

        var periode = (typeof ID_PERIODE !== 'undefined') ? ID_PERIODE : 0;
        fetch('../FR_APL/ambil_skema.php?action=search_dp&id_periode=' + periode +
              '&q=' + encodeURIComponent(val))
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status !== 'ok' || res.data.length === 0) {
                    dd.innerHTML = '<div class="skema-loading">Tidak ada hasil</div>';
                    return;
                }
                dd.innerHTML = '';
                res.data.forEach(function (s) {
                    var item = document.createElement('div');
                    item.className = 'skema-item';
                    item.innerHTML =
                        '<div class="sk-judul">' + escHtml(s.judul_skema) + '</div>' +
                        '<div class="sk-nomor">No: ' + escHtml(s.nomor_skema) +
                        ' &nbsp;|&nbsp; Asesor: ' + escHtml(s.nama_asesor) + '</div>';
                    item.onclick = function () { pilihSkemaDP(s); };
                    dd.appendChild(item);
                });
            })
            .catch(function () {
                dd.innerHTML = '<div class="skema-loading" style="color:red;">Gagal terhubung</div>';
            });
    }, 350);
}

function pilihSkemaDP(s) {
    var elJudul = document.getElementById('judul_skema');
    var elNomor = document.getElementById('nomor_skema');
    var dd      = document.getElementById('skema-dropdown');
    var elBadge = document.getElementById('skema-badge');

    if (elJudul) elJudul.value = s.judul_skema;
    if (elNomor) elNomor.value = s.nomor_skema;
    if (dd)      dd.style.display = 'none';
    if (elBadge) {
        elBadge.textContent = ' ' + s.judul_skema +
            ' (No. ' + s.nomor_skema + ') — Asesor: ' + s.nama_asesor;
        elBadge.style.display = 'inline-block';
    }

    setValIfExists('id_det_periode_hidden', s.id_det_periode);
    setValIfExists('id_skema_hidden',       s.id_skema);
    setValIfExists('judul_skema_hidden',    s.judul_skema);
    setValIfExists('nomor_skema_hidden',    s.nomor_skema);

    fetch('../FR_APL/ambil_skema.php?action=detail&id_skema=' + s.id_skema)
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.status !== 'ok') return;
            loadUnitTable(res.units, res.skema);
        })
        .catch(function () { console.warn('Gagal load unit'); });

    loadBuktiTable(s.id_skema);

    scheduleQR();
}

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
        if (ph)   { ph.textContent = 'Tidak ada unit kompetensi untuk skema ini'; ph.style.display = 'block'; }
        if (wrap) wrap.style.display = 'none';
        return;
    }

    tbody.innerHTML = '';
    var skn = escHtml(skema.standar_kompetensi_kerja || '-');

    units.forEach(function (u, i) {
        var tr = document.createElement('tr');
        var skCell = (i === 0)
            ? '<td rowspan="' + units.length +
              '" style="text-align:center;vertical-align:middle;font-size:12px;">' + skn + '</td>'
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
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.status !== 'ok') return;

            var tbodyBd = document.querySelector('#tbl-bukti-dasar tbody');
            if (tbodyBd) {
                tbodyBd.innerHTML = '';
                res.bukti_dasar.forEach(function (bd, i) {
                    tbodyBd.innerHTML +=
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align:left;">' + escHtml(bd.bukti_dasar) + '</td>' +
                        '<td><input type="radio" name="kondisi_bd[' + bd.id_bd + ']" value="Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_bd[' + bd.id_bd + ']" value="Tidak Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_bd[' + bd.id_bd + ']" value="Tidak Ada"></td>' +
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
                        '<td><input type="radio" name="kondisi_ba[' + ba.id_ba + ']" value="Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_ba[' + ba.id_ba + ']" value="Tidak Memenuhi Syarat"></td>' +
                        '<td><input type="radio" name="kondisi_ba[' + ba.id_ba + ']" value="Tidak Ada"></td>' +
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
    var idAsesi  = getVal('id_asesi')            || '-';
    var idSkema  = getVal('id_skema_hidden')     || '-';
    var judul    = getVal('judul_skema_hidden')  || getVal('judul_skema') || '-';
    var nama     = getVal('nama_pemohon')        || '';
    var tanggal  = getVal('tanggal_pemohon')     || '';
    var adminLsp = (typeof NAMA_ADMIN_LSP !== 'undefined') ? NAMA_ADMIN_LSP : '-';
    var ts = new Date().toISOString().slice(0, 19).replace('T', ' ');
    return 'LSP MUDIKAL | APL1B2' +
           ' | ID_ASESI:'  + idAsesi  +
           ' | ID_SKEMA:'  + idSkema  +
           ' | NAMA:'      + nama     +
           ' | SKEMA:'     + judul    +
           ' | ADMIN_LSP:' + adminLsp +
           ' | TGL:'       + tanggal  +
           ' | GEN:'       + ts;
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
    var idDetPeriode = getVal('id_det_periode_hidden');
    var idSkema      = getVal('id_skema_hidden');
    var tujuan       = document.querySelector('input[name="tujuan_asesmen"]:checked');
    var tanggal      = getVal('tanggal_pemohon');

    if (!idDetPeriode || !idSkema) {
        alert('Pilih skema dari daftar pencarian (klik salah satu hasil). Skema dan asesor akan terisi otomatis.');
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