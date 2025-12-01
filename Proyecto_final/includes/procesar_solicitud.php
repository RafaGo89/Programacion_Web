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
        require_once("conexion_bd.php");
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date('Y-m-d H:i:s');

        // Si obtuvimos una petición GET
        if (!isset($_GET['id']) || !is_numeric($_GET['id']) 
            || !isset($_GET['accion'])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Error: Faltan datos principales (ID o Acción).
                    </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
            exit;
        }

        // Convertimos el valor recibido a entero por seguridad
        $id = intval($_GET['id']);        

        // Nos cercioramos que la solicitud exista
        $stmt = $pdo->prepare("SELECT id      
                                FROM solicitudes
                                WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Si no hay una solicitud con dicha Id
        if ($stmt->rowCount() === 0) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                        La solicitud con id {$id} no existe.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
            exit;
        }

        // Si se mandó a aprobar la solicitud
        if ($_GET['accion'] == 'aceptar') {
                        // VALIDACIÓN ESPECÍFICA: Para aceptar SÍ necesitamos la materia
            if (!isset($_GET['id_materia']) || !is_numeric($_GET['id_materia'])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        Error: Se necesita el ID de la materia para aceptar.
                        </div>";
                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit;
            }
            $id_materia = intval($_GET['id_materia']);
            
            // Procedemos a cambiar el estatus de la solicitud
            $sql = "UPDATE solicitudes SET 
                           estado = 'Aprobado',                        
                           fecha_respuesta = :fecha_respuesta
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':fecha_respuesta' => $fecha_actual
            ]);

            // Procedemos ver si la materia YA tiene tareas
            $sql = "SELECT id
                    FROM tareas
                    WHERE id_materia = :id_materia";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_materia' => $id_materia
            ]);

            $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay tareas simplemente salimos
            if (!$tareas) {
                $message = "<div class='alert alert-success mt-2' role='alert'>
                        Se aprobó la solicitud con éxito--.
                        </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit;
            }

            // Si hay tareas, obtenemos el id del alumno que solicitó la materia
            $sql = "SELECT id_alumno
                    FROM solicitudes
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);

            $id_alumno = $stmt->fetchColumn();

            // Asigamos las tareas al alumno
            foreach ($tareas as $tarea) {
                $sql = "INSERT INTO 
                        calificaciones (id_tarea, id_alumno, calificacion)
                        VALUES (:id_tarea, :id_alumno, 0)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id_tarea'  => $tarea['id'],
                    ':id_alumno' => $id_alumno
                ]);
            }

            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Se aprobó la solicitud con éxito.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
            exit;
        }

        // Si se mandó a rechazar la solicitud
        if ($_GET['accion'] == 'rechazar') {
            // Procedemos a cambiar el estatus de la solicitud
            $sql = "UPDATE solicitudes SET 
                           estado = 'Rechazado',                        
                           fecha_respuesta = :fecha_respuesta
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':fecha_respuesta' => $fecha_actual
            ]);

            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Se rechazó la solicitud con éxito.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
            exit;
        }

        // Si no se pudó procesar correctamente la solicitud
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/profesor/index.php");
        exit;
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/profesor/index.php");
        exit;
    }