<?php
function obtenerMediciones($conexion, $usuario, $topic, $fecha) {
    // --- Validar fecha ---
    try {
        $dt = new DateTime($fecha);
        $ano = $dt->format('Y');
        $mes = $dt->format('m');
        $dia = $dt->format('d');
    } catch (Exception $e) {
        return ['error' => 'Formato de fecha inválido'];
    }

    // --- Definir unidad según topic ---
    $unidad = '';
    switch ($topic) {
        case 'frecuencia': $unidad = 'Hertz'; break;
        case 'tension':    $unidad = 'Voltios'; break;
        case 'intensidad': $unidad = 'Amperes'; break;
        case 'potencia':   $unidad = 'Vatios'; break;
        case 'fp':         $unidad = ''; break;
        default:           $unidad = ''; break;
    }

    // --- Construir consulta ---
    $queryMediciones = "
        SELECT UNIX_TIMESTAMP(fecha) AS ts, payload 
        FROM datos
        WHERE YEAR(fecha) = '$ano'
          AND MONTH(fecha) = '$mes'
          AND DAY(fecha) = '$dia'
          AND usuario = '$usuario'
          AND topic = '$topic'
        ORDER BY fecha ASC
    ";

    $resultadoMediciones = mysqli_query($conexion, $queryMediciones);
    $datosGrafico = [];

    if ($resultadoMediciones) {
        while ($row = mysqli_fetch_assoc($resultadoMediciones)) {
            $raw = str_replace(',', '.', $row['payload']);
            $valor = is_numeric($raw) ? floatval($raw) : null;
            if ($valor !== null) {
                $ts_ms = ($row['ts'] * 1000) - 10800000; // Ajuste de -3h
                $datosGrafico[] = [$ts_ms, $valor];
            }
        }
    }

    return ['unidad' => $unidad, 'datos' => $datosGrafico, 'topic' => $topic];
}