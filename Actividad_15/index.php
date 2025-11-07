<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Session / Cookie</title>
</head>
<body class="d-flex align-items-center vh-100 bg-secondary">
    <main class="container-fluid">
        <div class="row">
            <div class="col d-flex flex-column justify-content-center">
                <div class="container rounded bg-dark p-3">
                    <form action="index.php" method="POST">
                        <div class="mb-3 text-center">
                            <h2 class="text-light">Inicia sesión</h2>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light" for="usuario">Nombre de usuario</label>
                            <input class="form-control" type="text" name="usuario" id="usuario" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-light"for="contrasena">Contraseña</label>
                            <input class="form-control" type="password" name="contrasena" id="contrasena" required>
                        </div>

                        <div class="mb-2">
                            <span class="text-light">Color de texto que te gustaría:</span>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="color" id="azul" value="azul" required checked>
                            <label class="form-check-label text-light" for="azul">
                                Azul
                            </label>
                        </div>

                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="color" id="gris" value="gris" required>
                            <label class="form-check-label text-light" for="gris">
                                Gris
                            </label>
                        </div>

                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="color" id="morado" value="morado" required>
                            <label class="form-check-label text-light" for="morado">
                                Morado
                            </label>
                        </div>

                        <div class="d-grid"><input class="btn btn-primary mb-3" type="submit" value="Iniciar sesión"></div>
                    </form>
                </div>
            </div>
            <div class="col d-flex flex-column justify-content-center">
                <div class="container rounded bg-dark p-3">
                    <h3 class="text-light text-center">Credenciales</h2>
                    <p class="text-light fs-5"><span class="fw-bold">Usuario:</span> usuario_prueba</p>
                    <p class="text-light fs-5"><span class="fw-bold">Contraseña:</span> holamundo</p>
                </div>
            </div>
        </div>

        <?php
            // Validación del formulario
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Constantes
                define("USUARIO", "usuario_prueba");
                define("CONTRASENA", "holamundo");

                // Obtener y limpiar los datos
                $usuario = trim($_POST['usuario']);
                $contrasena = trim($_POST['contrasena']);
                $color = trim($_POST['color']);

                // Si algún campo está vacío
                if (empty($usuario) || empty($contrasena) || empty($color)) {
                    echo "<div class='alert alert-warning mt-2' role='alert'>
                            No puedes dejar los campos obligatorios vacíos
                         </div>";
                    exit;
                }
                
                // Validación de credenciales
                if (!($usuario == USUARIO) && !($contrasena == CONTRASENA)) {
                    echo "<div class='alert alert-danger mt-2' role='alert'>
                            Usuario o contraseña equivocada
                         </div>";
                    exit;
                }
            }
        ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>