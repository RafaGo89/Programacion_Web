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

        // Si obtuvimos una petición GET
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
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
            // Procedemos a cambiar el estatus de la solicitud
            $sql = "UPDATE solicitudes SET 
                           estado = 'Aprobado',                        
                           fecha_respuesta = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);

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
                           fecha_respuesta = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
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