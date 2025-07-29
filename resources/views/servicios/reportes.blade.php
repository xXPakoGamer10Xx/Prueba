@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h3 mb-0">Historial de Mantenimientos</h2>
        <a href="{{ route('servicios.mantenimiento') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Añadir Nuevo Reporte</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form class="d-flex mb-4" method="GET" action="{{ route('servicios.reportes') }}">
        <input class="form-control me-2" type="search" placeholder="Buscar por equipo, encargado o tipo..." name="search" value="{{ $search_query ?? '' }}">
        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
    </form>

    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Equipo (Serie)</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Encargado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($reportes as $reporte)
            <tr>
              <td>{{ $reporte->id_mantenimiento }}</td>
              <td>{{ $reporte->nombre_equipo }} ({{ $reporte->num_serie }})</td>
              <td>{{ $reporte->fecha }}</td>
              <td><span class="badge {{ $reporte->tipo == 'correctivo' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">{{ ucfirst($reporte->tipo) }}</span></td>
              <td>{{ $reporte->encargado }}</td>
              <td class="text-center">
                {{-- CAMBIO AQUÍ: Usamos un atributo data-* para pasar los datos --}}
                <button 
                    class="btn btn-danger btn-sm" 
                    data-reporte-json="{{ json_encode($reporte) }}" 
                    onclick="generarPDFReporte(this)">
                  <i class="fas fa-file-pdf"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class='text-center fst-italic'>No hay reportes disponibles.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($reportes->hasPages())
    <nav>
        {{-- Esto asegura que la paginación mantenga el filtro de búsqueda --}}
        {{ $reportes->appends(['search' => $search_query])->links() }}
    </nav>
    @endif
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generarPDFReporte(button) {
        const data = JSON.parse(button.getAttribute('data-reporte-json'));
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const fecha = new Date().toLocaleDateString('es-MX');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        let y = 20;

        // Encabezado
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(18);
        pdf.text('Reporte de Mantenimiento', pdfWidth / 2, y, { align: 'center' });
        y += 8;

        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(12);
        pdf.text('Hospital Municipal de Chiconcuac – Servicios Generales', pdfWidth / 2, y, { align: 'center' });
        y += 6;

        pdf.setFontSize(10);
        pdf.text(`Generado el: ${fecha}`, pdfWidth - 15, y, { align: 'right' });
        y += 8;

        pdf.setLineWidth(0.5);
        pdf.line(15, y, pdfWidth - 15, y);
        y += 10;

        // ID y Fecha
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(12);
        pdf.text(`Reporte ID: ${data.id_mantenimiento}`, 15, y);
        pdf.text(`Fecha del Servicio: ${data.fecha}`, pdfWidth / 2 + 10, y);
        y += 10;

        pdf.line(15, y - 5, pdfWidth - 15, y - 5);

        // Equipo
        pdf.setFont("helvetica", "bold");
        pdf.text('Equipo:', 15, y);
        let equipoLabelWidth = pdf.getTextWidth('Equipo:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.nombre_equipo || 'N/D'}`, 15 + equipoLabelWidth, y);
        y += 7;

        // Número de Serie
        pdf.setFont("helvetica", "bold");
        pdf.text('Número de Serie:', 15, y);
        let serieLabelWidth = pdf.getTextWidth('Número de Serie:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.num_serie || 'N/D'}`, 15 + serieLabelWidth, y);
        y += 7;

        // Tipo de Mantenimiento
        pdf.setFont("helvetica", "bold");
        pdf.text('Tipo de Mantenimiento:', 15, y);
        let tipoLabelWidth = pdf.getTextWidth('Tipo de Mantenimiento:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.tipo.charAt(0).toUpperCase() + data.tipo.slice(1)}`, 15 + tipoLabelWidth, y);
        y += 7;

        // Realizado por
        pdf.setFont("helvetica", "bold");
        pdf.text('Realizado por:', 15, y);
        let realizadoLabelWidth = pdf.getTextWidth('Realizado por:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.encargado} (${data.cargo})`, 15 + realizadoLabelWidth, y);
        y += 15;

        pdf.line(15, y - 5, pdfWidth - 15, y - 5);

        // Refacciones
        pdf.setFont("helvetica", "bold");
        pdf.text('Refacciones y Material Utilizado:', 15, y);
        y += 8;

        pdf.setFont("helvetica", "normal");
        const refacciones = pdf.splitTextToSize(data.refacciones_material || 'Ninguno.', pdfWidth - 30);
        pdf.text(refacciones, 15, y);
        y += (refacciones.length * 5) + 10;

        // Observaciones
        pdf.setFont("helvetica", "bold");
        pdf.text('Observaciones y Diagnóstico:', 15, y);
        y += 8;

        pdf.setFont("helvetica", "normal");
        const observaciones = pdf.splitTextToSize(data.observaciones || 'Sin observaciones.', pdfWidth - 30);
        pdf.text(observaciones, 15, y);
        y += (observaciones.length * 5) + 20;

        // Firma
        pdf.line(15, y, 85, y);
        pdf.setFontSize(10);
        pdf.text('Firma del Encargado', 35, y + 5);

        // Guardar PDF
        pdf.save(`Mantenimiento_ID_${data.id_mantenimiento}.pdf`);
    }
</script>

@endsection