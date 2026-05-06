<?php
require_once 'config/db.php';
global $conexion;
$result = mysqli_query($conexion, "SELECT id_vianda, nombre, imagen_url FROM viandas LIMIT 10");
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id_vianda'] . " | Nombre: " . $row['nombre'] . " | Imagen URL: " . $row['imagen_url'] . "\n";
}
?>
