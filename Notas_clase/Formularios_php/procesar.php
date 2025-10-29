<?php
    if (!empty($_POST['correo']) && !empty($_POST['edad']) && !empty($_POST['correo'])){
        $nombre = $_POST['nombre'];
        $edad = $_POST['edad'];
        $correo = $_POST['correo'];
        
        echo "El nombre del usuario es: {$nombre}<br>";
        echo "La edad del usuario es: {$edad}<br>";
        echo "El correo del usuario es: {$correo}";
    }
    
?>