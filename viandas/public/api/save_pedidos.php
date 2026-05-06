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
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'JSON invalido']);
    exit;
}

require_once '../../app/models/Pedido.php';

$data = [
    'cliente_nombre' => trim((string)($payload['cliente_nombre'] ?? '')),
    'cliente_whatsapp' => trim((string)($payload['cliente_whatsapp'] ?? '')),
    'direccion_entrega' => trim((string)($payload['direccion_entrega'] ?? '')),
    'items' => $payload['items'] ?? [],
];

$pedidoModel = new Pedido();
$result = $pedidoModel->createWithDetails($data);

if (!$result['ok']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'No se pudo guardar el pedido']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Pedido guardado correctamente',
    'data' => [
        'id_pedido' => $result['id_pedido'],
        'total_pago' => $result['total_pago']
    ]
]);
?>
