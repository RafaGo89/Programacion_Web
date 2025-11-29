<?php
    session_start();
    // Si no se ha inciado sesión y no se es profesor
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }

    try {
        require "conexion_bd.php";

        // Asegurarnos que se envío algo
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["id_calificacion"]) || empty($_POST["calificacion"]) ||
                !is_numeric($_POST["id_calificacion"]) || !is_numeric($_POST["calificacion"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        No se pueden dejar campos vacíos
                        </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/profesor/index.php");
                    exit; 
            }

            // Validación de formato de calificación
            if ($_POST['calificacion'] > 100 || $_POST['calificacion'] < 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Calificación fuera de rango (0-100).
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }                

            // Query para buscar que sí existe una tarea con ese id
            $check = $pdo->prepare("SELECT id FROM calificaciones WHERE id = :id");
            $check->execute([':id' => $_POST["id_calificacion"]]);

            if ($check->rowCount() == 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id de calificación no válido.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }

            // --- Si no están vacíos y se cumple el formato de los datos, continuamos ---
            $id_calificacion = $_POST["id_calificacion"];
            $calificacion = $_POST["calificacion"];

            // Creamos consulta para actualizar la calificación
            $sql_calificar = "UPDATE calificaciones
                                SET
                                calificacion = :calificacion,
                                esta_calificada = true
                            WHERE
                                id = :id_calificacion";

            // Preparar consulta de inserción
            $stmt_calificar = $pdo->prepare($sql_calificar);

            $resultado = $stmt_calificar->execute([
                ':calificacion'          => $calificacion,
                ':id_calificacion'       => $id_calificacion
            ]);

            if ($resultado) {
                $message = "<div class='alert alert-success mt-2' role='alert'>
                            Tarea calificada con éxito!
                            </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/profesor/index.php");
                    exit; 
            }

            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Error al crear la tarea. Inténtalo de nuevo.
                        </div>";

            // Si hubo algún error en la base de datos
            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
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
        header("Location: ../home/profesor/index.php");
        exit;
    }