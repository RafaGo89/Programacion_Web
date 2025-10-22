<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP</title>
</head>
<body>
    <h1>Tabla del 2</h1>
    
    <?php
        $tabla = 2;

        for ($i = 1; $i <= 10; $i++){
            echo "$tabla X $i = " . $tabla * $i . "<br>";
        }
    ?>
</body>
</html>