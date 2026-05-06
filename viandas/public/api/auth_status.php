<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metodo no permitido']);
    exit;
}

require_once '../../includes/auth.php';

echo json_encode([
    'status' => 'success',
    'data' => [
        'admin_logged_in' => isLoggedIn()
    ]
]);
?>
