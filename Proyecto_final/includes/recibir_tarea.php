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
        require "conexion_bd.php";
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date('Y-m-d H:i:s');

        // Asegurarnos que se envío algo
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["comentarios"]) || empty($_POST["id_calificacion"]) ||
                !is_numeric($_POST["id_calificacion"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            No se pueden dejar campos vacíos
                            </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/estudiante/index.php");
                    exit; 
            }

            // Query para buscar que sí existe una materia con ese id
            $check = $pdo->prepare("SELECT id FROM calificaciones WHERE id = :id");
            $check->execute([':id' => $_POST["id_calificacion"]]);

            if ($check->rowCount() == 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id de calificación no válido.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/estudiante/index.php");
                exit; 
            }

            // --- Si no están vacíos y se cumple el formato de los datos, continuamos ---
            $comentarios = trim($_POST["comentarios"]);
            $id_calificacion = $_POST["id_calificacion"];

            // Creamos consulta para actualizar la fila en calificación
            $sql_actualizar = "UPDATE calificaciones SET fecha_entrega = :fecha_entrega,
                                                       esta_entregada = true,
                                                       comentarios = :comentarios
                            WHERE id = :id_calificacion";

            // Preparar consulta de inserción
            $stmt_actualizar = $pdo->prepare($sql_actualizar);

            $resultado = $stmt_actualizar->execute([
                ':fecha_entrega'         => $fecha_actual,
                ':comentarios'           => $comentarios,
                ':id_calificacion'       => $id_calificacion
            ]);

            // Si la inserción tuvo éxito 
            if ($resultado) {
                $message = "<div class='alert alert-success mt-2' role='alert'>
                            Tarea entregada con éxito!
                            </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/estudiante/index.php");
                    exit; 
            }

            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Error al crear la tarea. Inténtalo de nuevo.
                        </div>";

            // Si hubo algún error en la base de datos
            $_SESSION['mensaje'] = $message;
            header("Location: ../home/estudiante/index.php");
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
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/estudiante/index.php");
        exit;
    }