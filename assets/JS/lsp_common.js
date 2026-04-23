var skemaTimer    = null;
var currentAsesor = { nama: '', noreg: '' };

function searchSkema(val) {
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

        fetch('ambil_skema.php?action=search&q=' + encodeURIComponent(val))
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
                        ' &nbsp;|&nbsp; ' + escHtml(s.standar_kompetensi_kerja || '') + '</div>';
                    item.onclick = function () { pilihSkema(s); };
                    dd.appendChild(item);
                });
            })
            .catch(function () {
                dd.innerHTML = '<div class="skema-loading" style="color:red;">Gagal terhubung</div>';
            });
    }, 350);
}

function pilihSkema(s) {
    var elJudul = document.getElementById('judul_skema');
    var elNomor = document.getElementById('nomor_skema');
    var elIdHid = document.getElementById('id_skema_hidden');
    var elBadge = document.getElementById('skema-badge');
    var dd      = document.getElementById('skema-dropdown');

    if (elJudul) elJudul.value = s.judul_skema;
    if (elNomor) elNomor.value = s.nomor_skema;
    if (elIdHid) elIdHid.value = s.id_skema;
    if (dd)      dd.style.display = 'none';
    if (elBadge) {
        elBadge.textContent = ' ' + s.judul_skema + ' (No. ' + s.nomor_skema + ')';
        elBadge.style.display = 'inline-block';
    }

    fetch('ambil_skema.php?action=apl2&id_skema=' + s.id_skema)
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.status !== 'ok') return;

            var nm = res.skema.nama_asesor || '-';
            var nr = res.skema.no_reg      || '-';
            currentAsesor = { nama: nm, noreg: nr };

            setTextIfExists('asesor-display',      nm);
            setTextIfExists('asesor-nama-display',  nm);
            setTextIfExists('asesor-nama-rek',      nm);
            setTextIfExists('asesor-noreg-rek',     nr);
            setTextIfExists('asesor-nama',          nm);
            setTextIfExists('asesor-noreg',         nr);
            setTextIfExists('asesor-ttd-nama',      nm);
            setTextIfExists('asesor-ttd-noreg',     nr);
            setValIfExists ('nama_asesor_hidden',   nm);
            setValIfExists ('no_reg_asesor_hidden', nr);
            setValIfExists ('asesor_pelaksanaan',   nm);

            if (typeof onSkemaSelected === 'function') {
                onSkemaSelected(s, res);
            }
        })
        .catch(function () {
            console.warn('Gagal fetch detail skema');
        });
}

document.addEventListener('click', function (e) {
    var wrap = document.querySelector('.skema-wrap');
    var dd   = document.getElementById('skema-dropdown');
    if (wrap && dd && !wrap.contains(e.target)) {
        dd.style.display = 'none';
    }
});

/**
 * @param {string} canvasId   
 * @param {string} phId   
 * @param {string} badgeId  
 * @param {string} btnDlId 
 * @param {string} hiddenId
 * @param {string} content    
 */
function generateQR(canvasId, phId, badgeId, btnDlId, hiddenId, content) {
    var canvas = document.getElementById(canvasId);
    var ph     = document.getElementById(phId);
    var badge  = document.getElementById(badgeId);
    var btn    = document.getElementById(btnDlId);
    if (!canvas) return;

    if (!content || content.trim() === '') {
        canvas.innerHTML = '';
        if (ph)    { canvas.appendChild(ph); ph.style.display = 'flex'; }
        if (badge) badge.style.display = 'none';
        if (btn)   btn.style.display   = 'none';
        return;
    }

    if (ph)    ph.style.display = 'none';
    canvas.innerHTML = '';

    try {
        new QRCode(canvas, {
            text        : content,
            width       : 130,
            height      : 130,
            colorDark   : '#000000',
            colorLight  : '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        if (badge) badge.style.display = 'inline-block';
        if (btn)   btn.style.display   = 'inline-block';
        if (hiddenId) setValIfExists(hiddenId, content);
    } catch (err) {
        canvas.innerHTML = '<p style="color:red;font-size:11px;">Error QR: ' + err.message + '</p>';
    }
}

/**
 * @param {string} canvasId
 * @param {string} filename
 */
function downloadQR(canvasId, filename) {
    var cvs = document.querySelector('#' + canvasId + ' canvas');
    var img = document.querySelector('#' + canvasId + ' img');
    var src = cvs ? cvs.toDataURL('image/png') : (img ? img.src : null);
    if (!src) { alert('QR belum terbuat.'); return; }
    var a = document.createElement('a');
    a.href     = src;
    a.download = filename + '.png';
    a.click();
}
function escHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

function setTextIfExists(id, val) {
    var el = document.getElementById(id);
    if (el) el.textContent = val;
}

function setValIfExists(id, val) {
    var el = document.getElementById(id);
    if (el) el.value = val;
}

function scheduleTimer(timerRef, fn, delay) {
    clearTimeout(timerRef);
    return setTimeout(fn, delay || 500);
}

function getVal(id) {
    var el = document.getElementById(id);
    return el ? el.value.trim() : '';
}