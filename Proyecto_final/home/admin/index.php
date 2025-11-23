<?php
    session_start();

    require_once("../../includes/conexion_bd.php");

    // Variables
    $roles = [];
    $profesores = [];

    // Queries para obtener datos de la base de datos
    $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
    $profesores = $pdo->query("SELECT id, 
                              CONCAT(nombres, ' ', a_paterno, ' ', a_materno) as nombre
                              FROM usuarios
                              WHERE id_rol = 2")->fetchAll(PDO::FETCH_ASSOC);
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
                            <a class="btn btn-accion me-2" data-bs-toggle="modal" data-bs-target="#modal-crear-estudiante">Crear</a>
                            <a href="ver_usuarios.php" class="btn btn-accion">Ver usuarios</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear Usuarios -->
            <div class="modal fade" id="modal-crear-estudiante" tabindex="-1" aria-labelledby="modal-crear-estudiante-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primario">
                            <h1 class="modal-title fs-3" id="modal-crear-estudiante-label">Nuevo usuario</h1>
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
                                    <select class="form-select" name="rol" for="rol" id="rol" aria-label="Default select example" required>
                                        <option value="" selected disable>Escoga un tipo de cuenta</option>
                                        <?php foreach($roles as $rol): ?>                                            
                                            <option value= "<?= $rol['id'] ?>">
                                                <?= htmlspecialchars($rol['id'] . "- " . $rol['nombre']) ?>
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
                                    <select class="form-select" name="id_profesor" id="profesor" for="profesor" aria-label="Default select example" required>
                                        <option selected disabled value="" >Escoga un profesor</option>
                                        <?php foreach($profesores as $profesor): ?>                                            
                                            <option value= "<?= $profesor['id'] ?>">
                                                <?= htmlspecialchars($profesor['id'] . "- " . $profesor['nombre']) ?>
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

    <script src="../../assets/js/mostrar_password.js" defer></script>
</body>
</html>