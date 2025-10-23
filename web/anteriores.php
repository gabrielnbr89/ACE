<!DOCTYPE html>
<html>
<head>
    <title>Historial</title>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="js/jquery-3.5.0.js" ></script>
    <script src="js/popper.min.js" ></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/highcharts.js"></script>
    <script src="js/exporting.js"></script>
</head>
<body style="background-image: url('css/pcb.jpg'); background-repeat: no-repeat; background-size: cover;">
<header style="margin-top: 4rem">
            <div class="container bg-transparent ">
                <div class="row bg-transparent">
                    <div class="col-1" bg-transparent></div>
                    <div class="col-12 col-sm-10 bg-primary">
                        <h1 class="text-center">Historial de mediciones:</h1>
                    </div>
                    <div class="col-1" bg-transparent></div>
                </div>
            </div>
</header>
<?php
require_once 'includes/db_connect.php';
require_once 'includes/get_mediciones.php';

// --- Sanear POST ---
$usuario = isset($_POST['usuario']) ? mysqli_real_escape_string($db, $_POST['usuario']) : '';
$topic   = isset($_POST['topic'])   ? mysqli_real_escape_string($db, $_POST['topic'])   : '';
$fecha   = isset($_POST['fecha'])   ? mysqli_real_escape_string($db, $_POST['fecha'])   : '';

// --- Validación ---
if ($usuario === '' || $topic === '' || $fecha === '') {
    echo "<p class='text-center text-light mt-4'>Faltan parámetros.</p>";
    mysqli_close($db);
    exit;
}

// --- Obtener datos ---
$result = obtenerMediciones($db, $usuario, $topic, $fecha);
$datosGrafico = $result['datos'] ?? [];
$unidad = $result['unidad'] ?? '';
$topic = $result['topic'] ?? '';

mysqli_close($db);
?>

<section>
    <div class="container" bg-transparent>
        <div class="row">
            <div class="col-1 bg-transparent"></div>
            <div class="col-12 col-sm-10 bg-primary" id="container1"></div>
            <div class="col-1" bg-transparent></div>
        </div> 
    </div>        
</section>
<?php if (!empty($datosGrafico)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Highcharts.chart('container1', {
        chart: { type: 'line', zoomType: 'x' },
        colors: ['#337ab7', '#cc3c1a'],
        title: { text: <?= json_encode($topic) ?> },
        xAxis: { type: 'datetime' },
        yAxis: {
            title: { text: <?= json_encode($unidad) ?> }
        },
        series: [{
            name: <?= json_encode($topic) ?>,
            data: <?= json_encode($datosGrafico) ?>
        }]
    });
});
</script>
<?php else: ?>
    <div class="text-center text-light mt-4">
        No hay mediciones registradas para la selección indicada.
    </div>
<?php endif; ?>

<div class="container">
    <div class="row bg-transparent">
        <div class="col-1 bg-transparent"></div>
            <div class="col-12 col-sm-10 bg-primary">
            <input type="button" class="btn btn-success" value="Regresar" id="regresar" onclick="history.back()"/>
            </div>
        <div class="col-1 bg-transparent"></div>
    </div>    
</div>        
</body>
</html>