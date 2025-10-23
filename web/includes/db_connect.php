<?php
// Parámetros de conexión
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "proyecto_final";
$dbPort = 3307;

// Crear conexión
$db = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

// Verificar conexión
if (!$db) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Codificación de caracteres
mysqli_set_charset($db, "utf8");
?>
