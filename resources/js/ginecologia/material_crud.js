// Este evento asegura que el script se ejecute solo cuando todo el HTML esté cargado.
document.addEventListener('DOMContentLoaded', function () {

    // --- Lógica para el Modal de Editar ---
    const modalEditar = document.getElementById('modalEditar');

    // Se comprueba si el modal existe en la página antes de añadir el listener.
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            // El botón que abrió el modal
            const button = event.relatedTarget; 

            // Extraemos la información de los atributos data-* del botón
            const url = button.getAttribute('data-url');
            const nombre = button.getAttribute('data-nombre');
            const cantidad = button.getAttribute('data-cantidad');

            // Obtenemos los elementos del formulario del modal
            const form = document.getElementById('formEditar');
            const inputNombre = document.getElementById('edit_nombre');
            const inputCantidad = document.getElementById('edit_cantidad');

            // Actualizamos el formulario con los datos del material
            form.action = url; // Asigna la URL para el envío (update)
            inputNombre.value = nombre;
            inputCantidad.value = cantidad;
        });
    }

    // --- Lógica para el Modal de Eliminar ---
    const modalEliminar = document.getElementById('modalEliminar');

    if (modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            const form = document.getElementById('formEliminar');
            
            // Solo necesitamos actualizar la URL de acción para este formulario
            form.action = url;
        });
    }
    
    // CAMBIO APLICADO AQUÍ: El bloque del buscador fue eliminado.
});