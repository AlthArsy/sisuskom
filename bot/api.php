<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$allowedRoles = ['Admin_utm', 'Admin_lsp', 'Asesor'];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles, true)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak.',
    ]);
    exit;
}

require_once __DIR__ . '/knowledge.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metode tidak diizinkan.',
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim((string) ($input['message'] ?? $_POST['message'] ?? ''));

if ($message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Pesan tidak boleh kosong.',
    ]);
    exit;
}

$answer = bot_find_answer($message, $_SESSION['role']);

echo json_encode([
    'success' => true,
    'reply' => $answer,
]);
