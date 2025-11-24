/**
 * Código JS para pasar el id del usuario que se quiere eliminar al modal
 * de confirmación
 */
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Obtenemos el modal por su ID
    var modalEliminar = document.getElementById('modal-eliminar-usuario');
    
    // 2. Verificamos que el modal exista para evitar errores en otras páginas
    if (modalEliminar) {
        
        // 3. Escuchamos cuando se abre el modal
        modalEliminar.addEventListener('show.bs.modal', function (event) {
            
            // Botón que activó el modal (el de la tabla)
            var boton = event.relatedTarget;
            
            // Extraemos la información del atributo data-id
            var idUsuario = boton.getAttribute('data-id');
            
            // Buscamos el botón de confirmación DENTRO del modal
            var botonConfirmar = document.getElementById('btn-confirmar-eliminar');
            
            // Actualizamos su href con el ID correcto
            if (botonConfirmar) {
                botonConfirmar.href += '?id=' + idUsuario;
            }
        });
    }
});