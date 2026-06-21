<?php

function parse_ods_rows($file_path)
{

    $zip = new ZipArchive();
    if ($zip->open($file_path) !== true) {
        throw new RuntimeException('Gagal membuka file ODS.');
    }

    $content = $zip->getFromName('content.xml');
    $zip->close();

    if ($content === false) {
        throw new RuntimeException('File ODS tidak valid (content.xml tidak ditemukan).');
    }

    $xml = simplexml_load_string($content);
    if ($xml === false) {
        throw new RuntimeException('Gagal membaca isi file ODS.');
    }

    $ns = [
        'table' => 'urn:oasis:names:tc:opendocument:xmlns:table:1.0',
        'text' => 'urn:oasis:names:tc:opendocument:xmlns:text:1.0',
    ];

    $table_rows = $xml->xpath('//table:table-row');
    $rows = [];

    foreach ($table_rows as $row) {
        $cells = [];
        foreach ($row->children($ns['table'])->{'table-cell'} as $cell) {
            $repeat = 1;
            $attrs = $cell->attributes($ns['table']);
            if ($attrs && isset($attrs['number-columns-repeated'])) {
                $repeat = max(1, (int) $attrs['number-columns-repeated']);
            }

            $value = '';
            $text_nodes = $cell->xpath('.//text:p');
            if (!empty($text_nodes)) {
                $parts = [];
                foreach ($text_nodes as $node) {
                    $parts[] = trim((string) $node);
                }
                $value = trim(implode(' ', $parts));
            }

            if ($value === '' && $repeat > 1) {
                continue;
            }

            for ($i = 0; $i < $repeat; $i++) {
                $cells[] = $value;
            }
        }

        while (count($cells) > 0 && trim((string) end($cells)) === '') {
            array_pop($cells);
        }

        $has_data = false;
        foreach ($cells as $cell_value) {
            if (trim($cell_value) !== '') {
                $has_data = true;
                break;
            }
        }

        if ($has_data) {
            $rows[] = $cells;
        }
    }

    return $rows;
}

function normalize_user_role($raw_role)
{
    $role = trim((string) $raw_role);
    $map = [
        'admin_lsp' => 'Admin_lsp',
        'admin lsp' => 'Admin_lsp',
        'adminlsp' => 'Admin_lsp',
        'asesor' => 'Asesor',
        'asesi' => 'Asesi',
    ];

    $key = strtolower(str_replace(['_', '-'], ' ', $role));
    $key = preg_replace('/\s+/', ' ', $key);

    if (isset($map[$key])) {
        return $map[$key];
    }

    if (in_array($role, ['Admin_lsp', 'Asesor', 'Asesi'], true)) {
        return $role;
    }

    return '';
}

function parse_user_import_rows($rows)
{
    if (count($rows) < 2) {
        throw new RuntimeException('File kosong atau hanya berisi header.');
    }

    $header = array_map(function ($col) {
        return strtolower(trim((string) $col));
    }, $rows[0]);

    $username_idx = array_search('username', $header, true);
    $password_idx = array_search('password', $header, true);
    $role_idx = array_search('role', $header, true);

    if ($username_idx === false || $password_idx === false || $role_idx === false) {
        throw new RuntimeException('Header wajib berisi kolom: Username, Password, Role.');
    }

    $users = [];
    foreach (array_slice($rows, 1) as $line => $row) {
        $row_number = $line + 2;
        $username = trim((string) ($row[$username_idx] ?? ''));
        $password = trim((string) ($row[$password_idx] ?? ''));
        $role_raw = trim((string) ($row[$role_idx] ?? ''));

        if ($username === '' && $password === '' && $role_raw === '') {
            continue;
        }

        $users[] = [
            'row' => $row_number,
            'username' => $username,
            'password' => $password,
            'role' => normalize_user_role($role_raw),
            'role_raw' => $role_raw,
        ];
    }

    return $users;
}
