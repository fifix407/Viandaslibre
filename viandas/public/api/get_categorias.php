<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metodo no permitido']);
    exit;
}

require_once '../../app/models/Vianda.php';

try {
    $viandaModel = new Vianda();
    $categorias = $viandaModel->getCategorias();
    echo json_encode([
        'status' => 'success',
        'data' => $categorias
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'No se pudieron obtener categorias']);
}
?>
