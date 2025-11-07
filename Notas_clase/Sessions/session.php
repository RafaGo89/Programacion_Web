<?php
    /**
     * Archivo 01
     * ¿Cómo crear una variable de sesión?
     */

    // Antes de poder usar $_SESSION, es obligatorio iniciar la sesión con:
    session_start(); 

    // Es una superglobal de PHP, es decir, una variable especial que está disponible en cualquier parte del código sin necesidad de declararla como global.
    $_SESSION['user'] = "favian@cucea.udg.mx";

    /**
     * 'user' es la clave dentro del arreglo $_SESSION. Funciona como el “nombre” de la variable de sesión. 
     * En este caso, estás creando una variable llamada 'user'.
     * "favian@cucea.udg.mx" Es el valor que estás guardando en esa variable.
     */       
    
    // Redirigir a la página "validar_session.php"
    header("Location: validate_session.php");
    