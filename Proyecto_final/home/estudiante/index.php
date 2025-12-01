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

    // DEFINICIÓN DE VARIABLES PARA LA VISTA
    $titulo = "Estudiante"; // Esto cambiará el <title> del header
    $ruta_estilos = "../../";       // Cuántas carpetas hay que subir para llegar a assets
    $ruta_cerrar_sesion = "../../";

    // Variables
    $materias = [];
    $inscripciones = [];
    $solicitudes = [];
    $tareas =[];

    // 1. Configurar zona horaria (CRUCIAL para que "hoy" sea hoy en mi país)
    date_default_timezone_set('America/Mexico_City');
    $hoy_mexico = date('Y-m-d');

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
                                          M.id_estatus NOT IN (3, 4)
                                    AND S.id_alumno = " . $id_estudiante)->fetchAll(PDO::FETCH_ASSOC);
        
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
                                    AND DATEDIFF(T.fecha_limite, '$hoy_mexico') >= 0
                                    AND C.esta_entregada = false
                                    ORDER BY
                                    T.fecha_limite")->fetchAll(PDO::FETCH_ASSOC);
           
        // CTE que obtiene el promedio general relativo, que cambia conforme se agreguen mas tareas con ponderación
        $calificacion_general = $pdo->query("WITH
                                                calificaciones_rel AS (
                                                    SELECT
                                                    (SUM(C.calificacion * T.ponderacion) / 100) / SUM(T.ponderacion) * 100 AS calificaciones
                                                    FROM
                                                    tareas AS T
                                                    INNER JOIN calificaciones AS C ON T.id = C.id_tarea
                                                    INNER JOIN materias AS M ON T.id_materia = M.id AND M.id_estatus NOT IN (3, 4) 
                                                    WHERE
                                                    C.id_alumno = {$id_estudiante}
                                                    GROUP BY
                                                    T.id_materia
                                                )
                                                SELECT
                                                ROUND(SUM(calificaciones) / COUNT(*), 2) AS calificacion_general
                                                FROM
                                                calificaciones_rel")->fetchColumn();              
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

            <!-- Calificaciones -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Calificación general</h5>
                        <p class="card-text text-center">Tu calificación general actual basado en todas las calificaciones registradas respecto a su ponderación.</p>
                        <p class="card-text text-center fs-1 fw-bold"><?= $calificacion_general ?></p>
                        <a href="#" class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-ver-calificacion">Calificaciones particulares</a>
                    </div>
                </div>
            </div>

            <!-- Modal calificaciones -->
            <div class="modal fade" id="modal-ver-calificacion" tabindex="-1" aria-labelledby="modal-ver-calificacion-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-ver-calificacion-label">Puntos ponderados obtenidos</h1>
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
                                        <?php 
                                            // Querie que obtiene los puntos ponderados obtenidos de los puntos disponibles
                                            $calificacion = $pdo->query("SELECT
                                                                                CONCAT(
                                                                                    ROUND(SUM((C.calificacion * T.ponderacion) / 100), 2),
                                                                                    '/',
                                                                                    SUM(T.ponderacion)
                                                                                ) AS calificacion
                                                                                FROM
                                                                                tareas AS T
                                                                                INNER JOIN calificaciones AS C ON T.id = C.id_tarea
                                                                                WHERE
                                                                                T.id_materia = {$materia['id_materia']}
                                                                                AND C.id_alumno = {$id_estudiante}")->fetchColumn();;
                                        ?>
                                        <span class="badge text-bg-secondary fs-5"><?= $calificacion ?></span>
                                    </li>
                                <?php endforeach; ?>                                
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
                            <div class="accordion" id="accordion_e">
                                <?php foreach($tareas as $tarea): ?>
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $tarea['id_tarea'] ?>_e" aria-expanded="false" aria-controls="<?= $tarea['id_tarea'] ?>_e">                                                
                                                <div class="text-start">                                                    
                                                    <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $tarea['materia'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Profesor:</span> <?= $tarea['profesor'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Fecha de entrega:</span> <?= $tarea['fecha_limite'] ?></p>
                                                    <p class="mb-0"><span class="fw-bold">Ponderación:</span> <?= $tarea['ponderacion'] ?>%</p>
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $tarea['id_tarea'] ?>_e" class="accordion-collapse collapse" data-bs-parent="#accordion1">
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
                            <div class="accordion" id="accordion_te">
                                <?php foreach($materias as $materia): ?>
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $materia['id_materia'] ?>_te" aria-expanded="false" aria-controls="<?= $materia['id_materia'] ?>_te">                                                
                                                <div class="text-start">
                                                    <p class="mb-0"><span class="fw-bold">Id:</span> <?= $materia['id_materia'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Materia:</span> <?= $materia['materia'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Profesor:</span> <?= $materia['profesor'] ?></p>                                                
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $materia['id_materia'] ?>_te" class="accordion-collapse collapse" data-bs-parent="#accordion_te">
                                            <div class="accordion-body">
                                                <?php 
                                                    // Querie para obtener la información de las tareas entregadas o no
                                                    $entregas = $pdo->query("SELECT
                                                                                T.titulo,
                                                                                T.ponderacion,
                                                                                T.fecha_limite,
                                                                                COALESCE(C.fecha_entrega, 'No entregada') AS fecha_entrega,
                                                                                C.calificacion
                                                                                FROM
                                                                                calificaciones AS C
                                                                                INNER JOIN tareas AS T ON C.id_tarea = T.id
                                                                                WHERE
                                                                                T.id_materia = {$materia['id_materia']}
                                                                                AND C.id_alumno = {$id_estudiante}
                                                                                ORDER BY
                                                                                T.fecha_limite")->fetchAll(PDO::FETCH_ASSOC);
                                                ?>
                                                <ul class="list-group">
                                                    <?php foreach($entregas as $entrega): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div class="ms-2 me-auto">                                                                                                                                                
                                                                <div class="text-start">
                                                                    <p class="mb-0 fw-bold"><?= $entrega['titulo'] ?></p>
                                                                    <p class="mb-0"><span class="fw-bold">Ponderación:</span> <?= $entrega['ponderacion'] ?>%</p>
                                                                    <p class="mb-0"><span class="fw-bold">Fecha de entrega:</span> <?= $entrega['fecha_limite'] ?></p>
                                                                    <p class="mb-0"><span class="fw-bold">Entregada el:</span> <?= $entrega['fecha_entrega'] ?></p>
                                                                </div>                                                            
                                                            </div>
                                                            <span class="badge text-bg-secondary fs-5"><?= $entrega['calificacion'] ?></span>
                                                        </li>    
                                                    <?php endforeach; ?>                                                                                            
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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

<?php 
    // Incluimos el footer
    require_once("../../includes/footer.php");
?>