<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/imgs/favicon.png" type="image/png" sizes="48x48">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/estilos_crear_cuenta.css">
    <title>Crear Cuenta</title>
</head>
<body>
    <main class="container d-flex justify-content-center align-items-center">
        <div class="formulario bg-secundario px-3 py-2 rounded shadow-lg">
            <form action="includes/nuevo_usuario.php" method="POST">
                <div class="mb-2 text-center">
                    <h2>Crear cuenta</h2>
                </div>

                <?php
                    // Mostrar mensaje de error sí lo hay
                    if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                }
                ?>

                <div class="mb-2">
                    <label class="form-label" for="nombres">Nombres</label>
                    <input class="form-control" type="text" name="nombres" id="nombres" placeholder="Rafael" required>
                </div>

                <div class="row">
                    <div class="col mb-2">
                        <label class="form-label" for="a_paterno">Apellido paterno</label>
                        <input class="form-control" type="text" name="a_paterno" id="a_paterno" placeholder="Rodríguez" required>
                    </div>
                    <div class="col mb-2">
                        <label class="form-label" for="a_materno">Apellido materno</label>
                        <input class="form-control" type="text" name="a_materno" id="a_materno" placeholder="Gómez" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label"for="correo">Correo electrónico</label>
                    <input class="form-control" type="email" name="correo" id="correo" placeholder="nombre@ejemplo.com" required>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="contrasena">Contraseña</label>
                    <input class="form-control" type="password" name="contrasena" id="contrasena" placeholder="••••••••" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="mostrarPass">
                    <label class="form-check-label" for="mostrarPass">
                        Mostrar contraseña
                    </label>
                </div>

                <div class="mb-2">
                    <span>Tipo de cuenta</span>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rol" id="alumno" value=3 required>
                    <label class="form-check-label" for="alumno">
                        Alumno
                    </label>
                </div>

                <div class="form-check form-check-inline mb-3">
                    <input class="form-check-input" type="radio" name="rol" id="profesor" value=2 required>
                    <label class="form-check-label" for="profesor">
                        Profesor
                    </label>
                </div>

                <div class="d-grid"><input class="btn btn-accion mb-3" type="submit" value="Crear cuenta"></div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        // 1. Seleccionamos los dos elementos que acabamos de crear
        const inputContrasena = document.getElementById('contrasena');
        const checkMostrar = document.getElementById('mostrarPass');

        // 2. Agregamos un "escuchador" al checkbox
        // que se activa CADA VEZ que cambia (se marca o desmarca)
        checkMostrar.addEventListener('change', function() {
            
            // 3. Comprobamos si el checkbox está MARCADO
            if (this.checked) {
                // Si está marcado, cambiamos el tipo del input a "text"
                inputContrasena.type = 'text';
            } else {
                // Si no está marcado, lo regresamos a "password"
                inputContrasena.type = 'password';
            }
        });
    </script>
</body>
</html>