<?php
    $host = "localhost";
    $db_name = "login_prueba";
    $user = "root";
    $password = "";
    $port = 3308; // Debido a que cambié el puerto del servidor de BD del WAMP

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Conexión exitosa!";
    }
    catch (PDOException $error) {
        die("Error de conexión: " . $error->getMessage());
    }