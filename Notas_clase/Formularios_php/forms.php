<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario en PHP</title>
</head>
<body>
    <h1>Formulario de registro</h1>
    <hr>
    <form action="procesar.php" method="POST">
        <label for="nombre">Nombre: </label>
        <input type="text" name='nombre' id='nombre' required>
        <label for="edad">Edad: </label>
        <input type="number" name='edad' id='edad' required>
        <label for="correo">Correo: </label>
        <input type="email" name='correo' id='correo' required>

        <input type="submit" value="Registrar">
    </form>

    <?php
    
    ?>
</body>
</html>