<?php
    session_start();                   
    // Inicia la sesión o reanuda una ya existente

    if (!isset($_SESSION['usuario_id'])) {  
        // Revisa si NO existe la variable de sesión "usuario_id"

        header("Location: ./");        
        // Si no está logueado, lo manda a la página principal (login)

        exit;                          
        // Detiene la ejecución del código
    }

    // Simulación de sistema de roles
    $rol = $_SESSION['rol_id'];        
    // Obtiene el rol del usuario desde la sesión

    if ($rol == 1) {                   
        $rol_texto = "Administrador";  // Si rol es 1, es Administrador
    } elseif ($rol == 2) {             
        $rol_texto = "Profesor";       // Si rol es 2, es Profesor
    } elseif ($rol == 3) {             
        $rol_texto = "Estudiante";     // Si rol es 3, es Estudiante
    } elseif ($rol == 4) {             
        $rol_texto = "Invitado";       // Si rol es 4, es Invitado
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
</head>
<body>
    <h2>
        Bienvenido, 
        <?php 
            // Muestra el nombre completo guardado en la sesión,
            // usando htmlspecialchars para evitar inyección de código (XSS)
            echo htmlspecialchars($_SESSION['nombre_completo']); 
        ?> 
    </h2>

    <p>
        Tu rol en el sistema es: 
        <strong>
            <?php 
                // Imprime el texto del rol (Administrador, Profesor, etc.)
                // usando la sintaxis corta de echo
                echo $rol_texto;  
            ?>
        </strong>
    </p>
    
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>