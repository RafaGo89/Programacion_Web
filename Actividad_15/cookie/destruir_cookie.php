<?php
    // Borramos las cookie
    setcookie('user', '', time()-60);
    setcookie('contador_sesiones', '', time()-60);

    // Redirigir a inciar sesión de nuevo
    header("Location: ../index.php");