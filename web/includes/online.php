<?php
require_once 'db_connect.php'; // Usa tu archivo de conexión centralizado

// Consulta: obtener los ACE conectados (últimos 15 minutos)
$sql = "SELECT DISTINCT usuario FROM datos 
        WHERE TIMESTAMPDIFF(SECOND, fecha, NOW()) <= 900";

$acesConectados = mysqli_query($db, $sql);

// Verificar resultados
if ($acesConectados && mysqli_num_rows($acesConectados) > 0) {
    while ($ace = mysqli_fetch_assoc($acesConectados)) {
        $nombreACE = htmlspecialchars($ace['usuario']);
        echo "<option value='$nombreACE'>$nombreACE</option>";
    }
} else {
    echo "<option value='' disabled>No hay ACE conectados</option>";
}

mysqli_close($db);
?>
