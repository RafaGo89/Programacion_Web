<?php
    session_start();

    // Si no se ha inciado sesión y no se es profesor
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }
    date_default_timezone_set('America/Mexico_City');
    $fecha_actual = date('Y-m-d H:i:s');

    // DEFINICIÓN DE VARIABLES PARA LA VISTA
    $titulo = "Editar tarea"; // Esto cambiará el <title> del header
    $ruta_estilos = "../../";       // Cuántas carpetas hay que subir para llegar a assets
    $ruta_cerrar_sesion = "../../";

    // Variables
    $materias = [];

    try {
        require_once("../../includes/conexion_bd.php");

        $id_profesor = $_SESSION['id_usuario'];

        // Queries para obtener datos de la base de datos
        $materias = $pdo->query("SELECT id, nombre, 
                                        DATE_FORMAT(fecha_creacion, '%d/%m/%Y') as fecha_creacion
                                 FROM materias
                                 WHERE id_estatus NOT IN (4, 3) AND
                                       id_profesor = " . $id_profesor)->fetchAll(PDO::FETCH_ASSOC);

        // Si se ha enviado el formulario con método POST, procesamos la edición.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. CAPTURAR EL ID DE LA TAREA PRIMERO (Para poder redirigir si hay error)
            $id_tarea_post = isset($_POST["id_tarea"]) ? $_POST["id_tarea"] : null;

            if (!$id_tarea_post) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Error crítico: No se recibió el ID de la tarea.
                            </div>";
                $_SESSION['mensaje'] = $message;
                header("Location: index.php");
                exit;
            }

            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["titulo"]) || empty($_POST["id_materia"]) || empty($_POST["descripcion"]) ||
                empty($_POST["ponderacion"]) || empty($_POST["fecha_limite"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            No se pueden dejar campos vacíos
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_tarea.php?id=" . $id_tarea_post);
                exit; 
            }

            // Validación de formato de ponderacion
            if ($_POST['ponderacion'] > 100 || $_POST['ponderacion'] < 1) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Ponderacion no válida
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_tarea.php?=id" . $id_tarea_post);
                exit; 
            }

            // Validación de fecha
            // 1. Configurar zona horaria (CRUCIAL para que "hoy" sea hoy en tu país)
            date_default_timezone_set('America/Mexico_City');

            $fecha_post = $_POST["fecha_limite"];
            
            // Creamos un objeto fecha a partir del formato esperado (Y-m-d)            
            $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_post);

            // Validación de formato de fecha
            if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha_post) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Formato de fecha no válido
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_tarea.php?id=". $id_tarea_post);
                exit; 
            }

            // Query para buscar que sí existe una tarea con ese id
            $check = $pdo->prepare("SELECT id FROM tareas WHERE id = :id");
            $check->execute([':id' => $id_tarea_post]);

            if ($check->rowCount() == 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id de tarea no válido.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: index.php");
                exit; 
            }

            // Verificamos que no se haya superado los 100 puntos de ponderación
            $sql = "SELECT SUM(ponderacion) FROM tareas WHERE id_materia = {$_POST["id_materia"]} AND id != {$id_tarea_post}";

            $stmt = $pdo->query($sql);

            $suma_ponderacion = $stmt->fetchColumn();

            // Si devuelve NULL (primera tarea), lo convertimos a 0
            $suma_ponderacion = $suma_ponderacion ? $suma_ponderacion : 0;

            // El límite de los porcentajes de la ponderación es 100
            if ($suma_ponderacion + $_POST["ponderacion"] > 100) {
                $restante = 100 - $suma_ponderacion;
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            No se puede superar el límite de ponderación de 100%. Restante {$restante}%. 
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_tarea.php?id=" . $id_tarea_post);
                exit; 
            }

            // --- Si no están vacíos y se cumple el formato de los datos, continuamos ---
            $id_tarea  = $_POST["id_tarea"];
            $titulo = trim($_POST["titulo"]);
            $materia = $_POST["id_materia"];
            $descripcion = trim($_POST["descripcion"]);
            $ponderacion = $_POST["ponderacion"];
            $fecha_limite = $_POST["fecha_limite"];
            
            // Si no hay conflicto, actualizamos los datos de la tarea         
            $sql = "UPDATE tareas SET 
                           titulo = :titulo,
                           id_materia = :id_materia,
                           descripcion = :descripcion,
                           fecha_limite = :fecha_limite,
                           ponderacion = :ponderacion,                        
                           fecha_modificacion = :fecha_modificacion
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo'          => $titulo,
                ':id_materia'      => $materia,
                ':descripcion'     => $descripcion,
                ':fecha_limite'    => $fecha_limite,
                ':ponderacion'     => $ponderacion,
                ':id'              => $id_tarea_post,
                'fecha_modificacion' => $fecha_actual 
            ]);

            // Redirigimos para evitar reenvío del formulario al refrescar
            header("Location: editar_tarea.php?id=$id_tarea_post&actualizado=1");
            exit;

            // Si hubo error, recargamos los datos de la tarea para mostrarlos
            // Preparamos la querie para traer los datos de la tarea
            $stmt = $pdo->prepare("SELECT id, titulo,
                                          id_materia,
                                          descripcion, 
                                          fecha_limite,
                                          ponderacion      
                                        FROM tareas
                                        WHERE id = :id");
            $stmt->execute([':id' => $id_tarea]);
            $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

        }
        // Si obtuvimos una petición GET
        elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
            // Convertimos el valor recibido a entero por seguridad
            $id = intval($_GET['id']);

            // Preparamos la querie para traer los datos de la tarea
            $stmt = $pdo->prepare("SELECT id, titulo,
                                          id_materia,
                                          descripcion, 
                                          fecha_limite,
                                          ponderacion      
                                        FROM tareas
                                        WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no se encontró una materia con ese ID
            if(!$tarea) {
                $message = "<div class='alert alert-danger mt-2' role='alert'>
                            No se encontró ninguna materia con ese Id.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: index.php");
                exit;
            }
        }
        else {
            // Si no se envío un ID válido mandamos un mensaje
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id enviado no válido.
                            </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: index.php");
            exit;
        }

        // Mensaje de éxito si venimos de la redirección después de guardar cambios
        if (isset($_GET['actualizado']) && $_GET['actualizado'] == 1) {
            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Materia actualizada correctamente.
                        </div>";
                            
            $_SESSION['mensaje'] = $message;
        }
    }
    catch (PDOException $e) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    {$e}
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: index.php");
        exit;
    }

    // Incluimos el header
    require_once("../../includes/header.php");
?>

    <main class="container flex-grow-1 my-2 d-flex justify-content-center align-items-center">
        <div class="bg-secundario rounded shadow-lg mt-3">
            <div class="container-fluid mb-2 text-center bg-primario p-2 rounded-top">
                    <h2>Editar Tarea</h2>
            </div>
            <form class="px-3 py-2" action="editar_tarea.php" method="POST">     
                <?php
                    // Mostrar mensaje de error sí lo hay
                    if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                    }
                ?>
                
                <!-- ID oculto para saber qué tarea estamos editando -->
                <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>">
                       
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="titulo">Titulo de la tarea</label>
                    <input class="form-control" value="<?= htmlspecialchars($tarea['titulo']) ?>" type="text" name="titulo" id="titulo" placeholder="Tarea #1" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="id_materia">Materia</label>
                    <select class="form-select" name="id_materia" id="id_materia"  aria-label="Default select example" required>                        
                        <?php foreach($materias as $materia): ?>                                            
                            <option value= "<?= $materia['id'] ?>" <?= $materia['id'] ==$tarea['id_materia'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($materia['id'] . "- " . $materia['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="descripcion">Descripción</label>
                    <textarea class="form-control text-start" name="descripcion" for="descripcion" placeholder="Escriba la descripción de su materia" id="descripcion" style="height: 200px" required><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="ponderacion">Ponderación</label>
                    <input class="form-control" value="<?= htmlspecialchars($tarea['ponderacion']) ?>" type="number" min="0" max="100" name="ponderacion" id="ponderacion" placeholder="20" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="fecha_limite">Ponderación</label>
                    <input class="form-control" value="<?= htmlspecialchars($tarea['fecha_limite']) ?>" type="date" name="fecha_limite" id="fecha_limite" required>
                </div>

                <div class="d-flex justify-content-center mt-auto">
                    <a href="index.php" class="btn btn-secondary mb-3 fs-5 me-3">Cancelar</a>
                    <input class="btn btn-accion mb-3 fs-5" type="submit" value="Guardar cambios">
                </div>
            </form>
        </div>
    </main>

<?php 
    // Incluimos el footer
    require_once("../../includes/footer.php");
?>