<?php
    // Si no se ha inciado sesión
    if (!isset($_SESSION['id_usuario'])) {
        $message = "<div class='alert alert-warning mt-2' role='alert'>
                    Acceso no autorizado.
                    </div>";

        $_SESSION['mensaje'] = $message;
        header("Location: ../index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="<?= $ruta_estilos ?>assets/imgs/favicon.png" type="image/png" sizes="48x48">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $ruta_estilos ?>assets/css/estilos_panel.css">

    <title><?= $titulo ?? 'Centro Educativo integra' ?></title>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="container-fluid d-flex justify-content-between py-2 bg-primario">  
        <div class="d-flex">
            <img src="<?= $ruta_estilos ?>assets/imgs/logo.png" alt="logo_escuela" width="60px" height="60px">
        </div>

        <div class="d-flex align-items-center">
            <a href="<?= $ruta_cerrar_sesion ?>cerrar_sesion.php" class="pe-2 fw-bold">Cerrar sesión</a>
            <img class="perfil" src="<?= $ruta_estilos ?>assets/imgs/usuario_foto.png" alt="logo_escuela" width="50px" height="50px" data-bs-toggle="modal" data-bs-target="#modal-ver-perfil">
        </div>  
    </header>

<!-- Modal Ver perfil -->
<div class="modal fade" id="modal-ver-perfil" tabindex="-1" aria-labelledby="modal-ver-perfil-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primario">
                <h1 class="modal-title fs-3" id="modal-ver-perfil-label">Perfil</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="nombres">Nombres</label>
                    <input disabled class="form-control" name="nombres" id="nombres" value="<?= $_SESSION['nombres'] ?>" required>
                </div>
                <div class="row">
                    <div class="col mb-3 text-start">
                        <label class="form-label fw-bold fs-5" for="a_paterno">Apellido paterno</label>
                        <input disabled class="form-control" name="a_paterno" id="a_paterno" value="<?= $_SESSION['a_paterno'] ?>" required>
                    </div>
                    <div class="col mb-3 text-start">
                        <label class="form-label fw-bold fs-5" for="a_materno">Apellido materno</label>
                        <input disabled class="form-control" name="a_materno" id="a_materno" value="<?= $_SESSION['a_materno'] ?>" required>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="correo">Correo electrónico</label>
                    <input disabled class="form-control" name="a_materno" id="a_materno" value="<?= $_SESSION['correo'] ?>" required>
                </div>       
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold fs-5" for="rol">Rol</label>
                    <?php 
                        if ($_SESSION['id_rol'] == '1') {
                            $rol_nombre = 'Administrador';
                        }
                        elseif ($_SESSION['id_rol'] == '2') {
                            $rol_nombre = 'Profesor';
                        }
                        elseif ($_SESSION['id_rol'] == '3') {
                            $rol_nombre = 'Alumno';
                        }
                        else {
                            $rol_nombre = 'Invitado';
                        }
                    ?>
                    <input disabled class="form-control" name="rol" id="rol" value="<?= $rol_nombre ?>" required>
                </div>          
            </div>
        </div>
    </div>
</div>