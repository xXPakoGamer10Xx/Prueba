@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0">Historial de Bajas de Equipo</h2>
    <a href="{{ route('servicios.bajas') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Registrar Nueva Baja</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form class="d-flex mb-4" method="GET" action="{{ route('servicios.bajas.historial') }}">
      <input class="form-control me-2" type="search" placeholder="Buscar por equipo, serie, motivo o estado..." name="search" value="{{ $search_query ?? '' }}">
      <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
  </form>

  <div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Equipo (Serie)</th>
          <th>Estado</th>
          <th>Motivo de Baja</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bajas as $baja)
            <tr>
              <td>{{ $baja->id_proceso_baja }}</td>
              <td>{{ $baja->nombre_equipo ?? 'N/D' }} ({{ $baja->num_serie ?? 'N/D' }})</td>
              <td>
                <span class="badge @switch($baja->estado)
                    @case('baja completa') bg-danger @break
                    @case('en proceso') bg-warning text-dark @break
                    @case('cancelada') bg-secondary @break
                    @default bg-light text-dark
                @endswitch">
                    {{ ucfirst($baja->estado) }}
                </span>
              </td>
              <td>{{ $baja->motivo }}</td>
              <td class="text-center">
                {{-- LÍNEA CORREGIDA: Se usa un atributo data-* para pasar los datos --}}
                <button 
                    class="btn btn-danger btn-sm" 
                    data-baja-json="{{ json_encode($baja) }}" 
                    onclick="generarPDFBaja(this)">
                    <i class="fas fa-file-pdf"></i>
                </button>
              </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No hay registros de baja para mostrar.</td>
            </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación --}}
  @if($bajas->hasPages())
    <nav>
        {{ $bajas->appends(['search' => $search_query ?? ''])->links() }}
    </nav>
  @endif
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generarPDFBaja(button) {
        const data = JSON.parse(button.getAttribute('data-baja-json'));
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const fecha = new Date().toLocaleDateString('es-MX');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        let y = 20;

        // Título
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(18);
        pdf.text('Reporte de Baja de Equipo Médico', pdfWidth / 2, y, { align: 'center' });
        y += 8;

        // Subtítulo
        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(12);
        pdf.text('Hospital Municipal de Chiconcuac – Servicios Generales', pdfWidth / 2, y, { align: 'center' });
        y += 6;

        // Fecha
        pdf.setFontSize(10);
        pdf.text(`Generado el: ${fecha}`, pdfWidth - 15, y, { align: 'right' });
        y += 8;

        // Línea separadora
        pdf.setLineWidth(0.5);
        pdf.line(15, y, pdfWidth - 15, y);
        y += 10;

        // ID y Estado
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(12);
        pdf.text(`Proceso de Baja ID: ${data.id_proceso_baja}`, 15, y);
        pdf.text(`Estado: ${data.estado.charAt(0).toUpperCase() + data.estado.slice(1)}`, pdfWidth / 2 + 10, y);
        y += 10;

        // Línea separadora
        pdf.line(15, y - 5, pdfWidth - 15, y - 5);

        // Equipo
        pdf.setFont("helvetica", "bold");
        pdf.text('Equipo:', 15, y);
        let equipoLabelWidth = pdf.getTextWidth('Equipo:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.nombre_equipo || 'No disponible'}`, 15 + equipoLabelWidth, y);
        y += 7;

        // Número de Serie
        pdf.setFont("helvetica", "bold");
        pdf.text('Número de Serie:', 15, y);
        let serieLabelWidth = pdf.getTextWidth('Número de Serie:') + 5;
        pdf.setFont("helvetica", "normal");
        pdf.text(`${data.num_serie || 'No disponible'}`, 15 + serieLabelWidth, y);
        y += 15;

        // Línea separadora
        pdf.line(15, y - 5, pdfWidth - 15, y - 5);

        // Motivo de la Baja
        pdf.setFont("helvetica", "bold");
        pdf.text('Motivo de la Baja:', 15, y);
        y += 8;

        pdf.setFont("helvetica", "normal");
        const motivo = pdf.splitTextToSize(data.motivo || 'No especificado.', pdfWidth - 30);
        pdf.text(motivo, 15, y);
        y += (motivo.length * 5) + 30;

        // Firmas
        pdf.line(15, y, 85, y);
        pdf.setFontSize(10);
        pdf.text('Firma de Autorización', 30, y + 5);

        pdf.line(pdfWidth - 85, y, pdfWidth - 15, y);
        pdf.text('Firma del Responsable', pdfWidth - 65, y + 5);

        // Guardar
        pdf.save(`Baja_Equipo_ID_${data.id_proceso_baja}.pdf`);
    }
</script>

@endsection
