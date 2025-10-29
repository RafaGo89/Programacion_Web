<?php
    $num1 = $_GET['num1'];
    $num2 = $_GET['num2'];
    $resultado = 0;

    $tipo_operacion = $_GET['operacion'];

    switch($tipo_operacion) {
        case 'suma':
            $resultado = $num1 + $num2;
        break;

        case 'resta':
            $resultado = $num1 - $num2;
        break;

        case 'multiplicacion':
            $resultado = $num1 * $num2;
        break;

        case 'division':
            if ($num2 == 0) {
                echo 'No se puede dividir entre 0<br>';
                exit;
            }
            $resultado = $num1 / $num2;
        break;

        default:
            echo 'La operación indicada no existe<br>';
            exit;
        break;
    }


    echo '----------------------------------<br>';
    echo "El resultado de la {$tipo_operacion} de {$num1} y {$num2} es: {$resultado}<br>";
    echo '----------------------------------';
    
?>