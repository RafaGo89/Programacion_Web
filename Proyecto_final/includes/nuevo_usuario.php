<?php
    session_start();
    require "conexion_bd.php";

    // Asegurarse que se envío algo por el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // PRIMER CASO DE ERROR: Campos vacíos
        if (empty($_POST["nombres"]) || empty($_POST["a_paterno"]) || empty($_POST["a_materno"]) ||
            empty($_POST["correo"]) || empty($_POST["contrasena"]) || empty($_POST["rol"])) {

               $message = "<div class='alert alert-warning mt-2' role='alert'>
                    No se pueden dejar campos vacíos
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../crear_cuenta.php");
                exit; 
        }

        // Validación de formato de correo
        if (!filter_var($_POST["correo"], FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Formato de correo electrónico inválido
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../crear_cuenta.php");
            exit; 
        }

        // --- Si no están vacíos y el correo es válido, continuamos ---
        $nombres = trim($_POST['nombres']);
        $a_paterno = trim($_POST['a_paterno']);
        $a_materno = trim($_POST['a_materno']);
        $correo = trim($_POST['correo']);
        $rol = $_POST['rol'];

        // Guardar contraseña encriptada
        $hash_contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

        // Verificar que el correo no esté siendo utilizado
        $sql_correo = "SELECT id from usuarios WHERE correo = :correo";

        // Preparar consulta para inyecciones sql
        $stmt_correo = $pdo->prepare($sql_correo);
        $stmt_correo->execute(["correo" => $correo]);

        // Ejecutar consulta
        $usuario_existente = $stmt_correo->fetch();

        // Si se encontró un usuario con ese correo
        if ($usuario_existente) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ese correo electrónico ya está en uso
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../crear_cuenta.php");
                exit; 
        }

        // Si llegamos aquí, podemos ingresa al nuevo usuario
        $sql_insertar = "INSERT INTO usuarios (nombres, a_paterno, a_materno, correo,
                                               contrasena, id_rol)
                        VALUES (:nombres, :a_paterno, :a_materno, :correo, :contrasena,
                                :id_rol)";
        
        $stmt_insertar = $pdo->prepare($sql_insertar);
        
        $resultado = $stmt_insertar->execute([
            'nombres' => $nombres,
            'a_paterno' => $a_paterno,
            'a_materno' => $a_materno,
            'correo' => $correo,
            'contrasena' => $hash_contrasena,
            'id_rol' => $rol
        ]);

        // Si la inserción tuvo éxito regresamos al login
        if ($resultado) {
            $message = "<div class='alert alert-success mt-2' role='alert'>
                    ¡Cuenta creada con éxito! Ya puedes iniciar sesión.
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../index.php");
                exit; 
        }

        $message = "<div class='alert alert-success mt-2' role='alert'>
                    Error al crear la cuenta. Inténtalo de nuevo.
                    </div>";

        // Si hubo algún error en la base de datos
        $_SESSION['mensaje'] = $message;
        header("Location: ../crear_cuenta.php");
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