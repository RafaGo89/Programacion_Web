<?php
    session_start();
    // Si no se ha inciado sesión y no se es admin
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }


    // Variables
    $roles = [];
    $estatuses = [];

    try {
        require_once("../../includes/conexion_bd.php");

        // Queries para obtener datos de la base de datos
        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
        $estatuses = $pdo->query("SELECT id, estado FROM estatus")->fetchAll(PDO::FETCH_ASSOC);

        // Si se ha enviado el formulario con método POST, procesamos la edición.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["nombres"]) || empty($_POST["a_paterno"]) || empty($_POST["a_materno"]) ||
                empty($_POST["correo"]) || empty($_POST["rol"]) || empty($_POST["estatus"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        No se pueden dejar campos vacíos
                        </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_usuario.php?id=" . $_POST['id']);
                exit; 
            }

             // Validación de formato de correo
            if (!filter_var($_POST["correo"], FILTER_VALIDATE_EMAIL)) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        Formato de correo electrónico inválido
                        </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_usuario.php?id=" . $_POST['id']);
                exit; 
            }

            // Obtenemos los datos del formulario
            $id = $_POST['id'];
            $nombres = trim($_POST['nombres']);
            $a_paterno = trim($_POST['a_paterno']);
            $a_materno = trim($_POST['a_materno']);
            $correo = trim($_POST['correo']);
            $rol = $_POST['rol'];
            $estatus = $_POST['estatus'];

            /*
            * Verificamos que el correo electrónico no esté en uso por otro usuario
            * con un ID distinto al actual.
            */
            $check = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :id");
            $check->execute([':correo' => $correo, ':id' => $id]);

            if ($check->rowCount() > 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            El correo ya está siendo usado por otro usuario.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_usuario.php?id=" . $_POST['id']);
                exit; 
            }
            else {
                // Si no hay conflicto, actualizamos los datos del usuario         
                $sql = "UPDATE usuarios SET 
                            nombres = :nombres,
                            a_paterno = :a_paterno,
                            a_materno = :a_materno,
                            correo = :correo,
                            id_rol = :rol,
                            id_estatus = :estatus,
                            fecha_modificacion = NOW()
                        WHERE id = :id";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombres'    => $nombres,
                    ':a_paterno'  => $a_paterno,
                    ':a_materno'  => $a_materno,
                    ':correo'     => $correo,
                    ':rol'     => $rol,
                    ':estatus' => $estatus,
                    'id' => $id
                ]);

                // Redirigimos para evitar reenvío del formulario al refrescar
                header("Location: editar_usuario.php?id=$id&actualizado=1");
                exit;
            } 

            // Si hubo error, recargamos los datos del usuario para mostrarlos
            // Preparamos la querie para traer los datos del usuario
            $stmt = $pdo->prepare("SELECT id, nombres,
                                          a_paterno, a_materno,
                                          correo, id_rol,
                                          id_estatus
                                        FROM usuarios
                                        WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // Si obtuvimos una petición GET
        elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
            // Convertimos el valor recibido a entero por seguridad
            $id = intval($_GET['id']);

            // Preparamos la querie para traer los datos del usuario
            $stmt = $pdo->prepare("SELECT id, nombres,
                                          a_paterno, a_materno,
                                          correo, id_rol,
                                          id_estatus
                                        FROM usuarios
                                        WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no se encontró a un usuario con ese ID
            if(!$usuario) {
                $message = "<div class='alert alert-danger mt-2' role='alert'>
                            No se encontró a un usuario con ese Id.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ver_usuarios.php");
                exit;
            }
        }
        else {
            // Si no se envío un ID válido mandamos un mensaje
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id enviado no válido.
                            </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ver_usuarios.php");
            exit;
        }

        // Mensaje de éxito si venimos de la redirección después de guardar cambios
        if (isset($_GET['actualizado']) && $_GET['actualizado'] == 1) {
            $message = "<div class='alert alert-success mt-2' role='alert'>
                            Usuario actualizado correctamente.
                            </div>";
                            
            $_SESSION['mensaje'] = $message;
        }
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ver_usuarios.php");
        exit;
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/imgs/favicon.png" type="image/png" sizes="48x48">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/estilos_panel.css">
    <title>Editar</title>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="container-fluid d-flex justify-content-between py-2 bg-primario">  
        <div class="d-flex">
            <img src="../../assets/imgs/logo.png" alt="logo_escuela" width="60px" height="60px">
        </div>

        <div class="d-flex align-items-center">
            <a href="../../cerrar_sesion.php" class="pe-1 fw-bold">Cerrar sesión</a>
            <img src="../../assets/imgs/usuario_foto.png" alt="logo_escuela" width="50px" height="50px">
        </div>  
    </header>

    <main class="container flex-grow-1 my-2 d-flex justify-content-center align-items-center">
        <div class="bg-secundario rounded shadow-lg mt-3">
            <div class="container-fluid mb-2 text-center bg-primario p-2 rounded-top">
                    <h2>Editar Usuario</h2>
            </div>
            <form class="px-3 py-2" action="editar_usuario.php" method="POST">     
                <?php
                    // Mostrar mensaje de error sí lo hay
                    if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                    }
                ?>
                
                <!-- ID oculto para saber qué usuario estamos editando -->
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                       

                <div class="mb-2">
                    <label class="form-label fw-bold fs-5" for="nombres">Nombres</label>
                    <input class="form-control" value="<?= htmlspecialchars($usuario['nombres']) ?>" type="text" name="nombres" id="nombres" placeholder="Rafael" required>
                </div>

                <div class="row">
                    <div class="col mb-2">
                        <label class="form-label fw-bold fs-5" for="a_paterno">Apellido paterno</label>
                        <input class="form-control" value="<?= htmlspecialchars($usuario['a_paterno']) ?>" type="text" name="a_paterno" id="a_paterno" placeholder="Rodríguez" required>
                    </div>
                    <div class="col mb-2">
                        <label class="form-label fw-bold fs-5" for="a_materno">Apellido materno</label>
                        <input class="form-control" value="<?= htmlspecialchars($usuario['a_materno']) ?>" type="text" name="a_materno" id="a_materno" placeholder="Gómez" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold fs-5"for="correo">Correo electrónico</label>
                    <input class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" type="email" name="correo" id="correo" placeholder="nombre@ejemplo.com" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="rol">Tipo de cuenta</label>
                    <select class="form-select" name="rol" id="rol" aria-label="Default select example" required>    
                        <?php foreach($roles as $rol): ?>                                            
                            <option value= "<?= $rol['id'] ?>" <?= $rol['id'] ==$usuario['id_rol'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['id'] . "- " . $rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="estatus">Estatus</label>
                    <select class="form-select" name="estatus" id="estatus" aria-label="Default select example" required>                    
                        <?php foreach($estatuses as $estatus): ?>                                            
                            <option value= "<?= $estatus['id'] ?>" <?= $estatus['id'] == $usuario['id_estatus'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estatus['id'] . "- " . $estatus['estado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex justify-content-center mt-auto">
                    <a href="ver_usuarios.php" class="btn btn-secondary mb-3 fs-5 me-3">Cancelar</a>
                    <input class="btn btn-accion mb-3 fs-5" type="submit" value="Guardar cambios">
                </div>
            </form>
        </div>
    </main>

    <footer class="container-fluid d-flex justify-content-between py-2 mt-3 bg-primario">
        <div>
            <span>&copy;Centro Educativo "Integra" 2025</span>
        </div>
        <div>
            <img class="mx-2" src="../../assets/imgs/instagram_logo.png" alt="instagram_logo" width="30px" height="30px">
            <img src="../../assets/imgs/facebook_logo.png" alt="facebook_logo" width="30px" height="30px">
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>