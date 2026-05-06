<?php
/**
 * DEMO TÉCNICA: API REST - Menú del Día
 * Objetivo: Conectar el Frontend con la Base de Datos y servir JSON.
 */

// PASO 1: Establecer el encabezado JSON (Requerimiento obligatorio del PDF)
header('Content-Type: application/json; charset=utf-8');

require_once '../../app/models/Vianda.php';
require_once '../../includes/image_url.php';

try {
    $viandaModel = new Vianda();
    $viandas = $viandaModel->getAllDisponibles();
    foreach ($viandas as &$vianda) {
        $vianda['imagen_src'] = build_vianda_image_url($vianda['imagen_url'] ?? '');
        $vianda['imagen_fallback'] = vianda_image_fallback_data_uri();
    }
    unset($vianda);
    echo json_encode([
        "status" => "success",
        "data" => $viandas
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error interno en la consulta a la base de datos."
    ]);
}
?>