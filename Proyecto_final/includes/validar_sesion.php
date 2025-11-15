<?php
    session_start();
    require "conexion_bd.php";

    // Asegurarnos que se envío algo
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // PRIMER CASO DE ERROR: Campos vacíos
        if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    No se pueden dejar campos vacíos
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../index.php");
            exit;
        }

        // Validación de formato de correo
        if (!filter_var($_POST["correo"], FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Formato de correo electrónico inválido
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../index.php");
            exit; 
        }

        // --- Si no están vacíos y el correo tiene formato valido, continuamos ---
        $correo = trim($_POST['correo']);
        $contrasena = $_POST['contrasena'];

        // Consulta para buscar si el correo está registrado en la bd
        $sql = "SELECT id,
                    nombres,
                    a_paterno,
                    a_materno,
                    id_rol,
                    contrasena
                from usuarios
                WHERE correo = :correo";
        
        // Preparar la consulta contra inyecciones SQL
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta con el correo del usuario
        $stmt->execute(['correo' => $correo]);

        // Recuperamos la información en forma de arreglo asociativo
        $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // SEGUNDO CASO DE ERROR: Datos incorrectos
        // Si la consulta NO regresó algo O la contraseña NO coincide
        if (!$datos_usuario || !password_verify($contrasena, $datos_usuario['contrasena'])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Correo o contraseña incorrecta
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../index.php");
            exit;
        }
        
        // --- SI LLEGAMOS AQUÍ, EL LOGIN FUE EXITOSO ---
        $_SESSION['id_usuario'] = $datos_usuario['id'];
        
        $_SESSION['nombre_completo'] = $datos_usuario['nombres'] . 
                                        $datos_usuario['a_paterno'] .
                                        $datos_usuario['a_materno'];

        $_SESSION['id_rol'] = $datos_usuario['id_rol'];
        
        // Reedirección según el rol
        switch ($_SESSION['id_rol']) {
            case 1:
                break;
            case 2:
                header("Location: ../home/profesor");
                break;
            case 3:
                header("Location: ../home/estudiante");
                break;
            default:
                $message = "<div class='alert alert-danger mt-2' role='alert'>
                    Rol de usuario no reconocido
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../index.php");
                exit;
        }

        exit;
    }
    else {
        $message = "<div class='alert alert-danger mt-2' role='alert'>
                    Acceso no autorizado
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }