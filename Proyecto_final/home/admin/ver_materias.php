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
    $materias = [];
    $busqueda = trim($_GET['busqueda'] ?? '');

    try { 
        require_once("../../includes/conexion_bd.php");

        // Si hay un valor en busqueda preparamos la querie de busqueda
        if ($busqueda !== '') {
            $sql = "SELECT  M.id,
                            M.nombre AS materia,
                            M.descripcion,
                            CONCAT(P.nombres, ' ', P.a_paterno, ' ', P.a_materno) AS profesor,
                            E.estado,
                            M.fecha_creacion,
                            M.fecha_modificacion
                        FROM materias AS M
                        INNER JOIN usuarios as P
                        ON M.id_profesor = P.id
                        INNER JOIN estatus AS E
                        ON M.id_estatus = E.id
                        WHERE M.nombre LIKE :patron";
            
            // Preparamos y ejecutamos la consulta con un valor escapado de forma segura
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['patron' => "%$busqueda%"]);
        }
        else {
            // Si no hay un valor en busqueda, seleccionamos a todos los usuarios
            $sql = "SELECT  M.id,
                            M.nombre AS materia,
                            M.descripcion,
                            CONCAT(P.nombres, ' ', P.a_paterno, ' ', P.a_materno) AS profesor,
                            E.estado,
                            M.fecha_creacion,
                            M.fecha_modificacion
                        FROM materias AS M
                        INNER JOIN usuarios as P
                        ON M.id_profesor = P.id
                        INNER JOIN estatus AS E
                        ON M.id_estatus = E.id";

            // Ejecumtamos la querie sin parametros
            $stmt = $pdo->query($sql);
        }

        // Obtenemos los resultados de la querie en un arreglo asociativo
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: index.php");
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
    <title>Materias</title>
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

    <main class="container-fluid flex-grow-1 text-center my-2">
        <div class="position-relative d-flex align-items-center mb-4 mt-3">
            <a href="index.php" type="button" class="btn btn-accion position-absolute start-0 top-50 translate-middle-y">Regresar</a>
            <h1 class="text-center w-100 m-0">Lista de materias</h1>
        </div>
        <hr>

        <div class="d-flex justify-content-between">
            <!-- Formulario de búsqueda -->
            <form class="mb-3 text-start" action="ver_materias.php" method="GET">
                <label class="form-label fw-bold" for="busqueda">Buscar por nombre de materia: </label>
                <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                <button type="submit" class="btn btn-accion">Buscar</button>
            </form>
            <!-- Formulario de reseteo de busqueda -->
            <form class="mb-3 text-start" action="ver_materias.php" method="GET">
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
            <caption>Lista de materias</caption>
            <thead>
                <tr class="align-middle">
                    <th scope="col">Id</th>
                    <th scope="col">Materia</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Profesor</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Creada</th>
                    <th scope="col">Modificada</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php  foreach ($materias as $materia):?>
                    <tr>
                        <th scope="row"><?= $materia['id'] ?></th>
                        <td><?= $materia['materia'] ?></td>
                        <td><?= $materia['descripcion'] ?></td>
                        <td><?= $materia['profesor'] ?></td>
                        <td><?= $materia['estado'] ?></td>
                        <td><?= $materia['fecha_creacion'] ?></td>
                        <td><?= $materia['fecha_modificacion'] ?></td>
                        <td>
                            <div class="d-flex"> 
                                <a href="editar_materia.php?id=<?= $materia['id'] ?>" class="btn btn-accion me-2">Editar</a>
                                <?php if ($materia['estado'] != 'Eliminado'): ?>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-eliminar" data-id="<?= $materia['id'] ?>">
                                        Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>            
        </table>

        <!-- Modal Confirmación de eliminar materia -->
        <div class="modal fade" id="modal-eliminar" tabindex="-1" aria-labelledby="modal-eliminar-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primario">
                        <h1 class="modal-title fs-3" id="modal-eliminar-label">Advertencia</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start fs-5">
                        <p>¿Estás seguro que quieres eliminar a esta materia?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-accion" data-bs-dismiss="modal">Cerrar</button> 
                        <!-- Botón para eliminar con confirmación -->                       
                        <a href="../../includes/eliminar_materia.php" id="btn-confirmar-eliminar" type="button" class="btn btn-danger" >Eliminar</a>
                    </div>
                </div>
            </div>
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
    <script src="../../assets/js/leer_id_modal.js" defer></script>
</body>
</html>