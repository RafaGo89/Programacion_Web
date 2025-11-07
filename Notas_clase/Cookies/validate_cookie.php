<!--
     * Archivo 02
     * ¿Cómo validar la Cookie creado?
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies PHP</title>
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
        //Leer Cookie si fue creada
        if(isset($_COOKIE['user'])){
            echo "<h3>Bienvenid@ " . $_COOKIE['user'] . "</h3>";
            echo "<a class='btn btn-danger' href='destroy_cookie.php'>Destruir Cookie</a>";
        } else{
            // En caso contrario, mostrar un mensaje al respecto
            echo "<h3>La cookie no está creada!</h3>";
            echo "<a class='btn btn-success' href='cookie.php'>Crear Cookie</a>";
        }
    ?>
</body>
</html>