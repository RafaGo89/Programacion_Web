<?php
    session_start();

    // Si no se ha inciado sesión y no se es alumno
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }

    try {
        require_once("conexion_bd.php");
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date('Y-m-d H:i:s');

        // Si obtuvimos una petición GET
        if (!isset($_GET['id_estudiante']) || !is_numeric($_GET['id_estudiante']) ||
            !isset($_GET['id_materia']) || !is_numeric($_GET['id_materia'])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/estudiante/index.php");
            exit;
        }

        // Convertimos el valor recibido a entero por seguridad
        $id_estudiante = intval($_GET['id_estudiante']);
        $id_materia = intval($_GET['id_materia']);

        // Nos cercioramos que la materia exista
        $stmt = $pdo->prepare("SELECT id      
                               FROM materias
                               WHERE id = :id_materia");
        $stmt->execute([':id_materia' => $id_materia]);

        // Si no hay una materia con dicha Id
        if ($stmt->rowCount() === 0) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                        La materia con id {$id} no existe.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/estudiante/index.php");
            exit;
        }

        // Nos cercioramos que el estudiante exista
        $stmt = $pdo->prepare("SELECT id      
                               FROM usuarios
                               WHERE id = :id_estudiante");
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        // Si no hay una materia con dicha Id
        if ($stmt->rowCount() === 0) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                        El estudiante con id {$id} no existe.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/estudiante/index.php");
            exit;
        }

        // Preparamos la inserción de la solicitud
        $sql = "INSERT INTO solicitudes (id_alumno, id_materia, fecha_solicitud)
                VALUES (:id_estudiante, :id_materia, :fecha_solicitud)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_estudiante' => $id_estudiante,
            ':id_materia'    => $id_materia,
            ':fecha_solicitud' => $fecha_actual
        ]);

        $message = "<div class='alert alert-success mt-2' role='alert'>
                        Se envió la solicitud con éxito.
                        </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/estudiante/index.php");
        exit;
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/estudiante/index.php");
        exit;
    }