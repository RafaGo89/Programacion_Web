<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Resultado de la operación</title>
</head>
<body>
    <?php
        echo "<nav aria-label='breadcrumb'> 
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item fs-4 ps-2'><a href='calculadora.html'>Regresar</a></li>
                </ol>
             </nav>";

        // Variables
        $num1 = $_GET['num1'];
        $num2 = $_GET['num2'];
        $resultado = 0;
        $simbolo = '+';

        $tipo_operacion = $_GET['operacion'];

        switch($tipo_operacion) {
            case 'suma':
                $resultado = $num1 + $num2;
            break;

            case 'resta':
                $resultado = $num1 - $num2;
                $simbolo = '-';
            break;

            case 'multiplicacion':
                $resultado = $num1 * $num2;
                $simbolo = 'x';
            break;

            case 'division':
                if ($num2 == 0) {
                    echo 'No se puede dividir entre 0<br>';
                    exit;
                }
                $resultado = $num1 / $num2;
                $simbolo = '/';
            break;

            default:
                echo 'La operación indicada no existe<br>';
                exit;
            break;
        }

        echo "<div class='container mt-5 text-center fs-2 bg-dark text-light rounded p-2'>
                El resultado de la {$tipo_operacion} de {$num1} {$simbolo} {$num2} es:<br> {$resultado}
             </div>";
    
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>