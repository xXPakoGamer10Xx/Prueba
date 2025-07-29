document.addEventListener('DOMContentLoaded', function () {

    // --- LÓGICA PARA CIRUGÍA GENERAL ---

    // Modal de Editar General
    const modalEditarGeneral = document.getElementById('modalEditarGeneral');
    if (modalEditarGeneral) {
        modalEditarGeneral.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const form = document.getElementById('formEditarGeneral');
            
            // 1. Asignar la URL de actualización al formulario
            form.action = button.getAttribute('data-url');
            
            // 2. Llenar cada campo del formulario usando su ID específico
            document.getElementById('edit_id_cirugia_general').value = button.getAttribute('data-id');
            document.getElementById('edit_fecha_ingreso_general').value = button.getAttribute('data-fecha_ingreso');
            document.getElementById('edit_fecha_egreso_general').value = button.getAttribute('data-fecha_egreso');
            document.getElementById('edit_pieza_patologica').value = button.getAttribute('data-pieza_patologica');
            document.getElementById('edit_region_anatomica').value = button.getAttribute('data-region_anatomica');
            document.getElementById('edit_tipoCirugia_general').value = button.getAttribute('data-tipoCirugia');
            document.getElementById('edit_id_doctor_general').value = button.getAttribute('data-id_doctor');
            document.getElementById('edit_id_paciente_general').value = button.getAttribute('data-id_paciente');
            document.getElementById('edit_id_diagnostico_general').value = button.getAttribute('data-id_diagnostico');
        });
    }

    // Modal de Eliminar General
    const modalEliminarGeneral = document.getElementById('modalEliminarGeneral');
    if (modalEliminarGeneral) {
        modalEliminarGeneral.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('formEliminarGeneral').action = button.getAttribute('data-url');
        });
    }

    // --- LÓGICA PARA CIRUGÍA GINECOLÓGICA ---

    // Modal de Editar Ginecológica
    const modalEditarGinecologica = document.getElementById('modalEditarGinecologica');
    if (modalEditarGinecologica) {
        modalEditarGinecologica.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const form = document.getElementById('formEditarGinecologica');
            
            // 1. Asignar la URL de actualización al formulario
            form.action = button.getAttribute('data-url');

            // 2. Llenar cada campo del formulario usando su ID específico
            document.getElementById('edit_id_cirugia_ginecologica').value = button.getAttribute('data-id');
            document.getElementById('edit_fecha_ingreso_ginecologica').value = button.getAttribute('data-fecha_ingreso');
            document.getElementById('edit_fecha_egreso_ginecologica').value = button.getAttribute('data-fecha_egreso');
            document.getElementById('edit_apeo').value = button.getAttribute('data-apeo');
            document.getElementById('edit_tipoCirugia_ginecologica').value = button.getAttribute('data-tipoCirugia');
            document.getElementById('edit_id_doctor_ginecologica').value = button.getAttribute('data-id_doctor');
            document.getElementById('edit_id_paciente_ginecologica').value = button.getAttribute('data-id_paciente');
            document.getElementById('edit_id_diagnostico_ginecologica').value = button.getAttribute('data-id_diagnostico');
        });
    }
    
    // Modal de Eliminar Ginecológica
    const modalEliminarGinecologica = document.getElementById('modalEliminarGinecologica');
    if (modalEliminarGinecologica) {
        modalEliminarGinecologica.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('formEliminarGinecologica').action = button.getAttribute('data-url');
        });
    }
});