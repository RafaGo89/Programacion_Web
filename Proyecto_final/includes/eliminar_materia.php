<?php
    session_start();

    // Si no se ha inciado sesión y no se es admin
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] != 1) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }

    // Validamos que se haya enviado un parámetro 'id' por la URL y que sea un número válido
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Parametro de id no válido.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_materias.php");
        exit;
                    
    }

    // Convertimos el valor recibido a entero por seguridad
    $id = intval($_GET['id']);

    try {
        require_once("conexion_bd.php");

        // Verificamos que la materia exista
        $verificar = $pdo->prepare("SELECT id FROM materias WHERE id = :id");
        $verificar->execute([':id' => $id]);

        // Si no se encontró un usuario con ese id
        if ($verificar->rowCount() === 0) {
            $message = "<div class='alert alert-danger mt-2' role='alert'>
                        No se encontró una materia con el id {$id}.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/admin/ver_materias.php");
            exit;
        }

        // Si la materia existe, cambiamos su estatus a 'elimianda'
        // preparando la consulta
        $eliminar = $pdo->prepare("UPDATE materias SET id_estatus = 4,
                                                       fecha_modificacion = NOW() 
                                   WHERE id = :id");
        $eliminar->execute([':id' => $id]);
                
        $message = "<div class='alert alert-success mt-2' role='alert'>
                    Materia eliminada con éxito
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_materias.php");
        exit;
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_materias.php");
        exit;
    }