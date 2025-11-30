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
    $titulo = "Administrador"; // Esto cambiará el <title> del header
    $ruta_estilos = "../../";       // Cuántas carpetas hay que subir para llegar a assets
    $ruta_cerrar_sesion = "../../";

    // Variables
    $roles = [];
    $profesores = [];

    try {
        require_once("../../includes/conexion_bd.php");

        // Queries para obtener datos de la base de datos
        $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
        $profesores = $pdo->query("SELECT id, 
                                          CONCAT(nombres, ' ', a_paterno, ' ', a_materno) as nombre
                                    FROM usuarios
                                    WHERE id_rol = 2 AND id_estatus != 4")->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Hubo un error, intentalo de nuevo más tarde.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../../index.php");
        exit;
    }

    // Incluimos el header
    require_once("../../includes/header.php");
?>

    <main class="container-fluid flex-grow-1 text-center my-2">
        <?php
            echo "<h1 class='text-center mb-4 mt-3'>Hola de nuevo, administrador $_SESSION[nombres]</h1>";
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

        <!-- Gestionar usuarios -->
        <div class="row mx-1 justify-content-center">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Usuarios</h5>
                        <p class="card-text">Gestiona los perfiles de los usuarios. Crea nuevas cuentas, edita o elimina perfiles existentes o consulta la lista completa.</p>
                        <div class="d-flex justify-content-center mt-auto">
                            <a class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-crear-usuario">Crear</a>
                            <a href="ver_usuarios.php" class="btn btn-accion">Ver usuarios</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear Usuarios -->
            <div class="modal fade" id="modal-crear-usuario" tabindex="-1" aria-labelledby="modal-crear-usuario-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-crear-usuario-label">Nuevo usuario</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Formulario -->
                            <form action="../../includes/nuevo_usuario.php" method="POST">
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="nombres">Nombres</label>
                                    <input class="form-control" type="text" name="nombres" id="nombres" placeholder="Rafael" required>
                                </div>
                                <div class="row">
                                    <div class="col mb-3 text-start">
                                        <label class="form-label fw-bold fs-5" for="a_paterno">Apellido paterno</label>
                                        <input class="form-control" type="text" name="a_paterno" id="a_paterno" placeholder="Rodríguez" required>
                                    </div>
                                    <div class="col mb-3 text-start">
                                        <label class="form-label fw-bold fs-5" for="a_materno">Apellido materno</label>
                                        <input class="form-control" type="text" name="a_materno" id="a_materno" placeholder="Gómez" required>
                                    </div>
                                </div>
                                 <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="correo">Correo electrónico</label>
                                    <input class="form-control" type="email" name="correo" id="correo" placeholder="nombre@ejemplo.com" required>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="contrasena">Contraseña</label>
                                    <input class="form-control" type="password" name="contrasena" id="contrasena" required>
                                </div>

                                <div class="form-check mb-3 text-start">
                                    <input class="form-check-input" type="checkbox" data-toggle-password-for="contrasena" id="mostrar_pass">
                                    <label class="form-check-label" for="mostrar_pass">
                                        Mostrar contraseña
                                    </label>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="rol">Tipo de cuenta</label>
                                    <select class="form-select" name="rol" id="rol" aria-label="Default select example" required>
                                        <option selected disabled value="">Escoga un tipo de cuenta</option>
                                        <?php foreach($roles as $rol): ?>                                            
                                            <option value= "<?= $rol['id'] ?>">
                                                <?= $rol['id'] . "- " . $rol['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <input class="btn btn-accion mb-3" type="submit" value="Crear usuario">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Ver Usuarios-->
            <div class="modal fade" id="modal-ver-estudiantes" tabindex="-1" aria-labelledby="modal-ver-estudiantes-label" aria-hidden="true">
                <div class="modal-dialog modal-lg  modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-ver-estudiantes-label">Estudiantes</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-secundario">
                                    <div>
                                        <p class="my-1 text-start"><span class="fw-bold">Estudiante:</span> Rafael Rodríguez</p>
                                        <p class="text-start"><span class="fw-bold">id:</span> 1</p>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-accion w-100">Editar</button>
                                        <button type="button" class="btn btn-danger w-100">Eliminar</button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestionar materias -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body bg-secundario d-flex flex-column">
                        <h5 class="card-title text-center">Materias</h5>
                        <p class="card-text">Administra el catálogo de materias. Crea nuevas asignaturas, asígnalas a profesores, eliminalas o edita el plan de estudios.</p>
                        <div class="d-flex justify-content-center mt-auto">
                            <a class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-crear-materia">Crear</a>
                            <a href="ver_materias.php" class="btn btn-accion">Ver materias</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear materias -->
            <div class="modal fade" id="modal-crear-materia" tabindex="-1" aria-labelledby="modal-crear-materia-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-crear-materia-label">Nueva materia</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../../includes/nueva_materia.php" method="POST">
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="nombre_materia">Nombre de la materia</label>
                                    <input class="form-control" type="text" name="nombre_materia" id="nombre_materia" placeholder="Estadística I" required>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="profesor">Profesor</label>
                                    <select class="form-select" name="id_profesor" id="profesor"  aria-label="Default select example" required>
                                        <option selected disabled value="" >Escoga un profesor</option>
                                        <?php foreach($profesores as $profesor): ?>                                            
                                            <option value= "<?= $profesor['id'] ?>">
                                                <?= $profesor['id'] . "- " . $profesor['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold fs-5" for="descripcion">Descripción</label>
                                    <textarea class="form-control" name="descripcion" for="descripcion" placeholder="Escriba la descripción de su materia" id="descripcion" style="height: 200px" required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-accion">Crear materia</a>
                                </div>
                            </form>
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