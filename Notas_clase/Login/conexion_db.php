<?php
    $host = "localhost";
    $db_name "login_db_rafa";
    $user = "root";
    $password = "root";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Conexión exitosa!";
    }
    catch (PDOException $error) {
        die("Error de conexión: " . $error->getMessage());
    }