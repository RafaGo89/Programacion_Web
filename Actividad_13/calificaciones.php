<?php
/*
Actividad 13 - Sistema de calificaciones en PHP
By: Rafael Rodríguez Gómez
*/

function estaAprobado(int $calificacion): string{
    // Función que revisa si un estudiante aprobo o no en base a su calificación
    if ($calificacion >= 70){
        return "Aprobado";
    }
    else {
        return "Reprobado";
    }
}

function obtenerPromedio(array $calificaciones): float{
    // Función que obtiene el promedio de calificaciones de un arreglo
    $sumatoria = 0;
    $promedio = 0;

    foreach($calificaciones as $calificacion){
        $sumatoria += $calificacion;
    }

    $promedio = $sumatoria / count($calificaciones);

    return $promedio;
}

define("NUM_ALUMNOS", 6);

// Estudiantes y sus calificaciones
$estudiantes = [
    "Valeria" => rand(50,100),
    "Natalia" => rand(50,100),
    "Pedro" => rand(50,100),
    "Julio" => rand(50,100),
    "Luisa" => rand(50,100),
    "Jorge" => rand(50,100)
];

echo "Resultados de los estudiantes<br><br>";

// Con $estudiante accedo al nombre del estudiante
// Con $calificacion accedo a su calificación
foreach($estudiantes as $estudiante => $calificacion){
    echo "Estudiante: {$estudiante} - Calificacion: {$calificacion} - " 
    . estaAprobado($calificacion) . "<br>";
}

echo "<br> Promedio General del grupo: " . round(obtenerPromedio($estudiantes), 2); 

?>