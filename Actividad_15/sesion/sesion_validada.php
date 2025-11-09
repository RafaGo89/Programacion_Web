<?php
    // Iniciamos la sesión
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Session</title>
</head>
<body class="d-flex align-items-center vh-100 bg-secondary">
    <main class="container d-flex align-items-center justify-content-evenly rounded bg-dark p-2">
        <?php
            // Si no se ha definido la sesión, se regresa al loginx
            if (!isset($_SESSION['usuario']) || !isset($_SESSION['contrasena']) ||
               !isset($_SESSION['color'])){
                header("Location: ../index.php");
            }

            if ($_SESSION['color'] == 'blanco') {
                // Navegador para regresar al index
            echo "<p class='text-light fs-5'>Veces que has iniciado sesión: {$_SESSION['contador_sesion']}</p>";

            echo "<p class='text-light fs-5'>Has inciado sesión :)</p>";

            echo "<a class='btn btn-danger' href='cerrar_sesion.php'>Cerrar sesión</a>";
            }

            if ($_SESSION['color'] == 'azul') {
                // Navegador para regresar al index
            echo "<p class='text-info fs-5'>Veces que has iniciado sesión: {$_SESSION['contador_sesion']}</p>";

            echo "<p class='text-info fs-5'>Has inciado sesión :)</p>";

            echo "<a class='btn btn-secondary text-info' href='cerrar_sesion.php'>Cerrar sesión</a>";
            }
            
        ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>