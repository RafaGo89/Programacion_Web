<?php
    $host = 'localhost';              // Dirección del servidor donde está MySQL (aquí, tu propia computadora)
    $dbname = 'login_db';             // Nombre de la base de datos a la que te quieres conectar
    $usuario = 'root';                // Usuario de MySQL (por defecto, “root” en local)
    $clave = '';                      // Contraseña del usuario de MySQL
    $port = 3308;

    try {                             // Intenta ejecutar el código de conexión
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8", 
            $usuario, 
            $clave
        );                            // Crea la conexión a la base de datos usando PDO

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION
        );                            // Activa los mensajes de error claros en caso de fallas
    } catch (PDOException $e) {        // Si ocurre un error al conectar...
        die("Error de conexión: " . $e->getMessage());
                                    // Muestra el error y detiene el programa
    }
?>