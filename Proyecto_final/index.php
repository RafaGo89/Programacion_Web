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
    <link rel="stylesheet" href="assets/css/estilos_login.css">
    <title>Iniciar sesión</title>
</head>
<body class="d-flex align-items-center">
    <main class="container-fluid">
        <div class="row">
            <div class="col bg-primario d-flex flex-column justify-content-center border-end border-dark pb-5 px-4">
                <h1 class="color-texto text-center">Bienvenido</h1>
                <span class="color-texto text-center fs-4 mb-2"><strong>Centro Educativo "Integra"</strong></span>
                <span class="color-texto text-center fs-5 mb-2">Donde el aprendizaje y la tecnología se encuentran.</span>
                <span class="color-texto text-center">Inicia sesión para comenzar tu jornada académica, revisar tus materias</span>
                <span class="color-texto text-center">y comunicarte con tus docentes.</span>
                <div class="text-center mt-4">
                    <img src="assets/imgs/logo.png" alt="logo_escuela" width="150px" height="150px">
                </div>
            </div>

            <div class="derecha col d-flex flex-column justify-content-center px-4">
                <div class="container bg-secundario rounded p-3 shadow">
                    <form action="includes/validar_sesion.php" method="POST">
                        <div class="mb-3 text-center">
                            <h2>Inicia sesión</h2>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-5" for="correo">Correo electrónico</label>
                            <input class="form-control" type="email" name="correo" id="correo" placeholder="nombre@ejemplo.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-5"for="contrasena">Contraseña</label>
                            <input class="form-control" type="password" name="contrasena" id="contrasena" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-toggle-password-for="contrasena" id="mostrarPass">
                            <label class="form-check-label" for="mostrarPass">
                                Mostrar contraseña
                            </label>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-accion mb-3" type="submit">Iniciar sesión</button>
                        </div>
                    </form>
                    <div class="mb-3 text-center">
                        <dv-m><span>¿Eres estudiante y todavía no tienes cuenta? <a href="crear_cuenta.php">Crea una</a></span></dv-m>
                    </div>
                </div>
                <?php
                // Mostrar mensaje de error sí lo hay
                if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                }
                ?>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="assets/js/mostrar_password.js" defer></script>
</body>
</html>