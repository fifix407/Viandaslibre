<?php
require_once 'includes/auth.php';
// Mocking index.php logic to see what's happening
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$requestPath = '/viandas/public/admin/login'; // Simulated
$requestPath = str_replace('\\', '/', $requestPath);

$base_path = '/viandas/public'; // Simulated based on user info

define('BASE_URL', $base_path);
$uriPath = $requestPath;
$usesFrontControllerInUrl = false;
define('ROUTER_BASE', BASE_URL . ($usesFrontControllerInUrl ? '/index.php' : ''));

function route_url($path = '/') {
    $normalizedPath = '/' . ltrim((string)$path, '/');
    $base = defined('ROUTER_BASE') ? ROUTER_BASE : (defined('BASE_URL') ? BASE_URL : '');
    return rtrim($base, '/') . $normalizedPath;
}

echo "BASE_URL: " . BASE_URL . "\n";
echo "ROUTER_BASE: " . ROUTER_BASE . "\n";
echo "Route for /admin/login: " . route_url('/admin/login') . "\n";
?>
