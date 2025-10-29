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
    <form action="procesar_2.php" method="GET">
        <label for="num1">Número 1: </label>
        <input type="number" name='num1' id='num1' required>

        <label for="num2">Número 2: </label>
        <input type="number" name='num2' id='num2' required>
        
        <div>
            <label for=""><strong>Operación:</strong></label>
            <label> Suma
                <input type="radio" name='operacion' value='suma'>
            </label>
            <label> Resta
                <input type="radio" name='operacion' value='resta'>
            </label>
            <label> Multiplicación
                <input type="radio" name='operacion' value='multiplicacion'>
            </label>
            <label> División
                <input type="radio" name='operacion' value='division'>
            </label>
        </div>
        <hr>
        <input type="submit" value="Calcular">
    </form>

    <?php
    
    ?>
</body>
</html>