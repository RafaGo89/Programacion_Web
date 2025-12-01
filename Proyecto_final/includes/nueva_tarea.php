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
        require "conexion_bd.php";

        // Asegurarnos que se envío algo
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["titulo"]) || empty($_POST["materia"]) || empty($_POST["descripcion"]) ||
                empty($_POST["ponderacion"]) || empty($_POST["fecha_limite"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        No se pueden dejar campos vacíos
                        </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/profesor/index.php");
                    exit; 
            }

            // Validación de formato de ponderacion
            if ($_POST['ponderacion'] > 100 || $_POST['ponderacion'] < 1) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Ponderacion no válida
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }

            // Validación de fecha
            // 1. Configurar zona horaria (CRUCIAL para que "hoy" sea hoy en mi país)
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
                header("Location: ../home/profesor/index.php");
                exit; 
            }
            
            // 2. Limpiamos la hora de la fecha elegida (00:00:00)
            $fecha_obj->setTime(0, 0, 0);
                
            // 3. Creamos "Hoy" con la hora limpia (00:00:00)
            $hoy = new DateTime();
            $hoy->setTime(0, 0, 0); 

            if ($fecha_obj < $hoy) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Esa fecha ya pasó.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }

            // Query para buscar que sí existe una materia con ese id
            $check = $pdo->prepare("SELECT id FROM materias WHERE id = :id");
            $check->execute([':id' => $_POST["materia"]]);

            if ($check->rowCount() == 0) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id de materia no válido.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }

            // Verificamos que no se haya superado los 100 puntos de ponderación
            $sql = "SELECT SUM(ponderacion) FROM tareas WHERE id_materia = {$_POST["materia"]}";

            $stmt = $pdo->query($sql);

            $suma_ponderacion = $stmt->fetchColumn();

            $restante = 100 - $suma_ponderacion;

            // El límite de los porcentajes de la ponderación es 100
            if ($suma_ponderacion + $_POST["ponderacion"] > 100) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                            No se puede superar el límite de ponderación de 100%. Restante {$restante}%. 
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ../home/profesor/index.php");
                exit; 
            }

            // --- Si no están vacíos y se cumple el formato de los datos, continuamos ---
            $titulo = trim($_POST["titulo"]);
            $materia = $_POST["materia"];
            $descripcion = trim($_POST["descripcion"]);
            $ponderacion = $_POST["ponderacion"];
            $fecha_limite = $_POST["fecha_limite"];

            // Creamos consulta para insertar la nueva tarea
            $sql_insertar = "INSERT INTO tareas (id_materia, titulo, descripcion,
                                                fecha_limite, ponderacion, fecha_creacion)
                            VALUES (:id_materia, :titulo, :descripcion, :fecha_limite,
                                    :ponderacion, NOW())";

            // Preparar consulta de inserción
            $stmt_insertar = $pdo->prepare($sql_insertar);

            $resultado = $stmt_insertar->execute([
                ':id_materia'   => $materia,
                ':titulo'       => $titulo,
                ':descripcion'  => $descripcion,
                ':fecha_limite' => $fecha_limite,
                ':ponderacion'  => $ponderacion
            ]);

            // 1. Obtenemos el ID directo de la inserción anterior
            $id_tarea = $pdo->lastInsertId();

            // Si la inserción tuvo éxito creamos las filas donde irán las calificaciones de los
            // estudiantes y regresamos al login
            if ($resultado) {
                $alumnos = $pdo->query("SELECT DISTINCT id_alumno FROM solicitudes
                                         WHERE id_materia = {$materia} AND estado = 'Aprobado'")->fetchAll(PDO::FETCH_ASSOC);
                
                // Creamos tantas tareas como alumnos haya
                foreach ($alumnos as $alumno) {
                    // Creamos consulta para insertar la nueva tarea
                    $sql_insertar = "INSERT INTO calificaciones (id_tarea, id_alumno)
                                     VALUES (:id_tarea, :id_alumno)";

                    // Preparar consulta de inserción
                    $stmt_insertar = $pdo->prepare($sql_insertar);

                    $resultado = $stmt_insertar->execute([
                        ':id_tarea'   => $id_tarea,
                        ':id_alumno'    => $alumno['id_alumno']
                    ]);
                }

                $message = "<div class='alert alert-success mt-2' role='alert'>
                            Tarea creada con éxito!
                            </div>";

                    $_SESSION['mensaje'] = $message;
                    header("Location: ../home/profesor/index.php");
                    exit; 
            }

            $message = "<div class='alert alert-success mt-2' role='alert'>
                        Error al crear la tarea. Inténtalo de nuevo.
                        </div>";

            // Si hubo algún error en la base de datos
            $_SESSION['mensaje'] = $message;
            header("Location: ../home/profesor/index.php");
            exit;
        }
        else {
            $message = "<div class='alert alert-danger mt-2' role='alert'>
                        Acceso no autorizado
                        </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ../index.php");
            exit;
        }
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../home/profesor/index.php");
        exit;
    }