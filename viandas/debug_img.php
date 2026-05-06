<?php
require_once 'includes/image_url.php';

$test_img = 'milanesa_pure.png';
$baseDir = resolve_vianda_image_base_dir();
$filePath = resolve_vianda_image_file_path($test_img);

echo "Base Dir: " . $baseDir . "\n";
echo "File Path for $test_img: " . ($filePath ?? 'NULL') . "\n";

if ($filePath && file_exists($filePath)) {
    echo "FILE EXISTS!\n";
} else {
    echo "FILE NOT FOUND!\n";
}

$url = build_vianda_image_url($test_img);
echo "Generated URL: " . $url . "\n";
?>
