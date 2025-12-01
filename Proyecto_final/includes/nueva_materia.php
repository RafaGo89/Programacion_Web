<?php
    session_start();
    // Si no se ha inciado sesión y no se es admin
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }

    require "conexion_bd.php";
    date_default_timezone_set('America/Mexico_City');
    $fecha_actual = date('Y-m-d H:i:s');

    // Asegurarnos que se envío algo
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // PRIMER CASO DE ERROR: Campos vacíos
        if (empty($_POST["nombre_materia"]) || empty($_POST["id_profesor"]) || empty($_POST["descripcion"])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    No se pueden dejar campos vacíos
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/admin/index.php");
                exit; 
        }

        // --- Si no están vacíos, continuamos ---
        $nombre_materia = $_POST["nombre_materia"];
        $id_profesor = $_POST["id_profesor"];
        $descripcion = $_POST["descripcion"];

        // Creamos consulta para insertar la nueva materia
        $sql_insertar = "INSERT INTO materias (nombre, descripcion, id_profesor,
                                               id_estatus, fecha_creacion)
                        VALUES (:nombre, :descripcion, :id_profesor, 2,
                                :fecha_creacion)";

        // Preparar consulta de inserción
        $stmt_insertar = $pdo->prepare($sql_insertar);

        $resultado = $stmt_insertar->execute([
            ':nombre' => $nombre_materia,
            ':descripcion' => $descripcion,
            ':id_profesor' => $id_profesor,
            ':fecha_creacion' => $fecha_actual
        ]);

        // Si la inserción tuvo éxito regresamos al login
        if ($resultado) {
            $message = "<div class='alert alert-success mt-2' role='alert'>
                    ¡Materia creada con éxito!
                    </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/admin/index.php");
                exit; 
        }

        $message = "<div class='alert alert-success mt-2' role='alert'>
                    Error al crear la materia. Inténtalo de nuevo.
                    </div>";

        // Si hubo algún error en la base de datos
        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/index.php");
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