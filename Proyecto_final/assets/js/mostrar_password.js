/**
 * Espera a que todo el documento HTML esté cargado antes de ejecutar el código
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Busca TODOS los checkboxes que tengan el atributo [data-toggle-password-for]
    const toggles = document.querySelectorAll('[data-toggle-password-for]');

    // 2. Recorre cada checkbox encontrado
    toggles.forEach(toggle => {

        // 3. Le añade un "escuchador" de eventos 'change' (cuando se marca/desmarca)
        toggle.addEventListener('change', function() {
            
            // 4. Obtiene el valor del atributo 'data-toggle-password-for'
            // (Este valor será el ID del input que queremos controlar)
            const targetId = this.dataset.togglePasswordFor;
            const targetInput = document.getElementById(targetId);

            // Si por error no encuentra el input, no hace nada
            if (!targetInput) {
                console.error('Error: No se encontró el input con id: ' + targetId);
                return;
            }

            // 5. Cambia el tipo del input (la misma lógica de antes)
            if (this.checked) {
                // Si el checkbox está MARCADO, muestra la contraseña
                targetInput.type = 'text';
            } else {
                // Si está DESMARCADO, oculta la contraseña
                targetInput.type = 'password';
            }
        });
    });

});