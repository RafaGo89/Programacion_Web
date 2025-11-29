<?php
    session_start();

    // Si no se ha inciado sesión y no se es admin
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }

    // Variables
    $materias = [];
    $solicitudes = [];

    try {
        require_once("../../includes/conexion_bd.php");

        $id_profesor = $_SESSION['id_usuario']; 
        
        // Queries para obtener datos de la base de datos
        $materias = $pdo->query("SELECT id, nombre, 
                                        DATE_FORMAT(fecha_creacion, '%d/%m/%Y') as fecha_creacion
                                 FROM materias
                                 WHERE id_estatus NOT IN (4, 3) AND
                                       id_profesor = " . $id_profesor)->fetchAll(PDO::FETCH_ASSOC);
        
        $solicitudes = $pdo->query("SELECT S.id, 
                                           CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS nombre,
                                           M.nombre AS materia,
                                           DATE_FORMAT(S.fecha_solicitud, '%d/%m/%Y') as fecha_solicitud  
                                    FROM solicitudes AS S
                                    INNER JOIN usuarios AS U
                                    ON S.id_alumno = U.id
                                    INNER JOIN materias AS M
                                    ON S.id_materia = M.id
                                    WHERE S.estado = 'Pendiente' AND
                                          U.id_estatus NOT IN (4, 3) AND 
                                          M.id_profesor = " . $id_profesor)->fetchAll(PDO::FETCH_ASSOC);

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
            echo "<h1 class='text-center mb-4 mt-3'>Hola de nuevo, profesor $_SESSION[nombres]</h1>";        
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
            <!-- Gestión de los grupos -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Tus grupos</h5>
                        <p class="card-text">Administra los grupos de tus materias y monitorea su avance.</p>
                        <button class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-grupos">Grupos</button>
                    </div>
                </div>
            </div>

            <!-- Modal grupos -->
            <div class="modal fade" id="modal-grupos" tabindex="-1" aria-labelledby="modal-grupos-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-grupos-label">Tus grupos</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion_g">
                                <?php foreach($materias as $materia): ?>
                                    <div class="accordion-item mb-3">                                    
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $materia['id'] ?>_g" aria-expanded="false" aria-controls="<?= $materia['id'] ?>_g">                                                
                                                <div class="text-start">
                                                    <p class="mb-0"><span class="fw-bold">Id:</span> <?= $materia['id'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Materia:</span> <?= $materia['nombre'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Fecha de creación:</span> <?= $materia['fecha_creacion'] ?></p>
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $materia['id'] ?>_g" class="accordion-collapse collapse" data-bs-parent="#accordion_g">
                                            <div class="accordion-body">                                                
                                                <?php 
                                                    // Obtenemos a los alumnos que son de una materia en particular y su calificación ponderada
                                                    $alumnos = $pdo->query("SELECT
                                                                                CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS nombre,
                                                                                CONCAT(
                                                                                    ROUND(SUM((C.calificacion * T.ponderacion) / 100), 2),
                                                                                    '/',
                                                                                    SUM(T.ponderacion)
                                                                                ) AS calificacion
                                                                                FROM
                                                                                tareas AS T
                                                                                INNER JOIN calificaciones AS C ON T.id = C.id_tarea
                                                                                INNER JOIN usuarios AS U ON C.id_alumno = U.id
                                                                                WHERE
                                                                                T.id_materia = {$materia['id']}
                                                                                GROUP BY
                                                                                C.id_alumno;")->fetchAll(PDO::FETCH_ASSOC);
                                                ?>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Alumno</th>
                                                        <th scope="col">Calificacion (puntos ponderados)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($alumnos as $alumno): ?>
                                                        <tr>
                                                            <td> <?= $alumno['nombre'] ?> </td>
                                                            <td><?= $alumno['calificacion'] ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solictudes pendientes -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Solicitudes pendientes</h5>
                        <p class="card-text text-center">Revisa las solicitudes de inscripción de los alumnos para tus materias. Aprueba o rechaza las peticiones aquí.</p>
                        <button class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-solicitudes-pendientes">Solicitudes</button>
                    </div>
                </div>
            </div>

            <!-- Modal solicitudes pendientes -->
            <div class="modal fade" id="modal-solicitudes-pendientes" tabindex="-1" aria-labelledby="modal-solicitudes-pendientes-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-solicitudes-pendientes-label">Solicitudes pendientes</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">                                                        
                            <ul class="list-group">
                                <?php foreach($solicitudes as $solicitud): ?>
                                <li class="list-group-item mb-3 d-flex justify-content-between align-items-center bg-secundario">
                                    <div class="text-start">
                                        <p class="mb-0"><span class="fw-bold">Alumno:</span> <?= $solicitud['nombre'] ?></p>
                                        <p class="mb-0"><span class="fw-bold">Materia:</span> <?= $solicitud['materia'] ?></p>
                                        <p class="mb-0"><span class="fw-bold">Fecha de solicitud:</span> <?= $solicitud['fecha_solicitud'] ?></p>
                                    </div>   
                                    <div class="d-grid gap-2 p-2">
                                        <a href="../../includes/procesar_solicitud.php?id=<?= $solicitud['id'] ?>&accion=aceptar" class="btn btn-success w-100">Aceptar</a>
                                        <a href="../../includes/procesar_solicitud.php?id=<?= $solicitud['id'] ?>&accion=rechazar" class="btn btn-danger w-100">Rechazar</a>
                                    </div>                           
                                </li>
                                <?php endforeach; ?>
                            </ul>                                                    
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tareas -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Tareas</h5>
                        <p class="card-text text-center">Crea tareas por grupos para tus alumnos y monitorea su avance.</p>
                        <div class="d-flex justify-content-center mt-auto">
                            <button class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-crear-tarea">Crear tarea</button>
                            <button class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-ver-tarea">Ver tareas</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear tarea -->
            <div class="modal fade" id="modal-crear-tarea" tabindex="-1" aria-labelledby="modal-crear-tarea-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-crear-tarea-label">Nueva materia</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../../includes/nueva_tarea.php" method="POST">
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="titulo">Titulo de la tarea</label>
                                    <input class="form-control" type="text" name="titulo" id="titulo" placeholder="Tarea #1" required>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="materia">Materia</label>
                                    <select class="form-select" name="materia" id="materia"  aria-label="Default select example" required>
                                        <option selected disabled value="" >Escoga una materia</option>
                                        <?php foreach($materias as $materia): ?>                                            
                                            <option value= "<?= $materia['id'] ?>">
                                                <?= $materia['id'] . "- " . $materia['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="descripcion">Descripción</label>
                                    <textarea class="form-control" name="descripcion" placeholder="Escriba la descripción de su tarea" id="descripcion" style="height: 200px"></textarea>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="ponderacion">Ponderacion (%)</label>
                                    <input class="form-control" type="number" min="1" max="100" name="ponderacion" id="ponderacion" placeholder="20" required>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="fecha_limite">Fecha limite</label>
                                    <input class="form-control" type="date" name="fecha_limite" id="fecha_limite" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-accion">Crear tarea</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Ver tarea -->
            <div class="modal fade" id="modal-ver-tarea" tabindex="-1" aria-labelledby="modal-ver-tarea-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-ver-tarea-label">Tareas asignadas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion_vt">
                                <?php foreach($materias as $materia): ?>
                                    <div class="accordion-item mb-3">                                    
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $materia['id'] ?>_t" aria-expanded="false" aria-controls="<?= $materia['id'] ?>_t">                                                
                                                <div class="text-start">
                                                    <p class="mb-0"><span class="fw-bold">Id:</span> <?= $materia['id'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Materia:</span> <?= $materia['nombre'] ?></p>                                                    
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $materia['id'] ?>_t" class="accordion-collapse collapse" data-bs-parent="#accordion_vt">
                                            <div class="accordion-body">                                                
                                                <?php 
                                                    // Obtenemos a los alumnos que son de una materia en particular
                                                    $tareas = $pdo->query("SELECT
                                                                                id,
                                                                                titulo,
                                                                                descripcion,
                                                                                fecha_limite,
                                                                                ponderacion,
                                                                                fecha_creacion
                                                                                FROM
                                                                                tareas
                                                                                WHERE
                                                                                id_materia = {$materia['id']}")->fetchAll(PDO::FETCH_ASSOC);;
                                                ?>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Id tarea</th>
                                                        <th scope="col">Titulo</th>
                                                        <th scope="col">Descripción</th>
                                                        <th scope="col">Fecha límite</th>
                                                        <th scope="col">Ponderación</th>
                                                        <th scope="col">Fecha creación</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($tareas as $tarea): ?>
                                                        <tr>
                                                            <td> <?= $tarea['id'] ?> </td>
                                                            <td> <?= $tarea['titulo'] ?> </td>
                                                            <td> <?= $tarea['descripcion'] ?> </td>
                                                            <td> <?= $tarea['fecha_limite'] ?> </td>
                                                            <td> <?= $tarea['ponderacion'] ?>%</td>
                                                            <td> <?= $tarea['fecha_creacion'] ?> </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calificar -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Calificar</h5>
                        <p class="card-text text-center">Asigna una calificación a las tareas que tus estudiantes envíen.</p>
                        <button class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-entregas">Ver entregas</button>
                    </div>
                </div>
            </div>

            <!-- Modal Ver tarea -->
            <div class="modal fade" id="modal-entregas" tabindex="-1" aria-labelledby="modal-entregas-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-entregas">Tareas por calificar</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion_t">
                                <?php foreach($materias as $materia): ?>
                                    <div class="accordion-item mb-3">                                    
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $materia['id'] ?>_e" aria-expanded="false" aria-controls="<?= $materia['id'] ?>_e">                                                
                                                <div class="text-start">
                                                    <p class="mb-0"><span class="fw-bold">Id:</span> <?= $materia['id'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Materia:</span> <?= $materia['nombre'] ?></p>                                                    
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $materia['id'] ?>_e" class="accordion-collapse collapse" data-bs-parent="#accordion_t">
                                            <div class="accordion-body">                                                
                                                <?php 
                                                    // Obtenemos a las tareas que han sido entregas por materia
                                                    $entregas = $pdo->query("SELECT
                                                                                CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS estudiante,
                                                                                C.id_tarea,
                                                                                DATE_FORMAT(C.fecha_entrega, '%d/%m/%Y') as fecha_entrega,
                                                                                C.comentarios,
                                                                                T.titulo,
                                                                                T.ponderacion,
                                                                                C.id
                                                                                FROM
                                                                                calificaciones AS C
                                                                                INNER JOIN tareas AS T ON C.id_tarea = T.id
                                                                                INNER JOIN usuarios AS U ON C.id_alumno = U.id
                                                                                WHERE C.esta_entregada = true
                                                                                AND C.esta_calificada = false
                                                                                AND T.id_materia = {$materia['id']}")->fetchAll(PDO::FETCH_ASSOC);
                                                ?>
                                                <ul class="list-group">
                                                    <?php foreach($entregas as $entrega): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div class="ms-2 me-auto">                        
                                                                <div class="text-start">
                                                                    <p class="mb-0 fw-bold"><?= $entrega['titulo'] ?></p>
                                                                    <p class="mb-0"><span class="fw-bold">Estudiante:</span> <?= $entrega['estudiante'] ?></p>
                                                                    <p class="mb-0"><span class="fw-bold">Ponderación:</span> <?= $entrega['ponderacion'] ?>%</p>
                                                                    <p class="mb-0"><span class="fw-bold">Fecha de entrega:</span> <?= $entrega['fecha_entrega'] ?></p>
                                                                    <p class="mb-0"><span class="fw-bold">Comentarios:</span> <?= $entrega['comentarios'] ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3">
                                                                <form action="../../includes/calificar.php" method="POST">
                                                                    <input class="form-control mb-2" type="number" min="0" max="100" name="calificacion" id="calificacion" placeholder="80" required>
                                                                    <button class="btn btn-accion me-2 mt-auto">Calificar</button>
                                                                    <input type="hidden" name="id_calificacion" value="<?= $entrega['id'] ?>">
                                                                </form>
                                                            </div>
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
            <!-- Avance -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Avances</h5>
                        <p class="card-text">Revisa el progreso de tus alumnos de acuerdo a las calificaciones que vayan obteniendo.</p>
                        <button class="btn btn-accion me-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modal-avance">Ver avance</button>
                    </div>
                </div>
            </div> 
            
            <!-- Modal Ver Avance -->
            <div class="modal fade" id="modal-avance" tabindex="-1" aria-labelledby="modal-avance-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-avance">Avance de tus alumnos</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="accordion_a">
                                <?php foreach($materias as $materia): ?>
                                    <div class="accordion-item mb-3">                                    
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $materia['id'] ?>_a" aria-expanded="false" aria-controls="<?= $materia['id'] ?>_a">                                                
                                                <div class="text-start">
                                                    <p class="mb-0"><span class="fw-bold">Id:</span> <?= $materia['id'] ?></p>
                                                    <p class="mb-1"><span class="fw-bold">Materia:</span> <?= $materia['nombre'] ?></p>                                                    
                                                </div>                                               
                                            </button>
                                        </h2>
                                        <div id="<?= $materia['id'] ?>_a" class="accordion-collapse collapse" data-bs-parent="#accordion_a">
                                            <div class="accordion-body">
                                                <?php 
                                                    // Querie para obtener las tareas que el profesor ha asignado por materia
                                                    $tareas = $pdo->query("SELECT
                                                                    id,
                                                                    titulo,
                                                                    ponderacion
                                                                    FROM
                                                                    tareas
                                                                    WHERE
                                                                    id_materia = {$materia['id']}")->fetchAll(PDO::FETCH_ASSOC);
                                                ?>
                                                <ul class="list-group">
                                                    <?php foreach($tareas as $tarea): ?>
                                                    <li class="list-group-item d-grid justify-content-between align-items-center mb-4">                                                                               
                                                        <div class="text-start mb-2">
                                                            <p class="mb-0"><span class="fw-bold">Id tarea:</span> <?= $tarea['id'] ?></p>
                                                            <p class="mb-0"><span class="fw-bold">Titulo:</span> <?= $tarea['titulo'] ?></p>
                                                            <p class="mb-0"><span class="fw-bold">Ponderacion:</span> <?= $tarea['ponderacion'] ?>%</p>
                                                        </div>
                                                        <?php 
                                                            // Querie para obtener la información de las calificaciones por materia
                                                            $calificaciones = $pdo->query("SELECT
                                                                                                CONCAT(U.nombres, ' ', U.a_paterno, ' ', U.a_materno) AS alumno,
                                                                                                C.fecha_entrega,
                                                                                                C.calificacion,
                                                                                                ROUND((C.calificacion * T.ponderacion) / 100, 2) AS puntos_obtenidos,
                                                                                                T.ponderacion
                                                                                                FROM
                                                                                                calificaciones AS C
                                                                                                INNER JOIN usuarios AS U ON C.id_alumno = U.id
                                                                                                INNER JOIN tareas as T ON C.id_tarea = T.id
                                                                                                WHERE
                                                                                                C.id_tarea = {$tarea['id']}")->fetchAll(PDO::FETCH_ASSOC);
                                                         ?>                                                          
                                                         <table class="table">
                                                            <thead>
                                                                <tr>
                                                                <th scope="col">Alumno</th>
                                                                <th scope="col">Fecha de entrega</th>
                                                                <th scope="col">Calificación</th>
                                                                <th scope="col">Puntos obtenidos</th>
                                                                <th scope="col">Ponderación</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($calificaciones as $calificacion): ?>
                                                                    <tr>
                                                                        <td> <?= $calificacion['alumno'] ?> </td>                                                                        
                                                                        <td><?= $calificacion['fecha_entrega'] ?></td>
                                                                        <td><?= $calificacion['calificacion'] ?></td>
                                                                        <td><?= $calificacion['puntos_obtenidos'] ?></td>
                                                                        <td><?= $calificacion['ponderacion'] ?>%</td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
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