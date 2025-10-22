<?php // Esto va siempre que se necesite de código PHP

echo "Hola mundo<br>";

$saludo = 'Hola mundo otra vez<br>';

echo $saludo;

// Definiendo una constante
define("PI", 3.1416);

echo "El valor de PI es: " . PI;

// Defininiendo variables
$num1 = 2;
$num2 = 4;
$resultado = $num1 + $num2;

echo "<br>El resultado de la suma es: $resultado<br><br>";

$tabla = 2;

// Ciclo for
for ($i = 1; $i <= 10; $i++){
    echo "$tabla X $i = " . $tabla * $i . "<br>";
}

// Definiendo una función
function sumar($para1, $para2){
    $result = $para1 + $para2;
    
    return $result;
}

// Llamando a una función
echo "<br>Usando una función para sumar: " . sumar(10, 100);

?>