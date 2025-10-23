<!DOCTYPE html>
<html>
<head>
  <title>Realtime</title>
  <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
  <script src="js/paho-mqtt.js" type="text/javascript"></script>
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  <script src="js/jquery-3.5.0.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body style="background-image: url('css/pcb.jpg'); background-repeat: no-repeat; background-size: cover;">

<?php
$acex = isset($_POST['acex']) ? htmlspecialchars($_POST['acex']) : '';

if ($acex === '') {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>No se especificó el dispositivo a monitorear.</div></div>";
    exit;
}
?>

<header style="margin-top: 4rem">
  <div class="container bg-transparent">
    <div class="row bg-transparent">
      <div class="col-1 bg-transparent"></div>
      <div class="col-12 col-sm-10 bg-primary">
        <h1 class="text-center text-light">Mediciones en tiempo real:</h1>
      </div>
      <div class="col-1 bg-transparent"></div>
    </div>
  </div>
</header>

<section>
  <div class="container bg-transparent">
    <div class="row bg-transparent">
      <div class="col-1 bg-transparent"></div>
      <div class="col-12 col-sm-10 bg-primary rounded border-dark">
        <div class="row bg-transparent">
          <div class="col-2 bg-transparent"></div>
          <div class="col-12 col-sm-8">
            <div class="card text-center">
              <div class="card-title bg-primary text-light border">
                Monitoreando: <?php echo $acex; ?>
              </div>
              <div class="card-body">
                <div><strong>Frecuencia:</strong> <span id="frecuencia">-</span></div>
                <div><strong>Tensión:</strong> <span id="tension">-</span></div>
                <div><strong>Intensidad:</strong> <span id="intensidad">-</span></div>
                <div><strong>Potencia:</strong> <span id="potencia">-</span></div>
                <div><strong>Factor de potencia:</strong> <span id="fp">-</span></div>
              </div>
            </div>
            <div class="mt-3 text-center">
              <input type="button" class="btn btn-success" value="Regresar" onclick="history.back()">
            </div>
          </div>
          <div class="col-2 bg-transparent"></div>
        </div>
      </div>
      <div class="col-1 bg-transparent"></div>
    </div>
  </div>
</section>

<script>
  const usuario = 'page';
  const contrasena = '';
  const acex = <?php echo json_encode($acex); ?>;

  function onConnect() {
    console.log("Conectado al broker MQTT");
    client.subscribe("#");
  }

  function onConnectionLost(responseObject) {
    if (responseObject.errorCode !== 0) {
      console.log("Conexión perdida:", responseObject.errorMessage);
    }
  }

  function onMessageArrived(message) {
    const base = '/aces/' + acex + '/';
    const value = message.payloadString;

    switch (message.destinationName) {
      case base + 'frecuencia':
        document.getElementById("frecuencia").textContent = value + " Hz";
        break;
      case base + 'tension':
        document.getElementById("tension").textContent = value + " V";
        break;
      case base + 'intensidad':
        document.getElementById("intensidad").textContent = value + " A";
        break;
      case base + 'potencia':
        document.getElementById("potencia").textContent = value + " W";
        break;
      case base + 'fp':
        document.getElementById("fp").textContent = value;
        break;
    }
  }

  function onFailure(context, errorCode, errorMessage) {
    console.error("Error al conectar al servidor MQTT:", errorMessage);
  }

  const clientId = "ws" + Math.random();
  const client = new Paho.MQTT.Client("acesensor.ddns.net", 9001, clientId);

  client.onConnectionLost = onConnectionLost;
  client.onMessageArrived = onMessageArrived;

  client.connect({
    userName: usuario,
    password: contrasena,
    onSuccess: onConnect,
    onFailure: onFailure,
    reconnect: true
  });
</script>

</body>
</html>
