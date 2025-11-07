<?php
    /**
     * Archivo 02
     * Validar la existencia de una variable de sesión 
     */

    // Antes de poder usar $_SESSION, es obligatorio iniciar la sesión con:
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions PHP</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
        }
        a{
            text-decoration: none;
        }
        h3{
            border-bottom: solid 1px #d1d1d1;
            margin-bottom: 30px;
        }
        .btn{
            padding: 10px 20px;
            text-align: center;
        }

        .btn-danger{
            outline: solid 1.5px crimson;
            color: crimson;
        }
        .btn-danger:hover{
            background-color: crimson;
            color: white;
        }

        .btn-success{
            outline: solid 1.5px green;
            color: green;
        }
        .btn-success:hover{
            background-color: green;
            color: white;
        }
    </style>
</head>
<body>
    <?php
        //Saber sí la variable de sesión ha sido definida
        if(isset($_SESSION['user'])){
            // Al haber una variable de sesión iniciada, se mostrará el siguiente mensaje:
            echo "<h3>Bienvenid@ al sistema </h3>";
            // Así mismo mostramos el botón para cerrar sesión (En realidad lo que haremos será destruir la variable de sesión existente)
            echo "<a class='btn btn-danger' href='destroy_session.php'>Destruir sesión</a>";
        } else{
            // En caso contrario, mostrará el siguiente mensaje:
            echo "<h3>La sesión no está creada!</h3>";
            // Y a su vez, mostraremos un botón para entonces crearla.
            echo "<a class='btn btn-success' href='session.php'>Crear sesión</a>";
        }
    ?>
</body>
</html>