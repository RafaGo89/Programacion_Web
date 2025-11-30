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

    // DEFINICIÓN DE VARIABLES PARA LA VISTA
    $titulo = "Lista de usuarios"; // Esto cambiará el <title> del header
    $ruta_estilos = "../../";       // Cuántas carpetas hay que subir para llegar a assets
    $ruta_cerrar_sesion = "../../";

    // Variables
    $usuarios = [];
    $busqueda = trim($_GET['busqueda'] ?? '');

    try {
        require_once("../../includes/conexion_bd.php");
            // Si hay un valor en busqueda preparamos la querie de busqueda
        if ($busqueda !== '') {
            $sql = "SELECT U.id, U.nombres,
                        U.a_paterno, U.a_materno,
                        U.correo, R.nombre AS rol,
                        E.estado, U.fecha_creacion,
                        U.fecha_modificacion
                    FROM usuarios as U
                    INNER JOIN roles AS R
                    ON U.id_rol = R.id
                    INNER JOIN estatus AS E
                    ON U.id_estatus = E.id
                    WHERE U.nombres LIKE :patron OR
                    U.correo LIKE :patron";
            
            // Preparamos y ejecutamos la consulta con un valor escapado de forma segura
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':patron' => "%$busqueda%"]);
        }
        else {
            // Si no hay un valor en busqueda, seleccionamos a todos los usuarios
            $sql = "SELECT U.id, U.nombres,
                        U.a_paterno, U.a_materno,
                        U.correo, R.nombre AS rol,
                        E.estado, U.fecha_creacion,
                        U.fecha_modificacion
                    FROM usuarios as U
                    INNER JOIN roles AS R
                    ON U.id_rol = R.id
                    INNER JOIN estatus AS E
                    ON U.id_estatus = E.id";

            // Ejecumtamos la querie sin parametros
            $stmt = $pdo->query($sql);
        }

        // Obtenemos los resultados de la querie en un arreglo asociativo
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: index.php");
        exit;
    }

    // Incluimos el header
    require_once("../../includes/header.php");
?>

    <main class="container-fluid flex-grow-1 text-center my-2">
        <div class="position-relative d-flex align-items-center mb-4 mt-3">
            <a href="index.php" type="button" class="btn btn-accion position-absolute start-0 top-50 translate-middle-y">Regresar</a>
            <h1 class="text-center w-100 m-0">Lista de usuarios</h1>
        </div>
        <hr>

        <div class="d-flex justify-content-between">
            <!-- Formulario de búsqueda -->
            <form class="mb-3 text-start" action="ver_usuarios.php" method="GET">
                <label class="form-label fw-bold" for="busqueda">Buscar por nombre o correo: </label>
                <input type="text" id="busqueda" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                <button type="submit" class="btn btn-accion">Buscar</button>
            </form>
            <!-- Formulario de reseteo de busqueda -->
            <form class="mb-3 text-start" action="ver_usuarios.php" method="GET">
                <input type="hidden" name="busqueda" value="">
                <button type="submit" class="btn btn-accion">Resetear búsqueda</button>
            </form>
        </div>

        <?php
            // Mostrar mensaje de error sí lo hay
            if (isset($_SESSION['mensaje'])) {

            echo $_SESSION['mensaje'];
            
            // Borrar el mensaje una vez se muestra
            unset($_SESSION['mensaje']);
            }
        ?>

        <table class="table table-striped table-hover align-middle">
            <caption>Lista de usuarios</caption>
            <thead>
                <tr class="align-middle">
                    <th scope="col">Id</th>
                    <th scope="col">Nombres</th>
                    <th scope="col">A. paterno</th>
                    <th scope="col">A. materno</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Creada</th>
                    <th scope="col">Modificada</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php  foreach ($usuarios as $usuario):?>
                    <tr>
                        <th scope="row"><?= $usuario['id'] ?></th>
                        <td><?= $usuario['nombres'] ?></td>
                        <td><?= $usuario['a_paterno'] ?></td>
                        <td><?= $usuario['a_materno'] ?></td>
                        <td><?= $usuario['correo'] ?></td>
                        <td><?= $usuario['rol'] ?></td>
                        <td><?= $usuario['estado'] ?></td>
                        <td><?= $usuario['fecha_creacion'] ?></td>
                        <td><?= $usuario['fecha_modificacion'] ?></td>
                        <td>
                            <div class="d-flex">
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-accion me-2">Editar</a>                                
                                <?php if ($usuario['estado'] != 'Eliminado'): ?>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-eliminar" data-id="<?= $usuario['id'] ?>">
                                        Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>            
        </table>

        <!-- Modal Confirmación de eliminar usuario -->
        <div class="modal fade" id="modal-eliminar" tabindex="-1" aria-labelledby="modal-eliminar-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primario">
                        <h1 class="modal-title fs-3" id="modal-eliminar-label">Advertencia</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start fs-5">
                        <p>¿Estás seguro que quieres eliminar a este usuario?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-accion" data-bs-dismiss="modal">Cerrar</button> 
                        <!-- Botón para eliminar con confirmación -->                       
                        <a href="../../includes/eliminar_usuario.php" id="btn-confirmar-eliminar" type="button" class="btn btn-danger" >Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php 
    // Incluimos el footer
    require_once("../../includes/footer.php");
?>