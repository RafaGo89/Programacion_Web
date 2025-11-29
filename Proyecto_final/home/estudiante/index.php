<?php
    session_start();

    // Si no se ha inciado sesión y no se es estudiante
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }

    // Variables
    $materias = [];
    $inscripciones = [];
    $solicitudes = [];
    $tareas =[];

    try {
        require_once("../../includes/conexion_bd.php");

        $id_estudiante = $_SESSION['id_usuario'];

        $materias = $pdo->query("SELECT S.id_materia,
                                        M.nombre AS materia,
                                        CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS profesor
                                    FROM solicitudes AS S
                                    INNER JOIN materias AS M
                                    ON S.id_materia = M.id
                                    INNER JOIN usuarios AS U
                                    ON M.id_profesor = U.id
                                    WHERE S.estado = 'Aprobado' AND
                                          S.id_alumno = " . $id_estudiante)->fetchAll(PDO::FETCH_ASSOC);
        
        $inscripciones = $pdo->query("SELECT M.id,
                                             M.nombre AS materia,
                                             CONCAT(U.nombres, ' ', U.a_paterno, ' ', a_materno) AS profesor
                                    FROM materias AS M
                                    INNER JOIN usuarios AS U
                                    ON M.id_profesor = U.id
                                    WHERE M.id_estatus = 2 AND
                                          M.id NOT IN (SELECT DISTINCT id_materia
                                                        FROM solicitudes
                                                        WHERE id_alumno = {$id_estudiante})")->fetchAll(PDO::FETCH_ASSOC);
        
        $solicitudes = $pdo->query("SELECT S.id,
                                           M.nombre AS materia,
                                           CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS profesor,
                                           S.estado
                                           FROM solicitudes AS S
                                           INNER JOIN materias AS M
                                           ON S.id_materia = M.id
                                           INNER JOIN usuarios AS U
                                           ON M.id_profesor = U.id
                                           WHERE S.id_alumno = {$id_estudiante}
                                           ORDER BY S.fecha_solicitud DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        $tareas = $pdo->query("SELECT
                                    T.id as id_tarea,
                                    T.titulo,
                                    T.descripcion,
                                    S.id_alumno as alumno,
                                    M.nombre as materia,
                                    CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS profesor,
                                    T.fecha_limite,
                                    C.id as id_calificacion,
                                    T.ponderacion
                                    FROM
                                    tareas AS T
                                    INNER JOIN materias AS M ON T.id_materia = M.id
                                    INNER JOIN usuarios AS U ON M.id_profesor = U.id
                                    INNER JOIN solicitudes AS S ON M.id = S.id_materia
                                    INNER JOIN calificaciones AS C ON T.id = C.id_tarea
                                    WHERE
                                    S.estado = 'Aprobado'
                                    AND M.id_estatus NOT IN (4, 3)
                                    AND C.id_alumno = {$id_estudiante}
                                    AND S.id_alumno = {$id_estudiante}
                                    AND DATEDIFF(T.fecha_limite, NOW()) >= 0
                                    AND C.esta_entregada = false
                                    ORDER BY
                                    T.fecha_limite")->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Tablero</title>
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
        <?php
            echo "<h1 class='text-center mb-4 mt-3'>Hola de nuevo, estudiante $_SESSION[nombres]</h1>";        
        ?>
        <hr>

        <?php
            // Mostrar mensaje de error sí lo hay
            if (isset($_SESSION['mensaje'])) {

            echo $_SESSION['mensaje'];

            // Borrar el mensaje una vez se muestra
            unset($_SESSION['mensaje']);
            }
        ?>
        
        <!-- Primera fila -->
        <div class="row mx-1 justify-content-start">
            <!-- Materias -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Materias</h5>
                        <p class="card-text">Explora el catálogo de materias disponibles y envía tus solicitudes de inscripción a las que prefieras.</p>
                        <div class="d-flex justify-content-center mt-auto">
                            <a href="#" class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-ver-materias">Materias</a>
                            <a href="#" class="btn btn-accion" data-bs-toggle="modal" data-bs-target="#modal-inscribirse">Inscribirse</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Tus materias -->
            <div class="modal fade" id="modal-ver-materias" tabindex="-1" aria-labelledby="modal-ver-materias-label" aria-hidden="true">
                <div class="modal-dialog modal-lg  modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-ver-materias-label">Tus materias</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <?php foreach($materias as $materia): ?>
                                <li class="list-group-item d-flex mb-3 justify-content-between align-items-center bg-secundario">
                                    <div class="text-start">
                                        <p class="mb-0"><span class="fw-bold">Id materia:</span> <?= $materia['id_materia'] ?></p>
                                        <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $materia['materia'] ?></p>
                                        <p class="mb-1"><span class="fw-bold">Profesor:</span> <?= $materia['profesor'] ?></p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal inscribirse-->
            <div class="modal fade" id="modal-inscribirse" tabindex="-1" aria-labelledby="modal-inscribirse-label" aria-hidden="true">
                <div class="modal-dialog modal-lg  modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-inscribirse-label">Materias disponibles</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <?php foreach($inscripciones as $inscripcion): ?>
                                <li class="list-group-item mb-3 d-flex justify-content-between align-items-center bg-secundario">
                                    <div class="text-start">
                                        <p class="mb-0"><span class="fw-bold">Id:</span> <?= $inscripcion['id'] ?></p>
                                        <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $inscripcion['materia'] ?></p>
                                        <p class="mb-1"><span class="fw-bold">Profesor:</span> <?= $inscripcion['profesor'] ?></p>
                                    </div>
                                    <a href="../../includes/solicitar_materia.php?id_estudiante=<?= $id_estudiante ?>&id_materia=<?= $inscripcion['id'] ?>" class="btn btn-accion">Inscribirse</a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promedio -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Promedio general</h5>
                        <p class="card-text text-center">Tu promedio general actual basado en todas las calificaciones registradas.</p>
                        <p class="card-text text-center fs-1 fw-bold">95.0</p>
                        <a href="#" class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-ver-promedio">Promedio particular</a>
                    </div>
                </div>
            </div>

            <!-- Modal promedio -->
            <div class="modal fade" id="modal-ver-promedio" tabindex="-1" aria-labelledby="modal-ver-promedio-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-ver-promedio-label">Promedios particulares</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-secundario">
                                    <div class="text-start">
                                        <p class="mb-0"><span class="fw-bold">Id:</span> [id_materia]</p>
                                        <p class="mb-0"><span class="fw-bold">Materia:</span> [Nombre_Materia]</p>
                                        <p class="mb-1"><span class="fw-bold">Profesor:</span> [Nombre_Profesor]</p>
                                    </div>
                                    <span class="badge text-bg-secondary fs-5">90.7</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas entregas -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Próximas entregas</h5>
                        <p class="card-text">Mantente al día. Revisa aquí las fechas de entrega más cercanas para tus tareas, proyectos y exámenes.</p>
                        <a href="#" class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-entregas">Todas las entregas</a>
                    </div>
                </div>
            </div>

            <!-- Modal próximas entregas -->
            <div class="modal fade" id="modal-entregas" tabindex="-1" aria-labelledby="modal-entregas-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-entregas-label">Próximas entregas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion1">
                                <?php foreach($tareas as $tarea): ?>
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $tarea['id_tarea'] ?>" aria-expanded="false" aria-controls="<?= $tarea['id_tarea'] ?>">                                                
                                                <div class="text-start">                                                    
                                                    <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $tarea['materia'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Profesor:</span> <?= $tarea['profesor'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Fecha de entrega:</span> <?= $tarea['fecha_limite'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Ponderación:</span> <?= $tarea['ponderacion'] ?>%</p>
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $tarea['id_tarea'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordion1">
                                            <div class="accordion-body">
                                                <form action="../../includes/recibir_tarea.php" method="POST">
                                                    <h3 class="text-start"><?= $tarea['titulo'] ?></h3>                                                    
                                                    <p class="text-start"><?= $tarea['descripcion'] ?></p>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="comentarios">Agregue sus comentarios aquí</label>
                                                        <textarea class="form-control" name="comentarios" placeholder="Escriba sus comentarios" id="comentarios" style="height: 100px"></textarea>
                                                    </div>
                                                    <input type="hidden" name="id_calificacion" value="<?= $tarea['id_calificacion'] ?>">
                                                    <button class="btn btn-accion me-2">Enviar</button>
                                                </form>                                                
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tareas entregadas -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Tareas entregadas</h5>
                        <p class="card-text">Dale un vistazo a las actividades que has entregado y revisa la calificación que obtuviste.</p>
                        <a href="#" class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-tareas-entregadas">Tareas calificadas</a>
                    </div>
                </div>
            </div>

            <!-- Modal tareas entregadas -->
            <div class="modal fade" id="modal-tareas-entregadas" tabindex="-1" aria-labelledby="modal-tareas-entregadas-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-tareas-entregadas-label">Tareas entregadas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion1">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">                                                
                                            <div class="text-start">
                                                <p class="mb-0"><span class="fw-bold">Id:</span> [id_materia]</p>
                                                <p class="mb-1"><span class="fw-bold">Materia:</span> [Nombre_Materia]</p>
                                                <p class="mb-1"><span class="fw-bold">Profesor:</span> [Nombre_Profesor]</p>                                                
                                            </div>                                               
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="ms-2 me-auto">                        
                                                        <div class="text-start">
                                                            <p class="mb-0 fw-bold">Tarea #1</p>
                                                            <p class="mb-0"><span class="fw-bold">Ponderación:</span> 20%</p>
                                                            <p class="mb-0"><span class="fw-bold">Fecha de entrega:</span> 12/12/25 11:59 pm</p>
                                                            <p class="mb-0"><span class="fw-bold">Entregada el:</span> 12/12/25 10:05 pm</p>
                                                        </div>
                                                    </div>
                                                    <span class="badge text-bg-secondary fs-5">90.7</span>
                                                </li>                                                                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Segunda fila -->
        <div class="row mx-1 justify-content-start">
            <!-- Solicitudes pendientes -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Solicitudes pendientes</h5>
                        <p class="card-text">Da seguimiento a tus solicitudes de materias. Consulta aquí si ya fueron aprobadas, rechazadas o si aún están pendientes.</p>
                        <a href="#" class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-solicitudes">Solicitudes</a>
                    </div>
                </div>
            </div>

            <!-- Modal solicitudes pendientes -->
            <div class="modal fade" id="modal-solicitudes" tabindex="-1" aria-labelledby="modal-solicitudes-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-solicitudes-label">Solicitudes enviadas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <?php foreach($solicitudes as $solicitud): ?>
                                <li class="list-group-item mb-3 d-flex justify-content-between align-items-center bg-secundario">
                                    <div class="text-start">
                                        <p class="mb-0"><span class="fw-bold">Id materia:</span> <?= $solicitud['id'] ?></p>
                                        <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $solicitud['materia'] ?></p>
                                        <p class="mb-1"><span class="fw-bold">Profesor:</span> <?= $solicitud['profesor'] ?></p>
                                    </div>                                    
                                    <?php 
                                        // Cambiar color de la badge y el mensaje dependiendo del estado de la solicitud
                                        if ($solicitud['estado'] == 'Aprobado') {
                                            $badge = 'text-bg-success';
                                            $caso = 'Aprobada';
                                        }
                                        elseif ($solicitud['estado'] == 'Rechazado') {
                                            $badge = 'text-bg-danger';
                                            $caso = 'Rechazada';
                                        }
                                        else {
                                            $badge = 'text-bg-secondary';
                                            $caso = 'Pendiente';
                                        }
                                    ?>
                                    <span class="badge <?= $badge ?> fs-5"><?= $caso ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
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
</body>
</html>