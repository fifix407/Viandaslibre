<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metodo no permitido']);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') === false) {
    http_response_code(415);
    echo json_encode(['status' => 'error', 'message' => 'Content-Type debe ser application/json']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
$idVianda = isset($payload['id_vianda']) ? (int)$payload['id_vianda'] : 0;
$stock = isset($payload['stock']) ? (int)$payload['stock'] : null;

if ($idVianda <= 0 || $stock === null || $stock < 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'id_vianda o stock invalido']);
    exit;
}

require_once '../../app/models/Vianda.php';
$viandaModel = new Vianda();
$result = $viandaModel->updateStockRapido($idVianda, $stock);

if (!$result['ok']) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar stock/disponible']);
    exit;
}

$response = [
    'status' => 'success',
    'message' => 'Stock actualizado',
    'data' => [
        'id_vianda' => $idVianda,
        'stock_solicitado' => $stock,
        'disponible' => $result['disponible'],
        'usa_stock' => $result['usa_stock']
    ]
];

if (!empty($result['warning'])) {
    $response['warning'] = $result['warning'];
}

echo json_encode($response);
?>
