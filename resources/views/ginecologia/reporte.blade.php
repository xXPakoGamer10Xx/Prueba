@extends('components.layouts.ginecologia.nav-ginecologia')

@section('contenido')
<main class="container my-5" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-body p-5">
            <h3 class="text-center fw-bold mb-4">Generar Reporte</h3>

            <div id="formReporte">
                <div class="mb-3">
                    <label for="tipo_reporte" class="form-label fw-bold">Tipo de reporte</label>
                    <select id="tipo_reporte" class="form-select" required>
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="problematica">Reporte de problemática</option>
                        <option value="material">Reporte de material</option>
                    </select>
                </div>
                
                {{-- Contenedor para el formulario de Problemática (visible por defecto) --}}
                <div id="form-problematica">
                    <div class="mb-3">
                        <label for="encargado_area" class="form-label">Encargado de área:</label>
                        <input type="text" id="encargado_area" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="destinatario" class="form-label">Destinatario:</label>
                        <input type="text" id="destinatario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_reporte" class="form-label">Ingrese el motivo del reporte:</label>
                        <textarea id="motivo_reporte" rows="5" class="form-control" required></textarea>
                    </div>
                </div>

                {{-- Contenedor para el formulario de Material (oculto por defecto) --}}
                <div id="form-material" style="display: none;">
                    <div class="mb-3">
                        <label for="nombre_material" class="form-label">Nombre del material:</label>
                        <input type="text" id="nombre_material" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="estado_material" class="form-label">Estado del material:</label>
                        <input type="text" id="estado_material" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_solicitada" class="form-label">Cantidad solicitada:</label>
                        <input type="number" id="cantidad_solicitada" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones:</label>
                        <textarea id="observaciones" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button id="btnGenerarPdf" class="btn btn-primary">Generar y Descargar PDF</button>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- SCRIPT PARA GENERAR PDF Y MANEJAR FORMULARIO DINÁMICO --}}
{{-- SCRIPT PARA GENERAR PDF Y MANEJAR FORMULARIO DINÁMICO --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    const tipoReporteSelect = document.getElementById('tipo_reporte');
    const formProblematica = document.getElementById('form-problematica');
    const formMaterial = document.getElementById('form-material');

    // Escucha los cambios en el menú desplegable
    tipoReporteSelect.addEventListener('change', function() {
        if (this.value === 'problematica') {
            formProblematica.style.display = 'block';
            formMaterial.style.display = 'none';
        } else if (this.value === 'material') {
            formProblematica.style.display = 'none';
            formMaterial.style.display = 'block';
        }
    });

    document.getElementById('btnGenerarPdf').addEventListener('click', function() {
        const tipo = tipoReporteSelect.value;
        if (!tipo) {
            alert('Por favor, selecciona un tipo de reporte.');
            return;
        }

        if (tipo === 'problematica') {
            const data = {
                encargado: document.getElementById('encargado_area').value,
                destinatario: document.getElementById('destinatario').value,
                motivo: document.getElementById('motivo_reporte').value
            };
            if (!data.encargado || !data.destinatario || !data.motivo) {
                 alert('Por favor, llena todos los campos.'); return;
            }
            generarPDFProblematica(data);
        } else if (tipo === 'material') {
            const data = {
                nombre: document.getElementById('nombre_material').value,
                estado: document.getElementById('estado_material').value,
                cantidad: document.getElementById('cantidad_solicitada').value,
                observaciones: document.getElementById('observaciones').value
            };
            if (!data.nombre || !data.estado || !data.cantidad) {
                 alert('Por favor, llena al menos los primeros tres campos de material.'); return;
            }
            generarPDFReporteMaterial(data);
        }
    });

    // --- Función para el PDF de Problemática (sin cambios) ---
    function generarPDFProblematica(data) {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const fecha = new Date().toLocaleDateString('es-MX');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        let y = 20;
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(18);
        pdf.text('Reporte de Problemática', pdfWidth / 2, y, { align: 'center' });
        y += 15;
        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(12);
        pdf.text(`Fecha: ${fecha}`, 15, y);
        y += 10;
        pdf.text(`De: ${data.encargado}`, 15, y);
        y += 10;
        pdf.text(`Para: ${data.destinatario}`, 15, y);
        y += 15;
        pdf.setLineWidth(0.5);
        pdf.line(15, y, pdfWidth - 15, y);
        y += 10;
        pdf.setFont("helvetica", "bold");
        pdf.text('Motivo del Reporte:', 15, y);
        y += 8;
        pdf.setFont("helvetica", "normal");
        const motivo = pdf.splitTextToSize(data.motivo, pdfWidth - 30);
        pdf.text(motivo, 15, y);
        y += (motivo.length * 5) + 40;
        pdf.setFontSize(10);
        pdf.line(30, y, 90, y);
        pdf.text(data.encargado, 60, y + 5, { align: 'center' });
        pdf.text('Firma del Responsable', 60, y + 10, { align: 'center' });
        pdf.line(pdfWidth - 90, y, pdfWidth - 30, y);
        pdf.text(data.destinatario, pdfWidth - 60, y + 5, { align: 'center' });
        pdf.text('Firma del Destinatario', pdfWidth - 60, y + 10, { align: 'center' });
        pdf.save(`Reporte_Problematica_${fecha.replace(/\//g, '-')}.pdf`);
    }

    // --- Función para el PDF de Reporte de Material Específico (ACTUALIZADA) ---
    function generarPDFReporteMaterial(data) {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const fecha = new Date().toLocaleDateString('es-MX');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        let y = 20;
        
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(18);
        pdf.text('Reporte de Material', pdfWidth / 2, y, { align: 'center' });
        y += 15;

        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(12);
        pdf.text(`Fecha del reporte: ${fecha}`, 15, y);
        y += 15;

        pdf.setFont("helvetica", "bold");
        pdf.text('Nombre del material:', 15, y);
        pdf.setFont("helvetica", "normal");
        pdf.text(data.nombre, 65, y);
        y += 10;

        pdf.setFont("helvetica", "bold");
        pdf.text('Estado del material:', 15, y);
        pdf.setFont("helvetica", "normal");
        pdf.text(data.estado, 65, y);
        y += 10;
        
        pdf.setFont("helvetica", "bold");
        pdf.text('Cantidad solicitada:', 15, y);
        pdf.setFont("helvetica", "normal");
        pdf.text(data.cantidad, 65, y);
        y += 15;

        pdf.setLineWidth(0.5);
        pdf.line(15, y, pdfWidth - 15, y);
        y += 10;

        pdf.setFont("helvetica", "bold");
        pdf.text('Observaciones:', 15, y);
        y += 8;
        pdf.setFont("helvetica", "normal");
        const observaciones = pdf.splitTextToSize(data.observaciones || 'Sin observaciones.', pdfWidth - 30);
        pdf.text(observaciones, 15, y);
        y += (observaciones.length * 5) + 40; // Aumentamos espacio para la firma

        // --- INICIA SECCIÓN DE FIRMA ---
        if (y > 270) { // Si la firma queda muy abajo, crea una nueva página
            pdf.addPage();
            y = 40;
        }
        
        pdf.setFontSize(10);
        pdf.line(30, y, 90, y); // Dibuja la línea para la firma
        pdf.text('Firma de Almacén', 60, y + 5, { align: 'center' }); // Agrega el texto debajo de la línea
        // --- TERMINA SECCIÓN DE FIRMA ---

        pdf.save(`Reporte_Material_${data.nombre.replace(/ /g, '_')}.pdf`);
    }
</script>
@endsection