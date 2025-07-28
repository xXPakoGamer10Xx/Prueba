document.addEventListener('DOMContentLoaded', function () {
    // Lógica para el Modal de Editar
    const modalEditar = document.getElementById('modalEditar');
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            
            // Llenar el formulario con los datos del paciente
            document.getElementById('formEditar').action = url;
            document.getElementById('edit_id_paciente').value = button.getAttribute('data-id');
            document.getElementById('edit_nombre_paciente').value = button.getAttribute('data-nombre');
            document.getElementById('edit_apellido1_paciente').value = button.getAttribute('data-apellido1');
            document.getElementById('edit_apellido2_paciente').value = button.getAttribute('data-apellido2');
            document.getElementById('edit_fecha_nac').value = button.getAttribute('data-fecha_nac');
            document.getElementById('edit_genero_paciente').value = button.getAttribute('data-genero');
        });
    }

    // Lógica para el Modal de Eliminar
    const modalEliminar = document.getElementById('modalEliminar');
    if (modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            document.getElementById('formEliminar').action = url;
        });
    }
});