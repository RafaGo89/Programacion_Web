<?php
    session_start();

    // Validamos que se haya enviado un parámetro 'id' por la URL y que sea un número válido
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Parametro de id no válido.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_usuarios.php");
        exit;
                    
    }

    // Convertimos el valor recibido a entero por seguridad
    $id = intval($_GET['id']);

    try {
        require_once("conexion_bd.php");

        // Verificamos que el usuario exista
        $verificar = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id");
        $verificar->execute([':id' => $id]);

        // Si no se encontró un usuario con ese id
        if ($verificar->rowCount() === 0) {
            $message = "<div class='alert alert-danger mt-2' role='alert'>
                        No se encontró un usuario con el id {$id}.
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../home/admin/ver_usuarios.php");
            exit;
        }

        // Si el usuario existe, cambiamos su estatus a 'elimiando'
        // preparando la consulta
        $eliminar = $pdo->prepare("UPDATE usuarios SET id_estatus = 4 WHERE id = :id");
        $eliminar->execute([':id' => $id]);
                
        $message = "<div class='alert alert-success mt-2' role='alert'>
                    Usuario eliminado con éxito
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_usuarios.php");
        exit;
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Ocurrió un error, inténtalo de nuevo.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/admin/ver_usuarios.php");
        exit;
    }