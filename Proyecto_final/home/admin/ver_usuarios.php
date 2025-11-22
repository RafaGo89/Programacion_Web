<?php
    session_start();

    require_once("../../includes/conexion_bd.php");

    // Variables
    $usuarios = [];

    // Queries para obtener datos de la base de datos
    $usuarios = $pdo->query("SELECT U.id, U.nombres,
                                    U.a_paterno, U.a_materno,
                                    U.correo, R.nombre AS rol,
                                    E.estado, U.fecha_creacion,
                                    U.fecha_modificacion
                                FROM usuarios as U
                                INNER JOIN roles AS R
                                ON U.id_rol = R.id
                                INNER JOIN estatus AS E
                                ON U.id_estatus = E.id;")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/imgs/favicon.png" type="image/png" sizes="48x48">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/estilos_panel.css">
    <title>Usuarios</title>
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
        <h1 class="text-center mb-4 mt-3">Lista de usuarios</h1>
        <hr>

        <table class="table table-striped table-hover align-middle">
            <caption>Lista de usuarios</caption>
            <thead>
                <tr class="align-middle">
                    <th scope="col">Id</th>
                    <th scope="col">Nombres</th>
                    <th scope="col">A. paterno</th>
                    <th scope="col">A. materno</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Cuenta creada</th>
                    <th scope="col">Cuenta modificada</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php  foreach ($usuarios as $usuario):?>
                    <tr>
                        <th scope="row"><?= $usuario['id'] ?></th>
                        <td><?= $usuario['nombres'] ?></td>
                        <td><?= $usuario['a_paterno'] ?></td>
                        <td><?= $usuario['a_materno'] ?></td>
                        <td><?= $usuario['correo'] ?></td>
                        <td><?= $usuario['rol'] ?></td>
                        <td><?= $usuario['estado'] ?></td>
                        <td><?= $usuario['fecha_creacion'] ?></td>
                        <td><?= $usuario['fecha_modificacion'] ?></td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-accion me-2">Editar</button>
                                <button type="button" class="btn btn-danger">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>            
        </table>
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