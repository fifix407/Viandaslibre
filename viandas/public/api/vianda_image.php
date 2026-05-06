<?php
/**
 * Sirve imágenes de viandas desde la base real del proyecto:
 * /assets/img (preferido) o /public/assets/img (compatibilidad)
 */

require_once __DIR__ . '/../../includes/image_url.php';

$raw = isset($_GET['img']) ? (string)$_GET['img'] : '';
$realCandidate = resolve_vianda_image_file_path($raw);
if ($realCandidate === null) {
    http_response_code(404);
    header('Content-Type: image/svg+xml; charset=UTF-8');
    echo urldecode(substr(vianda_image_fallback_data_uri(), strlen('data:image/svg+xml;charset=UTF-8,')));
    exit;
}

$mime = mime_content_type($realCandidate);
if (!is_string($mime) || strpos($mime, 'image/') !== 0) {
    $mime = 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . (string) filesize($realCandidate));
header('Cache-Control: public, max-age=86400');
readfile($realCandidate);
exit;
?>
