<?php
    session_start();
    // Borrar todo lo referente a la sesión
    session_unset();
    session_destroy();

    header("Location: index.html");