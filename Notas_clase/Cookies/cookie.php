<?php

    /**
     * Archivo 01
     * ¿Cómo crear un Cookie?
     */

    //Crear la Cookie
    // setcookie(nombre, valor, expiración, ruta, dominio, seguro, httponly);

    /**
     * Explicación a detalle:
     *  - nombre: cómo se llamará la cookie.
     *  - valor: el contenido que guardará (puede ser texto, números, etc.).
     *  - expiración: momento (en formato timestamp Unix) en que la cookie expirará.
     *  - Los demás parámetros son opcionales (por ejemplo, para restringir el dominio o la ruta).
     */

    // Ejemplo:
    setcookie('user', 'favian@cucea.udg.mx', time()+120);

    // Redirigir a la página "validar_cookie.php"
    header("Location: validate_cookie.php");