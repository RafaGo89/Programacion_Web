<?php
    // Destruir todo lo referente a la sesión
    session_start();
    session_unset();
    session_destroy();

    // Redirigir a inciar sesión de nuevo
    header("Location: ../index.php");