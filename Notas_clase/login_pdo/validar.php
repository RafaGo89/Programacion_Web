<?php
    session_start();                     
    // Inicia la sesión para poder guardar datos del usuario si inicia sesión correctamente

    require 'conexion.php';               // Importa el archivo que contiene la conexión a la base de datos ($pdo)

    $correo = $_POST['username'];        // Obtiene el correo enviado desde el formulario (input "username")

    $password = $_POST['password'];      // Obtiene la contraseña enviada desde el formulario (input "password")

    $sql = "SELECT id, password, nombres, a_paterno, rol_id 
            FROM usuarios 
            WHERE correo = :correo";
    // Consulta SQL para buscar al usuario por su correo (se usa un placeholder :correo)

    $stmt = $pdo->prepare($sql);
    // Prepara la consulta para prevenir inyecciones SQL (consulta segura)

    $stmt->execute(['correo' => $correo]);
    // Ejecuta la consulta sustituyendo :correo por el valor real

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    // Obtiene el usuario como un arreglo asociativo (o false si no existe)

    if ($usuario && password_verify($password, $usuario['password'])) {
    // Verifica que el usuario exista y que la contraseña ingresada coincida
    // con la contraseña encriptada guardada en la base de datos

        $_SESSION['usuario_id'] = $usuario['id'];
        // Guarda el ID del usuario en la sesión

        $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['a_paterno'];
        // Guarda el nombre completo del usuario en la sesión

        $_SESSION['rol_id'] = $usuario['rol_id'];
        // Guarda el rol del usuario (Administrador, Profesor, etc.)

        header("Location: home.php");
        // Redirige al usuario al home si el login es correcto

        exit;
        // Detiene la ejecución del script después de redirigir
    } else {
        echo "Usuario o contraseña incorrectos.";
        // Muestra un mensaje simple si las credenciales no coinciden
    }
?>