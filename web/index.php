<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MONITOR ACES</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="js/jquery-3.5.0.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <style>
    body {
      background-image: url('css/pcb.jpg');
      background-repeat: no-repeat;
      background-size: cover;
    }
    .card-title {
      font-weight: 600;
      padding: 0.5rem;
    }
  </style>
</head>

<body>
  <header class="mt-5">
    <div class="container bg-transparent">
      <div class="row bg-transparent align-items-stretch">
        <div class="col-1 bg-transparent"></div>
        <div class="col-12 col-lg-8 bg-primary text-light p-3 rounded-start d-flex flex-column justify-content-center">
          <div>
            <h2 class="mb-1">UNSa - FACULTAD DE CIENCIAS EXACTAS</h2>
            <h2 class="mb-3">ANALIZADOR DE CONSUMO ELÉCTRICO</h2>
            <h4>Materia: Electrónica Digital III</h4>
            <h4>Profesor: Maiver Wilfredo Villena</h4>
            <h4>Estudiante: Gabriel Nicolás Barrionuevo Ramírez</h4>
          </div>
        </div>
        <div class="col-12 col-lg-2 bg-white text-center p-2 rounded-end d-flex align-items-center justify-content-center">
          <img src="css/logo_unsa2.jpg" alt="Logo UNSa" class="img-fluid" style="max-height: 150px;">
        </div>
        <div class="col-1 bg-transparent"></div>
      </div>
    </div>
  </header>

  <!-- Sección principal -->
  <section class="mt-4">
    <div class="container">
      <div class="row justify-content-center g-4 align-items-stretch">

        <!-- Tarjeta: Historial -->
        <div class="col-12 col-md-5 mb-3 mb-md-0 d-flex">
          <div class="card text-center shadow flex-fill d-flex flex-column">
            <div class="card-title bg-primary text-light border-bottom rounded-top">
              Consultar historial de mediciones
            </div>

            <div class="card-body">
              <form action="anteriores.php" method="POST" id="anteriores">
                <!-- Selección ACE -->
                <div class="mb-3">
                  <label for="usuario" class="form-label">Seleccionar ACE:</label>
                  <select name="usuario" id="usuario" class="form-select" title="Seleccione un ACE" required>
                    <option value="" selected disabled>Seleccione...</option>
                  </select>
                </div>

                <!-- Selección magnitud -->
                <div class="mb-3">
                  <label for="topic" class="form-label">Magnitud:</label>
                  <select name="topic" id="topic" class="form-select" title="Seleccione una magnitud" required>
                    <option value="" selected disabled>Seleccione...</option>
                  </select>
                </div>

                <!-- Fecha -->
                <div class="mb-3">
                  <label for="fecha" class="form-label">Fecha:</label>
                  <input type="date" name="fecha" id="fecha" class="form-select"
                         title="Seleccione una fecha" placeholder="aaaa-mm-dd" required>
                </div>

                <div class="text-center">
                  <input type="submit" class="btn btn-primary px-4" value="Enviar" name="Enviar">
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Tarjeta: Monitorear -->
        <div class="col-12 col-md-5">
          <div class="card shadow">
            <div class="card-title text-center bg-primary text-light border-bottom rounded-top">
              Monitorear en tiempo real
            </div>

            <div class="card-body text-center">
              <form action="realtime.php" method="POST" id="realtime">
                <label for="acex" class="form-label" title="Seleccione un ACE">
                  Seleccione el ACE que desea monitorear:
                </label>
                <select multiple class="form-control" name="acex" id="acex" required>
                  <?php include 'includes/online.php'; ?>
                </select>

                <div class="mt-3">
                  <input type="submit" class="btn btn-primary me-2 px-4" value="Monitorear" id="Monitorear">
                  <input type="button" class="btn btn-success px-4" value="Refrescar" id="refrescar">
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <div style="height: 50px;"></div>

  <!-- Scripts -->
  <script>
    // Cargar opciones de usuario y topic desde get_options.php
    $(document).ready(function() {
      $.getJSON('includes/get_options.php', function(data) {
        $('#usuario').append(data.usuarios);
        $('#topic').append(data.topics);
      });

      // Refresca la lista de ACEs conectados
      $("#refrescar").click(function () {
        $('#acex').load('includes/online.php');
      });

      // Validación del formulario "anteriores"
      $("#anteriores").submit(function (event) {
        const usuario = $("#usuario").val();
        const topic = $("#topic").val();
        const fecha = $("#fecha").val();
        if (!usuario || !topic || !fecha) {
          alert("¡Debe seleccionar un ACE, magnitud y fecha válidos!");
          event.preventDefault();
        }
      });

      // Validación del formulario "realtime"
      $("#realtime").submit(function (event) {
        const acex = $("#acex").val();
        if (!acex) {
          alert("¡Debe seleccionar el ACE que desea monitorear!");
          event.preventDefault();
        }
      });
    });
  </script>
</body>
</html>
