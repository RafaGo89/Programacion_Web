<?php
    session_start();

    if (isset($_SESSION['usuario'])) {
        echo "Bienvenido Usuario {$_SESSION['usuario']}";

        echo "<div><a href='logout.php'>Cerrar sesión</a></div>";
    }
    else {
        header("Location: index.html");
        exit;
    }