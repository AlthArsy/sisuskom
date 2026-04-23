function onSkemaSelected(s, res) {
    fetchSoalForJawaban(s.id_skema);
    scheduleQRAsesor();
}

function fetchSoalForJawaban(id_skema) {
    var ph   = document.getElementById('soal-ph');
    var area = document.getElementById('jawaban-area');
    if (ph) { ph.textContent = 'Memuat soal...'; ph.style.display = 'block'; }
    if (area) area.innerHTML = '';

    fetch('ambil_soal.php?action=soal&id_skema=' + id_skema)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.soal || res.soal.length === 0) {
                if (ph) ph.textContent = 'Belum ada soal untuk skema ini.';
                return;
            }
            if (ph) ph.style.display = 'none';

            res.soal.forEach(function(s) {
                var box = document.createElement('div');
                box.className = 'jawaban-box';
                box.innerHTML =
                    '<div class="jawaban-soal">' + s.no_soal + '. ' + escHtml(s.pertanyaan) + '</div>' +
                    '<textarea class="jawaban-input" name="jawaban[' + s.id_soal + ']"' +
                    ' placeholder="Tuliskan jawaban kamu di sini..."></textarea>';
                area.appendChild(box);
            });

            var umpan = document.getElementById('umpan-section');
            if (umpan) umpan.style.display = 'block';
        })
        .catch(function() {
            if (ph) ph.textContent = 'Gagal memuat soal';
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
    var idAsesi = (typeof ID_ASESI !== 'undefined') ? ID_ASESI : '-';

    if (!nama) {
        generateQR('qr-asesi-canvas','qr-asesi-ph','qr-asesi-badge','btn-dl-asesi','ttd_asesi_qr_input','');
        return;
    }
    var content = 'LSP MUDIKAL | IA06C-ASESI' +
        ' | ID:' + idAsesi + ' | NAMA:' + nama +
        ' | SKEMA:' + skema + ' | TGL:' + (tanggal||'-') +
        ' | GEN:' + new Date().toISOString().slice(0,19).replace('T',' ');
    generateQR('qr-asesi-canvas','qr-asesi-ph','qr-asesi-badge','btn-dl-asesi','ttd_asesi_qr_input',content);
}

function dlQRAsesi() {
    downloadQR('qr-asesi-canvas', 'ttd_asesi_ia06c_' + ((typeof ID_ASESI!=='undefined')?ID_ASESI:'0'));
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

    if (!nm || nm==='-' || !tanggal) {
        generateQR('qr-asesor-canvas','qr-asesor-ph','qr-asesor-badge','btn-dl-asesor','ttd_asesor_qr_input','');
        return;
    }
    var content = 'LSP MUDIKAL | IA06C-ASESOR' +
        ' | ASESOR:' + nm + ' | NO_REG:' + nr +
        ' | SKEMA:' + skema + ' | TGL:' + tanggal +
        ' | GEN:' + new Date().toISOString().slice(0,19).replace('T',' ');
    generateQR('qr-asesor-canvas','qr-asesor-ph','qr-asesor-badge','btn-dl-asesor','ttd_asesor_qr_input',content);
}

function dlQRAsesor() {
    downloadQR('qr-asesor-canvas', 'ttd_asesor_ia06c_' + ((typeof ID_ASESI!=='undefined')?ID_ASESI:'0'));
}

function prepareQR() {
    doGenerateQRAsesi();
    doGenerateQRAsesor();
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    if (getVal('nama_asesi')) scheduleQRAsesi();
});