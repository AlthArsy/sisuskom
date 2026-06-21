<?php

function bot_normalize_text(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function bot_get_knowledge_base(): array
{
    return [
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['kuk', 'isi kuk', 'mengisi kuk', 'cara kuk', 'kriteria unjuk kerja', 'tambah kuk'],
            'answer' => 'Dengan mengisi Skema terlebih dahulu, kemudian Unit, lalu Elemen — di situlah KUK (Kriteria Unjuk Kerja) dapat ditambahkan. Buka menu Manajemen Skema → pilih Skema → Unit → Elemen → KUK.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['skema', 'buat skema', 'tambah skema', 'isi skema', 'mengisi skema', 'cara skema'],
            'answer' => 'Untuk membuat Skema: buka menu Manajemen Skema → Data Skema → tombol Tambah Skema. Isi nama skema dan data yang diminta, lalu simpan. Setelah itu Anda bisa menambahkan Unit Kompetensi.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['unit', 'unit kompetensi', 'tambah unit', 'isi unit', 'mengisi unit'],
            'answer' => 'Unit Kompetensi ditambahkan dari halaman Skema. Pilih Skema yang sudah dibuat → klik Unit Kompetensi → Tambah Unit. Isi kode dan judul unit, lalu simpan.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['elemen', 'tambah elemen', 'isi elemen', 'mengisi elemen'],
            'answer' => 'Elemen ditambahkan dari halaman Unit Kompetensi. Pilih Unit → klik Elemen → Tambah Elemen. Setelah elemen tersimpan, Anda bisa menambahkan KUK di dalamnya.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['urutan', 'alur', 'langkah', 'hierarki', 'struktur skema'],
            'answer' => 'Urutan pengisian data kompetensi: Skema → Unit Kompetensi → Elemen → KUK. Setelah struktur lengkap, Anda juga bisa mengatur Bukti Dasar dan Bukti Administratif per Skema.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['bukti dasar', 'dasar', 'tambah bukti dasar'],
            'answer' => 'Bukti Dasar diatur per Skema. Dari halaman Skema (setelah ada Unit), buka menu Bukti Dasar. Di sana Anda bisa menambah, mengubah, atau menghapus jenis bukti yang dibutuhkan asesi.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['bukti adm', 'bukti administratif', 'administratif', 'bukti admin'],
            'answer' => 'Bukti Administratif diatur oleh Asesor dari menu Bukti Adm pada Skema. Gunakan halaman tersebut untuk menambah persyaratan administratif yang harus dipenuhi asesi.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['fr apl', 'form lsp', 'formulir', 'urutan form', 'isi form'],
            'answer' => 'Asesi mengisi form melalui menu Form Lsp dengan urutan: FR APL 1 → FR APL 2 → FR AK 01 → FR IA 1 → FR AK 03 → FR AK 02 → FR IA 06. Sebagai Asesor, Anda memantau dan mengisi bagian asesor (misalnya FR AK 05).',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['fr ak05', 'ak05', 'ak 05', 'laporan asesmen'],
            'answer' => 'FR AK 05 adalah laporan asesmen yang diisi oleh Asesor. Buka melalui menu Form Lsp atau dari rekap FR AK 05. Pastikan data asesi dan hasil asesmen sudah lengkap sebelum menyimpan.',
        ],
        [
            'roles' => ['Asesor'],
            'keywords' => ['soal ia06', 'ia06a', 'ia 06', 'pertanyaan esai'],
            'answer' => 'Soal FR IA 06A dikelola Asesor melalui menu Soal IA06A. Pilih skema terkait, lalu tambah atau ubah pertanyaan esai yang akan dijawab asesi pada FR IA 06.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['rekap', 'catatan rekap', 'lihat rekap', 'cetak', 'pdf', 'print'],
            'answer' => 'Rekap tersedia di menu Catatan Rekap di sidebar. Pilih jenis form (APL 1, APL 2, AK01, dst.). Untuk cetak PDF, gunakan tombol cetak pada halaman rekap yang membuka file di folder pdf.',
        ],
        [
            'roles' => ['Admin_utm'],
            'keywords' => ['tambah user', 'buat user', 'user baru', 'manajemen user'],
            'answer' => 'Sebagai Admin Utama, buka menu Manajemen User untuk melihat semua pengguna. Gunakan menu Pengaturan → Tambah User Baru untuk membuat akun Admin LSP, Asesor, atau Asesi.',
        ],
        [
            'roles' => ['Admin_utm'],
            'keywords' => ['admin lsp', 'tambah admin'],
            'answer' => 'Kelola Admin LSP lewat menu Manajemen Admin LSP. Untuk menambah akun baru, gunakan Pengaturan → Tambah User Baru dan pilih peran Admin LSP.',
        ],
        [
            'roles' => ['Admin_lsp'],
            'keywords' => ['validator', 'tambah validator', 'manajemen validator'],
            'answer' => 'Sebagai Admin LSP, kelola validator lewat menu Manajemen Validator. Untuk menambah validator baru, buka Pengaturan → Tambah Validator Baru.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp'],
            'keywords' => ['asesor', 'tambah asesor', 'manajemen asesor'],
            'answer' => 'Kelola data Asesor lewat menu Manajemen Asesor. Anda bisa melihat, mengubah, atau menghapus profil asesor. Akun login asesor dibuat melalui menu Pengaturan.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp'],
            'keywords' => ['asesi', 'tambah asesi', 'manajemen asesi'],
            'answer' => 'Kelola data Asesi lewat menu Manajemen Asesi. Dari sana Anda bisa melihat detail, mengubah, atau menghapus data asesi yang terdaftar di LSP.',
        ],
        [
            'roles' => ['Admin_lsp'],
            'keywords' => ['rekomendasi', 'setujui', 'approve', 'apl 1'],
            'answer' => 'Admin LSP dapat memberikan rekomendasi pada Rekap FR APL 1. Buka Catatan Rekap → Rekap FR APL 1, lalu perbarui status rekomendasi sesuai hasil verifikasi dokumen asesi.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['profil', 'password', 'ganti password', 'pengaturan akun'],
            'answer' => 'Ubah profil dan password lewat menu Pengaturan di bagian bawah sidebar. Pastikan menggunakan password yang kuat dan simpan perubahan setelah selesai.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['login', 'masuk', 'tidak bisa login', 'lupa password'],
            'answer' => 'Admin Utama login di halaman Login Admin. Admin LSP dan Asesor login di halaman Login biasa dengan memilih peran yang sesuai. Jika lupa password, hubungi Admin Utama untuk reset.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['hapus', 'delete', 'menghapus'],
            'answer' => 'Penghapusan data bersifat permanen. Hapus Unit akan ikut menghapus Elemen dan KUK di dalamnya. Pastikan data yang dihapus sudah tidak digunakan sebelum melanjutkan.',
        ],
        [
            'roles' => ['Admin_utm', 'Admin_lsp', 'Asesor'],
            'keywords' => ['bantuan', 'help', 'tolong', 'panduan', 'cara pakai', 'cara menggunakan'],
            'answer' => 'Saya siap membantu! Tanyakan hal spesifik, misalnya: "cara mengisi KUK", "cara buat skema", "urutan form LSP", atau "cara lihat rekap". Ketik pertanyaan Anda di bawah.',
        ],
    ];
}

function bot_find_answer(string $message, string $role): string
{
    $normalized = bot_normalize_text($message);

    if ($normalized === '') {
        return 'Silakan ketik pertanyaan Anda. Contoh: "bagaimana cara mengisi KUK?"';
    }

    $greetings = ['halo', 'hai', 'hi', 'hello', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'pagi', 'siang', 'sore'];
    foreach ($greetings as $greeting) {
        if ($normalized === $greeting || str_starts_with($normalized, $greeting . ' ')) {
            return 'Halo! Saya asisten SISUSKOM. Ada yang bisa saya bantu? Tanyakan tentang Skema, Unit, Elemen, KUK, Form LSP, Rekap, atau fitur lainnya.';
        }
    }

    $bestScore = 0;
    $bestAnswer = null;

    foreach (bot_get_knowledge_base() as $entry) {
        if (!in_array($role, $entry['roles'], true)) {
            continue;
        }

        $score = 0;
        foreach ($entry['keywords'] as $keyword) {
            $keywordNorm = bot_normalize_text($keyword);
            if ($keywordNorm !== '' && str_contains($normalized, $keywordNorm)) {
                $score += max(1, strlen($keywordNorm));
            }
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestAnswer = $entry['answer'];
        }
    }

    if ($bestAnswer !== null) {
        return $bestAnswer;
    }

    return 'Maaf, saya belum menemukan jawaban untuk pertanyaan itu. Coba tanyakan dengan kata kunci seperti: KUK, Skema, Unit, Elemen, Form LSP, Rekap, atau Pengaturan.';
}
