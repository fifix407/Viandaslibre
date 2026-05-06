<?php

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');     
define('DB_NAME', 'viandalibre_db');
define('DB_USER', 'root');
define('DB_PASS', '');


$conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);


if (!$conexion) {
    
    die("Error de conexión crítica: " . mysqli_connect_error());
}

// Seteamos el charset para evitar problemas con tildes y eñes
mysqli_set_charset($conexion, "utf8mb4");

/**
 * NOTA PARA LA DEMO: 
 * Se utiliza MySQLi para cumplir con el Paso 2 del PDF que exige 
 * el uso de mysqli_fetch_all() en la capa de la API.
 */
?>