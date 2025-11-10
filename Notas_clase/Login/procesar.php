<?php
    session_start();
    require 'conexion_db.php';

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
            // Lectura de variables
            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            exit();
            

            // Simulación
            $db_user = "hola@gmail.com";
            $db_password = "1234";

            if ($usuario == $db_user && $password == $db_password) {
                // Creación de variable de sesión
                $_SESSION['usuario'] = $usuario;
                header("Location: home.php");
            }
            else {
                echo "<p>Usuario y/o contraseña incorrectos</p>";
            }
        }
        else {
            echo "<p>No es posible dejar campos vacío</p>";
        }
    }