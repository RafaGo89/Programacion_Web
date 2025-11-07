<?php
    /**
     * Archivo 03
     * ¿Cómo destruir la Cookie?
     */

    setcookie('user','',time()-120);

    /**
     * - Es el nombre de la cookie que quieres eliminar. "user", Debe coincidir exactamente con el nombre usado al crearla.
     * - Es el nuevo valor que le estás asignando. En este caso se establece como una cadena vacía, lo que efectivamente deja el contenido vacío.
     * - time() devuelve la hora actual (timestamp en segundos). Al restarle 120, se obtiene una hora en el pasado (hace 2 minutos).
     * NOTA: Esto significa que la cookie se crea con una fecha de expiración anterior al momento actual, por lo tanto el navegador interpreta que ya venció y la elimina inmediatamente.
     */


    

    // Redirigir a la página "validar_cookie.php"
    header("Location: validate_cookie.php");
    