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

if ($idVianda <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'id_vianda invalido']);
    exit;
}

require_once '../../app/models/Vianda.php';
$viandaModel = new Vianda();
$ok = $viandaModel->softDelete($idVianda);

if (!$ok) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'No se pudo desactivar la vianda']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Vianda marcada como no disponible',
    'data' => ['id_vianda' => $idVianda, 'disponible' => 0]
]);
?>
