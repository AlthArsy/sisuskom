function onSkemaSelected(s, res) {
    renderAPL2Units(res.units, res.skema);
    showRekSection();
}

function renderAPL2Units(units, skema) {
    var container = document.getElementById('unit-container');
    var ph        = document.getElementById('unit-placeholder');
    if (!container) return;

    if (!units || units.length === 0) {
        if (ph) { ph.textContent = 'Tidak ada unit untuk skema ini'; ph.style.display = 'block'; }
        return;
    }

    container.innerHTML = '';

    var buktiDefault = (typeof BUKTI_PREFILL !== 'undefined') ? BUKTI_PREFILL : '';
    var kbDisabled   = (typeof IS_ASESI !== 'undefined' && IS_ASESI) ? 'disabled' : '';
    var kbStyle      = kbDisabled ? 'pointer-events:none;opacity:0.7;' : '';

    units.forEach(function (unit, ui) {
        var box = document.createElement('div');
        box.className = 'unit-box';

        var html = '<div class="unit-header">' +
            'Unit Kompetensi ' + (ui + 1) + '<br>' +
            '<span class="unit-sub">Kode Unit : ' + escHtml(unit.kode_unit) + '</span><br>' +
            '<span class="unit-sub">Judul Unit : ' + escHtml(unit.judul_unit) + '</span>' +
            '</div>';

        html += '<div style="overflow-x:auto;"><table class="tbl-apl2">' +
            '<thead><tr>' +
            '<th style="width:38%;">Dapatkah Saya.......?</th>' +
            '<th style="width:7%;">K</th>' +
            '<th style="width:7%;">BK</th>' +
            '<th>Bukti yang relevan</th>' +
            '</tr></thead><tbody>';

        if (unit.elemen && unit.elemen.length > 0) {
            unit.elemen.forEach(function (el) {
                html += '<tr class="elemen-row">' +
                    '<td colspan="4">' + escHtml(el.no_elemen) +
                    '. Elemen: ' + escHtml(el.nama_elemen) + '</td></tr>';

                html += '<tr>' +
                    '<td><span style="font-size:11px;color:#555;">Kriteria Unjuk Kerja:</span>' +
                    '<ul class="kuk-list">';
                (el.kuk || []).forEach(function (k) {
                    html += '<li>' + escHtml(k.no_kuk) + ' ' + escHtml(k.kuk) + '</li>';
                });
                html += '</ul></td>';

                html += '<td style="text-align:center; ' + kbStyle + '">' +
                    '<input type="radio" name="jawaban[' + el.id_elemen + ']" value="K"' +
                    (kbDisabled ? ' disabled' : '') + '></td>';

                html += '<td style="text-align:center; ' + kbStyle + '">' +
                    '<input type="radio" name="jawaban[' + el.id_elemen + ']" value="BK"' +
                    (kbDisabled ? ' disabled' : '') + '></td>';

                html += '<td>' +
                    '<textarea class="bukti-input" name="bukti[' + el.id_elemen + ']"' +
                    ' placeholder="Tuliskan bukti relevan...">' +
                    escHtml(buktiDefault) +
                    '</textarea></td></tr>';
            });
        } else {
            html += '<tr><td colspan="4" style="text-align:center;color:#aaa;padding:12px;">' +
                    'Tidak ada elemen</td></tr>';
        }

        html += '</tbody></table></div>';
        box.innerHTML = html;
        container.appendChild(box);
    });
}

function showRekSection() {
    var rek = document.getElementById('rek-section');
    if (rek) rek.style.display = 'block';
}
