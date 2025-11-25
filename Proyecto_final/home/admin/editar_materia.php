<?php
    session_start();

    // Si no se ha inciado sesión y no se es admin
    if (!isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != 1) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }

    // Variables
    $profesores = [];
    $estatuses = [];

    try {
        require_once("../../includes/conexion_bd.php");

        // Queries para obtener datos de la base de datos
        $profesores = $pdo->query("SELECT id, 
                              CONCAT(nombres, ' ', a_paterno, ' ', a_materno) as nombre
                              FROM usuarios
                              WHERE id_rol = 2 AND id_estatus != 4")->fetchAll(PDO::FETCH_ASSOC);
        $estatuses = $pdo->query("SELECT id, estado FROM estatus")->fetchAll(PDO::FETCH_ASSOC);

        // Si se ha enviado el formulario con método POST, procesamos la edición.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // PRIMER CASO DE ERROR: Campos vacíos
            if (empty($_POST["nombre_materia"]) || empty($_POST["id_profesor"]) || empty($_POST["descripcion"])
                || empty($_POST["estatus"])) {
                $message = "<div class='alert alert-warning mt-2' role='alert'>
                        No se pueden dejar campos vacíos
                        </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: editar_materia.php?id=" . $_POST['id']);
                exit; 
            }

            // Obtenemos los datos del formulario
            $id = $_POST['id'];
            $nombre_materia = trim($_POST["nombre_materia"]);
            $id_profesor = $_POST["id_profesor"];
            $descripcion = trim($_POST["descripcion"]);
            $id_estatus = $_POST['estatus'];
            
            // Si no hay conflicto, actualizamos los datos de la materia         
            $sql = "UPDATE materias SET 
                           nombre = :nombre_materia,
                           descripcion = :descripcion,
                           id_profesor = :id_profesor,
                           id_estatus = :id_estatus,                        
                           fecha_modificacion = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre_materia' => $nombre_materia,
                ':descripcion'    => $descripcion,
                ':id_profesor'    => $id_profesor,
                ':id_estatus'     => $id_estatus,
                ':id'             => $id 
            ]);

            // Redirigimos para evitar reenvío del formulario al refrescar
            header("Location: editar_materia.php?id=$id&actualizado=1");
            exit;

            // Si hubo error, recargamos los datos del usuario para mostrarlos
            // Preparamos la querie para traer los datos del usuario
            $stmt = $pdo->prepare("SELECT id, nombre,
                                          descripcion, 
                                          id_profesor      
                                        FROM materias
                                        WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $materia = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // Si obtuvimos una petición GET
        elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
            // Convertimos el valor recibido a entero por seguridad
            $id = intval($_GET['id']);

            // Preparamos la querie para traer los datos del usuario
            $stmt = $pdo->prepare("SELECT id, nombre,
                                          descripcion, 
                                          id_profesor,
                                          id_estatus      
                                        FROM materias
                                        WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $materia = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no se encontró una materia con ese ID
            if(!$materia) {
                $message = "<div class='alert alert-danger mt-2' role='alert'>
                            No se encontró ninguna materia con ese Id.
                            </div>";

                $_SESSION['mensaje'] = $message;
                header("Location: ver_materias.php");
                exit;
            }
        }
        else {
            // Si no se envío un ID válido mandamos un mensaje
            $message = "<div class='alert alert-warning mt-2' role='alert'>
                            Id enviado no válido.
                            </div>";

            $_SESSION['mensaje'] = $message;
            header("Location: ver_materias.php");
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
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ver_materias.php");
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
    <title>Editar Materias</title>
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
                    <h2>Editar Materia</h2>
            </div>
            <form class="px-3 py-2" action="editar_materia.php" method="POST">     
                <?php
                    // Mostrar mensaje de error sí lo hay
                    if (isset($_SESSION['mensaje'])) {

                    echo $_SESSION['mensaje'];

                    // Borrar el mensaje una vez se muestra
                    unset($_SESSION['mensaje']);
                    }
                ?>
                
                <!-- ID oculto para saber qué usuario estamos editando -->
                <input type="hidden" name="id" value="<?= $materia['id'] ?>">
                       
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="nombre_materia">Nombre de la materia</label>
                    <input class="form-control" value="<?= htmlspecialchars($materia['nombre']) ?>" type="text" name="nombre_materia" id="nombre_materia" placeholder="Estadística I" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="profesor">Profesor</label>
                    <select class="form-select" name="id_profesor" id="profesor"  aria-label="Default select example" required>                        
                        <?php foreach($profesores as $profesor): ?>                                            
                            <option value= "<?= $profesor['id'] ?>" <?= $profesor['id'] ==$materia['id_profesor'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($profesor['id'] . "- " . $profesor['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="estatus">Estatus</label>
                    <select class="form-select" name="estatus" id="estatus" aria-label="Default select example" required>                    
                        <?php foreach($estatuses as $estatus): ?>                                            
                            <option value= "<?= $estatus['id'] ?>" <?= $estatus['id'] == $materia['id_estatus'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estatus['id'] . "- " . $estatus['estado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="descripcion">Descripción</label>
                    <textarea class="form-control text-start" name="descripcion" for="descripcion" placeholder="Escriba la descripción de su materia" id="descripcion" style="height: 200px" required><?= htmlspecialchars($materia['descripcion']) ?></textarea>
                </div>

                <div class="d-flex justify-content-center mt-auto">
                    <a href="ver_materias.php" class="btn btn-secondary mb-3 fs-5 me-3">Cancelar</a>
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