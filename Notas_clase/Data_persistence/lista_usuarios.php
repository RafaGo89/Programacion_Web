<?php
    /*
    * Recuerda:
    *   Activamos el manejo de sesiones: session_start();
    * Ademas:
    *   Es necesario verificamos si el usuario ha iniciado sesión correctamente.
    *   Si no existe la variable de sesión 'usuario_id', lo redirigimos al inicio.
    *   Esto evita que alguien sin permisos acceda directamente al script por la URL.
    */

    // Mostrar errores (solo en entorno de desarrollo)
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Importamos el archivo de conexión a la base de datos (PDO)    
    require_once("conexion.php");

    /*
    * Este script muestra una lista de usuarios.
    * Si se envía un parámetro de búsqueda (GET['busqueda']), 
    * se filtra por nombre o correo electrónico.
    */


    // Verificamos si hay una búsqueda (GET), si no, se deja como cadena vacía
    $busqueda = $_GET['busqueda'] ?? '';

    if ($busqueda !== '') {
        // Si hay un valor de búsqueda, preparamos una consulta que lo utilice
        // Se utiliza LIKE con % para encontrar coincidencias parciales en nombres o correos        
        $sql = "SELECT 
                    u.id,
                    u.nombres AS nombre_usuario,
                    u.a_paterno,
                    u.a_materno,
                    u.correo,
                    r.nombre AS rol,
                    e.descripcion AS estatus,
                    u.fecha_creacion,
                    u.fecha_actualizacion
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                JOIN estatus e ON u.estatus_id = e.id
                WHERE u.nombres LIKE :busqueda OR u.correo LIKE :busqueda";
    
        // Preparamos y ejecutamos la consulta con un valor escapado de forma segura    
        $stmt = $conn->prepare($sql);
        $stmt->execute([':busqueda' => "%$busqueda%"]);
    } else {

        // Si no hay búsqueda, simplemente seleccionamos todos los usuarios        
        $sql = "SELECT 
                    u.id,
                    u.nombres AS nombre_usuario,
                    u.a_paterno,
                    u.a_materno,
                    u.correo,
                    r.nombre AS rol,
                    e.descripcion AS estatus,
                    u.fecha_creacion,
                    u.fecha_actualizacion
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                JOIN estatus e ON u.estatus_id = e.id";
    
        // Ejecutamos la consulta directamente (sin parámetros)
        $stmt = $conn->query($sql);
    }

    // Obtenemos todos los resultados en un arreglo asociativo    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Usuarios</title>
  <style>
    /* Estilos básicos para la tabla */
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background-color: #eee; }
    /* Estilo para botones de acción */
    a.boton { padding: 4px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    a.boton.eliminar { background: #dc3545; }
  </style>
</head>
<body>
  <h2>Usuarios Registrados</h2>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="lista_usuarios.php">
    <label>Buscar por nombre o correo:</label>

    <!-- Mantenemos el valor ingresado después de hacer la búsqueda -->    
    <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
    <button type="submit">Buscar</button>
    </form>
    <br>

    <!-- Tabla de resultados -->
    <table>
        <thead>
        <tr>
            <th>Nombres</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estatus</th>
            <th>Creado</th>
            <th>Actualizado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        
        <tbody>
            <!-- Recorremos cada usuario y generamos una fila por cada uno -->
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                <td><?= $usuario['nombre_usuario'] ?></td>
                <td><?= $usuario['a_paterno'] ?></td>
                <td><?= $usuario['a_materno'] ?></td>
                <td><?= $usuario['correo'] ?></td>
                <td><?= $usuario['rol'] ?></td>
                <td><?= $usuario['estatus'] ?></td>
                <td><?= $usuario['fecha_creacion'] ?></td>
                <td><?= $usuario['fecha_actualizacion'] ?></td>
                <td>
                    <!-- Botón para editar, enviando el ID del usuario por GET -->
                    <a class="boton" href="editar_usuario.php?id=<?= $usuario['id'] ?>">Editar</a>
                    <!-- Botón para eliminar con confirmación -->
                    <a class="boton eliminar" href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">Eliminar</a>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>