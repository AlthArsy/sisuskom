/**
 * fr_ia06b.js - FR.IA.06B Kunci Jawaban
 * Depends: lsp_common.js
 *
 * CARA KERJA:
 * Setelah skema dipilih → fetch soal + kunci dari ambil_soal.php
 * Render form input kunci per soal
 */

function onSkemaSelected(s, res) {
    fetchSoalKunci(s.id_skema);
}

function fetchSoalKunci(id_skema) {
    var ph   = document.getElementById('kunci-ph');
    var form = document.getElementById('formKunci');
    if (ph) { ph.textContent = '⏳ Memuat soal...'; ph.style.display = 'block'; }
    if (form) form.style.display = 'none';

    fetch('ambil_soal.php?action=soal_kunci&id_skema=' + id_skema)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.soal || res.soal.length === 0) {
                if (ph) { ph.textContent = '⚠️ Belum ada soal untuk skema ini. Input soal di FR.IA.06A dulu.'; }
                return;
            }
            if (ph) ph.style.display = 'none';

            // Set id_skema ke hidden input form
            document.getElementById('id_skema_hidden').value = id_skema;

            // Render soal + input kunci
            var list = document.getElementById('kunci-list');
            list.innerHTML = '';
            res.soal.forEach(function(s) {
                var div = document.createElement('div');
                div.className = 'soal-kunci-box';
                div.innerHTML =
                    '<div class="soal-kunci-header">' + s.no_soal + '. ' + escHtml(s.pertanyaan) + '</div>' +
                    '<div class="soal-kunci-body">' +
                    '<label style="font-size:11px;color:#888;">Kunci Jawaban :</label>' +
                    '<textarea class="kunci-textarea" name="kunci[' + s.id_soal + ']"' +
                    ' placeholder="Tuliskan kunci jawaban...">' +
                    escHtml(s.kunci_jawaban || '') + '</textarea>' +
                    '</div>';
                list.appendChild(div);
            });

            // Render penyusun read-only
            var tbody = document.getElementById('penyusun-tbody');
            if (tbody) {
                if (!res.penyusun || res.penyusun.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#aaa;">Belum ada data penyusun</td></tr>';
                } else {
                    var html = '';
                    res.penyusun.forEach(function(p) {
                        html += '<tr>' +
                            '<td>' + escHtml(p.status) + '</td>' +
                            '<td style="text-align:center;">' + p.no + '</td>' +
                            '<td>' + escHtml(p.nama || '-') + '</td>' +
                            '<td>' + escHtml(p.no_met || '-') + '</td>' +
                            '<td>' + (p.tanggal || '-') + '</td></tr>';
                    });
                    tbody.innerHTML = html;
                }
            }

            if (form) form.style.display = 'block';
        })
        .catch(function() {
            if (ph) { ph.textContent = '❌ Gagal memuat soal'; ph.style.display = 'block'; }
        });
}
