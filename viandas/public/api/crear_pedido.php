<?php
header('Content-Type: application/json');

// 1. Leemos el "Body" (el cuerpo) de la petición que viene en formato JSON
$json_recibido = file_get_contents('php://input');

// 2. Lo transformamos en un objeto de PHP para poder usarlo
$datos = json_decode($json_recibido, true);

if ($datos) {
    // Acá es donde podrías hacer un INSERT en la base de datos
    // Por ahora, solo respondemos para demostrar que funcionó
    echo json_encode([
        "status" => "Éxito",
        "mensaje" => "Pedido recibido en el servidor",
        "datos_que_me_mandaste" => $datos
    ]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "No se recibió nada en el Body"]);
}
?>