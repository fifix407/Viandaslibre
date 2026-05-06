<?php
$_SERVER['SCRIPT_NAME'] = '/viandas/public/api/get_viandas.php';
require_once 'includes/image_url.php';

$img = 'milanesa_pure.png';
echo "Generated URL: " . build_vianda_image_url($img) . "\n";
?>
