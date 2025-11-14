<?php
    $host = 'localhost';
    $db_name = 'centro_integra';
    $usuario = 'root';
    $contrasena = '';
    $port = 3308;

    // Intentamos ejecutar la conexión a la base de datos
    try {
        $pdo = New PDO(
            "mysql:host=$host;dbname=$db_name;port=$port;charset=utf8",
            $usuario,
            $contrasena
        );

        // Activar los mensajes de error claros en caso de fallas
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }
    catch (PDOException $error) {
        die("Error de conexión " . $error->getMessage());
    }