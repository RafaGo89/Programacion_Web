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
    <footer class="container-fluid d-flex justify-content-between py-2 mt-3 bg-primario">
        <div>
            <span>&copy;Centro Educativo "Integra" 2025</span>
        </div>
        <div>
            <img class="mx-2" src="<?= $ruta_estilos ?>assets/imgs/instagram_logo.png" alt="instagram_logo" width="30px" height="30px">
            <img src="<?= $ruta_estilos ?>assets/imgs/facebook_logo.png" alt="facebook_logo" width="30px" height="30px">
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!-- Solo si se es admin usar estos 2 scripts -->
    <?php if (($_SESSION['id_rol']) == 1): ?>
        <script src="<?= $ruta_estilos ?>assets/js/mostrar_password.js" defer></script>
        <script src="<?= $ruta_estilos ?>assets/js/leer_id_modal.js" defer></script>
    <?php endif; ?>
</body>
</html>