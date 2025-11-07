<?php
    /**
     * Archivo 03
     * Destruir la variable de sesión 
     */

    session_start();    // Inicia la sesión
    session_unset();    // Elimina todas las variables de sesión
    session_destroy();  // Destruye la sesión

    // Redirigir a la página "validar_cookie.php"
    header("Location: validate_session.php");