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
        $tipo_sesion = trim($_POST['tipo-sesion']);

        // Si algún campo está vacío
        if (empty($usuario) || empty($contrasena) || empty($color) ||
            empty($tipo_sesion)) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    No puedes dejar los campos obligatorios vacíos
                    </div>";
        }
        // Validación de credenciales
        elseif (!($usuario == USUARIO) && !($contrasena == CONTRASENA)) {
            $message = "<div class='alert alert-danger mt-2' role='alert'>
                    Usuario o contraseña equivocada
                    </div>";
        }
        else {
            // Iniciar tipo de sesión
            if ($tipo_sesion == 'sesion') {
                // Inicar la sesión
                session_start();

                // Manejo de contador de sesiones
                // Leer archivo
                $archivo = fopen("contadores/contador_sesion.txt", "r+");
                $contador_sesion = (int)fgets($archivo);
                // Aumentamos en +1 la cantidad de incios de sesión
                $contador_sesion+= 1;
                // Escribimos en el archivo el nuevo valor de cantidad de sesiones
                file_put_contents("contadores/contador_sesion.txt", $contador_sesion);
                fclose($archivo);

                // Establecer los valores de la sesión
                $_SESSION['usuario'] = $usuario;
                $_SESSION['contrasena'] = $contrasena;
                $_SESSION['color'] = $color;
                $_SESSION['contador_sesion'] = $contador_sesion;

                // Redirigimos al archivo sesión_validad.php
                header("Location: sesion/sesion_validada.php");
                exit;
            }
            elseif ($tipo_sesion == 'cookie') {
                // Manejo de contador de sesiones
                // Leer archivo
                $archivo = fopen("contadores/contador_cookies.txt", "r+");
                $contador_sesion = (int)fgets($archivo);
                // Aumentamos en +1 la cantidad de incios de sesión
                $contador_sesion+= 1;
                // Escribimos en el archivo el nuevo valor de cantidad de sesiones
                file_put_contents("contadores/contador_cookies.txt", $contador_sesion);
                fclose($archivo);

                // Inicar la cookie
                setcookie('color', $color, time()+60);

                // Iniciar cookies de cantidad de sesiones
                setcookie('contador_cookies', $contador_sesion, time()+60);

                // Redirigimos al archivo sesión_validad.php
                header("Location: cookie/cookie_validada.php");
                exit;
            } 
        }     
    }   
?>

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

                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="color" id="blanco" value="blanco" required checked>
                            <label class="form-check-label text-light" for="blanco">
                                Blanco
                            </label>
                        </div>

                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="color" id="azul" value="azul" required>
                            <label class="form-check-label text-light" for="azul">
                                Azul
                            </label>
                        </div>

                        <div class="mb-2">
                            <span class="text-light">Tipo de sesión:</span>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo-sesion" id="sesion" value="sesion" required checked>
                            <label class="form-check-label text-light" for="sesion">
                                Sesión
                            </label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="radio" name="tipo-sesion" id="cookie" value="cookie" required>
                            <label class="form-check-label text-light" for="cookie">
                                Cookies (60 segundos)
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
        <!-- Mostrar mensajes de error-->
        <?php if(!empty($message)) echo $message; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>