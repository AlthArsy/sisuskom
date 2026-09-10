var currentIdSkema = null;

function onSkemaSelected(s, res) {
    currentIdSkema = s.id_skema;
    fetchAndRenderSoal(s.id_skema);
    var btnB = document.getElementById('btn-goto-b');
    var btnC = document.getElementById('btn-goto-c');
    if (btnB) btnB.style.display = 'inline-block';
    if (btnC) btnC.style.display = 'inline-block';
}

function fetchAndRenderSoal(id_skema) {
    var area = document.getElementById('konten-area');
    var ph   = document.getElementById('pilih-ph');
    if (ph) ph.style.display = 'none';

    fetch('ambil_soal.php?action=soal&id_skema=' + id_skema)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (IS_ASESI) {
                renderViewSoal(res.soal || [], res.penyusun || []);
            } else {
                renderFormSoal(res.soal || [], res.penyusun || [], id_skema);
            }
        })
        .catch(function() {
            if (area) area.innerHTML = '<p style="color:red;padding:12px;">Gagal memuat soal</p>';
        });
}

function renderViewSoal(soalList, penyusunList) {
    var area = document.getElementById('konten-area');
    var html = '<div class="section-title" style="margin:16px 0 8px;">JAWAB SEMUA PERTANYAAN DI BAWAH INI:</div>';

    if (soalList.length === 0) {
        html += '<p style="color:#aaa;font-size:13px;padding:8px;">Belum ada soal untuk skema ini.</p>';
    } else {
        soalList.forEach(function(s) {
            html += '<div class="view-soal">' +
                '<span class="no">' + s.no_soal + '.</span>' +
                escHtml(s.pertanyaan) + '</div>';
        });
    }

    html += renderPenyusunReadOnly(penyusunList);
    area.innerHTML = html;
}

function renderFormSoal(soalList, penyusunList, id_skema) {
    var area = document.getElementById('konten-area');
    var jumlah = soalList.length > 0 ? soalList.length : 10;

    var html = '<form method="post" id="formSoal">' +
        '<input type="hidden" name="id_skema" value="' + id_skema + '">';

    html += '<div class="section-title" style="margin:16px 0 8px;">Daftar Soal</div>';

    for (var i = 1; i <= jumlah; i++) {
        var soal = soalList.find(function(s) { return s.no_soal == i; });
        var val  = soal ? escHtml(soal.pertanyaan) : '';
        html += '<div class="soal-box">' +
            '<div class="soal-no">' + i + '.</div>' +
            '<textarea class="soal-textarea" name="soal[' + i + ']"' +
            ' placeholder="Tulis pertanyaan no ' + i + '...">' + val + '</textarea>' +
            '</div>';
    }

    html += '<div class="section-title" style="margin:20px 0 8px;">Penyusun dan Validator</div>';
    html += renderPenyusunForm(penyusunList);

    html += '<div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;">' +
        '<button type="button" class="btn-back" onclick="tambahSoal()">+ Tambah Soal</button>' +
        '<button type="submit" class="btn-submit">SIMPAN SOAL ✓</button>' +
        '</div></form>';

    area.innerHTML = html;
    window._jumlahSoal = jumlah;
}

function tambahSoal() {
    window._jumlahSoal = (window._jumlahSoal || 10) + 1;
    var n   = window._jumlahSoal;
    var box = document.createElement('div');
    box.className = 'soal-box';
    box.innerHTML = '<div class="soal-no">' + n + '.</div>' +
        '<textarea class="soal-textarea" name="soal[' + n + ']"' +
        ' placeholder="Tulis pertanyaan no ' + n + '..."></textarea>';
    var form = document.getElementById('formSoal');
    var btn  = form.querySelector('div[style*="margin-top"]');
    form.insertBefore(box, btn);
}

function renderPenyusunForm(list) {
    var statuses = [
        {status:'Penyusun', rows:[1,2]},
        {status:'Validator', rows:[1,2]}
    ];
    var html = '<div style="overflow-x:auto;">' +
        '<table class="tbl-penyusun"><thead><tr>' +
        '<th>Status</th><th>No.</th><th>Nama</th><th>Nomor MET</th><th>Tanggal</th>' +
        '</tr></thead><tbody>';

    var idx = 0;
    statuses.forEach(function(st) {
        st.rows.forEach(function(no) {
            var found = list.find(function(p) { return p.status===st.status && p.no==no; });
            html += '<tr>' +
                (no === 1 ? '<td rowspan="2" style="text-align:center;font-weight:bold;">' + st.status + '</td>' : '') +
                '<td style="text-align:center;">' + no + '</td>' +
                '<td><input type="text" class="form-control" name="penyusun[' + idx + '][nama]"' +
                    ' value="' + (found ? escHtml(found.nama) : '') + '" placeholder="Nama"></td>' +
                '<td><input type="text" class="form-control" name="penyusun[' + idx + '][no_met]"' +
                    ' value="' + (found ? escHtml(found.no_met) : '') + '" placeholder="No MET"></td>' +
                '<td><input type="date" class="form-control" name="penyusun[' + idx + '][tanggal]"' +
                    ' value="' + (found && found.tanggal ? found.tanggal : '') + '"></td>' +
                '<input type="hidden" name="penyusun[' + idx + '][status]" value="' + st.status + '">' +
                '<input type="hidden" name="penyusun[' + idx + '][no]" value="' + no + '">' +
                '</tr>';
            idx++;
        });
    });

    html += '</tbody></table></div>';
    return html;
}

function renderPenyusunReadOnly(list) {
    var html = '<div class="section-title" style="margin:20px 0 8px;">Penyusun dan Validator</div>' +
        '<div style="overflow-x:auto;"><table class="tbl-penyusun"><thead><tr>' +
        '<th>Status</th><th>No.</th><th>Nama</th><th>Nomor MET</th><th>Tanggal</th>' +
        '</tr></thead><tbody>';

    if (list.length === 0) {
        html += '<tr><td colspan="5" style="text-align:center;color:#aaa;">Belum ada data</td></tr>';
    } else {
        list.forEach(function(p, i) {
            html += '<tr>' +
                '<td>' + escHtml(p.status) + '</td>' +
                '<td style="text-align:center;">' + p.no + '</td>' +
                '<td>' + escHtml(p.nama || '-') + '</td>' +
                '<td>' + escHtml(p.no_met || '-') + '</td>' +
                '<td>' + (p.tanggal || '-') + '</td></tr>';
        });
    }

    html += '</tbody></table></div>';
    return html;
}

function gotoB() {
    if (!currentIdSkema) { alert('Pilih skema dulu!'); return; }
    window.location.href = 'FR_IA06B.php?id_asesi=' + ID_ASESI + '&id_skema=' + currentIdSkema;
}

function gotoC() {
    if (!currentIdSkema) { alert('Pilih skema dulu!'); return; }
    window.location.href = 'FR_IA06C.php?id_asesi=' + ID_ASESI + '&id_skema=' + currentIdSkema;
}
