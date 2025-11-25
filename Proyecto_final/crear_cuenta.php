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
        <div class="bg-secundario rounded shadow-lg">
            <div class="container-fluid mb-2 text-center bg-primario p-2 rounded-top">
                    <h2>Crear cuenta</h2>
            </div>
            <form class="px-3 py-2" action="includes/nuevo_usuario.php" method="POST">            
                <?php
                    // Mostrar mensaje de error sí lo hay
                    if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                    }
                ?>

                <div class="mb-2">
                    <label class="form-label fw-bold fs-5" for="nombres">Nombres</label>
                    <input class="form-control" type="text" name="nombres" id="nombres" placeholder="Rafael" required>
                </div>

                <div class="row">
                    <div class="col mb-2">
                        <label class="form-label fw-bold fs-5" for="a_paterno">Apellido paterno</label>
                        <input class="form-control" type="text" name="a_paterno" id="a_paterno" placeholder="Rodríguez" required>
                    </div>
                    <div class="col mb-2">
                        <label class="form-label fw-bold fs-5" for="a_materno">Apellido materno</label>
                        <input class="form-control" type="text" name="a_materno" id="a_materno" placeholder="Gómez" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold fs-5"for="correo">Correo electrónico</label>
                    <input class="form-control" type="email" name="correo" id="correo" placeholder="nombre@ejemplo.com" required>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold fs-5" for="contrasena">Contraseña</label>
                    <input class="form-control" type="password" name="contrasena" id="contrasena" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" data-toggle-password-for="contrasena" id="mostrarPass">
                    <label class="form-check-label" for="mostrarPass">
                        Mostrar contraseña
                    </label>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="tipo-cuenta-visual">Tipo de cuenta</label>
                    <input class="form-control" type="text" id="tipo-cuenta-visual" value="Alumno" disabled>                                    
                    <input type="hidden" name="rol" value="3">
                </div>

                <div class="d-grid">
                    <input class="btn btn-accion mb-3 fs-5" type="submit" value="Crear cuenta">
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="assets/js/mostrar_password.js" defer></script>
</body>
</html>