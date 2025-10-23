<?php
require_once 'db_connect.php';

// Consultas
$usuariosQuery = mysqli_query($db, "SELECT DISTINCT usuario FROM datos");
$topicsQuery   = mysqli_query($db, "SELECT DISTINCT topic FROM datos");

// Construcción de opciones para usuarios
$usuariosOptions = '';
while ($row = mysqli_fetch_array($usuariosQuery)) {
    $usuariosOptions .= '<option value="' . htmlspecialchars($row['usuario']) . '">' 
                      . htmlspecialchars($row['usuario']) . '</option>';
}

// Construcción de opciones para topics
$topicsOptions = '';
while ($row = mysqli_fetch_array($topicsQuery)) {
    $topicsOptions .= '<option value="' . htmlspecialchars($row['topic']) . '">' 
                    . htmlspecialchars($row['topic']) . '</option>';
}

mysqli_close($db);

// Salida en formato JSON
echo json_encode([
    'usuarios' => $usuariosOptions,
    'topics'   => $topicsOptions
]);
?>
