<?php
    /*
    * Recuerda:
    *   Activamos el manejo de sesiones: session_start();
    * Ademas:
    *   Es necesario verificamos si el usuario ha iniciado sesión correctamente.
    *   Si no existe la variable de sesión 'usuario_id', lo redirigimos al inicio.
    *   Esto evita que alguien sin permisos acceda directamente al script por la URL.
    */

    // Mostrar errores (solo en entorno de desarrollo)
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    try {
        // Importamos el archivo que contiene la conexión a la base de datos usando PDO        
        require_once "conexion.php";

        /*
        * Validación adicional de contraseñas del lado del servidor
        * Esto es importante incluso si ya se validó en el navegador con JavaScript,
        * porque un usuario puede modificar o saltarse el JS.
        */
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            echo ("Error: Las contraseñas no coinciden.");
            exit; // Se detiene la ejecución si no coinciden
        }

        // En caso de saltarse la validación con HTML, volvemos a verificar con PHP que todos los campos obligatorios estén llenos
        if (
            empty($_POST['nombres']) ||
            empty($_POST['a_paterno']) ||
            empty($_POST['correo']) ||
            empty($_POST['password'])
        ) {
            echo "Todos los campos obligatorios deben llenarse. <a href='registrar_usuario.html'>Volver</a>";
            exit; // Se detiene si hay algún campo vacío
        }

        // Asignamos los datos del formulario a variables PHP
        $nombres    = $_POST['nombres'];
        $a_paterno  = $_POST['a_paterno'];
        $a_materno  = $_POST['a_materno'];
        $correo     = $_POST['correo'];

        // Ciframos la contraseña antes de guardarla (muy importante para seguridad)        
        $password   = password_hash($password, PASSWORD_DEFAULT);

        // Valores por defecto para rol y estatus (puedes explicar estos roles a los alumnos)        
        $rol_id     = 1;
        $estatus_id = 1;    

        // Verificamos si el correo ya está registrado en la base de datos
        $verificar = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $verificar->execute([':correo' => $correo]);

        if ($verificar->rowCount() > 0) {
            echo "El correo ya está registrado. <a href='registrar_usuario.html'>Intentar con otro</a>";
            exit;
        }

        // Si el correo no existe, insertamos el nuevo usuario usando consulta preparada
        $sql = "INSERT INTO usuarios (nombres, a_paterno, a_materno, correo, password, rol_id, estatus_id)
                VALUES (:nombres, :a_paterno, :a_materno, :correo, :password, :rol_id, :estatus_id)";

        $stmt = $conn->prepare($sql);

        // Ejecutamos la consulta pasando los datos de forma segura (evita inyección SQL)
        $stmt = $stmt->execute([
            ':nombres'    => $nombres,
            ':a_paterno'  => $a_paterno,
            ':a_materno'  => $a_materno,
            ':correo'     => $correo,
            ':password'   => $password,
            ':rol_id'     => $rol_id,
            ':estatus_id' => $estatus_id
        ]);

        echo "Usuario registrado correctamente.";

    } catch (PDOException $e) {
        // Si ocurre un error con la base de datos, lo mostramos        
        die ("Error PDO: " . $e->getMessage());
    }
?>